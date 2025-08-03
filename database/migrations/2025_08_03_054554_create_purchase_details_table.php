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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id('idPDetail');
            $table->unsignedBigInteger('purchaseId');
            $table->unsignedBigInteger('productId');
            $table->enum('countType', ['unit','package'])->default('unit');
            $table->integer('quantity');
            $table->integer('unitsPerPackage')->nullable();
            $table->decimal('unitPurchasePrice', 10, 2);
            $table->decimal('subtotalPDetail',10,2)->nullable();
            $table->decimal('recommendedSalePrice',10,2);
            $table->foreign('purchaseId')->references('purchaseId')->on('purchases')->onDelete('cascade');
            $table->foreign('productId')->references('productId')->on('products')->onDelete('cascade');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchaseDetails');
    }
};
