@extends('admin.layouts.master')

@section('title', __('Label.Add Language'))

@section('content')
	<div class="body-content">
		<!-- mobile title -->
		<h1 class="page-title-sm">@yield('title')</h1>

		<div class="border-bottom row mb-3">
			<div class="col-sm-10">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item">
						<a href="{{ route('language') }}">{{__('Label.Language')}}</a>
					</li>
					<li class="breadcrumb-item active" aria-current="page">
						{{__('Label.Add Language')}}
					</li>
				</ol>
			</div>
			<div class="col-sm-2 d-flex align-items-center justify-content-end">
				<a href="{{ route('language') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('Label.Language')}}</a>
			</div>
		</div>

		<div class="card custom-border-card mt-3">
			<form enctype="multipart/form-data" id="save_language" autocomplete="off">
				@csrf
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.Name')}}</label>
							<input name="name" type="text" class="form-control" placeholder="{{__('Label.Enter Language Name')}}">
						</div>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.IMAGE')}}</label>
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
				<div class="border-top mt-2 pt-3 text-right">
					<button type="button" class="btn btn-default mw-120" onclick="save_language()">{{__('Label.SAVE')}}</button>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('pagescript')
	<script>
		function save_language() {
			var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
				$("#dvloader").show();
				var formData = new FormData($("#save_language")[0]);
				$.ajax({
					type: 'POST',
					url: '{{ route("languageSave") }}',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					success: function(resp) {
						$("#dvloader").hide();
						get_responce_message(resp, 'save_language', '{{ route("language") }}');
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