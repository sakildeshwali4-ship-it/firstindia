@section('title', 'Edit Page')
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<!-- Tab Icon -->
	<link rel="shortcut icon" href="{{tab_icon()}}">

	<!-- Title -->
	<title>{{App_Name()}}</title>

	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>

	<!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

	<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>


	<link rel="stylesheet" href="{{asset('/assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{asset('/assets/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
	<link href="{{asset('/assets/css/toastr.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('/assets/css/style.css') }}" rel="stylesheet">

	<input type="hidden" value="{{URL('')}}" id="base_url">

	<!-- Custom CSS -->
	<style>
		#dvloader {
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			position: fixed;
			display: block;
			opacity: 0.7;
			background-color: #fff;
			z-index: 9999;
			text-align: center;
		}

		#dvloader image {
			position: absolute;
			top: 100px;
			left: 240px;
			z-index: 100;
		}

		.db-color-card.subscribers-card {
			background: #c9b7f1;
			color: #530899;
		}

		.db-color-card.rent_video-card {
			background: #dfab91;
			color: #692705;
		}

		.db-color-card.plan-card {
			background: #999898;
			color: #201f1e;
		}

		.db-color-card.green-card {
			background: #83cf78;
			color: #245c1c;
		}

		.db-color-card.category-card {
			background: #e9aaf1;
			color: #9d0bb1;
		}
	</style>
</head>

<body>

	@include('admin.layouts.sidebar')
	<div class="right-content">
		@include('admin.layouts.header')
		<div class="body-content">
			<!-- mobile title -->
			<h1 class="page-title-sm">Edit Page</h1>

			<div class="border-bottom row mb-3">
				<div class="col-sm-10">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">
							<a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a>
						</li>
						<li class="breadcrumb-item">
							<a href="{{ route('Page') }}">Page</a>
						</li>
						<li class="breadcrumb-item active" aria-current="page">
							Edit Page
						</li>
					</ol>
				</div>
				<div class="col-sm-2 d-flex align-items-center justify-content-end">
					<a href="{{ route('Page') }}" class="btn btn-default mw-120" style="margin-top:-14px">Page</a>
				</div>
			</div>

			<div class="card custom-border-card mt-3">
				<form id="edit_page">
					@csrf
					<div class="form-row">
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('Label.TITLE')}}</label>
								<input name="title" type="text" class="form-control" value="{{$result->title}}" placeholder="Please Enter Title" autofocus>
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								<label>Page Subtitle</label>
								<input name="page_subtitle" type="text" class="form-control" value="{{$result->page_subtitle}}" placeholder="Please Enter Page Sub Title">
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-12 mb-3">
							<div class="form-group">
								<label>DESCRIPTION</label>
								<textarea class="form-control" name="description" id="summernote">{{$result->description}}</textarea>
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Icon</label>
								<input type="file" class="form-control" id="image" name="image">
								<label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<div class="custom-file ml-5">									
									<img src="{{$result->icon}}" style="height: 120px; width: 120px;" class="img-thumbnail" id="preview-image-before-upload">
									<input type="hidden" name="old_image" value="{{$result->icon}}">
								</div>
							</div>
						</div>
					</div>
					<div class="border-top mt-2 pt-3 text-right">
						<input type="hidden" value="{{$result->id}}" name="id">
						<button type="button" class="btn btn-default mw-120" onclick="edit_page()">{{__('Label.UPDATE')}}</button>
					</div>
				</form>
			</div>

			<div style="display:none" id="dvloader"><img src="{{ asset('assets/imgs/loading.gif')}}" /></div>
		</div>
		<div style="display:none" id="dvloader"><img src="{{ asset('assets/imgs/loading.gif')}}" /></div>
	</div>

	<script>
		function edit_page() {
			var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
				var formData = new FormData($("#edit_page")[0]);
				$("#dvloader").show();
				$.ajax({
					type: 'POST',
					url: '{{ route("PageUpdate") }}',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					success: function(resp) {
						$("#dvloader").hide();
						get_responce_message(resp, 'edit_page', '{{ route("Page") }}');
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
		$('#summernote').summernote({
			placeholder: 'Hello.....',
			tabsize: 2,
			height: 250,
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'underline', 'clear']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['table', ['table']],
				['insert', ['link', 'picture', 'video']],
				['view', ['fullscreen', 'codeview', 'help']]
			]
		});

		function get_responce_message(resp, form_name = "", url = "") {
			if (resp.status == '200') {
				toastr.success(resp.success);
				document.getElementById(form_name).reset();
				setTimeout(function() {
					window.location.replace(url);
				}, 500);
			} else {
				var obj = resp.errors;
				if (typeof obj === 'string') {
					toastr.error(obj);
				} else {
					$.each(obj, function(i, e) {
						toastr.error(e);
					});
				}
			}
		}
		$('#image').change(function() {
			let reader = new FileReader();
			reader.onload = (e) => {
				$('#preview-image-before-upload').attr('src', e.target.result);
			}
			reader.readAsDataURL(this.files[0]);
		});
	</script>
	<script src="{{ asset('/assets/js/js.js')}}"></script>
	<!-- Toastr -->
	<script src="{{ asset('/assets/js/toastr.min.js')}}"></script>
</body>

</html>