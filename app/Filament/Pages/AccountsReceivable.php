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
    protected static ?string $navigationLabel = 'Accounts Receivable';
    protected static ?string $slug = 'accounts-receivable';
    protected static string $view = 'filament.pages.accounts-receivable';

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
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('total_transactions')
                    ->label('Total Transactions')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                \Filament\Tables\Columns\TextColumn::make('total_paid')
                    ->label('Total Paid')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                \Filament\Tables\Columns\TextColumn::make('outstanding_receivable')
                    ->label('Outstanding Receivable')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ])
            ->defaultSort('outstanding_receivable', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
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
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading('Accounts Receivable Detail')
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn (Client $record) => view('filament.pages.accounts-receivable-detail-modal', [
                        'client' => $record,
                    ])),
            ]);
    }
}
