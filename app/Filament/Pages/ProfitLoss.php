<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Account;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProfitLoss extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $slug = 'reports/profit-loss';
    protected static string $view = 'admin.reports.profit-loss';

    // Navigation registration is manually handled in AdminPanelProvider for exact sorting order
    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return __('sidebar.Profit & Loss');
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return __('sidebar.Profit & Loss');
    }

    public string $filter_type = 'monthly';
    public ?string $filter_month = null;
    public ?string $filter_year = null;
    public ?string $start_date_input = null;
    public ?string $end_date_input = null;

    public static function canAccess(): bool
    {
        return canAccessMenu('admin/reports/profit-loss');
    }

    public function mount(): void
    {
        // Sync with request query parameters if available, otherwise use defaults
        $this->filter_type = request()->query('filter_type', 'monthly');
        $this->filter_month = request()->query('filter_month', now()->format('Y-m'));
        $this->filter_year = request()->query('filter_year', (string) now()->year);
        $this->start_date_input = request()->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $this->end_date_input = request()->query('end_date', now()->endOfMonth()->format('Y-m-d'));
    }

    protected function getFilteredDatesAndLabels(): array
    {
        if ($this->filter_type === 'monthly') {
            try {
                $parsed = Carbon::createFromFormat('Y-m', $this->filter_month);
            } catch (\Exception $e) {
                $this->filter_month = now()->format('Y-m');
                $parsed = Carbon::createFromFormat('Y-m', $this->filter_month);
            }
            $startDate = $parsed->copy()->startOfMonth();
            $endDate = $parsed->copy()->endOfMonth();
            $periodLabel = $parsed->format('F Y');
        } elseif ($this->filter_type === 'yearly') {
            $year = intval($this->filter_year) ?: now()->year;
            $startDate = Carbon::createFromDate($year)->startOfYear();
            $endDate = Carbon::createFromDate($year)->endOfYear();
            $periodLabel = 'Year ' . $year;
        } else { // Custom range
            $startDate = $this->start_date_input ? Carbon::parse($this->start_date_input)->startOfDay() : now()->startOfMonth();
            $endDate = $this->end_date_input ? Carbon::parse($this->end_date_input)->endOfDay() : now()->endOfMonth();
            $periodLabel = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        }

        return [
            'filter_type' => $this->filter_type,
            'filter_month' => $this->filter_month,
            'filter_year' => $this->filter_year,
            'start_date_input' => $this->start_date_input ?: $startDate->format('Y-m-d'),
            'end_date_input' => $this->end_date_input ?: $endDate->format('Y-m-d'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'period_label' => $periodLabel,
        ];
    }

    protected function calculateProfitAndLoss(Carbon $startDate, Carbon $endDate): array
    {
        $balances = JournalDetail::query()
            ->select('account_id', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        $revenueAccounts = Account::where('type', 'revenue')->get()->map(function($account) use ($balances) {
            $bal = $balances->get($account->id);
            $debit = $bal ? floatval($bal->total_debit) : 0.00;
            $credit = $bal ? floatval($bal->total_credit) : 0.00;
            $account->balance = $credit - $debit;
            return $account;
        });

        $expenseAccounts = Account::where('type', 'expense')->get()->map(function($account) use ($balances) {
            $bal = $balances->get($account->id);
            $debit = $bal ? floatval($bal->total_debit) : 0.00;
            $credit = $bal ? floatval($bal->total_credit) : 0.00;
            $account->balance = $debit - $credit;
            return $account;
        });

        $totalRevenue = $revenueAccounts->sum('balance');
        $totalExpense = $expenseAccounts->sum('balance');
        $netProfit = $totalRevenue - $totalExpense;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        return [
            'revenueAccounts' => $revenueAccounts,
            'expenseAccounts' => $expenseAccounts,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'netProfit' => $netProfit,
            'profitMargin' => $profitMargin,
        ];
    }

    protected function compileTrends(Carbon $startDate, Carbon $endDate, string $filterType): array
    {
        $labels = [];
        $revenueData = [];
        $expenseData = [];
        $profitData = [];

        if ($filterType === 'yearly') {
            $isSqlite = DB::connection()->getDriverName() === 'sqlite';
            $monthExpr = $isSqlite 
                ? "CAST(strftime('%m', journal_entries.date) AS INTEGER)" 
                : "MONTH(journal_entries.date)";

            $monthlyStats = JournalDetail::query()
                ->select(
                    DB::raw("$monthExpr as month_num"),
                    DB::raw("SUM(CASE WHEN accounts.type = 'revenue' THEN journal_details.credit - journal_details.debit ELSE 0 END) as revenue"),
                    DB::raw("SUM(CASE WHEN accounts.type = 'expense' THEN journal_details.debit - journal_details.credit ELSE 0 END) as expense")
                )
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
                ->join('accounts', 'accounts.id', '=', 'journal_details.account_id')
                ->whereBetween('journal_entries.date', [$startDate, $endDate])
                ->groupBy(DB::raw("$monthExpr"))
                ->orderBy('month_num', 'asc')
                ->get()
                ->keyBy('month_num');

            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $monthNames[$m - 1];
                $stat = $monthlyStats->get($m);
                $rev = $stat ? floatval($stat->revenue) : 0;
                $exp = $stat ? floatval($stat->expense) : 0;
                $revenueData[] = $rev;
                $expenseData[] = $exp;
                $profitData[] = $rev - $exp;
            }
        } else {
            $dailyStats = JournalDetail::query()
                ->select(
                    'journal_entries.date',
                    DB::raw("SUM(CASE WHEN accounts.type = 'revenue' THEN journal_details.credit - journal_details.debit ELSE 0 END) as revenue"),
                    DB::raw("SUM(CASE WHEN accounts.type = 'expense' THEN journal_details.debit - journal_details.credit ELSE 0 END) as expense")
                )
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
                ->join('accounts', 'accounts.id', '=', 'journal_details.account_id')
                ->whereBetween('journal_entries.date', [$startDate, $endDate])
                ->groupBy('journal_entries.date')
                ->orderBy('journal_entries.date', 'asc')
                ->get();

            foreach ($dailyStats as $stat) {
                $labels[] = Carbon::parse($stat->date)->format('d M');
                $rev = floatval($stat->revenue);
                $exp = floatval($stat->expense);
                $revenueData[] = $rev;
                $expenseData[] = $exp;
                $profitData[] = $rev - $exp;
            }

            if (empty($labels)) {
                $labels[] = now()->format('d M');
                $revenueData[] = 0;
                $expenseData[] = 0;
                $profitData[] = 0;
            }
        }

        return [
            'labels' => $labels,
            'revenue' => $revenueData,
            'expense' => $expenseData,
            'profit' => $profitData,
        ];
    }

    protected function calculateProfitAndLossMonthlyBreakdown(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate): array
    {
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $monthExpr = $isSqlite 
            ? "CAST(strftime('%m', journal_entries.date) AS INTEGER)" 
            : "MONTH(journal_entries.date)";

        $balances = JournalDetail::query()
            ->select(
                'account_id',
                DB::raw("$monthExpr as month_num"),
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->groupBy('account_id', DB::raw("$monthExpr"))
            ->get()
            ->groupBy('account_id');

        $revenueAccounts = Account::where('type', 'revenue')->get()->map(function($account) use ($balances) {
            $accountBalances = $balances->get($account->id) ? $balances->get($account->id)->keyBy('month_num') : collect();
            $monthlyBalances = [];
            for ($m = 1; $m <= 12; $m++) {
                $bal = $accountBalances->get($m);
                $debit = $bal ? floatval($bal->total_debit) : 0.00;
                $credit = $bal ? floatval($bal->total_credit) : 0.00;
                $monthlyBalances[$m] = $credit - $debit;
            }
            $account->monthly_balances = $monthlyBalances;
            $account->balance = array_sum($monthlyBalances);
            return $account;
        });

        $expenseAccounts = Account::where('type', 'expense')->get()->map(function($account) use ($balances) {
            $accountBalances = $balances->get($account->id) ? $balances->get($account->id)->keyBy('month_num') : collect();
            $monthlyBalances = [];
            for ($m = 1; $m <= 12; $m++) {
                $bal = $accountBalances->get($m);
                $debit = $bal ? floatval($bal->total_debit) : 0.00;
                $credit = $bal ? floatval($bal->total_credit) : 0.00;
                $monthlyBalances[$m] = $debit - $credit;
            }
            $account->monthly_balances = $monthlyBalances;
            $account->balance = array_sum($monthlyBalances);
            return $account;
        });

        $monthlyTotalRevenue = [];
        $monthlyTotalExpense = [];
        $monthlyNetProfit = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthlyTotalRevenue[$m] = $revenueAccounts->sum(fn($acc) => $acc->monthly_balances[$m]);
            $monthlyTotalExpense[$m] = $expenseAccounts->sum(fn($acc) => $acc->monthly_balances[$m]);
            $monthlyNetProfit[$m] = $monthlyTotalRevenue[$m] - $monthlyTotalExpense[$m];
        }

        $totalRevenue = array_sum($monthlyTotalRevenue);
        $totalExpense = array_sum($monthlyTotalExpense);
        $netProfit = $totalRevenue - $totalExpense;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        return [
            'revenueAccounts' => $revenueAccounts,
            'expenseAccounts' => $expenseAccounts,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'netProfit' => $netProfit,
            'profitMargin' => $profitMargin,
            'monthlyTotalRevenue' => $monthlyTotalRevenue,
            'monthlyTotalExpense' => $monthlyTotalExpense,
            'monthlyNetProfit' => $monthlyNetProfit,
        ];
    }

    protected function getViewData(): array
    {
        $filterData = $this->getFilteredDatesAndLabels();
        $startDate = $filterData['start_date'];
        $endDate = $filterData['end_date'];

        app(\App\Services\Accounting\MonthlyBalanceService::class)->ensureSnapshotsUpTo($endDate->year, $endDate->month);

        if ($filterData['filter_type'] === 'yearly') {
            $financials = $this->calculateProfitAndLossMonthlyBreakdown($startDate, $endDate);
        } else {
            $financials = $this->calculateProfitAndLoss($startDate, $endDate);
        }

        $trends = $this->compileTrends($startDate, $endDate, $filterData['filter_type']);

        $monthHeaders = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthHeaders[$m] = Carbon::create()->month($m)->translatedFormat('M');
        }

        return array_merge(
            $filterData,
            $financials,
            [
                'trends' => $trends,
                'monthHeaders' => $monthHeaders,
            ]
        );
    }
}
