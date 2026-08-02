<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('attendee_id')->constrained('attendees')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('secure_token', 128)->unique();
            $table->text('encrypted_payload');
            $table->string('digital_signature', 128);
            $table->string('qr_image_path')->nullable();
            $table->datetime('expires_at');
            $table->boolean('is_revoked')->default(false);
            $table->datetime('issued_at');
            $table->datetime('revoked_at')->nullable();
            $table->text('revoked_reason')->nullable();
            $table->integer('reissue_count')->default(0);
            $table->datetime('last_scanned_at')->nullable();
            $table->timestamps();
            
            $table->index('secure_token');
            $table->index('attendee_id');
            $table->index('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
