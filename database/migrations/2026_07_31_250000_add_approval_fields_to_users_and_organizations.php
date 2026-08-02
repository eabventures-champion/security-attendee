<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'approval_status')) {
                $table->string('approval_status')->default('approved')->after('is_active');
            }
            if (!Schema::hasColumn('users', 'approval_token')) {
                $table->string('approval_token')->nullable()->unique()->after('approval_status');
            }
            if (!Schema::hasColumn('users', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approval_token');
            }
        });

        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'approval_status')) {
                $table->string('approval_status')->default('approved')->after('is_active');
            }
            if (!Schema::hasColumn('organizations', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approval_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'approval_status')) {
                $table->dropColumn('approval_status');
            }
            if (Schema::hasColumn('users', 'approval_token')) {
                $table->dropColumn('approval_token');
            }
            if (Schema::hasColumn('users', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
        });

        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'approval_status')) {
                $table->dropColumn('approval_status');
            }
            if (Schema::hasColumn('organizations', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
        });
    }
};
