<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\AccountMonthlyBalance;
use App\Models\JournalEntry;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyBalanceService
{
    /**
     * Ensure snapshots exist and are clean up to the specified year and month.
     * Starts from the earliest journal entry month or target month if none exists.
     */
    public function ensureSnapshotsUpTo(int $year, int $month): void
    {
        $earliestDate = JournalEntry::min('date');
        if (!$earliestDate) {
            // No transactions, generate for target month directly.
            $this->generateSnapshot($year, $month);
            return;
        }

        $start = Carbon::parse($earliestDate)->startOfMonth();
        $target = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        // If target month is before earliest date, just generate for target month.
        if ($target->lessThan($start)) {
            $this->generateSnapshot($year, $month);
            return;
        }

        $current = $start->copy();
        while ($current->lessThanOrEqualTo($target)) {
            $currYear = $current->year;
            $currMonth = $current->month;

            // Check if snapshot exists and is clean
            $existsAndClean = AccountMonthlyBalance::where('period_year', $currYear)
                ->where('period_month', $currMonth)
                ->where('is_dirty', false)
                ->exists();

            if (!$existsAndClean) {
                $this->generateSnapshot($currYear, $currMonth);
            }

            $current->addMonth();
        }
    }

    /**
     * Generate snapshot for all active accounts for a given year and month.
     */
    public function generateSnapshot(int $year, int $month): void
    {
        DB::transaction(function () use ($year, $month) {
            // Get all active accounts
            $accounts = Account::orderBy('code', 'asc')->get();

            // Calculate previous month period
            $prevYear = $year;
            $prevMonth = $month - 1;
            if ($prevMonth === 0) {
                $prevMonth = 12;
                $prevYear = $year - 1;
            }

            // Load all previous month closing balances
            $prevBalances = AccountMonthlyBalance::where('period_year', $prevYear)
                ->where('period_month', $prevMonth)
                ->get()
                ->keyBy('account_id');

            // Load debit and credit sums for each account in the target month to prevent N+1 queries.
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            $monthSums = JournalDetail::query()
                ->select('account_id', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
                ->whereBetween('journal_entries.date', [$startDate, $endDate])
                ->groupBy('account_id')
                ->get()
                ->keyBy('account_id');

            foreach ($accounts as $account) {
                $openingBalance = 0.00;
                if ($prevBalances->has($account->id)) {
                    $openingBalance = floatval($prevBalances->get($account->id)->closing_balance);
                } else {
                    $earliestDate = JournalEntry::min('date');
                    $earliestCarbon = $earliestDate ? Carbon::parse($earliestDate)->startOfMonth() : null;
                    
                    if ($earliestCarbon && $startDate->greaterThan($earliestCarbon)) {
                        $history = JournalDetail::query()
                            ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
                            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
                            ->where('journal_details.account_id', $account->id)
                            ->where('journal_entries.date', '<', $startDate)
                            ->first();

                        $hDebit = $history ? floatval($history->total_debit) : 0.00;
                        $hCredit = $history ? floatval($history->total_credit) : 0.00;

                        if (in_array($account->type, ['asset', 'expense'])) {
                            $openingBalance = $hDebit - $hCredit;
                        } else {
                            $openingBalance = $hCredit - $hDebit;
                        }
                    }
                }

                $sum = $monthSums->get($account->id);
                $debitTotal = $sum ? floatval($sum->total_debit) : 0.00;
                $creditTotal = $sum ? floatval($sum->total_credit) : 0.00;

                if (in_array($account->type, ['asset', 'expense'])) {
                    $closingBalance = $openingBalance + $debitTotal - $creditTotal;
                } else {
                    $closingBalance = $openingBalance + $creditTotal - $debitTotal;
                }

                AccountMonthlyBalance::updateOrCreate(
                    [
                        'account_id' => $account->id,
                        'period_year' => $year,
                        'period_month' => $month,
                    ],
                    [
                        'opening_balance' => $openingBalance,
                        'debit_total' => $debitTotal,
                        'credit_total' => $creditTotal,
                        'closing_balance' => $closingBalance,
                        'is_dirty' => false,
                        'generated_at' => now(),
                    ]
                );
            }
        });
    }

    /**
     * Mark snapshots starting from the given date (year and month) as dirty.
     */
    public function markAsDirtyFrom(Carbon $date): void
    {
        $year = $date->year;
        $month = $date->month;

        AccountMonthlyBalance::query()
            ->where(function ($query) use ($year, $month) {
                $query->where('period_year', '>', $year)
                    ->orWhere(function ($q) use ($year, $month) {
                        $q->where('period_year', '=', $year)
                            ->where('period_month', '>=', $month);
                    });
            })
            ->update(['is_dirty' => true]);
    }
}
