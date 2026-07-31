<?php

namespace App\Http\Controllers\Admin\Reels;

use App\Http\Controllers\Controller;
use App\Models\Reels\DramaSeries;
use App\Services\ReelSeriesPricingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SeriesController extends Controller
{
    public function __construct(private ReelSeriesPricingService $pricingService)
    {
    }

    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        return view('admin.reels.series.index', [
            'query' => $query,
            'seriesList' => DramaSeries::query()
                ->withCount('episodes')
                ->when($query !== '', function ($builder) use ($query): void {
                    $builder->where(function ($inner) use ($query): void {
                        $inner
                            ->where('title', 'like', "%{$query}%")
                            ->orWhere('genre', 'like', "%{$query}%")
                            ->orWhere('language', 'like', "%{$query}%");
                    });
                })
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.reels.series.create', [
            'series' => new DramaSeries(['status' => 'published']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['poster_url'] = $this->mediaPath($request, 'poster_file', $data['poster_url'] ?? null, 'posters');
        $data['cover_url'] = $this->mediaPath($request, 'cover_file', $data['cover_url'] ?? null, 'posters');
        $data['is_premium'] = $request->boolean('is_premium');
        $data['coin_price'] = $data['is_premium'] ? ($data['coin_price'] ?? 0) : 0;
        $series = DB::transaction(function () use ($data): DramaSeries {
            $series = DramaSeries::query()->create($data);
            $this->pricingService->syncSeriesEpisodes($series);

            return $series;
        });

        return redirect()
            ->route('series.show', $series)
            ->with('status', 'Webseries created. Now add vertical episodes.');
    }

    public function show(DramaSeries $series): View
    {
        return view('admin.reels.series.show', [
            'series' => $series->load('episodes'),
        ]);
    }

    public function edit(DramaSeries $series): View
    {
        return view('admin.reels.series.edit', [
            'series' => $series,
        ]);
    }

    public function update(Request $request, DramaSeries $series): RedirectResponse
    {
        $data = $this->validatedData($request, $series);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['poster_url'] = $this->mediaPath($request, 'poster_file', $data['poster_url'] ?? $series->poster_url, 'posters');
        $data['cover_url'] = $this->mediaPath($request, 'cover_file', $data['cover_url'] ?? $series->cover_url, 'posters');
        $data['is_premium'] = $request->boolean('is_premium');
        $data['coin_price'] = $data['is_premium'] ? ($data['coin_price'] ?? 0) : 0;
        DB::transaction(function () use ($series, $data): void {
            $series->update($data);
            $this->pricingService->syncSeriesEpisodes($series);
        });

        return redirect()
            ->route('series.show', $series)
            ->with('status', 'Webseries updated.');
    }

    public function destroy(DramaSeries $series): RedirectResponse
    {
        $series->delete();

        return redirect()
            ->route('series.index')
            ->with('status', 'Webseries deleted.');
    }

    private function validatedData(Request $request, ?DramaSeries $series = null): array
    {
        $seriesId = $series?->id ?? 'NULL';

        return $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190', 'unique:drama_series,slug,'.$seriesId],
            'description' => ['required', 'string'],
            'poster_url' => ['nullable', 'url', 'max:500'],
            'cover_url' => ['nullable', 'url', 'max:500'],
            'poster_file' => ['nullable', 'image', 'max:4096'],
            'cover_file' => ['nullable', 'image', 'max:4096'],
            'genre' => ['required', 'string', 'max:80'],
            'language' => ['required', 'string', 'max:80'],
            'rating' => ['required', 'numeric', 'min:0', 'max:5'],
            'total_episodes' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_premium' => ['nullable', 'boolean'],
            'coin_price' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'number_of_free_episodes' => ['required', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);
    }

    private function mediaPath(Request $request, string $fileKey, ?string $fallback, string $folder): string
    {
        if (! $request->hasFile($fileKey)) {
            return (string) $fallback;
        }

        return Storage::disk('public')->url($request->file($fileKey)->store($folder, 'public'));
    }
}
