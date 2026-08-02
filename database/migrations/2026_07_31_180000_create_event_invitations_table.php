<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_invitations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('email')->nullable();
            $table->string('token', 64)->unique();
            $table->string('access_role', 50)->default('general_admission');
            $table->integer('max_uses')->default(1);
            $table->integer('use_count')->default(0);
            $table->boolean('is_revoked')->default(false);
            $table->datetime('expires_at')->nullable();
            $table->datetime('used_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['event_id', 'token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_invitations');
    }
};
