@extends('admin.layouts.master')

@section('title', __('Label.Videos'))

@section('content')
<style>.card-title { color:#fff; }</style>
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb mb-3">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('Label.Videos')}}</li>
                </ol>
            </div>
        </div>

        <!-- Search -->
        <form action="{{ route('video')}}" method="GET">
            <div class="page-search mb-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">
                        <img src="{{ asset('assets/imgs/search.png') }}">
                        </span>
                    </div>
                    <input type="text" name="input_search" value="@if(isset($_GET['input_search'])){{$_GET['input_search']}}@endif" class="form-control" placeholder="Search Videos" aria-label="Search" aria-describedby="basic-addon1">
                </div>
                <div class="sorting mr-3" style="width: 450px;">
                    <label>Sort by :</label>
                    <select class="form-control" name="input_type">
                        <option value="0" selected>All Type</option>
                        @for ($i = 0; $i < count($type); $i++) 
                            <option value="{{ $type[$i]['id'] }}" @if(isset($_GET['input_type'])){{ $_GET['input_type'] == $type[$i]['id'] ? 'selected' : ''}} @endif>
                                {{ $type[$i]['name'] }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="mr-3 ml-5">
                    <button class="btn btn-default" type="submit"> {{__('Label.SEARCH')}} </button>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <a href="{{ route('videoAdd') }}" class="add-video-btn">
                    <img src="{{ asset('assets/imgs/add.png') }}" alt="" class="icon" />
                    {{__('Label.Add new video')}}
                </a>
            </div>

            @foreach ($result as $key => $value)
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card video-card">
                    <div class="position-relative">

                        @if($value->is_premium == 1)
                            <div class="ribbon ribbon-top-left"><span>Premium</span></div>
                        @endif

                        <img class="card-img-top" src="{{$value->thumbnail}}" alt="">
                        @if($value->video_upload_type == "server_video")
                        <button class="btn play-btn video" data-toggle="modal" data-target="#videoModal" data-video="{{$value->video_320}}" data-image="{{$value->thumbnail}}">
                            <img src="{{ asset('assets/imgs/play.png') }}" alt="" />
                        </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <h5 class="card-title mr-5">{{$value->name}}</h5>
                        <div class="dropdown dropright">
                            <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis fa-2xl dot-icon"></i>
                                <!-- <img src="{{ asset('assets/imgs/dot.png') }}" class="" /> -->
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="{{ route('videoDetail', ['id' => $value->id])}}">
                                <i class="fa-solid fa-eye fa-2xl dot-icon"></i>
                                    <!-- <img src="{{ asset('assets/imgs/eye.png') }}" class="dot-icon" /> -->
                                    {{__('Label.Details')}}
                                </a>
                                <a class="dropdown-item" href="{{ route('editVideo', ['id' => $value->id.'_'.$pageNo])}}">
                                <i class="fa-regular fa-pen-to-square fa-2xl dot-icon"></i>
                              
                                    <!-- <img src="{{ asset('assets/imgs/edit.png') }}" class="dot-icon" /> -->
                                    {{__('Label.Edit')}}
                                </a>
                                <a class="dropdown-item" href="{{ route('deleteVideo', ['id' => $value->id])}}" onclick="return confirm('Are you sure !!! You want to Delete this Video ?')">
                                <i class="fa-solid fa-trash-can fa-2xl dot-icon"></i>
                              
                                <!-- <img src="{{ asset('assets/imgs/trash.png') }}" class="dot-icon" /> -->
                                    {{__('Label.Delete')}}
                                </a>
                            </div>
                        </div>
                        <div class="card-details">
                            <p class="card-text">
                                <?php
                                    if (isset($value->type->name)) {
                                        echo $value->type->name;
                                    } else {
                                        echo '';
                                    }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="modal fade" id="videoModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-body p-0 bg-transparent">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="text-dark">&times;</span>
                            </button>
                            <video controls width="800" height="500" preload='none' poster="" id="theVideo" controlsList="nodownload noplaybackrate" disablepictureinpicture>
                                <source src="" type="video/mp4">
                            </video>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center">
            <div> Showing {{ $result->firstItem() }} to {{ $result->lastItem() }} of total {{$result->total()}} entries </div>
            <div class="pb-5"> {{ $result->links() }} </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        $(function() {
            $(".video").click(function() {
                var theModal = $(this).data("target"),
                    videoSRC = $(this).attr("data-video"),
                    videoPoster = $(this).attr("data-image"),
                    videoSRCauto = videoSRC + "";

                $(theModal + ' source').attr('src', videoSRCauto);
                $(theModal + ' video').attr('poster', videoPoster);
                $(theModal + ' video').load();
                $(theModal + ' button.close').click(function() {
                    $(theModal + ' source').attr('src', videoSRC);
                });
            });
        });

        $("#videoModal .close").click(function() {
            theVideo.pause()
        });
    </script>
@endsection