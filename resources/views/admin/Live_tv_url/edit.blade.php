@extends('admin.layouts.master')

@section('title', 'Edit E-News Paper')

@section('content')
<div class="body-content">

    <h1 class="page-title-sm">@yield('title')</h1>

    <div class="row">
        <div class="col-sm-10">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('livetv.index') }}">Live Tv Url</a>
                </li>
                <li class="breadcrumb-item active">Edit Live Tv Url</li>
            </ol>
        </div>

        <div class="col-sm-2 d-flex align-items-center justify-content-end">
            <a href="{{ route('livetv.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">
                Live Tv List
            </a>
        </div>
    </div>

    <div class="card custom-border-card mt-3">
    <form id="update_livetv" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <input type="hidden" name="id" value="{{ $data->id }}">

        <!-- Name and URL Row -->
        <div class="form-row">
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label>Name</label>
                    <input name="name" type="text" class="form-control" value="{{ $data->name ?? '' }}" required>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label>URL</label>
                    <input name="url" type="text" class="form-control" value="{{ $data->url ?? '' }}" required>
                </div>
            </div>
        </div>

        <!-- Image and Dialog Image Row -->
        <div class="form-row">
            <!-- Main Image -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <small class="text-gray">Best Resolution: 600×400</small>
                    <div class="mt-2">
                        <img src="{{ $data->image ? $data->image : asset('assets/imgs/no_img.png') }}"
                             style="height: 120px; width: 120px;" class="img-thumbnail" id="preview-image">
                    </div>
                </div>
            </div>

            <!-- Dialog Image -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label>Dialog Image</label>
                    <input type="file" class="form-control" id="dialog_image" name="dialog_image" accept="image/*">
                    <small class="text-gray">Leave empty to keep old image</small>
                    <div class="mt-2">
                        <img src="{{ $data->dialog_image ? $data->dialog_image : asset('assets/imgs/no_img.png') }}"
                             style="height: 120px; width: 120px;" class="img-thumbnail" id="preview-dialog-image">
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Button -->
        <div class="text-right mt-3">
            <button type="button" class="btn btn-default mw-120" onclick="update_livetv()">UPDATE</button>
        </div>
    </form>
</div>
</div>
@endsection

@section('pagescript')
<script>

function update_livetv() {
    var formData = new FormData($("#update_livetv")[0]);

    $("#dvloader").show();

    $.ajax({
        type: 'POST',
        url: '{{ route("livetv.update", $data->id) }}',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,

        success: function(resp) {
            $("#dvloader").hide();
            get_responce_message(resp, 'update_livetv', '{{ route("livetv.index") }}');
        },

        error: function() {
            $("#dvloader").hide();
            toastr.error("Something went wrong!");
        }
    });
}

// Live Image Preview
$('#highlight_image').change(function(){
    let reader = new FileReader();
    reader.onload = (e) => {
        $('#preview-image-before-upload').attr('src', e.target.result);
    }
    reader.readAsDataURL(this.files[0]);
});

// Auto-open date calendar
$('input[type="date"]').focus(function () {
    this.showPicker();
});

$('#image').change(function(){
    let reader = new FileReader();
    reader.onload = (e) => {
        $('#preview-image').attr('src', e.target.result);
    }
    reader.readAsDataURL(this.files[0]);
});

$('#dialog_image').change(function(){
    let reader = new FileReader();
    reader.onload = (e) => {
        $('#preview-dialog-image').attr('src', e.target.result);
    }
    reader.readAsDataURL(this.files[0]);
});
</script>
@endsection
