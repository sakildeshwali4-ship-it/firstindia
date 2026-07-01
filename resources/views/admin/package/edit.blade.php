@extends('admin.layouts.master')

@section('title', __('Label.Edit Package'))

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
                    {{__('Label.Edit Package')}}
                </li>
            </ol>
        </div>
        <div class="col-sm-2 d-flex align-items-center justify-content-end">
            <a href="{{ route('package') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('Label.Package List')}}</a>
        </div>
    </div>

    <div class="card custom-border-card mt-3">
        <form enctype="multipart/form-data" id="save_edit_package" autocomplete="off">
            @csrf
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('Label.NAME')}}</label>
                        <input name="name" type="text" class="form-control" placeholder="{{__('Label.Enter Package Name')}}" value="{{$result->name}}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('Label.Price')}}</label>
                        <input name="price" type="number" class="form-control" placeholder="{{__('Label.Enter Package Price')}}" value="{{$result->price}}">
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('Label.Type')}}</label>
                        <?php $y = explode(",", $result->type_id); ?>
                        <select class="form-control selectd2" name="type_id[]" style="width:100%!important;" multiple id="type_id">
                            @foreach ($type as $key => $value)
                            <option value="{{ $value->id}}" {{(in_array($value->id, $y)) ? 'selected' : ''}}>
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
                            <option value="0" {{$result->watch_on_laptop_tv == 0  ? 'selected' : ''}}>{{__('Label.No')}}</option>
                            <option value="1" {{$result->watch_on_laptop_tv == 1  ? 'selected' : ''}}>{{__('Label.Yes')}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('Label.Ads Free Movies and Shows')}}</label>
                        <select class="form-control" id="ads_free_movies_shows" name="ads_free_movies_shows">
                            <option value="0" {{$result->ads_free_movies_shows == 0  ? 'selected' : ''}}>{{__('Label.No')}}</option>
                            <option value="1" {{$result->ads_free_movies_shows == 1  ? 'selected' : ''}}>{{__('Label.Yes')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('Label.Number of Devices that can be Logged In')}}</label>
                        <select class="form-control" id="no_of_device" name="no_of_device">
                            <option value="2" {{$result->no_of_device == 2  ? 'selected' : ''}}>2</option>
                            <option value="3" {{$result->no_of_device == 3  ? 'selected' : ''}}>3</option>
                            <option value="4" {{$result->no_of_device == 4  ? 'selected' : ''}}>4</option>
                            <option value="5" {{$result->no_of_device == 5  ? 'selected' : ''}}>5</option>
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
                            <option value="Day" {{$result->type == 'Day' ? 'selected' : ''}}>Day</option>
                            <option value="Week" {{$result->type == 'Week' ? 'selected' : ''}}>Week</option>
                            <option value="Month" {{$result->type == 'Month' ? 'selected' : ''}}>Month</option>
                            <option value="Year" {{$result->type == 'Year' ? 'selected' : ''}}>Year</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 mb-3 mt-5">
                    <div class="form-group">
                        <select class="form-control time" id="time" name="time">
                            <option value="">{{__('Label.Select Number')}}</option>
                            @for($i=1; $i<=31; $i++) <option value="{{$i}}" {{$result->time == $i ? 'selected' : ''}}>{{$i}}</option>
                                @endfor
                        </select>
                    </div>
                </div>
                <div class="col-md-6 mb-3 mt-3">
                    <div class="form-group">
                        <label>{{__('Label.Video Quality')}}</label>
                        <select class="form-control" id="video_qulity" name="video_qulity">
                            <option value="480p" {{$result->video_qulity == "480p"  ? 'selected' : ''}}>480p</option>
                            <option value="720p" {{$result->video_qulity == "720p"  ? 'selected' : ''}}>720p</option>
                            <option value="1080p" {{$result->video_qulity == "1080p"  ? 'selected' : ''}}>1080p</option>
                            <option value="1440p" {{$result->video_qulity == "1440p"  ? 'selected' : ''}}>1440p</option>
                            <option value="2160p" {{$result->video_qulity == "2160p"  ? 'selected' : ''}}>2160p</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Android Product Package</label>
                        <input name="android_product_package" type="text" class="form-control" placeholder="Enter Android Product Package" value="{{$result->android_product_package}}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>IOS Product Package</label>
                        <input name="ios_product_package" type="text" class="form-control" placeholder="Enter IOS Product Package" value="{{$result->ios_product_package}}">
                    </div>
                </div>
            </div>
            <div class="text-right">
                <input type="hidden" value="{{$result->id}}" name="id">
                <button type="button" class="btn btn-default mw-120" onclick="save_edit_package()">{{__('Label.UPDATE')}}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    function save_edit_package() {
        var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
        if(Check_Admin == 1){

            var formData = new FormData($("#save_edit_package")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("packageUpdate") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'save_edit_package', '{{ route("package") }}');
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

        var validity_type = "<?php echo $result->type; ?>";
        if (validity_type == "Day") {
            for (let i = 8; i <= 31; i++) {
                $(".time option[value=" + i + "]").hide();
            }
        } else if (validity_type == "Week") {
            for (let i = 5; i <= 31; i++) {
                $(".time option[value=" + i + "]").hide();
            }
        } else if (validity_type == "Month") {
            for (let i = 13; i <= 31; i++) {
                $(".time option[value=" + i + "]").hide();
            }
        } else if (validity_type == "Year") {
            for (let i = 2; i <= 31; i++) {
                $(".time option[value=" + i + "]").hide();
            }
        } else {
            $('.time').hide();
        }
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