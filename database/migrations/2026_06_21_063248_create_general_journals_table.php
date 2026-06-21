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
        Schema::create('general_journals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('restrict');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->date('date');
            $table->string('journal_number')->unique();
            $table->string('reference_type')->nullable()->comment('Contoh: PurchaseOrder, SalesOrder');
            $table->string('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_debit', 20, 2)->default(0);
            $table->decimal('total_credit', 20, 2)->default(0);
            $table->enum('status', ['draft', 'submitted', 'approved', 'posted', 'closed', 'void'])->default('draft');
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_journals');
    }
};
