@extends('admin.layouts.master')

@section('title', 'Edit Ad')

@section('content')
    <div class="body-content">
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ads') }}">Ads</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Ad</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('ads') }}" class="btn btn-default mw-120" style="margin-top:-14px">Ads</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form id="edit_ads" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="{{ $result->id }}" name="id">
                @include('admin.ads.form', ['result' => $result])
                <div class="text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="edit_ads()">{{__('Label.UPDATE')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
<script>
    $(document).ready(function () {

        $(function () {

            function toggleMediaFields() {
                var mediaType = $('#media_type').val();

                if (mediaType === 'image') {
                    $('#image_upload_div').removeClass('d-none').show();
                    $('#video_url_div').hide();
                } else {
                    $('#video_url_div').removeClass('d-none').show();
                    $('#image_upload_div').hide();
                }
            }

            // Page load
            toggleMediaFields();

            // On change
            $('#media_type').on('change', function () {
                toggleMediaFields();
            });

        });

    });
</script>
    <script>
        function edit_ads() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
                var formData = new FormData($("#edit_ads")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("adsUpdate") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'edit_ads', '{{ route("ads") }}');
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
