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
    protected static ?string $navigationGroup = 'Catalog';

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
                            Forms\Components\Section::make('Product Details')
                                ->schema([
                                    Forms\Components\TextInput::make('product_name')
                                        ->label(fn () => __('product.product_name'))
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make('description')
                                        ->label(fn () => __('product.description'))
                                        ->maxLength(65535)
                                        ->columnSpanFull(),
                                    Forms\Components\Toggle::make('is_active')
                                        ->label('Active')
                                        ->default(true),
                                ])
                        ])->columnSpan(['default' => 3, 'md' => 2]),

                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Thumbnail')
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
                                        ->columnSpanFull(),
                                ])
                        ])->columnSpan(['default' => 3, 'md' => 1]),
                    ]),

                Forms\Components\Repeater::make('variants')
                    ->relationship('variants')
                    ->schema([
                        Forms\Components\Grid::make(4)->schema([
                            Forms\Components\TextInput::make('variant_name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('variant_code')
                                ->label('Variant Code')
                                ->maxLength(100),
                            Forms\Components\ColorPicker::make('color')
                                ->hex()
                                ->required(),
                            Forms\Components\Select::make('product_type_id')
                                ->relationship('productType', 'name')
                                ->label('Product Type')
                                ->required(),
                        ]),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->disk('public')
                            ->directory('variant-images')
                            ->visibility('public')
                            ->openable()
                            ->downloadable(),
                        Forms\Components\Section::make('Stocks & Pricing')
                            ->schema([
                                \Awcodes\TableRepeater\Components\TableRepeater::make('stocks')
                                    ->relationship('stocks')
                                    ->hiddenLabel()
                                    ->headers([
                                        \Awcodes\TableRepeater\Header::make('size_option_id')->label('Size')->markAsRequired(),
                                        \Awcodes\TableRepeater\Header::make('stock')->markAsRequired(),
                                        \Awcodes\TableRepeater\Header::make('price')->markAsRequired(),
                                    ])
                                    ->schema([
                                        Forms\Components\Select::make('size_option_id')
                                            ->relationship('sizeOption', 'name')
                                            ->label('Size')
                                            ->required(),
                                        Forms\Components\TextInput::make('stock')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0),
                                        Forms\Components\TextInput::make('price')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->prefix('Rp'),
                                    ])
                                    ->addActionLabel('Add Stock')
                                    ->deletable(true)
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->compact(),
                    ])
                    ->columns(1)
                    ->addActionLabel('Add Variant')
                    ->deletable(true)
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
                Tables\Columns\TextColumn::make('variants_count')
                    ->label('Variants')
                    ->counts('variants'),
                Tables\Columns\TextColumn::make('price_range')
                    ->label('Price Range')
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
                    ->label('Total Stock')
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
                    ->label('Active')
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
                    ->label('Details'),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
