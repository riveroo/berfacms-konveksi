<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use Illuminate\Database\Eloquent\Builder;

class Customers extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Customers';
    protected static ?string $slug = 'customers';
    protected static string $view = 'filament.pages.customers';

    // We manually register this inside AdminPanelProvider to enforce exact ordering
    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return canAccessMenu('admin/customers');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn () => canAccessMenu('admin/import-export'))
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\CustomerTemplateExport(),
                        'customer_import_template.xlsx'
                    );
                }),
            \Filament\Actions\Action::make('importCustomer')
                ->label('Import Customer')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->visible(fn () => canAccessMenu('admin/import-export'))
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Choose CSV/Excel File')
                        ->required()
                        ->disk('local')
                        ->directory('temp-imports')
                        ->acceptedFileTypes([
                            'text/csv', 
                            'text/plain',
                            'application/vnd.ms-excel', 
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]),
                ])
                ->action(function (array $data) {
                    $realPath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['file']);
                    
                    $import = new \App\Imports\CustomerImport();
                    try {
                        \Maatwebsite\Excel\Facades\Excel::import($import, $realPath);
                        
                        \Illuminate\Support\Facades\Storage::disk('local')->delete($data['file']);
                        
                        $message = "{$import->getImportedCount()} customers imported, {$import->getSkippedCount()} customers skipped.";
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Import Successful')
                            ->body($message)
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Import Failed')
                            ->body('Error processing file: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->query(
                Client::select('clients.*')
                    ->selectSub(
                        Transaction::selectRaw('COALESCE(COUNT(id), 0)')
                            ->whereColumn('client_id', 'clients.id')
                            ->where('status', '!=', 'cancelled'),
                        'total_transactions'
                    )
                    ->selectSub(
                        Transaction::selectRaw('COALESCE(SUM(grand_total), 0)')
                            ->whereColumn('client_id', 'clients.id')
                            ->where('status', '!=', 'cancelled'),
                        'total_transaction_amount'
                    )
                    ->selectRaw(
                        '((SELECT COALESCE(SUM(grand_total), 0) FROM transactions WHERE transactions.client_id = clients.id AND transactions.status != "cancelled") - 
                         (SELECT COALESCE(SUM(amount), 0) FROM transaction_payments WHERE transaction_payments.transaction_id IN (SELECT id FROM transactions WHERE transactions.client_id = clients.id AND transactions.status != "cancelled"))) as total_outstanding_balance'
                    )
            )
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('client_name')
                    ->label('Customer Name')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('total_transactions')
                    ->label('Total Transactions')
                    ->sortable()
                    ->alignCenter(),
                \Filament\Tables\Columns\TextColumn::make('total_transaction_amount')
                    ->label('Total Transaction Amount')
                    ->sortable()
                    ->alignEnd()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                \Filament\Tables\Columns\TextColumn::make('total_outstanding_balance')
                    ->label('Total Outstanding Balance')
                    ->sortable()
                    ->alignEnd()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ])
            ->defaultSort('total_outstanding_balance', 'desc')
            ->filters([
                \Filament\Tables\Filters\Filter::make('client_name_search')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('client_name')
                            ->label('Customer Name')
                            ->placeholder('Search by customer name...'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['client_name'],
                            fn (Builder $query, $name): Builder => $query->where('client_name', 'like', "%{$name}%")
                        );
                    })
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading('Customer Detail')
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn (Client $record) => view('filament.pages.accounts-receivable-detail-modal', [
                        'client' => $record,
                    ])),
            ]);
    }
}
