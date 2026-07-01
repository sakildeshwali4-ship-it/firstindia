@extends('admin.layouts.master')

@section('title', 'Edit Rent Video')

@section('content')
	<div class="body-content">
		<!-- mobile title -->
		<h1 class="page-title-sm">@yield('title')</h1>

		<div class="border-bottom row mb-2">
			<div class="col-sm-10">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item">
						<a href="{{ route('RentVideo') }}">Rent Video</a>
					</li>
					<li class="breadcrumb-item active" aria-current="page">
						Edit Rent Video
					</li>
				</ol>
			</div>
			<div class="col-sm-2 d-flex align-items-center justify-content-end">
				<a href="{{ route('RentVideo') }}" class="btn btn-default mw-120" style="margin-top:-14px">Rent Video</a>
			</div>
		</div>

		<div class="card custom-border-card mt-3">
			<form id="edit_rent_video">
				@csrf
				<div class="form-row">
					<div class="col-md-6 mb-3">
						<div class="form-group">
							<label>{{__('Label.Type')}}</label>
							<select class="form-control" name="type_id" id="type_id" onclick="SelectTypeId()">
								<option value="">{{__('Label.Select Type')}}</option>
								@foreach ($type as $key => $value)
								<option value="{{ $value->id }}" {{$result->type_id == $value->id ? 'selected' : ''}}>{{ $value->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-6 mb-3">
						<div class="form-group">
							<label>Video Type</label>
							<select class="form-control" name="video_type" id="video_type">
								<option value="1" {{$result->video_type == '1' ? 'selected' : ''}}>Video</option>
								<option value="2" {{$result->video_type == '2' ? 'selected' : ''}}>Show</option>
							</select>
						</div>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-6 mb-3">
						<div class="form-group">
							<label>Price</label>
							<input name="price" type="number" class="form-control" id="price" placeholder="Please Enter Price" value="{{ $result->price}}">
						</div>
					</div>
					<div class="col-md-6 mb-3 option_class_video">
						<?php $x = explode(",", $result->video_id); ?>
						<div class="form-group">
							<label>{{__('Label.Video')}}</label>
							<select class="form-control" name="video_id" id="video_id">
								@foreach ($video as $key => $value)
								<option value="{{ $value->id}}" {{(in_array($value->id, $x)) ? 'selected' : ''}}>
									{{ $value->name }}
								</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-6 mb-3 option_class_show">
						<?php $y = explode(",", $result->video_id); ?>
						<div class="form-group">
							<label>{{__('Label.TV Show')}}</label>
							<select class="form-control" name="show_id" id="show_id">
								@foreach ($tvshowVideo as $key => $value)
								<option value="{{ $value->id}}" {{(in_array($value->id, $y)) ? 'selected' : ''}}>
									{{ $value->name }}
								</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-3 mb-3 mt-3">
						<div class="form-group">
							<label>{{__('Label.Validity')}}</label>
							<select class="form-control" name="type" id="validity_type">
								<option value="">{{__('Label.Select Type')}}</option>
								<option value="Day" {{$result->type == 'Day' ? 'selected' : ''}}>Day</option>
								<option value="Week" {{$result->type == 'Week' ? 'selected' : ''}}>Week</option>
								<option value="Month" {{$result->type == 'Month' ? 'selected' : ''}}>Month</option>
								<option value="Year" {{$result->type == 'Year' ? 'selected' : ''}}>Year</option>
							</select>
						</div>
					</div>
					<div class="col-md-3 mb-3 mt-5">
						<div class="form-group">
							<select class="form-control time" name="time" id="time">
								<option value="">{{__('Label.Select Number')}}</option>
								@for($i=1; $i<=31; $i++) 
									<option value="{{$i}}" {{$result->time == $i ? 'selected' : ''}}>{{$i}}</option>
								@endfor
							</select>
						</div>
					</div>
				</div>
				<div class="text-right">
					<input type="hidden" value="{{$result->id}}" name="id">
					<button type="button" class="btn btn-default mw-120" onclick="edit_rent_video()">{{__('Label.UPDATE')}}</button>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('pagescript')
	<script>
		$(document).ready(function() {
			var question_type = <?php echo $result->video_type; ?>;
			if (question_type == "1") {
				$('.option_class_video').show();
				$('.option_class_show').hide();
			} else if (question_type == "2") {
				$('.option_class_show').show();
				$('.option_class_video').hide();
			} else {
				$('.option_class_video').hide();
				$('.option_class_show').hide();
			}

			var validity_type = "<?php echo $result->type; ?>";
			if (validity_type == "Day") {
				for (let i = 8; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else if (validity_type == "Week") {
				for (let i = 5; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else if (validity_type == "Month") {
				for (let i = 13; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else if (validity_type == "Year") {
				for (let i = 2; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else {
				$('.time').hide();
			}
		});
		$('#video_type').on('click', function() {
			var question_type = $("#video_type").val();
			if (question_type == "1") {
				$('.option_class_video').show();
				$('.option_class_show').hide();
			} else if (question_type == "2") {
				$('.option_class_show').show();
				$('.option_class_video').hide();
			} else {
				$('.option_class_video').hide();
				$('.option_class_show').hide();
			}
		})

		$('#validity_type').on('click', function() {
			$('.time').show();
			var type = $("#validity_type").val()

			for (let i = 1; i <= 31; i++) {
				$(".time option[value=" + i + "]").show();
				$(".time option[value=" + i + "]").attr("selected", false);
			}

			if (type == "Day") {
				for (let i = 8; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else if (type == "Week") {
				for (let i = 5; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else if (type == "Month") {
				for (let i = 13; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else if (type == "Year") {
				for (let i = 2; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else {
				$('.time').hide();
			}
		})

		function SelectTypeId() {
			var Type_id = $('#type_id').find(":selected").val();

			if (Type_id != null && Type_id != "") {
				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: 'get',
					url: '{{ route("RentVideoAdd") }}',
					data: {
						type_id: Type_id
					},
					success: function(resp) {

						$('#video_id option').remove();
						$('#show_id option').remove();

						$('#video_id').append('<option value="" disabled selected> Select Video</option>');
						$('#show_id').append('<option value="" disabled selected> Select Video</option>');

						for (let i = 0; i < resp.video.length; i++) {
							$('#video_id').append(new Option(resp.video[i].name, resp.video[i].id));
						}
						for (let i = 0; i < resp.tvshow.length; i++) {
							$('#show_id').append(new Option(resp.tvshow[i].name, resp.tvshow[i].id));
						}
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						toastr.error(errorThrown.msg, 'failed');
					}
				});
			};
		};

		function edit_rent_video() {
			var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
            if(Check_Admin == 1){
				var formData = new FormData($("#edit_rent_video")[0]);
				$("#dvloader").show();
				$.ajax({
					type: 'POST',
					url: '{{ route("RentVideoUpdate") }}',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					success: function(resp) {
						$("#dvloader").hide();
						get_responce_message(resp, 'edit_rent_video', '{{ route("RentVideo") }}');
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
	</script>
@endsection