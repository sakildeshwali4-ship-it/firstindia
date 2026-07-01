@extends('admin.layouts.master')

@section('title', 'Edit Channel Banner')

@section('content')
	<div class="body-content">
		<!-- mobile title -->
		<h1 class="page-title-sm">@yield('title')</h1>

		<div class="row">
			<div class="col-sm-10">
				<ol class="breadcrumb mb-3">
					<li class="breadcrumb-item">
						<a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item">
						<a href="{{ route('ChannelBanner') }}">Banner</a>
					</li>
					<li class="breadcrumb-item active" aria-current="page">
						Edit Banner
					</li>
				</ol>
			</div>
			<div class="col-sm-2 d-flex align-items-center justify-content-end">
				<a href="{{ route('ChannelBanner') }}" class="btn btn-default mw-120" style="margin-top:-14px">Banner</a>
			</div>
		</div>

		<div class="card custom-border-card mt-3">
			<form id="edit_channel_banner" autocomplete="off">
				@csrf
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.NAME')}}</label>
							<select class="form-control" name="name">
								<option value="">{{__('Label.Select_Name')}}</option>
								@foreach ($channel as $key => $value)
								<option value="{{ $value->name}}" {{ $result->name == $value->name  ? 'selected' : ''}}>
									{{ $value->name }}
								</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.Link')}}</label>
							<input name="link" type="url" class="form-control" placeholder="Please Enter Link" value="{{ $result->link}}">
						</div>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Order No</label>
							<input name="order_no" min="1" type="number" class="form-control" placeholder="Please Enter No" value="{{ $result->order_no}}">
						</div>
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
					<button type="button" class="btn btn-default mw-120" onclick="edit_channel_banner()">{{__('Label.UPDATE')}}</button>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('pagescript')
	<script>
		function edit_channel_banner() {

			var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){

				var formData = new FormData($("#edit_channel_banner")[0]);
				$("#dvloader").show();
				$.ajax({
					type: 'POST',
					url: '{{ route("ChannelBannerUpdate") }}',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					success: function(resp) {
						$("#dvloader").hide();
						get_responce_message(resp, 'edit_channel_banner', '{{ route("ChannelBanner") }}');
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