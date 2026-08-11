@extends('admin.layouts.master')

@section('title', 'Users Report List')

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="row">
            <div class="col-sm-10">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users Report List</li>
                </ol>
            </div> 
        </div>

        <!-- Export Files -->
        <div class="row mt-2 mb-2">
            <div class="col-8">
                <label class="text-gray pt-2 font-weight-bold"><img src="{{ asset('assets/imgs/information.png') }}" class="mr-3">Only the following data will be captured in this File.</label>
            </div>
            <div class="col-4">
                <div class="d-flex justify-content-end gap-2">
                    <!-- <button id="ms_excel" class="btn btn-default" title="Download MS-Excel"><i class="fa-sharp fa-solid fa-file-excel mr-2 font-weight-bold text-white" style="font-size:18px"></i>MS-Excel</button> -->
                    @if(Auth::user()->id == 3)
                    <a href="{{ route('export.report') }}" class="btn btn-default" title="Download MS-Excel"><i class="fa-sharp fa-solid fa-file-excel mr-2 font-weight-bold text-white" style="font-size:18px"></i>Actual Excel All</a>
                    @endif
                    <button id="ms_excel_all" class="btn btn-default" title="Download MS-Excel"><i class="fa-sharp fa-solid fa-file-excel mr-2 font-weight-bold text-white" style="font-size:18px"></i>Excel All</button>
                    <?php /*<button id="csv" class="btn btn-default" title="Download CSV"><i class="fa-solid fa-file-csv mr-2 font-weight-bold text-white" style="font-size:18px"></i>CSV</button>
                    <button id="pdf" class="btn btn-default" title="Download PDF"><i class="fa-solid fa-file-pdf mr-2 font-weight-bold text-white" style="font-size:18px"></i>PDF</button> */ ?>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="page-search mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><img src="{{ asset('assets/imgs/search.png') }}"></span>
                </div>
                <input type="text" id="input_search" class="form-control" placeholder="Search Users" aria-label="Search" aria-describedby="basic-addon1">
            </div>
            <div class="sorting mr-4">
                <label>Sort by :</label>
                <select class="form-control" id="input_type">
                    <option value="all">All</option>
                    <option value="today">Today</option>
                    <option value="month">Month</option>
                    <option value="year">Year</option>
                </select>
            </div> 
        </div>

        <div class="table-responsive">
            <table class="table table-striped user-table text-center table-bordered example">
                <thead>
                    <tr style="background: #F9FAFF;">
                        <th> {{__('Label.#')}} </th>
                        <th> User</th>
                        <th> Register Date </th> 
                        <th> Hindi Reads </th> 
                        <th> English Reads </th> 
                        <th>Total No of Read </th> 
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
                var table = $('.user-table').DataTable({
                    dom: "<'top'f>rt<'row'<'col-4'i><'col-2'l><'col-6'p>>",
                    searching: false,
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
                        url: "{{ route('userReportData') }}",
                        data: function(d) {
                            d.input_type = $('#input_type').val(); 
                            d.input_search = $('#input_search').val();
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            searchable: false,
                            orderable: false
                        },
                        
                        {
                            data: 'mobile',
                            name: 'mobile',
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            }
                        },
                        
                        {
                            data: 'date',
                            name: 'date',
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            }
                        },
                        {
                            data: 'hindi_reads',
                            name: 'hindi_reads',
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            }
                        },
                        {
                            data: 'english_reads',
                            name: 'english_reads',
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            }
                        },
                        {
                            data: 'total_reads',
                            name: 'total_reads',
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            }
                        },
                    ],
                    buttons: [{
                            extend: 'excel',
                            filename: "{{App_Name()}} - Users",
                            exportOptions: {
                                columns: [0, 2, 3, 4, 5, 7]
                            },
                            customize: function(xlsx) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                $('row:first c', sheet).attr('s', '2');
                            },
                        },
						{
							extend: 'excel',
							title: 'Users List',
							exportOptions: {
								columns: ':visible'
							},
							"action": newexportaction
						}
                        /*{
                            extend: 'csv',
                            filename: "{{App_Name()}} - Users",
                            exportOptions: {
                                columns: [0, 2, 3, 4, 5, 7]
                            },
                        },
                        {
                            extend: 'pdf',
                            title: "{{App_Name()}} - Users",
                            filename: "{{App_Name()}} - Users",
                            pageSize: 'A4',
                            exportOptions: {
                                columns: [0, 2, 3, 4, 5, 7]
                            },
                            customize: function(doc) {
                                doc.styles.tableHeader.fontSize = 12; //2, 3, 4, etc
                                doc.defaultStyle.fontSize = 10; //2, 3, 4,etc
                                doc.content[1].table.widths = ['5%', '10%', '20%', '20%', '15%', '30%'];
                                doc.styles.title.fontSize = 22;
                                doc.styles.title.alignment = 'center';
                                doc.defaultStyle.alignment = 'center';

                                // Create a header
                                doc['header'] = (function(page, pages) {
                                    return {
                                        columns: [{
                                                alignment: 'left',
                                                bold: true,
                                                text: "{{App_Name()}}",
                                            },
                                            {
                                                alignment: 'right',
                                                bold: true,
                                                text: ['Total Page ', {
                                                    text: pages.toString()
                                                }],
                                            }
                                        ],
                                        margin: [20, 20],
                                    }
                                });
                                // Create a footer
                                doc['footer'] = (function(page, pages) {
                                    return {
                                        columns: [{
                                            alignment: 'center',
                                            bold: true,
                                            text: ['Page ', {
                                                text: page.toString()
                                            }, ' of ', {
                                                text: pages.toString()
                                            }],
                                        }],
                                    }
                                });
                            }
                        }*/
                    ],
                });

                $('#ms_excel').on('click', function() {
                    var check_access = '{{Check_Admin_Access()}}';
                    if (check_access == 1) {
                        var table = $('.user-table').DataTable();
                        table.button('0').trigger();
                    } else {
                        toastr.error("You have no right to Download This Files.");
                    }
                });
                $('#ms_excel_all').on('click', function() {
                    var check_access = '{{Check_Admin_Access()}}';
                    if (check_access == 1) {
                        $('.user-table').DataTable().buttons(0,1).trigger();
                    } else {
                        toastr.error("You have no right to Download This Files.");
                    }
                });
                /*$('#csv').on('click', function() {

                    var check_access = '{{Check_Admin_Access()}}';
                    if (check_access == 1) {
                        var table = $('.user-table').DataTable();
                        table.button('1').trigger();
                    } else {
                        toastr.error("You have no right to Download This Files.");
                    }
                });
                $('#pdf').on('click', function() {

                    var check_access = '{{Check_Admin_Access()}}';
                    if (check_access == 1) {
                        var table = $('.user-table').DataTable();
                        table.button('2').trigger();
                    } else {
                        toastr.error("You have no right to Download This Files.");
                    }
                });*/

                 
                $('#input_search').keyup(function() {
                    table.draw();
                });
            });
        });
		
		function newexportaction(e, dt, button, config) {
			var self = this;
			var oldStart = dt.settings()[0]._iDisplayStart;
			dt.one('preXhr', function (e, s, data) {
				// Just this once, load all data from the server...
				data.start = 0;
				data.length = 2147483647;
				dt.one('preDraw', function (e, settings) {
					// Call the original action function
					if (button[0].className.indexOf('buttons-copy') >= 0) {
						$.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
					} else if (button[0].className.indexOf('buttons-excel') >= 0) {
						$.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
							$.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
							$.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
					} else if (button[0].className.indexOf('buttons-csv') >= 0) {
						$.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
							$.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
							$.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
					} else if (button[0].className.indexOf('buttons-pdf') >= 0) {
						$.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
							$.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
							$.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
					} else if (button[0].className.indexOf('buttons-print') >= 0) {
						$.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
					}
					dt.one('preXhr', function (e, s, data) {
						// DataTables thinks the first item displayed is index 0, but we're not drawing that.
						// Set the property to what it was before exporting.
						settings._iDisplayStart = oldStart;
						data.start = oldStart;
					});
					// Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
					setTimeout(dt.ajax.reload, 0);
					// Prevent rendering of the full data to the DOM
					return false;
				});
			});
			// Requery the server with the new one-time export settings
			dt.ajax.reload();
		};
    </script>
@endsection