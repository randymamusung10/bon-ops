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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('inventory_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('cogs_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('income_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('code');
            $table->string('name');
            $table->string('type')->default('finished_good');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('cost', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['tenant_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
