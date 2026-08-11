<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\Type;
use App\Models\TVShow;
use App\Models\Category;
use App\Models\Language;
use App\Models\Video;
use App\Models\WebSeries;
use App\Models\App_Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Exception;

// Video Type = 1-Video, 2-Show, 3-Language, 4-Category, 5-Upcoming
class VideoSectionController extends Controller
{
    public function index()
    {
        try {

            $type = Type::orderby('position')->get();
            // dd($type->toArray());
            return view('admin.video_section.index', ['type' => $type]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function save(Request $request)
    {
	 
	 
        try {
            if (Auth::guard('admin')->user()->type != 1) {
                return response()->json(array('status' => 400, 'errors' => __('Label.You have no right to add, edit, and delete')));
            } else {

                if ($request->is_home_screen == 1) {
                    $validator = Validator::make($request->all(), [
                        'video_type' => 'required',
                        'title' => 'required|min:2',
                        'type_id' => 'required',
                        'video_id' => 'required',
                    ]);
                } else {
                    $validator = Validator::make($request->all(), [
                        'title' => 'required|min:2',
                        'type_id' => 'required',
                        'video_id' => 'required',
                    ]);
                }

                if ($validator->fails()) {
                    $errs = $validator->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }

                $data = new App_Section();

                $data->type_id = $request->type_id;
                $data->title = $request->title;
                $data->video_type = $request->video_type;
                $data->screen_layout = $request->screen_layout;
                $data->is_home_screen = $request->is_home_screen;
                $data->upcoming_type = isset($request->upcoming_type) ? $request->upcoming_type : 0;
                $VideoId = implode(',', $request->video_id);
                $data->video_id = $VideoId;

		if($request->type_id =="32"){
			$data->is_webseries = 1;
		}

                if ($data->save()) {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function get_all_data(Request $request)
    {
        
        try {
            if ($request->Video_Type == 0) {
                if ($request->Type_Id != "" && $request->Type_Id != null) {
                    $data = WebSeries::where('isActive', 1)
                    ->select('*', 'title as name')
                    ->get();
                    
                    return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $data));
                } else {
                    return response()->json(array('status' => 400, 'errors' => "Data Not Found"));
                }
            }
            else if ($request->Video_Type == 1) {
                if ($request->Type_Id != "" && $request->Type_Id != null) {

                    $data = Video::where('type_id', $request->Type_Id)->where('video_type', $request->Video_Type)->get();
                    return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $data));
                } else {
                    return response()->json(array('status' => 400, 'errors' => "Data Not Found"));
                }
            } else if ($request->Video_Type == 2) {

                if ($request->Type_Id != "" && $request->Type_Id != null) {

                    $data = TVShow::where('type_id', $request->Type_Id)->where('video_type', $request->Video_Type)->get();
                    return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $data));
                } else {
                    return response()->json(array('status' => 400, 'errors' => "Data Not Found"));
                }
            } else if ($request->Video_Type == 3) {

                $data = Language::get();
                return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $data));
            } else if ($request->Video_Type == 4) {

                $data = Category::get();
                return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $data));
            } else if ($request->Video_Type == 5) {

                if ($request->Type_Id != "" && $request->Type_Id != null && $request->Upcoming_Type != "" && $request->Upcoming_Type != null) {

                    if ($request->Upcoming_Type == 1) {

                        $data = Video::where('type_id', $request->Type_Id)->where('video_type', $request->Video_Type)->get();
                    } elseif ($request->Upcoming_Type == 2) {
                        $data = TVShow::where('type_id', $request->Type_Id)->where('video_type', $request->Video_Type)->get();
                    }
                    return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $data));
                } else {
                    return response()->json(array('status' => 400, 'errors' => "Data Not Found"));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function GetSectionData(Request $request)
    {
        try {
            if ($request->is_home_screen == 1) {

                $data = App_Section::where('is_home_screen', 1)->with('type')->orderBy('position')->get();

                for ($i = 0; $i < count($data); $i++) {

                    $Multipal_Ids = explode(",", $data[$i]['video_id']);
					$videoIds = $data[$i]['video_id'];
					$data[$i]['video_list'] = "";
                    if ($data[$i]['video_type'] == 1) {
                        if($data[$i]['type_id'] == 15) {
                            $videos = Video::select('id', 'name')->whereIn('id', $Multipal_Ids)->orderByRaw("FIELD(id, $videoIds)")->get();
                        } else {
                            $videos = Video::select('id', 'name')->whereIn('id', $Multipal_Ids)->where('type_id', $data[$i]['type_id'])->orderByRaw("FIELD(id, $videoIds)")->get();
                        }
                        /*$videos = array();
                        for ($j = 0; $j < count($video); $j++) {
                            $videos[] = $video[$j]['name'];
                        }

                        if (isset($videos)) {
                            $html = [];
                            for ($k = 0; $k < count($videos); $k++) {
                                $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                            }
                            $html = implode(" ", $html);
                            $data[$i]['video_list'] = $html;
                        } else {
                            $data[$i]['video_list'] = "";
                        }*/
                    } else if ($data[$i]['video_type'] == 2) {

                        $videos = TVShow::select('id', 'name')->whereIn('id', $Multipal_Ids)->where('type_id', $data[$i]['type_id'])->orderByRaw("FIELD(id, $videoIds)")->get();

                        /*$videos = array();
                        for ($j = 0; $j < count($video); $j++) {
                            $videos[] = $video[$j]['name'];
                        }

                        if (isset($videos)) {
                            $html = [];
                            for ($k = 0; $k < count($videos); $k++) {
                                $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                            }
                            $html = implode(" ", $html);
                            $data[$i]['video_list'] = $html;
                        } else {
                            $data[$i]['video_list'] = "";
                        }*/
                    } else if ($data[$i]['video_type'] == 3) {

                        $videos = Language::select('id', 'name')->whereIn('id', $Multipal_Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                        /*$videos = array();
                        for ($j = 0; $j < count($video); $j++) {
                            $videos[] = $video[$j]['name'];
                        }

                        if (isset($videos)) {
                            $html = [];
                            for ($k = 0; $k < count($videos); $k++) {
                                $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                            }
                            $html = implode(" ", $html);
                            $data[$i]['video_list'] = $html;
                        } else {
                            $data[$i]['video_list'] = "";
                        }*/
                    } else if ($data[$i]['video_type'] == 4) {

                        $videos = Category::select('id', 'name')->whereIn('id', $Multipal_Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                        /*$videos = array();
                        for ($j = 0; $j < count($video); $j++) {
                            $videos[] = $video[$j]['name'];
                        }

                        if (isset($videos)) {
                            $html = [];
                            for ($k = 0; $k < count($videos); $k++) {
                                $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                            }
                            $html = implode(" ", $html);
                            $data[$i]['video_list'] = $html;
                        } else {
                            $data[$i]['video_list'] = "";
                        }*/
                    } else if ($data[$i]['video_type'] == 5) {

                        if ($data[$i]['upcoming_type'] == 1) {

                            $videos = Video::select('id', 'name')->whereIn('id', $Multipal_Ids)->where('type_id', $data[$i]['type_id'])->orderByRaw("FIELD(id, $videoIds)")->get();

                            /*$videos = array();
                            for ($j = 0; $j < count($video); $j++) {
                                $videos[] = $video[$j]['name'];
                            }

                            if (isset($videos)) {
                                $html = [];
                                for ($k = 0; $k < count($videos); $k++) {
                                    $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                                }
                                $html = implode(" ", $html);
                                $data[$i]['video_list'] = $html;
                            } else {
                                $data[$i]['video_list'] = "";
                            }*/
                        } else if ($data[$i]['upcoming_type'] == 2) {

                            $videos = TVShow::select('id', 'name')->whereIn('id', $Multipal_Ids)->where('type_id', $data[$i]['type_id'])->orderByRaw("FIELD(id, $videoIds)")->get();

                            /*$videos = array();
                            for ($j = 0; $j < count($video); $j++) {
                                $videos[] = $video[$j]['name'];
                            }

                            if (isset($videos)) {
                                $html = [];
                                for ($k = 0; $k < count($videos); $k++) {
                                    $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                                }
                                $html = implode(" ", $html);
                                $data[$i]['video_list'] = $html;
                            } else {
                                $data[$i]['video_list'] = "";
                            }*/
                        }
                    }
					if(!empty($videos)) {
						foreach($videos as $v) {
							$data[$i]['video_list'] .= '<p data-video-id="'.$v->id.'" class="btn btn-outline-dark btn-sm mr-2">' . $v->name . '</p>';
						}
					}
				}
               
                return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $data));
            } else if ($request->is_home_screen == 2) {
                $data = App_Section::where('is_home_screen', 2)->with('type')->where('type_id', $request->type_id)->orderBy('position')->get();

                for ($i = 0; $i < count($data); $i++) {

                    $Multipal_Ids = explode(",", $data[$i]['video_id']);
					$videoIds = $data[$i]['video_id'];
					$data[$i]['video_list'] = "";
                    if ($data[$i]['video_type'] == 1) {

                        $videos = Video::select('id', 'name')->whereIn('id', $Multipal_Ids)->where('type_id', $data[$i]['type_id'])->orderByRaw("FIELD(id, $videoIds)")->get();

                        /*$videos = array();
                        for ($j = 0; $j < count($video); $j++) {
                            $videos[] = $video[$j]['name'];
                        }

                        if (isset($videos)) {
                            $html = [];
                            for ($k = 0; $k < count($videos); $k++) {
                                $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                            }
                            $html = implode(" ", $html);
                            $data[$i]['video_list'] = $html;
                        } else {
                            $data[$i]['video_list'] = "";
                        }*/
                    } else if ($data[$i]['video_type'] == 2) {

                        $videos = TVShow::select('id', 'name')->whereIn('id', $Multipal_Ids)->where('type_id', $data[$i]['type_id'])->orderByRaw("FIELD(id, $videoIds)")->get();

                        /*$videos = array();
                        for ($j = 0; $j < count($video); $j++) {
                            $videos[] = $video[$j]['name'];
                        }

                        if (isset($videos)) {
                            $html = [];
                            for ($k = 0; $k < count($videos); $k++) {
                                $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                            }
                            $html = implode(" ", $html);
                            $data[$i]['video_list'] = $html;
                        } else {
                            $data[$i]['video_list'] = "";
                        }*/
                    } else if ($data[$i]['video_type'] == 3) {

                        $videos = Language::select('id', 'name')->whereIn('id', $Multipal_Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                        /*$videos = array();
                        for ($j = 0; $j < count($video); $j++) {
                            $videos[] = $video[$j]['name'];
                        }

                        if (isset($videos)) {
                            $html = [];
                            for ($k = 0; $k < count($videos); $k++) {
                                $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                            }
                            $html = implode(" ", $html);
                            $data[$i]['video_list'] = $html;
                        } else {
                            $data[$i]['video_list'] = "";
                        }*/
                    } else if ($data[$i]['video_type'] == 4) {

                        $videos = Category::select('id', 'name')->whereIn('id', $Multipal_Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                        /*$videos = array();
                        for ($j = 0; $j < count($video); $j++) {
                            $videos[] = $video[$j]['name'];
                        }

                        if (isset($videos)) {
                            $html = [];
                            for ($k = 0; $k < count($videos); $k++) {
                                $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                            }
                            $html = implode(" ", $html);
                            $data[$i]['video_list'] = $html;
                        } else {
                            $data[$i]['video_list'] = "";
                        }*/
                    } else if ($data[$i]['video_type'] == 5) {

                        if ($data[$i]['upcoming_type'] == 1) {

                            $videos = Video::select('id', 'name')->whereIn('id', $Multipal_Ids)->where('type_id', $data[$i]['type_id'])->orderByRaw("FIELD(id, $videoIds)")->get();

                            /*$videos = array();
                            for ($j = 0; $j < count($video); $j++) {
                                $videos[] = $video[$j]['name'];
                            }

                            if (isset($videos)) {
                                $html = [];
                                for ($k = 0; $k < count($videos); $k++) {
                                    $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                                }
                                $html = implode(" ", $html);
                                $data[$i]['video_list'] = $html;
                            } else {
                                $data[$i]['video_list'] = "";
                            }*/
                        } else if ($data[$i]['upcoming_type'] == 2) {

                            $videos = TVShow::select('id', 'name')->whereIn('id', $Multipal_Ids)->where('type_id', $data[$i]['type_id'])->orderByRaw("FIELD(id, $videoIds)")->get();

                            /*$videos = array();
                            for ($j = 0; $j < count($video); $j++) {
                                $videos[] = $video[$j]['name'];
                            }

                            if (isset($videos)) {
                                $html = [];
                                for ($k = 0; $k < count($videos); $k++) {
                                    $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                                }
                                $html = implode(" ", $html);
                                $data[$i]['video_list'] = $html;
                            } else {
                                $data[$i]['video_list'] = "";
                            }*/
                        }
                    }
					if(!empty($videos)) {
						foreach($videos as $v) {
							$data[$i]['video_list'] .= '<p data-video-id="'.$v->id.'" class="btn btn-outline-dark btn-sm mr-2">' . $v->name . '</p>';
						}
					}

                }

                return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $data));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function edit(Request $request)
    {
        try {
            $App_Section = App_Section::where('id', $request->id)->first();

            if ($request->video_type == 1) {
                if($request->type_id == 15 && !empty($request->request_from) && $request->request_from == 'homeSection') {
                    $video = Video::select('id', 'name')->where('video_type', $request->video_type)->get();
                } else {
                    $video = Video::where('type_id', $request->type_id)->where('video_type', $request->video_type)->get();  
                }
            } else if ($request->video_type == 2) {
                $video = TVShow::where('type_id', $request->type_id)->where('video_type', $request->video_type)->get();
            } else if ($request->video_type == 3) {
                $video = Language::get();
            } else if ($request->video_type == 4) {
                $video = Category::get();
            } else if ($request->video_type == 5) {
                if ($request->upcoming_type == 1) {
                    $video = Video::where('type_id', $request->type_id)->where('video_type', $request->video_type)->get();
                } else if ($request->upcoming_type == 2) {
                    $video = TVShow::where('type_id', $request->type_id)->where('video_type', $request->video_type)->get();
                }
            }
            return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $App_Section, 'video' => $video));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function delete($id)
    {
        try {
            if (Auth::guard('admin')->user()->type != 1) {
                return response()->json(array('status' => 400, 'errors' => __('Label.You have no right to add, edit, and delete')));
            } else {
                $video = App_Section::where('id', $id)->first();
                if ($video) {
                    $video->delete();
                    return response()->json(array('status' => 200, 'success' => 'Data Deleted Successfully'));
                } else {
                    return response()->json(array('status' => 200, 'success' => 'Data Deleted Successfully'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function update(Request $request)
    {
        try {
            if (Auth::guard('admin')->user()->type != 1) {
                return response()->json(array('status' => 400, 'errors' => __('Label.You have no right to add, edit, and delete')));
            } else {
                $validator = Validator::make($request->all(), [
                    'title' => 'required|min:2',
                    'video_type' => 'required',
                    'type_id' => 'required',
                    'screen_layout' => 'required',
                    'video_id' => 'required',
                    'edit_id' => 'required',
                    'is_home_screen' => 'required',
                ]);

                if ($validator->fails()) {
                    $errs = $validator->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }

                $data = App_Section::where('id', $request->edit_id)->first();

                if (isset($data->id)) {

                    $data->type_id = $request->type_id;
                    $data->video_type = $request->video_type;
                    $data->title = $request->title;
                    $data->screen_layout = $request->screen_layout;
                    $data->is_home_screen = $request->is_home_screen;
                    $data->upcoming_type = isset($request->upcoming_type) ? $request->upcoming_type : 0;
					$VideoId = '';
					$newVideos = array_flip($request->video_id);
					if(!empty($data->video_id)) {
						$savedVideos = explode(',', $data->video_id);
						foreach($savedVideos as $sk => $sv) {
							if(isset($newVideos[$sv])) {
								unset($newVideos[$sv]);
								$VideoId .= ($sv.',');
							}
						}
						$VideoId = rtrim($VideoId, ',');
						$VideoId .= !empty($newVideos) ? ','.implode(',', array_flip($newVideos)) : '';
					} else {
						$VideoId .= implode(',', $request->video_id);
					}
					//echo $data->video_id.'<br>';
					//echo trim($VideoId, ',');die;
                    $data->video_id = trim($VideoId, ',');
                    if ($data->save()) {
                        return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
                    } else {
                        return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                    }
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	/*
	* Update Section sequence in a particular type
	*/
	public function update_sequence(Request $request)
    {
        try {
			if($request['is_home_screen'] == 1) {
				$savedRecords = App_Section::select()->where('id', '!=', $request['section_id'])->where('is_home_screen', $request['is_home_screen'])->orderBy('position', 'asc')->pluck('id')->toArray();
			} else {
				$savedRecords = App_Section::select()->where('is_home_screen', '!=', 1)->where('id', '!=', $request['section_id'])->where('type_id', $request['type_id'])->orderBy('position', 'asc')->pluck('id')->toArray();
			}
			$savedRecords = array_merge(array_slice($savedRecords, 0, $request['position']), array($request['section_id']), array_slice($savedRecords, $request['position']));
			if(!empty($savedRecords)) {
				$i = 1;
				foreach($savedRecords as $id) {
					$updateData = ['position'=> $i];
					App_Section::where('id', $id)->update($updateData);
					$i++;
				}
			}
			return response()->json(array('status' => 200, 'success' => __('Label.Section sequence updated Successfully.')));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	/*
	* Update Section videos sequence in a particular section
	*/
	public function update_video_sequence(Request $request)
    {
        try {
			if(!empty($request['ids']) && !empty($request['section_id'])) {
				$video_id = rtrim($request['ids'], ',');
				App_Section::where('id', $request['section_id'])->update(['video_id' => $video_id]);
			}
			return response()->json(array('status' => 200, 'success' => __('Label.Section video sequence updated Successfully.')));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
