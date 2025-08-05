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
        Schema::create('products', function (Blueprint $table) {
            $table->id('productId');
            $table->string('productName');
            $table->string('productDescription');
            $table->string('presentationType');
            $table->decimal('priceByFormat', 10,2);
            // $table->enum('countType', ['units','packages'])->default('units');
            // $table->integer('unitsPerPackage')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
