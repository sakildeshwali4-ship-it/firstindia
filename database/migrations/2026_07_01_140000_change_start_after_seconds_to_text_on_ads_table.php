<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeStartAfterSecondsToTextOnAdsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ads') || !Schema::hasColumn('ads', 'start_after_seconds')) {
            return;
        }

        Schema::table('ads', function (Blueprint $table) {
            $table->text('start_after_seconds')->nullable()->change();
        });

        DB::table('ads')
            ->select('id', 'start_after_seconds')
            ->orderBy('id')
            ->chunkById(100, function ($ads) {
                foreach ($ads as $ad) {
                    DB::table('ads')
                        ->where('id', $ad->id)
                        ->update([
                            'start_after_seconds' => json_encode([(int) $ad->start_after_seconds]),
                        ]);
                }
            });
    }

    public function down()
    {
        if (!Schema::hasTable('ads') || !Schema::hasColumn('ads', 'start_after_seconds')) {
            return;
        }

        DB::table('ads')
            ->select('id', 'start_after_seconds')
            ->orderBy('id')
            ->chunkById(100, function ($ads) {
                foreach ($ads as $ad) {
                    $values = json_decode($ad->start_after_seconds, true);
                    $firstValue = is_array($values) && !empty($values) ? (int) $values[0] : (int) $ad->start_after_seconds;

                    DB::table('ads')
                        ->where('id', $ad->id)
                        ->update([
                            'start_after_seconds' => $firstValue,
                        ]);
                }
            });

        Schema::table('ads', function (Blueprint $table) {
            $table->integer('start_after_seconds')->default(30)->change();
        });
    }
}
