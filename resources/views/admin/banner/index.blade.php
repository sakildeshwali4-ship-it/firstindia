@extends('admin.layouts.master')

@section('title', 'Banner')

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Banner</li>
                </ol>
            </div>
        </div>

        @if(isset($type) && $type != null && count($type) > 0)
        <ul class="tabs nav nav-pills custom-tabs inline-tabs" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="app-tab" onclick="Selected_Type('{{$type[0]['id']}}','{{$type[0]['type']}}','1')" data-is_home_screen="1" href="#app" role="tab" data-toggle="tab" aria-controls="app" aria-selected="true">Home</a>
            </li>
            @for ($i = 0; $i < count($type); $i++) 
            <li class="nav-item">
                <a class="nav-link" id="{{ $type[$i]['name']}}-tab" onclick="Selected_Type('{{$type[$i]['id']}}','{{$type[$i]['type']}}','2')" data-is_home_screen="2" data-id="{{$type[$i]['id']}}" data-type="{{$type[$i]['type']}}" data-toggle="tab" href="#{{ $type[$i]['name']}}" role="tab" aria-controls="{{ $type[$i]['name']}}" aria-selected="true">{{ $type[$i]['name']}}</a>
            </li>
            @endfor
        </ul>
        @endif

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="app" role="tabpanel" aria-labelledby="app-tab">
                <div class="card custom-border-card">
                    <h5 class="card-header">Add Banner</h5>
                    @if(isset($type) && $type != null && count($type) > 0)
                    <div class="card-body">
                        <form id="save_banner" name="banner">
                            @csrf

                            <div class="form-row radio-row">
                                <div class="col-md-6 d-flex justify-content-start">
                                    @for ($i = 0; $i < count($type); $i++) 
                                    <div class="form-check form-check-inline mr-3">
                                    <label class="form-check-label h6"><input class="form-check-input radio" type="radio" name="type_id" onclick="Selected_Type('{{$type[$i]['id']}}','{{$type[$i]['type']}}','2')" data-id="{{ $type[$i]['id']}}" data-type="{{ $type[$i]['type']}}" data-name="{{$type[$i]['name']}}" id="Video_Selecte{{$i}}" value="{{ $type[$i]['id']}}" {{ $i == 0  ? 'checked' : ''}}>
                                        {{ $type[$i]['name']}}</label>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-2 mr-3 upcoming_type">
                                    <div class="form-group mt-4">
                                        <label>Upcoming Type</label>
                                        <select class="form-control" name="upcoming_type" id="upcoming_type">
                                            <option value="" selected disabled> Select Type </option>
                                            <option value="1">Video</option>
                                            <option value="2">Show</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 option_class_video">
                                    <div class="form-group mt-4">
                                        <label>{{__('Label.Video')}}</label>
                                        <select class="form-control" name="video_id" id="video_id" style="width:100%!important;">
                                            <option value="" selected disabled> Select Video </option>
                                            @foreach ($video as $key => $value)
                                            <option value="{{ $value->id}}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="after-add-more"></div>

                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        $(document).ready(function() {
            $("#video_id").select2();
            $(".video_id").select2({
                placeholder: "{{__('Label.Select Video')}}",
            });
        });

        var type = $('input[name=type_id]:checked').data('type');
        if(type != 5){
            $(".upcoming_type").hide();
        }

        function Selected_Type(type_id, type, is_home_page){

            $(".upcoming_type").hide();
            if(type == 5){
                $(".upcoming_type").show();
                $("#video_id").empty();
                $('#video_id').append(`<option value="" selected disabled> Select Video </option>`);
            }

            if(is_home_page == 1) {
                $("#Video_Selecte0").prop('checked', true);
            }

            if (type == 1 || type == 2) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{ route("BannerTypeByVideo") }}',
                    data: {type_id:type_id, type:type},
                    success: function(resp) {
                        $("#video_id").empty();
                        $('#video_id').append(`<option value="" selected disabled> Select Video </option>`);          
                        for (var i = 0; i < resp.result.length; i++) {
                            $('#video_id').append(`<option value="${resp.result[i].id}">${resp.result[i].name}</option>`);          
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            } else if (type == 5) {

                var upcoming_type = $('#upcoming_type').find(":selected").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{ route("BannerTypeByVideo") }}',
                    data: {type_id:type_id, type:type, upcoming_type:upcoming_type},
                    success: function(resp) {
                        $("#video_id").empty();
                        $('#video_id').append(`<option value="" selected disabled> Select Video </option>`);          
                        for (var i = 0; i < resp.result.length; i++) {
                            $('#video_id').append(`<option value="${resp.result[i].id}">${resp.result[i].name}</option>`);          
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            }
        };

        // Upcoming Type
        $('#upcoming_type').on('change', function () {

            $("#video_id").empty();
            $('#video_id').append(`<option value="" selected disabled> Select Video </option>`);

            var Tab = $("ul.tabs li a.active");
            var Is_home_screen = Tab.data("is_home_screen");
            var upcoming_type = $(this).children("option:selected").val();

            if (Is_home_screen == 1) {

                var type_id = $('input[name=type_id]:checked').data('id');
                var type = $('input[name=type_id]:checked').data('type');
            } else if (Is_home_screen == 2) {

                var type_id = Tab.data("id");
                var type = Tab.data("type");
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route("BannerTypeByVideo") }}',
                data: {
                    type_id:type_id,
                    type:type,
                    upcoming_type:upcoming_type,
                },
                success: function(resp) {
                    $("#video_id").empty();
                    $('#video_id').append(`<option value="" selected disabled> Select Video </option>`);
                    for (var i = 0; i < resp.result.length; i++) {
                        $('#video_id').append(`<option value="${resp.result[i].id}">${resp.result[i].name}</option>`);          
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        });

        // Save
        $('#video_id').on('change', function () {

            var Tab = $("ul.tabs li a.active");
            var Is_home_screen = Tab.data("is_home_screen");

            var Video_Id = $(this).children("option:selected").val();

            if(Is_home_screen == 1){

                var Type_Id = $('input[name=type_id]:checked').val();
                var Video_Type = $('input[name=type_id]:checked').data('type');
                var Upcoming_Type = $('#upcoming_type').find(":selected").val();

            } else {

                var Type_Id = Tab.data("id");
                var Video_Type = Tab.data("type");
                var Upcoming_Type = $('#upcoming_type').find(":selected").val();
            }  

            $("#dvloader").show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route("BannerSave") }}',
                data: {
                    is_home_screen:Is_home_screen,
                    type_id:Type_Id,
                    video_type: Video_Type,
                    video_id:Video_Id,
                    upcoming_type:Upcoming_Type,
                },
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message2(resp, 'save_banner',"",1);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        });

        // Banner List
        var Tab = $("ul.tabs li a.active");
        var Is_home_screen = Tab.data("is_home_screen");
        if(Is_home_screen == 1){

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                data: {is_home_screen:Is_home_screen},
                url: '{{ route("BannerList") }}',
                success: function(resp) {
                    for (var i = 0; i < resp.result.length; i++) {
                        var data ='<div class="form-group row">' +
                                    '<div class="col-md-2">' +
                                        '<label>Type</label>' +
                                        '<input type="text" class="form-control" name="type" value="'+ resp.result[i].type.name +'" placeholder="Dropdown" readonly/>' +
                                        '<input type="hidden" class="form-control" name="video_type" value=""/>' +
                                    '</div>' +
                                    '<div class="col-md-4">' +
                                        '<label>Video</label>' +
                                        '<input type="text" class="form-control" name="video" value="'+ resp.result[i].video.name +'" id="video" placeholder="Dropdown" readonly/>' +
                                    '</div>' +
                                    '<div class="col-md-1 flex-grow-1 px-3">' +
                                        '<div class="change">' +
                                        '<label>&nbsp;</label><br/><a onclick="DeleteBanner('+ resp.result[i].id +')" class="btn btn-danger remove" id="remove"> <i class="fa-solid text-white fa-trash-can"></i> </a>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>';
                        $('.after-add-more').append(data);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }
        $('.nav-item a').on('click', function() {

            var Is_home_screen = $(this).data("is_home_screen");
            $(".after-add-more .row").remove();
            if(Is_home_screen == 2){

                $('.radio-row').hide();
                var type_id = $(this).data("id");

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    data: {type_id:type_id, is_home_screen:Is_home_screen},
                    url: '{{ route("BannerList") }}',
                    success: function(resp) {
                        for (var i = 0; i < resp.result.length; i++) {
                            var data ='<div class="form-group row">' +
                                        '<div class="col-md-2">' +
                                        '<label>Type</label>' +
                                        '<input type="text" class="form-control" name="type" value="'+ resp.result[i].type.name +'" placeholder="Dropdown" readonly/>' +
                                        '<input type="hidden" class="form-control" name="video_type" value=""/>' +
                                        '</div>' +
                                        '<div class="col-md-4">' +
                                        '<label>Video</label>' +
                                        '<input type="text" class="form-control" name="video" value="'+ resp.result[i].video['name'] +'" id="video" placeholder="Dropdown" readonly/>' +
                                        '</div>' +
                                        '<div class="col-md-1 flex-grow-1 px-3">' +
                                        '<div class="change">' +
                                            '<label for="">&nbsp;</label><br/><a onclick="DeleteBanner('+ resp.result[i].id +')" class="btn btn-danger remove" id="remove"> <i class="fa-solid text-white fa-trash-can"></i> </a>' +
                                        '</div>' +
                                        '</div>' +
                                    '</div>';
                            $('.after-add-more').append(data);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            } else {
                $('.radio-row').show();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    data: {is_home_screen:Is_home_screen},
                    url: '{{ route("BannerList") }}',
                    success: function(resp) {
                        for (var i = 0; i < resp.result.length; i++) {
                            var data ='<div class="form-group row">' +
                                        '<div class="col-md-2">' +
                                        '<label>Type</label>' +
                                        '<input type="text" class="form-control" name="type" value="'+ resp.result[i].type.name +'" placeholder="Dropdown" readonly/>' +
                                        '<input type="hidden" class="form-control" name="video_type" value=""/>' +
                                        '</div>' +
                                        '<div class="col-md-4">' +
                                        '<label>Video</label>' +
                                        '<input type="text" class="form-control" name="video" value="'+ resp.result[i].video.name +'" id="video" placeholder="Dropdown" readonly/>' +
                                        '</div>' +
                                        '<div class="col-md-1 flex-grow-1 px-3">' +
                                        '<div class="change">' +
                                            '<label for="">&nbsp;</label><br/><a onclick="DeleteBanner('+ resp.result[i].id +')" class="btn btn-danger remove" id="remove"> <i class="fa-solid text-white fa-trash-can"></i> </a>' +
                                        '</div>' +
                                        '</div>' +
                                    '</div>';
                            $('.after-add-more').append(data);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            };
        });

        // Delete Banner
        function DeleteBanner(id) {
            var url = "{{route('deleteBanner', '')}}"+"/"+id;

            $("#dvloader").show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url:url,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message2(resp, 'save_banner', '{{ route("Banner") }}', 2);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }
    </script>
@endsection