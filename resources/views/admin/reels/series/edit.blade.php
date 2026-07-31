@extends('admin.layouts.master')

@section('title', 'Edit Webseries')

@section('content')
    <div class="body-content">
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Label.Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('series.index') }}">Webseries</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('series.show', $series) }}">{{ $series->title }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Webseries</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end" style="margin-top:-14px">
                <a href="{{ route('series.show', $series) }}" class="btn btn-default mw-120">Back</a>
            </div>
        </div>

        <form method="post" action="{{ route('series.update', $series) }}" enctype="multipart/form-data">
            @csrf
            @method('put')
            @include('admin.reels.series.form', ['submitLabel' => 'Save Changes'])
        </form>
    </div>
@endsection
