<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('wallet_transactions')) {
            return;
        }

        Schema::table('wallet_transactions', function (Blueprint $table): void {
            if (! Schema::hasColumn('wallet_transactions', 'status')) {
                $table->string('status', 20)->default('success')->after('source')->index();
            }

            if (! Schema::hasColumn('wallet_transactions', 'gateway_order_id')) {
                $table->string('gateway_order_id')->nullable()->after('payment_id')->index();
            }

            if (! Schema::hasColumn('wallet_transactions', 'gateway_payment_id')) {
                $table->string('gateway_payment_id')->nullable()->after('gateway_order_id')->index();
            }

            if (! Schema::hasColumn('wallet_transactions', 'gateway_signature')) {
                $table->string('gateway_signature')->nullable()->after('gateway_payment_id');
            }

            if (! Schema::hasColumn('wallet_transactions', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('gateway_signature');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('wallet_transactions')) {
            return;
        }

        Schema::table('wallet_transactions', function (Blueprint $table): void {
            $dropColumns = [];

            foreach (['status', 'gateway_order_id', 'gateway_payment_id', 'gateway_signature', 'verified_at'] as $column) {
                if (Schema::hasColumn('wallet_transactions', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
