@extends('admin.layouts.master')

@section('title', 'TV View Report List')

@section('content')

<div class="body-content">

    <h1 class="page-title-sm">@yield('title')</h1>

    <div class="row mb-4">
        <div class="col-sm-10">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">{{ __('Label.Dashboard') }}</a>
                </li>
                <li class="breadcrumb-item active">TV View Report List</li>
            </ol>
        </div>
    </div>

    <!-- Filter -->
    <div class="page-search mb-3 d-flex justify-content-end">
        <div class="sorting mr-4 d-flex">

            <div class="mr-3">
                <label>Sort by :</label>
                <select id="dateFilter" class="form-control">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            <div id="customRangeDiv" style="display:none;">
                <label>Date Range :</label>
                <input type="text" id="dateRange" class="form-control">
            </div>

        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped user-table text-center table-bordered">
            <thead>
                <tr style="background: #F9FAFF;">
                    <th>#</th>
                    <th>TV Name</th>
                    <th id="viewsHeading">Views</th>
                    <th>Total Views</th>
                </tr>
            </thead>
            <tbody id="readsTableBody">
                <tr>
                    <td colspan="4">Please select filter</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
@endsection
@section('pagescript')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(document).ready(function () {

    $('#dateRange').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(
            picker.startDate.format('YYYY-MM-DD') + ' - ' +
            picker.endDate.format('YYYY-MM-DD')
        );
        loadReport();
    });

    function loadReport() {

        let filter = $('#dateFilter').val();
        let start = '';
        let end = '';

        if(filter === 'custom'){
            let range = $('#dateRange').val();

            if(range){
                let dates = range.split(' - ');
                start = dates[0];
                end = dates[1];
            }
        }

        $.ajax({
            url: "{{ route('livetv.filterReport') }}",
            type: "GET",
            data: {
                filter: filter,
                start: start,
                end: end
            },
            success: function (res) {

                let html = '';
                let i = 1;

                if (!res.data || res.data.length === 0) {
                    html = `<tr><td colspan="3">No data found</td></tr>`;
                } else {

                    res.data.forEach(row => {
                        html += `
                            <tr>
                                <td>${i++}</td>
                                <td>${row.tv_name}</td>
                                <td>${row.date_views}</td>
                                <td>${row.total_views}</td>
                            </tr>
                        `;
                    });

                }

                $('#readsTableBody').html(html);

            }
        });

    }

    $('#dateFilter').change(function(){

        if($(this).val() === 'custom'){
            $('#customRangeDiv').show();
        }else{
            $('#customRangeDiv').hide();
            loadReport();
        }

        let filter = $(this).val();
        let heading = 'Views';

        if (filter === 'today') {
            heading = 'Today Views';
        } 
        else if (filter === 'yesterday') {
            heading = 'Yesterday Views';
        } 
        else if (filter === 'week') {
            heading = 'This Week Views';
        } 
        else if (filter === 'month') {
            heading = 'This Month Views';
        } 
        else if (filter === 'custom') {
            heading = 'Custom Range Views';
        }

        $('#viewsHeading').text(heading);

    });

    loadReport();

});
</script>
@endsection
