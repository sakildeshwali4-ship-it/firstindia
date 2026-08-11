<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Audition;
use App\Models\AuditionApplication;
use App\Models\AuditionApplicationVoting;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Users;
use App\Models\Event;
use App\Models\LiveGrandFinale;
use App\Models\Transction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Validator;
use DB;

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
			//$cur_date = date('Y-m-d');
			//where('booking_close_date', '>=', $cur_date)->where('audition_date', '>=', $cur_date)
			/*->with(['city' => function ($query) {
				$query->select('id', 'city_name');
			}])->with('city:city_name')*/
			$data = Audition::select('gallery', 'season_id', 'auditions.id', 'description', 'video_url', 'city_name', 'audition_type', 'audition_title', 'booking_close_date', 'audition_date')->leftJoin('cities', 'auditions.city_id', '=', 'cities.id')->where('audition_type', '!=', 'finish')->where('auditions.status', 1)->orderby('audition_date');
			if($audition_type == 'upcoming' || $audition_type == 'current') {
				$data->where('audition_type', $audition_type);
			}
			$data = $data->get();
			if(!empty($data)) {
				foreach($data as $k => $d) {
					if(!empty($d->gallery) && $d->gallery != '[]') {
						$data[$k]['gallery'] = json_decode($data[$k]['gallery'], 1);
					}
				}
			}
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
					if($auditionApplication = AuditionApplication::create($saveData)) {
						$updateData['application_ref'] = $application_ref.'100'.$auditionApplication->id;
						$auditionApplication->update($updateData);
						$audition = AuditionApplication::with('audition')->with('audition.city:id,city_name')->where('id', $auditionApplication->id)->first();
						$user = Users::select('mobile', 'email', 'password', 'type')->where('id', $data['user_id'])->first();
						$mailsendemail = !empty($audition->email) ? $audition->email : $user->email;
						if(!empty($mailsendemail)) {
							$audition->username = $user->type == 3 ? $user->mobile : $user->email;
							$audition->password = $user->password;
							//Send_Mail('new_application', $mailsendemail, $audition);
						}
					}
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
		$cities = City::where('status', 1)->pluck('city_name', 'id')->toArray();
		$states = State::where('status', 1)->pluck('state_name', 'id')->toArray();
		$countries = Country::where('status', 1)->pluck('country_name', 'id')->toArray();
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
			$data = AuditionApplication::where('user_id', $user_id)->orderby('id');
			if(!empty($id)) {
				$data->where('id', $id);
			}
			$data = $data->get()->toArray();
			if(!empty($data)) {
				foreach($data as $dk => $dt) {
					$data[$dk]['audition'] = '';
					if(!empty($dt['audition_id'])) {
						$data[$dk]['audition'] = Audition::with('city:id,city_name')->where('id', $dt['audition_id'])->first();
					}
					$data[$dk]['city_name'] = !empty($dt['city_id']) ? $cities[$dt['city_id']] : '';
					$data[$dk]['state_name'] = !empty($dt['state_id']) ? $states[$dt['state_id']] : '';
					$data[$dk]['country_name'] = !empty($dt['country_id']) ? $countries[$dt['country_id']] : '';
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
		/*if($user_id == 100000) {
			try {
				$p = Send_Mail('buy_audition', 'nandk1988@gmail.com');
			} catch (Exception $e) {
				echo $e->getMessage();
			}
			prd($p);
		}*/
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
			if(!empty($data)) {
				return APIResponse(200, __('api_msg.get_record_successfully'), $data);
			} else {
				throw new Exception('Application not found!');
			}
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function audition_tester () {
    	try {
	    	$audition = AuditionApplication::with('audition')->with('audition.city:id,city_name')->where('id', 1)->first();
			$user = Users::select('mobile', 'email', 'password', 'type')->where('id', 1)->first();
			$mailsendemail = !empty($audition->email) ? $audition->email : $user->email;
			if(!empty($mailsendemail)) {
				$audition->username = $user->type == 3 ? $user->mobile : $user->email;
				$audition->password = $user->password;
				Send_Mail('new_application', 'nandk1988@gmail.com', $audition);
			}
    	} catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function getAllAuditionApplications(Request $request, $season_id = 0, $audition_id = 0, $ref_id = 0) {
		$data = $request->all();
		$cities = City::where('status', 1)->pluck('city_name', 'id')->toArray();
		$states = State::where('status', 1)->pluck('state_name', 'id')->toArray();
		$countries = Country::where('status', 1)->pluck('country_name', 'id')->toArray();
		$validator = Validator::make(['season_id' => $season_id], [
			"season_id" => 'required|numeric|min:1'
        ]);
		if($validator->fails()) { 
            $errors = $validator->errors();
			$sendError['status'] = 400;
			$sendError['message'] = $errors->first();
			return $sendError;         
        }
        try {
			$transactions = Transction::where('audition_id', '>', 0)->where('payment_status_numeric', 1)->pluck('payment_status_numeric', 'audition_id')->toArray();
			$data = AuditionApplication::/*where('season_id', $season_id)->leftJoin('auditions', 'audition_applications.audition_id', '=', 'auditions.id')->*/orderby('audition_applications.id');
			if(!empty($audition_id)) {
				$data->where('audition_applications.audition_id', $audition_id);
			}
			if(!empty($ref_id)) {
				$data->where('audition_applications.application_ref', $ref_id);
			}
			$data = $data->get()->toArray();
			if(!empty($data)) {
				foreach($data as $dk => $dt) {
					$data[$dk]['payment_status_numeric'] = isset($transactions[$dt['audition_id']]) ? 1 : 0;
					//$data[$dk]['order_id'] = isset($transactions[$dt['audition_id']]) ? 1 : 0;
					$data[$dk]['audition'] = '';
					if(!empty($dt['audition_id'])) {
						$data[$dk]['audition'] = Audition::with('city:id,city_name')->where('id', $dt['audition_id'])->first();
					}
					if($data[$dk]['audition']->season_id != $season_id) {
						unset($data[$dk]);
						continue;
					}
					$data[$dk]['city_name'] = !empty($dt['city_id']) ? $cities[$dt['city_id']] : '';
					$data[$dk]['state_name'] = !empty($dt['state_id']) ? $states[$dt['state_id']] : '';
					$data[$dk]['country_name'] = !empty($dt['country_id']) ? $countries[$dt['country_id']] : '';
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
	
	public function updateAuditionApplication(Request $request) {
		$data = $request->all();
		$validator = Validator::make($data, [
			"application_id" => 'required|numeric|min:1|exists:audition_applications,id'
        ]);
		if($validator->fails()) { 
            $errors = $validator->errors();
			$sendError['status'] = 400;
			$sendError['message'] = $errors->first();
			return $sendError;         
        }
        try {
			$data = $request->all();
			$update = [];
			if(!empty($data['presence'])) {
				$update['presence'] = $data['presence'];
			}
			if(!empty($data['verified'])) {
				$update['verified'] = $data['verified'];
			}
			if(!empty($update)) {
				$application = AuditionApplication::findOrFail($data['application_id']);
				$application->update($update);
				return APIResponse(200, __('Application updated successfully.'), $data);
			} else {
				throw new Exception('No data found to update!');
			}
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	public function auditionApplicationVoting(Request $request) {
		$data = $request->all();
		$validator = Validator::make($request->all(), [
				"voter_id" => 'required',
				"voting_type" => 'required',
				"application_id" => 'required'
	        ]
	    );
		if($validator->fails()) { 
            $errors = $validator->errors();
			$sendError['status'] = 400;
			$sendError['message'] = $errors->first();
			return $sendError;         
        }
        try {
			$data = $request->all();
			$voter = Users::select('mobile', 'email', 'id')->where('id', $data['voter_id'])->first();
			if(!empty($voter)) {
				$curTime = time();
	        	if($curTime >= strtotime("2024-10-01 00:00:00") && $curTime <= strtotime("2024-10-02 19:00:00")) {
	        		$voting_type = 'five_winners';
	        	} else if ($curTime >= strtotime("2024-10-02 20:00:00") && $curTime <= strtotime("2024-10-03 19:00:00")) {
	        		$voting_type = 'three_winners';
	        	} else if ($curTime < strtotime("2024-10-01 00:00:00")) {
	        		$voting_type = $data['voting_type'];
	        	} else {
	        		$voting_type = 'XXXXXXx';
	        	}
				$application = AuditionApplication::select('audition_applications.id', 'season_id')->where('audition_applications.id', $data['application_id'])->leftJoin('auditions', 'audition_applications.audition_id', '=', 'auditions.id')->first();
				if(!empty($application)) {
					$voteCount = AuditionApplicationVoting::where('voter_id', $data['voter_id'])->where('voting_type', $voting_type)->pluck('voter_id', 'application_id')->toArray();
					if(!array_key_exists($application->id, $voteCount) && count($voteCount) < 1) {
						$vote = new AuditionApplicationVoting();
						$vote->voter_id = $data['voter_id'];
						$vote->application_id = $data['application_id'];
						$vote->voting_type = $voting_type;
						$vote->season_id = $application->season_id;
						if($vote->save()) {
							return APIResponse(200, __('You have voted successfully.'), []);
						}
					} else {
						throw new Exception('You have already voted for maximum applicants!');
					}
				} else {
					throw new Exception('Application not found!');
				}
			} else {
				throw new Exception('Voter is not registered!');
			}
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	
	public function getApplicationVotings(Request $request) {
		$requestData = $request->all();
		$cities = City::where('status', 1)->pluck('city_name', 'id')->toArray();
		$states = State::where('status', 1)->pluck('state_name', 'id')->toArray();
		$countries = Country::where('status', 1)->pluck('country_name', 'id')->toArray();
		$validator = Validator::make($requestData, [
			"season_id" => 'required|numeric|min:1',
			"offset" => 'required|numeric|min:0',
			"limit" => 'required|numeric|min:1',
			"user_id" => 'required|numeric|min:1',
			"voting_type" => 'required'
        ]);
		if($validator->fails()) { 
            $errors = $validator->errors();
			$sendError['status'] = 400;
			$sendError['message'] = $errors->first();
			return $sendError;         
        }
        try {
        	$curTime = time();
        	if($curTime >= strtotime("2024-10-01 00:00:00") && $curTime <= strtotime("2024-10-02 19:00:00")) {
        		$voting_type = 'five_winners';
        	} else if ($curTime >= strtotime("2024-10-02 20:00:00") && $curTime <= strtotime("2024-10-03 19:00:00")) {
        		$voting_type = 'three_winners';
        	} else if ($curTime < strtotime("2024-10-01 00:00:00")) {
        		$voting_type = $requestData['voting_type'];
        	} else {
        		$voting_type = 'XXXXXXx';
        	}
			$votes = collect(DB::select("select application_id, count(*) as cnt from audition_application_votings where season_id = ? AND voting_type = ? group by application_id order by cnt desc", [$requestData['season_id'], $voting_type]))->pluck('cnt', 'application_id')->toArray();
			$user_votes = AuditionApplicationVoting::where('voter_id', $requestData['user_id'])->where('voting_type', $voting_type)->pluck('application_id')->toArray();
        	if($curTime >= strtotime("2024-10-01 00:00:00") && $curTime <= strtotime("2024-10-02 19:00:00")) {
        		$data = AuditionApplication::select('audition_id', 'audition_applications.id', 'season_id', 'first_name', 'last_name', 'mobile', 'email', 'address', 'photo_url', 'audition_applications.city_id', 'state_id', 'country_id')->where('presence', 'yes')->where('auditions.season_id', $requestData['season_id'])->where('five_winners', 1)->leftJoin('auditions', 'audition_applications.audition_id', '=', 'auditions.id');
        	} else if ($curTime >= strtotime("2024-10-02 20:00:00") && $curTime <= strtotime("2024-10-03 19:00:00")) {
        		$data = AuditionApplication::select('audition_id', 'audition_applications.id', 'season_id', 'first_name', 'last_name', 'mobile', 'email', 'address', 'photo_url', 'audition_applications.city_id', 'state_id', 'country_id')->where('presence', 'yes')->where('auditions.season_id', $requestData['season_id'])->where('three_winners', 1)->leftJoin('auditions', 'audition_applications.audition_id', '=', 'auditions.id');
        	} else if ($curTime < strtotime("2024-10-01 00:00:00")) {
				$data = AuditionApplication::select('audition_id', 'audition_applications.id', 'season_id', 'first_name', 'last_name', 'mobile', 'email', 'address', 'photo_url', 'audition_applications.city_id', 'state_id', 'country_id')->where('presence', 'yes')->where('auditions.season_id', $requestData['season_id'])/*->where('payment_status', 'complete')*/->leftJoin('auditions', 'audition_applications.audition_id', '=', 'auditions.id');
			} else {
				$data = AuditionApplication::where('id', 0);
			}
			$data = $data->get()->toArray();
			if(!empty($data)) {
				foreach($data as $dk => $dt) {
					//!empty($votesApplications[$data[$dk]['id']]) ? 
					$data[$dk]['votes'] = !empty($votes[$data[$dk]['id']]) ? $votes[$data[$dk]['id']] : 0;
					$data[$dk]['already_voted'] = in_array($data[$dk]['id'], $user_votes) ? true : false;
					$data[$dk]['city_name'] = !empty($dt['city_id']) ? $cities[$dt['city_id']] : '';
					$data[$dk]['state_name'] = !empty($dt['state_id']) ? $states[$dt['state_id']] : '';
					$data[$dk]['country_name'] = !empty($dt['country_id']) ? $countries[$dt['country_id']] : '';
					if(!empty($dt['photo_url'])) {
						$data[$dk]['photo_url'] = asset('images/'.$this->folder_audition_user.'/'.$dt['photo_url']);
					}
				}
			}
			
			/*usort(
				$data,
				// compare function for value 'B'.
				function($arr1, $arr2) { 
				  // descending order ($arr2, $arr1). for ascending compare ($arr1, $arr2)
				  return strcmp($arr2['votes'], $arr1['votes']);
				}
			);*/
			array_multisort(array_column($data, 'votes'), SORT_DESC, $data);
			if($requestData['offset'] == 0 && $requestData['limit'] == 10) {
				$data = array_slice($data, 0, 9, true);
			}
            return APIResponse(200, __('Get application voting list'), $data);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	public function audition_application_import () {
		$msg = 'Something went wrong!';
		try {
			$totalDone = 0;
			$cities = City::where('state_id', 22)->pluck('id', DB::raw('LOWER(city_name)'))->toArray();
			$states = State::pluck('id', DB::raw('LOWER(state_name)'))->toArray();
			$countries = Country::pluck('id', DB::raw('LOWER(country_name)'))->toArray();
			$auditions = Audition::where('season_id', 1)->pluck('id', 'city_id')->toArray();
			$excels = Excel::toArray(new AuditionApplication, public_path('6_AJMER_OFFLINE.xlsx'));
			$dataCount = count($excels);
			if($dataCount > 0) {
				for($i = 0; $i <= $dataCount; $i++) {
					if(!empty($excels[$i])) {
						$insertVariant = $savedVariants = [];
						foreach ($excels[$i] as $key => $data) {
							if($key == 0) {
								continue;
							}
							if(!empty($data[10])) {
								$audition_city = $cities[strtolower($data[17])];
								$audition_id = !empty($auditions[$audition_city]) ? $auditions[$audition_city] : '';
								$city = strtolower($data[13]);
								$city_id = !empty($cities[$city]) ? $cities[$city] : 0;
								$state = strtolower($data[14]);
								$state_id = !empty($states[$state]) ? $states[$state] : 0;
								$country = strtolower($data[15]);
								$country_id = !empty($countries[$country]) ? $countries[$country] : 0;
								$mobile = !empty($data[10]) ? $data[10] : '';
								$first_name = !empty($data[2]) ? $data[2] : '';
								$last_name = !empty($data[3]) ? $data[3] : '';
								$user = Users::where('mobile', $mobile)->orWhere('mobile', $mobile)->first();
								$saveData = [];
								if(!empty($user)) {
									$saveData['user_id'] = $user->id;
									$alreadyApplication = AuditionApplication::where('user_id', $user->id)->first();
									if($alreadyApplication) {
										continue;
									}
								} else {
									$createUser = [];
									$createUser['name'] = !empty($data[2]) ? $data[2] : '';
									$createUser['user_name'] = user_name($mobile);
									$createUser['mobile'] = $mobile;
									$createUser['email'] = !empty($data[9]) ? $data[9] : '';
									$createUser['password'] = "";
									$createUser['image'] = "";
									$createUser['type'] = 4;
									$createUser['status'] = 1;
									$createUser['expiry_date'] = "";
									$createUser['api_token'] = "";
									$createUser['email_verify_token'] = "";
									$createUser['is_email_verify'] = "";
									$saveData['user_id'] = Users::insertGetId($createUser);
								}
								$saveData['first_name'] = $first_name;
								$saveData['last_name'] = $last_name;
								$saveData['father_name'] = !empty($data[4]) ? $data[4] : '';
								$saveData['mother_name'] = !empty($data[5]) ? $data[5] : '';
								$saveData['dob'] = !empty($data[6]) ? date('Y-m-d', strtotime($data[6])) : null;
								$saveData['age'] = !empty($data[7]) ? $data[7] : 0;
								$saveData['gender'] = !empty($data[8]) ? strtolower($data[8]) : 'other';
								$saveData['email'] = !empty($data[9]) ? $data[9] : '';
								$saveData['mobile'] = $mobile;
								$saveData['alternative_mobile'] = !empty($data[11]) ? $data[11] : '';
								$saveData['address'] = !empty($data[12]) ? $data[12] : '';
								$saveData['city_id'] = $city_id;
								$saveData['state_id'] = $state_id;
								$saveData['country_id'] = $country_id;
								$saveData['zipcode'] = !empty($data[16]) ? $data[16] : '';
								$saveData['audition_id'] = $audition_id;
								$saveData['singing_quilification'] = !empty($data[18]) ? strtolower($data[18]) : '';
								$saveData['use_instrument'] = !empty($data[19]) ? strtolower($data[19]) : '';
								$saveData['accept_terms_condition'] = 'yes';
								$saveData['payment_status'] = !empty($data[20]) && $data[20] == 'COMPLETE' ? strtolower($data[20]) : 'pending';
								if($auditionApplication = AuditionApplication::create($saveData)) {
									$updateData['application_ref'] = 'RJ100'.$auditionApplication->id;
									$auditionApplication->update($updateData);
									$totalDone++;
								}
							}
						}
					}
				}
			}
			echo $totalDone.' records inserted successfully.';
		} catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
	}
	
	public function get_event_info(Request $request)
    {
        try {
            //$type = $request->type;
            //$email = isset($request->email) ? $request->email : "";
            $data = Event::select('id','name','image','url','is_show','is_live','created_at')->orderBy('id','DESC')->first();
            $data['image'] = asset('images/event/'.$data['image']); 
            //$data = collect($data)->map(function($x){ return (array) $x; })->toArray(); 
            if (!empty($data)) {
                return APIResponse(200, __('api_msg.get_status_successfully'),$data);
            } else {
                return APIResponse(400, __('api_msg.data_not_save'));
            }  
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	/*
	* Grand Finale Booking Api
	*
	*/
	public function book_ticket_grand_finale (Request $request) {
		$requestData = $request->all();
		//$cities = City::where('status', 1)->pluck('city_name', 'id')->toArray();
		$validator = Validator::make($requestData, [
			"name" => 'required',
			"dob" => 'required',
			"contact_no" => 'required',
			"email_id" => 'required',
			"gender" => 'required',
			"city_id" => 'required',
			"contact_person_name" => 'required',
			"contact_person_no" => 'required',
			"total_candidate" => 'required|numeric|min:1',
			"date_id" => 'required|numeric|min:0',
			"is_audition_person" => 'required|numeric|min:0',
        ]);
		if($validator->fails()) { 
            $errors = $validator->errors();
			$sendError['status'] = 400;
			$sendError['message'] = $errors->first();
			return $sendError;         
        }
        try {
			/*$audition_application = AuditionApplication::select('id')->where('mobile', $requestData['contact_no'])->first();
			if(!empty($audition_application)) {
				$requestData['is_audition_person'] = 1;
			} else {
				$requestData['is_audition_person'] = 0;
			}*/
			$user = Users::select('id')->where('mobile', $requestData['contact_no'])->first();
			if(!empty($user)) {
				$requestData['user_id'] = $user->id;
			} else {
				$user = Users::create([
					'user_name' => $requestData['contact_no'],
					'name' => $requestData['name'],
					'mobile' => $requestData['contact_no'],
					'email' => $requestData['email_id'],
					'password' => Hash::make('123456'),
					'type' => 3
				]);
				$requestData['user_id'] = $user->id;
			}
			$bookData = LiveGrandFinale::create($requestData);
			//$ticket_no = '#'.str_pad($bookData->id, 8, '0', STR_PAD_LEFT);
			//$bookData->update(['ticket_no' => $ticket_no]);
			return APIResponse(200, __('Your ticket booked successfully.'), ['id' => $bookData->id, 'customer_id' => $user->id]);
		} catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
	}
	
	/*
	* Grand Finale Booking update Api
	*
	*/
	public function update_ticket_grand_finale (Request $request) {
		$requestData = $request->all();
		$validator = Validator::make($requestData, [
			"order_id" => 'required'
        ]);
		if($validator->fails()) { 
            $errors = $validator->errors();
			$sendError['status'] = 400;
			$sendError['message'] = $errors->first();
			return $sendError;         
        }
        try {
			$transaction = Transction::where('payment_id', $requestData['order_id'])->where('package_id', 2)->where('payment_status_numeric', 1)->first();
			if(!empty($transaction)) {
				$finale = LiveGrandFinale::find($transaction->live_grand_finale_id);
				if(!empty($finale)) {
					$ticket_no = '#'.str_pad(($finale->id+1000), 8, '0', STR_PAD_LEFT);
					$finale->transaction_id = $transaction->id;
					$finale->payment_status = 'done';
					$finale->ticket_no = $ticket_no;
					$finale->update();
				} else {
					return APIResponse(400, 'Booking not found!');
				}
			} else {
				return APIResponse(400, 'Booking not found!');
			}
			return APIResponse(200, __('Your ticket confirmed successfully.'), ['data' => $finale, 'package_id' => $transaction->package_id]);
		} catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
	}
}