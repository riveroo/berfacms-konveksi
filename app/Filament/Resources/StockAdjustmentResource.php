<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockAdjustmentResource\Pages;
use App\Filament\Resources\StockAdjustmentResource\RelationManagers;
use App\Models\StockAdjustment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = \App\Models\StockAdjustment::class;
    protected static ?string $slug = 'adjustment';

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Adjustment';
    protected static ?int $navigationSort = 5;

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('item_type')
                            ->label('Item Type')
                            ->options([
                                'product' => 'Product',
                                'material' => 'Material',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set) {
                                $set('product_id', null);
                                $set('variant_id', null);
                                $set('size_option_id', null);
                                $set('item_id', null);
                                $set('current_stock', 0);
                            }),

                        // Product Selection Flow
                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'product_name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Forms\Get $get) => $get('item_type') === 'product')
                            ->visible(fn (Forms\Get $get) => $get('item_type') === 'product')
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set) {
                                $set('variant_id', null);
                                $set('size_option_id', null);
                                $set('current_stock', 0);
                            }),

                        Forms\Components\Select::make('variant_id')
                            ->label('Variant')
                            ->options(fn (Forms\Get $get) => 
                                \App\Models\Variant::where('product_id', $get('product_id'))
                                    ->pluck('variant_name', 'id')
                            )
                            ->searchable()
                            ->required(fn (Forms\Get $get) => $get('item_type') === 'product')
                            ->visible(fn (Forms\Get $get) => $get('item_type') === 'product')
                            ->disabled(fn (Forms\Get $get) => ! $get('product_id'))
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $set('size_option_id', null);
                                $service = new \App\Services\StockMovementService();
                                $set('current_stock', $service->getCurrentStock($get('item_type'), $get('variant_id'), null, $get('item_id')));
                            }),

                        Forms\Components\Select::make('size_option_id')
                            ->label('Size Option')
                            ->options(fn (Forms\Get $get) => 
                                \App\Models\Stock::where('variant_id', $get('variant_id'))
                                    ->whereNotNull('size_option_id')
                                    ->with('sizeOption')
                                    ->get()
                                    ->pluck('sizeOption.name', 'size_option_id')
                            )
                            ->visible(fn (Forms\Get $get) => 
                                $get('item_type') === 'product' && 
                                \App\Models\Stock::where('variant_id', $get('variant_id'))->whereNotNull('size_option_id')->exists()
                            )
                            ->required(fn (Forms\Get $get) => 
                                $get('item_type') === 'product' && 
                                \App\Models\Stock::where('variant_id', $get('variant_id'))->whereNotNull('size_option_id')->exists()
                            )
                            ->disabled(fn (Forms\Get $get) => ! $get('variant_id'))
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $service = new \App\Services\StockMovementService();
                                $set('current_stock', $service->getCurrentStock($get('item_type'), $get('variant_id'), $get('size_option_id'), $get('item_id')));
                            }),

                        // Material Selection
                        Forms\Components\Select::make('item_id')
                            ->label('Material Name')
                            ->relationship('item', 'item_name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Forms\Get $get) => $get('item_type') === 'material')
                            ->visible(fn (Forms\Get $get) => $get('item_type') === 'material')
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $service = new \App\Services\StockMovementService();
                                $set('current_stock', $service->getCurrentStock($get('item_type'), $get('variant_id'), $get('size_option_id'), $get('item_id')));
                            }),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('current_stock')
                                    ->label('Current Stock')
                                    ->numeric()
                                    ->readonly()
                                    ->dehydrated(false)
                                    ->placeholder('0'),
                                Forms\Components\TextInput::make('new_stock')
                                    ->label('New Stock')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->disabled(fn (Forms\Get $get) => ! $get('item_type')),
                            ]),

                        Forms\Components\Textarea::make('reason')
                            ->label('Adjustment Reason')
                            ->required()
                            ->rows(3)
                            ->placeholder('e.g., Stock opname finding, Damaged goods, etc.')
                            ->disabled(fn (Forms\Get $get) => ! $get('item_type')),

                        Forms\Components\DateTimePicker::make('trx_date')
                            ->label('TRX Date')
                            ->default(now())
                            ->required()
                            ->disabled(fn (Forms\Get $get) => ! $get('item_type')),

                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('trx_date')
                    ->label('TRX Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('item_type')
                    ->label('Item Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'product' => 'success',
                        'material' => 'info',
                    }),
                Tables\Columns\TextColumn::make('item_name')
                    ->label('Item Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('old_stock')
                    ->label('Old Stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('new_stock')
                    ->label('New Stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('difference')
                    ->label('Difference')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => ($state > 0 ? '+' : '') . $state),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('trx_date')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('From Date')
                            ->live(),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('To Date')
                            ->disabled(fn (Forms\Get $get) => ! $get('from_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('trx_date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('trx_date', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('item_type')
                    ->label('Item Type')
                    ->options([
                        'product' => 'Product',
                        'material' => 'Material',
                    ]),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                // Edit and Delete removed as per request
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Delete removed as per request
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStockAdjustments::route('/'),
        ];
    }
}
