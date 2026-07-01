<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Transction;
use App\Models\Type;
use App\Models\Package_Detail;
use Validator;
use Exception;

class PackageController extends Controller
{
    public function index()
    {
        try {
            return view('admin.package.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            $type = Type::where('type', 1)->Orwhere('type', 2)->get();
            return view('admin.package.add', ['type' => $type]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'price' => 'required',
                'type_id' => 'required',
                'watch_on_laptop_tv' => 'required',
                'ads_free_movies_shows' => 'required',
                'no_of_device' => 'required',
                'video_qulity' => 'required',
                'type' => 'required',
                'time' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $package = new Package();
            $package->name = $request->name;
            $package->price = $request->price;
            $type_id = implode(',', $request->type_id);
            $package->type_id = $type_id;
            $package->watch_on_laptop_tv = $request->watch_on_laptop_tv;
            $package->ads_free_movies_shows = $request->ads_free_movies_shows;
            $package->no_of_device = $request->no_of_device;
            $package->video_qulity = $request->video_qulity;
            $package->status = 1;
            $package->type = $request->type;
            $package->time = $request->time;
            $package->android_product_package = isset($request->android_product_package) ? $request->android_product_package : "";
            $package->ios_product_package = isset($request->ios_product_package) ? $request->ios_product_package : "";

            if ($package->save()) {
                if (package_detail($package->id) == "Success") {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
                }
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
            }
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
                    $data = Package::where('name', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Package::latest()->get();
                }

                for ($i = 0; $i < count($data); $i++) {
                    $type = Type::select('id', 'name')->whereIn('id', explode(",", $data[$i]->type_id))->get();
                    $data[$i]->type_name = $type;
                }

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editPackage", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deletePackage", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Package ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.package.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function delete($id)
    {
        try {
            $package = Package::where('id', $id)->first();
            $TVShowVideo = Transction::where('package_id', $package->id)->first();
            if ($TVShowVideo) {
                return back()->with('error', "This Package is used on some other table so you can not remove it.");
            } else {
                if ($package) {
                    $package->delete();
                    Package_Detail::where('package_id', $id)->delete();
                    return redirect()->route('package')->with('success', __('Label.Data Delete Successfully'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function edit($id)
    {
        try {
            $package = Package::where('id', $id)->first();
            $type = Type::where('type', 1)->Orwhere('type', 2)->get();
            return view('admin.package.edit', ['result' => $package, 'type' => $type]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'price' => 'required',
                'type_id' => 'required',
                'watch_on_laptop_tv' => 'required',
                'ads_free_movies_shows' => 'required',
                'no_of_device' => 'required',
                'video_qulity' => 'required',
                'type' => 'required',
                'time' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $package = Package::where('id', $request->id)->first();
            if (isset($package->id)) {

                $package->name = $request->name;
                $package->price = $request->price;

                $type_id = implode(',', $request->type_id);
                $package->type_id = $type_id;
                $package->watch_on_laptop_tv = $request->watch_on_laptop_tv;
                $package->ads_free_movies_shows = $request->ads_free_movies_shows;
                $package->no_of_device = $request->no_of_device;
                $package->status = 1;
                $package->video_qulity = $request->video_qulity;
                $package->type = $request->type;
                $package->time = $request->time;
                $package->android_product_package = isset($request->android_product_package) ? $request->android_product_package : "";
                $package->ios_product_package = isset($request->ios_product_package) ? $request->ios_product_package : "";

                if ($package->save()) {
                    if (package_detail($package->id) == "Success") {
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
}
