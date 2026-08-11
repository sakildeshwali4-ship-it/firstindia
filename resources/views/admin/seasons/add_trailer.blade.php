@extends('admin.layouts.master')

@section('title', "Season Add")

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="row">
            <div class="col-sm-10">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('seasons.index') }}">Season Trailer</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Season Trailer Add
                    </li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('seasons.trailers') }}" class="btn btn-default mw-120" style="margin-top:-14px">Trailers List</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form enctype="multipart/form-data" id="save_season" autocomplete="off">
                @csrf
                <div class="form-row">
                     <div class="col-md-3">
                        <div class="form-group">
                            <label>Select Season</label> 
                            <select class="form-control" name="season_id">

                            @if($season->count() > 0)

                                <option value="">-- Select Web Series --</option>

                                @foreach($season as $id => $title)
                                    <option value="{{ $id }}">
                                        {{ $title }}
                                    </option>
                                @endforeach

                            @else
                                <option value="">No Season Available</option>
                            @endif

                        </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{__('Label.Title')}}</label>
                            <input name="title" type="text" class="form-control" placeholder="Please Enter Title">
                        </div>
                    </div> 

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>is Active</label>
                            <select class="form-control" name="isActive">
                                <option value="0">{{__('Label.No')}}</option>
                                <option value="1">{{__('Label.Yes')}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Trailer Number</label>
                            <input type="number" name="trailer_number" class="form-control" placeholder="Enter Number Of Trailer">
                        </div>
                    </div> 
                </div>
                <div class="form-row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{__('Label.IMAGE')}}</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                        </div>
                    </div> 
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{__('Label.Landscape Image')}}</label>
                            <input type="file" class="form-control" id="landscape" name="landscape">
                            <input type="hidden" class="form-control" id="landscape_imdb" name="landscape_imdb">
                            <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                        </div>
                    </div> 
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Video URL</label>
                            <input type="url" name="video" class="form-control" placeholder="Enter Trailer Url">
                        </div>
                    </div> 
                </div>
                <div class="form-row mb-5">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-file">
                                <img src="{{asset('assets/imgs/no_img.png')}}" style="height: 130px; width: 120px;" class="img-thumbnail" id="preview-image-before-upload">
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-file">
                                <img src="{{asset('assets/imgs/no_img.png')}}" style="height: 100px; width: 150px;" class="img-thumbnail" id="preview-image-before-upload1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row mb-5">
                    <div class="col-md-12"> 
                        <div class="form-group">
                            <label>Sort Description</label>
                            <textarea type="text" class="form-control"  name="meta_desc"> </textarea>
                        </div>
                    </div> 
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_season()">{{__('Label.SAVE')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        function save_season() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){

                var formData = new FormData($("#save_season")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("trailer.store") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_season', '{{ route("seasons.trailers") }}');
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