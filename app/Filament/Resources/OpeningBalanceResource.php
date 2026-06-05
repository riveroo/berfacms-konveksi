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
                    ->label(fn () => __('finance.trx_date'))
                    ->required()
                    ->default(now()->format('Y-m-d')),
                Forms\Components\Select::make('account_id')
                    ->label(fn () => __('finance.choose_account'))
                    ->options(Account::where('type', 'asset')->where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('counter_account_id')
                    ->label(fn () => __('finance.counter_account_label'))
                    ->options(Account::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->different('account_id'),
                Forms\Components\TextInput::make('amount')
                    ->label(fn () => __('finance.amount'))
                    ->numeric()
                    ->minValue(0.01)
                    ->required()
                    ->placeholder(__('finance.amount_placeholder')),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(fn () => __('finance.trx_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('account.name')
                    ->label(fn () => __('finance.coa'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(fn () => __('finance.amount'))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(fn () => __('finance.user'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('counterAccount.name')
                    ->label(fn () => __('finance.counter_account'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('account_id')
                    ->label(fn () => __('finance.coa'))
                    ->options(Account::where('type', 'asset')->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->where('account_id', $data['value']);
                        }
                    }),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label(fn () => __('finance.user'))
                    ->options(User::pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->where('user_id', $data['value']);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
