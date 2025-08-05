<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetails extends Model
{
    use HasFactory;

    protected $primaryKey = 'idSDetail';

    protected $fillable = [
        'saleId',
        'productId',
        'quantity',
        'unitSalePrice',
        'subtotalSDetail',
    ];

    protected function casts(): array
    {
        return [
            'unitSalePrice' => 'decimal:2',
            'subtotalSDetail' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function sale()
    {
        return $this->belongsTo(Sales::class, 'saleId', 'saleId');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'productId', 'productId');
    }
}
