@extends('admin.layouts.master')

@section('title', 'Edit Option Data')

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
                    <a href="{{ route('option.index') }}">Option Data</a>
                </li>
                <li class="breadcrumb-item active">Edit Option Data</li>
            </ol>
        </div>

        <div class="col-sm-2 d-flex align-items-center justify-content-end">
            <a href="{{ route('option.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">
                Option List
            </a>
        </div>
    </div>

    <div class="card custom-border-card mt-3">
        <form id="update_enews" enctype="multipart/form-data" autocomplete="off">
            @csrf

            <input type="hidden" name="id" value="{{ $news->id }}">

            <div class="form-row">

                <!-- Type -->
                <div class="col-md-6">
                    <div class="form-group"> 
			<label>Type</label>
                        <select name="type" class="form-control"> 
                            <option value="game" {{ $news->type == 'game' ? 'selected' : '' }}>Game</option>
                            <option value="quize" {{ $news->type == 'quize' ? 'selected' : '' }}>Quize </option>
                            <option value="livescore" {{ $news->type == 'livescore' ? 'selected' : '' }}>Live score </option>
                            <option value="astro" {{ $news->type == 'astro' ? 'selected' : '' }}>Astro</option>
                        </select>
                    </div>
                </div>

                <!-- Date -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>URL</label>
                        <input name="url" type="url" class="form-control" value="{{ $news->url }}" required>
                    </div>
                </div>
            </div>

            <div class="form-row">

                <!-- Status -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ $news->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $news->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="form-row">

                <!-- Highlight Image Upload -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Highlight Image</label>
                        <input type="file" class="form-control" id="highlight_image" name="image" accept="image/*">
                        <small class="text-gray">Leave empty to keep old image</small>
                    </div>
                </div>

                <!-- Image Preview -->
                <div class="col-md-6">
                    <div class="form-group">
                        <img src="{{ $news->image ? asset($news->image) : asset('assets/imgs/no_img.png') }}"
                             style="height: 120px; width: 120px;" class="img-thumbnail"
                             id="preview-image-before-upload">
                    </div>
                </div>

            </div>

            <div class="text-right">
                <button type="button" class="btn btn-default mw-120" onclick="update_enews()">
                    UPDATE
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@section('pagescript')
<script>

function update_enews() {
    var formData = new FormData($("#update_enews")[0]);

    $("#dvloader").show();

    $.ajax({
        type: 'POST',
        url: '{{ route("option.update", $news->id) }}',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,

        success: function(resp) {
            $("#dvloader").hide();
            get_responce_message(resp, 'update_enews', '{{ route("option.index") }}');
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

</script>
@endsection
