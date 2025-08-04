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
        Schema::create('inventory', function (Blueprint $table) {
            $table->unsignedBigInteger('productId');
            $table->enum('countType', ['unit','package'])->default('unit');
            $table->integer('currentStock');
            $table->foreign('productId')->references('productId')->on('products')->onDelete('cascade');
            $table->timestampsTz();
            $table->primary(['productId', 'countType']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
