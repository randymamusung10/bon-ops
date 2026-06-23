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
        Schema::create('finance_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            
            $table->foreignId('cash_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('sales_revenue_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('tax_payable_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('cogs_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('inventory_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            
            $table->timestamps();

            $table->unique(['tenant_id', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_configs');
    }
};
