<?php

namespace App\Http\Controllers\Api\Reels;

use App\Http\Controllers\Controller;
use App\Models\Reels\DramaSeries;
use Illuminate\Http\Request; 
use Validator;

class SeriesController extends Controller
{
    public function index(Request $request): array
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
        ]);

        if ($validation->fails()) {
            $data['status'] = 400;
            $data['message'] = __('api_msg.please_enter_required_fields');
            return $data;
        }

        return [
            'data' => DramaSeries::query()
                ->where('status', 'published')
                ->latest()
                ->paginate(20),
        ];
    }

    public function show(DramaSeries $series): DramaSeries
    {
        return $series->load('episodes');
    }

    public function episodes(DramaSeries $series): array
    {
        return [
            'data' => $series->episodes()->get(),
        ];
    }

    public function pricing(DramaSeries $series): array
    {
        return [
            'series' => [
                'id' => $series->id,
                'title' => $series->title,
                'is_premium' => $series->is_premium,
                'coin_price' => $series->coin_price,
            ],
            'episodes' => $series->episodes()
                ->where('is_premium', true)
                ->get(['id', 'series_id', 'number', 'title', 'is_locked', 'coin_price'])
                ->map(fn ($episode): array => [
                    'id' => $episode->id,
                    'series_id' => $episode->series_id,
                    'number' => $episode->number,
                    'title' => $episode->title,
                    'is_locked' => $episode->is_locked,
                    'coin_price' => $episode->coin_price,
                ])
                ->values()
                ->all(),
        ];
    }
}
