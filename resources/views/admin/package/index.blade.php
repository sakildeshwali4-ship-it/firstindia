@extends('admin.layouts.master')

@section('title', __('Label.Package'))

@section('content')
<div class="body-content">
    <!-- mobile title -->
    <h1 class="page-title-sm">@yield('title')</h1>

    <div class="border-bottom row mb-2">
        <div class="col-sm-10">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{__('Label.Package')}}</li>
            </ol>
        </div>
        <div class="col-sm-2 d-flex align-items-center justify-content-end" style="margin-top:-14px">
            <a href="{{ route('packageAdd') }}" class="btn btn-default mw-120">{{__('Label.Add Package')}}</a>
        </div>
    </div>

    <!-- Search -->
    <div class="page-search mb-3">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><img src="{{ asset('assets/imgs/search.png') }}"></span>
            </div>
            <input type="text" id="input_search" class="form-control" placeholder="Search Package" aria-label="Search" aria-describedby="basic-addon1">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped category-table text-center table-bordered">
            <thead>
                <tr style="background: #F9FAFF;">
                    <th width="10px"> {{__('Label.#')}} </th>
                    <th width="80px"> {{__('Label.Name')}} </th>
                    <th width="65px"> {{__('Label.Price')}} </th>
                    <th width="150px"> {{__('Label.Type')}} </th>
                    <th width="150px"> {{__('Label.Watch On Laptop Or TV')}} </th>
                    <th width="150px"> {{__('Label.Ads Free Movies and Shows')}} </th>
                    <th width="150px"> {{__('Label.Number of Devices that can be Logged In')}} </th>
                    <th width="80px"> {{__('Label.Video Quality')}} </th>
                    <th width="80"> Time </th>
                    <th width="65px"> {{__('Label.Action')}} </th>
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
                var table = $('.category-table').DataTable({
                    dom: "<'top'f>rt<'row'<'col-2'i><'col-1'l><'col-9'p>>",
                    searching: false,
                    responsive: true,
                    autoWidth: false,
                    processing: true,
                    serverSide: true,
                    lengthMenu: [[10, 100, 500, -1], [10, 100, 500, "All"]],
                    language: {
                        paginate: {
                            previous: "<img src='{{url('assets/imgs/left-arrow.png')}}' >",
                            next: "<img src='{{url('assets/imgs/left-arrow.png')}}' style='transform: rotate(180deg)'>"
                        }
                    },
                    ajax: {
                        url: "{{ route('packageData') }}",
                        data: function(d) {
                            d.input_search = $('#input_search').val();
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'price',
                            name: 'price'
                        },
                        {
                            data: 'type_name',
                            name: 'type_name',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, full, meta) {
                                var type = [];
                                if (data) {
                                    for (var i = 0; i < data.length; i++) {
                                        type.push(data[i].name);
                                    }
                                    if (type !== undefined && type != 0) {
                                        return type;
                                    } else {
                                        return "-";
                                    }
                                } else {
                                    return "-";
                                }
                            }
                        },
                        {
                            data: 'watch_on_laptop_tv',
                            name: 'watch_on_laptop_tv',
                            render: function(data, type, full, meta) {
                                if (data == 1) {
                                    return "Yes";
                                } else {
                                    return "No";
                                }
                            }
                        },
                        {
                            data: 'ads_free_movies_shows',
                            name: 'ads_free_movies_shows',
                            render: function(data, type, full, meta) {
                                if (data == 1) {
                                    return "Yes";
                                } else {
                                    return "No";
                                }
                            }
                        },
                        {
                            data: 'no_of_device',
                            name: 'no_of_device',
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            }
                        },
                        {
                            data: 'video_qulity',
                            name: 'video_qulity'
                        },
                        {
                            data: 'time',
                            name: 'time',
                            orderable: false,
                            searchable: false,
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

                $('#input_search').keyup(function() {
                    table.draw();
                });
            });
        });
    </script>
@endsection