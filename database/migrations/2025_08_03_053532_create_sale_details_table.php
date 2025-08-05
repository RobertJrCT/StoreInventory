<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id('idSDetail');
            $table->unsignedBigInteger('saleId');
            $table->unsignedBigInteger('productId');
            $table->integer('quantity');
            $table->decimal('unitSalePrice', 10, 2);
            $table->decimal('subtotalSDetail',10,2)->nullable();
            $table->foreign('saleId')->references('saleId')->on('sales')->onDelete('cascade');
            $table->foreign('productId')->references('productId')->on('products')->onDelete('cascade');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
