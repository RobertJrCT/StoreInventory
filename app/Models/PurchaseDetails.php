<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetails extends Model
{
    /** @use HasFactory<\Database\Factories\PurchaseDetailsFactory> */
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'idPDetail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchaseId',
        'productId',
        'countType',
        'quantity',
        'unitsPerPackage',
        'unitPurchasePrice',
        'subtotalPDetail',
        'recommendedSalePrice',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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

    /**
     * Get the purchase that owns this purchase detail.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchases::class, 'purchaseId', 'purchaseId');
    }

    /**
     * Get the product associated with this purchase detail.
     */
    public function product()
    {
        return $this->belongsTo(Products::class, 'productId', 'productId');
    }
}
