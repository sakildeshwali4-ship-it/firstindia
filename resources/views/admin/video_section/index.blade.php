@extends('admin.layouts.master')

@section('title', 'Section')

@section('content')
<style>.card {position:inherit !important;} div[class^='video_sort_'], div[class*=' video_sort_']{overflow: auto;}#edit_video_section11 .select2-selection__rendered {max-height: 100px; overflow: scroll !important;}</style>
     <div class="body-content">
          <!-- mobile title -->
          <h1 class="page-title-sm">@yield('title')</h1>

          <div class="row">
               <div class="col-sm-12">
                    <ol class="breadcrumb mb-0">
                         <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Label.Dashboard')}}</a></li>
                         <li class="breadcrumb-item active" aria-current="page">Section</li>
                    </ol>
               </div>
          </div>

          @if(isset($type) && $type != null && count($type) > 0)
          <ul class="tabs nav nav-pills custom-tabs inline-tabs " id="pills-tab" role="tablist">
               <li class="nav-item">
                    <a class="nav-link active section_type_tab_{{$type[0]['id']}}_1" id="app-tab"
                         onclick="Selected_Type('{{$type[0]['id']}}','{{$type[0]['type']}}','1')" data-is_home_screen="1"
                         data-id="0" data-toggle="pill" href="#app" role="tab" aria-controls="app" aria-selected="true">Home</a>
               </li>
               @for ($i = 0; $i < count($type); $i++) 
                    <li class="nav-item">
                         <a class="nav-link section_type_tab_{{$type[$i]['id']}}_2" id="{{ $type[$i]['name']}}-tab"
                              onclick="Selected_Type('{{$type[$i]['id']}}','{{$type[$i]['type']}}','2')" data-is_home_screen="2"
                              data-id="{{$type[$i]['id']}}" data-type="{{$type[$i]['type']}}" data-toggle="pill"
                              href="#{{ $type[$i]['name']}}" role="tab" aria-controls="{{ $type[$i]['name']}}" aria-selected="true">{{ $type[$i]['name']}}</a>
                    </li>
               @endfor
          </ul>
          @endif

          <div class="tab-content" id="pills-tabContent">
               <div class="tab-pane fade show active" id="app" role="tabpanel" aria-labelledby="app-tab">
                    <div class="card custom-border-card">
                         <h5 class="card-header">Add Section </h5>
                         @if(isset($type) && $type != null)
                         <div class="card-body">
 
                              <form id="save_video_section" name="save_video_section" autocomplete="off">
                                   @csrf
                                   <div class="custom-border-card">
                                        <div class="form-row">
                                             <div class="col-md-3">
                                                  <div class="form-group">
                                                       <label> {{__('Label.Title')}} </label>
                                                       <input name="title" type="text" class="form-control" id="title" placeholder="Enter Your Title">
                                                  </div>
                                             </div>
                                             <div class="col-md-2 Top_Video_Type">
                                                  <div class="form-group">
                                                       <label>{{__('Label.Video Type')}}</label>
                                                       <select class="form-control" name="video_type" id="video_type">
                                                            <option value="" selected disabled>{{__('Label.Select Video Type')}}</option>
                                                            <option value="0">Web Series</option>
                                                            <option value="1">{{__('Label.Video')}}</option>
                                                            <option value="2">{{__('Label.Show')}}</option>
                                                            <option value="3">{{__('Label.Language')}}</option>
                                                            <option value="4">{{__('Label.Category')}}</option>
                                                            <option value="5">Upcoming</option>
                                                       </select>
                                                  </div>
                                             </div>
                                             <div class="col-md-2 upcoming_type">
                                                  <div class="form-group">
                                                       <label>Upcoming Type</label>
                                                       <select class="form-control" name="upcoming_type" id="upcoming_type">
                                                            <option value="" selected disabled>Select Type</option>
                                                            <option value="1">{{__('Label.Video')}}</option>
                                                            <option value="2">{{__('Label.Show')}}</option>
                                                       </select>
                                                  </div>
                                             </div>
                                             <div class="col-md-2 Top_Type">
                                                  <div class="form-group">
                                                       <label>{{__('Label.Type')}}</label>
                                                       <select class="form-control" name="type_id" id="type_id" style="width:100%!important;">
                                                            <option value="" selected disabled> Select Type </option>
                                                            @foreach ($type as $key => $value)
                                                            <option value="{{$value->id}}">{{ $value->name }}</option>
                                                            @endforeach
                                                       </select>
                                                  </div>
                                             </div>
                                             <div class="col-md-2">
                                                  <div class="form-group">
                                                       <label>Screen Layout</label>
                                                       <select class="form-control" name="screen_layout" id="screen_layout">
                                                            <option value="landscape">Landscape</option>
                                                            <option value="square">Square</option>
                                                            <option value="potrait">Potrait</option>
                                                       </select>
                                                  </div>
                                             </div>
                                             <div class="col-md-1 mt-2">
                                                  <div class="flex-grow-1 px-5 pt-2 d-inline-flex">
                                                       <div class="change mr-3 mt-4" id="add_btn">
                                                            <a class="btn btn-success add-more text-white" onclick="Save_Video_Section(Is_home_screen, Type_id)">+</a>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                        <div class="form-row">
                                             <div class="col-md-12 mb-3">
                                                  <div class="form-group option_class_video">
                                                       <label>{{__('Label.Video')}}</label>
                                                       <select class="form-control selectd2" style="width:100%!important;"name="video_id[]" multiple id="video_id"></select>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                              </form>

                              <div id="sectionContent" class="after-add-more"></div>

                         </div>
                         @endif
                    </div>
               </div>
          </div>

          <!-- model -->
          <div class="modal fade" id="exampleModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
               <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                         <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Edit Section</h5>
                              <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                              </button>
                         </div>
                         <div class="modal-body">
                              <form id="edit_video_section11" name="edit_video_section11" autocomplete="off">
                                   @csrf
                                   <div class="form-row">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label> {{__('Label.Title')}} </label>
                                                  <input name="title" type="text" class="form-control" id="edit_title"
                                                       placeholder="Enter Your Title">
                                             </div>
                                        </div>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label for="screen_layout">Screen Layout</label>
                                                  <select class="form-control" name="screen_layout" id="edit_screen_layout">
                                                       <option value="landscape">Landscape</option>
                                                       <option value="square">Square</option>
                                                       <option value="potrait">Potrait</option>
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6 Model_Video_Type">
                                             <div class="form-group">
                                                  <label>{{__('Label.Video Type')}}</label>
                                                  <select class="form-control" name="video_type" id="edit_video_type">
                                                       <option value="" selected disabled>{{__('Label.Select Video Type')}}</option>
                                                       <option value="1">{{__('Label.Video')}}</option>
                                                       <option value="2">{{__('Label.Show')}}</option>
                                                       <option value="3">{{__('Label.Language')}}</option>
                                                       <option value="4">{{__('Label.Category')}}</option>
                                                       <option value="5">Upcoming</option>
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6 Model_Type_Id">
                                             <div class="form-group Model_Type_Id_Inner">
                                                  <label>{{__('Label.Type')}}</label>
                                                  <select class="form-control" name="type_id" id="edit_type_id" style="width:100%!important;">
                                                       <option value="" selected disabled> Select Type </option>
                                                       @foreach ($type as $key => $value)
                                                            <option value="{{ $value->id}}">{{ $value->name }}</option>
                                                       @endforeach
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6 Model_Upcoming_Type">
                                             <div class="form-group">
                                                  <label>Upcoming Type</label>
                                                  <select class="form-control" name="upcoming_type" id="edit_upcoming_type">
                                                       <option value="" selected disabled>Select Type</option>
                                                       <option value="1">{{__('Label.Video')}}</option>
                                                       <option value="2">{{__('Label.Show')}}</option>
                                                  </select>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="form-row">
                                        <div class="col-md-12 mb-3">
                                             <div class="form-group option_class_video">
                                                  <label>{{__('Label.Video')}}</label>
                                                  <select class="form-control selectd2" style="width:100%!important;"
                                                       name="video_id[]" multiple id="edit_video_id">

                                                  </select>
                                             </div>
                                        </div>
                                   </div>
                                   <input type="hidden" name="edit_id" id="edit_id">
                              </form>
                         </div>
                         <div class="modal-footer">
                              <button type="button" class="btn btn-default mw-120"
                                   onclick="Edit_Video_Section1(Is_home_screen, Type_id)">Update</button>
                              <button type="button" class="btn btn-cancel mw-120" data-dismiss="modal">Close</button>
                         </div>
                    </div>
               </div>
          </div>
     </div>
@endsection

@section('pagescript')
     <script>
	$(document).ready(function () {
		$('#sectionContent').sortable({
			handle:".sectionContentSortingHandle",
			forcePlaceholderSize : true,
			update: function(event, ui) {
				var position = ui.item.index();
				var section_id = ui.item.attr('data-section-id');
				var type_id = ui.item.attr('data-type-id');
				var is_home_screen = ui.item.attr('data-is_home_screen');
				$.ajax({
					url: "{{ route('updateVideoSectionSequence') }}",
					data: {position, section_id, type_id, is_home_screen},
					type: 'POST',
					success: function(response){
                              
						if(response.status == 200){
							$('.section_type_tab_'+type_id+'_'+is_home_screen).trigger('click');
						} else {
							alert('Error on update sequence.');
						}
					}
				});
			}
		});
		$( "#sectionContent" ).disableSelection();
	});
	
	function initVideoSort(sectionId) {
		$('.video_sort_'+sectionId).sortable({
			items:"p",
			forcePlaceholderSize : true,
			zIndex: 10000, //or greater than any other relative/absolute/fixed elements and droppables
			update: function(event, ui) {
				var position = ui.item.index();
				//var video_id = ui.item.attr('data-video-id');
				//var type_id = ui.item.attr('data-type-id');
				//var is_home_screen = ui.item.attr('data-is_home_screen');
				var ids = '';
				$(".video_sort_"+sectionId).children().each(function() {
					ids += $(this).attr("data-video-id")+',';
				});
				$.ajax({
					url: "{{ route('updateVideoSectionSequenceVideos') }}",
					data: {ids, section_id:sectionId},
					type: 'POST',
					success: function(response){
						if(response.status != 200){
							alert('Error on update video sequence.');
						}
					}
				});
			}
		});
		$( ".video_sort_"+sectionId ).disableSelection();
	}
	
          $("#video_id").select2();
          $(".selectd2").select2({
               placeholder: "Select Video"
          });

          // Type_id && Is_home_Screen 
          var Tab = $("ul.tabs li a.active");
          var Is_home_screen = Tab.data("is_home_screen");
          var Type_id = Tab.data("id");
          var Type_type;
          $(".upcoming_type").hide();

          $('.nav-item a').on('click', function() {

               Is_home_screen = $(this).data("is_home_screen");
               Type_id = $(this).data("id");

               if (Is_home_screen == 2) {

                    $("#video_id").empty();

                    Type_type = $(this).data("type");
                    document.getElementById("save_video_section").reset();

                    $(".Top_Video_Type").hide();
                    $('.Top_Type').hide();
                    $('.upcoming_type').hide();
                    if(Type_type == 5){
                         $(".upcoming_type").show();
                    }

                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Type_type,
                              Type_Id: Type_id
                         },
                         success: function(resp) {
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               } else {

                    $("#video_id").empty();

                    $(".Top_Video_Type").show();
                    $('.Top_Type').show();
                    $(".upcoming_type").hide();

                    document.getElementById("save_video_section").reset();
               }
          });

          // Data get in Video_Type && Type Dropdown
          $("#video_type").change(function() {

               var Video_Type = $(this).children("option:selected").val();
               var Type_Id = $('#type_id').find(":selected").val();

               if(Video_Type == 5){
                    $(".upcoming_type").show();
                    var Upcoming_Type = $('#upcoming_type').find(":selected").val();
               } else {
                    $(".upcoming_type").hide();
               }

               $("#video_id").empty();
               if (Video_Type == 3 || Video_Type == 4) {
                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type
                         },
                         success: function(resp) {
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               } else if (Video_Type == 1 || Video_Type == 2 && Type_Id != "" && Type_Id != null) {
                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type,
                              Type_Id: Type_Id
                         },
                         success: function(resp) {
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               } else if (Video_Type == 5 && Type_Id != "" && Type_Id != null) {
                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type,
                              Type_Id: Type_Id,
                              Upcoming_Type: Upcoming_Type,
                         },
                         success: function(resp) {
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               }else if (Video_Type == 0 && Type_Id != "" && Type_Id != null) {
                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type,
                              Type_Id: Type_Id,
                              Upcoming_Type: Upcoming_Type,
                         },
                         success: function(resp) {
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               }
          });
          $("#type_id").change(function() {

               var Type_Id = $(this).children("option:selected").val();
               var Video_Type = $('#video_type').find(":selected").val();

               if (Video_Type == 1 || Video_Type == 2) {
                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type,
                              Type_Id: Type_Id
                         },
                         success: function(resp) {
                              $("#video_id").empty();
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               } else if (Video_Type == 5) {
               
                    var Upcoming_Type = $('#upcoming_type').find(":selected").val();

                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type,
                              Type_Id: Type_Id,
                              Upcoming_Type: Upcoming_Type
                         },
                         success: function(resp) {
                              $("#video_id").empty();
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               } else if (Video_Type == 0) {
               
                    var Upcoming_Type = $('#upcoming_type').find(":selected").val();

                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type,
                              Type_Id: Type_Id,
                              Upcoming_Type: Upcoming_Type
                         },
                         success: function(resp) {
                              $("#video_id").empty();
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].title}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               }
          });
          $("#upcoming_type").change(function() {

               var Type_Id = $('.nav-item a.nav-link.active').data("id");
               var Upcoming_Type = $(this).children("option:selected").val();
               if(Is_home_screen == 1){
                    var Video_Type = $('#video_type').find(":selected").val();
               } else {
                    var Video_Type = $('.nav-item a.nav-link.active').data("type");
               }

               $.ajax({
                    type: 'get',
                    url: '{{ route("GetLangOrCat") }}',
                    data: {
                         Video_Type: Video_Type,
                         Type_Id: Type_Id,
                         Upcoming_Type: Upcoming_Type
                    },
                    success: function(resp) {
                         $("#video_id").empty();
                         for (var i = 0; i < resp.result.length; i++) {
                              $('#video_id').append(
                                   `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                              );
                         }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                         toastr.error(errorThrown.msg, 'failed');
                    }
               });
          });

          // Page Wise Data Get (Type)
          if (Is_home_screen == 1) {
               $.ajax({
                    headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                         is_home_screen: Is_home_screen
                    },
                    url: '{{ route("GetSectionData") }}',
                    success: function(resp) {
                         console.log("resp___", resp);
                         for (var i = 0; i < resp.result.length; i++) {
                              var typeHideClass = '';
                              if(resp.result[i].type_id == 15) {
                                   var typeHideClass = 'hide';
                              }
                              if (resp.result[i].video_type == 1) {
                                   var Video_Type = "Video";
                              } else if (resp.result[i].video_type == 2) {
                                   var Video_Type = "Show";
                              } else if (resp.result[i].video_type == 3) {
                                   var Video_Type = "Language";
                              } else if (resp.result[i].video_type == 4) {
                                   var Video_Type = "Category";
                              } else if (resp.result[i].video_type == 5) {
                                   var Video_Type = "Upcoming";
                              }

                              if (resp.result[i].screen_layout == "landscape") {
                                   var Screen_Layout = "Landscape";
                              } else if (resp.result[i].screen_layout == "square") {
                                   var Screen_Layout = "Square";
                              } else if (resp.result[i].screen_layout == "potrait") {
                                   var Screen_Layout = "Potrait";
                              }

                              var data ='<div class="custom-border-card" data-section-id="'+resp.result[i].id+'" data-type-id="'+resp.result[i].type_id+'" data-is_home_screen="1">' +
                                             '<div class="form-row sectionContentSortingHandle">' +
                                                  '<div class="col-md-4">' +
                                                       '<div class="form-group">' +
                                                            '<label> {{__("Label.Title")}} </label>' +
                                                            '<input name="title" type="text" class="form-control" id="title" value="' +  resp.result[i].title + '" placeholder="Enter Your Title" readonly>' +
                                                       '</div>' +
                                                  '</div>' +
                                                  '<div class="col-md-2">' +
                                                       '<div class="form-group">' +
                                                            '<label>{{__("Label.Video Type")}}</label>' +
                                                            '<input type="text" class="form-control" name="video_type" id="video_type" value="' + Video_Type + '" style="width:100%!important;" readonly/>' +
                                                       '</div>' +
                                                  '</div>' +
                                                  '<div class="col-md-2 '+typeHideClass+'">' +
                                                       '<div class="form-group">' +
                                                            '<label>{{__("Label.Type")}}</label>' +
                                                            '<input type="text" class="form-control" name="type_id" id="type_id" value="' +resp.result[i].type.name + '" style="width:100%!important;" readonly/>' +
                                                       '</div>' +
                                                  '</div>' +
                                                  '<div class="col-md-2">' +
                                                       '<div class="form-group">' +
                                                            '<label>Screen Layout</label>' +
                                                            '<input type="text" class="form-control" name="screen_layout" id="screen_layout" value="' + Screen_Layout + '" style="width:100%!important;" readonly/>' +
                                                       '</div>' +
                                                  '</div>' +
                                                  '<div class="col-md-2 mt-2">' +
                                                       '<div class="flex-grow-1 px-5 d-inline-flex">' +
                                                            '<div class="mr-3">' +
                                                                 '<label>&nbsp;</label><br/>' +
                                                                 '<a data-toggle="modal" data-target="#exampleModal" onclick="EditSection(' + resp.result[i].id + ', ' + resp.result[i].video_type + ', ' + resp.result[i].type_id + ', ' + resp.result[i].upcoming_type + ', 1)" class="btn btn-info"> <i class="fa-regular text-white fa-pen-to-square"></i> </a>' +
                                                            '</div>' +
                                                            '<div class="change">' +
                                                                 '<label>&nbsp;</label><br/>' +
                                                                 '<a onclick="DeleteSection(' + resp.result[i].id +')" class="btn btn-danger remove"> <i class="fa-solid text-white fa-trash-can"></i> </a>' +
                                                            '</div>' +
                                                       '</div>' +
                                                  '</div>' +
                                             '</div>' +
                                             '<div class="form-row">' +
                                                  '<div class="col-md-12">' +
                                                       '<div class="form-group">' +
                                                            '<label>Video list</label><br>' +
                                                            '<div class="pt-2 pl-2 pr-2 video_sort_' + resp.result[i].id + '">' +
                                                                 resp.result[i].video_list +
                                                            '</div>' +
                                                       '</div>' +
                                                  '</div>' +
                                             '</div>' +
                                        '</div>';

                              $('.after-add-more').append(data);
							  initVideoSort(resp.result[i].id);
                         }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                         toastr.error(errorThrown.msg, 'failed');
                    }
               });
          }
          function Selected_Type(type_id, Type, is_home_screen) {
               //console.log("is_home_screen", is_home_screen);
               $.ajax({
                    headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{ route("GetSectionData") }}',
                    data: {
                         type_id: type_id,
                         is_home_screen: is_home_screen
                    },
                    success: function(resp) {
                         $('.after-add-more').html('');

                         for (var i = 0; i < resp.result.length; i++) {
							 //console.log(resp.result[i]);
                              if (resp.result[i].video_type == 1) {
                                   var Video_Type = "Video";
                              } else if (resp.result[i].video_type == 2) {
                                   var Video_Type = "Show";
                              } else if (resp.result[i].video_type == 3) {
                                   var Video_Type = "Language";
                              } else if (resp.result[i].video_type == 4) {
                                   var Video_Type = "Category";
                              } else if (resp.result[i].video_type == 5) {
                                   var Video_Type = "Upcoming";
                              }

                              if (resp.result[i].screen_layout == "landscape") {
                                   var Screen_Layout = "Landscape";
                              } else if (resp.result[i].screen_layout == "square") {
                                   var Screen_Layout = "Square";
                              } else if (resp.result[i].screen_layout == "potrait") {
                                   var Screen_Layout = "Potrait";
                              }

                              if (is_home_screen == 1) {
                                   var data = '<div class="custom-border-card"  data-section-id="'+resp.result[i].id+'" data-type-id="'+resp.result[i].type_id+'" data-is_home_screen="1">' +
                                                  '<div class="form-row sectionContentSortingHandle">' +
                                                       '<div class="col-md-4">' +
                                                            '<div class="form-group">' +
                                                                 '<label> {{__("Label.Title")}} </label>' +
                                                                 '<input name="title" type="text" class="form-control" id="title" value="' +resp.result[i].title + '" placeholder="Enter Your Title" readonly>' +
                                                            '</div>' +
                                                       '</div>' +
                                                       '<div class="col-md-2">' +
                                                            '<div class="form-group">' +
                                                                 '<label for="video_type">{{__("Label.Video Type")}}</label>' +
                                                                 '<input type="text" class="form-control" name="video_type" id="video_type" value="' +Video_Type + '" style="width:100%!important;" readonly/>' +
                                                            '</div>' +
                                                       '</div>' +
                                                       '<div class="col-md-2">' +
                                                            '<div class="form-group">' +
                                                                 '<label>{{__("Label.Type")}}</label>' +
                                                                 '<input type="text" class="form-control" name="type_id" id="type_id" value="' + resp.result[i].type.name + '" style="width:100%!important;" readonly/>' +
                                                            '</div>' +
                                                       '</div>' +
                                                       '<div class="col-md-2">' +
                                                            '<div class="form-group">' +
                                                                 '<label for="screen_layout">Screen Layout</label>' +
                                                                 '<input type="text" class="form-control" name="screen_layout" id="screen_layout" value="' +Screen_Layout + '" style="width:100%!important;" readonly/>' +
                                                            '</div>' +
                                                       '</div>' +
                                                       '<div class="col-md-2 mt-2">' +
                                                            '<div class="flex-grow-1 px-5 d-inline-flex">' +
                                                                 '<div class="mr-3">' +
                                                                      '<label>&nbsp;</label><br/>' +
                                                                      '<a data-toggle="modal" data-target="#exampleModal" onclick="EditSection(' +resp.result[i].id + ', ' + resp.result[i].video_type + ', ' + resp.result[i].type_id + ', ' + resp.result[i].upcoming_type + ', 1)" class="btn btn-info"> <i class="fa-regular text-white fa-pen-to-square"></i> </a>' +
                                                                 '</div>' +
                                                                 '<div class="change">' +
                                                                      '<label>&nbsp;</label><br/>' +
                                                                      '<a onclick="DeleteSection(' + resp.result[i].id + ')" class="btn btn-danger remove"> <i class="fa-solid text-white fa-trash-can"></i> </a>' +
                                                                 '</div>' +
                                                            '</div>' +
                                                       '</div>' +
                                                  '</div>' +
                                                  '<div class="form-row">' +
                                                       '<div class="col-md-12">' +
                                                            '<div class="form-group">' +
                                                                 '<label for="video_list">Video list</label><br>' +
                                                                 '<div class="pt-2 pl-2 pr-2 video_sort_' + resp.result[i].id + '">' +
                                                                      resp.result[i].video_list +
                                                                 '</div>' +
                                                            '</div>' +
                                                       '</div>' +
                                                  '</div>' +
                                             '</div>';

                              } else if (is_home_screen == 2) {
                                   var data = '<div class="custom-border-card" data-section-id="'+resp.result[i].id+'" data-type-id="'+resp.result[i].type_id+'" data-is_home_screen="2">' +
                                                  '<div class="form-row sectionContentSortingHandle">' +
                                                       '<div class="col-md-4">' +
                                                            '<div class="form-group">' +
                                                                 '<label> {{__("Label.Title")}} </label>' +
                                                                 '<input name="title" type="text" class="form-control" id="title" value="' + resp.result[i].title + '" placeholder="Enter Your Title" readonly>' +
                                                            '</div>' +
                                                       '</div>' +
                                                       '<div class="col-md-2">' +
                                                            '<div class="form-group">' +
                                                                 '<label for="video_type">{{__("Label.Video Type")}}</label>' +
                                                                 '<input type="text" class="form-control" name="video_type" id="video_type" value="' +Video_Type + '" style="width:100%!important;" readonly/>' +
                                                            '</div>' +
                                                       '</div>' +
                                                       '<div class="col-md-2">' +
                                                            '<div class="form-group">' +
                                                                 '<label>{{__("Label.Type")}}</label>' +
                                                                 '<input type="text" class="form-control" name="type_id" id="type_id" value="' +resp.result[i].type.name + '" style="width:100%!important;" readonly/>' +
                                                            '</div>' +
                                                       '</div>' +
                                                       '<div class="col-md-2">' +
                                                            '<div class="form-group">' +
                                                                 '<label for="screen_layout">Screen Layout</label>' +
                                                                 '<input type="text" class="form-control" name="screen_layout" id="screen_layout" value="' +Screen_Layout + '" style="width:100%!important;" readonly/>' +
                                                            '</div>' +
                                                       '</div>' +
                                                       '<div class="col-md-2 mt-2">' +
                                                            '<div class="flex-grow-1 px-5 d-inline-flex">' +
                                                                 '<div class="mr-3">' +
                                                                      '<label>&nbsp;</label><br/>' +
                                                                      '<a data-toggle="modal" data-target="#exampleModal" onclick="EditSection(' + resp.result[i].id + ', ' + resp.result[i].video_type + ', ' + resp.result[i].type_id + ', ' + resp.result[i].upcoming_type + ', 2)" class="btn btn-info"> <i class="fa-regular text-white fa-pen-to-square"></i> </a>' +
                                                                 '</div>' +
                                                                 '<div class="change">' +
                                                                      '<label>&nbsp;</label><br/>' +
                                                                      '<a onclick="DeleteSection(' + resp.result[i].id + ')" class="btn btn-danger remove"> <i class="fa-solid text-white fa-trash-can"></i> </a>' +
                                                                 '</div>' +
                                                            '</div>' +
                                                       '</div>' +
                                                  '</div>' +
                                                  '<div class="form-row">' +
                                                       '<div class="col-md-12">' +
                                                            '<div class="form-group">' +
                                                                 '<label>Video list</label><br>' +
                                                                 '<div class="pt-2 pl-2 pr-2 video_sort_' + resp.result[i].id + '">' + resp.result[i].video_list +
                                                                 '</div>' +
                                                            '</div>' +
                                                       '</div>' +
                                                  '</div>' +
                                             '</div>';
                              }

                              $('.after-add-more').append(data);
								initVideoSort(resp.result[i].id);
                              var value = resp.result[i].video_type;
                              $(".video_type" + i + " option").each(function() {
                                   if ($(this).val() == value) {
                                        $(this).attr("selected", "selected");
                                   }
                              });

                              if (Is_home_screen == 1) {
                                   var value1 = resp.result[i].type_id;
                                   $(".type_id" + i + " option").each(function() {
                                        if ($(this).val() == value1) {
                                             $(this).attr("selected", "selected");
                                        }
                                   });
                              } else {
                                   $(".type_id" + i).val(resp.result[i].type.id);
                                   // $(".type_id"+i).attr("placeholder", resp.result[i].type.name);
                              }
                         }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                         toastr.error(errorThrown.msg, 'failed');
                    }
               });
          };

          // Save Section
          function Save_Video_Section(Is_home_screen, Type_id) {

               var formData = new FormData($("#save_video_section")[0]);
               formData.append('is_home_screen', Is_home_screen);
               if (Is_home_screen == 2 && Type_id != "") {
                    formData.append('type_id', Type_id);
                    formData.append('video_type', Type_type);
               }

               $("#dvloader").show();
               $.ajax({
                    type: 'POST',
                    url: '{{ route("VideoSectionSave") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                         $("#dvloader").hide();
                         get_responce_message2(resp, 'save_video_section', '{{ route("VideoSection") }}', 1);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                         $("#dvloader").hide();
                         toastr.error(errorThrown.msg, 'failed');
                    }
               });
          }

          // Edit Section
          function EditSection(id, video_type, type_id, upcoming_type, is_home_page) {
               if(is_home_page == 1 && type_id == 15) {
                    $(".Model_Type_Id_Inner").hide();
               } else {
                    $(".Model_Type_Id_Inner").show();
               }
               $.ajax({
                    headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{ route("VideoSectionUpdate") }}',
                    data: {
                         id: id,
                         video_type: video_type,
                         type_id: type_id,
                         upcoming_type: upcoming_type,
                         is_home_page,
                         request_from:"homeSection"
                    },
                    success: function(resp) {
                         //console.log(resp);
                         $("#edit_video_id").empty();
                         $(".Model_Upcoming_Type").hide();

                         $("#edit_title").val(function() {
                              return resp.result.title;
                         });
                         $("#edit_id").val(function() {
                              return resp.result.id;
                         });

                         if (is_home_page == 2) {
                              $(".Model_Video_Type").hide();
                              $(".Model_Type_Id").hide();
                         }
                         if(resp.result.video_type == 5){
                              $(".Model_Upcoming_Type").show();
                         }

                         $('#edit_video_type').find('option[value="' + resp.result.video_type + '"]').prop('selected', true);
                         $('#edit_type_id').find('option[value="' + resp.result.type_id + '"]').prop('selected',true);
                         $('#edit_screen_layout').find('option[value="' + resp.result.screen_layout + '"]').prop('selected', true);
                         $('#edit_upcoming_type').find('option[value="' + resp.result.upcoming_type + '"]').prop('selected', true);

                         // Seleted Video Type
                         var data = resp.result.video_type;
                         for (var i = 0; i < resp.video.length; i++) {
                              $('#edit_video_id').append(`<option value="${resp.video[i].id}">${resp.video[i].name}</option>`);
                         }

                         var value1 = resp.result.video_id;
                         var array = value1.split(",");
                         for (let k = 0; k < array.length; k++) {
                              $("#edit_video_id option").each(function() {
                                   if ($(this).val() == array[k]) {
                                        $(this).attr("selected", "selected");
                                   }
                              });
                         }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                         toastr.error(errorThrown.msg, 'failed');
                    }
               });
          }

          $("#edit_video_type").change(function() {

               var Video_Type = $(this).children("option:selected").val();
               var Type_Id = $('#edit_type_id').find(":selected").val();

               if(Video_Type == 5){
                    $(".Model_Upcoming_Type").show();
                    var Upcoming_Type = $('#edit_upcoming_type').find(":selected").val();
               } else {
                    $(".Model_Upcoming_Type").hide();
               }

               $("#edit_video_id").empty();
               if (Video_Type == 3 || Video_Type == 4) {
                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type
                         },
                         success: function(resp) {
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#edit_video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               } else if (Video_Type == 1 || Video_Type == 2 && Type_Id != "" && Type_Id != null) {
                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type,
                              Type_Id: Type_Id
                         },
                         success: function(resp) {
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#edit_video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               } else if (Video_Type == 5 && Type_Id != "" && Type_Id != null) {
                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type,
                              Type_Id: Type_Id,
                              Upcoming_Type: Upcoming_Type,
                         },
                         success: function(resp) {
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#edit_video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               }
               else if (Video_Type == 0 && Type_Id != "" && Type_Id != null) {
                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type,
                              Type_Id: Type_Id,
                              Upcoming_Type: Upcoming_Type,
                         },
                         success: function(resp) {
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#edit_video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].title}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               }
          });
          $("#edit_type_id").change(function() { 
               var Type_Id = $(this).children("option:selected").val();
               var Video_Type = $('#edit_video_type').find(":selected").val();

               if (Video_Type == 1 || Video_Type == 2) {
                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type,
                              Type_Id: Type_Id
                         },
                         success: function(resp) {
                              $("#edit_video_id").empty();
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#edit_video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               } else if (Video_Type == 5) {

                    var Upcoming_Type = $('#edit_upcoming_type').find(":selected").val();

                    $.ajax({
                         type: 'get',
                         url: '{{ route("GetLangOrCat") }}',
                         data: {
                              Video_Type: Video_Type,
                              Type_Id: Type_Id,
                              Upcoming_Type: Upcoming_Type,
                         },
                         success: function(resp) {
                              $("#edit_video_id").empty();
                              for (var i = 0; i < resp.result.length; i++) {
                                   $('#edit_video_id').append(
                                        `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                                   );
                              }
                         },
                         error: function(XMLHttpRequest, textStatus, errorThrown) {
                              toastr.error(errorThrown.msg, 'failed');
                         }
                    });
               }
          });
          $("#edit_upcoming_type").change(function() {

               var Type_Id = $('#edit_type_id').find(":selected").val();
               var Video_Type = $('#edit_video_type').find(":selected").val();
               var Upcoming_Type = $(this).children("option:selected").val();

               $.ajax({
                    type: 'get',
                    url: '{{ route("GetLangOrCat") }}',
                    data: {
                         Video_Type: Video_Type,
                         Type_Id: Type_Id,
                         Upcoming_Type: Upcoming_Type
                    },
                    success: function(resp) {
                         $("#edit_video_id").empty();
                         for (var i = 0; i < resp.result.length; i++) {
                              $('#edit_video_id').append(
                                   `<option value="${resp.result[i].id}">${resp.result[i].name}</option>`
                              );
                         }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                         toastr.error(errorThrown.msg, 'failed');
                    }
               });
          });

          // Update
          function Edit_Video_Section1(Is_home_screen, Type_id) {

               var formData = new FormData($("#edit_video_section11")[0]);
               formData.append('is_home_screen', Is_home_screen);
               if (Is_home_screen == 2 && Type_id != "") {
                    formData.append('type_id', Type_id);
               }

               $("#dvloader").show();
               $.ajax({
                    type: 'POST',
                    url: '{{ route("VideoSectionUpdate1") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                         $("#dvloader").hide();
                         get_responce_message2(resp, 'edit_video_section11', '{{ route("VideoSection") }}', 3);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                         $("#dvloader").hide();
                         toastr.error(errorThrown.msg, 'failed');
                    }
               });
          }

          // Delete Section 
          function DeleteSection(id) {
               var url = "{{route('deleteVideoSection', '')}}" + "/" + id;

               $("#dvloader").show();
               $.ajax({
                    type: 'get',
                    url: url,
                    success: function(resp) {
                         $("#dvloader").hide();
                         get_responce_message2(resp, 'save_video_section', '{{ route("VideoSection") }}', 2);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                         $("#dvloader").hide();
                         toastr.error(errorThrown.msg, 'failed');
                    }
               });
          }
     </script>
@endsection