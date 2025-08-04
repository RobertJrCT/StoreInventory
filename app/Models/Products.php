<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    /** @use HasFactory<\Database\Factories\ProductsFactory> */
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'productId';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'productName',
        'productDescription',
        'productPrice',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'productPrice' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the stock record associated with the product.
     */
    public function stock()
    {
        return $this->hasMany(Stock::class, 'productId');
    }

    /**
     * Get the sale details for the product.
     */
    public function saleDetails()
    {
        return $this->hasMany(SaleDetails::class, 'productId');
    }

    /**
     * Get the purchase details for the product.
     */
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetails::class, 'productId');
    }
}
