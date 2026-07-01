@extends('admin.layouts.master')

@section('title', __('Label.Notification Setting'))

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('Label.Notification Setting')}}</li>
                </ol>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form id="save_notificationsetting">
                @csrf
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>{{__('Label.ONESIGNAL APP ID')}}</label>
                            <input name="onesignal_apid" type="text" class="form-control" value="{{$result['onesignal_apid']}}" placeholder="ENTER ONESIGNAL APP ID" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>{{__('Label.ONESIGNAL REST KEY')}}</label>
                            <input name="onesignal_rest_key" type="text" class="form-control" value="{{$result['onesignal_rest_key']}}" placeholder="ENTER ONESIGNAL REST KEY" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_notificationsetting()">{{__('Label.SAVE')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        function save_notificationsetting() {

            var formData = new FormData($("#save_notificationsetting")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("notificationSettingsave") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    get_responce_message(resp, 'save_notificationsetting', '{{ route("notification") }}');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }
    </script>
@endsection