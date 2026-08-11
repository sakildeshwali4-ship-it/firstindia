@extends('admin.layouts.master')

@section('title', 'Assign Ads')

@section('content')
    <div class="body-content">
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('livetv.index') }}">Live Tv Url</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Assign Ads</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('livetv.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">Live Tv List</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form id="save_livetv_ads" autocomplete="off">
                @csrf
                <input type="hidden" name="live_tv_id" value="{{ $liveTv->id }}">

                <div class="form-row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Live Tv</label>
                            <input type="text" class="form-control" value="{{ $liveTv->name }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Ads</label>
                            <select name="ad_ids[]" id="ad_ids" class="form-control" multiple style="width:100%!important;">
                                @foreach ($ads as $ad)
                                    <option value="{{ $ad->id }}" {{ in_array($ad->id, $selectedAds) ? 'selected' : '' }}>
                                        {{ $ad->title }} ({{ ucfirst(str_replace('_', ' ', $ad->type)) }} / {{ ucfirst($ad->media_type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Ad Position</label>
                            <select name="ad_position" class="form-control">
                                <option value="pre_roll" {{ $adPosition == 'pre_roll' ? 'selected' : '' }}>Pre Roll</option>
                                <option value="mid_roll" {{ $adPosition == 'mid_roll' ? 'selected' : '' }}>Mid Roll</option>
                                <option value="post_roll" {{ $adPosition == 'post_roll' ? 'selected' : '' }}>Post Roll</option>
                                <option value="banner" {{ $adPosition == 'banner' ? 'selected' : '' }}>Banner</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3 text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_livetv_ads()">{{__('Label.UPDATE')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        $(document).ready(function() {
            $('#ad_ids').select2({
                placeholder: 'Select Ads',
            });
        });

        function save_livetv_ads() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
                var formData = new FormData($("#save_livetv_ads")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("livetv.ads.save") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_livetv_ads', '{{ route("livetv.index") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            } else {
                toastr.error('You have no right to add, edit, and delete.');
            }
        }
    </script>
@endsection
