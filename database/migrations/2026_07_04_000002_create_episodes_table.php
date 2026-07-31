<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reel_episodes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('series_id')->constrained('drama_series')->cascadeOnDelete();
            $table->unsignedInteger('number');
            $table->string('title');
            $table->text('synopsis');
            $table->string('thumbnail_url');
            $table->string('video_url');
            $table->unsignedSmallInteger('duration_seconds')->default(60);
            $table->boolean('is_locked')->default(false);
            $table->unsignedInteger('likes')->default(0);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->unique(['series_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reel_episodes');
    }
};
