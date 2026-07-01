@extends('admin.layouts.master')

@section('title', 'Edit Episodes')

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
                        <a href="{{ route('upcomingTVShow') }}">{{__('Label.TV Show')}}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('upcomingTVShowVideo', ['id' => $tvshowId]) }}">Episode List</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Edit Episode
                    </li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('upcomingTVShowVideo',['id'=> $tvshowId]) }}" class="btn btn-default mw-120" style="margin-top:-14px">Episode List</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form enctype="multipart/form-data" id="save_edit_TVShow_video" autocomplete="off">
                @csrf
                <input type="hidden" name="show_id" id="show_id" value="{{$tvshowId}}">
                <input type="hidden" name="video_id" id="video_id" value="{{$result->id}}">
                <input type="hidden" name="old_video_upload_type" value="{{$result->video_upload_type}}">
                <input type="hidden" name="old_subtitle_type" value="{{$result->subtitle_type}}">
                <input type="hidden" name="old_video_320" value="{{$result->video_320}}">
                <input type="hidden" name="old_video_480" value="{{$result->video_480}}">
                <input type="hidden" name="old_video_720" value="{{$result->video_720}}">
                <input type="hidden" name="old_video_1080" value="{{$result->video_1080}}">
                <input type="hidden" name="old_subtitle_1" value="{{$result->subtitle_1}}">
                <input type="hidden" name="old_subtitle_2" value="{{$result->subtitle_2}}">
                <input type="hidden" name="old_subtitle_3" value="{{$result->subtitle_3}}">

                <div class="custom-border-card">
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{__('Label.Name')}}</label>
                                <input name="name" type="text" class="form-control" placeholder="Enter Episode Name" value="{{$result->name}}">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.Session')}}</label>
                                <select class="form-control" id="session_id" name="session_id">
                                    <option value="">{{__('Label.Select Session')}}</option>
                                    @foreach ($session as $key => $value)
                                    <option value="{{ $value->id}}" {{ $value->id == $result->session_id ? 'selected' : ''}}>
                                        {{ $value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Video Duration</label>
                                <input type="text" id="timePicker" name="video_duration" placeholder="Video Duration" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-lg-6">
                            <label>{{__('Label.Video Upload Type')}}</label>
                            <select name="video_upload_type" id="video_upload_type" class="form-control">
                                <option selected="selected" value="server_video" {{ $result->video_upload_type == "server_video" ? 'selected' : ''}}>{{__('Label.Server Video')}}</option>
                                <option value="external" {{ $result->video_upload_type == "external" ? 'selected' : ''}}>External URL</option>
                                <option value="youtube" {{ $result->video_upload_type == "youtube" ? 'selected' : ''}}>Youtube</option>
                                <option value="vimeo" {{ $result->video_upload_type == "vimeo" ? 'selected' : ''}}>Vimeo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group Is_Download">
                                <label >Is Download</label>
                                <select class="form-control" name="download">
                                <option value="0" {{ $result->download == 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{ $result->download == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-lg-3 video_box">
                            <div style="display: block;">
                                <label>{{__('Label.Upload Video (320 px)')}}</label>
                                <div id="filelist"></div>
                                <div id="container" style="position: relative;">
                                    <div class="form-group">
                                        <input type="file" id="uploadFile" name="uploadFile" style="position: relative; z-index: 1;" class="form-control">
                                    </div>
                                    <input type="hidden" name="upload_video_320" id="mp3_file_name" class="form-control">
        
                                    <div class="form-group">
                                        <a id="upload" class="btn text-white" style="background-color:#4e45b8;">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->video_upload_type == 'server_video'){{{$result->video_320}}}@endif</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-3 video_box">
                            <div style="display: block;">
                                <label>{{__('Label.Upload Video (480 px)')}}</label>
                                <div id="filelist1"></div>
                                <div id="container1" style="position: relative;">
                                    <div class="form-group">
                                        <input type="file" id="uploadFile1" name="uploadFile1" style="position: relative; z-index: 1;" class="form-control">
                                    </div>
                                    <input type="hidden" name="upload_video_480" id="mp3_file_name1" class="form-control">
        
                                    <div class="form-group">
                                        <a id="upload1" class="btn text-white" style="background-color:#4e45b8;">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->video_upload_type == 'server_video'){{{$result->video_480}}}@endif</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-3 video_box">
                            <div style="display: block;">
                                <label>{{__('Label.Upload Video (720 px)')}}</label>
                                <div id="filelist2"></div>
                                <div id="container2" style="position: relative;">
                                    <div class="form-group">
                                        <input type="file" id="uploadFile2" name="uploadFile2" style="position: relative; z-index: 1;" class="form-control">
                                    </div>
                                    <input type="hidden" name="upload_video_720" id="mp3_file_name2" class="form-control">
        
                                    <div class="form-group">
                                        <a id="upload2" class="btn text-white" style="background-color:#4e45b8;">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->video_upload_type == 'server_video'){{{$result->video_720}}}@endif</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-3 video_box">
                            <div style="display: block;">
                                <label>{{__('Label.Upload Video (1080 px)')}}</label>
                                <div id="filelist3"></div>
                                <div id="container3" style="position: relative;">
                                    <div class="form-group">
                                        <input type="file" id="uploadFile3" name="uploadFile3" style="position: relative; z-index: 1;" class="form-control">
                                    </div>
                                    <input type="hidden" name="upload_video_1080" id="mp3_file_name3" class="form-control">
        
                                    <div class="form-group">
                                        <a id="upload3" class="btn text-white" style="background-color:#4e45b8;">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->video_upload_type == 'server_video'){{{$result->video_1080}}}@endif</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-6 url_box">
                            <label for="url">{{__('Label.URL (320 px)')}}</label>
                            <input name="video_url_320" value="@if($result->video_upload_type != 'server_video'){{{$result->video_320}}}@endif" type="url" class="form-control" placeholder="Enter Video URL (320 px)">
                        </div>
                        <div class="form-group col-lg-6 url_box">
                            <label for="url">{{__('Label.URL (480 px)')}}</label>
                            <input name="video_url_480" value="@if($result->video_upload_type != 'server_video'){{{$result->video_480}}}@endif" type="url" class="form-control" placeholder="Enter Video URL (480 px)">
                        </div>
                        <div class="form-group col-lg-6 url_box">
                            <label for="url">{{__('Label.URL (720 px)')}}</label>
                            <input name="video_url_720" value="@if($result->video_upload_type != 'server_video'){{{$result->video_720}}}@endif" type="url" class="form-control" placeholder="Enter Video URL (720 px)">
                        </div>
                        <div class="form-group col-lg-6 url_box">
                            <label for="url">{{__('Label.URL (1080 px)')}}</label>
                            <input name="video_url_1080" value="@if($result->video_upload_type != 'server_video'){{{$result->video_1080}}}@endif" type="url" class="form-control" placeholder="Enter Video URL (1080 px)">
                        </div>            
                    </div>
                </div>
                <div class="custom-border-card">
                    <div class="form-row">
                        <div class="form-group col-lg-6">
                            <label>Subtitle Type</label>
                            <select name="subtitle_type" id="subtitle_type" class="form-control">
                                <option selected="selected" value="server_video" {{ $result->subtitle_type == "server_file" ? 'selected' : ''}}>Server File</option>
                                <option value="external" {{ $result->subtitle_type == "external_url" ? 'selected' : ''}}>External URL</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-4 mb-0">
                            <div class="form-group">
                                <label>Language Name</label>
                                <input type="text" name="subtitle_lang_1" value="{{$result->subtitle_lang_1}}" class="form-control" placeholder="Enter Your Language">
                            </div>
                        </div>
                        <div class="col-md-4 mb-0">
                            <div class="form-group">
                                <label>Language Name</label>
                                <input type="text" name="subtitle_lang_2" value="{{$result->subtitle_lang_2}}" class="form-control" placeholder="Enter Your Language">
                            </div>
                        </div>
                        <div class="col-md-4 mb-0">
                            <div class="form-group">
                                <label>Language Name</label>
                                <input type="text" name="subtitle_lang_3" value="{{$result->subtitle_lang_3}}" class="form-control" placeholder="Enter Your Language">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-lg-4 subtitle_box">
                            <div style="display: block;">
                                <label>Upload SubTitle</label>
                                <div id="filelist4"></div>
                                <div id="container4" style="position: relative;">
                                    <div class="form-group">
                                        <input type="file" id="uploadFile4" name="uploadFile4" style="position: relative; z-index: 1;" class="form-control">
                                    </div>
                                    <input type="hidden" name="subtitle1" id="mp3_file_name4" class="form-control">
        
                                    <div class="form-group">
                                        <a id="upload4" class="btn text-white" style="background-color:#4e45b8;">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->subtitle_type == 'server_file'){{{$result->subtitle_1}}}@endif</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-4 subtitle_box">
                            <div style="display: block;">
                                <label>Upload SubTitle</label>
                                <div id="filelist6"></div>
                                <div id="container6" style="position: relative;">
                                    <div class="form-group">
                                        <input type="file" id="uploadFile6" name="uploadFile6" style="position: relative; z-index: 1;" class="form-control">
                                    </div>
                                    <input type="hidden" name="subtitle2" id="mp3_file_name6" class="form-control">

                                    <div class="form-group">
                                        <a id="upload6" class="btn text-white" style="background-color:#4e45b8;">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->subtitle_type == 'server_file'){{{$result->subtitle_2}}}@endif</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-4 subtitle_box">
                            <div style="display: block;">
                                <label>Upload SubTitle</label>
                                <div id="filelist7"></div>
                                <div id="container7" style="position: relative;">
                                    <div class="form-group">
                                        <input type="file" id="uploadFile7" name="uploadFile7" style="position: relative; z-index: 1;" class="form-control">
                                    </div>
                                    <input type="hidden" name="subtitle3" id="mp3_file_name7" class="form-control">
        
                                    <div class="form-group">
                                        <a id="upload7" class="btn text-white" style="background-color:#4e45b8;">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->subtitle_type == 'server_file'){{{$result->subtitle_3}}}@endif</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-4 subtitle_url_box">
                            <label>SubTitle</label>
                            <input name="subtitle_url_1" type="url" value="@if($result->subtitle_type != 'server_file'){{{$result->subtitle_1}}}@endif" class="form-control" placeholder="Enter Subtitle URL">
                        </div>
                        <div class="form-group col-lg-4 subtitle_url_box">
                            <label>SubTitle</label>
                            <input name="subtitle_url_2" type="url" value="@if($result->subtitle_type != 'server_file'){{{$result->subtitle_2}}}@endif" class="form-control" placeholder="Enter Subtitle URL">
                        </div>
                        <div class="form-group col-lg-4 subtitle_url_box">
                            <label>SubTitle</label>
                            <input name="subtitle_url_3" type="url" value="@if($result->subtitle_type != 'server_file'){{{$result->subtitle_3}}}@endif" class="form-control" placeholder="Enter Subtitle URL">
                        </div>
                    </div>
                </div>
                <div class="custom-border-card">
                    <div class="form-row">
                        <div class="form-group col-lg-12">
                            <label>{{__('Label.Description')}}</label>
                            <textarea name="description" class="form-control" rows="3" id="description" placeholder="{{__('Label.Hello,')}}">{{ $result->description }}</textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.Is Premium')}}</label>
                                <select class="form-control" name="is_premium">
                                    <option value="0" {{ $result->is_premium == "0" ? 'selected' : ''}}>{{__('Label.No')}}</option>
                                    <option value="1" {{ $result->is_premium == "1" ? 'selected' : ''}}>{{__('Label.Yes')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.Is Title')}}</label>
                                <select class="form-control" name="is_title">
                                    <option value="0" {{ $result->is_title == "0" ? 'selected' : ''}}>{{__('Label.No')}}</option>
                                    <option value="1" {{ $result->is_title == "1" ? 'selected' : ''}}>{{__('Label.Yes')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.Thumbnail Image')}}</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail" value="{{$result->thumbnail}}">
                                <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.Landscape Image')}}</label>
                                <input type="file" class="form-control" id="landscape" name="landscape" value="{{$result->thumbnail}}">
                                <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-file mb-5">
                                    <img src="{{$result->thumbnail}}" style="height: 130px; width: 120px;" class="img-thumbnail" id="preview-image-before-upload">
                                    <input type="hidden" name="old_thumbnail" value="{{$result->thumbnail}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-file mb-5">
                                    <img src="{{$result->landscape}}" style="height: 100px; width: 150px;" class="img-thumbnail" id="preview-image-before-upload1">
                                    <input type="hidden" name="old_landscape" value="{{$result->landscape}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-top mt-2 pt-3 text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_edit_TVShow_video()">{{__('Label.SAVE')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
	<script>

        var duration = '<?php echo $result->video_duration; ?>';

        function msToHours(duration) {
            var hours = Math.floor((duration / (1000 * 60 * 60)) % 24);
                hours = (hours < 10) ? "0" + hours : hours;
                return hours;
        }
        function msToMinutes(duration) {
            var minutes = Math.floor((duration / (1000 * 60)) % 60),
                minutes = (minutes < 10) ? "0" + minutes : minutes;
                return minutes;
        }
        function msToSeconds(duration) {
            var seconds = Math.floor((duration / 1000) % 60),
                seconds = (seconds < 10) ? "0" + seconds : seconds;
                return seconds;
        }

        let hours = msToHours(duration);
        let minutes = msToMinutes(duration);
        let seconds = msToSeconds(duration);

        var date = new Date();
            date.setHours(hours,minutes,seconds);

        $('#timePicker').datetimepicker({
            useCurrent: false,
            format:'HH:mm:ss',
            defaultDate: date,
            showClose:true,
            showTodayButton: true,
            icons: {
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                today: "fa fa-clock-o",
                close: "fa fa-times",
            }
        })

		function save_edit_TVShow_video() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
                var formData = new FormData($("#save_edit_TVShow_video")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("upcomingtvshowvideoUpdate", ["show_id" => $tvshowId, "id" => $result->id]) }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_edit_TVShow_video', '{{ route("upcomingTVShowVideo", ["id" => $tvshowId]) }}');
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

        $(document).ready(function() {
            var video_upload_type = "<?php echo $result->video_upload_type; ?>";
            if(video_upload_type == "server_video"){
                $(".url_box").hide();
            } else if(video_upload_type == "external" || video_upload_type == "youtube" || video_upload_type == "vimeo"){
                $(".video_box").hide();
            } else {
                $(".url_box").hide();
            }

            if(video_upload_type == "server_video" || video_upload_type == "external"){
                $(".Is_Download").show();
            } else {
                $(".Is_Download").hide();
            }

            $('#video_upload_type').change(function() {
                var optionValue = $(this).val();

                if (optionValue == "server_video") {
                    $(".video_box").show();
                    $(".url_box").hide();
                } else {
                    $(".url_box").show();
                    $(".video_box").hide();
                }

                if (optionValue == 'server_video' || optionValue == 'external') {
                    $(".Is_Download").show();
                } else {
                    $(".Is_Download").hide();
                }
            });

            var subtitle_type = "<?php echo $result->subtitle_type; ?>";
            if(subtitle_type == "server_video"){
                $(".subtitle_url_box").hide();
            } else if(subtitle_type == "external"){
                $(".subtitle_box").hide();
            } else {
                $(".subtitle_url_box").hide();
            } 

            $('#subtitle_type').change(function() {
                var optionValue = $(this).val();
    
                if (optionValue == 'server_video') {
                    $(".subtitle_box").show();
                    $(".subtitle_url_box").hide();
                } else {
                    $(".subtitle_url_box").show();
                    $(".subtitle_box").hide();
                }
            });
        });
	</script>
@endsection