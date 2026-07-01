@extends('admin.layouts.master')

@section('title', __('Label.Cast'))

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="row">
            <div class="col-sm-10">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('Label.Cast')}}</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('castAdd') }}" class="btn btn-default mw-120" style="margin-top: -14px;">{{__('Label.Add Cast')}}</a>
            </div>
        </div>

        <!-- Search -->
        <div class="page-search mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><img src="{{ asset('assets/imgs/search.png') }}"></span>
                </div>
                <input type="text" id="input_search" class="form-control" placeholder="Search Cast" aria-label="Search" aria-describedby="basic-addon1">
            </div>
            <div class="sorting mr-4">
                <label>Sort by :</label>
                <select class="form-control" id="input_type">
                    <option value="all">{{__('Label.Select Type')}}</option>
                    <option value="Director">{{__('Label.Director')}}</option>
                    <option value="Writer">{{__('Label.Writer')}}</option>
                    <option value="Actor"> {{__('Label.Actor')}}</option>
                    <option value="Actress"> {{__('Label.Actress')}}</option>
                    <option value="Cricketer"> {{__('Label.Cricketer')}}</option>
                    <option value="Dancers"> {{__('Label.Dancers')}}</option>
                    <option value="Journalist"> {{__('Label.Journalist')}}</option>
                    <option value="Other"> {{__('Label.Other')}}</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped cast-table text-center table-bordered">
                <thead>
                    <tr style="background: #F9FAFF;">
                        <th> {{__('Label.#')}} </th>
                        <th> {{__('Label.Image')}} </th>
                        <th> {{__('Label.Name')}} </th>
                        <th> {{__('Label.Type')}} </th>
                        <th width="500px"> {{__('Label.Personal Info')}} </th>
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
                var table = $('.cast-table').DataTable({
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
                    ajax:
                        {
                        url: "{{ route('castData') }}",
                        data : function(d){
                            d.input_type = $('#input_type').val();
                            d.input_login_type = $('#input_login_type').val();
                            d.input_search = $('#input_search').val();
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
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
                            data: 'type',
                            name: 'type',
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return data;
                                } else {
                                    return "-";
                                }
                            }
                        },
                        {
                            data: 'personal_info',
                            name: 'personal_info',
                            render: function(data, type, full, meta) {
                                if (data) {
                                    return (data.length > 130) ? data.substring(0, 130) + '...' : data;
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

                $('#input_type').change(function(){
                    table.draw();
                });
                $('#input_search').keyup(function(){
                    table.draw();
                });
            });
        });
    </script>
@endsection