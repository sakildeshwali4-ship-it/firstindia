@extends('admin.layouts.master')

@section('title', __('Label.Add Cast'))

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
                        <a href="{{ route('cast') }}">Cast</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{__('Label.Add Cast')}}
                    </li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('cast') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('Label.Cast')}}</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form id="save_cast" autocomplete="off">
                @csrf
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{__('Label.NAME')}}</label>
                            <input name="name" type="text" class="form-control" placeholder="{{__('Label.Please Enter Cast')}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{__('Label.Type')}}</label>
                            <select class="form-control" name="type">
                                <option value="">{{__('Label.Select Type')}}</option>
                                <option value="Director">{{__('Label.Director')}}</option>
                                <option value="Writer">{{__('Label.Writer')}}</option>
                                <option value="Actor"> {{__('Label.Actor')}}</option>
                                <option value="Actress"> {{__('Label.Actress')}}</option>
                                <option value="Cricketer"> {{__('Label.Cricketer')}}</option>
                                <option value="Dancers"> {{__('Label.Dancers')}}</option>
                                <option value="Journalist"> {{__('Label.Journalist')}}</option>
                                <option value="Other"> {{__('Label.Other')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-12">
                        <label>{{__('Label.Personal Info')}}</label>
                        <textarea name="personal_info" class="form-control" rows="5" id="personal_info" placeholder="I am ..."></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="image">{{__('Label.IMAGE')}}</label>
                            <input type="file" class="form-control" id="image" name="image" autocomplete="off">
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
                <div class="text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_cast()">{{__('Label.SAVE')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        function save_cast() {

            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
                var formData = new FormData($("#save_cast")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("castSave") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_cast', '{{ route("cast") }}');
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