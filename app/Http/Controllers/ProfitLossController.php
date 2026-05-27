<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProfitLossExport;

class ProfitLossController extends Controller
{
    /**
     * Display the Profit & Loss Report screen.
     */
    public function index(Request $request)
    {
        $filterData = $this->getFilteredDatesAndLabels($request);
        $startDate = $filterData['start_date'];
        $endDate = $filterData['end_date'];
        $periodLabel = $filterData['period_label'];

        $financials = $this->calculateProfitAndLoss($startDate, $endDate);
        $trends = $this->compileTrends($startDate, $endDate, $filterData['filter_type']);

        return view('admin.reports.profit-loss', array_merge(
            $filterData,
            $financials,
            ['trends' => $trends]
        ));
    }

    /**
     * Get transaction details for the clicked account in the period (drilldown API).
     */
    public function drilldown(Request $request)
    {
        $accountId = $request->input('account_id');
        $filterData = $this->getFilteredDatesAndLabels($request);
        $startDate = $filterData['start_date'];
        $endDate = $filterData['end_date'];

        $account = Account::findOrFail($accountId);

        $details = JournalDetail::query()
            ->select(
                'journal_details.*', 
                'journal_entries.date as entry_date', 
                'journal_entries.description as entry_description', 
                'journal_entries.reference_type', 
                'journal_entries.reference_id', 
                'accounts.type as account_type'
            )
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_details.account_id')
            ->where('journal_details.account_id', $accountId)
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->orderBy('journal_entries.date', 'asc')
            ->orderBy('journal_details.id', 'asc')
            ->get()
            ->map(function($detail) {
                // Determine transaction net balance based on normal credit (revenue) or debit (expense) balance
                $amount = $detail->account_type === 'revenue' 
                    ? ($detail->credit - $detail->debit) 
                    : ($detail->debit - $detail->credit);

                return [
                    'date' => Carbon::parse($detail->entry_date)->format('d/m/Y'),
                    'description' => $detail->entry_description ?: 'Manual Journal Entry',
                    'reference' => ($detail->reference_type ? ucfirst(str_replace('_', ' ', $detail->reference_type)) : 'Manual') . ($detail->reference_id ? ' #' . $detail->reference_id : ''),
                    'amount' => 'Rp ' . number_format($amount, 0, ',', '.'),
                ];
            });

        return response()->json([
            'account_name' => $account->name,
            'transactions' => $details,
        ]);
    }

    /**
     * Export Profit & Loss report as PDF.
     */
    public function exportPdf(Request $request)
    {
        $filterData = $this->getFilteredDatesAndLabels($request);
        $startDate = $filterData['start_date'];
        $endDate = $filterData['end_date'];
        $periodLabel = $filterData['period_label'];

        $financials = $this->calculateProfitAndLoss($startDate, $endDate);

        $pdf = Pdf::loadView('admin.reports.profit-loss-pdf', array_merge(
            $filterData,
            $financials
        ));

        $fileName = 'profit_loss_' . str_replace(' ', '_', strtolower($periodLabel)) . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Export Profit & Loss report as Excel.
     */
    public function exportExcel(Request $request)
    {
        $filterData = $this->getFilteredDatesAndLabels($request);
        $startDate = $filterData['start_date'];
        $endDate = $filterData['end_date'];
        $periodLabel = $filterData['period_label'];

        $financials = $this->calculateProfitAndLoss($startDate, $endDate);

        $fileName = 'profit_loss_' . str_replace(' ', '_', strtolower($periodLabel)) . '.xlsx';
        return Excel::download(
            new ProfitLossExport(
                $financials['revenueAccounts'],
                $financials['expenseAccounts'],
                $financials['totalRevenue'],
                $financials['totalExpense'],
                $financials['netProfit'],
                $financials['profitMargin'],
                $periodLabel
            ), 
            $fileName
        );
    }

    /**
     * Helper: Resolve start and end dates and formatting labels for active filters.
     */
    private function getFilteredDatesAndLabels(Request $request): array
    {
        $filterType = $request->input('filter_type', 'monthly');
        $filterMonth = $request->input('filter_month', now()->format('Y-m'));
        $filterYear = $request->input('filter_year', now()->year);
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        if ($filterType === 'monthly') {
            try {
                $parsed = Carbon::createFromFormat('Y-m', $filterMonth);
            } catch (\Exception $e) {
                $filterMonth = now()->format('Y-m');
                $parsed = Carbon::createFromFormat('Y-m', $filterMonth);
            }
            $startDate = $parsed->copy()->startOfMonth();
            $endDate = $parsed->copy()->endOfMonth();
            $periodLabel = $parsed->format('F Y');
        } elseif ($filterType === 'yearly') {
            $year = intval($filterYear) ?: now()->year;
            $startDate = Carbon::createFromDate($year)->startOfYear();
            $endDate = Carbon::createFromDate($year)->endOfYear();
            $periodLabel = 'Year ' . $year;
        } else { // Custom range
            $startDate = $startDateInput ? Carbon::parse($startDateInput)->startOfDay() : now()->startOfMonth();
            $endDate = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : now()->endOfMonth();
            $periodLabel = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        }

        return [
            'filter_type' => $filterType,
            'filter_month' => $filterMonth,
            'filter_year' => $filterYear,
            'start_date_input' => $startDateInput ?: $startDate->format('Y-m-d'),
            'end_date_input' => $endDateInput ?: $endDate->format('Y-m-d'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'period_label' => $periodLabel,
        ];
    }

    /**
     * Helper: Query database and compute accounts and net Profit & Loss totals.
     */
    private function calculateProfitAndLoss(Carbon $startDate, Carbon $endDate): array
    {
        // Query journal details sums grouped by account_id in the selected date range
        $balances = JournalDetail::query()
            ->select('account_id', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->whereBetween('journal_entries.date', [$startDate, $endDate])
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        // Map revenue accounts (balance is credit - debit)
        $revenueAccounts = Account::where('type', 'revenue')->get()->map(function($account) use ($balances) {
            $bal = $balances->get($account->id);
            $debit = $bal ? floatval($bal->total_debit) : 0.00;
            $credit = $bal ? floatval($bal->total_credit) : 0.00;
            $account->balance = $credit - $debit;
            return $account;
        });

        // Map expense accounts (balance is debit - credit)
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

    /**
     * Helper: Compile daily or monthly trends data points.
     */
    private function compileTrends(Carbon $startDate, Carbon $endDate, string $filterType): array
    {
        $labels = [];
        $revenueData = [];
        $expenseData = [];
        $profitData = [];

        if ($filterType === 'yearly') {
            // Check driver to support database-agnostic queries (SQLite in tests, MySQL in prod)
            $isSqlite = DB::connection()->getDriverName() === 'sqlite';
            $monthExpr = $isSqlite 
                ? "CAST(strftime('%m', journal_entries.date) AS INTEGER)" 
                : "MONTH(journal_entries.date)";

            // Compile monthly trends (Jan to Dec)
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
            // Compile daily trends
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

            // Format daily labels and populate datasets
            foreach ($dailyStats as $stat) {
                $labels[] = Carbon::parse($stat->date)->format('d M');
                $rev = floatval($stat->revenue);
                $exp = floatval($stat->expense);
                $revenueData[] = $rev;
                $expenseData[] = $exp;
                $profitData[] = $rev - $exp;
            }

            // Fallback if no transactions exist, insert today
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
}
