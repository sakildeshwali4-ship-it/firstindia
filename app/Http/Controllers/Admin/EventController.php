<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\Event;
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
class EventController extends Controller
{
    public function index()
    {
        try {
            return view('admin.event.index');
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
                    $data = Event::where('name', 'LIKE', "%{$input_search}%")->orderby('name')->get();
                } else {
                    $data = Event::orderby('name')->get();
                }
                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editevent", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteEvent", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Type ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.event.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            return view('admin.event.add');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tittle' => 'required',
                'url' => ['required','regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],
                'is_show' => 'required',
                'image' => 'required',
                'is_live' => 'required'
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }
            
            
            $file = $request->file('image');
            $exten = $file->getClientOriginalExtension();
            $filename = time().".".$exten;
            

            $event = new Event();
            $event->name = $request->tittle;
            $event->is_show = $request->is_show;
            $event->image = $filename;
            $event->url = $request->url;
            $event->is_live = $request->is_live;
            if ($event->save()) {
                $file->move('images/event',$filename);
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
            $user = Event::where('id', $id)->first();
            return view('admin.event.edit', ['result' => $user]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tittle' => 'required',
                'url' => ['required','regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],
                'image' => 'required',
                'is_show' =>'required',
                'is_live' => 'required'
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }
            
            $file = $request->file('image');
            $exten = $file->getClientOriginalExtension();
            $filename = time().".".$exten;
            
            $event = Event::where('id', $request->id)->first();
            if (isset($event->id)) {
                $event->name = $request->tittle;
                $event->is_show = $request->is_show;
                $event->image = $filename;
                $event->url = $request->url;
                $event->is_live = $request->is_live;
                if ($event->save()) {
                     $file->move('images/event',$filename);
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
            $data = Event::where('id', $id)->first();
            if ($data->delete()) {
                return redirect()->route('event')->with('success', __('Label.Data Delete Successfully'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	public function update_sequence(Request $request)
    {
        try {
			$savedTypes = Event::select()->where('id', '!=', $request['type_id'])->orderBy('position', 'asc')->pluck('id')->toArray();
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
