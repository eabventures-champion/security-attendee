<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_invitations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_invitations', 'no_details')) {
                $table->boolean('no_details')->default(false)->after('access_role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_invitations', function (Blueprint $table) {
            if (Schema::hasColumn('event_invitations', 'no_details')) {
                $table->dropColumn('no_details');
            }
        });
    }
};
