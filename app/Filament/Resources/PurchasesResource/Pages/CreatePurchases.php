<?php

namespace App\Filament\Resources\PurchasesResource\Pages;

use App\Filament\Resources\PurchasesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Products;
use App\Models\Inventory;

class CreatePurchases extends CreateRecord
{
    protected static string $resource = PurchasesResource::class;

    protected function afterCreate(): void
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
