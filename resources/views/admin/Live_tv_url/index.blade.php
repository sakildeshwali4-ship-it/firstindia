@extends('admin.layouts.master')

@section('title', 'Live Tv Url')

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

         <div class="row">
            <div class="col-sm-8">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Live tv Url</li>
                </ol>
            </div>
            
            <div class="col-sm-4 d-flex align-items-center justify-content-end"> 
                <a href="{{ route('livetv.create') }}" class="btn btn-default mw-120" style="margin-top: -14px;">Add New</a>
            </div>
        </div>

        

       <!-- Search -->
        <div class="page-search mb-3 mt-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <img src="{{ asset('assets/imgs/search.png') }}">
                    </span>
                </div>
                <input type="text" id="input_search" class="form-control" placeholder="Search..." aria-label="Search">
            </div>

            <div class="sorting mr-4">
                <label>Type :</label>
                <select class="form-control" id="input_type">
                    <option value="english" selected>English</option>
                    <option value="hindi">Hindi</option>
                </select>
            </div>
        </div>

         <div class="card custom-border-card mt-3">
        <div class="card-body">
            <table class="table table-bordered" id="livetv-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Dialog Image</th>
                        <th>URL</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    </div>
@endsection

@section('pagescript')


<script>
$(function() {
    $('#livetv-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("livetv.data") }}',
        columns: [
            {data: 'DT_RowIndex', name: '', orderable: false, searchable: false}, // <-- important
            {data: 'name', name: 'name'},
            {data: 'image', name: 'image', orderable: false, searchable: false},
            {data: 'dialog_image', name: 'dialog_image', orderable: false, searchable: false},
            {data: 'url', name: 'url'},
            {data: 'action', name: '', orderable: false, searchable: false},
        ],
        language: {
            paginate: {
                previous: "<img src='{{url('assets/imgs/left-arrow.png')}}' >",
                next: "<img src='{{url('assets/imgs/left-arrow.png')}}' style='transform: rotate(180deg)'>"
            }
        },
    });

});
</script>
@endsection

