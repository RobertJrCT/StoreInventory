<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $primaryKey = 'productId';

    protected $fillable = [
        'productName',
        'productDescription',
        'presentationType',
        'priceByFormat',
    ];

    protected function casts(): array
    {
        return [
            'priceByFormat' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'productId');
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetails::class, 'productId');
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetails::class, 'productId');
    }
}
