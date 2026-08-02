<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_devices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('device_name');
            $table->string('device_identifier')->unique();
            $table->string('device_token', 128)->unique();
            $table->boolean('is_authorized')->default(false);
            $table->foreignId('authorized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('authorized_at')->nullable();
            $table->datetime('last_active_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_devices');
    }
};
