<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Audition;
use App\Models\AuditionApplication;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Exception;
use Validator;

// Video Type = 1-Video, 2-Show, 3-Language, 4-Category, 5-Upcoming
// Video Upload Type = server_video, external, youtube, vimeo
// Subtitle Type = server_video, external
// Trailer Type = server_video, external, youtube

class AuditionController extends Controller
{

    private $folder_audition = "auditions";
    private $folder_audition_user = "auditions/applications";

    public function getCountryList()
    {
        try {
            $data = Country::where('status', 1)->orderby('country_name')->pluck('country_name', 'id');
			$retData = [];
			if(!empty($data)) {
				foreach($data as $k => $val) {
					$retData[] = ['id' => $k, 'name' => $val];
				}
			}
            return APIResponse(200, __('api_msg.get_record_successfully'), $retData);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
    public function getStateList($country_id = null)
    {
        try {
			if(!empty($country_id)) {
				$data = State::where('country_id', $country_id)->where('status', 1)->orderby('state_name')->pluck('state_name', 'id');
			} else {
				$data = State::where('status', 1)->orderby('state_name')->pluck('state_name', 'id');
			}
			$retData = [];
			if(!empty($data)) {
				foreach($data as $k => $val) {
					$retData[] = ['id' => $k, 'name' => $val];
				}
			}
            return APIResponse(200, __('api_msg.get_record_successfully'), $retData);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
    public function getCityList($state_id = null)
    {
        try {
			if(!empty($state_id)) {
				$data = City::where('state_id', $state_id)->where('status', 1)->orderby('city_name')->pluck('city_name', 'id');
			} else {
				$data = City::where('status', 1)->orderby('city_name')->pluck('city_name', 'id');
			}
			$retData = [];
			if(!empty($data)) {
				foreach($data as $k => $val) {
					$retData[] = ['id' => $k, 'name' => $val];
				}
			}
            return APIResponse(200, __('api_msg.get_record_successfully'), $retData);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	public function getAuditionList($audition_type = 'all')
    {
        try {
			$cur_date = date('Y-m-d');
			//where('booking_close_date', '>=', $cur_date)->where('audition_date', '>=', $cur_date)
			/*->with(['city' => function ($query) {
				$query->select('id', 'city_name');
			}])->with('city:city_name')*/
			$data = Audition::select('season_id', 'auditions.id', 'description', 'video_url', 'city_name', 'audition_type', 'audition_title', 'booking_close_date', 'audition_date')->leftJoin('cities', 'auditions.city_id', '=', 'cities.id')->where('audition_type', '!=', 'finish')->where('auditions.status', 1)->orderby('audition_date');
			if($audition_type == 'upcoming' || $audition_type == 'current') {
				$data->where('audition_type', $audition_type);
			}
			$data = $data->get();
            return APIResponse(200, __('api_msg.get_record_successfully'), $data);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	public function auditionApplication (Request $request) {
		$data = $request->all();
		$validator = Validator::make($data, [
			"first_name" => 'required',
			"last_name" => 'required',
			"father_name" => 'required', 
			"mother_name" => 'required',
			"dob" => 'required|date',
			"age" => 'required|numeric',
			"gender" => 'required',
			"mobile" => 'required',
			"address" => 'required',
			"city_id" => 'required|numeric|min:1|exists:cities,id',
			"state_id" => 'required|numeric|min:1|exists:states,id',
			"country_id" => 'required|numeric|min:1|exists:countries,id',
			"audition_id" => 'required|numeric|min:1|exists:auditions,id',
			"photo_url" => 'required',
			"document_url" => 'required',
			"user_id" => 'required|numeric|min:1|exists:user,id',
        ]);
		if($validator->fails()) { 
            $errors = $validator->errors();
			$sendError['status'] = 400;
			$sendError['message'] = $errors->first();
			return $sendError;         
        }
		try {
			$audition = Audition::findOrFail($data['audition_id']);
			$state = State::findOrFail($data['state_id']);
			$application_ref = $state->state_code;
			if(!empty($audition)) {
				if(!empty($data['id'])) {
					$auditionApplication = AuditionApplication::findOrFail($data['id']);
					if(empty($auditionApplication)) {
						throw new Exception('Application not found to update!');
					}
				}
				$saveData['user_id'] = $data['user_id'];
				$saveData['first_name'] = !empty($data['first_name']) ? $data['first_name'] : '';
				$saveData['last_name'] = !empty($data['last_name']) ? $data['last_name'] : '';
				$saveData['father_name'] = !empty($data['father_name']) ? $data['father_name'] : '';
				$saveData['mother_name'] = !empty($data['mother_name']) ? $data['mother_name'] : '';
				$saveData['dob'] = !empty($data['dob']) ? $data['dob'] : null;
				$saveData['age'] = !empty($data['age']) ? $data['age'] : 0;
				$saveData['gender'] = !empty($data['gender']) ? $data['gender'] : 'other';
				$saveData['email'] = !empty($data['email']) ? $data['email'] : '';
				$saveData['mobile'] = !empty($data['mobile']) ? $data['mobile'] : '';
				$saveData['alternative_mobile'] = !empty($data['alternative_mobile']) ? $data['alternative_mobile'] : '';
				$saveData['address'] = !empty($data['address']) ? $data['address'] : '';
				$saveData['city_id'] = !empty($data['city_id']) ? $data['city_id'] : 0;
				$saveData['state_id'] = !empty($data['state_id']) ? $data['state_id'] : 0;
				$saveData['country_id'] = !empty($data['country_id']) ? $data['country_id'] : 0;
				$saveData['zipcode'] = !empty($data['zipcode']) ? $data['zipcode'] : '';
				$saveData['audition_id'] = !empty($data['audition_id']) ? $data['audition_id'] : 0;
				$saveData['singing_quilification'] = !empty($data['singing_quilification']) ? $data['singing_quilification'] : '';
				$saveData['use_instrument'] = !empty($data['use_instrument']) ? $data['use_instrument'] : '';
				if (!empty($data['photo_url'])) {
					$saveData['photo_url'] = saveImage($data['photo_url'], $this->folder_audition_user);
					if(!empty($auditionApplication['photo_url'])) {
						deleteImageToFolder($this->folder_audition_user, $auditionApplication['photo_url']);
					}
				}
				
				if (!empty($data['document_url'])) {
					$saveData['document_url'] = saveImage($data['document_url'], $this->folder_audition_user);
					if(!empty($auditionApplication['document_url'])) {
						deleteImageToFolder($this->folder_audition_user, $auditionApplication['document_url']);
					}
				}
				$saveData['accept_terms_condition'] = !empty($data['accept_terms_condition']) ? $data['accept_terms_condition'] : 'no';
				if(!empty($data['id'])) {
					$saveData['application_ref'] = $application_ref.'100'.$data['id'];
					$auditionApplication->update($saveData);
				} else {
					$auditionApplication = AuditionApplication::create($saveData);
					$updateData['application_ref'] = $application_ref.'100'.$auditionApplication->id;
					$auditionApplication->update($updateData);
				}
				return APIResponse(200, __('Audition applicatition saved successfully.'), array($auditionApplication));
			} else {
				throw new Exception('Audition not found!');
			}
		} catch(\Exception $e) {
			return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
	}
	
	public function getAuditionApplicationDetail(Request $request, $user_id = 0, $id = 0)
    {
		$data = $request->all();
		$validator = Validator::make(['user_id' => $user_id], [
			"user_id" => 'required|numeric|min:1|exists:user,id'
        ]);
		if($validator->fails()) { 
            $errors = $validator->errors();
			$sendError['status'] = 400;
			$sendError['message'] = $errors->first();
			return $sendError;         
        }
        try {
			$data = AuditionApplication::with(
				['city' => function ($query) {
    				$query->select('city_name');
				},
				'state' => function ($query) {
    				$query->get(['state_name']);
				},
				'country' => function ($query) {
    				$query->get(['country_name']);
				}]
			)->where('user_id', $user_id)->orderby('id');
			if(!empty($id)) {
				$data->where('id', $id);
			}
			$data = $data->get()->toArray();
			if(!empty($data)) {
				foreach($data as $dk => $dt) {
					if(!empty($dt['photo_url'])) {
						$data[$dk]['photo_url'] = asset('images/'.$this->folder_audition_user.'/'.$dt['photo_url']);
					}
					if(!empty($dt['document_url'])) {
						$data[$dk]['document_url'] = asset('images/'.$this->folder_audition_user.'/'.$dt['document_url']);
					}
				}
			}
            return APIResponse(200, __('api_msg.get_record_successfully'), $data);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function checkAuditionApplication(Request $request, $user_id = 0, $season_id = 0)
    {
		$data = $request->all();
		$validator = Validator::make(['user_id' => $user_id, 'season_id' => $season_id], [
				"user_id" => 'required',
				"season_id" => 'required'
	        ]
	    );
		if($validator->fails()) { 
            $errors = $validator->errors();
			$sendError['status'] = 400;
			$sendError['message'] = $errors->first();
			return $sendError;         
        }
        try {
			$data = AuditionApplication::leftJoin('auditions', 'audition_id', '=', 'auditions.id')->where('user_id', $user_id)->where('auditions.season_id', $season_id)->first();
            return APIResponse(200, __('api_msg.get_record_successfully'), $data);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
