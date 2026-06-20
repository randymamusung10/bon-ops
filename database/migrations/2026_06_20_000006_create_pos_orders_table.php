<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('pos_shift_id')->constrained('pos_shifts')->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('order_number');
            $table->date('date');
            $table->decimal('total_amount', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('grand_total', 15, 4)->default(0);
            $table->string('payment_method')->nullable(); // cash, card, qris, etc.
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded
            $table->string('status')->default('pending'); // pending, processing, completed, cancelled
            $table->string('customer_name')->nullable();
            $table->string('table_number')->nullable(); // table identifier
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['tenant_id', 'order_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_orders');
    }
};
