@extends('admin.layouts.master')

@section('title', __('Label.Edit Video'))

@section('content')
<style>
.select2-search__field { color: #fff; }
</style>
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
                        <a href="{{ route('video') }}">{{__('Label.Video')}}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{__('Label.Edit Video')}}
                    </li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('video') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('Label.Video List')}}</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form enctype="multipart/form-data" id="save_edit_video" autocomplete="off">
                @csrf
                <input type="hidden" name="id" id="id" value="{{$result->id}}">
                <input type="hidden" name="old_video_upload_type" value="{{$result->video_upload_type}}">
                <input type="hidden" name="old_video_320" value="{{$result->video_320}}">
                <input type="hidden" name="old_video_480" value="{{$result->video_480}}">
                <input type="hidden" name="old_video_720" value="{{$result->video_720}}">
                <input type="hidden" name="old_video_1080" value="{{$result->video_1080}}">
                <input type="hidden" name="old_trailer_type" value="{{$result->trailer_type}}">
                <input type="hidden" name="old_trailer" value="{{$result->trailer_url}}">
                <input type="hidden" name="old_subtitle_type" value="{{$result->subtitle_type}}">
                <input type="hidden" name="old_subtitle_1" value="{{$result->subtitle_1}}">
                <input type="hidden" name="old_subtitle_2" value="{{$result->subtitle_2}}">
                <input type="hidden" name="old_subtitle_3" value="{{$result->subtitle_3}}">
                <input name="release_year" type="hidden" class="form-control" id="release_year">
                <input name="imdb_rating" type="hidden" class="form-control" id="imdb_rating">

                <div class="custom-border-card">
                    <!--  <div class="form-row">
                        <div class="col-md-2">
                            <div class="form-group pt-3">
                                <label>Import From IMDb</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="Imdb_id" id="Imdb_id" class="form-control" placeholder="Enter IMDb ID (e.g. tt11783766)">
                                <label class="mt-1 text-gray">Recommended : Search by IMDb ID for better result <a href="https://developer.imdb.com/documentation/key-concepts" target="_blank" class="btn-link">Click Here</a> </label>
                            </div>
                        </div>
                        <div class="col-md-2 ml-5">
                            <div class="form-group">
                                <button type="button" class="btn btn-default mw-120" onclick="imdb_data_fetch()">Fetch</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12 ml-5">
                            <div class="form-group">
                                <label>OR</label>
                            </div>
                        </div>
                    </div> -->
                    <div class="form-row">
                        <div class="col-md-2 pt-3">
                            <div class="form-group mb-0">
                                <label>Movies {{__('Label.Name')}}</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-0">
                            <div class="form-group mb-0">
                                <input type="text" name="name" id="Imdb_name" list="Imdb_name_list" class="form-control" value="{{ $result->name}}" placeholder="Enter Movies Name">
                                <datalist id="Imdb_name_list"></datalist>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="custom-border-card">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.Type')}}</label>
                                <select class="form-control" name="type_id" id="video_type">
                                    <option value=""> {{__('Label.Select Type')}}</option>
                                    @foreach ($type as $key => $value)
                                    <option value="{{ $value->id}}" {{ $result->type_id == $value->id  ? 'selected' : ''}}>
                                        {{ $value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.Channel')}}</label>
                                <select class="form-control" name="channel_id">
                                    <option value="">{{__('Label.Select Channel')}}</option>
                                    @foreach ($channel as $key => $value)
                                    <option value="{{ $value->id}}" {{ $result->channel_id == $value->id  ? 'selected' : ''}}>
                                        {{ $value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
						<div class="col-md-6" id="is_exclusive_movie_wrapper">
                            <div class="form-group">
                                <label>Is Exclusive</label>
                                <select class="form-control" name="is_exclusive_movie" id="is_exclusive_movie">
                                    <option value="0">{{__('Label.No')}}</option>
                                    <option {{ $result->is_exclusive_movie == 1  ? 'selected' : ''}} value="1">{{__('Label.Yes')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
					<div class="form-row" id="exclusive_movie_fieds_wrapper" style="{{ $result->is_exclusive_movie == 1 ? '' : 'display:none' }}">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Trailer URL</label>
                                <input value="<?php echo !empty($result->exclusive_movie_data['trailer_url']) ? $result->exclusive_movie_data['trailer_url'] : ''; ?>" type="text" name="exclusive_movie_data[trailer_url]" class="form-control" placeholder="Enter Trailer Url">
                            </div>
                        </div>
						<div class="form-group col-lg-4 subtitle_box">
							<label>Trailer Placeholder Image</label>
							<div class="form-group">
								<input type="file" id="trailer_image" name="exclusive_movie_data[trailer_image]" class="form-control">
								<input type="hidden" name="trailer_image_old" value="<?php echo !empty($result->exclusive_movie_data['trailer_image']) ? $result->exclusive_movie_data['trailer_image'] : ''; ?>" />
							</div>
                        </div>
						
						<div class="col-md-8">
                            <div class="form-group">
                                <label>Teaser URL</label>
                                <input value="<?php echo !empty($result->exclusive_movie_data['teaser_url']) ? $result->exclusive_movie_data['teaser_url'] : ''; ?>" type="text" name="exclusive_movie_data[teaser_url]" class="form-control" placeholder="Enter Teaser Url">
                            </div>
                        </div>
						<div class="form-group col-lg-4 subtitle_box">
							<label>Teaser Placeholder Image</label>
							<div class="form-group">
								<input type="file" id="teaser_image" name="exclusive_movie_data[teaser_image]" class="form-control">
								<input type="hidden" name="teaser_image_old" value="<?php echo !empty($result->exclusive_movie_data['teaser_image']) ? $result->exclusive_movie_data['teaser_image'] : ''; ?>" />
							</div>
                        </div>
						
						<div class="col-md-8">
                            <div class="form-group">
                                <label>Promo URL</label>
                                <input value="<?php echo !empty($result->exclusive_movie_data['promo_url']) ? $result->exclusive_movie_data['promo_url'] : ''; ?>" type="text" name="exclusive_movie_data[promo_url]" class="form-control" placeholder="Enter Promo Url">
                            </div>
                        </div>
						<div class="form-group col-lg-4 subtitle_box">
							<label>Promo Placeholder Image</label>
							<div class="form-group">
								<input type="file" id="promo_image" name="exclusive_movie_data[promo_image]" class="form-control">
								<input type="hidden" name="promo_image_old" value="<?php echo !empty($result->exclusive_movie_data['promo_image']) ? $result->exclusive_movie_data['promo_image'] : ''; ?>" />
							</div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php $x = explode(",", $result->category_id); ?>
                                <label>{{__('Label.Category')}}</label>
                                <select class="form-control selectd2" style="width:100%!important;" name="category_id[]" multiple id="category_id">
                                    @foreach ($category as $key => $value)
                                    <option value="{{ $value->id}}" {{(in_array($value->id, $x)) ? 'selected' : ''}}>
                                        {{ $value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php $y = explode(",", $result->language_id); ?>
                                <label>{{__('Label.Language')}}</label>
                                <select class="form-control  selectd2_1" style="width:100%!important;" name="language_id[]" id="language_id" multiple>
                                    @foreach ($language as $key => $value)
                                    <option value="{{ $value->id}}" {{(in_array($value->id, $y)) ? 'selected' : ''}}>
                                        {{ $value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php $z = explode(",", $result->cast_id); ?>
                                <label>{{__('Label.Cast')}}</label>
                                <select class="form-control selectd2_2" style="width:100%!important;" name="cast_id[]" multiple id="cast_id">
                                    @foreach ($cast as $key => $value)
                                    <option value="{{ $value->id}}" {{(in_array($value->id, $z)) ? 'selected' : ''}}>
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
                        <div class="col-md-6">
                            <label>Release Date</label>
                            <input name="release_date" type="date" class="form-control" value="{{$result->release_date}}">
                        </div>
                    </div>
                </div>
                <div class="custom-border-card">
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
                        <div class="col-md-6 mb-3">
                            <div class="form-group Is_Download">
                                <label>Is Download</label>
                                <select class="form-control" name="download">
                                    <option value="0" {{ $result->download == 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{ $result->download == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <!--<div class="form-group col-lg-3 video_box">
                            <div style="display: block;">
                                <label>{{__('Label.Upload Video (320 px)')}}</label>
                                <div id="filelist"></div>
                                <div id="container" style="position: relative;">
                                    <div class="form-group">
                                        <input type="file" id="uploadFile" name="uploadFile" style="position: relative; z-index: 1;" class="form-control">
                                    </div>
                                    <input type="hidden" name="upload_video_320" id="mp3_file_name" class="form-control">

                                    <div class="form-group">
                                        <a id="upload" class="btn text-white btn-default">{{__('Label.Upload Files')}}</a>
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
                                        <a id="upload1" class="btn text-white btn-default">{{__('Label.Upload Files')}}</a>
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
                                        <a id="upload2" class="btn text-white btn-default">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->video_upload_type == 'server_video'){{{$result->video_720}}}@endif</label>
                                </div>
                            </div>
                        </div>-->
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
                                        <a id="upload3" class="btn text-white btn-default">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->video_upload_type == 'server_video'){{{$result->video_1080}}}@endif</label>
                                </div>
                            </div>
                        </div>
                        <!--<div class="form-group col-lg-6 url_box">
                            <label>{{__('Label.URL (320 px)')}}</label>
                            <input name="video_url_320" value="@if($result->video_upload_type != 'server_video'){{{$result->video_320}}}@endif" type="url" class="form-control" placeholder="Enter Video URL (320 px)">
                        </div>
                        <div class="form-group col-lg-6 url_box">
                            <label>{{__('Label.URL (480 px)')}}</label>
                            <input name="video_url_480" value="@if($result->video_upload_type != 'server_video'){{{$result->video_480}}}@endif" type="url" class="form-control" placeholder="Enter Video URL (480 px)">
                        </div>
                        <div class="form-group col-lg-6 url_box">
                            <label>{{__('Label.URL (720 px)')}}</label>
                            <input name="video_url_720" value="@if($result->video_upload_type != 'server_video'){{{$result->video_720}}}@endif" type="url" class="form-control" placeholder="Enter Video URL (720 px)">
                        </div>-->
                        <div class="form-group col-lg-6 url_box">
                            <label>{{__('Label.URL (1080 px)')}}</label>
                            <input name="video_url_1080" value="@if($result->video_upload_type != 'server_video'){{{$result->video_1080}}}@endif" type="url" class="form-control" placeholder="Enter Video URL (1080 px)">
                        </div>
                    </div>
                </div>

                <div class="custom-border-card">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Enable Skip?</label>
                            <select name="skip_enable" id="skip_enable" class="form-control">
                                <option value="0"  {{ $result->skip_enable == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $result->skip_enable == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>

                        <div class="form-group skip_fields ml-3">
                            <label>Skip Start Time (mm:ss)</label>
                            <input type="text" name="skip_start" id="skip_start" class="form-control" placeholder="mm:ss" value="{{$result->skip_start}}">
                        </div>

                        <div class="form-group skip_fields">
                            <label>Skip End Time (mm:ss)</label>
                            <input type="text" name="skip_end" id="skip_end" class="form-control" placeholder="mm:ss"  value="{{$result->skip_end}}">
                        </div>
                    </div>
                </div>

                <div class="custom-border-card">
                    <div class="form-row">
                        <div class="form-group col-lg-6">
                            <label>Subtitle Type</label>
                            <select name="subtitle_type" id="subtitle_type" class="form-control">
                                <option selected="selected" value="server_video" {{ $result->subtitle_type == "server_video" ? 'selected' : ''}}>{{__('Label.Server Video')}}</option>
                                <option value="external" {{ $result->subtitle_type == "external" ? 'selected' : ''}}>External URL</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Language Name</label>
                                <input type="text" name="subtitle_lang_1" value="{{$result->subtitle_lang_1}}" class="form-control" placeholder="Enter Your Language">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Language Name</label>
                                <input type="text" name="subtitle_lang_2" value="{{$result->subtitle_lang_2}}" class="form-control" placeholder="Enter Your Language">
                            </div>
                        </div>
                        <div class="col-md-4">
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
                                        <a id="upload4" class="btn text-white btn-default">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->subtitle_type == 'server_video'){{{$result->subtitle_1}}}@endif</label>
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
                                        <a id="upload6" class="btn text-white btn-default">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->subtitle_type == 'server_video'){{{$result->subtitle_2}}}@endif</label>
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
                                        <a id="upload7" class="btn text-white btn-default">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->subtitle_type == 'server_video'){{{$result->subtitle_3}}}@endif</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-4 subtitle_url_box">
                            <label>SubTitle</label>
                            <input name="subtitle_url_1" type="url" value="@if($result->subtitle_type != 'server_video'){{{$result->subtitle_1}}}@endif" class="form-control" placeholder="Enter Subtitle URL">
                        </div>
                        <div class="form-group col-lg-4 subtitle_url_box">
                            <label>SubTitle</label>
                            <input name="subtitle_url_2" type="url" value="@if($result->subtitle_type != 'server_video'){{{$result->subtitle_2}}}@endif" class="form-control" placeholder="Enter Subtitle URL">
                        </div>
                        <div class="form-group col-lg-4 subtitle_url_box">
                            <label>SubTitle</label>
                            <input name="subtitle_url_3" type="url" value="@if($result->subtitle_type != 'server_video'){{{$result->subtitle_3}}}@endif" class="form-control" placeholder="Enter Subtitle URL">
                        </div>
                    </div>
                </div>
                <!--<div class="custom-border-card">
                    <div class="form-row">
                        <div class="form-group col-lg-6">
                            <label>Trailer Type</label>
                            <select name="trailer_type" id="trailer_type" class="form-control">
                                <option selected="selected" value="server_video" {{ $result->trailer_type == "server_video" ? 'selected' : ''}}>{{__('Label.Server Video')}}</option>
                                <option value="external" {{ $result->trailer_type == "external" ? 'selected' : ''}}>External URL</option>
                                <option value="youtube" {{ $result->trailer_type == "youtube" ? 'selected' : ''}}>Youtube</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-6 trailer_box">
                            <div style="display: block;">
                                <label>Trailer</label>
                                <div id="filelist5"></div>
                                <div id="container5" style="position: relative;">
                                    <div class="form-group">
                                        <input type="file" id="uploadFile5" name="uploadFile5" style="position: relative; z-index: 1;" class="form-control">
                                    </div>
                                    <input type="hidden" name="trailer" id="mp3_file_name5" class="form-control">

                                    <div class="form-group">
                                        <a id="upload5" class="btn text-white btn-default">{{__('Label.Upload Files')}}</a>
                                    </div>
                                    <label class="text-gray">@if($result->trailer_type == 'server_video'){{{$result->trailer_url}}}@endif</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-6 trailer_url_box">
                            <label>Trailer</label>
                            <input name="trailer_url" type="url" class="form-control" value="@if($result->trailer_type != 'server_video'){{{$result->trailer_url}}}@endif" placeholder="Enter Trailer URL">
                        </div>
                    </div>
                </div>-->
                <div class="custom-border-card">
                    <div class="form-row">
                        <div class="form-group col-lg-12">
                            <div class="form-group">
                                <label>{{__('Label.Description')}}</label>
                                <textarea name="description" class="form-control" rows="3" id="description" placeholder="{{__('Label.Hello,')}}">{{$result->description}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.Is Premium')}}</label>
                                <select class="form-control" id="is_premium" name="is_premium">
                                    <option value="0" {{ $result->is_premium == 0  ? 'selected' : ''}}>{{__('Label.No')}}</option>
                                    <option value="1" {{ $result->is_premium == 1  ? 'selected' : ''}}>{{__('Label.Yes')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="is_title">{{__('Label.Is Title')}}</label>
                                <select class="form-control" id="is_title" name="is_title">
                                    <option value="0" {{ $result->is_title == 0  ? 'selected' : ''}}>{{__('Label.No')}}</option>
                                    <option value="1" {{ $result->is_title == 1  ? 'selected' : ''}}>{{__('Label.Yes')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.Thumbnail Image')}}</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail" value="{{$result->thumbnail}}">
                                <input type="hidden" class="form-control" id="thumbnail_imdb" name="thumbnail_imdb">
                                <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('Label.Landscape Image')}}</label>
                                <input type="file" class="form-control" id="landscape" name="landscape" value="{{$result->landscape}}">
                                <input type="hidden" class="form-control" id="landscape_imdb" name="landscape_imdb">
                                <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-file">
                                    <img src="{{$result->thumbnail}}" style="height: 130px; width: 120px;" class="img-thumbnail" id="preview-image-before-upload">
                                    <input type="hidden" name="old_thumbnail" value="{{$result->thumbnail}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <div class="custom-file">
                                <img src="{{$result->landscape}}" style="height: 100px; width: 150px;" class="img-thumbnail" id="preview-image-before-upload1">
                                <input type="hidden" name="old_landscape" value="{{$result->landscape}}">
                            </div>
                        </div>
                    </div>
                </div>
				<div class="custom-border-card">
					<div class="form-row">
                        <div class="form-group col-lg-12">
                            <label>Related Videos</label>
                            <select name="related_videos[]" class="form-control" id="related_videos" multiple>
								@foreach ($related_video_options as $vid => $vname)
                                    <option value="{{ $vid}}" selected="selected">
                                        {{ $vname }}
                                    </option>
                                @endforeach
							</select>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_edit_video()">{{__('Label.SAVE')}}</button>
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

        function save_edit_video() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
				var redirectUrl = '{{ route("video", $queryArray) }}';
				var re = new RegExp('&amp;', 'g');
				var redirectUrl = redirectUrl.replace(re,'&');
                var formData = new FormData($("#save_edit_video")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("videoUpdate") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_edit_video', redirectUrl);
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
			
			/*$('#video_type').change(function() {
				if($(this).val() == 4) {
					$('#is_exclusive_movie_wrapper').show();
				} else {
					$('#is_exclusive_movie_wrapper').hide();
					$('#exclusive_movie_fieds_wrapper').hide();
					$('#is_exclusive_movie').val('0');
				}
			});*/
			
			$('#is_exclusive_movie').change(function() {
				if($(this).val() == 1) {
					$('#exclusive_movie_fieds_wrapper').show();
				} else {
					$('#exclusive_movie_fieds_wrapper').hide();
				}
			});
			
			$("#related_videos").select2({
                placeholder: "Select Related Videos",
				minimumInputLength: 4,
				maximumSelectionLength: 20,
				multiple: true,
                ajax: {
                    type: "POST",
                    url: "{{route('search_related_videos')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
					data: function (params) {
						return {
							term: params.term, // search term
							selected: $("#related_videos").val()
						};
					},
					dataType: 'json'
				}
            });
			
            $("#category_id").select2();
            $(".selectd2").select2({
                placeholder: "Select Category"
            });

            $("#language_od").select2();
            $(".selectd2_1").select2({
                placeholder: "Select Language"
            });

            $("#cast_id").select2();
            $(".selectd2_2").select2({
                placeholder: "Select Cast"
            });

            var video_upload_type = "<?php echo $result->video_upload_type; ?>";

            if (video_upload_type == "server_video") {
                $(".url_box").hide();
            } else {
                $(".video_box").hide();
            }

            if (video_upload_type == "server_video" || video_upload_type == "external") {
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
            if (subtitle_type == "server_video") {
                $(".subtitle_url_box").hide();
            } else if (subtitle_type == "external") {
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

            var trailer_type = "<?php echo $result->trailer_type; ?>";
            if (trailer_type == "server_video") {
                $(".trailer_url_box").hide();
            } else {
                $(".trailer_box").hide();
            }

            $('#trailer_type').change(function() {
                var optionValue = $(this).val();

                if (optionValue == 'server_video') {
                    $(".trailer_box").show();
                    $(".trailer_url_box").hide();
                } else {
                    $(".trailer_url_box").show();
                    $(".trailer_box").hide();
                }
            });
            
            // First load condition
            if ($("#skip_enable").val() == "1") {
                $('.skip_fields').show();
            } else {
                $('.skip_fields').hide();
            }

            // Change event
            $("#skip_enable").on("change", function () {
                if ($(this).val() == "1") {
                    $('.skip_fields').show();
                } else {
                    $('.skip_fields').hide();
                    $("#skip_start").val('');
                    $("#skip_end").val('');
                }
            });
        });

        $('#Imdb_name').keyup(function() {
            var txtVal = this.value;

            if (txtVal.length >= 3) {
                var url = "{{route('SerachName', '')}}" + "/" + txtVal;
                $.ajax({
                    type: "POST",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: txtVal,
                    success: function(resp) {

                        if (resp.status == 200) {
                            if (resp.data.Response = "True") {

                                var Title_Data = resp.data.results;
                                $('#Imdb_name_list').empty();
                                for (let i = 0; i < Title_Data.length; i++) {
                                    $('#Imdb_name_list').append('<option id="' + resp.data.results[i].id + '" value="' + resp.data.results[i].title + '"></option>');
                                }
                            }
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            }
        });
        $('#Imdb_name').on('input', function() {
            var userText = $(this).val();

            $("#Imdb_name_list").find("option").each(function() {
                if ($(this).val() == userText) {

                    var CompanyName = $("#Imdb_name").val();
                    c_id = $('#Imdb_name_list').find('option[value="' + CompanyName + '"]').attr('id');

                    $("#dvloader").show();
                    var url = "{{route('GetData', '')}}" + "/" + c_id;
                    $.ajax({
                        type: "POST",
                        url: url,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: c_id,
                        success: function(resp) {
                            $("#dvloader").hide();

                            if (resp.status == 200) {
                                var C_Id = resp.C_Id;
                                var L_Id = resp.L_Id;
                                var C_Insert_Data = resp.C_Insert_Data;
                                var L_Insert_Data = resp.L_Insert_Data;
                                var Poster_img = resp.Poster_img;
                                var Description = resp.Description;
                                var Cast_Id = resp.Cast_Id;
                                var Cast_Insert_Data = resp.Cast_Insert_Data;
                                var Year = resp.Year;
                                var imdbRating = resp.imdbRating;

                                // Append New Category
                                for (let i = 0; i < C_Insert_Data.length; i++) {
                                    var data = '<option value="' + C_Insert_Data[i].id + '">' + C_Insert_Data[i].name + '</option>';
                                    $('.selectd2').append(data);
                                }
                                $(".selectd2").val(C_Id).trigger("change");

                                // Append New Language
                                for (let i = 0; i < L_Insert_Data.length; i++) {
                                    var data = '<option value="' + L_Insert_Data[i].id + '">' + L_Insert_Data[i].name + '</option>';
                                    $('.selectd2_1').append(data);
                                }
                                $(".selectd2_1").val(L_Id).trigger("change");

                                // Append New Cast
                                for (let i = 0; i < Cast_Insert_Data.length; i++) {
                                    var data = '<option value="' + Cast_Insert_Data[i].id + '">' + Cast_Insert_Data[i].name + '</option>';
                                    $('.selectd2_2').append(data);
                                }
                                $(".selectd2_2").val(Cast_Id).trigger("change");

                                // Image 
                                $('#preview-image-before-upload').attr('src', Poster_img);
                                $('#preview-image-before-upload1').attr('src', Poster_img);
                                $('#thumbnail_imdb').attr('value', Poster_img);
                                $('#landscape_imdb').attr('value', Poster_img);

                                // Desctiption
                                $('#description').val(Description);

                                // Year
                                $("#release_year").attr('value', Year);

                                // imdb_rating
                                $("#imdb_rating").attr('value', imdbRating);
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            $("#dvloader").hide();
                            toastr.error(errorThrown.msg, 'failed');
                        }
                    });

                }
            })
        })
        function imdb_data_fetch() {

            var id = $("#Imdb_id").val();

            if (id != "") {

                $("#dvloader").show();
                var url = "{{route('GetData', '')}}" + "/" + id;
                $.ajax({
                    type: "POST",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: id,
                    success: function(resp) {
                        $("#dvloader").hide();

                        if (resp.status == 200) {
                            var C_Id = resp.C_Id;
                            var L_Id = resp.L_Id;
                            var C_Insert_Data = resp.C_Insert_Data;
                            var L_Insert_Data = resp.L_Insert_Data;
                            var Poster_img = resp.Poster_img;
                            var Title = resp.title;
                            var Description = resp.Description;
                            var Cast_Id = resp.Cast_Id;
                            var Cast_Insert_Data = resp.Cast_Insert_Data;
                            var Year = resp.Year;
                            var imdbRating = resp.imdbRating;

                            // Append New Category
                            for (let i = 0; i < C_Insert_Data.length; i++) {
                                var data = '<option value="' + C_Insert_Data[i].id + '">' + C_Insert_Data[i].name + '</option>';
                                $('.selectd2').append(data);
                            }
                            $(".selectd2").val(C_Id).trigger("change");

                            // Append New Language
                            for (let i = 0; i < L_Insert_Data.length; i++) {
                                var data = '<option value="' + L_Insert_Data[i].id + '">' + L_Insert_Data[i].name + '</option>';
                                $('.selectd2_1').append(data);
                            }
                            $(".selectd2_1").val(L_Id).trigger("change");

                            // Append New Cast
                            for (let i = 0; i < Cast_Insert_Data.length; i++) {
                                var data = '<option value="' + Cast_Insert_Data[i].id + '">' + Cast_Insert_Data[i].name + '</option>';
                                $('.selectd2_2').append(data);
                            }
                            $(".selectd2_2").val(Cast_Id).trigger("change");

                            // Image 
                            $('#preview-image-before-upload').attr('src', Poster_img);
                            $('#preview-image-before-upload1').attr('src', Poster_img);
                            $('#thumbnail_imdb').attr('value', Poster_img);
                            $('#landscape_imdb').attr('value', Poster_img);
                            // Title
                            $('#Imdb_name').val(Title);
                            // Desctiption
                            $('#description').val(Description);
                            // Year
                            $("#release_year").attr('value', Year);
                            // imdb_rating
                            $("#imdb_rating").attr('value', imdbRating);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            } else {
                alert("Please input IMDb ID");
            }
        }
    </script>
@endsection