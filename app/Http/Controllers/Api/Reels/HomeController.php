<?php

namespace App\Http\Controllers\Api\Reels;

use App\Http\Controllers\Controller;
use App\Models\Reels\DramaSeries;
use App\Models\Reels\Episode;

class HomeController extends Controller
{
    public function __invoke(): array
    {
        return [
            'hero' => DramaSeries::query()->where('status', 'published')->latest()->take(3)->get(),
            'trending' => DramaSeries::query()->where('status', 'published')->orderByDesc('rating')->take(10)->get(),
            'new_releases' => DramaSeries::query()->where('status', 'published')->latest()->take(10)->get(),
            'continue_watching' => Episode::query()->latest('published_at')->take(5)->get(),
        ];
    }
}
