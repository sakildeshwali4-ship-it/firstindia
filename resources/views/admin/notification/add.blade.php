@extends('admin.layouts.master')

@section('title', __('Label.Add Notification'))

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-0">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('Label.Add Notification')}}</li>
                </ol>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form enctype="multipart/form-data" id="save_notification_send" autocomplete="off">
                @csrf
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{__('Label.TITLE')}}</label>
                            <input name="title" type="text" class="form-control" placeholder="{{__('Label.Enter Title')}}" >
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12">
                        <label>{{__('Label.MESSAGE')}}</label>
                        <textarea class="form-control" rows="5" name="message" placeholder="{{__('Label.Start Write Here...')}}"></textarea>
                    </div>
                </div>
                <div class="form-row mt-3" style="display:none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{__('Label.IMAGE (OPTIONAL)')}}</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="custom-file ml-5">
                                <img src="{{asset('assets/imgs/no_img.png')}}" style="height: 120px; width: 120px;" class="img-thumbnail" id="preview-image-before-upload">
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_notification_send()">{{__('Label.SAVE')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        function save_notification_send() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){

                var formData = new FormData($("#save_notification_send")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("notificationSave") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_notification_send', '{{ route("notification") }}');
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