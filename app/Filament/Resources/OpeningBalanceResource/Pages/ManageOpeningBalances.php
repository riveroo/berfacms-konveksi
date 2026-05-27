<?php

namespace App\Filament\Resources\OpeningBalanceResource\Pages;

use App\Filament\Resources\OpeningBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOpeningBalances extends ManageRecords
{
    protected static string $resource = OpeningBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = auth()->id();
                    return $data;
                }),
        ];
    }
}
