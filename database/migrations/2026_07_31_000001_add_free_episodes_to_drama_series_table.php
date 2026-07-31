<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drama_series', function (Blueprint $table): void {
            $table->unsignedInteger('number_of_free_episodes')->default(0)->after('coin_price');
        });

        $seriesIds = DB::table('drama_series')->pluck('id');

        foreach ($seriesIds as $seriesId) {
            $freeEpisodes = DB::table('reel_episodes')
                ->where('series_id', $seriesId)
                ->where('coin_price', 0)
                ->count();

            DB::table('drama_series')
                ->where('id', $seriesId)
                ->update([
                    'number_of_free_episodes' => $freeEpisodes,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('drama_series', function (Blueprint $table): void {
            $table->dropColumn('number_of_free_episodes');
        });
    }
};
