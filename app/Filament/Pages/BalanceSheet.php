<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Account;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BalanceSheet extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $slug = 'balance-sheet';
    protected static string $view = 'filament.pages.balance-sheet';

    // Navigation registration is manually handled in AdminPanelProvider for exact sorting order
    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return __('sidebar.Balance Sheet');
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return __('sidebar.Balance Sheet');
    }

    public ?string $period = null;

    public static function canAccess(): bool
    {
        return canAccessMenu('admin/balance-sheet');
    }

    public function mount(): void
    {
        $this->period = now()->format('Y-m');
    }

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

    protected function getBalanceSheetData(): array
    {
        $parts = explode('-', $this->period);
        $year = isset($parts[0]) ? intval($parts[0]) : now()->year;
        $month = isset($parts[1]) ? intval($parts[1]) : now()->month;

        app(\App\Services\Accounting\MonthlyBalanceService::class)->ensureSnapshotsUpTo($year, $month);

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

    protected function getViewData(): array
    {
        return $this->getBalanceSheetData();
    }
}
