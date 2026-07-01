@extends('admin.layouts.master')

@section('title', 'Ads')

@section('content')
    <div class="body-content">
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ads</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end" style="margin-top:-14px">
                <a href="{{ route('adsAdd') }}" class="btn btn-default mw-120">Add Ad</a>
            </div>
        </div>

        <div class="page-search mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><img src="{{ asset('assets/imgs/search.png') }}"></span>
                </div>
                <input type="text" id="input_search" class="form-control" placeholder="Search Ads" aria-label="Search" aria-describedby="basic-addon1">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped ads-table text-center table-bordered">
                <thead>
                    <tr style="background: #F9FAFF;">
                        <th>{{__('Label.#')}}</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Media Type</th>
                        <th>Start After</th>
                        <th>Repeat Every</th>
                        <th>Duration</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>{{__('Label.Action')}}</th>
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
            var table = $('.ads-table').DataTable({
                dom: "<'top'f>rt<'row'<'col-2'i><'col-1'l><'col-9'p>>",
                searching: false,
                autoWidth: false,
                responsive: true,
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
                    url: "{{ route('adsData') }}",
                    data: function(d) {
                        d.input_search = $('#input_search').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'title', name: 'title' },
                    { data: 'type', name: 'type' },
                    { data: 'media_type', name: 'media_type' },
                    { data: 'start_after_seconds', name: 'start_after_seconds' },
                    { data: 'repeat_every_seconds', name: 'repeat_every_seconds' },
                    { data: 'duration_seconds', name: 'duration_seconds' },
                    { data: 'priority', name: 'priority' },
                    { data: 'active_badge', name: 'active', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
            });

            $('#input_search').keyup(function() {
                table.draw();
            });
        });
    </script>
@endsection
