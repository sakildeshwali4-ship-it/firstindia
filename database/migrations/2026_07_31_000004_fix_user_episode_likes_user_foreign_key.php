<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_episode_likes')) {
            return;
        }

        Schema::table('user_episode_likes', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
        });

        Schema::table('user_episode_likes', function (Blueprint $table): void {
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('user_episode_likes')) {
            return;
        }

        Schema::table('user_episode_likes', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
        });

        Schema::table('user_episode_likes', function (Blueprint $table): void {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
