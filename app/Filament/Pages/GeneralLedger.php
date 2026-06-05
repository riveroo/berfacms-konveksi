<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\Account;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GeneralLedgerExport;

class GeneralLedger extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $slug = 'general-ledger';
    protected static string $view = 'filament.pages.general-ledger';

    // Navigation registration is manually handled in AdminPanelProvider for exact sorting order
    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return __('sidebar.General Ledger');
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return __('sidebar.General Ledger');
    }

    public ?string $accountId = null;
    public ?string $period = null;

    public array $balancesCache = [];
    public float $totalDebitValue = 0.0;
    public float $totalCreditValue = 0.0;
    public float $endingBalanceValue = 0.0;

    public static function canAccess(): bool
    {
        return canAccessMenu('admin/general-ledger');
    }

    public function mount(): void
    {
        $this->period = now()->format('Y-m');
    }

    public function updatedAccountId(): void
    {
        $this->resetTable();
    }

    public function updatedPeriod(): void
    {
        $this->resetTable();
    }

    public function getAccountsProperty()
    {
        return Account::where('is_active', true)
            ->orderBy('code', 'asc')
            ->get()
            ->mapWithKeys(fn ($acc) => [$acc->id => "{$acc->code} - {$acc->name}"]);
    }

    protected function getLedgerData(): array
    {
        if (!$this->accountId || !$this->period) {
            return [];
        }

        $parts = explode('-', $this->period);
        $year = isset($parts[0]) ? intval($parts[0]) : now()->year;
        $month = isset($parts[1]) ? intval($parts[1]) : now()->month;

        app(\App\Services\Accounting\MonthlyBalanceService::class)->ensureSnapshotsUpTo($year, $month);

        $account = Account::findOrFail($this->accountId);

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
            ->where('journal_details.account_id', $this->accountId)
            ->whereYear('journal_entries.date', $year)
            ->whereMonth('journal_entries.date', $month)
            ->orderBy('journal_entries.date', 'asc')
            ->orderBy('journal_details.id', 'asc')
            ->get();

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $prevMonthDate = $startDate->copy()->subDay()->format('Y-m-d');

        $snapshot = \App\Models\AccountMonthlyBalance::where('account_id', $this->accountId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->first();
        $startingBalance = $snapshot ? floatval($snapshot->opening_balance) : 0.00;

        $runningBalance = $startingBalance;
        $isAssetOrExpense = in_array($account->type, ['asset', 'expense']);

        $rows = [];
        // Prepend Saldo Awal row
        $rows[] = [
            'date' => $prevMonthDate,
            'code' => $account->code,
            'name' => $account->name . ' (' . __('finance.opening_balance') . ')',
            'debit' => 0.0,
            'credit' => 0.0,
            'balance' => $runningBalance,
        ];

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

    public function calculateBalances(): void
    {
        $this->balancesCache = [];
        $this->totalDebitValue = 0.0;
        $this->totalCreditValue = 0.0;
        $this->endingBalanceValue = 0.0;

        if (!$this->accountId || !$this->period) {
            return;
        }

        $data = $this->getLedgerData();
        if (empty($data)) {
            return;
        }

        $this->totalDebitValue = $data['totalDebit'];
        $this->totalCreditValue = $data['totalCredit'];
        $this->endingBalanceValue = $data['endingBalance'];

        // Repopulate balances cache keyed by detail ID
        $parts = explode('-', $this->period);
        $year = isset($parts[0]) ? intval($parts[0]) : now()->year;
        $month = isset($parts[1]) ? intval($parts[1]) : now()->month;

        app(\App\Services\Accounting\MonthlyBalanceService::class)->ensureSnapshotsUpTo($year, $month);

        $account = Account::find($this->accountId);
        if (!$account) return;

        $snapshot = \App\Models\AccountMonthlyBalance::where('account_id', $this->accountId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->first();
        $startingBalance = $snapshot ? floatval($snapshot->opening_balance) : 0.00;

        // Populate virtual opening balance row's balance
        $this->balancesCache[0] = $startingBalance;

        $details = JournalDetail::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->where('journal_details.account_id', $this->accountId)
            ->whereYear('journal_entries.date', $year)
            ->whereMonth('journal_entries.date', $month)
            ->orderBy('journal_entries.date', 'asc')
            ->orderBy('journal_details.id', 'asc')
            ->select('journal_details.*')
            ->get();

        $runningBalance = $startingBalance;
        $isAssetOrExpense = in_array($account->type, ['asset', 'expense']);

        foreach ($details as $detail) {
            $debit = floatval($detail->debit);
            $credit = floatval($detail->credit);

            if ($isAssetOrExpense) {
                $runningBalance += ($debit - $credit);
            } else {
                $runningBalance += ($credit - $debit);
            }

            $this->balancesCache[$detail->id] = $runningBalance;
        }
    }

    protected function getViewData(): array
    {
        $this->calculateBalances();
        return [];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                if (!$this->accountId || !$this->period) {
                    return JournalDetail::query()->whereRaw('1 = 0');
                }

                $parts = explode('-', $this->period);
                $year = isset($parts[0]) ? intval($parts[0]) : now()->year;
                $month = isset($parts[1]) ? intval($parts[1]) : now()->month;
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
                $prevMonthDate = $startDate->copy()->subDay()->format('Y-m-d');

                $openingQuery = DB::table('accounts')
                    ->select([
                        DB::raw('0 as id'),
                        DB::raw('0 as journal_entry_id'),
                        DB::raw('accounts.id as account_id'),
                        DB::raw('0.00 as debit'),
                        DB::raw('0.00 as credit'),
                        DB::raw('NULL as created_at'),
                        DB::raw('NULL as updated_at'),
                        DB::raw("'$prevMonthDate' as entry_date"),
                        DB::raw("'Saldo Awal' as entry_description"),
                        DB::raw('accounts.name as account_name'),
                        DB::raw('accounts.code as account_code'),
                    ])
                    ->where('accounts.id', $this->accountId);

                $detailsQuery = JournalDetail::query()
                    ->select([
                        'journal_details.id',
                        'journal_details.journal_entry_id',
                        'journal_details.account_id',
                        'journal_details.debit',
                        'journal_details.credit',
                        'journal_details.created_at',
                        'journal_details.updated_at',
                        'journal_entries.date as entry_date',
                        'journal_entries.description as entry_description',
                        'accounts.name as account_name',
                        'accounts.code as account_code'
                    ])
                    ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
                    ->join('accounts', 'accounts.id', '=', 'journal_details.account_id')
                    ->where('journal_details.account_id', $this->accountId)
                    ->whereYear('journal_entries.date', $year)
                    ->whereMonth('journal_entries.date', $month);

                return JournalDetail::fromSub($openingQuery->unionAll($detailsQuery), 'journal_details');
            })
            ->columns([
                TextColumn::make('entry_date')
                    ->label(__('finance.trx_date'))
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('account_code')
                    ->label(__('finance.code')),
                TextColumn::make('account_name')
                    ->label(__('finance.name'))
                    ->formatStateUsing(fn ($record, $state) => $record->id == 0 ? $state . ' (' . __('finance.opening_balance') . ')' : $state),
                TextColumn::make('debit')
                    ->label(__('finance.debit'))
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'Rp ' . number_format($state, 0, ',', '.') : '-')
                    ->alignEnd()
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->label(__('finance.total_debit'))->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))),
                TextColumn::make('credit')
                    ->label(__('finance.credit'))
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'Rp ' . number_format($state, 0, ',', '.') : '-')
                    ->alignEnd()
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->label(__('finance.total_credit'))->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))),
                TextColumn::make('balance')
                    ->label(__('finance.balance'))
                    ->state(fn ($record) => $this->balancesCache[$record->id] ?? 0.0)
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->alignEnd()
                    ->weight('bold')
                    ->summarize(\Filament\Tables\Columns\Summarizers\Summarizer::make()->label(__('finance.ending_balance'))->using(fn () => $this->endingBalanceValue)->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))),
            ])
            ->defaultSort('entry_date', 'asc')
            ->paginated(false); // Disable pagination to compute continuous running balance cleanly
    }
}
