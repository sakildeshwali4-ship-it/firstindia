<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_progress', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_id')->nullable()->index();
            $table->foreignId('episode_id')->constrained('reel_episodes')->cascadeOnDelete();
            $table->unsignedSmallInteger('progress_seconds')->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamps();
            $table->unique(['guest_id', 'episode_id']);
        });

        Schema::create('watchlists', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('series_id')->constrained('drama_series')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'series_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlists');
        Schema::dropIfExists('watch_progress');
    }
};
