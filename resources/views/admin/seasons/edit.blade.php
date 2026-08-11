@extends('admin.layouts.master')

@section('title', "Season Edit")

@section('content')
<div class="body-content">

    <h1 class="page-title-sm">@yield('title')</h1>

    <div class="row">
        <div class="col-sm-10">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('seasons.index') }}">Season</a>
                </li>
                <li class="breadcrumb-item active">Season Edit</li>
            </ol>
        </div>

        <div class="col-sm-2 d-flex justify-content-end">
            <a href="{{ route('seasons.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">
                Seasons
            </a>
        </div>
    </div>

    <div class="card custom-border-card mt-3">

        <form enctype="multipart/form-data" id="update_season" autocomplete="off">
            @csrf

            <input type="hidden" name="id" value="{{ $result->id }}">
             
            <div class="form-row">

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Select Web Series</label>
                        <select class="form-control" name="web_series_id">

                            <option value="">-- Select Web Series --</option>

                            @forelse($webseries as $id => $title)
                                <option value="{{ $id }}"
                                    {{ $result->web_series_id == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @empty
                                <option value="">No Web Series Available</option>
                            @endforelse

                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{__('Label.Title')}}</label>
                        <input name="title" type="text"
                            class="form-control"
                            value="{{ old('title', $result->title) }}"
                            placeholder="Please Enter Title">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Is Active</label>
                        <select class="form-control" name="isActive">
                            <option value="0" {{ $result->isActive == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $result->isActive == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Season Number</label>
                        <input type="number"
                            name="season_number"
                            class="form-control"
                            value="{{ old('season_number', $result->season_number) }}"
                            placeholder="Enter Season Number">
                    </div>
                </div>
            </div>
 
            <div class="form-row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{__('Label.IMAGE')}}</label>
                        <input type="file" class="form-control" name="image">
                        <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{__('Label.Landscape Image')}}</label>
                        <input type="file" class="form-control" id="landscape" name="landscape" value="{{$result->landscape}}">
                        <input type="hidden" class="form-control" id="landscape_imdb" name="landscape_imdb">
                        <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Video URL</label>
                        <input type="url"
                            name="video"
                            class="form-control"
                            value="{{ old('video', $result->video) }}"
                            placeholder="Enter Trailer Url">
                    </div>
                </div>


            </div>
 
            <div class="form-row mb-4">
                <div class="col-md-4">
                    <div class="form-group">
                        <img src="{{ $result->thumbnail ?? asset('assets/imgs/no_img.png') }}"
                             style="height: 130px; width: 120px;"
                             class="img-thumbnail"
                             id="preview-image-before-upload">
                             <input type="hidden" name="old_image" value="{{$result->thumbnail}}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="custom-file">
                            <img src="{{$result->landscape}}" style="height: 100px; width: 150px;" class="img-thumbnail" id="preview-image-before-upload1">
                            <input type="hidden" name="old_landscape" value="{{$result->landscape}}">
                        </div>
                    </div>
                </div>
            </div>
 
            <div class="form-row mb-5">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Short Description</label>
                        <textarea class="form-control" name="meta_desc"
                            rows="4">{{ old('meta_desc', $result->meta_desc) }}</textarea>
                    </div>
                </div>
            </div>
<input type="hidden" value="{{$result->id}}" name="id"> 
            <div class="text-right">
                <button type="button"
                        class="btn btn-default mw-120"
                        onclick="update_season()">
                    {{__('Label.UPDATE')}}
                </button>
            </div>

        </form>

    </div>
</div>
@endsection

@section('pagescript')
<script>
function update_season() {

    var Check_Admin = '<?php echo Check_Admin_Access(); ?>';

    if(Check_Admin == 1){

        var formData = new FormData($("#update_season")[0]);
        $("#dvloader").show();

        $.ajax({
            type: "POST",
            url: "{{ route('seasons.update') }}", 
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            success: function(resp) {
                $("#dvloader").hide();

                get_responce_message(resp, "update_season", "{{ route('seasons.index') }}");
            },

            error: function(xhr) {
                $("#dvloader").hide();
                toastr.error("Something went wrong!");
            }
        });

    } else {
        toastr.error("You have no right to update.");
    }
}
</script>
@endsection
