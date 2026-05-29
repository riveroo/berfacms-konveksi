<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn () => canAccessMenu('admin/import-export'))
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\SupplierTemplateExport(),
                        'supplier_import_template.xlsx'
                    );
                }),
            Actions\Action::make('importSuppliers')
                ->label('Import Suppliers')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->visible(fn () => canAccessMenu('admin/import-export'))
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Choose CSV/Excel File')
                        ->required()
                        ->disk('local')
                        ->directory('temp-imports')
                        ->acceptedFileTypes([
                            'text/csv', 
                            'text/plain',
                            'application/vnd.ms-excel', 
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]),
                ])
                ->action(function (array $data) {
                    $realPath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['file']);
                    
                    $import = new \App\Imports\SupplierImport();
                    try {
                        \Maatwebsite\Excel\Facades\Excel::import($import, $realPath);
                        
                        \Illuminate\Support\Facades\Storage::disk('local')->delete($data['file']);
                        
                        $message = "{$import->getImportedCount()} suppliers imported, {$import->getSkippedCount()} suppliers skipped.";
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Import Successful')
                            ->body($message)
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Import Failed')
                            ->body('Error processing file: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
