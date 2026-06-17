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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->foreignId('tenant_id')->nullable()->after('uuid')->constrained('tenants')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->after('tenant_id')->constrained('companies')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained('branches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['users_branch_id_foreign']);
            $table->dropForeign(['users_company_id_foreign']);
            $table->dropForeign(['users_tenant_id_foreign']);
            
            $table->dropColumn(['uuid', 'tenant_id', 'company_id', 'branch_id']);
        });
    }
};
