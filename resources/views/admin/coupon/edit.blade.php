@extends('admin.layouts.master')

@section('title', 'Edit Coupon')

@section('content')
	<div class="body-content">
		<!-- mobile title -->
		<h1 class="page-title-sm">@yield('title')</h1>

		<div class="border-bottom row mb-3">
			<div class="col-sm-10">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item">
						<a href="{{ route('coupon') }}">Coupon</a>
					</li>
					<li class="breadcrumb-item active" aria-current="page">
						Edit Coupon
					</li>
				</ol>
			</div>
			<div class="col-sm-2 d-flex align-items-center justify-content-end">
				<a href="{{ route('coupon') }}" class="btn btn-default mw-120" style="margin-top:-14px">Coupon</a>
			</div>
		</div>

		<div class="card custom-border-card mt-3">
			<form id="edit_coupon" autocomplete="off">
				@csrf
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.Name')}}</label>
							<input name="name" type="text" value="{{ $result->name}}" class="form-control" placeholder="Please Enter Name">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.is_use')}}</label>
							<select class="form-control" name="is_use">
								<option value="0" {{ $result->is_use == 0  ? 'selected' : ''}}>{{__('Label.All')}}</option>
								<option value="1" {{ $result->is_use == 1  ? 'selected' : ''}}>{{__('Label.One')}}</option>
							</select>
						</div>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.start_date')}}</label>
							<input name="start_date" value="{{ $result->start_date}}" type="date" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.end_date')}}</label>
							<input name="end_date" value="{{ $result->end_date}}" type="date" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label>{{__('Label.amount_type')}}</label>
							<select class="form-control" name="amount_type" id="amount_type">
								<option value="1" {{ $result->amount_type == 1  ? 'selected' : ''}}>{{__('Label.Price')}}</option>
								<option value="2" {{ $result->amount_type == 2  ? 'selected' : ''}}>{{__('Label.Percentage')}}</option>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="label">{{__('Label.Price')}}</label>
							<input name="price" type="number" value="{{ $result->price}}" class="form-control" placeholder="Please Enter Price">
						</div>
					</div>
				</div>
				<div class="border-top mt-2 pt-3 text-right">
					<input type="hidden" value="{{$result->id}}" name="id">
					<button type="button" class="btn btn-default mw-120" onclick="edit_coupon()">{{__('Label.UPDATE')}}</button>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('pagescript')
	<script>
		function edit_coupon() {
			var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){

				var formData = new FormData($("#edit_coupon")[0]);
				$("#dvloader").show();
				$.ajax({
					type: 'POST',
					url: '{{ route("couponUpdate") }}',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					success: function(resp) {
						$("#dvloader").hide();
						get_responce_message(resp, 'edit_coupon', '{{ route("coupon") }}');
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						$("#dvloader").hide();
						toastr.error(errorThrown.msg, 'failed');
					}
				});

			} else {
                toastr.error('You have no right to add, edit, and delete.');
            }
		}

		var amount_type = "<?php echo $result->amount_type; ?>";

		if (amount_type == 1) {
			$(".label").text("Price");
		} else {
			$(".label").text("Percentage");
		}

		$('#amount_type').change(function() {
			var optionValue = $(this).val();
			if (optionValue == '1') {
				$(".label").text("Price");
			} else {
				$(".label").text("Percentage");
			}
		});
	</script>
@endsection