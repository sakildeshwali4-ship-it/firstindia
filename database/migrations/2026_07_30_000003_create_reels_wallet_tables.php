<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user') && ! Schema::hasColumn('user', 'wallet')) {
            Schema::table('user', function (Blueprint $table): void {
                $table->unsignedInteger('wallet')->default(0)->after('status');
            });
        }

        if (! Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('coin_package_id')->nullable()->index();
                $table->unsignedBigInteger('episode_id')->nullable()->index();
                $table->unsignedBigInteger('series_id')->nullable()->index();
                $table->string('payment_id')->nullable()->index();
                $table->string('transaction_type', 20);
                $table->string('source', 50);
                $table->unsignedInteger('coins');
                $table->unsignedInteger('balance_before');
                $table->unsignedInteger('balance_after');
                $table->unsignedInteger('amount_rupees')->nullable();
                $table->string('description')->nullable();
                $table->longText('meta')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('user_episode_purchases')) {
            Schema::create('user_episode_purchases', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('episode_id')->index();
                $table->unsignedInteger('coins_spent')->default(0);
                $table->timestamp('purchased_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'episode_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_episode_purchases');
        Schema::dropIfExists('wallet_transactions');

        if (Schema::hasTable('user') && Schema::hasColumn('user', 'wallet')) {
            Schema::table('user', function (Blueprint $table): void {
                $table->dropColumn('wallet');
            });
        }
    }
};
