@extends('admin.layouts.master')

@section('title', "Web Series Add")

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
                        <a href="{{ route('web-series.index') }}">Web Series</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Web Series Add
                    </li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('web-series.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">Web Series</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form enctype="multipart/form-data" id="save_channel" autocomplete="off">
                @csrf
                <div class="form-row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{__('Label.Title')}}</label>
                            <input name="title" type="text" class="form-control" placeholder="Please Enter Title">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>IsHeader</label>
                            <select class="form-control" name="isHeader">
                                <option value="0">{{__('Label.No')}}</option>
                                <option value="1">{{__('Label.Yes')}}</option>
                            </select>
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
                            <label>Release Date</label>
                            <input type="date" class="form-control"  name="release_date" value="{{ date('Y-m-d') }}"> 
                        </div>
                    </div> 
                </div>
                 <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.Thumbnail Image')}}</label>
                                <input type="file" class="form-control" id="thumbnail" name="image">
                                <input type="hidden" class="form-control" id="thumbnail_imdb" name="thumbnail_imdb">
                                <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.Landscape Image')}}</label>
                                <input type="file" class="form-control" id="landscape" name="landscape">
                                <input type="hidden" class="form-control" id="landscape_imdb" name="landscape_imdb">
                                <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-file">
                                    <img src="{{asset('assets/imgs/no_img.png')}}" style="height: 130px; width: 120px;" class="img-thumbnail" id="preview-image-before-upload">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-file">
                                    <img src="{{asset('assets/imgs/no_img.png')}}" style="height: 100px; width: 150px;" class="img-thumbnail" id="preview-image-before-upload1">
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{__('Label.IMAGE')}}</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Release Date</label>
                            <input type="date" class="form-control"  name="release_date" value="{{ date('Y-m-d') }}"> 
                        </div>
                    </div> 
                </div>
                <div class="form-row mb-5">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="custom-file">
                                <img src="{{asset('assets/imgs/no_img.png')}}" style="height: 130px; width: 120px;" class="img-thumbnail" id="preview-image-before-upload">
                            </div>
                        </div>
                    </div> 
                </div>  -->
                    <div class="form-row">
                        <div class="form-group col-lg-3">
                            <label>Enable Likes?</label>
                            <select name="is_like" class="form-control">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div> 
                        <div class="form-group col-lg-3">
                            <label>Enable Dislikes?</label>
                            <select name="is_dislike" class="form-control">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div> 
                        <div class="form-group col-lg-3">
                            <label>Enable Superlikes?</label>
                            <select name="is_superlike" class="form-control">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div> 
                        <div class="form-group col-lg-3">
                            <label>Enable Wishlists?</label>
                            <select name="wishlist" class="form-control">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div> 
                    </div> 
                <div class="form-row mb-5">
                    <div class="col-md-12"> 
                        <div class="form-group">
                            <label>Description</label>
                            <textarea type="text" class="form-control"  name="description"> </textarea>
                        </div>
                    </div> 
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_channel()">{{__('Label.SAVE')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        function save_channel() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){

                var formData = new FormData($("#save_channel")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("web-series.store") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_channel', '{{ route("web-series.index") }}');
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