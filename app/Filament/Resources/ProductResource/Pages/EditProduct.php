<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn ($record) => url('/products/' . $record->id))
                ->openUrlInNewTab(),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action, \App\Models\Product $record) {
                    $usages = [];
                    if (\DB::table('transaction_details')->where('product_id', $record->id)->exists()) {
                        $usages[] = 'Detail Transaksi (Sales / Orders)';
                    }
                    if (\DB::table('pre_order_details')->where('product_id', $record->id)->exists()) {
                        $usages[] = 'Detail Pre Order';
                    }
                    if (\DB::table('stock_ins')->where('product_id', $record->id)->exists()) {
                        $usages[] = 'Riwayat Stock In';
                    }
                    if (\DB::table('production_products')->where('product_id', $record->id)->exists()) {
                        $usages[] = 'Riwayat Produksi';
                    }

                    $variantIds = $record->variants()->pluck('id')->toArray();
                    if (!empty($variantIds)) {
                        if (\DB::table('stock_outs')->whereIn('variant_id', $variantIds)->exists()) {
                            $usages[] = 'Riwayat Stock Out';
                        }
                        if (\DB::table('stock_adjustments')->whereIn('variant_id', $variantIds)->exists()) {
                            $usages[] = 'Riwayat Stock Adjustment';
                        }
                    }

                    if (!empty($usages)) {
                        $uniqueUsages = array_unique($usages);
                        $msg = "Produk \"{$record->product_name}\" tidak dapat dihapus karena telah digunakan dalam riwayat transaksi atau modul lainnya (" . implode(', ', $uniqueUsages) . ").";
                        
                        $action->getLivewire()->js("alert('" . addslashes($msg) . "')");
                        $action->halt();
                    }
                }),
        ];
    }
}
