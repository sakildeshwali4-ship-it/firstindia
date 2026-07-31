<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_episode_likes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('episode_id');
            $table->timestamps();

            $table->unique(['user_id', 'episode_id']);
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('episode_id')->references('id')->on('reel_episodes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_episode_likes');
    }
};
