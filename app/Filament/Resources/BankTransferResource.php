<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankTransferResource\Pages;
use App\Models\BankTransfer;
use App\Models\Account;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BankTransferResource extends Resource
{
    protected static ?string $model = BankTransfer::class;

    protected static ?string $slug = 'bank-transfers';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static bool $shouldRegisterNavigation = false; // Registered manually in AdminPanelProvider

    public static function canViewAny(): bool
    {
        return canAccessMenu('admin/bank-transfers');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('Trx Date')
                    ->required()
                    ->default(now()->format('Y-m-d')),
                Forms\Components\Select::make('from_account_id')
                    ->label('Choose Account From')
                    ->options(Account::where('type', 'asset')->where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('to_account_id')
                    ->label('Account To')
                    ->options(Account::where('type', 'asset')->where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->different('from_account_id'),
                Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->minValue(0.01)
                    ->required()
                    ->placeholder('e.g. 10000000'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Trx Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fromAccount.name')
                    ->label('COA From')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('toAccount.name')
                    ->label('COA To')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('from_account_id')
                    ->label('From Account')
                    ->options(Account::where('type', 'asset')->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->where('from_account_id', $data['value']);
                        }
                    }),
                Tables\Filters\SelectFilter::make('to_account_id')
                    ->label('To Account')
                    ->options(Account::where('type', 'asset')->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->where('to_account_id', $data['value']);
                        }
                    }),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->options(User::pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->where('user_id', $data['value']);
                        }
                    }),
            ])
            ->actions([
                // Read & Create Only: No Edit or Delete actions
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Read & Create Only: No Delete bulk action
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBankTransfers::route('/'),
        ];
    }
}
