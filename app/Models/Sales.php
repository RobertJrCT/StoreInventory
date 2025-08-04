<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $primaryKey = "saleId";

    protected $fillable = ['saleTotal'];

    protected function casts(): array
    {
        return [
            'saleTotal' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function saleDetails() {
        return $this->hasMany(SaleDetails::class, 'saleId');
    }
}
