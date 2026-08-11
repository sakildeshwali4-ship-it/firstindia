@extends('admin.layouts.master')

@section('title', __('Label.Edit Types'))

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
                        <a href="{{ route('type') }}">{{__('Label.Event')}}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{__('Edit Event')}}
                    </li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('event') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('Label.Event')}}</a>
            </div>
        </div>

        <div class="card custom-border-card mt-3">
            <form id="save_edit_type">
                @csrf
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{__('Label.Image')}}</label>
                            <input name="image" type="file" class="form-control" value="{{ $result->image}}" id="image"
                                placeholder="{{__('Label.Please Upload Image')}}" accept="image/*">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{__('Label.Tittle')}}</label>
                            <input name="tittle" type="text" class="form-control" value="{{ $result->name}}" id="tittle"
                                placeholder="{{__('Label.Please Enter Tittle')}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{__('Label.URL')}}</label>
                            <input name="url" type="text" class="form-control" value="{{ $result->url}}" id="url"
                                placeholder="{{__('Label.Please Enter URL')}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">{{__('Label.Status')}}</label>
                            <select class="form-control" id="is_show" name="is_show" value="{{ $result->is_show}}">
                                
                                <option value="1"> {{__('Label.Active')}}</option>
                                <option value="0"> {{__('Label.InActive')}}</option>
                                <!--<option value="2"> {{__('Label.Show')}}</option>
                                <option value="5"> Upcoming</option>-->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">{{__('Label.Live')}}</label>
                            <select class="form-control" id="is_live" name="is_live" value="{{ $result->is_live}}">
                                
                                <option value="1"> {{__('Label.Yes')}}</option>
                                <option value="0"> {{__('Label.No')}}</option>
                                <option value="2"> Out</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <input type="hidden" value="{{$result->id}}" name="id">
                    <button type="button" class="btn btn-default mw-120" onclick="save_edit_type()">{{__('Label.UPDATE')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        function save_edit_type() {
            var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
                var formData = new FormData($("#save_edit_type")[0]);
                $("#dvloader").show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("EventUpdate") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'save_edit_type', '{{ route("event") }}');
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