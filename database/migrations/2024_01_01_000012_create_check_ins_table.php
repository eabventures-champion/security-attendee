<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('attendee_id')->constrained('attendees')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('gate_id')->nullable()->constrained('gates')->nullOnDelete();
            $table->foreignId('qr_code_id')->nullable()->constrained('qr_codes')->nullOnDelete();
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('device_id')->nullable();
            $table->string('scan_result')->comment('granted, denied_wrong_gate, denied_already_checked_in, denied_qr_expired, denied_not_verified, denied_revoked, denied_unauthorized, denied_invalid, denied_device_unauthorized');
            $table->datetime('scanned_at');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('attendee_id');
            $table->index('event_id');
            $table->index('gate_id');
            $table->index('scan_result');
            $table->index('scanned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
