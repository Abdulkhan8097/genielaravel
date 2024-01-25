<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToArray;

class MeetinglogModel extends Model
{
    use HasFactory;

    public static function getDistributorByARN($arn_number){
        $data =  DB::table('drm_distributor_master')
                    ->select('*')
                    ->where('ARN', '=', $arn_number)
                    ->get();
        return $data;
    }
	public static function getARN($id){
        $data =  DB::table('drm_meeting_logger')
                    ->select('ARN','sms_sent_to_customer')
                    ->where('id', '=', $id)
                    ->get();
        return $data;
    }
	public static function getremark($id){
		$data = DB::table('drm_meeting_logger')
		->select('customer_remarks')
		->where('id', $id)
		->whereNotNull('customer_remarks')
		->where('customer_remarks', '!=', '')
		->get()
		->toArray();
		
        return $data;
    }
	public static function getcheck_empanelled($ARN){
        $data =  DB::table('drm_distributor_master')
                    ->select('*')
                    ->whereNotNull('direct_relationship_user_id') // Filter for not null
        			->where('direct_relationship_user_id', '!=', '') // Filter for not empty
                    ->where('ARN', '=', $ARN)
                    ->get()
					->toArray();
        return $data;
    }

    public static function getDRMMeetingLog(){
        $data =  DB::table('drm_meeting_logger')
                    ->join('users', 'users.id', '=','drm_meeting_logger.user_id')
                    ->select('drm_meeting_logger.*','users.name as bdm_name')
                    ->get();
        return $data;
    }
	public static function getnotification_data($id){
		$currentDate = now()->toDateString();
        $data =  DB::table('drm_meeting_logger')
                    ->join('users', 'users.id', '=','drm_meeting_logger.user_id')
					->join('drm_distributor_master as distributor_master', 'distributor_master.ARN', '=', 'drm_meeting_logger.ARN')
                    ->select('drm_meeting_logger.*','users.name as bdm_name', 'distributor_master.arn_holders_name')
                    ->where('drm_meeting_logger.user_id',$id)
					
					->where(DB::raw('DATE(drm_meeting_logger.end_datetime)'), '<', $currentDate)
					->where(function($query) {
						$query->where('drm_meeting_logger.meeting_remarks', '')
							  ->orWhereNull('drm_meeting_logger.meeting_remarks');
					})
                    ->get();
        return $data;
    }
	public static function getTodayMeetingNotificationData($id) {
    $currentDate = now()->toDateString();
    $query = DB::table('drm_meeting_logger')
        ->join('users', 'users.id', '=', 'drm_meeting_logger.user_id')
		->join('drm_distributor_master as distributor_master', 'distributor_master.ARN', '=', 'drm_meeting_logger.ARN')
        ->select('drm_meeting_logger.*', 'users.name as bdm_name', 'distributor_master.arn_holders_name')
        ->where('drm_meeting_logger.user_id', $id)
        ->where(DB::raw('DATE(drm_meeting_logger.start_datetime)'), '=', $currentDate)
        ->where(function ($query) {
            $query->where('drm_meeting_logger.meeting_remarks', '')
                ->orWhereNull('drm_meeting_logger.meeting_remarks');
        });

    // If you want to execute the query and return the results, use the following:
    return $query->get();
	}
	public static function getMeetingDataByTag($id) {

		$currentDate = now()->toDateString();
		$query = DB::table('drm_meeting_logger')
			->join('users', 'users.id', '=', 'drm_meeting_logger.user_id')
			->join('drm_distributor_master as distributor_master', 'distributor_master.ARN', '=', 'drm_meeting_logger.ARN')
			->select('drm_meeting_logger.*', 'users.name as bdm_name', 'distributor_master.arn_holders_name')
			->where(DB::raw('DATE(drm_meeting_logger.start_datetime)'), '=', $currentDate)
			// ->whereIn('drm_meeting_logger.bdm_data', (array) $id);
			->where(function ($query) use ($id) {
				$query->orWhereRaw("FIND_IN_SET(?, drm_meeting_logger.bdm_data)", [$id]);
			});
		return $query->get();
	}
	public static function getTodayPartnersBirthDayNotificationData() {
		$session = session()->all();
		$show_all_arn_data = $session['logged_in_user_roles_and_permissions']['role_details']['show_all_arn_data'];
		// x($show_all_arn_data);

		$currentDate = now()->toDateString();
		$data =  DB::table(DB::raw('drm_rankmf_partner_registration as p'))
		->join('drm_distributor_master as d', 'd.ARN', '=', 'p.ARN')
			->select(['p.ARN', 'p.arn_name', 'p.dob']) // Use an array to specify column names
			->where('p.ARN', '!=', '')
			->where('p.dob', '!=', '0000-00-00')
			->whereRaw('MONTH(p.dob) = ?', [date('m', strtotime($currentDate))])
        	->whereRaw('DAY(p.dob) = ?', [date('d', strtotime($currentDate))]);

		if(!($show_all_arn_data == 1)){
			$data->where('d.direct_relationship_user_id','=',Auth::user()->id);
		}


		$data = $data->get()
		->toArray();
	
		return $data;
	}
	public static function getUpcomingPartnersBirthDayNotificationData() {
		$session = session()->all();
		$show_all_arn_data = $session['logged_in_user_roles_and_permissions']['role_details']['show_all_arn_data'];
		$currentDate = now()->toDateString();
		$newDate = date("Y-m-d", strtotime($currentDate . " +7 days"));
		$data =  DB::table('drm_rankmf_partner_registration as p')
			->select(['p.ARN', 'p.arn_name', 'p.dob', DB::raw('concat(lpad(MONTH(p.dob),2,0),lpad(DAY(p.dob),2,0)) as tmp')]) // Use an array to specify column names
			->join('drm_distributor_master as d', 'd.ARN', '=', 'p.ARN')
			->where('p.ARN', '!=', '')
			->where('p.dob', '!=', '0000-00-00')
			->where(DB::raw('concat(lpad(MONTH(p.dob),2,0),lpad(DAY(p.dob),2,0))'),'>', date('md', strtotime($currentDate)))
			->where(DB::raw('concat(lpad(MONTH(p.dob),2,0),lpad(DAY(p.dob),2,0))'),'<=', date('md', strtotime($newDate)))
			->orderBy('tmp');

			if(!($show_all_arn_data == 1)){
				$data->where('d.direct_relationship_user_id','=',Auth::user()->id);
			}

			$data = $data->get()
			->toArray();	
		return $data;
	}	
    public static function UpdateMeetingLogByID($input_arr = array()){
        $data = DB::table('drm_meeting_logger')->where($input_arr['where'])->update($input_arr['data']);
        return true;
    }
	public static function starRating()
	{
		$session = session()->all();
		$show_all_arn_data = $session['logged_in_user_roles_and_permissions']['role_details']['show_all_arn_data'];
		$min_user_rating=getSettingsTableValue('MIN_USER_RATING');
		if(!($show_all_arn_data == 1)){
			$query = DB::table('drm_meeting_logger')
			->select('user_id', DB::raw('COUNT(id) as count'), DB::raw('SUM(customer_given_rating) as total_rating'), DB::raw('ROUND(AVG(customer_given_rating), 1) as rating'))
			->where('customer_given_rating', '>', 0);
			$query->where('user_id', '=', Auth::user()->id);
			$result = $query
			->groupBy('user_id')
			->havingRaw('COUNT(id) >='.$min_user_rating)
			->first();
		}else{
			$result=[];
		}

		return $result;
	}


    public static function getMeetingLogList($input_arr = array()){
        extract($input_arr);                // Import variables into the current symbol table from an array

        $flag_refresh_datatable = false;    // decides whether to just refresh datatable or complete page
        $output_arr = array();              // stores datatable required JSON output values
        if(isset($load_datatable) && is_numeric($load_datatable) && ($load_datatable == 1)){
            $flag_refresh_datatable = true;
        }

        $flag_export_data = false;          // decides whether request came for exporting the data or not
        if(isset($export_data) && !empty($export_data) && ($export_data == 1)){
            $flag_export_data = true;
        }

        if($flag_export_data){
            if(isset($columns) && !empty($columns) && json_decode($columns) !== FALSE){
                $columns = json_decode($columns, true);     // json_decode with parameter TRUE returns data in an array format
            }
            else{  $columns = array();  }
        }

        if(!isset($start) || empty($start) || !is_numeric($start)){
            $start = 0;
        }
        $start = intval($start);    // offset of records to be shown

        if(!isset($length) || empty($length) || !is_numeric($length)){
            $length = 10;        // default records to be shown on one page
        }
        $length = intval($length);

        $flag_get_user_meetings = false;    // helps to identify whether to show user wise meetings in clubbed manner or not
        if(isset($get_user_meetings) && ($get_user_meetings == 1)){
            $flag_get_user_meetings = true;
        }

        $where_conditions = array();
        $where_in_conditions = array();
        // $columns variable have list of all Table Headings/ Column names associated against the datatable
        if(isset($columns) && is_array($columns) && count($columns) > 0){
            foreach($columns as $key => $value){
                // data key have field names for whom data needs to be searched
                if(isset($value['search']['value'])){
                    $value['search']['value'] = trim($value['search']['value']);        // removing leading & trailing spaces from the searched value
                }
                switch($value['data']){
                    case 'start_datetime':
                    case 'end_datetime':
                    case 'created_at':
                    case 'updated_at':
                        if(isset($value['search']['value']) && !empty($value['search']['value']) && strpos($value['search']['value'], ';') !== FALSE){
                            $searching_dates = explode(';', $value['search']['value']);
                            if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE && isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                // when both FROM DATE & TO DATE both are present
                                $where_conditions[] = array('a.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                $where_conditions[] = array('a.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                            }
                            else{
                                // when either of the FROM DATE & TO DATE are present
                                if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE){
                                    $where_conditions[] = array('a.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                }
                                if(isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                    $where_conditions[] = array('a.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                                }
                            }
                            unset($searching_dates);
                        }
                        break;
                    case 'ARN':
                    case 'bdm_data':
                    case 'zonal_Head':
                    case 'meeting_purpose':
                    case 'contact_person_name':
                    case 'bdm_name':
                    case 'bdm_email':
                    case 'bdm_mobile':
                    case 'bdm_employee_code':
                        // if(isset($value['search']['value']) && !empty($value['search']['value'])){
                        //     if($value['data'] == 'bdm_name'){
                        //         $value['data'] = 'users.name';
                        //     }
						// 	if($value['data'] == 'meeting_purpose'){
                        //         $value['data'] = 'a.meeting_purpose';
                        //     }
                        //     elseif($value['data'] == 'bdm_email'){
                        //         $value['data'] = 'users.email';
                        //     }
                        //     elseif($value['data'] == 'bdm_mobile'){
                        //         $value['data'] = 'users_details.mobile_number';
                        //     }
                        //     elseif($value['data'] == 'bdm_employee_code'){
                        //         $value['data'] = 'users_details.employee_code';
                        //     }
                        //     else{
                        //         $value['data'] = 'a.'. $value['data'];
                        //     }

                        //     if($value['data'] == 'a.ARN' && isset($exact_arn_match) && (intval($exact_arn_match) == 1)){
                        //         $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                        //     }
                        //     else{
                        //         if($value['data'] == 'a.contact_person_name'){
                        //             $where_conditions[] = array(DB::raw('CONCAT(a.contact_person_name,"::",a.contact_person_email,"::",a.contact_person_mobile)'), 'like', '%'. $value['search']['value'] .'%');
                        //         }
                        //         else{
                        //             $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                        //         }
                        //     }
                        // }
						if (isset($value['search']['value']) && !empty($value['search']['value'])) {
							$searchValue = $value['search']['value'];
						
							switch ($value['data']) {
								case 'bdm_name':
									$value['data'] = 'users.name';
									break;
								case 'meeting_purpose':
									$value['data'] = 'a.meeting_purpose';
									break;
								case 'bdm_email':
									$value['data'] = 'users.email';
									break;
								case 'bdm_mobile':
									$value['data'] = 'users_details.mobile_number';
									break;
								case 'bdm_employee_code':
									$value['data'] = 'users_details.employee_code';
									break;
								default:
									$value['data'] = 'a.' . $value['data'];
									break;
							}
						
							if ($value['data'] === 'a.ARN' && isset($exact_arn_match) && (intval($exact_arn_match) === 1)) {
								$where_conditions[] = [$value['data'], '=', $searchValue];
							} else {
								if ($value['data'] === 'a.contact_person_name') {
									$where_conditions[] = [DB::raw('CONCAT(a.contact_person_name,"::",a.contact_person_email,"::",a.contact_person_mobile)'), 'like', '%' . $searchValue . '%'];
								} else {
									$where_conditions[] = [$value['data'], 'like', '%' . $searchValue . '%'];
								}
							}
						}
						
                        break;
                    case 'email_sent_to_customer':
                    case 'sms_sent_to_customer':
                    case 'customer_response_received':
                    case 'is_rankmf_partner':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                            if($value['data'] == 'is_rankmf_partner'){
                                $where_conditions[] = array('drm_distributor_master.'. $value['data'], '=', $value['search']['value']);
                            }
                            else{
                                $where_conditions[] = array('a.'. $value['data'], '=', $value['search']['value']);
                            }
                        }
                        break;
                    default:
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array('a.'. $value['data'], '=', $value['search']['value']);
                        }
                }
            }
            unset($key, $value);
        }

        $order_by_clause = '';
        // $order is the POST variable sent by Datatable plugin. We are taking on single column ordering that's why considering $order[0] element only.
        $order_by_field = "a.start_datetime";
        if(isset($order[0]) && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
            if($columns[$order[0]['column']]['data'] == 'bdm_name'){
                $order_by_field = "users.name";
            }
            elseif($columns[$order[0]['column']]['data'] == 'total_ind_aum'){
                $order_by_field = "drm_distributor_master.total_ind_aum";
            }
            elseif($columns[$order[0]['column']]['data'] == 'is_rankmf_partner'){
                $order_by_field = "drm_distributor_master.is_rankmf_partner";
            }
            else{
                $order_by_field = "a.". $columns[$order[0]['column']]['data'];
            }
        }
        
        $dir = "DESC";
        if(isset($order[0]) && isset($order[0]['dir'])) {
            $dir = $order[0]['dir'];
        }

        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $arr_select_fields = array('a.*','users.name as bdm_name','drm_distributor_master.is_rankmf_partner','drm_distributor_master.total_ind_aum', DB::raw("COALESCE(drm_user_goal.target_meetings,(SELECT value FROM settings WHERE `key` = 'BDM_TARGET_MEETINGS' LIMIT 1)) as target_meetings"), DB::raw("COALESCE(drm_user_goal.target_calls,(SELECT value FROM `settings` WHERE `key` = 'BDM_TARGET_CALLS' LIMIT 1)) as target_calls"));
        if(!$flag_get_user_meetings){
            // retrieving last_meeting_date only when get_user_meetings is ZERO
            $arr_select_fields[] = DB::raw("(SELECT IFNULL(b.start_datetime, 'NA') FROM drm_meeting_logger AS b WHERE b.ARN = a.ARN AND b.start_datetime < a.start_datetime ORDER BY b.start_datetime DESC LIMIT 0,1) AS last_meeting_date");
        }
        else{
            $arr_select_fields = array(
				'users.name AS bdm_name',
				'users.email AS bdm_email',
                'users_details.mobile_number AS bdm_mobile',
                'users_details.employee_code AS bdm_employee_code',
                'a.ARN',
				'a.start_datetime',
				'a.end_datetime',
                'a.meeting_mode',
				'a.meeting_remarks',
                'a.contact_person_name',
				'a.contact_person_email',
				'a.contact_person_mobile',
				'a.created_at',
				'a.updated_at',
				DB::raw("COALESCE(drm_user_goal.target_meetings,(SELECT value FROM settings WHERE `key` = 'BDM_TARGET_MEETINGS' LIMIT 1)) as target_meetings"),
				DB::raw("COALESCE(drm_user_goal.target_calls,(SELECT value FROM `settings` WHERE `key` = 'BDM_TARGET_CALLS' LIMIT 1)) as target_calls"),
			);
        }
        $records = DB::table('drm_meeting_logger AS a')
                        ->select($arr_select_fields)
                        ->join('drm_distributor_master', 'a.ARN', '=', 'drm_distributor_master.ARN')
                        ->join('users', 'users.id', '=','a.user_id')
                        ->leftjoin('drm_user_goal', 'users.id', '=','drm_user_goal.user_id')
                        ->join('users_details', 'users.id', '=', 'users_details.user_id');

        if(count($where_conditions) > 0){
            $records = $records->where($where_conditions);
        }
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $records = $records->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }

        $no_of_records = 0;
        if(!$flag_export_data){
            // calculating number of records without using LIMIT & OFFSET for pagination, only when showing data inside DataTable
            try{
                // $no_of_records = $records->where($where_conditions)->count();
                $no_of_records = $records->count();
            }
            catch(Exception $e){
            }

            if(isset($start) && is_numeric($start)){
                $records = $records->offset($start);
            }

            if(isset($length) && is_numeric($length)){
                $records = $records->limit($length);
            }
        }

        $arr_bdm_wise_meetings = array();
        try{
            // retrieving logged in user role and permission details
            if(!isset($logged_in_user_roles_and_permissions)){
                $logged_in_user_roles_and_permissions = array();
            }
            if(!isset($flag_have_all_permissions)){
                $flag_have_all_permissions = false;
            }
           $records = $records->orderBy($order_by_field,$dir)->get();
            if(!$records->isEmpty()){
                $arn_list = array();
                foreach($records as $key => $value){
					//print_r($value);
                    if(!$flag_get_user_meetings){
                        $value->action = '';
                    }

                    // showing View Distributors page link only when it have permission to do so
                    $date1 = new \DateTime($value->start_datetime);
                    $date2 = new \DateTime($value->end_datetime);
                    $difference = $date1->diff($date2);
                    $minutes = $difference->days * 24 * 60;
                    $minutes += $difference->h * 60;
                    $minutes += $difference->i;
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('view-detail', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE)){
                            $value->action .= "<a  href='javascript:void(0);'title='View'><i class='icons view-icon' title='View Record' onclick='view_code($value->id)'></i></a>";
							if(strtotime(date('Y-m-d')) < strtotime($value->start_datetime)){
								$value->action .= "<a href='meetinglog/edit/" . $value->id . "' title='Edit'><i class='icons edit-icon' title='Edit Record'></i></a>";
							}
							
                        }
                        // if(intval($value->customer_response_received) == 0 && (intval($value->email_sent_to_customer) == 0 || intval($value->sms_sent_to_customer) == 0)){
                        //     if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('meeting-feedback-notification', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE)){
                        //         $value->action .= "<a href='javascript:void(0);' onclick='send_feedback_notification($value->id)' class='btn btn-primary btn-sm'>Send Notification</a>";
                        //         }
                        // }
                        $value->contact_person_name = '<b>Name:</b> '. $value->contact_person_name.'<br><b>Email:</b> '. $value->contact_person_email .'<br><b>Mobile:</b> '. $value->contact_person_mobile;
                    }
                    elseif(!$flag_get_user_meetings){
                        $value->contact_person_name = "Name: ". $value->contact_person_name."\nEmail:". $value->contact_person_email ."\nMobile:". $value->contact_person_mobile;
                    }

                    $value->start_datetime = $value->start_datetime;
                    $value->end_datetime = $value->end_datetime;
                    $value->meeting_hour = ((intval($minutes)>0)?$minutes:"");

                    if(!$flag_get_user_meetings){
                        // retrieving customer feedback only when get_user_meetings is ZERO
                        $value->email_sent_to_customer = !empty($value->email_sent_to_customer) ? 'Yes':'No';
                        $value->sms_sent_to_customer = !empty($value->sms_sent_to_customer) ? 'Yes':'No';
                        $value->customer_response_received = !empty($value->customer_response_received) ? 'Yes':'No';
                        $value->is_rankmf_partner = !empty($value->is_rankmf_partner) ? 'Yes':'No';
                        $value->product_information_received = !empty($value->product_information_received) ? 'Yes':'No';
                    }

                    if($flag_get_user_meetings){
                        if(!isset($value->bdm_employee_code)){
                            $value->bdm_employee_code = ($value->bdm_email);
                        }

                        $value->bdm_employee_code = trim(strip_tags($value->bdm_employee_code));
                        if(strtolower(substr($value->bdm_employee_code, 0, 1)) !== 'e'){
                            $value->bdm_employee_code = 'E'. $value->bdm_employee_code;
                        }

                        if(!isset($arr_bdm_wise_meetings[$value->bdm_employee_code])){
                            $arr_bdm_wise_meetings[$value->bdm_employee_code] = array('bdm_name' => ($value->bdm_name??''), 'bdm_email' => ($value->bdm_email??''), 'bdm_mobile' => ($value->bdm_mobile??''), 'bdm_employee_code' => ($value->bdm_employee_code??''), 'meetings' => array());
                        }
                        $arr_bdm_wise_meetings[$value->bdm_employee_code]['meetings'][] = array('ARN' => ($value->ARN??''), 'start_datetime' => ($value->start_datetime??''), 'end_datetime' => ($value->end_datetime??''), 'meeting_mode' => ($value->meeting_mode??''), 'meeting_remarks' => ($value->meeting_remarks??''), 'contact_person_name' => ($value->contact_person_name??''), 'contact_person_email' => ($value->contact_person_email??''), 'contact_person_mobile' => ($value->contact_person_mobile??''), 'meeting_time' => ($value->meeting_hour??''),'created_at'=>($value->created_at??''),'updated_at'=>($value->updated_at??''));
                    }
					if(isset($value->target_meetings) && isset($value->bdm_employee_code)){
						$arr_bdm_wise_meetings[$value->bdm_employee_code]['target_meetings'] = $value->target_meetings;
					}
					if(isset($value->target_calls) && isset($value->bdm_employee_code)){
						$arr_bdm_wise_meetings[$value->bdm_employee_code]['target_calls'] = $value->target_calls;
					}
                }
                unset($key, $value);
            }
            unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions);
        }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }

        if($flag_get_user_meetings){
            // re-setting records variable because wanted to have BDM wise clubbed meeting details
            $records = $arr_bdm_wise_meetings;
            $no_of_records = count($arr_bdm_wise_meetings);
        }
        unset($where_conditions, $where_in_conditions, $order_by_clause, $arr_bdm_wise_meetings);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

	public static function getMeetingGoalList($input_arr){

		extract($input_arr);

		$working_days = 1;
		$actual_days = 1;

		switch($period){
			case 'Daily':
				$working_days = 1;
				$actual_days = 1;
			break;
			case 'Weekly':
				if(empty($select_user)){
					$working_days = 6;
				}
				$actual_days = date('w') + 1;
			break;
			case 'Monthly':
				if(empty($select_user)){
					$working_days = 22;
				}
				$actual_days = date('d');
			break;
		}

		$target_meetings = getSettingsTableValue('BDM_TARGET_MEETINGS');

		$target_calls = getSettingsTableValue('BDM_TARGET_CALLS');

		$user_id = '';

		if(!empty($select_user)){
			$user_id = " user_id = $select_user AND ";
		}
		
		if(!isset($show_users_data) || empty($show_users_data)){
            $show_users_data = false;
        }
        elseif($show_users_data){
            $flag_show_all_arn_data = false;
            $where_conditions[] = array('users_details.status', '=', 1);
        }

		$records = DB::table('users')
			->select([
				'users.name',
				'users.id',
				DB::raw('IFNULL(a.meeting_mode, "") AS meeting_mode'),
				DB::raw('COALESCE(drm_user_goal.target_calls * '.$working_days.','.$target_calls*$working_days.') AS target_calls'),
				DB::raw('COALESCE(drm_user_goal.target_meetings * '.$working_days.','.$target_meetings*$working_days.') AS target_meetings'),
				DB::raw('COUNT(a.id) AS count'),
				DB::raw('DATE(`a`.`start_datetime`) as date')
			])
			->Join('users_details',function($join){
				$join->on('users.id', '=', 'users_details.user_id')
				->whereIn('users_details.role_id',[3,4])
				->where('users_details.is_old', '=',0)
				->where('users_details.is_deleted', '=',0);
			})
			->leftJoin(DB::raw('(select * from drm_meeting_logger where '.$user_id.' date(`end_datetime`) <= "'.date("Y-m-d", time()).'" AND date(`end_datetime`) > "'.date("Y-m-d", strtotime('-'. 24*$actual_days .' hours', time())).'" AND (`meeting_remarks` != "" OR `meeting_remarks` IS NOT NULL)) as a'), 'users.id', '=', 'a.user_id')
			->leftJoin('drm_user_goal', 'users.id', '=', 'drm_user_goal.user_id')
			->leftJoin('drm_distributor_master', 'a.ARN', '=', 'drm_distributor_master.ARN');

		if(is_array($where_conditions) && count($where_conditions) > 0){
			$records = $records->where($where_conditions);
		}

		if(empty($select_user)){
			$records->groupBy('users.id', 'a.meeting_mode');
		}else{
			$records->groupBy(DB::raw('DATE(`a`.`start_datetime`)'), 'a.meeting_mode')->where('users.id','=',$select_user);
		}

		if(isset($start) && is_numeric($start)){
			$records->offset(intval(trim($start)));
		}

		if(isset($length) && is_numeric($length)){
			$records->limit(intval(trim($length)));
		}

		$where_in_conditions = [];

		// checking whether to show all ARN data or not
		$retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);

		//print_r($retrieve_users_data);

		if(!$retrieve_users_data['flag_show_all_arn_data']){
			// as all ARN data should not be shown that's why assigning only supervised user list
			$where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
		}

        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $records = $records->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }

		//echo $records->toSQL(); exit;

		$no_of_records = $records->get()->count();

		$records = $records->get()->toArray();

		//print_r($records);

		if(!empty($select_user)){

			$start = date('Y-m-d', strtotime("-".($actual_days - 1)." day", strtotime(date('Y-m-d'))));
			$end = date('Y-m-d');

			$startDateTime = new \DateTime($start);

			$endDateTime = new \DateTime($end);

			$records = array_map(function($object) {
				return (array)$object;
			}, $records);

			//print_r($records);
			$tmp = [];
			foreach($records as $record){
				$tmp[$record['date'].'-'.$record['meeting_mode']] = $record;
			}
			$records = $tmp;
			//print_r($records);

			$datesInRange = [];

			while ($startDateTime <= $endDateTime) {
				$datesInRange[$startDateTime->format('Y-m-d')] = true;
				$startDateTime->modify('+1 day');
			}

			$user = DB::table('users')
				->select([
					'users.name',
					'users.id',
					DB::raw("COALESCE(drm_user_goal.target_meetings,(SELECT value FROM settings WHERE `key` = 'BDM_TARGET_MEETINGS' LIMIT 1)) as target_meetings"),
					DB::raw("COALESCE(drm_user_goal.target_calls,(SELECT value FROM `settings` WHERE `key` = 'BDM_TARGET_CALLS' LIMIT 1)) as target_calls")
				])
				->leftjoin('drm_user_goal', 'users.id', '=', 'drm_user_goal.user_id')
				->where('users.id',$select_user)
				->limit(1)
				->get()
				->toArray();

			//print_r($user);
			$mods = config('constants');
			$mods = $mods['MEETING_MODE'];
			
			foreach($datesInRange as $key => $value){
				$date = $key;
				foreach($mods as $mod){
					if(!empty(trim($key))){
						$key = $key.'-'.$mod;
						if(isset($records[$key])){
							$records[$key] = (object)$records[$key];
						}else{
								$records[$key] = (object)[
									'name' => $user[0]->name,
									'id' => $user[0]->id,
									'meeting_mode' => '',
									'target_calls' => $user[0]->target_calls*$working_days,
									'target_meetings' => $user[0]->target_meetings*$working_days,
									'count' => 0,
									'date' => $date
								];
						}
					}
					$key = $mod = '';
				}
			}

			unset($records['-']);

			//print_r($records);

			$no_of_records = count($records);
			$records = array_values($records);
		}
		
		return array('records' => $records, 'no_of_records' => $no_of_records);
	}
	public static function getNearestPinCode($partner_pinCode){
		$pindata =  DB::table('drm_nearest_pinmap')
				->select('*')
				->where('pincode', $partner_pinCode)
				->first();
		return $pindata;	
	}

}
