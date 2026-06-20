<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_waste_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_waste_id')->constrained('stock_wastes')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('qty', 15, 4)->default(0);
            $table->string('reason')->nullable(); // Expired, Spoiled, Broken, etc.
            $table->decimal('cost', 15, 4)->default(0); // Cost per unit at the time of waste
            $table->timestamps();
            
            $table->unique(['stock_waste_id', 'product_id'], 'stock_waste_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_waste_items');
    }
};
