@extends('admin.layouts.master')

@section('title', 'View Report List')

@section('content')
<div class="body-content">

    <h1 class="page-title-sm">@yield('title')</h1>

    <div class="row mb-4">
        <div class="col-sm-10">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">{{ __('Label.Dashboard') }}</a>
                </li>
                <li class="breadcrumb-item active">View Report List</li>
            </ol>
        </div>
    </div>

    <!-- Filter -->
    <div class="page-search mb-3 d-flex justify-content-end">
        <div class="sorting mr-4">
            <label>Sort by :</label>
            <select id="dateFilter" class="form-control">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped user-table text-center table-bordered">
            <thead>
                <tr style="background: #F9FAFF;">
                    <th>#</th>
                    <th>Type</th>
                    <th>Views</th>
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
<script>
$(document).ready(function () {

    function loadReport() {
        let filter = $('#dateFilter').val();

        $.ajax({
            url: "{{ route('filter.report') }}",
            type: "GET",
            data: { filter: filter },
            success: function (res) {

                let html = '';
                let i = 1;

                if (!res.data || res.data.length === 0) {
                    html = `<tr><td colspan="4">No data found</td></tr>`;
                } else {
                    res.data.forEach(row => {
                        html += `
                            <tr>
                                <td>${i++}</td>
                                <td>${row.type}</td>
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

    // On filter change
    $('#dateFilter').on('change', loadReport);

    // Auto load Today data on page load
    loadReport();

});
</script>
@endsection
