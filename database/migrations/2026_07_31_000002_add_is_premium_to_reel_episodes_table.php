<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reel_episodes', function (Blueprint $table): void {
            $table->boolean('is_premium')->default(false)->after('coin_price');
        });
    }

    public function down(): void
    {
        Schema::table('reel_episodes', function (Blueprint $table): void {
            $table->dropColumn('is_premium');
        });
    }
};
