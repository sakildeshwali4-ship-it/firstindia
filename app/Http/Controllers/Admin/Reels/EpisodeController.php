<?php

namespace App\Http\Controllers\Admin\Reels;

use App\Http\Controllers\Controller;
use App\Models\Reels\DramaSeries;
use App\Models\Reels\Episode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EpisodeController extends Controller
{
    public function create(DramaSeries $series): View
    {
        return view('admin.reels.episodes.create', [
            'series' => $series,
            'episode' => new Episode([
                'series_id' => $series->id,
                'number' => ($series->episodes()->max('number') ?? 0) + 1,
                'duration_seconds' => 60,
                'published_at' => now(),
                'coin_price' => 0,
                'show_like_button' => true,
                'show_watchlist_button' => true,
                'show_share_button' => true,
                'show_episodes_button' => true,
            ]),
        ]);
    }

    public function store(Request $request, DramaSeries $series): RedirectResponse
    {
        $data = $this->validatedData($request, $series);
        $data['series_id'] = $series->id;
        $data['is_locked'] = $request->boolean('is_locked');
        $data['coin_price'] = $data['is_locked'] ? ($data['coin_price'] ?? 0) : 0;
        $data = $this->applyVisibilityFlags($request, $data);
        $data['thumbnail_url'] = $this->mediaPath($request, 'thumbnail_file', $data['thumbnail_url'] ?? $series->poster_url, 'posters');
        $data['video_url'] = $this->mediaPath($request, 'video_file', $data['video_url'] ?? null, 'videos');

        $series->episodes()->create($data);
        $series->update(['total_episodes' => $series->episodes()->count()]);

        return redirect()
            ->route('series.show', $series)
            ->with('status', 'Vertical episode added.');
    }

    public function edit(DramaSeries $series, Episode $episode): View
    {
        abort_unless($episode->series_id === $series->id, 404);

        return view('admin.reels.episodes.edit', [
            'series' => $series,
            'episode' => $episode,
        ]);
    }

    public function update(Request $request, DramaSeries $series, Episode $episode): RedirectResponse
    {
        abort_unless($episode->series_id === $series->id, 404);

        $data = $this->validatedData($request, $series, $episode);
        $data['is_locked'] = $request->boolean('is_locked');
        $data['coin_price'] = $data['is_locked'] ? ($data['coin_price'] ?? 0) : 0;
        $data = $this->applyVisibilityFlags($request, $data);
        $data['thumbnail_url'] = $this->mediaPath($request, 'thumbnail_file', $data['thumbnail_url'] ?? $episode->thumbnail_url, 'posters');
        $data['video_url'] = $this->mediaPath($request, 'video_file', $data['video_url'] ?? $episode->video_url, 'videos');

        $episode->update($data);

        return redirect()
            ->route('series.show', $series)
            ->with('status', 'Episode updated.');
    }

    public function destroy(DramaSeries $series, Episode $episode): RedirectResponse
    {
        abort_unless($episode->series_id === $series->id, 404);

        $episode->delete();
        $series->update(['total_episodes' => $series->episodes()->count()]);

        return redirect()
            ->route('series.show', $series)
            ->with('status', 'Episode deleted.');
    }

    private function validatedData(Request $request, DramaSeries $series, ?Episode $episode = null): array
    {
        $episodeId = $episode?->id ?? 'NULL';

        return $request->validate([
            'number' => ['required', 'integer', 'min:1', 'max:9999', 'unique:reel_episodes,number,'.$episodeId.',id,series_id,'.$series->id],
            'title' => ['required', 'string', 'max:190'],
            'synopsis' => ['required', 'string'],
            'thumbnail_url' => ['nullable', 'url', 'max:500'],
            'thumbnail_file' => ['nullable', 'image', 'max:4096'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-m4v', 'max:512000'],
            'duration_seconds' => ['required', 'integer', 'min:1', 'max:3600'],
            'is_locked' => ['nullable', 'boolean'],
            'coin_price' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'show_like_button' => ['nullable', 'boolean'],
            'show_watchlist_button' => ['nullable', 'boolean'],
            'show_share_button' => ['nullable', 'boolean'],
            'show_episodes_button' => ['nullable', 'boolean'],
            'likes' => ['required', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
        ]);
    }

    private function applyVisibilityFlags(Request $request, array $data): array
    {
        foreach ([
            'show_like_button',
            'show_watchlist_button',
            'show_share_button',
            'show_episodes_button',
        ] as $field) {
            $data[$field] = $request->boolean($field, true);
        }

        return $data;
    }

    private function mediaPath(Request $request, string $fileKey, ?string $fallback, string $folder): string
    {
        if (! $request->hasFile($fileKey)) {
            return (string) $fallback;
        }

        return Storage::disk('public')->url($request->file($fileKey)->store($folder, 'public'));
    }
}
