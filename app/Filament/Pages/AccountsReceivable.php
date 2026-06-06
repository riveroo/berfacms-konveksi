<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use Illuminate\Database\Eloquent\Builder;

class AccountsReceivable extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $slug = 'accounts-receivable';
    protected static string $view = 'filament.pages.accounts-receivable';

    public function getTitle(): string
    {
        return __('accounts_receivable.title');
    }

    // We manually register this inside AdminPanelProvider to enforce exact ordering
    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return canAccessMenu('admin/accounts-receivable');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\AccountsReceivableOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->query(
                Client::whereHas('transactions', function ($q) {
                    $q->where('status', '!=', 'cancelled');
                })
                ->select('clients.*')
                ->selectSub(
                    Transaction::selectRaw('COALESCE(SUM(grand_total), 0)')
                        ->whereColumn('client_id', 'clients.id')
                        ->where('status', '!=', 'cancelled'),
                    'total_transactions'
                )
                ->selectSub(
                    TransactionPayment::selectRaw('COALESCE(SUM(amount), 0)')
                        ->whereIn('transaction_id', Transaction::select('id')->whereColumn('client_id', 'clients.id')->where('status', '!=', 'cancelled')),
                    'total_paid'
                )
                ->selectRaw(
                    '(SELECT COALESCE(SUM(grand_total), 0) FROM transactions WHERE transactions.client_id = clients.id AND transactions.status != "cancelled") - 
                     (SELECT COALESCE(SUM(amount), 0) FROM transaction_payments WHERE transaction_payments.transaction_id IN (SELECT id FROM transactions WHERE transactions.client_id = clients.id AND transactions.status != "cancelled")) as outstanding_receivable'
                )
            )
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                \Filament\Tables\Columns\TextColumn::make('client_name')
                    ->label(__('accounts_receivable.customer_name'))
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('total_transactions')
                    ->label(__('accounts_receivable.total_transactions'))
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                \Filament\Tables\Columns\TextColumn::make('total_paid')
                    ->label(__('accounts_receivable.total_paid'))
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                \Filament\Tables\Columns\TextColumn::make('outstanding_receivable')
                    ->label(__('accounts_receivable.outstanding_receivable'))
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ])
            ->defaultSort('outstanding_receivable', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('payment_status')
                    ->label(__('accounts_receivable.payment_status'))
                    ->options([
                        'paid' => __('accounts_receivable.paid'),
                        'unpaid' => __('accounts_receivable.unpaid'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'paid') {
                            $query->whereRaw('( (SELECT COALESCE(SUM(grand_total), 0) FROM transactions WHERE transactions.client_id = clients.id AND transactions.status != "cancelled") - (SELECT COALESCE(SUM(amount), 0) FROM transaction_payments WHERE transaction_payments.transaction_id IN (SELECT id FROM transactions WHERE transactions.client_id = clients.id AND transactions.status != "cancelled")) ) <= 0');
                        } elseif ($data['value'] === 'unpaid') {
                            $query->whereRaw('( (SELECT COALESCE(SUM(grand_total), 0) FROM transactions WHERE transactions.client_id = clients.id AND transactions.status != "cancelled") - (SELECT COALESCE(SUM(amount), 0) FROM transaction_payments WHERE transaction_payments.transaction_id IN (SELECT id FROM transactions WHERE transactions.client_id = clients.id AND transactions.status != "cancelled")) ) > 0');
                        }
                    }),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('detail')
                    ->label(__('accounts_receivable.detail'))
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading(__('accounts_receivable.modal_heading'))
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('accounts_receivable.close'))
                    ->modalContent(fn (Client $record) => view('filament.pages.accounts-receivable-detail-modal', [
                        'client' => $record,
                    ])),
            ]);
    }
}
