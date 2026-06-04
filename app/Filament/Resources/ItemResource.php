<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('sidebar.Items');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.Inventory');
    }

    public static function canViewAny(): bool
    {
        return canAccessMenu('admin/items');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('item_id')
                    ->required()
                    ->unique(ignoreRecord: true)
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
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('product_type_id')
                    ->relationship('productType', 'name')
                    ->required(),
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->required(),
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name'),
                Forms\Components\TextInput::make('minimum_stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('item_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('productType.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->label('Supplier'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, Item $record) {
                        if (
                            \DB::table('stock_ins')->where('item_id', $record->id)->exists() ||
                            \DB::table('stock_outs')->where('item_id', $record->id)->exists() ||
                            \DB::table('stock_adjustments')->where('item_id', $record->id)->exists() ||
                            \DB::table('production_materials')->where('item_id', $record->id)->exists()
                        ) {
                            \Filament\Notifications\Notification::make()
                                ->title('Cannot Delete Item')
                                ->body("The item \"{$record->item_name}\" cannot be deleted because it is in use by transactions or production.")
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Tables\Actions\DeleteBulkAction $action, \Illuminate\Support\Collection $records) {
                            foreach ($records as $record) {
                                if (
                                    \DB::table('stock_ins')->where('item_id', $record->id)->exists() ||
                                    \DB::table('stock_outs')->where('item_id', $record->id)->exists() ||
                                    \DB::table('stock_adjustments')->where('item_id', $record->id)->exists() ||
                                    \DB::table('production_materials')->where('item_id', $record->id)->exists()
                                ) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Cannot Delete Selected Items')
                                        ->body("One or more of the selected items cannot be deleted because they are in use by transactions or production.")
                                        ->danger()
                                        ->send();

                                    $action->cancel();
                                    return;
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
