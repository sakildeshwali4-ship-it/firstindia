<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('coin_packages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('coins');
            $table->unsignedInteger('bonus_coins')->default(0);
            $table->unsignedInteger('price_rupees');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('drama_series', function (Blueprint $table): void {
            $table->unsignedInteger('coin_price')->default(0)->after('is_premium');
        });

        Schema::table('reel_episodes', function (Blueprint $table): void {
            $table->unsignedInteger('coin_price')->default(0)->after('is_locked');
        });
    }

    public function down(): void
    {
        Schema::table('reel_episodes', function (Blueprint $table): void {
            $table->dropColumn('coin_price');
        });

        Schema::table('drama_series', function (Blueprint $table): void {
            $table->dropColumn('coin_price');
        });

        Schema::dropIfExists('coin_packages');
        Schema::dropIfExists('wallet_settings');
    }
};
