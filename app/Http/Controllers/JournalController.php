<?php

namespace App\Http\Controllers;

use App\Models\JournalDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class JournalController extends Controller
{
    /**
     * Display the general journal table.
     */
    public function index(Request $request)
    {
        $filterMonth = $request->input('filter_month', now()->format('Y-m'));
        
        try {
            $parsedDate = Carbon::createFromFormat('Y-m', $filterMonth);
        } catch (\Exception $e) {
            $filterMonth = now()->format('Y-m');
            $parsedDate = Carbon::createFromFormat('Y-m', $filterMonth);
        }

        $year = $parsedDate->year;
        $month = $parsedDate->month;

        $details = JournalDetail::query()
            ->select(
                'journal_details.*', 
                'journal_entries.date as entry_date', 
                'journal_entries.description as entry_description',
                'accounts.name as account_name', 
                'accounts.code as account_code'
            )
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_details.account_id')
            ->whereYear('journal_entries.date', $year)
            ->whereMonth('journal_entries.date', $month)
            ->orderBy('journal_entries.date', 'asc')
            ->orderBy('journal_details.journal_entry_id', 'asc')
            ->orderBy('journal_details.debit', 'desc') // Debit usually shown first in accounting
            ->orderBy('journal_details.id', 'asc')
            ->get();

        $totalDebit = $details->sum('debit');
        $totalCredit = $details->sum('credit');

        return view('journal.index', compact('details', 'filterMonth', 'totalDebit', 'totalCredit'));
    }

    /**
     * Export the filtered journal as PDF.
     */
    public function exportPdf(Request $request)
    {
        $filterMonth = $request->input('filter_month', now()->format('Y-m'));
        
        try {
            $parsedDate = Carbon::createFromFormat('Y-m', $filterMonth);
        } catch (\Exception $e) {
            $filterMonth = now()->format('Y-m');
            $parsedDate = Carbon::createFromFormat('Y-m', $filterMonth);
        }

        $year = $parsedDate->year;
        $month = $parsedDate->month;

        $details = JournalDetail::query()
            ->select(
                'journal_details.*', 
                'journal_entries.date as entry_date', 
                'journal_entries.description as entry_description',
                'accounts.name as account_name', 
                'accounts.code as account_code'
            )
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_details.account_id')
            ->whereYear('journal_entries.date', $year)
            ->whereMonth('journal_entries.date', $month)
            ->orderBy('journal_entries.date', 'asc')
            ->orderBy('journal_details.journal_entry_id', 'asc')
            ->orderBy('journal_details.debit', 'desc')
            ->orderBy('journal_details.id', 'asc')
            ->get();

        $totalDebit = $details->sum('debit');
        $totalCredit = $details->sum('credit');

        $pdf = Pdf::loadView('journal.pdf', compact('details', 'filterMonth', 'totalDebit', 'totalCredit'));
        
        return $pdf->download("journal-{$filterMonth}.pdf");
    }
}
