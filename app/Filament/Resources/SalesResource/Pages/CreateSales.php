<?php

namespace App\Filament\Resources\SalesResource\Pages;

use App\Filament\Resources\SalesResource;
use App\Models\Inventory;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSales extends CreateRecord
{
    protected static string $resource = SalesResource::class;

    protected function afterCreate(): void
    {
        $sale = $this->record;
        
        foreach ($sale->saleDetails as $detail) {
            $inventory = Inventory::where('productId', $detail->productId)->first();
            
            if ($inventory && $inventory->currentStock >= $detail->quantity) {
                $inventory->currentStock -= $detail->quantity;
                $inventory->save();
            }
        }
    }
}
