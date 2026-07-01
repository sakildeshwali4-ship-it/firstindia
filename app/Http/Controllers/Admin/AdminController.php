<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Models\Cast;
use App\Models\Users;
use App\Models\Channel;
use App\Models\Language;
use App\Models\TVShow;
use App\Models\Video;
use App\Models\Package;
use App\Models\Page;
use App\Models\RentTransction;
use App\Models\Transction;
use App\Http\Controllers\Controller;
use URL;
use Exception;

class AdminController extends Controller
{
    public function dashboard()
    {
        try {

            $params['total_user'] = Users::get()->count();
            $params['total_channel']  = Channel::get()->count();
            $params['total_video'] = Video::get()->count();
            $params['total_show']  = TVShow::get()->count();
            $params['total_cast'] = Cast::get()->count();
            $params['total_rent_transaction'] = RentTransction::sum('price');
            $params['total_month_rent_transaction'] = RentTransction::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('price');
            $params['total_package'] = Package::get()->count();
            $params['total_transaction'] = Transction::sum('amount');
            $params['total_month_transaction'] = Transction::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('amount');

            // User Statistice
            $user_data = [];
            $user_month = [];
            $d = date('t', mktime(0, 0, 0, date('m'), 1, date('Y'))); 

            for ($i = 1; $i < 13; $i++) {
                $Sum = Users::whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->count();
                $user_data['sum'][] = (int) $Sum;
            }
            for ($i = 1; $i <= $d; $i++) {

                $Sum = Users::whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->whereDay('created_at', $i)->count();
                $user_month['sum'][] = (int) $Sum;
            }
            $params['user_year'] = json_encode($user_data);
            $params['user_month'] = json_encode($user_month);

            // Plan Earning Statistice
            $subscription = Package::get();
            $pack_data = [];
            foreach ($subscription as $row) {

                $sum = array();
                for ($i = 1; $i < 13; $i++) {

                    $Sum = Transction::where('package_id', $row->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->sum('amount');
                    $sum[] = (int) $Sum;
                }
                $pack_data['label'][] = $row->name;
                $pack_data['sum'][] = $sum;
            }
            $params['package'] = json_encode($pack_data);

            // Rent Earning Statistice
            $rent_sum = array();
            for ($i = 1; $i < 13; $i++) {

                $Sum = RentTransction::whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->sum('price');
                $rent_sum['sum'][] = (int) $Sum;
            }
            $params['rent'] = json_encode($rent_sum);

            // Most View Video
            $params['most_view_video'] = Video::orderBy('view', 'desc')->first();
            // Most View TVShow
            $params['most_view_show'] = TVShow::orderBy('view', 'desc')->first();

            return view('admin.dashboard', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function language($id)
    {
        try {
            Language::where('status', '1')->update(['status' => '0']);

            $language = Language::where('id', $id)->first();
            if (isset($language->id)) {
                $language->status = '1';
                if ($language->save()) {
                    App::setLocale($language->lang_code);
                    session()->put('locale', $language->lang_code);
                    return back()->with('success', __('Label.Language Change Successfully.'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function Page()
    {
        try {

            $currentURL = URL::current();

            $link_array = explode('/', $currentURL);
            $page = end($link_array);

            $data = Page::where('id', $page)->first();

            if (isset($data)) {
                return view('page', ['result' => $data]);
            } else {
                return view('errors.404');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
