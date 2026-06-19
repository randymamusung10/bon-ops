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
        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_receipt_id');
            $table->unsignedBigInteger('purchase_order_item_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unit_id');
            
            $table->decimal('ordered_qty', 15, 2)->default(0);
            $table->decimal('received_qty', 15, 2)->default(0);
            
            $table->text('notes')->nullable();

            $table->timestamps();
            
            // Constraints
            $table->foreign('goods_receipt_id')->references('id')->on('goods_receipts')->onDelete('cascade');
            
            $table->index('product_id');
            $table->index('purchase_order_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_items');
    }
};
