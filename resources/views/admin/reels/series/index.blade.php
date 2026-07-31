@extends('admin.layouts.master')

@section('title', 'Webseries')

@section('content')
    <div class="body-content">
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Label.Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Webseries</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end" style="margin-top:-14px">
                <a href="{{ route('series.create') }}" class="btn btn-default mw-120">Add Webseries</a>
            </div>
        </div>

        <div class="page-search mb-3">
            <form method="get" action="{{ route('series.index') }}">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="series-search-icon"><img src="{{ asset('assets/imgs/search.png') }}"></span>
                    </div>
                    <input type="text" name="q" value="{{ $query }}" class="form-control" placeholder="Search title, genre, language" aria-describedby="series-search-icon">
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped text-center table-bordered">
                <thead>
                    <tr style="background: #F9FAFF;">
                        <th>#</th>
                        <th>Poster</th>
                        <th>Title</th>
                        <th>Episodes</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>{{ __('Label.Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($seriesList as $index => $series)
                        <tr>
                            <td>{{ $seriesList->firstItem() + $index }}</td>
                            <td>
                                @if ($series->poster_url)
                                    <img src="{{ $series->poster_url }}" alt="{{ $series->title }}" style="width: 60px; height: 90px; object-fit: cover; border-radius: 6px;">
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td class="text-left">
                                <strong>{{ $series->title }}</strong><br>
                                <small>{{ $series->genre }} / {{ $series->language }} / {{ $series->rating }}/5</small>
                            </td>
                            <td>{{ $series->episodes_count }}</td>
                            <td>{{ $series->is_premium ? 'Premium' : 'Free' }}</td>
                            <td>{{ ucfirst($series->status) }}</td>
                            <td>
                                <a href="{{ route('series.show', $series) }}" class="btn btn-default">Episodes</a>
                                <a href="{{ route('series.edit', $series) }}" class="btn btn-default">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No webseries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">{{ $seriesList->links() }}</div>
    </div>
@endsection
