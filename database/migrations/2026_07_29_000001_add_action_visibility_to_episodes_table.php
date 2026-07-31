<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reel_episodes', function (Blueprint $table): void {
            $table->boolean('show_like_button')->default(true)->after('is_locked');
            $table->boolean('show_watchlist_button')->default(true)->after('show_like_button');
            $table->boolean('show_share_button')->default(true)->after('show_watchlist_button');
            $table->boolean('show_episodes_button')->default(true)->after('show_share_button');
        });
    }

    public function down(): void
    {
        Schema::table('reel_episodes', function (Blueprint $table): void {
            $table->dropColumn([
                'show_like_button',
                'show_watchlist_button',
                'show_share_button',
                'show_episodes_button',
            ]);
        });
    }
};
