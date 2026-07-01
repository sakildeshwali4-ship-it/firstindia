@extends('admin.layouts.master')

@section('title', 'Coupons')

@section('content')
<div class="body-content">
    <!-- mobile title -->
    <h1 class="page-title-sm">@yield('title')</h1>

    <div class="border-bottom row mb-3">
        <div class="col-sm-8">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Coupons</li>
            </ol>
        </div>
        <div class="col-sm-2 d-flex align-items-center justify-content-center">
            <a href="{{route('couponRandomAdd')}}" class="btn btn-default mw-120" style="margin-top: -14px;">Add Random</a>
        </div>
        <div class="col-sm-2 d-flex align-items-center justify-content-center">
            <a href="{{ route('couponAdd') }}" class="btn btn-default mw-120" style="margin-top: -14px;">Add Coupon</a>
        </div>
    </div>

    <!-- Search -->
    <div class="page-search mb-3">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><img src="{{ asset('assets/imgs/search.png') }}"></span>
            </div>
            <input type="text" id="input_search" class="form-control" placeholder="Search Coupons" aria-label="Search" aria-describedby="basic-addon1">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped coupon-table text-center table-bordered">
            <thead>
                <tr style="background: #F9FAFF;">
                    <th> {{__('Label.#')}} </th>
                    <th> {{__('Label.unique_id')}} </th>
                    <th> {{__('Label.Name')}} </th>
                    <th> {{__('Label.start_date')}} </th>
                    <th> {{__('Label.end_date')}} </th>
                    <th> {{__('Label.amount_type')}} </th>
                    <th> {{__('Label.Price')}} </th>
                    <th> {{__('Label.is_use')}} </th>
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
            var table = $('.coupon-table').DataTable({
                dom: "<'top'f>rt<'row'<'col-2'i><'col-1'l><'col-9'p>>",
                searching: false,
                autoWidth: false,
                responsive: true,
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
                    url: "{{ route('couponData') }}",
                    data: function(d) {
                        d.input_search = $('#input_search').val();
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        visible: false
                    },
                    {
                        data: 'unique_id',
                        name: 'unique_id',
                        render: function(data, type, full, meta) {
                            if (data) {
                                return data;
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, full, meta) {
                            if (data) {
                                return data;
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        render: function(data, type, full, meta) {
                            if (data) {
                                return data;
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                        render: function(data, type, full, meta) {
                            if (data) {
                                return data;
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        data: 'amount_type',
                        name: 'amount_type',
                        render: function(data, type, full, meta) {
                            if (data == 1) {
                                return "Price";
                            } else if (data == 2) {
                                return "Percentage";
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
                        data: 'is_use',
                        name: 'is_use',
                        render: function(data, type, full, meta) {
                            if (data == 0) {
                                return "All";
                            } else if (data == 1) {
                                return "One";
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