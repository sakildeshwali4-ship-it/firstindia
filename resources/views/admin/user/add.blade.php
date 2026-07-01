@extends('admin.layouts.master')

@section('title', __('Label.Add User'))

@section('content')
	<div class="body-content">
		<!-- mobile title -->
		<h1 class="page-title-sm">@yield('title')</h1>

		<div class="row">
			<div class="col-sm-10">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item">
						<a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item">
						<a href="{{ route('user') }}">{{__('Label.Users')}}</a>
					</li>
					<li class="breadcrumb-item active" aria-current="page">
						{{__('Label.Add User')}}
					</li>
				</ol>
			</div>
			<div class="col-sm-2 d-flex align-items-center justify-content-end">
				<a href="{{ route('user') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('Label.Users List')}}</a>
			</div>
		</div>

		<div class="card custom-border-card mt-3">
			<form id="save_user" enctype="multipart/form-data" autocomplete="off">
				@csrf
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.NAME')}} </label>
							<input name="name" type="text" class="form-control" placeholder="Enter Your Name">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.MOBILE NUMBER')}} </label>
							<input name="mobile" type="text" class="form-control" placeholder="Enter Mobile Number">
						</div>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.EMAIL')}}</label>
							<input name="email" type="email" class="form-control" placeholder="Enter Email">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Password</label>
							<input name="password" type="password" class="form-control" placeholder="Enter password">
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
				<div class=" text-right">
					<button type="button" class="btn btn-default mw-120" onclick="save_user()">{{__('Label.SAVE')}}</button>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('pagescript')
	<script>
		function save_user() {
			var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
				var formData = new FormData($("#save_user")[0]);
				$("#dvloader").show();
				$.ajax({
					type: 'POST',
					url: '{{ route("userSave") }}',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					success: function(resp) {
						$("#dvloader").hide();
						get_responce_message(resp, 'save_user', '{{ route("user") }}');
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