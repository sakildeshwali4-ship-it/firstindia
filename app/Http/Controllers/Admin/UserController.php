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
        try {
            if ($request == true) {

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
                                ->latest()->get();
                        } else if ($input_type == "month") {

                            $data = Users::where(function ($query) use ($input_search) {
                                $query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
                            })
                                ->whereMonth('created_at', date('m'))
                                ->whereYear('created_at', date('Y'))
                                ->latest()->get();
                        } else if ($input_type == "year") {

                            $data = Users::where(function ($query) use ($input_search) {
                                $query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
                            })
                                ->whereYear('created_at', date('Y'))
                                ->latest()->get();
                        } else {

                            $data = Users::where(function ($query) use ($input_search) {
                                $query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
                            })
                                ->latest()->get();
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
                                ->latest()->get();
                        } else if ($input_type == "month") {

                            $data = Users::where(function ($query) use ($input_search) {
                                $query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
                            })
                                ->where('type', $input_login_type)
                                ->whereMonth('created_at', date('m'))
                                ->whereYear('created_at', date('Y'))
                                ->latest()->get();
                        } else if ($input_type == "year") {

                            $data = Users::where(function ($query) use ($input_search) {
                                $query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
                            })
                                ->where('type', $input_login_type)
                                ->whereYear('created_at', date('Y'))
                                ->latest()->get();
                        } else {

                            $data = Users::where(function ($query) use ($input_search) {
                                $query->where('name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile', 'LIKE', "%{$input_search}%");
                            })
                                ->where('type', $input_login_type)
                                ->latest()->get();
                        }
                    }
                } else {

                    if ($input_login_type == "all") {

                        if ($input_type == "today") {

                            $data = Users::whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                        } else if ($input_type == "month") {

                            $data = Users::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                        } else if ($input_type == "year") {

                            $data = Users::whereYear('created_at', date('Y'))->latest()->get();
                        } else {

                            $data = Users::latest()->get();
                        }
                    } else {

                        if ($input_type == "today") {

                            $data = Users::where('type', $input_login_type)->whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                        } else if ($input_type == "month") {

                            $data = Users::where('type', $input_login_type)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                        } else if ($input_type == "year") {

                            $data = Users::where('type', $input_login_type)->whereYear('created_at', date('Y'))->latest()->get();
                        } else {

                            $data = Users::where('type', $input_login_type)->latest()->get();
                        }
                    }
                }

                imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editUser", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteUser", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this User ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->addColumn('date', function ($row) {
                        $date = date("Y-m-d", strtotime($row->created_at));
                        return $date;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
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
