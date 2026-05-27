<?php

namespace App\Filament\Resources\BankTransferResource\Pages;

use App\Filament\Resources\BankTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBankTransfers extends ManageRecords
{
    protected static string $resource = BankTransferResource::class;

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
