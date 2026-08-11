@extends('admin.layouts.master')

@section('title', __('Label.Settings'))

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-0">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('Label.Developer Setting')}}</li>
                </ol>
            </div>
        </div>

        <ul class="nav nav-pills custom-tabs inline-tabs" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="subscription-settings-tab" data-toggle="tab" href="#subscription_settings" role="tab" aria-controls="subscription-settings" aria-selected="true">{{__('SETTINGS')}}</a>
            </li>
			<li class="nav-item">
                <a class="nav-link" id="change-password-tab" data-toggle="tab" href="#change-password" role="tab" aria-controls="change-password" aria-selected="true">{{__('Label.CHANGE PASSWORD')}}</a>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade  show active" id="subscription_settings" role="tabpanel" aria-labelledby="subscription-settings">
				<form id="save_setting_subscription">
					<div class="card custom-border-card">
						<h5 class="card-header">App Setting</h5>
						<div class="card-body">
							@csrf
							<div class="row col-lg-12">
								<div class="form-group col-lg-6">
									<label>{{__('Label.Is Login')}} </label>
									<select name="check_is_login" class="form-control">
										<option {{old('check_is_login', $result['check_is_login']) == 'false' ? 'selected="selected"' : '' }} value="false">FALSE</option>
										<option {{old('check_is_login', $result['check_is_login']) == 'true' ? 'selected="selected"' : '' }} value="true">TRUE</option>
									</select>
								</div>
								<div class="form-group col-lg-6">
									<label> {{__('Label.Is Subscribed')}} </label>
									<select name="check_is_subscribed" class="form-control">
										<option {{old('check_is_subscribed', $result['check_is_subscribed']) == 'false' ? 'selected="selected"' : '' }} value="false">FALSE</option>
										<option {{old('check_is_subscribed', $result['check_is_subscribed']) == 'true' ? 'selected="selected"' : '' }} value="true">TRUE</option>
									</select>
								</div>
							</div>
							
							<div class="row col-lg-12">
								<div class="form-group col-lg-6">
									<label>{{__('IOS App Version')}}</label>
									<input type="text" name="iso_app_version" class="form-control" value="{{$result['iso_app_version']}}">
								</div>
								<div class="form-group col-lg-6">
									<label> {{__('IOS App Force Download')}} </label>
									<select name="ios_app_force_download" class="form-control">
										<option {{old('ios_app_force_download', $result['ios_app_force_download']) == 'false' ? 'selected="selected"' : '' }} value="false">FALSE</option>
										<option {{old('ios_app_force_download', $result['ios_app_force_download']) == 'true' ? 'selected="selected"' : '' }} value="true">TRUE</option>
									</select>
								</div>
							</div>
							<div class="row col-lg-12">
								<div class="form-group col-lg-6">
									<label>{{__('Android App Version')}}</label>
									<input type="text" name="android_app_version" class="form-control" value="{{$result['android_app_version']}}">
								</div>
								<div class="form-group col-lg-6">
									<label> {{__('Android App Force Download')}} </label>
									<select name="android_app_force_download" class="form-control">
										<option {{old('android_app_force_download', $result['android_app_force_download']) == 'false' ? 'selected="selected"' : '' }} value="false">FALSE</option>
										<option {{old('android_app_force_download', $result['android_app_force_download']) == 'true' ? 'selected="selected"' : '' }} value="true">TRUE</option>
									</select>
								</div>
							</div>
							<div class="row col-lg-12">
								<div class="form-group col-lg-6">
									<label>Show What's up Button</label>
									<select name="show_whats_up_button" class="form-control">
										<option {{old('show_whats_up_button', $result['show_whats_up_button']) == 'false' ? 'selected="selected"' : '' }} value="false">FALSE</option>
										<option {{old('show_whats_up_button', $result['show_whats_up_button']) == 'true' ? 'selected="selected"' : '' }} value="true">TRUE</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="card custom-border-card">
						<h5 class="card-header">Additional Banner</h5>
						<div class="card-body">
							<div class="row col-lg-12">
								<div class="form-group col-lg-6">
									<label>{{__('Additional Banner Link')}}</label>
									<input type="text" name="additional_banner_link" class="form-control" placeholder="Enter Additional Banner Link" value="{{$result['additional_banner_link']}}">
								</div>
								<div class="form-group col-lg-6">
									<label> {{__('Additional Banner Status')}} </label>
									<select name="additional_banner_status" class="form-control">
										<option {{old('additional_banner_status', $result['additional_banner_status']) == 'false' ? 'selected="selected"' : '' }} value="false">FALSE</option>
										<option {{old('additional_banner_status', $result['additional_banner_status']) == 'true' ? 'selected="selected"' : '' }} value="true">TRUE</option>
									</select>
								</div>
							</div>
							<div class="row col-lg-12">
								<div class="form-group col-lg-6">
									<label for="additional_banner_image">{{__('Additional Banner Image')}}</label>
									<input type="file" name="additional_banner_image" class="form-control" id="image" placeholder="Enter Your App Name" value="{{$result['additional_banner_image']}}">
									<label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
								</div>
								<div class="col-md-6 mb-5">
									<div class="form-group">
										<div class="custom-file ml-5">
											<?php 
											if (!empty($result['additional_banner_image'])) {
												$app = $result['additional_banner_image'];
											} else {
												$app = asset('assets/imgs/no_img.png');
											} ?>
											<img src="{{$app}}" style="height: 120px; width: 120px;" class="img-thumbnail mb-5" id="preview_additional_banner_image"><?php 
											if (!empty($result['additional_banner_image'])) { ?>
												<button type="button" class="button remove_image" style="position: absolute;left: 25px;top: 80px;" id="remove_additional_banner_image">Remove</button><?php
											} ?>
											<input type="hidden" name="old_additional_banner_image" id="old_additional_banner_image" value="{{$result['additional_banner_image']}}">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card custom-border-card">
						<h5 class="card-header">Popup Banner</h5>
						<div class="card-body">
							<div class="row col-lg-12">
								<div class="form-group col-lg-6">
									<label>{{__('Popup Banner Link')}}</label>
									<input type="text" name="popup_banner_link" class="form-control" placeholder="Enter Popup Link" value="{{$result['popup_banner_link']}}">
								</div>
								<div class="form-group col-lg-6">
									<label> {{__('Popup Banner Status')}} </label>
									<select name="popup_banner_status" class="form-control">
										<option {{old('popup_banner_status', $result['popup_banner_status']) == 'false' ? 'selected="selected"' : '' }} value="false">FALSE</option>
										<option {{old('popup_banner_status', $result['popup_banner_status']) == 'true' ? 'selected="selected"' : '' }} value="true">TRUE</option>
									</select>
								</div>
								<div class="form-group col-lg-6">
									<label> {{__('Popup Banner Live Status')}} </label>
									<select name="popup_banner_live_status" class="form-control">
										<option {{old('popup_banner_live_status', $result['popup_banner_live_status']) == 'false' ? 'selected="selected"' : '' }} value="false">FALSE</option>
										<option {{old('popup_banner_live_status', $result['popup_banner_live_status']) == 'true' ? 'selected="selected"' : '' }} value="true">TRUE</option>
									</select>
								</div>
							</div>
							<div class="row col-lg-12">
								<div class="form-group col-lg-6">
									<label for="popup_banner_image">{{__('Popup Banner Image')}}</label>
									<input type="file" name="popup_banner_image" class="form-control" id="image_popup" value="{{$result['popup_banner_image']}}">
									<label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
								</div>
								<div class="col-md-6 mb-5">
									<div class="form-group">
										<div class="custom-file ml-5">
											<?php 
											if (!empty($result['popup_banner_image'])) {
												$app = $result['popup_banner_image'];
											} else {
												$app = asset('assets/imgs/no_img.png');
											} ?>
											<img src="{{$app}}" style="height: 120px; width: 120px;" class="img-thumbnail mb-5" id="preview_popup_banner_image"><?php 
											if (!empty($result['popup_banner_image'])) { ?>
												<button type="button" class="button remove_image" style="position: absolute;left: 25px;top: 80px;" id="remove_popup_banner_image">Remove</button><?php
											} ?>
											<input type="hidden" name="old_popup_banner_image" id="old_popup_banner_image" value="{{$result['popup_banner_image']}}">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card custom-border-card">
						<h5 class="card-header">Home Banner</h5>
						<div class="card-body">
							<div class="row col-lg-12">
								<div class="form-group col-lg-12">
									<label>{{__('Home Banner URL')}}</label>
									<input type="text" name="home_banner_url" class="form-control" placeholder="Enter Banner URL" value="{{$result['home_banner_url']}}">
								</div>
							</div>
							<div class="row col-lg-12">
								<div class="form-group col-lg-6">
									<label for="home_banner_image">{{__('Home Banner Image')}}</label>
									<input type="file" name="home_banner_image" class="form-control" id="image_popup" value="{{$result['home_banner_image']}}">
									<label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
								</div>
								<div class="col-md-6 mb-5">
									<div class="form-group">
										<div class="custom-file ml-5">
											<?php 
											if (!empty($result['home_banner_image'])) {
												$app = $result['home_banner_image'];
											} else {
												$app = asset('assets/imgs/no_img.png');
											} ?>
											<img src="{{$app}}" style="height: 120px; width: 120px;" class="img-thumbnail mb-5" id="preview_home_banner_image"><?php 
											if (!empty($result['home_banner_image'])) { ?>
												<button type="button" class="button remove_image" style="position: absolute;left: 25px;top: 80px;" id="remove_home_banner_image">Remove</button><?php
											} ?>
											<input type="hidden" name="old_home_banner_image" id="old_home_banner_image" value="{{$result['home_banner_image']}}">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div> 
					<div class="card custom-border-card">
						<h5 class="card-header">E-News Banner</h5>
						<div class="card-body">
							<div class="row col-lg-12">
								<div class="form-group col-lg-6">
									<label for="enews_banner_image">{{__('E-News Image')}}</label>
									<input type="file" name="enews_banner_image" class="form-control" id="image_popup" value="{{$result['enews_banner_image']}}">
									<label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
								</div>
								<div class="col-md-6 mb-5">
									<div class="form-group">
										<div class="custom-file ml-5">
											<?php 
											if (!empty($result['enews_banner_image'])) {
												$app = $result['enews_banner_image'];
											} else {
												$app = asset('assets/imgs/no_img.png');
											} ?>
											<img src="{{$app}}" style="height: 120px; width: 120px;" class="img-thumbnail mb-5" id="preview_enews_banner_image"><?php 
											if (!empty($result['enews_banner_image'])) { ?>
												<button type="button" class="button remove_image" style="position: absolute;left: 25px;top: 80px;" id="remove_enews_banner_image">Remove</button><?php
											} ?>
											<input type="hidden" name="old_enews_banner_image" id="old_enews_banner_image" value="{{$result['enews_banner_image']}}">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="text-right">
								<button type="button" class="btn btn-default mw-120" onclick="save_setting_subscription()">{{__('Label.SAVE')}}</button>
							</div>
						</div>
					</div>
				</form>
            </div>
           
            <div class="tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="change-password-tab">
                <div class="card custom-border-card">
                    <h5 class="card-header">{{__('Label.Change Password')}}</h5>
                    <div class="card-body">
                        <div class="">
                            <div class="form-group">
                                <form id="change_password">
                                    @csrf
                                    <input type="hidden" name="admin_id" value="<?php echo $admin->id; ?>">
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label>{{__('Label.New Password')}}</label>
                                            <input type="password" name="password" class="form-control" placeholder="Enter New Password">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label>{{__('Label.Confirm Password')}}</label>
                                            <input type="password" name="confirm_password" class="form-control" placeholder="Enter Confirm Password">
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-default mw-120" onclick="change_password()">{{__('Label.SAVE')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
	$(document).ready(function () {
		$('#remove_popup_banner_image').click(function (e) {
			$('#preview_popup_banner_image').attr('src', '');
			$('#old_popup_banner_image').val('');
			$(this).hide();
		});
		
		$('#remove_additional_banner_image').click(function (e) {
			$('#preview_additional_banner_image').attr('src', '');
			$('#old_additional_banner_image').val('');
			$(this).hide();
		});
		
		$('#remove_home_banner_image').click(function (e) {
			$('#preview_home_banner_image').attr('src', '');
			$('#old_home_banner_image').val('');
			$(this).hide();
		});


		$('#remove_enews_banner_image').click(function (e) {
			$('#preview_enews_banner_image').attr('src', '');
			$('#old_home_banner_image').val('');
			$(this).hide();
		});
	});
	
	function change_password() {
		var formData = new FormData($("#change_password")[0]);
		$("#dvloader").show();
		$.ajax({
			type: 'POST',
			url: '{{ route("settingchangepassword") }}',
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function(resp) {
				$("#dvloader").hide();
				$("html, body").animate({
					scrollTop: 0
				}, "swing");
				get_responce_message(resp, 'change_password');
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$("#dvloader").hide();
				toastr.error(errorThrown.msg, 'failed');
			}
		});
	}

	function save_setting_subscription() {
		$("#dvloader").show();
		var formData = new FormData($("#save_setting_subscription")[0]);
		$("#dvloader").show();
		$.ajax({
			type: 'POST',
			url: '{{ route("settingsubscription") }}',
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function(resp) {
				$("#dvloader").hide();
				/*$("html, body").animate({
					scrollTop: 0
				}, "swing");*/
				location.reload(true);
				get_responce_message(resp);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$("#dvloader").hide();
				toastr.error(errorThrown.msg, 'failed');
			}
		});
	}
    </script>
@endsection