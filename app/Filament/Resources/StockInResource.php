<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockInResource\Pages;
use App\Filament\Resources\StockInResource\RelationManagers;
use App\Models\StockIn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockInResource extends Resource
{
    protected static ?string $model = StockIn::class;
    protected static ?string $slug = 'stock-in';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Stock In';
    protected static ?int $navigationSort = 3;

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
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('size_option_id', null)),

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
                            ->disabled(fn (Forms\Get $get) => ! $get('variant_id')),

                        // Material Selection
                        Forms\Components\Select::make('item_id')
                            ->label('Material Name')
                            ->relationship('item', 'item_name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Forms\Get $get) => $get('item_type') === 'material')
                            ->visible(fn (Forms\Get $get) => $get('item_type') === 'material'),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Update Stock (Quantity)')
                            ->numeric()
                            ->required()
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
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Update Stock')
                    ->numeric()
                    ->sortable(),
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
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from_date'] ?? null) {
                            $indicators[] = 'From ' . $data['from_date'];
                        }
                        if ($data['to_date'] ?? null) {
                            $indicators[] = 'To ' . $data['to_date'];
                        }
                        return $indicators;
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
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn (Tables\Table $table) => \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\StockInExport($table->getFilteredQuery()),
                        'stock_in_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                    )),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\StockInExport(\App\Models\StockIn::whereIn('id', $records->pluck('id'))),
                            'stock_in_selected_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                        )),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStockIns::route('/'),
        ];
    }
}
