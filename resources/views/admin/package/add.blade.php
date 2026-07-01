@extends('admin.layouts.master')

@section('title', __('Label.Add Package'))

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-2">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('package') }}">{{__('Label.Package')}}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{__('Label.Add Package')}}
                    </li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('package') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('Label.Package')}}</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form enctype="multipart/form-data" id="save_package" autocomplete="off">
                @csrf
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{__('Label.NAME')}}</label>
                            <input name="name" type="text" class="form-control" placeholder="{{__('Label.Enter Package Name')}}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{__('Label.Price')}}</label>
                            <input name="price" type="number" class="form-control" placeholder="{{__('Label.Enter Package Price')}}" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{__('Label.Type')}}</label>
                            <select class="form-control selectd2" name="type_id[]" style="width:100%!important;" multiple id="type_id">
                                @foreach ($type as $key => $value)
                                <option value="{{ $value->id}}">
                                    {{ $value->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{__('Label.Watch On Laptop Or TV')}}</label>
                            <select class="form-control" id="watch_on_laptop_tv" name="watch_on_laptop_tv">
                                <option value="0">{{__('Label.No')}}</option>
                                <option value="1">{{__('Label.Yes')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{__('Label.Ads Free Movies and Shows')}}</label>
                            <select class="form-control" id="ads_free_movies_shows" name="ads_free_movies_shows">
                                <option value="0">{{__('Label.No')}}</option>
                                <option value="1">{{__('Label.Yes')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{__('Label.Number of Devices that can be Logged In')}}</label>
                            <select class="form-control" id="no_of_device" name="no_of_device">
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-3 mb-3 mt-3">
                        <div class="form-group">
                            <label>Package Time</label>
                            <select class="form-control" id="validity_type" name="type">
                                <option value="">{{__('Label.Select Type')}}</option>
                                <option value="Day">Day</option>
                                <option value="Week">Week</option>
                                <option value="Month">Month</option>
                                <option value="Year">Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="form-group">
                            <select class="form-control time mt-5" id="time" name="time">
                                <option value="">{{__('Label.Select Number')}}</option>
                                @for($i=1; $i<=31; $i++) <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 mt-3">
                        <div class="form-group">
                            <label>{{__('Label.Video Quality')}}</label>
                            <select class="form-control" id="video_qulity" name="video_qulity">
                                <option value="480p">480p</option>
                                <option value="720p">720p</option>
                                <option value="1080p">1080p</option>
                                <option value="1440p">1440p</option>
                                <option value="2160p">2160p</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Android Product Package</label>
                            <input name="android_product_package" type="text" class="form-control" placeholder="Enter Android Product Package">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>IOS Product Package</label>
                            <input name="ios_product_package" type="text" class="form-control" placeholder="Enter IOS Product Package">
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_package()">{{__('Label.SAVE')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        function save_package() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){

                var formData = new FormData($("#save_package")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("packageSave") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_package', '{{ route("package") }}');
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
            $("#type_id").select2();
            $(".selectd2").select2({
                placeholder: "{{__('Label.Select Type')}}"
            });

            $('.time').hide();
        });

        $('#validity_type').on('click', function() {
            $('.time').show();
            var type = $("#validity_type").val()

            for (let i = 1; i <= 31; i++) {
                $(".time option[value=" + i + "]").show();
                $(".time option[value=" + i + "]").attr("selected", false);
            }

            if (type == "Day") {
                for (let i = 8; i <= 31; i++) {
                    $(".time option[value=" + i + "]").hide();
                }
            } else if (type == "Week") {
                for (let i = 5; i <= 31; i++) {
                    $(".time option[value=" + i + "]").hide();
                }
            } else if (type == "Month") {
                for (let i = 13; i <= 31; i++) {
                    $(".time option[value=" + i + "]").hide();
                }
            } else if (type == "Year") {
                for (let i = 2; i <= 31; i++) {
                    $(".time option[value=" + i + "]").hide();
                }
            } else {
                $('.time').hide();
            }
        })
    </script>
@endsection