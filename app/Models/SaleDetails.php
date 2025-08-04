<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetails extends Model
{
    /** @use HasFactory<\Database\Factories\SaleDetailsFactory> */
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'idSDetail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'saleId',
        'productId',
        'countType',
        'quantity',
        'unitSalePrice',
        'subtotalSDetail',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unitSalePrice' => 'decimal:2',
            'subtotalSDetail' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the sale that owns this sale detail.
     */
    public function sale()
    {
        return $this->belongsTo(Sales::class, 'saleId', 'saleId');
    }

    /**
     * Get the product associated with this sale detail.
     */
    public function product()
    {
        return $this->belongsTo(Products::class, 'productId', 'productId');
    }
}
