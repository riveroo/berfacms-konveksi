<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockOutResource\Pages;
use App\Filament\Resources\StockOutResource\RelationManagers;
use App\Models\StockOut;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockOutResource extends Resource
{
    protected static ?string $model = StockOut::class;
    protected static ?string $slug = 'stock-out';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('sidebar.Stock Out');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.Inventory');
    }

    public static function canViewAny(): bool
    {
        return canAccessMenu('admin/stock-out');
    }

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
                            ->label(fn () => __('stock.item_type'))
                            ->options(fn () => [
                                'product' => __('stock.product'),
                                'material' => __('stock.material'),
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set) {
                                $set('product_id', null);
                                $set('variant_id', null);
                                $set('size_option_id', null);
                                $set('item_id', null);
                            }),

                        // Product Selection Flow
                        Forms\Components\Select::make('product_id')
                            ->label(fn () => __('stock.product'))
                            ->relationship('product', 'product_name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Forms\Get $get) => $get('item_type') === 'product')
                            ->visible(fn (Forms\Get $get) => $get('item_type') === 'product')
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set) {
                                $set('variant_id', null);
                                $set('size_option_id', null);
                            }),

                        Forms\Components\Select::make('variant_id')
                            ->label(fn () => __('stock.variant'))
                            ->options(fn (Forms\Get $get) => 
                                \App\Models\Variant::where('product_id', $get('product_id'))
                                    ->pluck('variant_name', 'id')
                            )
                            ->searchable()
                            ->required(fn (Forms\Get $get) => $get('item_type') === 'product')
                            ->visible(fn (Forms\Get $get) => $get('item_type') === 'product')
                            ->disabled(fn (Forms\Get $get) => ! $get('product_id'))
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('size_option_id', null)),

                        Forms\Components\Select::make('size_option_id')
                            ->label(fn () => __('stock.size_option'))
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
                            ->disabled(fn (Forms\Get $get) => ! $get('variant_id')),

                        // Material Selection
                        Forms\Components\Select::make('item_id')
                            ->label(fn () => __('stock.material_name'))
                            ->relationship('item', 'item_name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Forms\Get $get) => $get('item_type') === 'material')
                            ->visible(fn (Forms\Get $get) => $get('item_type') === 'material'),

                        Forms\Components\TextInput::make('quantity')
                            ->label(fn () => __('stock.update_stock_qty'))
                            ->numeric()
                            ->required()
                            ->disabled(fn (Forms\Get $get) => ! $get('item_type'))
                            ->rules([
                                fn (Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $service = new \App\Services\StockMovementService();
                                    if (!$service->checkSufficiency(
                                        $get('item_type'),
                                        $get('product_id'),
                                        $get('variant_id'),
                                        $get('size_option_id'),
                                        $get('item_id'),
                                        $value
                                    )) {
                                        $fail(__('stock.insufficient_stock'));
                                    }
                                },
                            ]),

                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label(fn () => __('stock.no'))
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('trx_date')
                    ->label(fn () => __('stock.trx_date'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('item_type')
                    ->label(fn () => __('stock.item_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('stock.' . $state))
                    ->color(fn (string $state): string => match ($state) {
                        'product' => 'success',
                        'material' => 'info',
                    }),
                Tables\Columns\TextColumn::make('item_name')
                    ->label(fn () => __('stock.item_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(fn () => __('stock.update_stock'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(fn () => __('stock.user'))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('trx_date')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label(fn () => __('stock.from_date'))
                            ->live(),
                        Forms\Components\DatePicker::make('to_date')
                            ->label(fn () => __('stock.to_date'))
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
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from_date'] ?? null) {
                            $indicators[] = __('stock.from_date') . ': ' . $data['from_date'];
                        }
                        if ($data['to_date'] ?? null) {
                            $indicators[] = __('stock.to_date') . ': ' . $data['to_date'];
                        }
                        return $indicators;
                    }),
                Tables\Filters\SelectFilter::make('item_type')
                    ->label(fn () => __('stock.item_type'))
                    ->options(fn () => [
                        'product' => __('stock.product'),
                        'material' => __('stock.material'),
                    ]),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label(fn () => __('stock.user'))
                    ->relationship('user', 'name'),
            ])
            ->actions([
                // Edit and Delete removed as per request
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label(fn () => __('stock.export_excel'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn (Tables\Table $table) => \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\StockOutExport($table->getFilteredQuery()),
                        'stock_out_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                    )),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export')
                        ->label(fn () => __('stock.export_selected'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\StockOutExport(\App\Models\StockOut::whereIn('id', $records->pluck('id'))),
                            'stock_out_selected_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                        )),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStockOuts::route('/'),
        ];
    }
}
