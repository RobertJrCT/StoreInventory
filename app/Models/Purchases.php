<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products;
use App\Models\Inventory;

class Purchases extends Model
{
    use HasFactory;

    protected $primaryKey = "purchaseId";

    protected $fillable = ['purchaseTotal'];

    protected function casts(): array
    {
        return [
            'purchaseTotal' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function purchaseDetails() {
        return $this->hasMany(PurchaseDetails::class, 'purchaseId');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($purchase) {
            // Load the purchase details with their products
            $purchase->load('purchaseDetails.product');
            
            foreach ($purchase->purchaseDetails as $detail) {
                // Update product price with recommended sale price
                $product = $detail->product;
                if ($product) {
                    $product->productPrice = $detail->recommendedSalePrice;
                    $product->save();
                }
                
                // Create or update inventory stock
                $inventory = Inventory::where('productId', $detail->productId)
                    ->where('countType', $detail->countType)
                    ->first();
                
                if ($inventory) {
                    // Update existing inventory
                    $inventory->currentStock += $detail->quantity;
                    $inventory->save();
                } else {
                    // Create new inventory record
                    Inventory::create([
                        'productId' => $detail->productId,
                        'countType' => $detail->countType,
                        'currentStock' => $detail->quantity,
                    ]);
                }
            }
        });
    }
}
