<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('reference_type'); // e.g., 'stock_adjustment', 'purchase_receipt', 'sales'
            $table->unsignedBigInteger('reference_id'); // ID of the reference document
            $table->date('date');
            $table->decimal('qty_in', 15, 4)->default(0);
            $table->decimal('qty_out', 15, 4)->default(0);
            $table->decimal('balance_after', 15, 4)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'warehouse_id', 'product_id']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
