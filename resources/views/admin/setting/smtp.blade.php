@extends('admin.layouts.master')

@section('title', __('Label.Settings'))

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-0">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            {{__('Label.Dashboard')}}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="{{ route('setting') }}">
                            {{__('Label.Setting')}}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{__('Label.SMTP Setting')}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <h5 class="card-header">{{__('Label.Email Setting [SMTP]')}}</h5>
            <div class="card-body">
                <form id="smtp_setting">
                    @csrf
                    <div class="row col-lg-12">
                        <div class="form-group  col-lg-6">
                            <label>{{__('Label.IS SMTP Active')}}</label>
                            <select name="status" class="form-control">
                                <option value="0" {{ $smtp->status == 0  ? 'selected' : ''}}>
                                    {{__('Label.No')}}
                                </option>
                                <option value="1" {{ $smtp->status == 1  ? 'selected' : ''}}>
                                    {{__('Label.Yes')}}
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <input type="hidden" name="smtp_id" value="1">
                            <label>{{__('Label.Host')}}</label>
                            <input type="text" name="host" class="form-control" value="@if($smtp){{$smtp->host}}@endif" placeholder="Enter Host">
                        </div>
                        <div class="form-group col-lg-6">
                            <label>{{__('Label.Port')}}</label>
                            <input type="text" name="port" class="form-control" value="@if($smtp){{$smtp->port}}@endif" placeholder="Enter Port">
                        </div>
                        <div class="form-group col-lg-6">
                            <label>{{__('Label.Protocol')}}</label>
                            <input type="text" name="protocol" class="form-control" placeholder="Enter Your protocol" value="@if($smtp){{$smtp->protocol}}@endif" placeholder="Enter Protocol">
                        </div>
                    </div>
                    <div class="row col-lg-12">
                        <div class="form-group col-lg-6">
                            <label>{{__('Label.User name')}}</label>
                            <input type="text" name="user" class="form-control" value="@if($smtp){{$smtp->user}}@endif" placeholder="Enter User Name">
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="pass">{{__('Label.Password')}}</label>
                            <input type="password" name="pass" class="form-control" value="@if($smtp){{$smtp->pass}}@endif" placeholder="Enter Password">
                            <label class="mt-1 text-gray">Recommended : Search for better result <a href="https://support.google.com/mail/answer/185833?hl=en" target="_blank" class="btn-link">Click Here</a></label>
                        </div>
                    </div>
                    <div class="row col-lg-12">
                        <div class="form-group col-lg-6">
                            <label>{{__('Label.From name')}}</label>
                            <input type="text" name="from_name" class="form-control" value="@if($smtp){{$smtp->from_name}}@endif" placeholder="Enter Form Name">
                        </div>
                        <div class="form-group col-lg-6">
                            <label>{{__('Label.From Email')}}</label>
                            <input type="text" name="from_email" class="form-control" value="@if($smtp){{$smtp->from_email}}@endif" placeholder="Enter From Email">
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="smtp_setting()">{{__('Label.SAVE')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        function smtp_setting() {
            var formData = new FormData($("#smtp_setting")[0]);
            $.ajax({
                type: 'POST',
                url: '{{ route("settingsmtp") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    get_responce_message(resp, 'smtp_setting', '{{ route("setting") }}');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }
    </script>
@endsection