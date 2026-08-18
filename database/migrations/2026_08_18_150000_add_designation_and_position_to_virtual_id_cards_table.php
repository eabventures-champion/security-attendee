<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('virtual_id_cards', function (Blueprint $table) {
            $table->string('designation', 50)->default('member')->after('completion_year');
            $table->string('position')->nullable()->after('designation');
        });
    }

    public function down(): void
    {
        Schema::table('virtual_id_cards', function (Blueprint $table) {
            $table->dropColumn(['designation', 'position']);
        });
    }
};
