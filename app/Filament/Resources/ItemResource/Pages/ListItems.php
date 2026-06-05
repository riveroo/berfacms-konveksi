<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemTemplateExport;
use App\Imports\ItemsImport;
use Illuminate\Support\Facades\Storage;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_template')
                ->label(fn () => __('item.download_template'))
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn () => canAccessMenu('admin/import-export'))
                ->action(function () {
                    return Excel::download(new ItemTemplateExport, 'item_template.xlsx');
                }),
                
            Actions\Action::make('import_excel')
                ->label(fn () => __('item.import_excel'))
                ->icon('heroicon-o-document-arrow-up')
                ->visible(fn () => canAccessMenu('admin/import-export'))
                ->form([
                    FileUpload::make('file')
                        ->label(fn () => __('item.excel_file'))
                        ->disk('local')
                        ->directory('imports')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    try {
                        $filePath = Storage::disk('local')->path($data['file']);
                        Excel::import(new ItemsImport, $filePath);
                        Notification::make()
                            ->title(__('item.import_successful'))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title(__('item.import_failed'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }
}
