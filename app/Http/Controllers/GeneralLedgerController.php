<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GeneralLedgerExport;

class GeneralLedgerController extends Controller
{
    protected function getLedgerData(string $accountId, string $period): array
    {
        $parts = explode('-', $period);
        $year = isset($parts[0]) ? intval($parts[0]) : now()->year;
        $month = isset($parts[1]) ? intval($parts[1]) : now()->month;

        $account = Account::findOrFail($accountId);

        // Fetch journal details
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
            ->where('journal_details.account_id', $accountId)
            ->whereYear('journal_entries.date', $year)
            ->whereMonth('journal_entries.date', $month)
            ->orderBy('journal_entries.date', 'asc')
            ->orderBy('journal_details.id', 'asc')
            ->get();

        $runningBalance = 0.0;
        $isAssetOrExpense = in_array($account->type, ['asset', 'expense']);

        $rows = [];
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($details as $detail) {
            $debit = floatval($detail->debit);
            $credit = floatval($detail->credit);

            if ($isAssetOrExpense) {
                $runningBalance += ($debit - $credit);
            } else {
                $runningBalance += ($credit - $debit);
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $rows[] = [
                'date' => Carbon::parse($detail->entry_date)->format('Y-m-d'),
                'code' => $detail->account_code,
                'name' => $detail->account_name,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $runningBalance,
            ];
        }

        return [
            'account' => $account,
            'period_label' => Carbon::createFromDate($year, $month)->format('F Y'),
            'rows' => $rows,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'endingBalance' => $runningBalance,
        ];
    }

    public function exportExcel(Request $request)
    {
        $accountId = $request->input('account_id');
        $period = $request->input('period', now()->format('Y-m'));

        if (!$accountId) {
            abort(400, 'Account selection is required.');
        }

        $data = $this->getLedgerData($accountId, $period);
        $fileName = 'general_ledger_' . strtolower(str_replace(' ', '_', $data['account']->name)) . '_' . $period . '.xlsx';

        return Excel::download(new GeneralLedgerExport($data), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $accountId = $request->input('account_id');
        $period = $request->input('period', now()->format('Y-m'));

        if (!$accountId) {
            abort(400, 'Account selection is required.');
        }

        $data = $this->getLedgerData($accountId, $period);
        $pdf = Pdf::loadView('admin.reports.general-ledger-pdf', $data);
        $fileName = 'general_ledger_' . strtolower(str_replace(' ', '_', $data['account']->name)) . '_' . $period . '.pdf';

        return $pdf->download($fileName);
    }
}
