<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAccounts extends ManageRecords
{
    protected static string $resource = AccountResource::class;

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
                        new \App\Exports\CoaTemplateExport(),
                        'coa_import_template.xlsx'
                    );
                }),
            Actions\Action::make('importCoa')
                ->label('Import C.O.A')
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
                    
                    $import = new \App\Imports\CoaImport();
                    try {
                        \Maatwebsite\Excel\Facades\Excel::import($import, $realPath);
                        
                        \Illuminate\Support\Facades\Storage::disk('local')->delete($data['file']);
                        
                        $message = "Import completed successfully! Created: {$import->createdCount}, Updated: {$import->updatedCount}, Skipped: {$import->skippedCount}.";
                        
                        if (!empty($import->errors)) {
                            $errorDetails = implode('<br>', array_slice($import->errors, 0, 5));
                            if (count($import->errors) > 5) {
                                $errorDetails .= '<br>... and more errors.';
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Import Completed with Warnings')
                                ->body($message . '<br><br><strong>Errors:</strong><br>' . $errorDetails)
                                ->warning()
                                ->persistent()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Import Successful')
                                ->body($message)
                                ->success()
                                ->send();
                        }
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
