<?php

namespace App\Services;

use App\Models\Reels\DramaSeries;
use App\Models\Reels\Episode;
use Illuminate\Support\Facades\DB;

class ReelSeriesPricingService
{
    public function syncSeriesEpisodes(DramaSeries $series): void
    {
        DB::transaction(function () use ($series): void {
            $series = DramaSeries::query()->lockForUpdate()->findOrFail($series->id);
            $episodes = Episode::query()
                ->where('series_id', $series->id)
                ->orderBy('number')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $freeEpisodes = max(0, (int) $series->number_of_free_episodes);
            $premiumEnabled = (bool) $series->is_premium && (int) $series->coin_price > 0;

            foreach ($episodes as $index => $episode) {
                $isPremium = $premiumEnabled && $index >= $freeEpisodes;
                $targetCoinPrice = $isPremium ? (int) $series->coin_price : 0;

                $episode->fill([
                    'is_premium' => $isPremium,
                    'coin_price' => $targetCoinPrice,
                ]);

                if ($episode->isDirty(['is_premium', 'coin_price'])) {
                    $episode->save();
                }
            }

            $series->fill([
                'total_episodes' => $episodes->count(),
            ]);

            if ($series->isDirty(['total_episodes'])) {
                $series->save();
            }
        });
    }
}
