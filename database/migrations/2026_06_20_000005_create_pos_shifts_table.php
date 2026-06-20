<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->decimal('start_cash', 15, 4)->default(0);
            $table->decimal('end_cash', 15, 4)->nullable(); // expected end cash
            $table->decimal('actual_end_cash', 15, 4)->nullable(); // physical end cash entered by user
            $table->text('notes')->nullable();
            $table->string('status')->default('open'); // open, closed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_shifts');
    }
};
