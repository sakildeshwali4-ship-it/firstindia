@extends('admin.layouts.master')

@section('title', __('Label.Transactions'))

@section('content')
<style>
.transaction_user_data {list-style: none;margin-left: 13px;padding: 0;text-align:left;}
.transaction_user_data span {font-weight: bold;}
.card-header h3 {color:#fff;}
</style>
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
            <!--<div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('transactionAdd') }}" class="btn btn-default mw-120" style="margin-top: -14px;">Add Transaction</a>-->
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
            <div class="sorting col-md-3">
                <label style="width:160px;">Sort by :</label>
                <select class="form-control" id="type">
                    <option value="all">All</option>
                    <option value="today">Today</option>
                    <option value="month">Month</option>
                    <option value="year">Year</option>
                </select>
            </div>
            <div class="sorting col-md-3">
                <label style="width:150px;">Order Type :</label>
                <select class="form-control" id="order_type">
                    <option value="all">All</option>
                    <option value="package">Package</option>
                    <option value="audition">Audition</option>
                </select>
            </div>
            <div class="sorting col-md-3">
                <label style="width:185px;">Order Status :</label>
                <select class="form-control" id="order_status">
                    <option value="all">All</option>
                    <option value="0">Pending</option>
                    <option value="1">Completed</option>
                    <option value="2">Failed</option>
                    <option value="3">Cancelled</option>
                    <option value="4">Refund</option>
                </select>
            </div>
        </div>

        <div class="table-responsive table">
            <table class="table table-striped text-center table-bordered" id="datatable" style="width:99.98%;">
                <thead>
                    <tr>
                        <th> {{__('Label.#')}} </th>
                        <!--<th> {{__('Label.Coupons')}} </th>-->
                        <!-- <th> {{__('Label.User Name')}} </th>
                        <th> {{__('Label.Email')}} </th>-->
                        <th> User </th>
                        <th> Transaction Type </th>
                        <th> {{__('Label.Payment Id')}} </th>
                        <th> {{__('Label.Amount')}} </th>
                        <!--<th> {{__('Label.Description')}} </th>-->
                        <th> {{__('Label.Date')}} </th>
                        <th> {{__('Label.Status')}} </th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
		
		<div class="card">
			<div class="card-header">
				<div class="d-flex align-items-center">
					<h3 class="card-title-deposit">Transaction Summary</h3>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="form-group col-md-4 col-lg-4 col-12">
						<label for="total_applications"> Total Audition Applications: </label>
						<label id="total_applications"></label>  
					</div>
					<div class="form-group col-md-4 col-lg-4 col-12">
						<label for="package_subs"> Total Package Subscriptions: </label>
						<label id="package_subs"></label>  
					</div>
					<div class="form-group col-md-4 col-lg-4 col-12">
						<label for="total_users"> Total Users: </label>
						<label id="total_users"><?php echo $total_users; ?></label>  
					</div> 
				</div>
				<div class="row">
					<div class="form-group col-md-4 col-lg-4 col-12">
						<label for="total_amount"> Total Amount: </label>
						<label id="total_amount"></label>  
					</div>
					<div class="form-group col-md-4 col-lg-4 col-12">
						<label for="package_amount"> Pakage Transactions: </label>
						<label id="package_amount"></label>  
					</div>
					<div class="form-group col-md-4 col-lg-4 col-12">
						<label for="audition_amount"> Audition Transactions: </label>
						<label id="audition_amount"></label>  
					</div> 
				</div>   
			</div>   
		</div>
    </div>
@endsection

@section('pagescript')
    <script>
        $(document).ready(function() {
            $(function() {
                var table = $('#datatable').DataTable({
                    dom: "<'top'f>rt<'row'<'col-3'i><'col-2'l><'col-7'p>>",
                    searching: false,
                    //responsive: true,
                    //autoWidth: false,
                    processing: true,
                    serverSide: true,
					"sScrollX": '100%',
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
                            d.order_type = $('#order_type').val();
                            d.order_status = $('#order_status').val();
                        },
                    },
                    columns: [{
							orderable: false,
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        /*{
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
                        },*/
                        {
                            orderable: false,
                            render: function(data, type, row, meta) {
								return '<ul class="transaction_user_data"><li><span>Name:</span> '+row.user.name+'</li><li><span>Email:</span> '+row.user.email+'</li><li><span>Mobile:</span> '+row.user.mobile+'</li></ul>';
                            },
                        },
                        /*{
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
                        },*/
                        {
                            orderable: false,
                            render: function(data, type, row, meta) {
								let order_type_data = '<ul class="transaction_user_data"><li>'+row.order_type+'</li>';
								if(row.order_type == 'Audition') {
									//order_type_data += '<li><span>Name:</span> ';
								} else {
									order_type_data += '<li><span>Expiry:</span> '+row.expiry_date;
								}
								return order_type_data += '</ul>';
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
                        /*{
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
                        },*/
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            searchable: false
                        },
                    ],
					"aaSorting": [],
					"drawCallback": function (settings) {
						var response = settings.json;
						$('#total_amount').html(parseFloat(response.package_complete_transaction) + parseFloat(response.audition_complete_transaction));
						$('#package_amount').html(parseFloat(response.package_complete_transaction));
						$('#audition_amount').html(parseFloat(response.audition_complete_transaction));
						$('#package_subs').html(parseFloat(response.package_complete_transaction_count));
						$('#total_applications').html(parseFloat(response.audition_complete_transaction_count));
					}
                });

                $('#type, #order_status, #order_type').change(function(){
                    table.draw();
                });
                $('#input_search').keyup(function(){
                    table.draw();
                });
            });
        });
    </script>
@endsection