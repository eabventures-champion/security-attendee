<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waiting_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->integer('position');
            $table->datetime('notified_at')->nullable();
            $table->string('status')->default('waiting')->comment('waiting, notified, registered, expired');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waiting_lists');
    }
};
