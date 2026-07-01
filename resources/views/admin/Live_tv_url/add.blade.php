@extends('admin.layouts.master')

@section('title', 'Add E-News Paper')

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
                <li class="breadcrumb-item active">Add Live Tv Url</li>
            </ol>
        </div>

        <div class="col-sm-2 d-flex align-items-center justify-content-end">
            <a href="{{ route('livetv.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">
                Live Tv List
            </a>
        </div>
    </div>

    <div class="card custom-border-card mt-3">
        <form id="save_enews" enctype="multipart/form-data" autocomplete="off">
            @csrf

            <div class="form-row">
                <!-- Date -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Name</label>
                        <input name="name" type="text" class="form-control" required >
                    </div>
                </div>

                 <div class="col-md-6">
                    <label for="url" class="form-label">Stream URL</label>
                    <input type="text" name="url" class="form-control" placeholder="Enter live TV URL" required>
                </div>

            </div>

            <div class="form-row">

                <!-- Highlight Image Upload -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label> Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-gray">Best Resolution: 600×400</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label> dialog_image</label>
                        <input type="file" class="form-control" id="dialog_image" name="dialog_image" accept="image/*">
                        <!-- <small class="text-gray">Best Resolution: 600×400</small> -->
                    </div>
            </div>
                <!-- Image Preview -->
                <div class="col-md-6">
                    <div class="form-group">
                        <img src="{{ asset('assets/imgs/no_img.png') }}"
                             style="height: 120px; width: 120px;" class="img-thumbnail"
                             id="preview-image-before-upload">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <img src="{{ asset('assets/imgs/no_img.png') }}"
                             style="height: 120px; width: 120px;" class="img-thumbnail"
                             id="preview-image-before-upload-new">
                    </div>
                </div>

            </div>

            <div class="text-right">
                <button type="button" class="btn btn-default mw-120" onclick="save_enews()">
                    SAVE
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
@section('pagescript')
<script>

// Save Function
function save_enews() {
    var formData = new FormData($("#save_enews")[0]);

    $("#dvloader").show();
    $.ajax({
        type: 'POST',
        url: '{{ route("livetv.store") }}',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,

        success: function(resp) {
            $("#dvloader").hide();
            get_responce_message(resp, 'save_livetv', '{{ route("livetv.index") }}');
        },

        error: function() {
            $("#dvloader").hide();
            toastr.error("Something went wrong!");
        }
    });
}

// Image Preview
$('#image').change(function(){
    let reader = new FileReader();
    reader.onload = (e) => {
        $('#preview-image-before-upload').attr('src', e.target.result);
    }
    reader.readAsDataURL(this.files[0]);
});
$('#dialog_image').change(function(){
    let reader = new FileReader();
    reader.onload = (e) => {
        $('#preview-image-before-upload-new').attr('src', e.target.result);
    }
    reader.readAsDataURL(this.files[0]);
});

$('input[type="date"]').focus(function () {
    this.showPicker();   // Chrome, Edge, Opera
});

</script>
@endsection
