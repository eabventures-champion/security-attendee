<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('ticket_category_id')->nullable()->constrained('ticket_categories')->nullOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->string('country')->nullable();
            $table->string('gender')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->text('dietary_preferences')->nullable();
            $table->text('accessibility_needs')->nullable();
            $table->boolean('consent')->default(false);
            $table->string('access_role')->default('general_admission');
            $table->string('verification_status')->default('pending')->comment('pending, verified, rejected');
            $table->string('verification_token')->unique()->nullable();
            $table->string('verification_code')->nullable()->comment('for OTP');
            $table->datetime('verified_at')->nullable();
            $table->string('registration_ip')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['event_id', 'email']);
            $table->index('organization_id');
            $table->index('verification_status');
            $table->index('access_role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
};
