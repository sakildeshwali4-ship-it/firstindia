@extends('admin.layouts.master')

@section('title', __('Label.Users List'))

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="row">
            <div class="col-sm-10">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('Label.Users List')}}</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('userAdd') }}" class="btn btn-default mw-120" style="margin-top: -14px;">{{__('Label.Add User')}}</a>
            </div>
        </div>

        <!-- Export Files -->
        <div class="row mt-2 mb-2">
            <div class="col-8">
                <label class="text-gray pt-2 font-weight-bold"><img src="{{ asset('assets/imgs/information.png') }}" class="mr-3">Only the following data will be captured in this File.</label>
            </div>
            <div class="col-4">
                <div class="d-flex justify-content-end gap-2">
                    <button id="ms_excel" class="btn btn-default" title="Download MS-Excel"><i class="fa-sharp fa-solid fa-file-excel mr-2 font-weight-bold text-white" style="font-size:18px"></i>MS-Excel</button>
                    <button id="csv" class="btn btn-default" title="Download CSV"><i class="fa-solid fa-file-csv mr-2 font-weight-bold text-white" style="font-size:18px"></i>CSV</button>
                    <button id="pdf" class="btn btn-default" title="Download PDF"><i class="fa-solid fa-file-pdf mr-2 font-weight-bold text-white" style="font-size:18px"></i>PDF</button>
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
            <div class="sorting">
                <label>Sort by :</label>
                <select class="form-control" id="input_login_type">
                    <option value="all">All Type</option>
                    <option value="1">Facebook</option>
                    <option value="2">Google</option>
                    <option value="3">OTP</option>
                    <option value="4">Normal</option>
                    <option value="5">Apple</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped user-table text-center table-bordered example">
                <thead>
                    <tr style="background: #F9FAFF;">
                        <th> {{__('Label.#')}} </th>
                        <th> {{__('Label.Image')}} </th>
                        <th> {{__('Label.Name')}} </th>
                        <th> {{__('Label.Email')}} </th>
                        <th> {{__('Label.Mobile')}} </th>
                        <th> Register Date </th>
                        <th> {{__('Label.Type')}} </th>
                        <th> Login Type (1-Facebook, 2-Google, 3-OTP, 4-Normal, 5-Apple)</th>
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
                var table = $('.user-table').DataTable({
                    dom: "<'top'f>rt<'row'<'col-2'i><'col-1'l><'col-9'p>>",
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
                        url: "{{ route('userData') }}",
                        data: function(d) {
                            d.input_type = $('#input_type').val();
                            d.input_login_type = $('#input_login_type').val();
                            d.input_search = $('#input_search').val();
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            searchable: false
                        },
                        {
                            data: 'image',
                            name: 'image',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, full, meta) {
                                return "<a href='" + data + "' target='_blank' title='Watch'><img src='" + data + "' class='rounded-circle' style='height:55px; width:55px'></a>";
                            },
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
                            data: 'email',
                            name: 'email',
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            }
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
                            data: 'type',
                            name: 'type',
                            render: function(data, type, full, meta) {
                                if (data == 1) {
                                    return "<i class='fa-brands fa-facebook fa-3x' title='Facebook Login'></i>";
                                } else if (data == 2) {
                                    return "<i class='fa-brands fa-google fa-3x' title='Goggle Login'></i>";
                                } else if (data == 3) {
                                    return "<i class='fa-solid fa-mobile-screen-button fa-3x' title='OTP Login'></i>";
                                } else if (data == 4) {
                                    return "<i class='fa-solid fa-lock fa-3x' title='Normal Login'></i>";
                                } else if (data == 5) {
                                    return "<i class='fa-brands fa-apple fa-3x' title='Apple Login'></i>";
                                } else {
                                    return "-";
                                }
                            }
                        },
                        {
                            data: 'type',
                            name: 'Login Type',
                            orderable: false,
                            searchable: false,
                            visible: false,
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
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
                        }
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
                $('#csv').on('click', function() {

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
                });

                $('#input_type, #input_login_type').change(function() {
                    table.draw();
                });
                $('#input_search').keyup(function() {
                    table.draw();
                });
            });
        });
    </script>
@endsection