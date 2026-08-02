<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'invitation_token')) {
                $table->string('invitation_token')->nullable()->unique()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'invitation_status')) {
                $table->string('invitation_status')->default('pending')->after('invitation_token');
            }
            if (!Schema::hasColumn('users', 'invitation_accepted_at')) {
                $table->timestamp('invitation_accepted_at')->nullable()->after('invitation_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'invitation_token')) {
                $table->dropColumn('invitation_token');
            }
            if (Schema::hasColumn('users', 'invitation_status')) {
                $table->dropColumn('invitation_status');
            }
            if (Schema::hasColumn('users', 'invitation_accepted_at')) {
                $table->dropColumn('invitation_accepted_at');
            }
        });
    }
};
