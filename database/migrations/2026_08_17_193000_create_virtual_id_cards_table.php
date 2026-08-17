<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('virtual_id_cards', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('member_id_number')->index();
            $table->string('full_name');
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('photo_path')->nullable();
            $table->string('institution')->nullable();
            $table->string('law_faculty')->nullable();
            $table->string('admission_year')->nullable();
            $table->string('completion_year')->nullable();
            $table->json('custom_fields')->nullable();
            $table->string('qr_token', 64)->unique();
            $table->string('status', 20)->default('active'); // active, suspended, expired
            $table->json('card_template')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_id_cards');
    }
};
