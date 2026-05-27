<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpeningBalanceResource\Pages;
use App\Models\OpeningBalance;
use App\Models\Account;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OpeningBalanceResource extends Resource
{
    protected static ?string $model = OpeningBalance::class;

    protected static ?string $slug = 'opening-balance';

    protected static bool $shouldRegisterNavigation = false; // Registered manually in AdminPanelProvider

    public static function canViewAny(): bool
    {
        return canAccessMenu('admin/opening-balance');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('Trx Date')
                    ->required()
                    ->default(now()->format('Y-m-d')),
                Forms\Components\Select::make('account_id')
                    ->label('Choose Account')
                    ->options(Account::where('type', 'asset')->where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('counter_account_id')
                    ->label('Counter Account')
                    ->options(Account::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->different('account_id'),
                Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->minValue(0.01)
                    ->required()
                    ->placeholder('e.g. 50000000'),
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
                Tables\Columns\TextColumn::make('account.name')
                    ->label('COA')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                Tables\Columns\TextColumn::make('counterAccount.name')
                    ->label('Category (counter account)')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('account_id')
                    ->label('COA')
                    ->options(Account::where('type', 'asset')->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->where('account_id', $data['value']);
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
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOpeningBalances::route('/'),
        ];
    }
}
