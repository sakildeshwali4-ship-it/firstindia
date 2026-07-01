@extends('admin.layouts.master')

@section('title', __('Label.Edit Cast'))

@section('content')
	<div class="body-content">
		<!-- mobile title -->
		<h1 class="page-title-sm">@yield('title')</h1>

		<div class="row">
			<div class="col-sm-10">
				<ol class="breadcrumb mb-2">
					<li class="breadcrumb-item">
						<a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item">
						<a href="{{ route('cast') }}">Cast</a>
					</li>
					<li class="breadcrumb-item active" aria-current="page">
						{{__('Label.Edit Cast')}}
					</li>
				</ol>
			</div>
			<div class="col-sm-2 d-flex align-items-center justify-content-end">
				<a href="{{ route('cast') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('Label.Cast')}}</a>
			</div>
		</div>

		<div class="card custom-border-card mt-3">
			<form method="post" action="{{ route('castUpdate') }}" id="save_edit_cast" autocomplete="off">
				@csrf
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.NAME')}}</label>
							<input name="name" type="text" value="{{$result->name}}" class="form-control" placeholder="Please Enter Cast">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.Type')}}</label>
							<select class="form-control" name="type">
								<option value="">{{__('Label.Select Type')}}</option>
								<option value="Director" {{ $result->type == "Director" ? 'selected' : ''}}> {{__('Label.Director')}}</option>
								<option value="Writer" {{ $result->type == "Writer" ? 'selected' : ''}}> {{__('Label.Writer')}}</option>
								<option value="Actor" {{ $result->type == "Actor" ? 'selected' : ''}}> {{__('Label.Actor')}}</option>
								<option value="Actress" {{ $result->type == "Actress" ? 'selected' : ''}}> {{__('Label.Actress')}}</option>
								<option value="Cricketer" {{ $result->type == "Cricketer" ? 'selected' : ''}}> {{__('Label.Cricketer')}}</option>
								<option value="Dancers" {{ $result->type == "Dancers" ? 'selected' : ''}}> {{__('Label.Dancers')}}</option>
								<option value="Journalist" {{ $result->type == "Journalist" ? 'selected' : ''}}> {{__('Label.Journalist')}}</option>
								<option value="Other" {{ $result->type == "Other" ? 'selected' : ''}}> {{__('Label.Other')}}</option>
							</select>
						</div>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-lg-12">
						<label>{{__('Label.Personal Info')}}</label>
						<textarea name="personal_info" class="form-control" rows="5" id="personal_info" placeholder="I am ... " autocomplete="off">{{$result->personal_info}}</textarea>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.IMAGE')}}</label>
							<input type="file" class="form-control" id="image" name="image" value="{{$result->image}}">
							<label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
						</div>
					</div>
					<div class="col-md-6">
                        <div class="form-group">
                            <div class="custom-file ml-5">
                                <img src="{{$result->image}}" style="height: 120px; width: 120px;" class="img-thumbnail" id="preview-image-before-upload">
                                <input type="hidden" name="old_image" value="{{$result->image}}">
                            </div>
                        </div>
                    </div>
				</div>
				<div class="text-right">
					<input type="hidden" value="{{$result->id}}" name="id">
					<button type="button" class="btn btn-default mw-120" onclick="save_edit_cast()">{{__('Label.UPDATE')}}</button>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('pagescript')
	<script>
		function save_edit_cast() {

			var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
				var formData = new FormData($("#save_edit_cast")[0]);
				$.ajax({
					type: 'POST',
					url: '{{ route("castUpdate") }}',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					success: function(resp) {
						get_responce_message(resp, 'save_edit_cast', '{{ route("cast") }}');
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						toastr.error(errorThrown.msg, 'failed');
					}
				});
			} else {
                toastr.error('You have no right to add, edit, and delete.');
            }
		}
	</script>
@endsection