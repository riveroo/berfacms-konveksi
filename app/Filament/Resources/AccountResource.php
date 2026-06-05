<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Accounting';
    
    protected static ?string $navigationLabel = 'C.O.A (Chart Of Accounts)';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Account';
    
    protected static ?string $pluralModelLabel = 'C.O.A (Chart Of Accounts)';

    protected static ?string $slug = 'coa';

    public static function canViewAny(): bool
    {
        return canAccessMenu('admin/coa');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(fn () => __('finance.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('e.g. 1001'),
                        Forms\Components\TextInput::make('name')
                            ->label(fn () => __('finance.name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Cash'),
                        Forms\Components\Select::make('type')
                            ->label(fn () => __('finance.type'))
                            ->required()
                            ->options(fn () => [
                                'asset' => __('finance.asset'),
                                'liability' => __('finance.liability'),
                                'equity' => __('finance.equity'),
                                'revenue' => __('finance.revenue'),
                                'expense' => __('finance.expense'),
                            ])
                            ->native(false),
                        Forms\Components\Select::make('parent_id')
                            ->label(fn () => __('finance.parent_account'))
                            ->options(Account::pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(fn () => __('finance.active_status'))
                            ->default(true),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('code', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(fn () => __('finance.code'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->label(fn () => __('finance.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record, $state) {
                        $depth = 0;
                        $parent = $record->parent;
                        while ($parent) {
                            $depth++;
                            $parent = $parent->parent;
                        }
                        return str_repeat('— ', $depth) . $state;
                    }),
                Tables\Columns\TextColumn::make('type')
                    ->label(fn () => __('finance.type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('finance.' . $state))
                    ->color(fn (string $state): string => match ($state) {
                        'asset' => 'success',
                        'liability' => 'danger',
                        'equity' => 'info',
                        'revenue' => 'warning',
                        'expense' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label(fn () => __('finance.parent_account'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label(fn () => __('finance.status')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(fn () => __('finance.type'))
                    ->options(fn () => [
                        'asset' => __('finance.asset'),
                        'liability' => __('finance.liability'),
                        'equity' => __('finance.equity'),
                        'revenue' => __('finance.revenue'),
                        'expense' => __('finance.expense'),
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(fn () => __('finance.active_status')),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAccounts::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
