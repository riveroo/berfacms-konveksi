<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TrialBalanceExport;

class TrialBalanceController extends Controller
{
    protected function getTrialBalanceData(string $period): array
    {
        $parts = explode('-', $period);
        $year = isset($parts[0]) ? intval($parts[0]) : now()->year;
        $month = isset($parts[1]) ? intval($parts[1]) : now()->month;

        $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $balances = JournalDetail::query()
            ->select('account_id', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        $accounts = Account::where('is_active', true)->orderBy('code', 'asc')->get();

        $rows = [];
        $totalDebitSum = 0.00;
        $totalCreditSum = 0.00;

        foreach ($accounts as $account) {
            $bal = $balances->get($account->id);
            $totalDebit = $bal ? floatval($bal->total_debit) : 0.00;
            $totalCredit = $bal ? floatval($bal->total_credit) : 0.00;

            $debitValue = null;
            $creditValue = null;

            if (in_array($account->type, ['asset', 'expense'])) {
                $balance = $totalDebit - $totalCredit;
                if ($balance >= 0) {
                    $debitValue = $balance;
                } else {
                    $creditValue = abs($balance);
                }
            } else {
                $balance = $totalCredit - $totalDebit;
                if ($balance >= 0) {
                    $creditValue = $balance;
                } else {
                    $debitValue = abs($balance);
                }
            }

            $totalDebitSum += ($debitValue ?? 0.00);
            $totalCreditSum += ($creditValue ?? 0.00);

            $rows[] = [
                'code' => $account->code,
                'name' => $account->name,
                'debit' => $debitValue,
                'credit' => $creditValue,
                'parent_id' => $account->parent_id,
            ];
        }

        $isBalanced = abs($totalDebitSum - $totalCreditSum) < 0.01;
        $difference = abs($totalDebitSum - $totalCreditSum);

        return [
            'period_label' => Carbon::createFromDate($year, $month, 1)->format('F Y'),
            'rows' => $rows,
            'totalDebit' => $totalDebitSum,
            'totalCredit' => $totalCreditSum,
            'isBalanced' => $isBalanced,
            'difference' => $difference,
        ];
    }

    public function exportExcel(Request $request)
    {
        if (!canAccessMenu('admin/import-export')) {
            abort(403, 'Anda tidak memiliki akses untuk ekspor data.');
        }

        $period = $request->input('period', now()->format('Y-m'));
        $data = $this->getTrialBalanceData($period);
        $fileName = 'trial_balance_' . $period . '.xlsx';

        return Excel::download(new TrialBalanceExport($data, $period), $fileName);
    }

    public function exportPdf(Request $request)
    {
        if (!canAccessMenu('admin/import-export')) {
            abort(403, 'Anda tidak memiliki akses untuk ekspor data.');
        }

        $period = $request->input('period', now()->format('Y-m'));
        $data = $this->getTrialBalanceData($period);
        $pdf = Pdf::loadView('admin.reports.trial-balance-pdf', $data);
        $fileName = 'trial_balance_' . $period . '.pdf';

        return $pdf->download($fileName);
    }
}
