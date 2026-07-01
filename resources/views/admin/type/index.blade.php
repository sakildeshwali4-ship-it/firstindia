@extends('admin.layouts.master')

@section('title', __('Label.Types'))

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('Label.Types')}}</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end">
                <a href="{{ route('typeAdd') }}" class="btn btn-default mw-130" style="margin-top: -14px;">
                    {{__('Label.Add Types')}}
                </a>
            </div>
        </div>

        <!-- Search -->
        <div class="page-search mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><img src="{{ asset('assets/imgs/search.png') }}"></span>
                </div>
                <input type="text" id="input_search" class="form-control" placeholder="Search Type" aria-label="Search" aria-describedby="basic-addon1">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped type-table text-center table-bordered">
                <thead>
                    <tr >
                        <th> {{__('Label.#')}} </th>
                        <th> {{__('Label.Name')}} </th>
                        <th> {{__('Label.Type')}} </th>
                        <th> {{__('Label.Action')}} </th>
                    </tr>
                </thead>
                <tbody id="typeTableData"></tbody>
            </table>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        $(document).ready(function() {
            $(function() {
                var table = $('.type-table').DataTable({
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
                        url: "{{ route('typeData') }}",
                        data: function(d) {
                            d.input_search = $('#input_search').val();
                        },
                    },
					rowId: 'id',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
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
                                if (data == 1) {
                                    return "Video";
                                } else if (data == 2) {
                                    return "Show";
                                } else if (data == 5) {
                                    return "Upcoming";
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
					"aaSorting":[]
                });

                $('#input_search').keyup(function() {
                    table.draw();
                });
			
				$('#typeTableData').sortable({
					helper: "clone",
					"order": [],
					update: function(event, ui) {
						var position = ui.item.index();
						var type_id = ui.item.attr('id');
						$.ajax({
							url: "{{ route('updateTypeSequence') }}",
							data: {'position': position, 'type_id' : type_id},
							type: 'POST',
							success: function(response){
								if(response.status == 200){
									table.ajax.reload();
								} else {
									alert('Error on update sequence.');
								}
							}
						});
					}
				});
			});
        });
    </script>
@endsection