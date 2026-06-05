<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return __('sidebar.Suppliers');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.Inventory');
    }

    public static function canViewAny(): bool
    {
        return canAccessMenu('admin/suppliers');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(fn () => __('supplier.supplier_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact')
                    ->label(fn () => __('supplier.contact'))
                    ->maxLength(255),
                Forms\Components\Textarea::make('information')
                    ->label(fn () => __('supplier.information'))
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('address')
                    ->label(fn () => __('supplier.address'))
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Supplier::query()
                    ->select('suppliers.*')
                    ->selectSub(
                        \App\Models\Item::selectRaw('COALESCE(COUNT(id), 0)')
                            ->whereColumn('supplier_id', 'suppliers.id'),
                        'total_items'
                    )
                    ->selectSub(
                        \App\Models\Item::selectRaw('COALESCE(SUM(price), 0)')
                            ->whereColumn('supplier_id', 'suppliers.id'),
                        'total_transaction_amount'
                    )
                    ->selectRaw('0 as total_outstanding_balance')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(fn () => __('supplier.supplier_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact')
                    ->label(fn () => __('supplier.contact'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label(fn () => __('supplier.address'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_items')
                    ->label(fn () => __('supplier.total_items'))
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                Tables\Filters\Filter::make('name_search')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label(fn () => __('supplier.supplier_name'))
                            ->placeholder(fn () => __('supplier.search_placeholder')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['name'],
                            fn (Builder $query, $name): Builder => $query->where('name', 'like', "%{$name}%")
                        );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(fn () => __('supplier.detail')),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'view' => Pages\ViewSupplier::route('/{record}'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
