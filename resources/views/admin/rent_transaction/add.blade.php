@extends('admin.layouts.master')

@section('title','Add Rent Transaction')

@section('content')
<div class="body-content">
    <!-- mobile title -->
    <h1 class="page-title-sm">@yield('title')</h1>

    <div class="border-bottom row mb-2">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('RentTransaction') }}">Transaction</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    Transaction
                </li>
            </ol>
        </div>
    </div>

    <div class="card custom-border-card mt-3">
        <form enctype="multipart/form-data" id="search_user">
            @csrf
            <div class="form-row">
                <div class="col-8">
                    <div class="form-group">
                        <input name="name" type="text" class="form-control" id="name" placeholder="Search User Name or Mobile" autocomplete="off">
                    </div>
                </div>
                <div class="col-4">
                    <button type="button" class="btn btn-default mw-120 mr-3" onclick="search_user()">Search</button>
                    <a href="{{route('RenttransactionAdd')}}" class="btn btn-cancel mw-120">Clear</a>
                </div>
            </div>
        </form>
    </div>

    <?php if (isset($user->id)) { ?>
        <div class="card custom-border-card mt-3">
            <form enctype="multipart/form-data" id="add_transaction">
                @csrf
                <div class="form-row">
                    <div class="col-4">
                        <div class="form-group">
                            <label>Name</label>
                            <input name="user_id" type="hidden" class="form-control" readonly id="user_id" value="{{$user->id}}">
                            <input name="name" type="text" class="form-control" readonly id="user_id" value="@if($user->name){{$user->name}}@else - @endif">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input name="name" type="text" class="form-control" readonly value="@if($user->mobile){{$user->mobile}}@else - @endif">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Email</label>
                            <input name="name" type="text" class="form-control" readonly value="@if($user->email){{$user->email}}@else - @endif">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="text-gray">Note: If you select both, The video will be saved.</label>
                    </div>
                    <div class="col-4 mt-3">
                        <div class="form-group">
                            <label>Rent Videos</label>
                            <select name="rent_video_id" class="form-control">
                                <option value="">Select Video</option>
                                @foreach($video as $row)
                                    <option value="{{$row->id}}">{{$row->video->name}}&nbsp;&nbsp; - &nbsp;&nbsp;{{$row->price}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-4 mt-3">
                        <div class="form-group">
                            <label>Rent TV Shows</label>
                            <select name="rent_show_id" class="form-control">
                                <option value="">Select Show</option>
                                @foreach($show as $row)
                                    <option value="{{$row->id}}">{{$row->tvshow->name}}&nbsp;&nbsp; - &nbsp;&nbsp;{{$row->price}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <button type="button" class="btn btn-default mw-120" onclick="add_transaction()">Save</button>
                </div>
            </form>
        </div>
    <?php } else { ?>
        <div class="card custom-border-card mt-3">
            <div class="col-12">
                <h3>User List</h3>

                <div id="user_list"></div>
            </div>
        </div>
    <?php } ?>
</div>
@endsection

@section('pagescript')
<script>
    function add_transaction() {
        var formData = new FormData($("#add_transaction")[0]);
        $("#dvloader").show();
        $.ajax({
            type: 'POST',
            url: '{{ route("RenttransactionSave") }}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(resp) {
                $("#dvloader").hide();
                get_responce_message(resp, 'add_transaction', '{{ route("RentTransaction") }}');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("#dvloader").hide();
                toastr.error(errorThrown.msg, 'failed');
            }
        });
    }

    function search_user() {
        var formData = new FormData($("#search_user")[0]);
        $("#dvloader").show();
        $.ajax({
            type: 'POST',
            url: '{{ route("rentSearchUser") }}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(resp) {
                $("#dvloader").hide();
                $('#user_list').html(resp.result);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("#dvloader").hide();
                toastr.error(errorThrown.msg, 'failed');
            }
        });
    }
</script>
@endsection