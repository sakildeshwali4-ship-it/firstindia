@extends('admin.layouts.master')

@section('title', 'Add Ad')

@section('content')
    <div class="body-content">
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ads') }}">Ads</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Ad</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('ads') }}" class="btn btn-default mw-120" style="margin-top:-14px">Ads</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form id="save_ads" autocomplete="off" enctype="multipart/form-data">
                @csrf
                @include('admin.ads.form', ['result' => null])
                <div class="border-top pt-3 text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="save_ads()">{{__('Label.SAVE')}}</button>
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
        function save_ads() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
                var formData = new FormData($("#save_ads")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("adsSave") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_ads', '{{ route("ads") }}');
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
