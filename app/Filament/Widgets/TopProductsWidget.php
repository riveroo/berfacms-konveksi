<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class TopProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 5 Best Selling Products';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TransactionDetail::query()
                    ->select('product_id', 'variant_id', DB::raw('MIN(id) as id'), DB::raw('SUM(quantity) as total_qty'))
                    ->whereHas('transaction', function ($q) {
                        $q->where('status', '!=', 'cancelled')
                          ->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    })
                    ->groupBy('product_id', 'variant_id')
                    ->orderBy('total_qty', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('variant.variant_name')
                    ->label(__('product.variant_name'))
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('total_qty')
                    ->label('Total Sold')
                    ->alignEnd()
                    ->badge()
                    ->color('success'),
            ])
            ->paginated(false);
    }
}
