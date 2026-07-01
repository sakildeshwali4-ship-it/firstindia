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
                    <li class="breadcrumb-item active" aria-current="page">{{__('Label.Setting')}}</li>
                </ol>
            </div>
        </div>

        <ul class="nav nav-pills custom-tabs inline-tabs" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="app-tab" data-toggle="tab" href="#app" role="tab" aria-controls="app" aria-selected="true">{{__('Label.APP SETTINGS')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="social-tab" data-toggle="tab" href="#social" role="tab" aria-controls="social" aria-selected="false">SOCIAl SETTING</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="change-password-tab" data-toggle="tab" href="#change-password" role="tab" aria-controls="change-password" aria-selected="true">{{__('Label.CHANGE PASSWORD')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="admob-tab" data-toggle="tab" href="#admob" role="tab" aria-controls="admob" aria-selected="false">{{__('Label.ADMOB')}}</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" id="facebook-ads-tab" data-toggle="tab" href="#facebook-ads" role="tab"
                    aria-controls="facebook-ads" aria-selected="false">{{__('Label.FACEBOOK ADS')}}</a>
            </li> -->
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade  show active" id="app" role="tabpanel" aria-labelledby="app-tab">
                <div class="app-right-btn">
                    <a href="{{route('settingsmtpindex')}}" class="btn btn-default">{{__('Label.EMAIL SETTINGS [SMTP]')}}</a>
                </div>
                <div class="card custom-border-card">
                    <h5 class="card-header">{{__('Label.App Configrations')}}</h5>
                    <div class="card-body">
                        <div class="input-group">
                            <div class="col-2">
                                <label class="ml-5 pt-3" style="font-size:16px; font-weight:500; color:#1b1b1b">{{__('Label.API Path')}}</label>
                            </div>
                            <input type="text" readonly value="{{url('/')}}/api/" name="api_path" class="form-control" id="api_path">
                            <div class="input-group-prepend">
                                <div class="btn btn-default" onclick="Function_Api_path()">
                                <i class="fa-regular fa-copy text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               <!-- <div class="card custom-border-card">
                    <h5 class="card-header">IMDb Api Key</h5>
                    <div class="card-body">
                        <form id="save_imdb_api_key">
                            @csrf
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-8">
                                    <label>IMDb Api Key</label>
                                    <input type="text" name="imdb_api_key" class="form-control" value="{{$result['imdb_api_key']}}" placeholder="Enter IMDb Api Key">
                                </div>
                            </div>
                            <label class="mt-1 text-gray">Recommended : How to Create IMDb API Key <a href="https://imdb-api.com/Identity/Account/Login" target="_blank" class="btn-link">Click Here</a></label>
                            <div class="text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="save_imdb_api_key()">{{__('Label.SAVE')}}</button>
                            </div>
                        </form>
                    </div>
                </div> -->
                <div class="card custom-border-card">
                    <h5 class="card-header">{{__('Label.App Settings')}}</h5>
                    <div class="card-body">
                        <form id="app_setting" enctype="multipart/form-data">
                            @csrf
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-6">
                                    <label>{{__('Label.App Name')}}</label>
                                    <input type="text" name="app_name" class="form-control" placeholder="Enter App Name" value="{{$result['app_name']}}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>{{__('Label.Host Email')}}</label>
                                    <input type="email" name="host_email" class="form-control" value="{{$result['host_email']}}" placeholder="Enter Host Email">
                                </div>
                            </div>
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-6">
                                    <label>{{__('Label.App Version')}}</label>
                                    <input type="text" name="app_version" class="form-control" value="{{$result['app_version']}}" placeholder="Enter App Version">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>{{__('Label.Author')}}</label>
                                    <input type="text" name="Author" class="form-control" value="{{$result['Author']}}" placeholder="Enter Author">
                                </div>
                            </div>
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-6">
                                    <label>{{__('Label.Email')}} </label>
                                    <input type="email" name="email" class="form-control" value="{{$result['email']}}" placeholder="Enter Email">
                                </div>
                                <div class="form-group  col-lg-6">
                                    <label>{{__('Label.Contact')}} </label>
                                    <input type="number" name="contact" class="form-control" value="{{$result['contact']}}" placeholder="Enter Contact">
                                </div>
                            </div>
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-12">
                                    <label>{{__('Label.APP DESCRIPATION')}}</label>
                                    <textarea name="app_desripation" class="form-control" rows="5" placeholder="Enter App Desripation">{{$result['app_desripation']}}</textarea>
                                </div>
                            </div>
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-12">
                                    <label>{{__('Label.PRIVACY POLICY')}}</label>
                                    <textarea name="privacy_policy" class="form-control summernote" rows="5" placeholder="Enter Privacy Policy">{{$result['privacy_policy']}}</textarea>
                                </div>
                            </div>
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-12">
                                    <label>{{__('Label.Instrucation')}}</label>
                                    <textarea name="instrucation" class="form-control summernote" rows="5" placeholder="Enter Instrucation">{{$result['instrucation']}}</textarea>
                                </div>
                            </div>
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-6">
                                    <label for="app_logo">{{__('Label.APP IMAGE')}}</label>
                                    <input type="file" name="app_logo" class="form-control" id="image" placeholder="Enter Your App Name" value="{{$result['app_logo']}}">
                                    <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>{{__('Label.WEBSITE')}}</label>
                                    <input type="text" name="website" class="form-control" value="{{$result['website']}}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-5">
                                <div class="form-group">
                                    <div class="custom-file ml-5">
                                        <?php $app = Get_Image('app', $result['app_logo']); ?>
                                        <img src="{{$app}}" style="height: 120px; width: 120px;" class="img-thumbnail mb-5" id="preview-image-before-upload">
                                        <input type="hidden" name="old_app_logo" value="{{$result['app_logo']}}">
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="app_setting()">{{__('Label.SAVE')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card custom-border-card">
                    <h5 class="card-header">{{__('Label.Currency Settings')}}</h5>
                    <div class="card-body">
                        <form id="save_currency">
                            @csrf
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-6">
                                    <label>{{__('Label.Currency Name')}} </label>
                                    <input type="text" name="currency" class="form-control" value="{{$result['currency']}}" placeholder="Enter Currency Name">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label> {{__('Label.Currency Code')}} </label>
                                    <input type="text" name="currency_code" class="form-control" value="{{$result['currency_code']}}" placeholder="Enter Currency Code">
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="save_currency()">{{__('Label.SAVE')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                <div class="card custom-border-card">
                    <h5 class="card-header">Social Links</h5>
                    <div class="card-body">
                        <form id="social_link" enctype="multipart/form-data">
                            @csrf
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-5">
                                    <label>Name</label>
                                    <input type="text" name="name[]" class="form-control" placeholder="Enter URL Name">
                                </div>
                                <div class="form-group col-lg-5">
                                    <label>URL</label>
                                    <input type="url" name="url[]" class="form-control" placeholder="Enter URL">
                                </div>
                                <div class="col-md-1 mt-2">
                                    <div class="flex-grow-1 px-5 d-inline-flex">
                                        <div class="change mr-3 mt-4" id="add_btn" title="Add More">
                                            <a class="btn btn-success add-more text-white" onclick="add_more_link()">+</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-5">
                                    <label>Icon</label>
                                    <input type="file" name="image[]" class="form-control social_img" id="social_img">
                                    <input type="hidden" name="old_image[]" value="">
                                    <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                                </div>
                                <div class="form-group">
                                    <div class="custom-file">
                                        <img src="{{asset('assets/imgs/no_img.png')}}" style="height: 120px; width: 120px;" class="img-thumbnail" id="link_img_social_img">
                                    </div>
                                </div>
                            </div>

                            @for ($i=0; $i < count($social_link); $i++)
                                <div class="social_part">
                                    <div class="row col-lg-12">
                                        <div class="form-group col-lg-5">
                                            <label>Name</label>
                                            <input type="text" name="name[]" value="{{ $social_link[$i]['name'] }}" class="form-control" placeholder="Enter URL Name">
                                        </div>
                                        <div class="form-group col-lg-5">
                                            <label>URL</label>
                                            <input type="url" name="url[]" value="{{ $social_link[$i]['url'] }}" class="form-control" placeholder="Enter URL">
                                        </div>
                                        <div class="col-md-1 mt-2">
                                            <div class="flex-grow-1 px-5 d-inline-flex">
                                                <div class="change mr-3 mt-4" id="add_btn" title="Remove">
                                                    <a class="btn btn-danger text-white remove_link">-</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row col-lg-12">
                                        <div class="form-group col-lg-5">
                                            <label>Icon</label>
                                            <input type="file" name="image[]" class="form-control social_img" id="social_img_{{$i}}">
                                            <input type="hidden" name="old_image[]" value="{{ $social_link[$i]['image'] }}">
                                            <label class="mt-1 text-gray">{{__('Label.Note_Image')}}</label>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-file">
                                                <?php $app = Get_Image('app', $social_link[$i]['image']); ?>
                                                <img src="{{$app}}" style="height: 120px; width: 120px;" class="img-thumbnail" id="link_img_social_img_{{$i}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor               
                            
                            <div class="after-add-more"></div>

                            <div class="text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="social_link()">{{__('Label.SAVE')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
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
            <div class="tab-pane fade" id="admob" role="tabpanel" aria-labelledby="admob-tab">
                <div class="card custom-border-card mt-3">
                    <h5 class="card-header">{{__('Label.Android Settings')}}</h5>
                    <div class="card-body">
                        <form id="admob_android">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>{{__('Label.Banner Ad')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="banner_ad" name="banner_ad" class="custom-control-input" {{ ($result['banner_ad']=='1')? "checked" : "" }} value="1">
                                                <label class="custom-control-label" for="banner_ad">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="banner_ad1" name="banner_ad" class="custom-control-input" {{ ($result['banner_ad']=='0')? "checked" : "" }} value="0">
                                                <label class="custom-control-label" for="banner_ad1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>{{__('Label.Interstital Ad')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="interstital_ad" name="interstital_ad" class="custom-control-input" {{ ($result['interstital_ad']=='1')? "checked" : "" }} value="1">
                                                <label class="custom-control-label" for="interstital_ad">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="interstital_ad1" name="interstital_ad" class="custom-control-input" {{ ($result['interstital_ad']=='0')? "checked" : "" }} value="0">
                                                <label class="custom-control-label" for="interstital_ad1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>Reward Ad</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="reward_ad" name="reward_ad" class="custom-control-input" {{ ($result['reward_ad']=='1')? "checked" : "" }} value="1">
                                                <label class="custom-control-label" for="reward_ad">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="reward_ad1" name="reward_ad" class="custom-control-input" {{ ($result['reward_ad']=='0')? "checked" : "" }} value="0">
                                                <label class="custom-control-label" for="reward_ad1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>{{__('Label.Banner Ad ID')}}</label>
                                        <input type="text" name="banner_adid" class="form-control" placeholder="Enter Banner Ad ID" value="{{$result['banner_adid']}}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>{{__('Label.Interstital Ad ID')}}</label>
                                        <input type="text" name="interstital_adid" class="form-control" placeholder="Enter interstital Ad ID" value="{{$result['interstital_adid']}}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>Reward Ad ID</label>
                                        <input type="text" name="reward_adid" class="form-control" placeholder="Enter Reward Ad ID" value="{{$result['reward_adid']}}">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label></label>
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>{{__('Label.Interstital Ad Click')}}</label>
                                        <input type="text" name="interstital_adclick" class="form-control" placeholder="Enter Interstital Ad Click" value="{{$result['interstital_adclick']}}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>{{__('Label.Reward Ad Click')}}</label>
                                        <input type="text" name="reward_adclick" class="form-control" placeholder="Enter Reward Ad Click" value="{{$result['reward_adclick']}}">
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="admob_android()">{{__('Label.SAVE')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card custom-border-card mt-3">
                    <h5 class="card-header">{{__('Label.IOS Settings')}}</h5>
                    <div class="card-body">
                        <form id="admob_ios">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>{{__('Label.Banner Ad')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_banner_ad" name="ios_banner_ad" class="custom-control-input" {{ ($result['ios_banner_ad']=='1')? "checked" : "" }} value="1">
                                                <label class="custom-control-label" for="ios_banner_ad">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_banner_ad1" name="ios_banner_ad" class="custom-control-input" {{ ($result['ios_banner_ad']=='0')? "checked" : "" }} value="0">
                                                <label class="custom-control-label" for="ios_banner_ad1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>{{__('Label.Interstital Ad')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_interstital_ad" name="ios_interstital_ad" class="custom-control-input" {{ ($result['ios_interstital_ad']=='1')? "checked" : "" }} value="1">
                                                <label class="custom-control-label" for="ios_interstital_ad">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_interstital_ad1" name="ios_interstital_ad" class="custom-control-input" {{ ($result['ios_interstital_ad']=='0')? "checked" : "" }} value="0">
                                                <label class="custom-control-label" for="ios_interstital_ad1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>Reward Ad</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_reward_ad" name="ios_reward_ad" class="custom-control-input" {{ ($result['ios_reward_ad']=='1')? "checked" : "" }} value="1">
                                                <label class="custom-control-label" for="ios_reward_ad">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="ios_reward_ad1" name="ios_reward_ad" class="custom-control-input" {{ ($result['ios_reward_ad']=='0')? "checked" : "" }} value="0">
                                                <label class="custom-control-label" for="ios_reward_ad1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>{{__('Label.Banner Ad ID')}}</label>
                                        <input type="text" name="ios_banner_adid" class="form-control" id="ios_banner_adid" placeholder="Enter Banner Ad ID" value="{{$result['ios_banner_adid']}}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>{{__('Label.Interstital Ad ID')}}</label>
                                        <input type="text" name="ios_interstital_adid" class="form-control" id="ios_interstital_adid" placeholder="Enter interstital Ad ID" value="{{$result['ios_interstital_adid']}}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>Reward Ad ID</label>
                                        <input type="text" name="ios_reward_adid" class="form-control" id="ios_reward_adid" placeholder="Enter Reward Ad ID" value="{{$result['ios_reward_adid']}}">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label></label>
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>Interstital Ad Click</label>
                                        <input type="text" name="ios_interstital_adclick" class="form-control" id="ios_interstital_adclick" placeholder="Enter Interstital Ad Click" value="{{$result['ios_interstital_adclick']}}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label>Reward Ad Click</label>
                                        <input type="text" name="ios_reward_adclick" class="form-control" placeholder="Enter Reward Ad Click" value="{{$result['ios_reward_adclick']}}">
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-default mw-120" onclick="admob_ios()">{{__('Label.SAVE')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- <div class="tab-pane fade" id="facebook-ads" role="tabpanel" aria-labelledby="facebook-ads-tab">
                <div class="card custom-border-card mt-3">
                    <h5 class="card-header">{{__('Label.Android Settings')}}</h5>
                    <div class="card-body">
                        <form id="fbad">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_native_status">{{__('Label.Native Status')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_native_status" name="fb_native_status"
                                                    class="custom-control-input"
                                                    {{ ($result['fb_native_status']=='1')? "checked" : "" }} value="1">
                                                <label class="custom-control-label"
                                                    for="fb_native_status">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_native_status1" name="fb_native_status"
                                                    class="custom-control-input"
                                                    {{ ($result['fb_native_status']=='0')? "checked" : "" }} value="0">
                                                <label class="custom-control-label"
                                                    for="fb_native_status1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_banner_status">{{__('Label.Banner Status')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_banner_status" name="fb_banner_status"
                                                    class="custom-control-input"
                                                    {{($result['fb_banner_status']=='1')? "checked" : "" }} value="1">
                                                <label class="custom-control-label"
                                                    for="fb_banner_status">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_banner_status1" name="fb_banner_status"
                                                    class="custom-control-input"
                                                    {{ ($result['fb_banner_status']=='0')? "checked" : "" }} value="0">
                                                <label class="custom-control-label"
                                                    for="fb_banner_status1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_interstiatial_status">{{__('Label.Interstiatial Status')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_interstiatial_status"
                                                    name="fb_interstiatial_status" class="custom-control-input"
                                                    {{($result['fb_interstiatial_status']=='1')? "checked" : "" }}
                                                    value="1">
                                                <label class="custom-control-label"
                                                    for="fb_interstiatial_status">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_interstiatial_status1"
                                                    name="fb_interstiatial_status" class="custom-control-input"
                                                    {{ ($result['fb_interstiatial_status']=='0')? "checked" : "" }}
                                                    value="0">
                                                <label class="custom-control-label"
                                                    for="fb_interstiatial_status1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_native_id">{{__('Label.Native Key')}}</label>
                                        <input type="text" name="fb_native_id" class="form-control" id="fb_native_id"
                                            value="{{$result['fb_native_id']}}" placeholder="Enter Native Key">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_banner_id">{{__('Label.Banner Key')}}</label>
                                        <input type="text" name="fb_banner_id" class="form-control" id="fb_banner_id"
                                            value="{{$result['fb_banner_id']}}" placeholder="Enter Banner key">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_interstiatial_id">{{__('Label.Interstiatial Key')}}</label>
                                        <input type="text" name="fb_interstiatial_id" class="form-control"
                                            id="fb_interstiatial_id" value="{{$result['fb_interstiatial_id']}}"
                                            placeholder="Enter Interstiatial Key">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group col-lg-6">
                                        <label for="fb_rewardvideo_status">{{__('Label.RewardVideo Status')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_rewardvideo_status" name="fb_rewardvideo_status"
                                                    class="custom-control-input"
                                                    {{($result['fb_rewardvideo_status']=='1')? "checked" : "" }}
                                                    value="1">
                                                <label class="custom-control-label"
                                                    for="fb_rewardvideo_status">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_rewardvideo_status1" name="fb_rewardvideo_status"
                                                    class="custom-control-input"
                                                    {{ ($result['fb_rewardvideo_status']=='0')? "checked" : "" }}
                                                    value="0">
                                                <label class="custom-control-label"
                                                    for="fb_rewardvideo_status1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group col-lg-6">
                                        <label for="fb_native_full_status">{{__('Label.Native Full Status')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_native_full_status" name="fb_native_full_status"
                                                    class="custom-control-input"
                                                    {{($result['fb_native_full_status']=='1')? "checked" : "" }}
                                                    value="1">
                                                <label class="custom-control-label"
                                                    for="fb_native_full_status">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_native_full_status1" name="fb_native_full_status"
                                                    class="custom-control-input"
                                                    {{ ($result['fb_native_full_status']=='0')? "checked" : "" }}
                                                    value="0">
                                                <label class="custom-control-label"
                                                    for="fb_native_full_status1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_rewardvideo_id">{{__('Label.Rewardvideo Status Key')}}</label>
                                        <input type="text" name="fb_rewardvideo_id" class="form-control"
                                            id="fb_rewardvideo_id" value="{{$result['fb_rewardvideo_id']}}"
                                            placeholder="Enter Reward Video Status Key">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_native_full_id">{{__('Label.Native Full Key')}}</label>
                                        <input type="text" name="fb_native_full_id" class="form-control"
                                            id="fb_native_full_id" value="{{$result['fb_native_full_id']}}"
                                            placeholder="Enter Native Full Key">
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-default mw-120"
                                    onclick="fbad()">{{__('Label.SAVE')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card custom-border-card mt-3">
                    <h5 class="card-header">{{__('Label.IOS Settings')}}</h5>
                    <div class="card-body">
                        <form id="fbad_ios">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_ios_native_status">{{__('Label.Native Status')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_ios_native_status" name="fb_ios_native_status"
                                                    class="custom-control-input"
                                                    {{ ($result['fb_ios_native_status']=='1')? "checked" : "" }}
                                                    value="1">
                                                <label class="custom-control-label"
                                                    for="fb_ios_native_status">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_ios_native_status1" name="fb_ios_native_status"
                                                    class="custom-control-input"
                                                    {{ ($result['fb_ios_native_status']=='0')? "checked" : "" }}
                                                    value="0">
                                                <label class="custom-control-label"
                                                    for="fb_ios_native_status1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_ios_banner_status">{{__('Label.Banner Status')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_ios_banner_status" name="fb_ios_banner_status"
                                                    class="custom-control-input"
                                                    {{($result['fb_ios_banner_status']=='1')? "checked" : "" }} value="1">
                                                <label class="custom-control-label"
                                                    for="fb_ios_banner_status">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_ios_banner_status1" name="fb_ios_banner_status"
                                                    class="custom-control-input"
                                                    {{ ($result['fb_ios_banner_status']=='0')? "checked" : "" }}
                                                    value="0">
                                                <label class="custom-control-label"
                                                    for="fb_ios_banner_status1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label
                                            for="fb_ios_interstiatial_status">{{__('Label.Interstiatial Status')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_ios_interstiatial_status"
                                                    name="fb_ios_interstiatial_status" class="custom-control-input"
                                                    {{($result['fb_ios_interstiatial_status']=='1')? "checked" : "" }}
                                                    value="1">
                                                <label class="custom-control-label"
                                                    for="fb_ios_interstiatial_status">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_ios_interstiatial_status1"
                                                    name="fb_ios_interstiatial_status" class="custom-control-input"
                                                    {{ ($result['fb_ios_interstiatial_status']=='0')? "checked" : "" }}
                                                    value="0">
                                                <label class="custom-control-label"
                                                    for="fb_ios_interstiatial_status1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_ios_native_id">{{__('Label.Native Key')}}</label>
                                        <input type="text" name="fb_ios_native_id" class="form-control"
                                            id="fb_ios_native_id" value="{{$result['fb_ios_native_id']}}"
                                            placeholder="Enter Native Key">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_ios_banner_id">{{__('Label.Banner Key')}}</label>
                                        <input type="text" name="fb_ios_banner_id" class="form-control"
                                            id="fb_ios_banner_id" value="{{$result['fb_ios_banner_id']}}"
                                            placeholder="Enter Banner Key">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_ios_interstiatial_id">{{__('Label.Interstiatial Key')}}</label>
                                        <input type="text" name="fb_ios_interstiatial_id" class="form-control"
                                            id="fb_ios_interstiatial_id" value="{{$result['fb_ios_interstiatial_id']}}"
                                            placeholder="Enter Interstiatial Key">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group col-lg-6">
                                        <label for="fb_ios_rewardvideo_status">{{__('Label.RewardVideo Status')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_ios_rewardvideo_status"
                                                    name="fb_ios_rewardvideo_status" class="custom-control-input"
                                                    {{($result['fb_ios_rewardvideo_status']=='1')? "checked" : "" }}
                                                    value="1">
                                                <label class="custom-control-label"
                                                    for="fb_ios_rewardvideo_status">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_ios_rewardvideo_status1"
                                                    name="fb_ios_rewardvideo_status" class="custom-control-input"
                                                    {{ ($result['fb_ios_rewardvideo_status']=='0')? "checked" : "" }}
                                                    value="0">
                                                <label class="custom-control-label"
                                                    for="fb_ios_rewardvideo_status1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group col-lg-6">
                                        <label for="fb_ios_native_full_status">{{__('Label.Native Full Status')}}</label>
                                        <div class="radio-group">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_ios_native_full_status"
                                                    name="fb_ios_native_full_status" class="custom-control-input"
                                                    {{($result['fb_ios_native_full_status']=='1')? "checked" : "" }}
                                                    value="1">
                                                <label class="custom-control-label"
                                                    for="fb_ios_native_full_status">{{__('Label.Yes')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="fb_ios_native_full_status1"
                                                    name="fb_ios_native_full_status" class="custom-control-input"
                                                    {{ ($result['fb_ios_native_full_status']=='0')? "checked" : "" }}
                                                    value="0">
                                                <label class="custom-control-label"
                                                    for="fb_ios_native_full_status1">{{__('Label.No')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_ios_rewardvideo_id">{{__('Label.Rewardvideo Status Key')}}</label>
                                        <input type="text" name="fb_ios_rewardvideo_id" class="form-control"
                                            id="fb_ios_rewardvideo_id" value="{{$result['fb_ios_rewardvideo_id']}}"
                                            placeholder="Enter Reward Video Status Key">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <label for="fb_ios_native_full_id">{{__('Label.Native Full Key')}}</label>
                                        <input type="text" name="fb_ios_native_full_id" class="form-control"
                                            id="fb_ios_native_full_id" value="{{$result['fb_ios_native_full_id']}}"
                                            placeholder="Enter native Full Key">
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-default mw-120"
                                    onclick="fbad_ios()">{{__('Label.SAVE')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        function app_setting() {

            var formData = new FormData($("#app_setting")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("settingapp") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    // $("html, body").animate({ scrollTop: 0 }, "swing");
                    // get_responce_message(resp);
                    get_responce_message(resp, 'app_setting', '{{ route("setting") }}');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }

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

        function save_currency() {
            $("#dvloader").show();
            var formData = new FormData($("#save_currency")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("settingcurrency") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    $("html, body").animate({
                        scrollTop: 0
                    }, "swing");
                    get_responce_message(resp);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }

        function save_imdb_api_key() {
            $("#dvloader").show();
            var formData = new FormData($("#save_imdb_api_key")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("settingimdbkey") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    $("html, body").animate({
                        scrollTop: 0
                    }, "swing");
                    get_responce_message(resp);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }

        function admob_android() {
            var formData = new FormData($("#admob_android")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("settingadmob_android") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    $("html, body").animate({
                        scrollTop: 0
                    }, "swing");
                    get_responce_message(resp);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }

        function admob_ios() {
            var formData = new FormData($("#admob_ios")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("settingadmob_ios") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    $("html, body").animate({
                        scrollTop: 0
                    }, "swing");
                    get_responce_message(resp);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }

        function fbad() {
            var formData = new FormData($("#fbad")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("settingfacebookad") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    $("html, body").animate({
                        scrollTop: 0
                    }, "swing");
                    get_responce_message(resp);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }

        function fbad_ios() {
            var formData = new FormData($("#fbad_ios")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("settingfacebookad_ios") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    $("html, body").animate({
                        scrollTop: 0
                    }, "swing");
                    get_responce_message(resp);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }

        function Function_Api_path() {
            /* Get the text field */
            var copyText = document.getElementById("api_path");

            /* Select the text field */
            copyText.select();
            copyText.setSelectionRange(0, 99999); /* For mobile devices */

            document.execCommand('copy');

            /* Alert the copied text */
            alert("Copied the API Path: " + copyText.value);
        }

        // ========== Social Settings ==========
        // Multipal Img Show 
        $(document).on('change', '.social_img', function(){
            // alert("this.id: " + this.id);
            readURL(this, this.id);
        });
        function readURL(input, id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                 
                reader.onload = function (e) {
                    $('#link_img_'+id).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Add Link Part
        var i = -1;
        function add_more_link(){

            var data = '<div class="social_part">';
                data += '<div class="row col-lg-12">';
                data += '<div class="form-group col-lg-5">';
                data += '<label>Name</label>';
                data += '<input type="text" name="name[]" class="form-control" placeholder="Enter URL Name">';
                data += '</div>';
                data += '<div class="form-group col-lg-5">';
                data += '<label>URL</label>';
                data += '<input type="url" name="url[]" class="form-control" placeholder="Enter URL">';
                data += '</div>';
                data += '<div class="col-md-1 mt-2">';
                data += '<div class="flex-grow-1 px-5 d-inline-flex">';
                data += '<div class="change mr-3 mt-4" id="add_btn" title="Remove">';
                data += '<a class="btn btn-danger add-more text-white remove_link">-</a>';
                data += '</div>';
                data += '</div>';
                data += '</div>';
                data += '</div>';
                data += '<div class="row col-lg-12">';
                data += '<div class="form-group col-lg-5">';
                data += '<label>Icon</label>';
                data += '<input type="file" name="image[]" class="form-control social_img" id="social_img_'+i+'">';
                data += '<input type="hidden" name="old_image[]" value="">';
                data += '<label class="mt-1 text-gray">{{__("Label.Note_Image")}}</label>';
                data += '</div>';
                data += '<div class="form-group">';
                data += '<div class="custom-file">';
                data += '<img src="{{asset("assets/imgs/no_img.png")}}" style="height: 120px; width: 120px;" class="img-thumbnail" id="link_img_social_img_'+i+'">';
                data += '</div>';
                data += '</div>';
                data += '</div>';
                data += '</div>';

            $('.after-add-more').append(data);
            i--;
            $("html, body").animate({ scrollTop: $(document).height() }, "slow");
        }
        // Remove Link Part
        $("body").on("click", ".remove_link", function(e) {
            $(this).parents('.social_part').remove();
        });

        // Save Social Link
        function social_link() {

            var formData = new FormData($("#social_link")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("settingSocialLink") }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    // $("html, body").animate({ scrollTop: 0 }, "swing");
                    // get_responce_message(resp);
                    get_responce_message(resp, 'app_setting', '{{ route("setting") }}');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown.msg, 'failed');
                }
            });
        }
    </script>
@endsection