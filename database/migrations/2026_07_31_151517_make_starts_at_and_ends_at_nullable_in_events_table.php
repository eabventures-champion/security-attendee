<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->datetime('starts_at')->nullable()->change();
            $table->datetime('ends_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->datetime('starts_at')->nullable(false)->change();
            $table->datetime('ends_at')->nullable(false)->change();
        });
    }
};
