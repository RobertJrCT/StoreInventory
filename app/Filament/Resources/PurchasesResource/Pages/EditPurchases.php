<?php

namespace App\Filament\Resources\PurchasesResource\Pages;

use App\Filament\Resources\PurchasesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Products;
use App\Models\Inventory;

class EditPurchases extends EditRecord
{
    protected static string $resource = PurchasesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $purchase = $this->record;
        
        foreach ($purchase->purchaseDetails as $detail) {
            $product = $detail->product;
            if ($product && $detail->recommendedSalePrice>0) {
                $product->priceByFormat = $detail->recommendedSalePrice;
                $product->save();
            }
            
            $inventory = Inventory::where('productId', $detail->productId)->first();
            
            if ($inventory) {
                $inventory->currentStock += $detail->quantity;
                $inventory->save();
            } else {
                Inventory::create([
                    'productId' => $detail->productId,
                    'currentStock' => $detail->quantity,
                ]);
            }
        }
    }
}
