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
            if ($product) {
                $product->productPrice = $detail->recommendedSalePrice;
                $product->save();
            }
            
            $inventory = Inventory::where('productId', $detail->productId)
                ->where('countType', $detail->countType)
                ->first();
            
            if ($inventory) {
                $inventory->currentStock += $detail->quantity;
                $inventory->save();
            } else {
                Inventory::create([
                    'productId' => $detail->productId,
                    'countType' => $detail->countType,
                    'currentStock' => $detail->quantity,
                ]);
            }
        }
    }
}
