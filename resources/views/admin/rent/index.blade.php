@extends('admin.layouts.master')

@section('title', 'Rent Video')

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-2">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Rent video</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('RentVideoAdd') }}" class="btn btn-default mw-120" style="margin-top: -14px;">Add Rent Video</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped rent-table text-center table-bordered">
                <thead>
                    <tr style="background: #F9FAFF;">
                        <th> {{__('Label.#')}} </th>
                        <th> {{__('Label.Video')}} </th>
                        <th> {{__('Label.Price')}} </th>
                        <th> {{__('Label.Video Type')}} </th>
                        <th> {{__('Label.Type')}} </th>
                        <th> {{__('Label.Validity')}} </th>
                        <th> {{__('Label.Action')}} </th>
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
            $(function() {
                var table = $('.rent-table').DataTable({
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
                    ajax: "{{ route('RentVideoData') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'video_name',
                            name: 'video_name',
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            }
                        },
                        {
                            data: 'price',
                            name: 'price',
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            }
                        },
                        {
                            data: 'video_type',
                            name: 'video_type',
                            render: function(data, type, full, meta) {
                                if (data == 1) {
                                    return "Video";
                                } else if (data == 2) {
                                    return "Show";
                                } else {
                                    return "-";
                                }
                            }
                        },
                        {
                            data: 'type_name',
                            name: 'type_name',
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            }
                        },
                        {
                            data: 'time',
                            name: 'time',
                            render: function(data, type, row, meta) {
                                if (row.time && row.type) {
                                    return row.time + " " + row.type;
                                } else {
                                    return "-";
                                }
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                });
            });
        });
    </script>
@endsection