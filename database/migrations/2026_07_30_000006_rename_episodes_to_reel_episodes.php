<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('episodes') && ! Schema::hasTable('reel_episodes')) {
            Schema::rename('episodes', 'reel_episodes');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('reel_episodes') && ! Schema::hasTable('episodes')) {
            Schema::rename('reel_episodes', 'episodes');
        }
    }
};
