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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('total_amount', 24, 2)->default(0)->change();
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->change();
            $table->decimal('unit_price', 24, 2)->change();
            $table->decimal('total_price', 24, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->default(0)->change();
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
            $table->decimal('unit_price', 15, 2)->change();
            $table->decimal('total_price', 15, 2)->change();
        });
    }
};
