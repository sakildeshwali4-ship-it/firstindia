@extends('admin.layouts.master')

@section('title', 'Episodes')

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('upcomingTVShow') }}">{{__('Label.TV Show')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Episode List</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('upcomingTVShow') }}" class="btn btn-default mw-150" style="margin-top: -14px;">{{__('Label.TV Show')}}</a>
            </div>
        </div>

        <!-- Search -->
        <form class="" action="{{ route('upcomingTVShowVideo', ['id' => $tvshowId]) }}" method="GET">
            <input type="hidden" name="show_id" id="show_id" value="{{$tvshowId}}">
            <div class="page-search mb-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">
                        <img src="{{ asset('assets/imgs/search.png') }}">
                        </span>
                    </div>
                    <input type="text" name="input_search" value="@if(isset($_GET['input_search'])){{$_GET['input_search']}}@endif" class="form-control" placeholder="Search Episodes" aria-label="Search" aria-describedby="basic-addon1" />
                </div>
                <div class="sorting mr-3" style="width: 450px;">
                    <label>Sort by :</label>
                    <select class="form-control" name="input_session">
                        <option value="0" selected>All Session</option>
                        @for ($i = 0; $i < count($session); $i++) 
                            <option value="{{ $session[$i]['id'] }}" @if(isset($_GET['input_session'])){{ $_GET['input_session'] == $session[$i]['id'] ? 'selected' : ''}} @endif>
                                {{ $session[$i]['name'] }}
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
                <a href="{{ route('upcomingtvshowvideoAdd', ['id' => $tvshowId]) }}" class="add-video-btn">
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
                        <button class="btn play-btn video" data-toggle="modal" data-target="#videoModal" data-video="{{$value->video_320}}" data-image="{{$value->landscape}}">
                            <img src="{{ asset('assets/imgs/play.png') }}" alt="" />
                        </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{$value->name}}</h5>
                        <div class="dropdown dropright">
                            <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{{ asset('assets/imgs/dot.png') }}" class="dot-icon" />
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="{{ route('upcomingtvshowvideoEdit', ['show_id' => $value->show_id, 'id' => $value->id])}}">
                                    <img src="{{ asset('assets/imgs/edit.png') }}" class="dot-icon" />
                                    {{__('Label.Edit')}}
                                </a>
                                <a class="dropdown-item" href="{{ route('upcomingtvshowvideoDelete', ['id' => $value->id])}}" onclick="return confirm('Are you sure !!! You want to Delete this Episode ?')">
                                    <img src="{{ asset('assets/imgs/trash.png') }}" class="dot-icon" />
                                    {{__('Label.Delete')}}
                                </a>
                            </div>
                        </div>
                        <div class="card-details">
                            <p class="card-text">
                                <?php
                                    if (isset($value->session->name)) {
                                        echo $value->session->name;
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

            <!-- Video Modal -->
            <div class="modal fade" id="videoModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-body p-0 bg-transparent">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close">
                                <span aria-hidden="true" class="text-dark">&times;</span>
                            </button>
                            <video controls width="800" height="500" preload='none' poster="" id="theVideo" controlsList="nodownload noplaybackrate" disablepictureinpicture>
                                <source src="" type="video/mp4">
                            </video>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Video Modal -->
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