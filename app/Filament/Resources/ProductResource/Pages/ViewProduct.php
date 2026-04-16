<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Store Preview')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn($record) => url('/products/' . $record->id))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
        ];
    }
}
