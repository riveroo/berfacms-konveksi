<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Account;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\Accounting\MonthlyBalanceService;

class TrialBalance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $slug = 'trial-balance';
    protected static string $view = 'filament.pages.trial-balance';

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return __('sidebar.Trial Balance');
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return __('sidebar.Trial Balance');
    }

    public ?string $period = null;

    public static function canAccess(): bool
    {
        return canAccessMenu('admin/trial-balance');
    }

    public function mount(): void
    {
        $this->period = now()->format('Y-m');
    }

    protected function getTrialBalanceData(): array
    {
        if (!$this->period) {
            return [];
        }

        $parts = explode('-', $this->period);
        $year = isset($parts[0]) ? intval($parts[0]) : now()->year;
        $month = isset($parts[1]) ? intval($parts[1]) : now()->month;

        // Ensure monthly snapshots are generated up to this month to keep data clean and pre-warmed.
        app(MonthlyBalanceService::class)->ensureSnapshotsUpTo($year, $month);

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

    protected function getViewData(): array
    {
        return $this->getTrialBalanceData();
    }
}
