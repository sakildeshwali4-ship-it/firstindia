@extends('admin.layouts.master')

@section('title', __('Label.Transactions'))

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-2">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('Label.Transaction')}}</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('transactionAdd') }}" class="btn btn-default mw-120" style="margin-top: -14px;">Add Transaction</a>
            </div>
        </div>

        <!-- Search -->
        <div class="page-search mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><img src="{{ asset('assets/imgs/search.png') }}"></span>
                </div>
                <input type="text" id="input_search" class="form-control" placeholder="Search By Payment ID" aria-label="Search" aria-describedby="basic-addon1">
            </div>
            <div class="sorting">
                <label>Sort by :</label>
                <select class="form-control" id="type">
                    <option value="all">All</option>
                    <option value="today">Today</option>
                    <option value="month">Month</option>
                    <option value="year">Year</option>
                </select>
            </div>
        </div>

        <div class="table-responsive table">
            <table class="table table-striped text-center table-bordered" id="datatable">
                <thead>
                    <tr>
                        <th> {{__('Label.#')}} </th>
                        <th> {{__('Label.Coupons')}} </th>
                        <th> {{__('Label.User Name')}} </th>
                        <th> {{__('Label.Email')}} </th>
                        <th> Mobile Number </th>
                        <th> {{__('Label.Package')}} </th>
                        <th> {{__('Label.Payment Id')}} </th>
                        <th> {{__('Label.Amount')}} </th>
                        <th> {{__('Label.Description')}} </th>
                        <th> {{__('Label.Date')}} </th>
                        <th> Expiry Date </th>
                        <th> {{__('Label.Status')}} </th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr >
                        <td colspan="12" class="text-center"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        $(document).ready(function() {
            $(function() {
                var table = $('#datatable').DataTable({
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
                    ajax:
                        {
                        url: "{{ route('TransactionData') }}",
                        data : function(d){
                            d.type = $('#type').val();
                            d.input_search = $('#input_search').val();
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'unique_id',
                            name: 'unique_id',
                            orderable: false,
                            render: function(data, type, full, meta) {
                                if (data != "") {
                                    return data;
                                } else {
                                    return "-";
                                }
                            },
                        },
                        {
                            data: 'user.name',
                            name: 'user.name',
                            orderable: false,
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            },
                        },
                        {
                            data: 'user.email',
                            name: 'user.email',
                            orderable: false,
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            },
                        },
                        {
                            data: 'user.mobile',
                            name: 'user.mobile',
                            orderable: false,
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            },
                        },
                        {
                            data: 'package.name',
                            name: 'package.name',
                            orderable: false,
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            },
                        },
                        {
                            data: 'payment_id',
                            name: 'payment_id',
                            orderable: false,
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            },
                        },
                        {
                            data: 'amount',
                            name: 'amount',
                            orderable: false,
                            render: function(data, type, row, meta) {
                                return row.currency_code + " " + row.amount;
                            }
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false,
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            },
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'expiry_date',
                            name: 'expiry_date'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            searchable: false
                        },
                    ],
                    footerCallback: function ( row, data, start, end, display ) {
                        var api = this.api(), data;
 
                        // converting to interger to find total
                        var intVal = function ( i ) {
                            return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
                        };

                        // computing column Total of the complete result 
                        var Total = api
                            .column(7)
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );

                        // Update footer by showing the total with the reference of the column index 
                        $(api.column(1).footer() ).html("Total Amount =&nbsp &nbsp {{currency_code() }}"+ " " + Total);
                    },
                });

                $('#type').change(function(){
                    table.draw();
                });
                $('#input_search').keyup(function(){
                    table.draw();
                });
            });
        });
    </script>
@endsection