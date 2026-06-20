<?php

namespace App\Filament\Resources\StockInResource\Pages;

use App\Filament\Resources\StockInResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStockIns extends ManageRecords
{
    protected static string $resource = StockInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bulk_stock_in')
                ->label(fn () => __('stock.bulk_stock_in'))
                ->url('/admin/stock-in/bulk')
                ->color('primary')
                ->icon('heroicon-o-plus-circle'),
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
