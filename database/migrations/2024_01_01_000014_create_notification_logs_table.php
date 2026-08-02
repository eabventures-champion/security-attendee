<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('attendee_id')->nullable()->constrained('attendees')->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('events')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel')->comment('email, sms, whatsapp, in_app');
            $table->string('type')->comment('registration_confirmation, verification_request, verification_success, qr_delivery, event_reminder, checkin_confirmation, thank_you');
            $table->string('status')->comment('pending, sent, failed, delivered');
            $table->datetime('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
