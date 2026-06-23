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
        Schema::create('crm_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['percentage', 'nominal']);
            $table->decimal('value', 15, 2);
            $table->decimal('minimum_spend', 15, 2)->default(0);
            $table->decimal('maximum_discount', 15, 2)->nullable();
            $table->integer('quota')->nullable();
            $table->integer('used_count')->default(0);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_vouchers');
    }
};
