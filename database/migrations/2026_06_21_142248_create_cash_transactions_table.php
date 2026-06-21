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
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            
            $table->string('transaction_number')->unique();
            $table->enum('type', ['receipt', 'disbursement']);
            $table->date('date');
            $table->foreignId('account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            
            $table->enum('status', ['draft', 'submitted', 'approved', 'posted', 'void'])->default('draft');
            $table->string('attachment_path')->nullable();
            
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
