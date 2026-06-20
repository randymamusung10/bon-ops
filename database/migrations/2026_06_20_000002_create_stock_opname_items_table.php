<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('system_qty', 15, 4)->default(0);
            $table->decimal('actual_qty', 15, 4)->default(0);
            $table->decimal('difference', 15, 4)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['stock_opname_id', 'product_id'], 'stock_opname_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
