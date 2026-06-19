<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('supplier_invoice_id');
            $table->string('document_number')->unique();
            $table->date('payment_date');
            $table->string('payment_method')->default('transfer'); // transfer, cash, giro, cheque
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_reference')->nullable(); // No. referensi / No. cek / No. giro
            $table->decimal('payment_amount', 18, 2)->default(0);
            $table->decimal('invoice_amount', 18, 2)->default(0); // amount of the invoice
            $table->string('status')->default('draft'); // draft, submitted, approved, posted
            $table->text('notes')->nullable();
            // Workflow
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
