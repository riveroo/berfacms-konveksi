<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Transaction;

class TopTransactionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 5 Highest Value Transactions';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->select('client_id', \Illuminate\Support\Facades\DB::raw('MIN(id) as id'), \Illuminate\Support\Facades\DB::raw('SUM(grand_total) as total_amount'))
                    ->where('status', '!=', 'cancelled')
                    ->whereBetween('created_at', [now()->startOfMonth(), now()])
                    ->groupBy('client_id')
                    ->orderBy('total_amount', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('client.client_name')
                    ->label('Customer')
                    ->weight('bold')
                    ->default('-'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->alignEnd()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ])
            ->paginated(false);
    }
}
