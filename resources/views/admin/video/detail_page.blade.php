@extends('admin.layouts.master')

@section('title', 'video Details')

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('video') }}">Video</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('video') }}" class="btn btn-default mw-150" style="margin-top: -14px;">Video List</a>
            </div>
        </div>

        <div class="card custom-border-card">
            <table class="table table-striped table-hover table-bordered w-75 text-center" style="margin-left:auto; margin-right:auto">

                <thead>
                    <tr class="table-info">
                        <th colspan="2"> Video Details </th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Name</td>
                        <td>{{$result->name}}</td>
                    </tr>
                    <tr>
                        <td>Channel</td>
                        <td>{{ isset($result->name) ? $result->name : "-" }}</td>
                    </tr>
                    <tr>
                        <td>Category</td>
                        <td>
                            @foreach ($category as $key => $value)
                            {{ $value->name .","}}
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td>Language</td>
                        <td>
                            @foreach ($language as $key => $value)
                            {{ $value->name .","}}
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td>Cast</td>
                        <td>
                            @foreach ($cast as $key => $value)
                            {{ $value->name ." ("}}{{$value->type ."),"}}
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td>Image (Thumbnail)</td>
                        <td><img src="{{$result->thumbnail}}" style="height:80px; width:70px" class="img-thumbnail"></td>
                    </tr>
                    <tr>
                        <td>Image (Landscape)</td>
                        <td><img src="{{$result->landscape}}" style="height:60px; width:80px" class="img-thumbnail"></td>
                    </tr>
                    <tr>
                        <td>View</td>
                        <td>{{$result->view}}</td>
                    </tr>
                    <tr>
                        <td>Is Downloads</td>
                        <td>
                            @if($result->download == 0)
                                No
                            @else 
                                Yes
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Description</td>
                        <td>
                            @if($result->description)
                                {{$result->description}}                            
                            @else 
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Is Title</td>
                        <td>
                            @if($result->is_title == 0)
                                No
                            @else 
                                Yes
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Video Type</td>
                        <td>
                            @if($result->video_type == 1)
                                Video
                            @elseif($result->video_type == 2)
                                Show
                            @elseif($result->video_type == 5)
                                Upcoming
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Is Premium</td>
                        <td>
                            @if($result->is_premium == 0)
                                No
                            @else 
                                Yes
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Release Date</td>
                        <td>
                            @if($result->release_date)
                                {{$result->release_date}}
                            @else 
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Trailer URl</td>
                        <td>
                            @if($result->trailer_type == "server_video" && $result->trailer_url != null)
                                {{ Get_Video('video', $result->trailer_url)}}
                            @elseif($result->trailer_url != null)
                                {{$result->trailer_url}} 
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Release Year</td>
                        <td>
                            @if($result->release_year)
                                {{$result->release_year}}
                            @else 
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Video Upload Type</td>
                        <td>
                            @if($result->video_upload_type)
                                {{$result->video_upload_type}}
                            @else 
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Video URL (320 px)</td>
                        <td>
                            @if($result->video_upload_type == "server_video" && $result->video_320 != null)
                                {{ Get_Video('video', $result->video_320)}}
                            @elseif($result->video_320 != null)
                                {{$result->video_320}} 
                            @else
                                -                           
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Video URL (480 px)</td>
                        <td>
                            @if($result->video_upload_type == "server_video" && $result->video_480 != null)
                                {{ Get_Video('video', $result->video_480)}}
                            @elseif ($result->video_480 != null)
                                {{$result->video_480}}
                            @else
                                -                   
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Video URL (720 px)</td>
                        <td>
                            @if($result->video_upload_type == "server_video" && $result->video_720 != null)
                                {{ Get_Video('video', $result->video_720)}}
                            @elseif ($result->video_720 != null)
                                {{$result->video_480}}         
                            @else 
                                -                   
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Video URL (1080 px)</td>
                        <td>
                            @if($result->video_upload_type == "server_video" && $result->video_1080 != null)
                                {{ Get_Video('video', $result->video_1080)}}
                            @elseif($result->video_1080 != null)
                                {{$result->video_1080}}
                            @else 
                                -                          
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Subtitle Language 1</td>
                        <td>
                            @if($result->subtitle_lang_1)
                                {{$result->subtitle_lang_1}}
                            @else 
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Subtitle 1</td>
                        <td>
                            @if($result->subtitle_type == "server_video" && $result->subtitle_1 != null)
                                {{ Get_Video('video', $result->subtitle_1)}}
                            @elseif($result->subtitle_1 != null)
                                {{$result->subtitle_1}}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Subtitle Language 2</td>
                        <td>
                            @if($result->subtitle_lang_2)
                                {{$result->subtitle_lang_2}}
                            @else 
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Subtitle 2</td>
                        <td>
                            @if($result->subtitle_type == "server_video" && $result->subtitle_2 != null)
                                {{ Get_Video('video', $result->subtitle_2)}}
                            @elseif($result->subtitle_2 != null)
                                {{$result->subtitle_2}}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Subtitle Language 3</td>
                        <td>
                            @if($result->subtitle_lang_3)
                                {{$result->subtitle_lang_3}}
                            @else 
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Subtitle 3</td>
                        <td>
                            @if($result->subtitle_type == "server_video" && $result->subtitle_3 != null)
                                {{ Get_Video('video', $result->subtitle_3)}}
                            @elseif($result->subtitle_3 != null)
                                {{$result->subtitle_3}}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Video Extension</td>
                        <td>
                            @if($result->video_extension)
                                {{$result->video_extension}}
                            @else 
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Video Duration</td>
                        <td>
                            @if($result->video_duration)
                                {{MillisecondsToTime($result->video_duration)}}
                            @else 
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>IMDB Rating</td>
                        <td>
                            @if($result->imdb_rating)
                                {{$result->imdb_rating}}
                            @else 
                                0
                            @endif
                        </td>
                    </tr>            
                </tbody>
            </table>
        </div>
        
    </div>
@endsection