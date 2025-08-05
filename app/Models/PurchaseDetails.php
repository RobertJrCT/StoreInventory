<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetails extends Model
{
    use HasFactory;

    protected $primaryKey = 'idPDetail';

    protected $fillable = [
        'purchaseId',
        'productId',
        'quantity',
        'unitPurchasePrice',
        'subtotalPDetail',
        'recommendedSalePrice',
    ];

    protected function casts(): array
    {
        return [
            'unitPurchasePrice' => 'decimal:2',
            'subtotalPDetail' => 'decimal:2',
            'recommendedSalePrice' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function purchase()
    {
        return $this->belongsTo(Purchases::class, 'purchaseId', 'purchaseId');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'productId', 'productId');
    }
}
