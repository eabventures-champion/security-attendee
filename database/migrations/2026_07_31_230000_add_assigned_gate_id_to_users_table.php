<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'assigned_gate_id')) {
                $table->foreignId('assigned_gate_id')->nullable()->after('organization_id')->constrained('gates')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'assigned_gate_id')) {
                $table->dropForeign(['assigned_gate_id']);
                $table->dropColumn('assigned_gate_id');
            }
        });
    }
};
