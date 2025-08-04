<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
