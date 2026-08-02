<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendees', function (Blueprint $table) {
            if (!Schema::hasColumn('attendees', 'assigned_gate_id')) {
                $table->foreignId('assigned_gate_id')->nullable()->after('event_id')->constrained('gates')->nullOnDelete();
            }
            if (!Schema::hasColumn('attendees', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('organization_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendees', function (Blueprint $table) {
            if (Schema::hasColumn('attendees', 'assigned_gate_id')) {
                $table->dropForeign(['assigned_gate_id']);
                $table->dropColumn('assigned_gate_id');
            }
            if (Schema::hasColumn('attendees', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
