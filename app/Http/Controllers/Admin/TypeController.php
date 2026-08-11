<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\Type;
use App\Models\Video;
use App\Models\TVShow;
use App\Models\RentVideo;
use App\Models\Channel_Section;
use App\Models\Banner;
use App\Models\App_Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

// Video Type = 1-Video, 2-Show, 3-Language, 4-Category, 5-Upcoming
class TypeController extends Controller
{
	private $folder = "type_image";
    public function index()
    {
        try {
            return view('admin.type.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function data(Request $request)
    {
        try {
            if ($request == true) {
                
                $input_search = $request['input_search'];
                
                if ($input_search != null && isset($input_search)) {
                    $data = Type::where('name', 'LIKE', "%{$input_search}%")->orderby('position')->get();
                } else {
                    $data = Type::orderby('position')->get();
                }
				imageNameToUrl($data, 'type_image', $this->folder);
                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editType", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteType", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Type ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.type.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            return view('admin.type.add');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'type' => 'required',
                'type_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }
            
            $type = new Type();
            $type->name = $request->name;
            $type->type = $request->type;

            if($request->type =='0'){
                $type->is_webseries = 1;
            }
			
            $org_name = $request->file('type_image');
            $type->type_image = saveImage($org_name, $this->folder);
			
            if ($type->save()) {
                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            $user = Type::where('id', $id)->first();
            return view('admin.type.edit', ['result' => $user]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'type' => 'required',
                'type_image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $type = Type::where('id', $request->id)->first();
            if (isset($type->id)) {
                $type->name = $request->name;
                $type->type = $request->type;

                 if($request->type =='0'){
                    $type->is_webseries = 1;
                }
				
				if (isset($request->type_image)) {
                    $files = $request->type_image;
                    $type->type_image = saveImage($files, $this->folder);
                    deleteImageToFolder($this->folder, basename($request->old_image));
                }
				
                if ($type->save()) {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function delete($id)
    {
        try {
            $data = Type::where('id', $id)->first();
            $Video = Video::where('type_id', $data->id)->first();
            $TVShow = TVShow::where('type_id', $data->id)->first();
            $App_Section = App_Section::where('type_id', $data->id)->first();
            $Banner = Banner::where('type_id', $data->id)->first();
            $Channel_Section = Channel_Section::where('type_id', $data->id)->first();
            $RentVideo = RentVideo::where('type_id', $data->id)->first();

            if ($Video) {
                return back()->with('error', "This Type is used on some other table so you can not remove it.");
            } elseif ($TVShow) {
                return back()->with('error', "This Type is used on some other table so you can not remove it.");
            } elseif ($App_Section) {
                return back()->with('error', "This Type is used on some other table so you can not remove it.");
            } elseif ($Banner) {
                return back()->with('error', "This Type is used on some other table so you can not remove it.");
            } elseif ($Channel_Section) {
                return back()->with('error', "This Type is used on some other table so you can not remove it.");
            } elseif ($RentVideo) {
                return back()->with('error', "This Type is used on some other table so you can not remove it.");
            } else {
                if ($data->delete()) {
                    return redirect()->route('type')->with('success', __('Label.Data Delete Successfully'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	public function update_sequence(Request $request)
    {
        try {
			$savedTypes = Type::select()->where('id', '!=', $request['type_id'])->orderBy('position', 'asc')->pluck('id')->toArray();
			$savedTypes = array_merge(array_slice($savedTypes, 0, $request['position']), array($request['type_id']), array_slice($savedTypes, $request['position']));
			if(!empty($savedTypes)) {
				$i = 1;
				foreach($savedTypes as $id) {
					$updateTypeData = ['position'=> $i];
					Type::where('id', $id)->update($updateTypeData);
					$i++;
				}
			}
			return response()->json(array('status' => 200, 'success' => __('Label.Type sequence updated Successfully.')));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
