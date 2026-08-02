<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('venue_name')->nullable();
            $table->text('venue_address')->nullable();
            $table->string('venue_city')->nullable();
            $table->string('venue_country')->nullable();
            $table->decimal('venue_latitude', 10, 8)->nullable();
            $table->decimal('venue_longitude', 11, 8)->nullable();
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->datetime('registration_opens_at')->nullable();
            $table->datetime('registration_deadline')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('status')->default('draft')->comment('draft, published, cancelled, completed, archived');
            $table->json('settings')->nullable();
            $table->boolean('is_multi_day')->default(false);
            $table->boolean('is_free')->default(true);
            $table->datetime('published_at')->nullable();
            $table->datetime('cancelled_at')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('organization_id');
            $table->index('status');
            $table->index('starts_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
