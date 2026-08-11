@extends('admin.layouts.master')

@section('title', 'Assign Ad')

@section('content')
    <div class="body-content">
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('adAssignments') }}">Ad Assignments</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Assign Ad</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('adAssignments') }}" class="btn btn-default mw-120" style="margin-top:-14px">Assignments</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form id="save_ad_assignment" autocomplete="off">
                @csrf
                @include('admin.ad_assignments.form', ['result' => null])
                <div class="border-top pt-3 text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_ad_assignment()">{{__('Label.SAVE')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    @include('admin.ad_assignments.form_script', ['result' => null])
    <script>
        function save_ad_assignment() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
                var formData = new FormData($("#save_ad_assignment")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("adAssignmentsSave") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_ad_assignment', '{{ route("adAssignments") }}');
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
