<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->text('encrypted_payload')->nullable()->change();
            $table->string('digital_signature', 128)->nullable()->change();
            $table->datetime('expires_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->text('encrypted_payload')->nullable(false)->change();
            $table->string('digital_signature', 128)->nullable(false)->change();
            $table->datetime('expires_at')->nullable(false)->change();
        });
    }
};
