<?php

namespace App\Filament\Resources\StockOutResource\Pages;

use App\Filament\Resources\StockOutResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStockOuts extends ManageRecords
{
    protected static string $resource = StockOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bulk_stock_out')
                ->label(fn () => __('stock.bulk_stock_out'))
                ->url('/admin/stock-out/bulk')
                ->color('primary')
                ->icon('heroicon-o-minus-circle'),
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $timezone = session('device_timezone') ?? config('app.timezone');
                    $localTime = \Carbon\Carbon::now($timezone);
                    $now = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $localTime->toDateTimeString(), config('app.timezone'));
                    $data['trx_date'] = $now;
                    return $data;
                }),
        ];
    }
}
