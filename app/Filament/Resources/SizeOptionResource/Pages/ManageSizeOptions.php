<?php

namespace App\Filament\Resources\SizeOptionResource\Pages;

use App\Filament\Resources\SizeOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSizeOptions extends ManageRecords
{
    protected static string $resource = SizeOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
