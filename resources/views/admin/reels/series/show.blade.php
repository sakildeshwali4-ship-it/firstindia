@extends('admin.layouts.master')

@section('title', $series->title)

@section('content')
    <div class="body-content">
        <h1 class="page-title-sm">{{ $series->title }}</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-8">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Label.Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('series.index') }}">Webseries</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $series->title }}</li>
                </ol>
            </div>
            <div class="col-sm-4 d-flex align-items-center justify-content-end" style="margin-top:-14px">
                <a href="{{ route('series.episodes.create', $series) }}" class="btn btn-default mr-2">Add Episode</a>
                <a href="{{ route('series.edit', $series) }}" class="btn btn-default">Edit Webseries</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3">
                        @if ($series->poster_url)
                            <img src="{{ $series->poster_url }}" alt="{{ $series->title }}" style="width: 100%; max-width: 220px; border-radius: 8px;">
                        @endif
                    </div>
                    <div class="col-sm-9">
                        <p><strong>Genre:</strong> {{ $series->genre }}</p>
                        <p><strong>Language:</strong> {{ $series->language }}</p>
                        <p><strong>Total Episodes:</strong> {{ $series->total_episodes }}</p>
                        <p><strong>Type:</strong> {{ $series->is_premium ? 'Premium' : 'Free' }}</p>
                        <p><strong>Series Price:</strong> {{ $series->coin_price }} coins</p>
                        <p><strong>Free Episodes:</strong> {{ $series->number_of_free_episodes }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($series->status) }}</p>
                        <p><strong>Rating:</strong> {{ $series->rating }}/5</p>
                        <p><strong>Description:</strong> {{ $series->description }}</p>
                        <form method="post" action="{{ route('series.destroy', $series) }}" onsubmit="return confirm('Delete this webseries and all episodes?')">
                            @csrf
                            @method('delete')
                            <button class="btn btn-danger" type="submit">Delete Webseries</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped text-center table-bordered">
                <thead>
                    <tr style="background: #F9FAFF;">
                        <th>#</th>
                        <th>Thumbnail</th>
                        <th>Episode</th>
                        <th>Details</th>
                        <th>Buttons</th>
                        <th>{{ __('Label.Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($series->episodes as $episode)
                        <tr>
                            <td>{{ $episode->number }}</td>
                            <td>
                                @if ($episode->thumbnail_url)
                                    <img src="{{ $episode->thumbnail_url }}" alt="{{ $episode->title }}" style="width: 70px; height: 90px; object-fit: cover; border-radius: 6px;">
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td class="text-left">
                                <strong>{{ $episode->title }}</strong><br>
                                <small>{{ $episode->synopsis }}</small>
                            </td>
                            <td class="text-left">
                                <div>Duration: {{ $episode->duration_seconds }}s</div>
                                <div>Likes: {{ $episode->likes }}</div>
                                <div>Type: {{ $episode->is_premium ? 'Premium' : 'Free' }}</div>
                                <div>Price: {{ $episode->coin_price }} coins</div>
                            </td>
                            <td class="text-left">
                                <div>Like: {{ $episode->show_like_button ? 'Show' : 'Hide' }}</div>
                                <div>Watchlist: {{ $episode->show_watchlist_button ? 'Show' : 'Hide' }}</div>
                                <div>Share: {{ $episode->show_share_button ? 'Show' : 'Hide' }}</div>
                                <div>Episodes: {{ $episode->show_episodes_button ? 'Show' : 'Hide' }}</div>
                            </td>
                            <td>
                                @if ($episode->video_url)
                                    <a href="{{ $episode->video_url }}" target="_blank" class="btn btn-sm btn-default mb-1">Video</a>
                                @endif
                                <a href="{{ route('series.episodes.edit', [$series, $episode]) }}" class="btn btn-sm btn-default mb-1">Edit</a>
                                <form method="post" action="{{ route('series.episodes.destroy', [$series, $episode]) }}" onsubmit="return confirm('Delete this episode?')">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No episodes added yet. Add the first vertical video episode.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
