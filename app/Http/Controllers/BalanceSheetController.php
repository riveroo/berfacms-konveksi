<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BalanceSheetExport;

class BalanceSheetController extends Controller
{
    protected function calculateRetainedEarnings(Carbon $startDate, Carbon $endDate): float
    {
        $revenue = JournalDetail::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_details.account_id')
            ->where('accounts.type', 'revenue')
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->sum(DB::raw('journal_details.credit - journal_details.debit'));

        $expense = JournalDetail::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_details.account_id')
            ->where('accounts.type', 'expense')
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->sum(DB::raw('journal_details.debit - journal_details.credit'));

        return floatval($revenue) - floatval($expense);
    }

    protected function getBalanceSheetData(string $period): array
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

        $accounts = Account::where('is_active', true)->get();

        $cashAccounts = [];
        $arAccounts = [];
        $inventoryAccounts = [];
        $ppeAccounts = [];
        $depreciationAccounts = [];
        
        $apAccounts = [];
        $accruedLiabilities = [];
        
        $equityAccounts = [];

        foreach ($accounts as $account) {
            $bal = $balances->get($account->id);
            $debit = $bal ? floatval($bal->total_debit) : 0.00;
            $credit = $bal ? floatval($bal->total_credit) : 0.00;
            
            $nameLower = strtolower($account->name);
            
            if ($account->type === 'asset') {
                $balanceValue = $debit - $credit;
                $account->balance = $balanceValue;
                
                if ($account->code === '1001' || $account->code === '1002' || str_contains($nameLower, 'cash') || str_contains($nameLower, 'bank') || str_contains($nameLower, 'kas') || str_contains($nameLower, 'rekening')) {
                    $cashAccounts[] = $account;
                } elseif (str_contains($nameLower, 'receivable') || str_contains($nameLower, 'piutang')) {
                    $arAccounts[] = $account;
                } elseif ($account->code === '1003' || str_contains($nameLower, 'inventory') || str_contains($nameLower, 'persediaan')) {
                    $inventoryAccounts[] = $account;
                } elseif (str_contains($nameLower, 'depreciation') || str_contains($nameLower, 'penyusutan')) {
                    $depreciationAccounts[] = $account;
                } else {
                    $ppeAccounts[] = $account;
                }
            } elseif ($account->type === 'liability') {
                $balanceValue = $credit - $debit;
                $account->balance = $balanceValue;
                
                if (str_contains($nameLower, 'payable') || str_contains($nameLower, 'utang') || str_contains($nameLower, 'hutang')) {
                    $apAccounts[] = $account;
                } else {
                    $accruedLiabilities[] = $account;
                }
            } elseif ($account->type === 'equity') {
                $balanceValue = $credit - $debit;
                $account->balance = $balanceValue;
                
                if (!str_contains($nameLower, 'retained') && !str_contains($nameLower, 'laba ditahan')) {
                    $equityAccounts[] = $account;
                }
            }
        }

        $totalCash = collect($cashAccounts)->sum('balance');
        $totalAR = collect($arAccounts)->sum('balance');
        $totalInventory = collect($inventoryAccounts)->sum('balance');
        $totalCurrentAssets = $totalCash + $totalAR + $totalInventory;

        $totalPPE = collect($ppeAccounts)->sum('balance');
        $totalDepreciation = collect($depreciationAccounts)->sum('balance');
        $totalNonCurrentAssets = $totalPPE - abs($totalDepreciation);

        $totalAssets = $totalCurrentAssets + $totalNonCurrentAssets;

        $totalAP = collect($apAccounts)->sum('balance');
        $totalAccrued = collect($accruedLiabilities)->sum('balance');
        $totalCurrentLiabilities = $totalAP + $totalAccrued;

        $totalShareCapital = collect($equityAccounts)->sum('balance');
        
        $retainedEarnings = $this->calculateRetainedEarnings($startDate, $endDate);
        $totalEquity = $totalShareCapital + $retainedEarnings;

        $totalLiabilitiesAndEquity = $totalCurrentLiabilities + $totalEquity;

        $isBalanced = abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01;
        $difference = abs($totalAssets - $totalLiabilitiesAndEquity);

        return [
            'period_label' => Carbon::createFromDate($year, $month, 1)->format('F Y'),
            'startDate' => $startDate,
            'endDate' => $endDate,
            
            // Assets
            'cashAccounts' => $cashAccounts,
            'totalCash' => $totalCash,
            'arAccounts' => $arAccounts,
            'totalAR' => $totalAR,
            'inventoryAccounts' => $inventoryAccounts,
            'totalInventory' => $totalInventory,
            'totalCurrentAssets' => $totalCurrentAssets,
            
            'ppeAccounts' => $ppeAccounts,
            'totalPPE' => $totalPPE,
            'depreciationAccounts' => $depreciationAccounts,
            'totalDepreciation' => $totalDepreciation,
            'totalNonCurrentAssets' => $totalNonCurrentAssets,
            
            'totalAssets' => $totalAssets,
            
            // Liabilities & Equity
            'apAccounts' => $apAccounts,
            'totalAP' => $totalAP,
            'accruedLiabilities' => $accruedLiabilities,
            'totalAccrued' => $totalAccrued,
            'totalCurrentLiabilities' => $totalCurrentLiabilities,
            
            'equityAccounts' => $equityAccounts,
            'totalShareCapital' => $totalShareCapital,
            'retainedEarnings' => $retainedEarnings,
            'totalEquity' => $totalEquity,
            
            'totalLiabilitiesAndEquity' => $totalLiabilitiesAndEquity,
            
            // Validation
            'isBalanced' => $isBalanced,
            'difference' => $difference,
        ];
    }

    public function exportExcel(Request $request)
    {
        $period = $request->input('period', now()->format('Y-m'));
        $data = $this->getBalanceSheetData($period);
        $fileName = 'balance_sheet_' . $period . '.xlsx';

        return Excel::download(new BalanceSheetExport($data, $period), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $period = $request->input('period', now()->format('Y-m'));
        $data = $this->getBalanceSheetData($period);
        $pdf = Pdf::loadView('admin.reports.balance-sheet-pdf', $data);
        $fileName = 'balance_sheet_' . $period . '.pdf';

        return $pdf->download($fileName);
    }
}
