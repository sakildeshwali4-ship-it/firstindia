@extends('admin.layouts.master')

@section('title', 'Add Option')

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
                    <a href="{{ route('enews.index') }}">Option</a>
                </li>
                <li class="breadcrumb-item active">Add Option</li>
            </ol>
        </div>

        <div class="col-sm-2 d-flex align-items-center justify-content-end">
            <a href="{{ route('enews.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">
                E-News List
            </a>
        </div>
    </div>

    <div class="card custom-border-card mt-3">
        <form id="save_option" enctype="multipart/form-data" autocomplete="off">
            @csrf

            <div class="form-row">

                <!-- Type -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control"> 
                            <option value="game">Game</option>
                            <option value="quize">Quize </option>
                            <option value="livescore">Live score </option>
                            <option value="astro">Astro</option>
                        </select>
                    </div>
                </div>

                <!-- Date -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Url</label>
                        <input name="url" type="url" class="form-control" required>
                    </div>
                </div>
            </div>


            <div class="form-row">

                <!-- Highlight Image Upload -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" class="form-control" id="highlight_image" name="image" accept="image/*">
                        <small class="text-gray">Best Resolution: 600×400</small>
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

            </div>
            
            
            <div class="form-row">

                <!-- Status -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="text-right">
                <button type="button" class="btn btn-default mw-120" onclick="save_option()">
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
function save_option() {
    var formData = new FormData($("#save_option")[0]);

    $("#dvloader").show();
    $.ajax({
        type: 'POST',
        url: '{{ route("option.store") }}',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,

        success: function(resp) {
            $("#dvloader").hide();
            get_responce_message(resp, 'save_option', '{{ route("option.index") }}');
        },

        error: function() {
            $("#dvloader").hide();
            toastr.error("Something went wrong!");
        }
    });
}

// Image Preview
$('#highlight_image').change(function(){
    let reader = new FileReader();
    reader.onload = (e) => {
        $('#preview-image-before-upload').attr('src', e.target.result);
    }
    reader.readAsDataURL(this.files[0]);
});

$('input[type="date"]').focus(function () {
    this.showPicker();   // Chrome, Edge, Opera
});

</script>
@endsection
