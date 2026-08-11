@extends('admin.layouts.master')

@section('title', 'E-News Paper')

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

         <div class="row">
            <div class="col-sm-7">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">E-News Paper</li>
                </ol>
            </div>
            
            <div class="col-sm-5 d-flex align-items-center justify-content-end">
            @if(Auth::user()->id == 3)
                <!-- <a href="{{ route('views.tv_report') }}" class="btn btn-default mw-120 mr-4" style="margin-top: -14px;">View Reports Live TV</a> -->
                <a href="{{ route('views.report') }}" class="btn btn-default mw-120 mr-4" style="margin-top: -14px;">View Reports</a>
            @endif
                <a href="{{ route('user.report') }}" class="btn btn-default mw-120 mr-4" style="margin-top: -14px;">Report Users</a>
             
                <a href="{{ route('enews.create') }}" class="btn btn-default mw-120" style="margin-top: -14px;">Add New</a>
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

        <div class="table-responsive">
            <table class="table table-striped user-table text-center table-bordered example">
                <thead>
                    <tr style="background: #F9FAFF;">
                        <th>#</th>
                        <th>Image</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>PDF</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('pagescript')
<script>
$(document).ready(function() {

    var table = $('.user-table').DataTable({
        dom: "<'top'f>rt<'row'<'col-2'i><'col-1'l><'col-9'p>>",
        searching: false,
        order: [],
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        lengthMenu: [
            [10, 100, 500, -1],
            [10, 100, 500, "All"]
        ],
        language: {
            paginate: {
                previous: "<img src='{{url('assets/imgs/left-arrow.png')}}' >",
                next: "<img src='{{url('assets/imgs/left-arrow.png')}}' style='transform: rotate(180deg)'>"
            }
        },
        ajax: {
            url: "{{ route('enews.data') }}",
            data: function(d) {
                d.language = $('#input_type').val();
                d.search = $('#input_search').val();
            }
        },

        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },

            {   // image
                data: 'image',
                name: 'image',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return "<img src='" + data + "' class='rounded' style='height:55px; width:55px; object-fit:cover'>";
                }
            },

            {   // type
                data: 'type',
                name: 'type',
                render: function(data) {
                    return data.toUpperCase();
                }
            },

            { data: 'date', name: 'date' },

            {   // pdf
                data: 'pdf_file',
                name: 'pdf_file',
                render: function(data) {
                    return "<a href='" + data + "' class='btn btn-sm btn-primary' target='_blank'>View</a>";
                }
            },

            {   // status
                data: 'status',
                name: 'status',
                render: function(data) {
                    return data == 1
                        ? "<span class='badge badge-success'>Active</span>"
                        : "<span class='badge badge-danger'>Inactive</span>";
                }
            },

            {   // actions
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });

    // Search + Filters
    $('#input_type').change(function() {
        table.draw();
    });

    $('#input_search').keyup(function() {
        table.draw();
    });

});
</script>

@endsection
