<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Season; 
use App\Models\WebSeries;
use App\Models\SeasonTrailer;
use Validator;
use Exception; 

class SeasonController extends Controller
{
    private $folder = "season";
    private $folder_trailers = "season_trailers";

    public function index()
    {
        try {
            return view('admin.seasons.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

   public function data(Request $request){
        
        try {
             
            if ($request->ajax()) {

                $input_search = $request->input_search;

                if (!empty($input_search)) {
                    $data = Season::with('webSeries')
                        ->where('title', 'LIKE', "%{$input_search}%")
                        ->latest()
                        ->get();
                } else {
                    $data = Season::with('webSeries')
                        ->latest()
                        ->get();
                }

                imageNameToUrl($data, 'thumbnail', $this->folder); 

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('web_series_name', function ($row) {
                            return $row->webSeries ? $row->webSeries->title : '-';
                        })
                    ->editColumn('isActive', function ($row) {
                        if($row->isActive =='1'){
                            $status = '<span class="btn btn-success bg-success px-2">Active</span>';
                        }else{
                            $status = '<span class="btn btn-danger bg-danger px-2">InActive</span>';
                        }
                        return $status;
                    }) 
                    ->addColumn('action', function ($row) {

                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        
                        $btn .= '<a href="' . route("seasons.trailers") . '" title="Edit">
                                    Trailers
                                </a>';
                        $btn .= '<a href="' . route("seasons.edit", $row->id) . '" title="Edit">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>';

                        $btn .= '<a href="' . route("seasons.destroy", $row->id) . '" title="Delete"
                                    onclick="return confirm(\'Are you sure?\')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>';

                        $btn .= '</div>';

                        return $btn;
                    })

                    ->rawColumns(['isActive','web_series_name','action'])
                    ->make(true);
            }
 
            return view('admin.seasons.index');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        try {

            $webseries = WebSeries::where('isActive', 1)->pluck('title', 'id'); // title => id

            return view('admin.seasons.add', compact('webseries'));

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
         
        try {
            $validator = Validator::make($request->all(), [
                'web_series_id' => 'required',
                'title' => 'required|min:2',
                'isActive' => 'required',
                'video' => 'required',
                'season_number'   => 'required|integer|min:1',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'meta_desc' => 'required', 
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $season = new Season();
            $season->web_series_id = $request->web_series_id;  
            $season->title = $request->title;  
            $season->isActive = $request->isActive;
            $season->video = $request->video;
            $season->season_number = $request->season_number;
            $season->meta_desc = $request->meta_desc; 

            $org_name = $request->file('image'); 
            if ($org_name != null) {
                $season->thumbnail = saveImage($org_name, $this->folder);
            }
            $org_name1 = $request->file('landscape'); 
            if ($org_name1 != null) {
                $season->landscape = saveImage($org_name1, $this->folder);
            }

            if ($season->save()) {
                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $WebSeries = WebSeries::where('isActive', 1)->pluck('title', 'id'); // title => id
            $season = Season::where('id', $id)->first();

            imageNameToUrl(array($season), 'thumbnail', $this->folder); 
            imageNameToUrl(array($season), 'landscape', $this->folder); 

            return view('admin.seasons.edit', ['result' => $season, 'webseries'=> $WebSeries]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'web_series_id' => 'required',
                'title' => 'required|min:2',
                'isActive' => 'required',
                'video' => 'required',
                'season_number'   => 'required|integer|min:1', 
                'meta_desc' => 'required', 
            ]);

            if ($validator->fails()) {

                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $season = Season::where('id', $request->id)->first();
            if (isset($season->id)) { 
                $season->web_series_id = $request->web_series_id;  
                $season->title = $request->title;  
                $season->isActive = $request->isActive;
                $season->video = $request->video;
                $season->season_number = $request->season_number;
                $season->meta_desc = $request->meta_desc; 


                if (isset($request->image)) {
                    $files = $request->image;
                    $season->thumbnail = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_image));
                }

                if (isset($request->landscape)) {
                    $files = $request->landscape;
                    $season->landscape = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_landscape));
                }
                






                if ($season->save()) {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    { 
        try {
            $season = Season::where('id', $id)->first(); 

                deleteImageToFolder($this->folder, $season->thumbnail); 
                $season->delete();
                return redirect()->route('seasons.index')->with('success', __('Label.Data Delete Successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function trailers()
    {
        
        try {
            return view('admin.seasons.trailers');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function trailersData(Request $request)
    {
       
        try {

            if ($request->ajax()) {

                $input_search = $request->input_search;

                $query = SeasonTrailer::query();

                if (!empty($input_search)) {
                    $query->where('title', 'LIKE', "%{$input_search}%");
                }

                $data = $query->latest()->get();
                 
                imageNameToUrl($data, 'thumbnail', $this->folder_trailers);

                return DataTables()::of($data)
                    ->addIndexColumn()

                    ->editColumn('status', function ($row) {

                        if ($row->status == 1) {
                            return '<span class="btn btn-success px-2">Active</span>';
                        } else {
                            return '<span class="btn btn-danger px-2">Inactive</span>';
                        }
                    })

                    ->addColumn('action', function ($row) {

                        $btn = '<div class="d-flex justify-content-center gap-2">';

                        $btn .= '<a href="' . route("trailer.edit", $row->id) . '" title="Edit">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>';

                        $btn .= '<a href="' . route("trailer.destroy", $row->id) . '" 
                                    onclick="return confirm(\'Are you sure?\')" title="Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>';

                        $btn .= '</div>';

                        return $btn;
                    })

                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            return view('admin.seasons.trailers');

        } catch (\Exception $e) {

            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function trailers_add()
    {
        try {

            $season = Season::where('isActive', 1)->pluck('title', 'id');

            return view('admin.seasons.add_trailer', compact('season'));

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    
    public function trailerStore(Request $request)
    { 
         
        try {
            $validator = Validator::make($request->all(), [
                'season_id' => 'required',
                'title' => 'required|min:2',
                'isActive' => 'required',
                'video' => 'required',
                'trailer_number'   => 'required|integer|min:1',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'meta_desc' => 'required', 
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $season = new SeasonTrailer();
            $season->season_id = $request->season_id;  
            $season->title = $request->title;  
            $season->status = $request->isActive;
            $season->video_url = $request->video;
            $season->trailer_number = $request->trailer_number;
            $season->meta_desc = $request->meta_desc; 

            $org_name = $request->file('image'); 
            if ($org_name != null) {
                $season->thumbnail = saveImage($org_name, $this->folder_trailers);
            }
            $org_name1 = $request->file('landscape'); 
            if ($org_name1 != null) {
                $season->landscape = saveImage($org_name1, $this->folder_trailers);
            }

            if ($season->save()) {
                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }


    public function trailerEdit($id)
    {
        try {
            $WebSeries = Season::where('isActive', 1)->pluck('title', 'id'); // title => id
            $season = SeasonTrailer::where('id', $id)->first();

            imageNameToUrl(array($season), 'thumbnail', $this->folder_trailers); 
            imageNameToUrl(array($season), 'landscape', $this->folder_trailers); 

            return view('admin.seasons.edit_trailer', ['result' => $season, 'webseries'=> $WebSeries]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function trailerUpdate(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'season_id' => 'required',
                'title' => 'required|min:2',
                'isActive' => 'required',
                'video_url' => 'required',
                'trailer_number'   => 'required|integer|min:1', 
                'meta_desc' => 'required', 
            ]);

            if ($validator->fails()) {

                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $season = SeasonTrailer::where('id', $request->id)->first();
            if (isset($season->id)) { 
                $season->season_id = $request->season_id;  
                $season->title = $request->title;  
                $season->status = $request->isActive;
                $season->video_url = $request->video_url;
                $season->trailer_number = $request->trailer_number;
                $season->meta_desc = $request->meta_desc; 


                if (isset($request->image)) {
                    $files = $request->image;
                    $season->thumbnail = saveImage($files, $this->folder_trailers);

                    deleteImageToFolder($this->folder_trailers, basename($request->old_image));
                }

                if (isset($request->landscape)) {
                    $files = $request->landscape;
                    $season->landscape = saveImage($files, $this->folder_trailers);

                    deleteImageToFolder($this->folder_trailers, basename($request->old_landscape));
                }
                
 

                if ($season->save()) {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function trailerDestroy($id)
    { 
        try {
                $season = SeasonTrailer::where('id', $id)->first(); 

                deleteImageToFolder($this->folder_trailers, $season->thumbnail); 
                deleteImageToFolder($this->folder_trailers, $season->landscape); 
                $season->delete();
                return redirect()->route('seasons.trailers')->with('success', __('Label.Data Delete Successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }


    

}
