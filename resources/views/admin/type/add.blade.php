@extends('admin.layouts.master')

@section('title', __('Label.Add Type'))

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('type')}}">{{__('Label.Types')}}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{__('Label.Add Types')}}
                    </li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('type') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('Label.Types')}}</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form enctype="multipart/form-data" id="save_type">
                @csrf
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{__('Label.NAME')}}</label>
                            <input name="name" type="text" class="form-control" id="name"
                                placeholder="{{__('Label.Please Enter Name')}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">{{__('Label.Types')}}</label>
                            <select class="form-control" id="type" name="type">
                                <option value="1"> {{__('Label.Video')}}</option>
                                <option value="0"> Web Series</option>
                                <!--<option value="2"> {{__('Label.Show')}}</option>
                                <option value="5"> Upcoming</option>-->
                            </select>
                        </div>
                    </div>
                </div>
				<div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{__('Label.IMAGE')}}</label>
                            <input type="file" class="form-control" id="image" name="type_image">
                            <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="custom-file ml-5">
                                <img src="{{asset('assets/imgs/no_img.png')}}" style="height: 120px; width: 120px;" class="img-thumbnail" id="preview-image-before-upload">
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_type()">{{__('Label.SAVE')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        function save_type() {

            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){

                var formData = new FormData($("#save_type")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("typeSave") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_type', '{{ route("type") }}');
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