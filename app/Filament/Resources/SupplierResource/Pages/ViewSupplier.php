<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables;
use Filament\Forms;
use App\Models\Item;

class ViewSupplier extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = SupplierResource::class;

    protected static string $view = 'filament.resources.supplier-resource.pages.view-supplier';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(Item::query()->where('supplier_id', $this->record->id))
            ->columns([
                Tables\Columns\TextColumn::make('item_code')
                    ->label('Item Code')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('item_name')
                    ->label('Item Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('productType.name')
                    ->label('Product Type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->sortable()
                    ->alignEnd()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('minimum_stock')
                    ->label('Minimum Stock')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('addItem')
                    ->label('Add Item')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->form([
                        Forms\Components\TextInput::make('item_id')
                            ->required()
                            ->unique('items', 'item_id')
                            ->maxLength(255)
                            ->default(function () {
                                $lastItem = \App\Models\Item::where('item_id', 'like', 'ITM-%')
                                    ->latest('id')
                                    ->first();
                                
                                $nextNumber = 1;
                                if ($lastItem) {
                                    $parts = explode('-', $lastItem->item_id);
                                    if (isset($parts[1]) && is_numeric($parts[1])) {
                                        $nextNumber = (int)$parts[1] + 1;
                                    }
                                }
                                
                                return 'ITM-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                            })
                            ->disabled()
                            ->dehydrated(true),
                        Forms\Components\TextInput::make('item_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('item_code')
                            ->required()
                            ->unique('items', 'item_code')
                            ->maxLength(255),
                        Forms\Components\Select::make('product_type_id')
                            ->relationship('productType', 'name')
                            ->required(),
                        Forms\Components\Select::make('unit_id')
                            ->relationship('unit', 'name')
                            ->required(),
                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->default($this->record->id)
                            ->disabled()
                            ->dehydrated(true),
                        Forms\Components\TextInput::make('minimum_stock')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ])
                    ->action(function (array $data) {
                        Item::create($data);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Item Created')
                            ->body('The new supplier item has been added successfully.')
                            ->success()
                            ->send();
                    })
            ]);
    }
}
