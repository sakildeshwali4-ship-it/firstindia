<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Download;
use App\Models\Users;
use App\Models\Video_Watch;
use Illuminate\Http\Request;
use Validator;
use Exception;
use Carbon\Carbon;

// Login Type = 1- Facebook, 2- Google, 3- OTP, 4- Normal, 5- Apple	

class UserController extends Controller
{
    private $folder = "user";

    public function index()
    {
        try {
            return view('admin.user.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

     public function data(Request $request)
    {
		ini_set('memory_limit', '-1');
        try {

		    if (!$request) {
		        return view('admin.user.index');
		    }
 
		    if ($request->length == 2147483647) {

		        $data = Users::select('name', 'email', 'mobile');

		        return DataTables::of($data)
		            ->addIndexColumn()
		            ->addColumn('image', fn() => '')
		            ->addColumn('date', fn() => '')
		            ->addColumn('action', fn() => '')
		            ->addColumn('type', fn() => '')
		            ->make(true);
		    }
 
		    $query = Users::query();
 
		    if (!empty($request->input_search)) {
		        $search = $request->input_search;
		        $query->where(function ($q) use ($search) {
		            $q->where('name', 'LIKE', "%{$search}%")
		              ->orWhere('email', 'LIKE', "%{$search}%")
		              ->orWhere('mobile', 'LIKE', "%{$search}%");
		        });
		    }
 
		    if ($request->input_login_type !== 'all') {
		        $query->where('type', $request->input_login_type);
		    }

		     
		    if (!empty($request->date_range)) {

		        [$start, $end] = explode(' - ', $request->date_range);

		        $query->whereBetween('created_at', [
		            Carbon::createFromFormat('m/d/Y', $start)->startOfDay(),
		            Carbon::createFromFormat('m/d/Y', $end)->endOfDay(),
		        ]);
		    }  
		    else if ($request->input_type !== 'all') {

		        if ($request->input_type === 'today') {
		            $query->whereDate('created_at', Carbon::today());
		        }

		        if ($request->input_type === 'month') {
		            $query->whereMonth('created_at', now()->month)
		                  ->whereYear('created_at', now()->year);
		        }

		        if ($request->input_type === 'year') {
		            $query->whereYear('created_at', now()->year);
		        }
		    }
 
		    $appName = config('app.image_url');
		    $length  = $request->length;
 
		    return DataTables()::of($query->latest())
		        ->addIndexColumn()

		        ->addColumn('date', function ($row) {
		            return $row->created_at
		                ? $row->created_at->format('Y-m-d')
		                : '-';
		        })

		        ->addColumn('image', function ($row) use ($appName, $length) {

		            if ($length == -1) return '';

		            if (!empty($row->image) &&
		                file_exists(public_path('images/' . $this->folder . '/' . $row->image))) {
		                return $appName . $this->folder . '/' . $row->image;
		            }

		            return asset('assets/imgs/no_user.png');
		        })

		        ->addColumn('action', function ($row) use ($length) {

		            if ($length == -1) return '';

		            return '
		            <div class="d-flex justify-content-center gap-2">
		                <a href="' . route("editUser", $row->id) . '" title="Edit">
		                    <i class="fa-regular fa-pen-to-square"></i>
		                </a>
		                <a href="' . route("deleteUser", $row->id) . '"
		                   onclick="return confirm(\'Are you sure?\')"
		                   title="Delete">
		                    <i class="fa-solid fa-trash-can"></i>
		                </a>
		            </div>';
		        })

		        ->rawColumns(['action'])
		        ->make(true);

		} catch (\Exception $e) {

		    return response()->json([
		        'status' => 400,
		        'errors' => $e->getMessage()
		    ]);
		}
    }


    public function dataOld(Request $request)
    {
		ini_set('memory_limit', '-1');
        try {
            if ($request) {
				if($request->length == 2147483647) {
					$data = Users::select('name', 'email', 'mobile');
					return DataTables()::of($data)->addIndexColumn()
					->addColumn('image', function ($row) {
						return '';
					})
					->addColumn('date', function ($row) {
						return '';
					})
					->addColumn('action', function ($row) {
						return '';
					})
					->addColumn('type', function ($row) {
						return '';
					})
					->make(true);
					//$output['data'] = $data;
					//$output['recordsTotal'] = count($data);
					//$output['recordsFiltered'] = $output['recordsTotal'];
					//$output['draw'] = $request->draw;
					//return json_encode($output);
				} else { 
					$input_search = $request['input_search'];
					$input_type = $request['input_type'];
					$input_login_type = $request['input_login_type'];

					if ($input_search != null && isset($input_search)) {

						if ($input_login_type == "all") {

							if ($input_type == "today") {

								$data = Users::where(function ($query) use ($input_search) {
									$query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
								})
									->whereDay('created_at', date('d'))
									->whereMonth('created_at', date('m'))
									->whereYear('created_at', date('Y'))
									->latest()/*->get()*/;
							} else if ($input_type == "month") {

								$data = Users::where(function ($query) use ($input_search) {
									$query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
								})
									->whereMonth('created_at', date('m'))
									->whereYear('created_at', date('Y'))
									->latest()/*->get()*/;
							} else if ($input_type == "year") {

								$data = Users::where(function ($query) use ($input_search) {
									$query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
								})
									->whereYear('created_at', date('Y'))
									->latest()/*->get()*/;
							} else {

								$data = Users::where(function ($query) use ($input_search) {
									$query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
								})
									->latest()/*->get()*/;
							}
						} else {

							if ($input_type == "today") {

								$data = Users::where(function ($query) use ($input_search) {
									$query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
								})
									->where('type', $input_login_type)
									->whereDay('created_at', date('d'))
									->whereMonth('created_at', date('m'))
									->whereYear('created_at', date('Y'))
									->latest()/*->get()*/;
							} else if ($input_type == "month") {

								$data = Users::where(function ($query) use ($input_search) {
									$query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
								})
									->where('type', $input_login_type)
									->whereMonth('created_at', date('m'))
									->whereYear('created_at', date('Y'))
									->latest()/*->get()*/;
							} else if ($input_type == "year") {

								$data = Users::where(function ($query) use ($input_search) {
									$query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
								})
									->where('type', $input_login_type)
									->whereYear('created_at', date('Y'))
									->latest()/*->get()*/;
							} else {

								$data = Users::where(function ($query) use ($input_search) {
									$query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
								})
									->where('type', $input_login_type)
									->latest()/*->get()*/;
							}
						}
					} else {

						if ($input_login_type == "all") {

							if ($input_type == "today") {

								$data = Users::whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()/*->get()*/;
							} else if ($input_type == "month") {

								$data = Users::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()/*->get()*/;
							} else if ($input_type == "year") {

								$data = Users::whereYear('created_at', date('Y'))->latest()/*->get()*/;
							} else {

								$data = Users::latest()/*->get()*/;
							}
						} else {

							if ($input_type == "today") {

								$data = Users::where('type', $input_login_type)->whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()/*->get()*/;
							} else if ($input_type == "month") {

								$data = Users::where('type', $input_login_type)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()/*->get()*/;
							} else if ($input_type == "year") {

								$data = Users::where('type', $input_login_type)->whereYear('created_at', date('Y'))->latest()/*->get()*/;
							} else {

								$data = Users::where('type', $input_login_type)->latest()/*->get()*/;
							}
						}
					}

					//imageNameToUrl($data->offset(0)->limit(10)->get(), 'image', $this->folder);
					$appName = \Config::get('app.image_url');
					$length = $request->length;
					return DataTables()::of($data)
						->addIndexColumn()
						->addColumn('action', function ($row) use ($length) {
							if($length == -1) {
								return '';
							}
							$btn = '<div class="d-flex justify-content-center gap-2">';
							$btn .= '<a href="' . route("editUser", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
							$btn .= '<a href="' . route("deleteUser", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this User ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
							return $btn;
						})
						->addColumn('date', function ($row) {
							$date = date("Y-m-d", strtotime($row->created_at));
							return $date;
						})
						->addColumn('image', function ($row) use($appName, $length) {
							if($length == -1) {
								return '';
							}
							if(!empty($row['image'])) {
								if (file_exists(public_path('images/' . $this->folder . '/' . $row['image']))) {
									return $appName . $this->folder . '/' . $row['image'];
								} else {
									return asset('assets/imgs/no_user.png');
								}
							} else {
								return asset('assets/imgs/no_user.png');
							}
						})
						->rawColumns(['action'])
						->make(true);
				}
            } else {
                return view('admin.user.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            return view('admin.user.add');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'password' => 'required|min:4',
                'mobile' => 'required|numeric|unique:user,mobile',
                'email' => 'required|unique:user|email',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $user = new Users();

            $email_array = explode('@', $request->email);
            $user->user_name = user_name($email_array[0]);

            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->type = 4;
            $user->api_token = "";
            $user->email_verify_token = "";
            $user->is_email_verify = "";

            $org_name = $request->file('image');
            $user->image = saveImage($org_name, $this->folder);

            if ($user->save()) {
                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.error_add_user')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function edit($id)
    {
        try {
            $user = Users::where('id', $id)->first();

            imageNameToUrl(array($user), 'image', $this->folder);

            if ($user) {
                return view('admin.user.edit', ['result' => $user]);
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'mobile' => 'required|unique:user,mobile,' . $request->id,
                'email' => 'required|email|unique:user,email,' . $request->id,
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $user = Users::where('id', $request->id)->first();
            if (isset($user->id)) {
                $user->name = $request->name;
                $user->mobile = $request->mobile;
                $user->email = $request->email;

                if (isset($request->image)) {
                    $files = $request->image;
                    $user->image = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_image));
                }

                if ($user->save()) {
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

            $user = Users::where('id', $id)->first();

            if ($user->delete()) {

                Bookmark::where('user_id', $user->id)->delete();
                Download::where('user_id', $user->id)->delete();
                Video_Watch::where('user_id', $user->id)->delete();

                deleteImageToFolder($this->folder, $user->image);
                return redirect()->route('user')->with('success', __('Label.Data Delete Successfully'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function searchUser(Request $request)
    {
        try {
            $name = $request->name;
            $user = Users::orWhere('name', 'like', '%' . $name . '%')->orWhere('mobile', 'like', '%' . $name . '%')->orWhere('email', 'like', '%' . $name . '%')->get();

            $url = url('admin/transaction/add?user_id');
            $text = '<table width="100%" class="table table-striped category-table text-center table-bordered"><tr><th class="table-active">Name</th><th class="table-active">Mobile</th><th class="table-active">Email</th><th class="table-active">Action</th></tr>';
            if ($user->count() > 0) {
                foreach ($user as $row) {

                    $a = '<a href="' . $url . '=' . $row->id . '">Select</a>';
                    $text .= '<tr><td>' . $row->name . '</td><td>' . $row->mobile . '</td><td>' . $row->email . '</td><td>' . $a . '</td></tr>';
                }
            } else {
                $text .= '<tr><td colspan="3">User Not Found</td></tr>';
            }
            $text .= '</table>';

            return response()->json(array('status' => 200, 'success' => 'Search User', 'result' => $text));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
