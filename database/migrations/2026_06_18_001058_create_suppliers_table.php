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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('code');
            $table->string('name');
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('tax_number')->nullable();
            $table->foreignId('account_payable_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('default_currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete();
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
        Schema::dropIfExists('suppliers');
    }
};
