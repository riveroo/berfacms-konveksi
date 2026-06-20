<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('sidebar.Products');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.Catalog');
    }

    public static function canViewAny(): bool
    {
        return canAccessMenu('admin/products');
    }

    public static function getModelLabel(): string
    {
        return __('product.product_details');
    }

    public static function getPluralModelLabel(): string
    {
        return __('product.products');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Group::make([
                            Forms\Components\Section::make(fn () => __('product.product_details'))
                                ->schema([
                                    Forms\Components\TextInput::make('product_name')
                                        ->label(fn () => __('product.product_name'))
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true),
                                    Forms\Components\Textarea::make('description')
                                        ->label(fn () => __('product.description'))
                                        ->maxLength(65535)
                                        ->columnSpanFull()
                                        ->live(onBlur: true),
                                    Forms\Components\Toggle::make('is_active')
                                        ->label(fn () => __('product.active'))
                                        ->default(true)
                                        ->live(),
                                    Forms\Components\Toggle::make('is_service')
                                        ->label(fn () => __('product.is_service'))
                                        ->formatStateUsing(fn ($state) => $state === 'yes')
                                        ->dehydrateStateUsing(fn ($state) => $state ? 'yes' : null)
                                        ->live(),
                                    Forms\Components\TextInput::make('sort_order')
                                        ->label(fn () => __('product.urutan'))
                                        ->numeric()
                                        ->default(0)
                                        ->live(onBlur: true),
                                ])
                        ])->columnSpan(['default' => 3, 'md' => 2]),

                        Forms\Components\Group::make([
                            Forms\Components\Section::make(fn () => __('product.thumbnail'))
                                ->schema([
                                    Forms\Components\FileUpload::make('thumbnail')
                                        ->hiddenLabel()
                                        ->image()
                                        ->imageEditor()
                                        ->maxSize(2048)
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->disk('public')
                                        ->directory('product-thumbnails')
                                        ->visibility('public')
                                        ->columnSpanFull()
                                        ->live(),
                                ])
                        ])->columnSpan(['default' => 3, 'md' => 1]),
                    ]),

                Forms\Components\Section::make(fn () => __('product.variants'))
                    ->schema([
                        \Filament\Forms\Components\Livewire::make(\App\Livewire\ProductVariantsManager::class)
                            ->key('product-variants-manager')
                            ->dehydrated(false),
                    ])
                    ->hidden(fn (string $operation) => $operation === 'create')
                    ->columnSpanFull(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['variants.stocks']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label(fn() => __('product.product_name'))
                    ->searchable(),
                Tables\Columns\TextInputColumn::make('sort_order')
                    ->label(fn() => __('product.urutan'))
                    ->sortable()
                    ->rules(['nullable', 'integer', 'min:0']),
                Tables\Columns\TextColumn::make('variants_count')
                    ->label(fn() => __('product.variants'))
                    ->counts('variants'),
                Tables\Columns\TextColumn::make('price_range')
                    ->label(fn() => __('product.price_range'))
                    ->getStateUsing(function (Product $record) {
                        $prices = $record->variants->flatMap->stocks->pluck('price')->filter();
                        if ($prices->isEmpty()) {
                            return 'Rp 0';
                        }
                        $min = $prices->min();
                        $max = $prices->max();
                        if ($min == $max) {
                            return 'Rp ' . number_format($min, 0, ',', '.');
                        }
                        return 'Rp ' . number_format($min, 0, ',', '.') . ' - Rp ' . number_format($max, 0, ',', '.');
                    })
                    ->fontFamily('outfit')
                    ->color('rose'),
                Tables\Columns\TextColumn::make('total_stock')
                    ->label(fn() => __('product.total_stock'))
                    ->getStateUsing(function (Product $record) {
                        return $record->variants->flatMap->stocks->sum('stock');
                    })
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state <= 5 => 'danger',
                        $state <= 20 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(fn() => __('product.active'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(fn() => __('product.product_details')),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
