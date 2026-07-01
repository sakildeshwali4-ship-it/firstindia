@extends('admin.layouts.master')

@section('title', 'Channel Section')

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chennal Section</li>
                </ol>
            </div>
        </div>

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="app" role="tabpanel" aria-labelledby="app-tab">
                <div class="card custom-border-card">
                    <h5 class="card-header">Add Channel Section </h5>
                    <div class="card-body">

                        <form id="save_channel_section" name="save_channel_section" autocomplete="off">
                            @csrf
                            <div class="custom-border-card">
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label> {{__('Label.Title')}} </label>
                                            <input name="title" type="text" class="form-control" id="title" placeholder="Enter Your Title">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{__('Label.Channel')}}</label>
                                            <select class="form-control" name="channel_id" id="channel_id" style="width:100%!important;">
                                                <option value="" selected disabled> Select channel </option>
                                                @foreach ($channel as $key => $value)
                                                <option value="{{$value->id}}">{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="video_type">{{__('Label.Video Type')}}</label>
                                            <select class="form-control" name="video_type" id="video_type">
                                                <option value="" selected disabled>{{__('Label.Select Video Type')}}</option>
                                                <option value="1">{{__('Label.Video')}}</option>
                                                <option value="2">{{__('Label.Show')}}</option>
                                                <!-- <option value="3">{{__('Label.Language')}}</option>
                                <option value="4">{{__('Label.Category')}}</option> -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{__('Label.Type')}}</label>
                                            <select class="form-control" name="type_id" id="type_id" style="width:100%!important;">
                                                <option value="" selected disabled> Select Type </option>
                                                @foreach ($type as $key => $value)
                                                <option value="{{ $value->id}}">{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="screen_layout">Screen Layout</label>
                                            <select class="form-control" name="screen_layout" id="screen_layout">
                                                <option value="landscape">Landscape</option>
                                                <option value="square">Square</option>
                                                <option value="potrait">Potrait</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <div class="flex-grow-1 px-5 d-inline-flex">
                                            <div class="change mr-3" id="add_btn">
                                                <label for="">&nbsp;</label><br />
                                                <a class="btn btn-success add-more text-white" onclick="Save_Channel_Section()"> + </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group option_class_video">
                                            <label>{{__('Label.Video')}}</label>
                                            <select class="form-control selectd2" style="width:100%!important;" name="video_id[]" multiple id="video_id">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="after-add-more">
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- model -->
        <div class="modal fade" id="exampleModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Section</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="edit_channel_section11" name="edit_channel_section11" autocomplete="off">
                            @csrf
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label> {{__('Label.Title')}} </label>
                                        <input name="title" type="text" class="form-control" id="edit_title" placeholder="Enter Your Title">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="screen_layout">Screen Layout</label>
                                        <select class="form-control" name="screen_layout" id="edit_screen_layout">
                                            <option value="landscape">Landscape</option>
                                            <option value="square">Square</option>
                                            <option value="potrait">Potrait</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('Label.Channel')}}</label>
                                        <select class="form-control" name="channel_id" id="edit_channel_id" style="width:100%!important;">
                                            <option value="" selected disabled> Select channel </option>
                                            @foreach ($channel as $key => $value)
                                            <option value="{{ $value->id}}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="video_type">{{__('Label.Video Type')}}</label>
                                        <select class="form-control" name="video_type" id="edit_video_type">
                                            <option value="" selected disabled>{{__('Label.Select Video Type')}}</option>
                                            <option value="1">{{__('Label.Video')}}</option>
                                            <option value="2">{{__('Label.Show')}}</option>
                                            <!-- <option value="3">{{__('Label.Language')}}</option>
                            <option value="4">{{__('Label.Category')}}</option> -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('Label.Type')}}</label>
                                        <select class="form-control" name="type_id" id="edit_type_id" style="width:100%!important;">
                                            <option value="" selected disabled> Select Type </option>
                                            @foreach ($type as $key => $value)
                                            <option value="{{ $value->id}}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-group option_class_video">
                                        <label>{{__('Label.Video')}}</label>
                                        <select class="form-control selectd2" style="width:100%!important;" name="video_id[]" multiple id="edit_video_id">

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="edit_id" id="edit_id">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default mw-120" onclick="Edit_Channel_Section1()">Update</button>
                        <button type="button" class="btn btn-cancel mw-120" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('pagescript')
    <script>
        $("#video_id").select2();
        $(".selectd2").select2({
            placeholder: "Select Video"
        });

        $("#channel_id").change(function() {

            var Channel_Id = $(this).children("option:selected").val();
            var Video_Type = $('#video_type').find(":selected").val();
            var Type_Id = $('#type_id').find(":selected").val();

            $("#video_id").empty();
            if (Channel_Id != null && Channel_Id != "" && Video_Type != null && Video_Type != "" && Type_Id != null && Type_Id != "") {
                $.ajax({
                    type: 'get',
                    url: '{{ route("ChannelGetLangOrCat") }}',
                    data: {
                        Video_Type: Video_Type,
                        Type_Id: Type_Id,
                        Channel_Id: Channel_Id
                    },
                    success: function(resp) {
                        for (var i = 0; i < resp.result.length; i++) {
                            $('#video_id').append(`<option value="${resp.result[i].id}">${resp.result[i].name}</option>`);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            }
        });

        $("#video_type").change(function() {

            var Video_Type = $(this).children("option:selected").val();
            var Type_Id = $('#type_id').find(":selected").val();
            var Channel_Id = $('#channel_id').find(":selected").val();

            $("#video_id").empty();
            if (Channel_Id != null && Channel_Id != "" && Video_Type != null && Video_Type != "" && Type_Id != null && Type_Id != "") {
                $.ajax({
                    type: 'get',
                    url: '{{ route("ChannelGetLangOrCat") }}',
                    data: {
                        Video_Type: Video_Type,
                        Type_Id: Type_Id,
                        Channel_Id: Channel_Id
                    },
                    success: function(resp) {
                        for (var i = 0; i < resp.result.length; i++) {
                            $('#video_id').append(`<option value="${resp.result[i].id}">${resp.result[i].name}</option>`);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            }
        });

        $("#type_id").change(function() {

            var Type_Id = $(this).children("option:selected").val();
            var Video_Type = $('#video_type').find(":selected").val();
            var Channel_Id = $('#channel_id').find(":selected").val();

            if (Channel_Id != null && Channel_Id != "" && Video_Type != null && Video_Type != "" && Type_Id != null && Type_Id != "") {
                $.ajax({
                    type: 'get',
                    url: '{{ route("ChannelGetLangOrCat") }}',
                    data: {
                        Video_Type: Video_Type,
                        Type_Id: Type_Id,
                        Channel_Id: Channel_Id
                    },
                    success: function(resp) {
                        $("#video_id").empty();
                        for (var i = 0; i < resp.result.length; i++) {
                            $('#video_id').append(`<option value="${resp.result[i].id}">${resp.result[i].name}</option>`);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            }
        });

        // Get Section Data & Show
        $(document).ready(function() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route("ChannelGetSectionData") }}',
                success: function(resp) {

                    for (var i = 0; i < resp.result.length; i++) {

                        if (resp.result[i].video_type == 1) {
                            var Video_Type = "Video";
                        } else if (resp.result[i].video_type == 2) {
                            var Video_Type = "Show";
                        }

                        if (resp.result[i].screen_layout == "landscape") {
                            var Screen_Layout = "Landscape";
                        } else if (resp.result[i].screen_layout == "square") {
                            var Screen_Layout = "Square";
                        } else if (resp.result[i].screen_layout == "potrait") {
                            var Screen_Layout = "Potrait";
                        }

                        var data = '<div class="custom-border-card">' +
                            '<div class="form-row">' +
                            '<div class="col-md-2">' +
                            '<div class="form-group">' +
                            '<label> Title</label>' +
                            '<input name="title" type="text" class="form-control" id="title" value="' + resp.result[i].title + '" placeholder="Enter Your Title" readonly>' +
                            '</div>' +
                            '</div>' +
                            '<div class="col-md-2">' +
                            '<div class="form-group">' +
                            '<label>Channel</label>' +
                            '<input type="text" class="form-control" name="channel_id" id="channel_id" value="' + resp.result[i].channel.name + '" style="width:100%!important;" readonly/>' +
                            '</div>' +
                            '</div>' +
                            '<div class="col-md-2">' +
                            '<div class="form-group">' +
                            '<label for="video_type">Video Type</label>' +
                            '<input type="text" class="form-control" name="video_type" id="video_type" value="' + Video_Type + '" style="width:100%!important;" readonly/>' +
                            '</div>' +
                            '</div>' +
                            '<div class="col-md-2">' +
                            '<div class="form-group">' +
                            '<label>Type</label>' +
                            '<input type="text" class="form-control" name="type_id" id="type_id" value="' + resp.result[i].type.name + '" style="width:100%!important;" readonly/>' +
                            '</div>' +
                            '</div>' +
                            '<div class="col-md-2">' +
                            '<div class="form-group">' +
                            '<label for="screen_layout">Screen Layout</label>' +
                            '<input type="text" class="form-control" name="screen_layout" id="screen_layout" value="' + Screen_Layout + '" style="width:100%!important;" readonly/>' +
                            '</div>' +
                            '</div>' +
                            '<div class="col-md-1 mt-2">' +
                            '<div class="flex-grow-1 px-5 d-inline-flex">' +
                            '<div class="mr-3">' +
                            '<label>&nbsp;</label><br/>' +
                            '<a data-toggle="modal" data-target="#exampleModal" onclick="EditSection(' + resp.result[i].id + ')" class="btn btn-info"> <img src="{{ asset("assets/imgs/edit-black.png") }}"> </a>' +
                            '</div>' +
                            '<div class="change">' +
                            '<label>&nbsp;</label><br/>' +
                            '<a onclick="DeleteSection(' + resp.result[i].id + ')" class="btn btn-danger remove"> <img src="{{ asset("assets/imgs/trash-black.png") }}"> </a>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '<div class="form-row">' +
                            '<div class="col-md-12">' +
                            '<div class="form-group">' +
                            '<label>Video list</label><br>' +
                            // '<textarea class="form-control" id="video_list" rows="2" readonly>'+resp.result[i].video_list+'</textarea>'+
                            '<div class="pt-2 pl-2 pr-2" style="background-color: #e9ecef;border-radius: 10px;">' +
                            '<p id="video_list" class="mb-0" style="">' + resp.result[i].video_list + '<br></p>' +
                            '</div>' +
                            '</div>' +
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
        });

        // Save Section
        function Save_Channel_Section() {

            var formData = new FormData($("#save_channel_section")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("ChannelSectionSave") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message2(resp, 'save_channel_section', '{{ route("ChannelSection") }}', 1);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }

        // Delete Section 
        function DeleteSection(id) {
            $("#dvloader").show();

            var url = "{{route('deleteChannelSection', '')}}" + "/" + id;
            $.ajax({
                type: 'get',
                url: url,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message2(resp, 'save_channel_section', '{{ route("ChannelSection") }}', 2);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }

        // Edit Section
        function EditSection(id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route("ChannelSectionUpdate") }}',
                data: {
                    id: id
                },
                success: function(resp) {

                    $("#edit_video_id").empty();

                    $("#edit_title").val(function() {
                        return resp.result.title;
                    });
                    $("#edit_id").val(function() {
                        return resp.result.id;
                    });
                    $("#edit_live_url").val(function() {
                        return resp.result.live_url;
                    });
                    $('#edit_video_type').find('option[value="' + resp.result.video_type + '"]').prop('selected', true);
                    $('#edit_type_id').find('option[value="' + resp.result.type_id + '"]').prop('selected', true);
                    $('#edit_category_id').find('option[value="' + resp.result.category_id + '"]').prop('selected', true);
                    $('#edit_screen_layout').find('option[value="' + resp.result.screen_layout + '"]').prop('selected', true);
                    $('#edit_channel_id').find('option[value="' + resp.result.channel_id + '"]').prop('selected', true);

                    var data = resp.result.video_type;
                    for (var i = 0; i < resp.video.length; i++) {
                        $('#edit_video_id').append(`<option value="${resp.video[i].id}">${resp.video[i].name}</option>`);
                    }
                    if (data == 1) {
                        var value1 = resp.result.video_id;
                        var array = value1.split(",");
                        for (let k = 0; k < array.length; k++) {
                            $("#edit_video_id option").each(function() {
                                if ($(this).val() == array[k]) {
                                    $(this).attr("selected", "selected");
                                }
                            });
                        }
                    } else if (data == 2) {
                        var value4 = resp.result.tv_show_id;
                        var array = value4.split(",");
                        for (let k = 0; k < array.length; k++) {
                            $("#edit_video_id option").each(function() {
                                if ($(this).val() == array[k]) {
                                    $(this).attr("selected", "selected");
                                }
                            });
                        }
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }

        $("#edit_channel_id").change(function() {

            var Channel_Id = $(this).children("option:selected").val();
            var Video_Type = $('#edit_video_type').find(":selected").val();
            var Type_Id = $('#edit_type_id').find(":selected").val();

            $("#edit_video_id").empty();
            if (Channel_Id != null && Channel_Id != "" && Video_Type != null && Video_Type != "" && Type_Id != null && Type_Id != "") {
                $.ajax({
                    type: 'get',
                    url: '{{ route("ChannelGetLangOrCat") }}',
                    data: {
                        Video_Type: Video_Type,
                        Type_Id: Type_Id,
                        Channel_Id: Channel_Id
                    },
                    success: function(resp) {
                        for (var i = 0; i < resp.result.length; i++) {
                            $('#edit_video_id').append(`<option value="${resp.result[i].id}">${resp.result[i].name}</option>`);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            }
        });

        $("#edit_video_type").change(function() {

            var Video_Type = $(this).children("option:selected").val();
            var Type_Id = $('#edit_type_id').find(":selected").val();
            var Channel_Id = $('#edit_channel_id').find(":selected").val();

            $("#edit_video_id").empty();
            if (Channel_Id != null && Channel_Id != "" && Video_Type != null && Video_Type != "" && Type_Id != null && Type_Id != "") {
                $.ajax({
                    type: 'get',
                    url: '{{ route("ChannelGetLangOrCat") }}',
                    data: {
                        Video_Type: Video_Type,
                        Type_Id: Type_Id,
                        Channel_Id: Channel_Id
                    },
                    success: function(resp) {
                        for (var i = 0; i < resp.result.length; i++) {
                            $('#edit_video_id').append(`<option value="${resp.result[i].id}">${resp.result[i].name}</option>`);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            }
        });

        $("#edit_type_id").change(function() {

            var Type_Id = $(this).children("option:selected").val();
            var Video_Type = $('#edit_video_type').find(":selected").val();
            var Channel_Id = $('#edit_channel_id').find(":selected").val();

            $("#edit_video_id").empty();
            if (Channel_Id != null && Channel_Id != "" && Video_Type != null && Video_Type != "" && Type_Id != null && Type_Id != "") {
                $.ajax({
                    type: 'get',
                    url: '{{ route("ChannelGetLangOrCat") }}',
                    data: {
                        Video_Type: Video_Type,
                        Type_Id: Type_Id,
                        Channel_Id: Channel_Id
                    },
                    success: function(resp) {
                        for (var i = 0; i < resp.result.length; i++) {
                            $('#edit_video_id').append(`<option value="${resp.result[i].id}">${resp.result[i].name}</option>`);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        toastr.error(errorThrown.msg, 'failed');
                    }
                });
            }
        });

        function Edit_Channel_Section1() {

            $("#dvloader").show();
            var formData = new FormData($("#edit_channel_section11")[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route("ChannelSectionUpdate1") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message2(resp, 'edit_channel_section11', '{{ route("ChannelSection") }}', 3);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }
    </script>
@endsection