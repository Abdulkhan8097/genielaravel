<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class DistributorsModel extends Model
{
    use HasFactory;
    public static function getDistributorsList($input_arr = array()){
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
                    case 'created_at':
                    case 'arn_valid_from':
                    case 'arn_valid_till':
                    case 'ind_aum_as_on_date':
                        if(isset($value['search']['value']) && !empty($value['search']['value']) && strpos($value['search']['value'], ';') !== FALSE){
                            $searching_dates = explode(';', $value['search']['value']);
                            if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE && isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                // when both FROM DATE & TO DATE both are present
                                $where_conditions[] = array('drm_distributor_master.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                $where_conditions[] = array('drm_distributor_master.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                            }
                            else{
                                // when either of the FROM DATE & TO DATE are present
                                if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE){
                                    $where_conditions[] = array('drm_distributor_master.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                }
                                if(isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                    $where_conditions[] = array('drm_distributor_master.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                                }
                            }
                            unset($searching_dates);
                        }
                        break;
                    case 'status':
                    case 'is_rankmf_partner':
                    case 'is_samcomf_partner':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                            if($value['data'] == 'status'){
                                $value['data'] = 'drm_distributor_master.status';
                            }
                            $where_conditions[] = array($value['data'], '=', intval($value['search']['value']));
                        }
                        break;
                    case 'ARN':
                    case 'arn_holders_name':
                    case 'arn_email':
                    case 'arn_telephone_r':
                    case 'arn_telephone_o':
                    case 'arn_city':
                    case 'pincode_city':
                    case 'arn_state':
                    case 'bdm_name':
                    case 'bdm_email':
                    case 'bdm_mobile':
                    case 'bdm_designation':
                    case 'reporting_to_name':
                    case 'reporting_to_email':
                    case 'reporting_to_mobile':
                    case 'reporting_to_designation':
                    case 'arn_pincode':
                    case 'arn_euin':
                    case 'rankmf_email':
                    case 'rankmf_mobile':
                    case 'samcomf_email':
                    case 'samcomf_mobile':
                    case 'alternate_mobile_1':
                    case 'alternate_email_1':
					case 'arn_zone':
					case 'rankmf_partner_code':
						if($value['data'] == 'rankmf_partner_code' && isset($value['search']['value']) && !empty($value['search']['value'])){

							$where_conditions[] = array('drm_distributor_master.rankmf_partner_code', 'like', '%'. $value['search']['value'] .'%');

						}
                        if($value['data'] == 'bdm_name' && isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array('users.name', 'like', '%'. $value['search']['value'] .'%');
                        }else
						if ($value['data'] == 'alternate_mobile_1' && isset($value['search']['value']) && !empty($value['search']['value'])) {
							$where_conditions[] = [DB::raw("CONCAT_WS('::', drm_distributor_master.alternate_mobile_1, drm_distributor_master.alternate_mobile_2, drm_distributor_master.alternate_mobile_3, drm_distributor_master.alternate_mobile_4, drm_distributor_master.alternate_mobile_5)"), 'like', '%' . $value['search']['value'] . '%'];
						}else
						if ($value['data'] == 'alternate_email_1' && isset($value['search']['value']) && !empty($value['search']['value'])) {
							$where_conditions[] = [DB::raw("CONCAT_WS('::', drm_distributor_master.alternate_email_1, drm_distributor_master.alternate_email_2, drm_distributor_master.alternate_email_3, drm_distributor_master.alternate_email_4, drm_distributor_master.alternate_email_5)"), 'like', '%' . $value['search']['value'] . '%'];
						}
                        elseif($value['data'] == 'bdm_email' && isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array('users.email', 'like', '%'. $value['search']['value'] .'%');
                        }
                        elseif($value['data'] == 'bdm_mobile' && isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array('users_details.mobile_number', 'like', '%'. $value['search']['value'] .'%');
                        }
                        elseif($value['data'] == 'bdm_designation' && isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array('users_details.designation', 'like', '%'. $value['search']['value'] .'%');
                        }
                        elseif($value['data'] == 'reporting_to_name' && isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array('reporting_to_tbl.name', 'like', '%'. $value['search']['value'] .'%');
                        }
                        elseif($value['data'] == 'reporting_to_email' && isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array('reporting_to_tbl.email', 'like', '%'. $value['search']['value'] .'%');
                        }
                        elseif($value['data'] == 'reporting_to_mobile' && isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array('reporting_to_tbl_details.mobile_number', 'like', '%'. $value['search']['value'] .'%');
                        }
                        elseif($value['data'] == 'reporting_to_designation' && isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array('reporting_to_tbl_details.designation', 'like', '%'. $value['search']['value'] .'%');
                        }
                        elseif(isset($value['search']['value']) && !empty($value['search']['value'])){
                            if(isset($value['search']['exact_match']) && $value['search']['exact_match']){
                                $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                            }
                            else{
                                $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                            }
                        }
                        break;
                    default:
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                        }
                }
            }
            unset($key, $value);
        }

        $order_by_clause = '';
        // $order is the POST variable sent by Datatable plugin. We are taking on single column ordering that's why considering $order[0] element only.
        if(isset($order[0]['column']) && isset($order[0]['dir']) && isset($columns[$order[0]['column']]['data']) && !empty($columns[$order[0]['column']]['data'])){
            // $columns variable have list of all Table Headings/ Column names associated against the datatable
            switch($columns[$order[0]['column']]['data']){
                default:
                    $order_by_clause = $columns[$order[0]['column']]['data'];
            }
            $order_by_clause .= ' ' .$order[0]['dir'];
        }
        else{
            $order_by_clause = 'drm_distributor_master.ARN ASC';
        }
        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $records = DB::table('drm_distributor_master')
                    ->select('drm_distributor_master.ARN', 'drm_distributor_master.arn_holders_name',
                             'drm_distributor_master.arn_email', 'drm_distributor_master.arn_telephone_r',
                             'drm_distributor_master.arn_telephone_o', 'drm_distributor_master.arn_address',
                             'drm_distributor_master.arn_city', 'drm_distributor_master.pincode_city', 'drm_distributor_master.arn_state', 'drm_distributor_master.arn_pincode',
                             'drm_distributor_master.status', 'drm_distributor_master.arn_euin', 'drm_distributor_master.arn_kyd_compliant',
                             (!$flag_export_data?'drm_distributor_master.created_at':DB::raw('DATE_FORMAT(drm_distributor_master.created_at, "%d/%m/%Y") AS created_at')),
                             (!$flag_export_data?'drm_distributor_master.arn_valid_from':DB::raw('DATE_FORMAT(drm_distributor_master.arn_valid_from, "%d/%m/%Y") AS arn_valid_from')),
                             (!$flag_export_data?'drm_distributor_master.arn_valid_till':DB::raw('DATE_FORMAT(drm_distributor_master.arn_valid_till, "%d/%m/%Y") AS arn_valid_till')),
                             'drm_distributor_master.distributor_category', 'drm_distributor_master.project_focus',
                             'drm_distributor_master.project_emerging_stars',
                             'drm_distributor_master.arn_avg_aum', 'drm_distributor_master.arn_total_commission',
                             'drm_distributor_master.arn_yield', 'drm_distributor_master.arn_business_focus_type',
                             'drm_distributor_master.rankmf_partner_aum', 'drm_distributor_master.samcomf_live_sip_amount',
                             'drm_distributor_master.samcomf_partner_netinflow', 'drm_distributor_master.samcomf_partner_aum',
                             'drm_distributor_master.total_aum', 'users.name AS bdm_name',
                             'users.email AS bdm_email', 'users_details.mobile_number AS bdm_mobile',
                             'users_details.designation AS bdm_designation', 'reporting_to_tbl.email AS reporting_to_email',
                             'reporting_to_tbl_details.mobile_number AS reporting_to_mobile',
                             'reporting_to_tbl_details.designation AS reporting_to_designation',
                             'reporting_to_tbl.name AS reporting_to_name', 'drm_distributor_master.is_rankmf_partner',
                             'drm_distributor_master.samcomf_stage_of_prospect',
                             'drm_distributor_master.rankmf_partner_code',
							 'drm_distributor_master.rankmf_email',
                             'drm_distributor_master.samcomf_stage_of_prospect', 'drm_distributor_master.rankmf_email',
                             'drm_distributor_master.rankmf_mobile', 'drm_distributor_master.samcomf_email',
                             'drm_distributor_master.samcomf_mobile', 'drm_distributor_master.rm_relationship',
                             'drm_distributor_master.total_ind_aum', 'drm_distributor_master.relationship_quality_with_arn',
                             (!$flag_export_data?'drm_distributor_master.ind_aum_as_on_date':DB::raw('DATE_FORMAT(drm_distributor_master.ind_aum_as_on_date, "%d/%m/%Y") AS ind_aum_as_on_date')),
                             'drm_distributor_master.arn_zone', 'drm_distributor_master.project_green_shoots',
							 'drm_distributor_master.alternate_mobile_1',
							 'drm_distributor_master.alternate_mobile_2',
							 'drm_distributor_master.alternate_mobile_3',
							 'drm_distributor_master.alternate_mobile_4',
							 'drm_distributor_master.alternate_mobile_5',
							 'drm_distributor_master.alternate_email_1',
							 'drm_distributor_master.alternate_email_2',
							 'drm_distributor_master.alternate_email_3',
							 'drm_distributor_master.alternate_email_4',
							 'drm_distributor_master.alternate_email_5')
                    ->leftJoin('users', 'drm_distributor_master.direct_relationship_user_id', '=', 'users.id')
                    ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
                    ->leftJoin('users AS reporting_to_tbl', 'users_details.reporting_to', '=', 'reporting_to_tbl.id')
                    ->leftJoin('users_details AS reporting_to_tbl_details', 'reporting_to_tbl.id', '=', 'reporting_to_tbl_details.user_id')
					->whereNotNull('drm_distributor_master.ARN')
					->where('drm_distributor_master.ARN','!=','');

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

        try{
            // while doing export of data returning the query conditions, order by clause
            if($flag_export_data){
                return array('where_conditions' => $where_conditions, 'where_in_conditions' => $where_in_conditions, 'order_by_clause' => $order_by_clause);
            }

            // retrieving logged in user role and permission details
            if(!isset($logged_in_user_roles_and_permissions)){
                $logged_in_user_roles_and_permissions = array();
            }
            if(!isset($flag_have_all_permissions)){
                $flag_have_all_permissions = false;
            }

            $records = $records->orderByRaw($order_by_clause)->get();
			// x($records->toSql());
			
            if(!$records->isEmpty()){
                foreach($records as $key => &$value){
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        $value->action  = '';
                        // showing View Distributors page link only when it have permission to do so
                        if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('distributor/{arn_number}', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE)){
                            $value->action .= '<a target="_blank"  class=" btn btn-primary btn-sm" href="'. env('APP_URL') .'/distributor/'. $value->ARN .'" title="View Record">View Record</a><br>';
                        }
                        // showing Add/Create Meeting page link only when it have permission to do so
                        if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('meetinglog/create/{arn_number}', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE)){
                            $value->action .= '<a target="_blank"  class=" btn btn-primary btn-sm" href="'. env('APP_URL') .'/meetinglog/create/'. $value->ARN .'" title="Add Meeting" style="margin-top: 3px;">Add Meeting</a>';
                        }

						$tmp = [
							$value->alternate_mobile_1,
							$value->alternate_mobile_2,
							$value->alternate_mobile_3,
							$value->alternate_mobile_4,
							$value->alternate_mobile_5,
						];

						$value->alternate_mobile_1 = implode("<br/>",array_filter($tmp));
						// $value->alternate_mobile_1 = "". $value->alternate_mobile_1."<br>". $value->alternate_mobile_1 ."<br>". $value->alternate_mobile_1."<br>". $value->alternate_mobile_4."<br>". $value->alternate_mobile_5;
						$tmp1 = [
							$value->alternate_email_1,
							$value->alternate_email_2,
							$value->alternate_email_3,
							$value->alternate_email_4,
							$value->alternate_email_5,
						];
						$value->alternate_email_1 = implode("<br/>",array_filter($tmp1));
						// $value->alternate_email_1 = "". $value->alternate_email_1."<br>". $value->alternate_email_2 ."<br>". $value->alternate_email_3."<br>". $value->alternate_email_4."<br>". $value->alternate_email_5;
                    }

					$value->distributor_category = '<input type="text" value="'.$value->distributor_category.'" class="distributor_category not_editable" />';

                    // assigning label to current partner a readable status i.e. Created/Activated etc.
                    if(isset($value->status) && (intval($value->status) == 1)){
                        $value->status = 'Active';
                    }
                    else{
                        $value->status = 'Inactive';
                    }

                    if(isset($value->project_focus) && !empty($value->project_focus)){
                        $value->project_focus = ucfirst($value->project_focus);
                    }

                    if(isset($value->project_emerging_stars) && !empty($value->project_emerging_stars)){
                        $value->project_emerging_stars = ucfirst($value->project_emerging_stars);
                    }

                    if(isset($value->project_green_shoots) && !empty($value->project_green_shoots)){
                        $value->project_green_shoots = ucfirst($value->project_green_shoots);
                    }

                    if(isset($value->is_rankmf_partner) && (intval($value->is_rankmf_partner) == 1)){
                        $value->is_rankmf_partner = 'Yes';
                    }
                    else{
                        $value->is_rankmf_partner = 'No';
                    }

                    if(isset($value->is_samcomf_partner) && (intval($value->is_samcomf_partner) == 1)){
                        $value->is_samcomf_partner = 'Yes';
                    }
                    else{
                        $value->is_samcomf_partner = 'No';
                    }

                    if(isset($value->rm_relationship) && !empty($value->rm_relationship)){
                        $value->rm_relationship = ucfirst($value->rm_relationship);
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
        unset($where_conditions, $where_in_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }
	public static function getDistributorsListbyusers($input_arr = array()){
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

        $where_conditions = array();
        $where_in_conditions = array();
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

		$records = DB::table('drm_distributor_master')
		->select('drm_distributor_master.ARN', 'drm_distributor_master.arn_holders_name','drm_distributor_master.arn_email', 'drm_distributor_master.arn_telephone_r',
				'drm_distributor_master.arn_telephone_o', 'drm_distributor_master.arn_address',
				'drm_distributor_master.arn_city', 'drm_distributor_master.pincode_city', 'drm_distributor_master.arn_state', 'drm_distributor_master.arn_pincode')
		->whereNotNull('drm_distributor_master.ARN')
		->where('drm_distributor_master.ARN', '!=' , '');
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

        
        }

        try{

            // retrieving logged in user role and permission details
            if(!isset($logged_in_user_roles_and_permissions)){
                $logged_in_user_roles_and_permissions = array();
            }
            if(!isset($flag_have_all_permissions)){
                $flag_have_all_permissions = false;
            }

            $records = $records->get()->toArray();
			
          
            unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions);
        }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        unset($where_conditions, $where_in_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getDistributorByARN($arn_number, $input_arr = array()){
        $where_in_conditions = array();
        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $data =  DB::table('drm_distributor_master')
                    ->leftJoin('users', 'drm_distributor_master.direct_relationship_user_id', '=', 'users.id')
                    ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
                    ->leftJoin('users AS reporting_to_tbl', 'users_details.reporting_to', '=', 'reporting_to_tbl.id')
                    ->select('drm_distributor_master.*', 'users.name AS bdm_name', 'reporting_to_tbl.name AS reporting_to_name')
                    ->where('drm_distributor_master.ARN', '=', $arn_number);
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $data = $data->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }
        $data = $data->get();
        unset($where_in_conditions);
        return $data;
    }
	public static function getDistributorByPincode($NearestPin, $ARN, $input_arr = array()){
        $where_in_conditions = array();
        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $data = DB::table('drm_distributor_master')
				->select('ARN', 'arn_holders_name', 'arn_pincode', 'arn_email', DB::raw('"action" as action'),DB::raw("IF(is_rankmf_partner = '0', 'No', 'Yes') as is_rankmf_partner"), 'arn_address','arn_pincode', 'arn_city', 'arn_telephone_r', 'arn_telephone_o')
				->where('ARN','<>',$ARN)
				->whereIn('arn_pincode', $NearestPin);
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $data = $data->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }
        // $data = $data->get();
        unset($where_in_conditions);
        return $data;
    }

	/*
	To edit metting logger data
	*/
	public static function getMeetingEdit($logID, $input_arr = array()){
        $where_in_conditions = array();
        
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            
            $where_in_conditions['drm_meeting_logger.user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);
        $data =  DB::table('drm_meeting_logger')
                    ->leftJoin('users', 'drm_meeting_logger.user_id', '=', 'users.id')
                    ->leftJoin('drm_distributor_master', 'drm_meeting_logger.ARN', '=', 'drm_distributor_master.ARN')
                    ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
                    ->leftJoin('users AS reporting_to_tbl', 'users_details.reporting_to', '=', 'reporting_to_tbl.id')
                    ->select('drm_distributor_master.*','drm_meeting_logger.*', 'users.name AS bdm_name', 'reporting_to_tbl.name AS reporting_to_name')
                    ->where('drm_meeting_logger.id', '=', $logID);
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $data = $data->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }
        // echo $data = $data->toSql();
		$data = $data->get();
		// die();
        unset($where_in_conditions);
        return $data;
    }

    public static function getAmcWiseDataByARN($arn_number, $input_arr = array()){
        $where_in_conditions = array();
        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $data =  DB::table('drm_project_focus_amc_wise_details')
                    ->join('drm_distributor_master', 'drm_project_focus_amc_wise_details.ARN', '=', 'drm_distributor_master.ARN')
                    ->select('drm_project_focus_amc_wise_details.*')
                    ->where('drm_project_focus_amc_wise_details.ARN', '=', $arn_number);
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $data = $data->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }
        $data = $data->get();
        unset($where_in_conditions);
        return $data;
    }

    public static function getAmcWiseDataByARNToExport($input_arr = array()){
        extract($input_arr);                // Import variables into the current symbol table from an array
        $where_in_conditions = array();
        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $data =  DB::table('drm_project_focus_amc_wise_details')
                    ->join('drm_distributor_master', 'drm_project_focus_amc_wise_details.ARN', '=', 'drm_distributor_master.ARN')
                    ->select(array('drm_project_focus_amc_wise_details.ARN', 'drm_project_focus_amc_wise_details.amc_name', 'drm_project_focus_amc_wise_details.total_commission_expenses_paid', 'drm_project_focus_amc_wise_details.gross_inflows', 'drm_project_focus_amc_wise_details.net_inflows', 'drm_project_focus_amc_wise_details.avg_aum_for_last_reported_year', 'drm_project_focus_amc_wise_details.closing_aum_for_last_financial_year', 'drm_project_focus_amc_wise_details.effective_yield', 'drm_project_focus_amc_wise_details.nature_of_aum', 'drm_project_focus_amc_wise_details.reported_year'))
        ->where('drm_project_focus_amc_wise_details.ARN', '=', $arn_number);
        if(isset($searched_text) && !empty($searched_text)){
            $data = $data->where(function($query) use($searched_text){
                $query->orWhere('drm_project_focus_amc_wise_details.amc_name', 'like', '%'. $searched_text .'%');
                $query->orWhere('drm_project_focus_amc_wise_details.nature_of_aum', 'like', '%'. $searched_text .'%');
            });
        }
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $data = $data->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }
        $data = $data->get();
        unset($where_in_conditions);
        return $data;
    }

    public static function UpdateSamcoPByArn($input_arr = array()){
        $arr_skip_fields_from_record_logs = array('front_visiting_card_image', 'back_visiting_card_image');
        if(isset($input_arr['data']) && is_array($input_arr['data']) && count($input_arr['data']) > 0){
            $arr_backup_record_details = array();
            $arr_fields_to_select = array_keys($input_arr['data']);
            $known_record_data = DB::table('drm_distributor_master')
                                    ->where($input_arr['where'])
                                    ->select($arr_fields_to_select)
                                    ->get();
            if(!$known_record_data->isEmpty() && isset($known_record_data[0]) && is_object($known_record_data[0]) && get_object_vars($known_record_data[0]) > 0){
                // converting StdClass object to an Array format
                $known_record_data = (array) $known_record_data[0];

                // looping for each field data for checking whether
                // a) that field value is allowed to be logged or not
                // b) old field value and new field value should not be same
                foreach($known_record_data as $field_key => $field_value){
                    $new_record_value = trim(strip_tags(($input_arr['data'][$field_key]??'')));
                    // preparing text values for fields which are coming from master tables
                    if($field_key == 'direct_relationship_user_id'){
                        // retrieving User(s) name for storing it into logs. STARS
                        $arr_searched_values = array();
                        if(isset($field_value) && !empty($field_value)){
                            $arr_searched_values[] = $field_value;
                        }

                        if(isset($new_record_value) && !empty($new_record_value)){
                            $arr_searched_values[] = $new_record_value;
                        }

                        if(count($arr_searched_values) > 0){
                            $master_record_data = DB::table('users')
                                                    ->where('is_drm_user', 1)
                                                    ->whereIn('id', $arr_searched_values)
                                                    ->select('id', 'name')
                                                    ->get();
                            if(!$master_record_data->isEmpty()){
                                foreach($master_record_data as $master_record_values){
                                    if(!empty($field_value) && $master_record_values->id == $field_value){
                                        $field_value = $master_record_values->name;
                                        break;
                                    }
                                    if(!empty($new_record_value) && $master_record_values->id == $new_record_value){
                                        $new_record_value = $master_record_values->name;
                                        break;
                                    }
                                }
                                unset($master_record_values);
                            }
                            unset($master_record_data);
                        }
                        unset($arr_searched_values);
                        // retrieving User(s) name for storing it into logs. ENDS
                    }

                    if(in_array($field_key, $arr_skip_fields_from_record_logs) !== FALSE || ($field_value == $new_record_value)){
                        // if field value should not be logged then moving to next field value available within a loop
                        continue;
                    }

                    // preparing backup record data
                    $arr_backup_record_details[] = array('ARN' => $input_arr['arn_number'],
                                                         'field_label' => $field_key,
                                                         'old_records' => $field_value,
                                                         'new_records' => $new_record_value,
                                                         'status' => 1,
                                                         'created_by' => $input_arr['logged_in_user_id']);
                    unset($new_record_value);
                }
                unset($field_key, $field_value);

                // checking record details available or not
                if(is_array($arr_backup_record_details) && count($arr_backup_record_details) > 0){
                    try{
                        DB::table('drm_distributor_master_logs')->insert($arr_backup_record_details);
                    }
                    catch(Exception | \Illuminate\Database\QueryException $e){
                        return array('err_flag' => 1, 'err_msg' => array('Unable to create record backup'));
                    }
                }
            }
            else{
                return array('err_flag' => 1, 'err_msg' => array('Record details not found'));
            }
        }
        unset($arr_fields_to_select, $known_record_data, $arr_backup_record_details);

        // retrieving existing field "record_last_available_in_amfi" data and updating back to it the record
        // because that field data should be updated only if record details fetched from MongoDB table: mf_arn_data
        $record_last_available_in_amfi = self::getRecordLastAvailableInAmfi($input_arr['arn_number']);
        if(isset($record_last_available_in_amfi) && !empty($record_last_available_in_amfi) && strtotime($record_last_available_in_amfi) !== FALSE){
            $input_arr['data'] = array_merge($input_arr['data'],
                                            array('record_last_available_in_amfi' => $record_last_available_in_amfi));
        }

        $where_in_conditions = array();
        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $data = DB::table('drm_distributor_master')->where($input_arr['where']);
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $data = $data->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }

        $output_arr['err_flag'] = 0;
        $output_arr['err_msg'] = array();
        try{
            $data = $data->update($input_arr['data']);
        }
        catch(Exception | \Illuminate\Database\QueryException $e){
            $output_arr['err_flag'] = 1;
            $output_arr['err_msg'] = array('Unable to update record details');
        }
        unset($where_in_conditions, $arr_skip_fields_from_record_logs);
        return $output_arr;
    }

    public static function getRecordLastAvailableInAmfi($arn_number){
        $record_last_available_in_amfi = '';
        if(!empty($arn_number)){
            // retrieving existing field "record_last_available_in_amfi" data and updating back to it the record
            // because that field data should be updated only if record details fetched from MongoDB table: mf_arn_data
            $record_details = DB::table('drm_distributor_master')
                                ->select('record_last_available_in_amfi')
                                ->where('ARN', '=', $arn_number)
                                ->get();
            if(!$record_details->isEmpty()){
                if(isset($record_details[0]->record_last_available_in_amfi) && !empty($record_details[0]->record_last_available_in_amfi) && strtotime($record_details[0]->record_last_available_in_amfi) !== FALSE){
                    $record_last_available_in_amfi = $record_details[0]->record_last_available_in_amfi;
                }
            }
        }
        return $record_last_available_in_amfi;
    }

    public static function getARNQualityScore($input_arr = array()){
        extract($input_arr);                // Import variables into the current symbol table from an array
        $where_conditions = array();
        $where_in_conditions = array();

        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['users_details.user_id'] = $retrieve_users_data['show_data_for_users'];
            $where_conditions[] = array('users_details.status', '=', 1);
        }
        unset($retrieve_users_data);

        $flag_date_range_filter_available = false;
        // searching data from the given start date
        if(isset($start) && !empty($start) && strtotime($start) !== FALSE){
            $start = date_create($start);
            // taking 1 previous day of currently passed input parameter $start, because we want to have different between the score of current day and it's previous day
            date_sub($start, date_interval_create_from_date_string("1 days"));
            $start = date_format($start, 'Y-m-d 00:00:00');
            $where_conditions[] = array('score.score_of_date', '>=', $start);
            $flag_date_range_filter_available = true;
        }

        // searching data till the given end date
        if(isset($end) && !empty($end) && strtotime($end) !== FALSE){
            $end = date('Y-m-d 23:59:59', strtotime($end));
            $where_conditions[] = array('score.score_of_date', '<=', $end);
        }

        // searching data for given set of user(s) only
        if(isset($user_id)){
            if(is_array($user_id) && count($user_id) > 0){
                // removing any whitespaces coming along with array element
                array_walk($user_id, function(&$_value){
                    $_value = intval(trim(strip_tags($_value)));
                });
                // removes any blank/empty array elements and keeping only unique elements within an array
                $user_id = array_unique(array_filter($user_id));
                $where_in_conditions['score.user_id'] = $user_id;
            }
            elseif(!empty($user_id) && is_numeric($user_id)){
                $where_conditions[] = array('score.user_id', '=', $user_id);
            }
        }

        // if date filters data not available then only retrieving max score_of_date for retrieving data
        if(!$flag_date_range_filter_available){
            // retrieving latest date of score calculation
            $max_score_of_date = DB::table('drm_users_arn_relationship_quality_score AS score')
                                    ->join('users_details', 'score.user_id', '=', 'users_details.user_id');
            if(count($where_conditions) > 0){
                $max_score_of_date = $max_score_of_date->where($where_conditions);
            }
            if(count($where_in_conditions) > 0){
                foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                    $max_score_of_date = $max_score_of_date->whereIn($in_condition_field, $in_condition_data);
                }
                unset($in_condition_field, $in_condition_data);
            }
            $max_score_of_date = $max_score_of_date->select(DB::raw('MAX(score_of_date) AS score_of_date'))->first();
            if(isset($max_score_of_date->score_of_date) && !empty($max_score_of_date->score_of_date) && strtotime($max_score_of_date->score_of_date) !== FALSE){
                $max_score_of_date = $max_score_of_date->score_of_date;
            }
            else{
                // if maximum score of date is not found then assigning it as current date
                $max_score_of_date = date('Y-m-d');
            }

            $where_conditions[] = array('score.score_of_date', $max_score_of_date);
        }

        // retrieving related user(s) score for relationship quality with ARN
        $users_data = DB::table('drm_users_arn_relationship_quality_score AS score')
                            ->join('users', 'score.user_id', '=', 'users.id')
                            ->join('users_details', 'users.id', '=', 'users_details.user_id')
                            ->select('users.name AS user_name', 'score.score_of_date', 'score.no_of_assigned_arn', 'score.maximum_score', 'score.calculated_score');
        if(is_array($where_conditions) && count($where_conditions) > 0){
            $users_data = $users_data->where($where_conditions);
        }
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $users_data = $users_data->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }
        $users_data = $users_data
                        ->orderby('score.score_of_date','ASC')
                        ->orderby('score.calculated_score','DESC')
                        ->get();
        unset($where_conditions, $where_in_conditions);
        return $users_data;
    }

    public static function getARNbyStateWise($input_arr = array()){
        extract($input_arr);                // Import variables into the current symbol table from an array
        $where_conditions = array();
        $where_in_conditions = array();

        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        $flag_show_all_arn_data = $retrieve_users_data['flag_show_all_arn_data'];
        if(!isset($show_users_data) || empty($show_users_data)){
            $show_users_data = false;
        }
        elseif($show_users_data){
            $flag_show_all_arn_data = false;
            $where_conditions[] = array('users_details.status', '=', 1);
        }
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
            $where_conditions[] = array('users_details.status', '=', 1);
        }
        unset($retrieve_users_data);

        $arn_data = DB::table('drm_distributor_master')
                        ->rightJoin('users', 'drm_distributor_master.direct_relationship_user_id', '=', 'users.id')
                        ->rightJoin('users_details',function($join){
							$join->on('users.id', '=', 'users_details.user_id')
							->whereIn('users_details.role_id',[3,4])
							->where('users_details.is_old', '=',0)
							->where('users_details.is_deleted', '=',0);
						})->whereNotNull($flag_show_all_arn_data?'drm_distributor_master.arn_state':'users.name')
						->Where($flag_show_all_arn_data?DB::raw('TRIM(drm_distributor_master.arn_state)'):DB::raw('TRIM(users.name)'),'!=','')
                        ->select(($flag_show_all_arn_data?'drm_distributor_master.arn_state':'users.name AS user_name'),
                                 DB::raw('SUM(IF(drm_distributor_master.is_rankmf_partner = 0, 1, 0)) AS not_empanelled'),
                                 DB::raw('SUM(IF(drm_distributor_master.is_rankmf_partner = 1, 1, 0)) AS empanelled'),
                                 DB::raw('COUNT(DISTINCT drm_distributor_master.ARN) AS total'));

        if(is_array($where_conditions) && count($where_conditions) > 0){
            $arn_data = $arn_data->where($where_conditions);
        }
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $arn_data = $arn_data->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }
        $arn_data = $arn_data->groupBy(($flag_show_all_arn_data?'drm_distributor_master.arn_state':'user_name'))
                        ->orderby(($flag_show_all_arn_data?'drm_distributor_master.arn_state':'user_name'),'ASC')
                        ->get();
		
        unset($where_conditions, $where_in_conditions, $flag_show_all_arn_data);
        return $arn_data;
    }

    public static function assign_users_to_arn($input_arr = array()){
        extract($input_arr);                // Import variables into the current symbol table from an array
        $err_flag = 0;                      // err_flag is 0 means no error
        $err_msg = array();                 // err_msg stores list of errors found during execution
        // stores required JSON output values
        $output_arr = array('display_messages' => array());

        // helps to identify whether to create log file and store all display messages for future references or not
        if(!isset($flag_log_display_messages) || empty($flag_log_display_messages)){
            $flag_log_display_messages = false;
        }

        $where_conditions_field = '';
        $where_conditions_data = array();

        $subquery_where_conditions_field = '';
        $subquery_where_conditions_data = array();

        $subquery_where_conditions_field_1 = '';
        $subquery_where_conditions_data_1 = array();

        // search the records based on input parameter ARN Number
        if(isset($arn_number) && !empty($arn_number)){
            $where_conditions_field .= ' AND drm.ARN = :mainquery_arn_number ';
            $where_conditions_data[':mainquery_arn_number'] = $arn_number;

            $subquery_where_conditions_field .= ' AND b.ARN = :subquery_arn_number ';
            $subquery_where_conditions_data[':subquery_arn_number'] = $arn_number;

            $subquery_where_conditions_field_1 .= ' AND b.ARN = :subquery_arn_number_1 ';
            $subquery_where_conditions_data_1[':subquery_arn_number_1'] = $arn_number;
        }

        // search the records which were created from and after the given input parameter from_date
        if(isset($from_date) && !empty($from_date) && strtotime($from_date) !== FALSE){
            $where_conditions_field .= ' AND drm.created_at >= :mainquery_from_date';
            $where_conditions_data[':mainquery_from_date'] = date('Y-m-d 00:00:00', strtotime($from_date));

            $subquery_where_conditions_field .= ' AND b.created_at >= :subquery_from_date';
            $subquery_where_conditions_data[':subquery_from_date'] = date('Y-m-d 00:00:00', strtotime($from_date));

            $subquery_where_conditions_field_1 .= ' AND b.created_at >= :subquery_from_date_1';
            $subquery_where_conditions_data_1[':subquery_from_date_1'] = date('Y-m-d 00:00:00', strtotime($from_date));
        }

        // search the records which were created till the given input parameter till_date
        if(isset($till_date) && !empty($till_date) && strtotime($till_date) !== FALSE){
            $where_conditions_field .= ' AND drm.created_at <= :mainquery_till_date';
            $where_conditions_data[':mainquery_till_date'] = date('Y-m-d 23:59:59', strtotime($till_date));

            $subquery_where_conditions_field .= ' AND b.created_at <= :subquery_till_date';
            $subquery_where_conditions_data[':subquery_till_date'] = date('Y-m-d 23:59:59', strtotime($till_date));

            $subquery_where_conditions_field_1 .= ' AND b.created_at <= :subquery_till_date_1';
            $subquery_where_conditions_data_1[':subquery_till_date_1'] = date('Y-m-d 23:59:59', strtotime($till_date));
        }

        $current_datetime = date('Y-m-d H:i:s');
        $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tScript start datetime";

        try{
            // # Query to update list of ARN(s) whose pincode is matching with exactly one BDM serving in that area
            DB::update('UPDATE (SELECT drm.ARN, drm.arn_pincode, IFNULL(drm.total_ind_aum, 0) AS total_ind_aum, users.id AS user_id, users.email AS user_email, users_details.cadre_of_employee, COUNT(drm.ARN) AS no_of_arn FROM drm_distributor_master AS drm INNER JOIN users_details ON (FIND_IN_SET(drm.arn_pincode, users_details.serviceable_pincode)) INNER JOIN users ON (users_details.user_id = users.id) WHERE drm.direct_relationship_user_id IS NULL AND users.is_drm_user = 1 AND users_details.status = 1 AND users_details.is_Old = 0 AND users_details.is_deleted = 0 AND users_details.skip_in_arn_mapping = 0 '. $where_conditions_field .' GROUP BY drm.ARN HAVING no_of_arn = 1) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) SET b.direct_relationship_user_id = a.user_id, b.created_at = b.created_at, b.record_last_available_in_amfi = b.record_last_available_in_amfi WHERE b.direct_relationship_user_id IS NULL '. $subquery_where_conditions_field .';', array_merge($where_conditions_data, $subquery_where_conditions_data));

            // finding number of affected records from above query because DB::update do not give the updated records count
            $affected_rows = DB::table('drm_distributor_master')
                                ->where('updated_at', '>=', $current_datetime)
                                ->selectRaw('COUNT(1) AS total')
                                ->first();
            if(isset($affected_rows) && isset($affected_rows->total)){
                $affected_rows = $affected_rows->total;
            }
            else{
                $affected_rows = 0;
            }
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tUpdating list of ARN(s) whose pincode is matching with exactly one BDM serving in that area, affected rows: ". $affected_rows;

			/*

            $current_datetime = date('Y-m-d H:i:s');
            // # Query to update list of ARN(s) whose having total ind AUM > 25, pincode is matching with exactly one BDM serving in that area whose cadre is 3
            DB::update('UPDATE (SELECT a.*, users_details.user_id, users_details.cadre_of_employee, COUNT(users_details.user_id) AS no_of_cadre_users FROM (SELECT drm.ARN, drm.arn_pincode, IFNULL(drm.total_ind_aum, 0) AS total_ind_aum, COUNT(users_details.user_id) AS no_of_users FROM drm_distributor_master AS drm INNER JOIN users_details ON (FIND_IN_SET(drm.arn_pincode, users_details.serviceable_pincode)) INNER JOIN users ON (users_details.user_id = users.id) WHERE drm.direct_relationship_user_id IS NULL AND users.is_drm_user = 1 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 AND drm.total_ind_aum > 25 '. $where_conditions_field .' GROUP BY drm.ARN HAVING no_of_users > 1 ORDER BY no_of_users DESC) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) LEFT JOIN users_details ON (FIND_IN_SET(b.arn_pincode, users_details.serviceable_pincode)) WHERE a.total_ind_aum > 25 AND users_details.cadre_of_employee = 3 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 '. $subquery_where_conditions_field .' GROUP BY b.ARN, users_details.cadre_of_employee HAVING no_of_cadre_users = 1) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) SET b.direct_relationship_user_id = a.user_id, b.created_at = b.created_at, b.updated_at = b.updated_at, b.record_last_available_in_amfi = b.record_last_available_in_amfi WHERE b.direct_relationship_user_id IS NULL AND a.total_ind_aum > 25 '. $subquery_where_conditions_field_1 .';', array_merge($where_conditions_data, $subquery_where_conditions_data, $subquery_where_conditions_data_1));

            // finding number of affected records from above query because DB::update do not give the updated records count
            $affected_rows = DB::table('drm_distributor_master')
                                ->where('updated_at', '>=', $current_datetime)
                                ->selectRaw('COUNT(1) AS total')
                                ->first();
            if(isset($affected_rows) && isset($affected_rows->total)){
                $affected_rows = $affected_rows->total;
            }
            else{
                $affected_rows = 0;
            }
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tUpdating list of ARN(s) whose having total ind AUM > 25, pincode is matching with exactly one BDM serving in that area whose cadre is 3, affected rows: ". $affected_rows;

            $current_datetime = date('Y-m-d H:i:s');
            // # Query to update list of ARN(s) whose having total ind AUM > 5 & AUM <=25, pincode is matching with exactly one BDM serving in that area whose cadre is 2
            DB::update('UPDATE (SELECT a.*, users_details.user_id, users_details.cadre_of_employee, COUNT(users_details.user_id) AS no_of_cadre_users FROM (SELECT drm.ARN, drm.arn_pincode, IFNULL(drm.total_ind_aum, 0) AS total_ind_aum, COUNT(users_details.user_id) AS no_of_users FROM drm_distributor_master AS drm INNER JOIN users_details ON (FIND_IN_SET(drm.arn_pincode, users_details.serviceable_pincode)) INNER JOIN users ON (users_details.user_id = users.id) WHERE drm.direct_relationship_user_id IS NULL AND users.is_drm_user = 1 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 AND drm.total_ind_aum > 5 AND drm.total_ind_aum <= 25 '. $where_conditions_field .' GROUP BY drm.ARN HAVING no_of_users > 1 ORDER BY no_of_users DESC) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) LEFT JOIN users_details ON (FIND_IN_SET(b.arn_pincode, users_details.serviceable_pincode)) WHERE a.total_ind_aum > 5 AND a.total_ind_aum <= 25 AND users_details.cadre_of_employee = 2 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 '. $subquery_where_conditions_field .' GROUP BY b.ARN, users_details.cadre_of_employee HAVING no_of_cadre_users = 1) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) SET b.direct_relationship_user_id = a.user_id, b.created_at = b.created_at, b.updated_at = b.updated_at, b.record_last_available_in_amfi = b.record_last_available_in_amfi WHERE b.direct_relationship_user_id IS NULL AND a.total_ind_aum > 5 AND a.total_ind_aum <= 25 '. $subquery_where_conditions_field_1 .';', array_merge($where_conditions_data, $subquery_where_conditions_data, $subquery_where_conditions_data_1));

            // finding number of affected records from above query because DB::update do not give the updated records count
            $affected_rows = DB::table('drm_distributor_master')
                                ->where('updated_at', '>=', $current_datetime)
                                ->selectRaw('COUNT(1) AS total')
                                ->first();
            if(isset($affected_rows) && isset($affected_rows->total)){
                $affected_rows = $affected_rows->total;
            }
            else{
                $affected_rows = 0;
            }
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tUpdating list of ARN(s) whose having total ind AUM > 5 & AUM <=25, pincode is matching with exactly one BDM serving in that area whose cadre is 2, affected rows: ". $affected_rows;

            $current_datetime = date('Y-m-d H:i:s');
            // # Query to update list of ARN(s) whose having total ind AUM >= 0.5 & AUM <=5, pincode is matching with exactly one BDM serving in that area whose cadre is 1
            DB::update('UPDATE (SELECT a.*, users_details.user_id, users_details.cadre_of_employee, COUNT(users_details.user_id) AS no_of_cadre_users FROM (SELECT drm.ARN, drm.arn_pincode, IFNULL(drm.total_ind_aum, 0) AS total_ind_aum, COUNT(users_details.user_id) AS no_of_users FROM drm_distributor_master AS drm INNER JOIN users_details ON (FIND_IN_SET(drm.arn_pincode, users_details.serviceable_pincode)) INNER JOIN users ON (users_details.user_id = users.id) WHERE drm.direct_relationship_user_id IS NULL AND users.is_drm_user = 1 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 AND drm.total_ind_aum >= 0.5 AND drm.total_ind_aum <= 5 '. $where_conditions_field .' GROUP BY drm.ARN HAVING no_of_users > 1 ORDER BY no_of_users DESC) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) LEFT JOIN users_details ON (FIND_IN_SET(b.arn_pincode, users_details.serviceable_pincode)) WHERE a.total_ind_aum >= 0.5 AND a.total_ind_aum <= 5 AND users_details.cadre_of_employee = 1 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 '. $subquery_where_conditions_field .' GROUP BY b.ARN, users_details.cadre_of_employee HAVING no_of_cadre_users = 1) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) SET b.direct_relationship_user_id = a.user_id, b.created_at = b.created_at, b.updated_at = b.updated_at, b.record_last_available_in_amfi = b.record_last_available_in_amfi WHERE b.direct_relationship_user_id IS NULL AND a.total_ind_aum >= 0.5 AND a.total_ind_aum <= 5 '. $subquery_where_conditions_field_1 .';', array_merge($where_conditions_data, $subquery_where_conditions_data, $subquery_where_conditions_data_1));

            // finding number of affected records from above query because DB::update do not give the updated records count
            $affected_rows = DB::table('drm_distributor_master')
                                ->where('updated_at', '>=', $current_datetime)
                                ->selectRaw('COUNT(1) AS total')
                                ->first();
            if(isset($affected_rows) && isset($affected_rows->total)){
                $affected_rows = $affected_rows->total;
            }
            else{
                $affected_rows = 0;
            }
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tUpdating list of ARN(s) whose having total ind AUM >= 0.5 & AUM <=5, pincode is matching with exactly one BDM serving in that area whose cadre is 1, affected rows: ". $affected_rows;

			*/

            // # Query to get list of DRM users for assigning them against an ARN whose direct_relationship_user_id is BLANK
            $users_data = array();
            $users_record_count = 0;
            $retrieved_data = DB::table('users')
                                ->join('users_details', 'users.id', '=', 'users_details.user_id')
                                ->leftJoin('drm_distributor_master AS drm', 'users.id', '=', 'drm.direct_relationship_user_id')
                                ->select('users.id', 'users.name', 'users.email', DB::raw('IFNULL(users_details.cadre_of_employee, 0) AS cadre_of_employee'), 'users_details.serviceable_pincode', DB::raw('COUNT(DISTINCT drm.ARN) AS assigned_arn_count'))
                                ->where(array(array('users.is_drm_user', '=', 1),
												array('users_details.status', '=', 1),
												array('users_details.is_old', '=', 0),
                                              array('users_details.skip_in_arn_mapping', '=', 0)
                                              )
                                    )
                                ->groupBy('users.id')
                                ->orderBy('cadre_of_employee', 'desc')
                                ->orderBy('assigned_arn_count', 'asc')
                                ->get();

            if(!$retrieved_data->isEmpty() && count($retrieved_data) > 0){
                foreach($retrieved_data as $record){
                    if(!isset($users_data[$record->cadre_of_employee])){
                        $users_data[$record->cadre_of_employee] = array();
                    }
                    // removing extra spaces between the multiple serviceable_pincode value
                    $record->serviceable_pincode = preg_replace('/\s+/', '', $record->serviceable_pincode);
                    // converting a value to an integer so that it will be helpful while sorting the data
                    $record->cadre_of_employee = intval($record->cadre_of_employee);
                    $record->assigned_arn_count = intval($record->assigned_arn_count);
                    $users_data[$record->cadre_of_employee]['id_'. $record->id] = (array) $record;
                    $users_record_count += 1;
                }
                unset($record);
            }

            // sorting users data based on Assigned ARN Count values in ASCENDING order
            if(is_array($users_data) && count($users_data) > 0){
                foreach($users_data as &$record){
                    array_multisort(array_column($record, 'assigned_arn_count'), SORT_ASC, $record);
                }
                unset($record);
            }
            // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tUsers data: ". print_r($users_data, true);
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tUsers count: ". $users_record_count;

			/*

            // # Query to get list of ARN(s) whose having total ind AUM > 25 and having more than one BDM with cadre equals to 3
            $retrieved_data = DB::select('SELECT a.*, users_details.cadre_of_employee, COUNT(users_details.user_id) AS no_of_cadre_users FROM (SELECT drm.ARN, drm.arn_pincode, IFNULL(drm.total_ind_aum, 0) AS total_ind_aum, COUNT(users_details.user_id) AS no_of_users FROM drm_distributor_master AS drm INNER JOIN users_details ON (FIND_IN_SET(drm.arn_pincode, users_details.serviceable_pincode)) INNER JOIN users ON (users_details.user_id = users.id) WHERE drm.direct_relationship_user_id IS NULL AND users.is_drm_user = 1 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 AND drm.total_ind_aum > 25 '. $where_conditions_field .' GROUP BY drm.ARN HAVING no_of_users > 1 ORDER BY no_of_users DESC) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) LEFT JOIN users_details ON (FIND_IN_SET(b.arn_pincode, users_details.serviceable_pincode)) WHERE a.total_ind_aum > 25 AND users_details.cadre_of_employee = 3 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 '. $subquery_where_conditions_field .' GROUP BY b.ARN, users_details.cadre_of_employee HAVING no_of_cadre_users > 1 ORDER BY b.ARN ASC, users_details.cadre_of_employee DESC, no_of_cadre_users DESC;', array_merge($where_conditions_data, $subquery_where_conditions_data));
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tRetrieving of ARN(s) whose having total ind AUM > 25 and having more than one BDM with cadre equals to 3, retrieved rows: ". count($retrieved_data);
            if(is_array($retrieved_data) && count($retrieved_data) > 0 && count($users_data) > 0){
                // checking users data for cadre 3 available or not from the system
                $users_data_cadre_keys = 3;
                // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t1. users_data_cadre_keys: ". $users_data_cadre_keys;

                $affected_rows = 0;
                foreach($retrieved_data as $record){
                    // retrieving the list of users available for that cadre
                    $assign_from_users = $users_data[$users_data_cadre_keys];
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t1. assign_from_users: ". print_r($assign_from_users, true);

                    // keeping only those user(s)/bdm(s) which are having servicing pincode as that of looping ARN entry
                    foreach($assign_from_users as $user_record_key => $user_record){
                        if(!isset($user_record['serviceable_pincode']) || empty($user_record['serviceable_pincode'])){
                            // if serviceable_pincode is data not present for the user then removing that user
                            unset($assign_from_users[$user_record_key]);
                        }
                        elseif(!empty($user_record['serviceable_pincode']) && in_array($record->arn_pincode, explode(',', $user_record['serviceable_pincode'])) === FALSE){
                            // if serviceable_pincode is present but that user do not serve the pincode as that of ARN then removing that user
                            unset($assign_from_users[$user_record_key]);
                        }
                    }
                    unset($user_record_key, $user_record);

                    // sorting the users data based on key assigned_arn_count in ASCENDING order
                    array_multisort(array_column($assign_from_users, 'assigned_arn_count'), SORT_ASC, $assign_from_users);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t2. assign_from_users: ". print_r($assign_from_users, true);

                    // gets first element from an array
                    $found_user_assignee = current($assign_from_users);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tfound_user_assignee: ". print_r($found_user_assignee, true);

                    if(isset($found_user_assignee) && isset($found_user_assignee['id'])){
                        // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tupdating record data and conditions=". print_r(array('where' => array(array('ARN', '=', $record->ARN), array('arn_pincode', '=', $record->arn_pincode)), 'data' => array('direct_relationship_user_id' => $found_user_assignee['id']), 'arn_number' => $record->ARN, 'flag_show_all_arn_data' => true, 'logged_in_user_id' => $logged_in_user_id??0), true);
                        $query_output = self::UpdateSamcoPByArn(
                                            array('where' => array(array('ARN', '=', $record->ARN),
                                                                   array('arn_pincode', '=', $record->arn_pincode)
                                                                ),
                                                  'data' => array('direct_relationship_user_id' => $found_user_assignee['id']),
                                                  'arn_number' => $record->ARN,
                                                  'flag_show_all_arn_data' => true,
                                                  'logged_in_user_id' => $logged_in_user_id??0
                                                )
                                            );
                        if($query_output){
                            // updating already prepared users array with new value of assigned_arn_count
                            $users_data[$users_data_cadre_keys]['id_'. $found_user_assignee['id']]['assigned_arn_count'] += 1;
                            // updating count of records affected during the process
                            $affected_rows += 1;
                        }
                        unset($query_output);
                    }
                    unset($found_user_assignee, $assign_from_users);
                }
                unset($record, $users_data_cadre_keys);
                $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tList of ARN(s) whose having total ind AUM > 25 and having more than one BDM with cadre equals to 3, affected rows: ". $affected_rows;
            }

            // # Query to get list of ARN(s) whose having total ind AUM > 25 but not having any user(s) with cadre equals to 3
            $retrieved_data = DB::select('SELECT a.*, users_details.cadre_of_employee, COUNT(users_details.user_id) AS no_of_cadre_users FROM (SELECT drm.ARN, drm.arn_pincode, IFNULL(drm.total_ind_aum, 0) AS total_ind_aum, COUNT(users_details.user_id) AS no_of_users FROM drm_distributor_master AS drm INNER JOIN users_details ON (FIND_IN_SET(drm.arn_pincode, users_details.serviceable_pincode)) INNER JOIN users ON (users_details.user_id = users.id) WHERE drm.direct_relationship_user_id IS NULL AND users.is_drm_user = 1 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 AND drm.total_ind_aum > 25 '. $where_conditions_field .' GROUP BY drm.ARN HAVING no_of_users > 1 ORDER BY no_of_users DESC) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) LEFT JOIN users_details ON (FIND_IN_SET(b.arn_pincode, users_details.serviceable_pincode)) WHERE a.total_ind_aum > 25 AND users_details.cadre_of_employee != 3 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 '. $subquery_where_conditions_field .' GROUP BY b.ARN, users_details.cadre_of_employee ORDER BY b.ARN ASC, users_details.cadre_of_employee DESC, no_of_cadre_users DESC;', array_merge($where_conditions_data, $subquery_where_conditions_data));
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tRetrieving list of ARN(s) whose having total ind AUM > 25 but not having any user(s) with cadre equals to 3, retrieved rows: ". count($retrieved_data);
            if(is_array($retrieved_data) && count($retrieved_data) > 0 && count($users_data) > 0){
                $affected_rows = 0;
                foreach($retrieved_data as $record){
                    $new_users_data = $users_data;
                    foreach($new_users_data as $cadre_key => $cadre_wise_users_data){
                        foreach($cadre_wise_users_data as $user_record_key => $user_record){
                            if(!isset($user_record['serviceable_pincode']) || empty($user_record['serviceable_pincode'])){
                                // if serviceable_pincode is data not present for the user then removing that user
                                unset($new_users_data[$cadre_key][$user_record_key]);
                            }
                            elseif(!empty($user_record['serviceable_pincode']) && in_array($record->arn_pincode, explode(',', $user_record['serviceable_pincode'])) === FALSE){
                                // if serviceable_pincode is present but that user do not serve the pincode as that of ARN then removing that user
                                unset($new_users_data[$cadre_key][$user_record_key]);
                            }
                        }
                        unset($user_record_key, $user_record);
                    }
                    unset($cadre_key, $cadre_wise_users_data);

                    // checking whether highest cadre data available from the system
                    $users_data_cadre_keys = array_keys($new_users_data);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t1. users_data_cadre_keys: ". print_r($users_data_cadre_keys, true);

                    // sorting the array keys(field cadre value is the key) in DESCENDING order
                    rsort($users_data_cadre_keys);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t2. users_data_cadre_keys: ". print_r($users_data_cadre_keys, true);

                    // retrieving the first value from an array $users_data_cadre_keys
                    $users_data_cadre_keys = current($users_data_cadre_keys);
                    // retrieving the list of users available for that cadre
                    $assign_from_users = $new_users_data[$users_data_cadre_keys];
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t1. assign_from_users: ". print_r($assign_from_users, true);

                    // sorting the users data based on key assigned_arn_count in ASCENDING order
                    array_multisort(array_column($assign_from_users, 'assigned_arn_count'), SORT_ASC, $assign_from_users);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t2. assign_from_users: ". print_r($assign_from_users, true);

                    // gets first element from an array
                    $found_user_assignee = current($assign_from_users);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tfound_user_assignee: ". print_r($found_user_assignee, true);

                    if(isset($found_user_assignee) && isset($found_user_assignee['id'])){
                        // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tupdating record data and conditions=". print_r(array('where' => array(array('ARN', '=', $record->ARN), array('arn_pincode', '=', $record->arn_pincode)), 'data' => array('direct_relationship_user_id' => $found_user_assignee['id']), 'arn_number' => $record->ARN, 'flag_show_all_arn_data' => true, 'logged_in_user_id' => $logged_in_user_id??0), true);
                        $query_output = self::UpdateSamcoPByArn(
                                            array('where' => array(array('ARN', '=', $record->ARN),
                                                                   array('arn_pincode', '=', $record->arn_pincode)
                                                                ),
                                                  'data' => array('direct_relationship_user_id' => $found_user_assignee['id']),
                                                  'arn_number' => $record->ARN,
                                                  'flag_show_all_arn_data' => true,
                                                  'logged_in_user_id' => $logged_in_user_id??0
                                                )
                                            );
                        if($query_output){
                            // updating already prepared users array with new value of assigned_arn_count
                            $users_data[$users_data_cadre_keys]['id_'. $found_user_assignee['id']]['assigned_arn_count'] += 1;
                            // updating count of records affected during the process
                            $affected_rows += 1;
                        }
                        unset($query_output);
                    }
                    unset($found_user_assignee, $assign_from_users, $new_users_data);
                }
                unset($record, $users_data_cadre_keys);
                $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tList of ARN(s) whose having total ind AUM > 25 but not having any user(s) with cadre equals to 3, affected rows: ". $affected_rows;
            }

            // # Query to get list of ARN(s) whose having total ind AUM > 5 & AUM <= 25 and having more than one BDM with cadre equals to 2
            $retrieved_data = DB::select('SELECT a.*, users_details.cadre_of_employee, COUNT(users_details.user_id) AS no_of_cadre_users FROM (SELECT drm.ARN, drm.arn_pincode, IFNULL(drm.total_ind_aum, 0) AS total_ind_aum, COUNT(users_details.user_id) AS no_of_users FROM drm_distributor_master AS drm INNER JOIN users_details ON (FIND_IN_SET(drm.arn_pincode, users_details.serviceable_pincode)) INNER JOIN users ON (users_details.user_id = users.id) WHERE drm.direct_relationship_user_id IS NULL AND users.is_drm_user = 1 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 AND drm.total_ind_aum > 5 AND drm.total_ind_aum <= 25 '. $where_conditions_field .' GROUP BY drm.ARN HAVING no_of_users > 1 ORDER BY no_of_users DESC) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) LEFT JOIN users_details ON (FIND_IN_SET(b.arn_pincode, users_details.serviceable_pincode)) WHERE a.total_ind_aum > 5 AND a.total_ind_aum <= 25 AND users_details.cadre_of_employee = 2 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 '. $subquery_where_conditions_field .' GROUP BY b.ARN, users_details.cadre_of_employee HAVING no_of_cadre_users > 1 ORDER BY b.ARN ASC, users_details.cadre_of_employee DESC, no_of_cadre_users DESC;', array_merge($where_conditions_data, $subquery_where_conditions_data));
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tRetrieving of ARN(s) whose having total ind AUM > 5 & AUM <= 25 and having more than one BDM with cadre equals to 2, retrieved rows: ". count($retrieved_data);
            if(is_array($retrieved_data) && count($retrieved_data) > 0 && count($users_data) > 0){
                // checking users data for cadre 3 available or not from the system
                $users_data_cadre_keys = 2;
                // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t1. users_data_cadre_keys: ". print_r($users_data_cadre_keys, true);

                $affected_rows = 0;
                foreach($retrieved_data as $record){
                    // retrieving the list of users available for that cadre
                    $assign_from_users = $users_data[$users_data_cadre_keys];
                    // $output_arr['display_messages'][] = "1. assign_from_users: ". print_r($assign_from_users, true);

                    // keeping only those user(s)/bdm(s) which are having servicing pincode as that of looping ARN entry
                    foreach($assign_from_users as $user_record_key => $user_record){
                        if(!isset($user_record['serviceable_pincode']) || empty($user_record['serviceable_pincode'])){
                            // if serviceable_pincode is data not present for the user then removing that user
                            unset($assign_from_users[$user_record_key]);
                        }
                        elseif(!empty($user_record['serviceable_pincode']) && in_array($record->arn_pincode, explode(',', $user_record['serviceable_pincode'])) === FALSE){
                            // if serviceable_pincode is present but that user do not serve the pincode as that of ARN then removing that user
                            unset($assign_from_users[$user_record_key]);
                        }
                    }
                    unset($user_record_key, $user_record);

                    // sorting the users data based on key assigned_arn_count in ASCENDING order
                    array_multisort(array_column($assign_from_users, 'assigned_arn_count'), SORT_ASC, $assign_from_users);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t2. assign_from_users: ". print_r($assign_from_users, true);

                    // gets first element from an array
                    $found_user_assignee = current($assign_from_users);
                    // $output_arr['display_messages'][] = "found_user_assignee: ". print_r($found_user_assignee, true);

                    if(isset($found_user_assignee) && isset($found_user_assignee['id'])){
                        // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tupdating record data and conditions=". print_r(array('where' => array(array('ARN', '=', $record->ARN), array('arn_pincode', '=', $record->arn_pincode)), 'data' => array('direct_relationship_user_id' => $found_user_assignee['id']), 'arn_number' => $record->ARN, 'flag_show_all_arn_data' => true, 'logged_in_user_id' => $logged_in_user_id??0), true);
                        $query_output = self::UpdateSamcoPByArn(
                                            array('where' => array(array('ARN', '=', $record->ARN),
                                                                   array('arn_pincode', '=', $record->arn_pincode)
                                                                ),
                                                  'data' => array('direct_relationship_user_id' => $found_user_assignee['id']),
                                                  'arn_number' => $record->ARN,
                                                  'flag_show_all_arn_data' => true,
                                                  'logged_in_user_id' => $logged_in_user_id??0
                                                )
                                            );
                        if($query_output){
                            // updating already prepared users array with new value of assigned_arn_count
                            $users_data[$users_data_cadre_keys]['id_'. $found_user_assignee['id']]['assigned_arn_count'] += 1;
                            // updating count of records affected during the process
                            $affected_rows += 1;
                        }
                        unset($query_output);
                    }
                    unset($found_user_assignee, $assign_from_users);
                }
                unset($record, $users_data_cadre_keys);
                $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tList of ARN(s) whose having total ind AUM > 5 & AUM <= 25 and having more than one BDM with cadre equals to 2, affected rows: ". $affected_rows;
            }

            // # Query to get list of ARN(s) whose having total ind AUM > 5 & AUM <=25 but not having any user(s) with cadre equals to 2
            $retrieved_data = DB::select('SELECT a.*, users_details.cadre_of_employee, COUNT(users_details.user_id) AS no_of_cadre_users FROM (SELECT drm.ARN, drm.arn_pincode, IFNULL(drm.total_ind_aum, 0) AS total_ind_aum, COUNT(users_details.user_id) AS no_of_users FROM drm_distributor_master AS drm INNER JOIN users_details ON (FIND_IN_SET(drm.arn_pincode, users_details.serviceable_pincode)) INNER JOIN users ON (users_details.user_id = users.id) WHERE drm.direct_relationship_user_id IS NULL AND users.is_drm_user = 1 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 AND drm.total_ind_aum > 5 AND drm.total_ind_aum <= 25 '. $where_conditions_field .' GROUP BY drm.ARN HAVING no_of_users > 1 ORDER BY no_of_users DESC) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) LEFT JOIN users_details ON (FIND_IN_SET(b.arn_pincode, users_details.serviceable_pincode)) WHERE a.total_ind_aum > 5 AND a.total_ind_aum <= 25 AND users_details.cadre_of_employee != 2 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 '. $subquery_where_conditions_field .' GROUP BY b.ARN, users_details.cadre_of_employee ORDER BY b.ARN ASC, users_details.cadre_of_employee DESC, no_of_cadre_users DESC;', array_merge($where_conditions_data, $subquery_where_conditions_data));
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tRetrieving list of ARN(s) whose having total ind AUM > 5 & AUM <=25 but not having any user(s) with cadre equals to 2, retrieved rows: ". count($retrieved_data);
            if(is_array($retrieved_data) && count($retrieved_data) > 0 && count($users_data) > 0){
                // checking users data for cadre 1 available or not from the system
                $users_data_cadre_keys = 1;
                // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t1. users_data_cadre_keys: ". print_r($users_data_cadre_keys, true);

                $affected_rows = 0;
                foreach($retrieved_data as $record){
                    // retrieving the list of users available for that cadre
                    $assign_from_users = $users_data[$users_data_cadre_keys];
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t1. assign_from_users: ". print_r($assign_from_users, true);

                    // keeping only those user(s)/bdm(s) which are having servicing pincode as that of looping ARN entry
                    foreach($assign_from_users as $user_record_key => $user_record){
                        if(!isset($user_record['serviceable_pincode']) || empty($user_record['serviceable_pincode'])){
                            // if serviceable_pincode is data not present for the user then removing that user
                            unset($assign_from_users[$user_record_key]);
                        }
                        elseif(!empty($user_record['serviceable_pincode']) && in_array($record->arn_pincode, explode(',', $user_record['serviceable_pincode'])) === FALSE){
                            // if serviceable_pincode is present but that user do not serve the pincode as that of ARN then removing that user
                            unset($assign_from_users[$user_record_key]);
                        }
                    }
                    unset($user_record_key, $user_record);

                    // sorting the users data based on key assigned_arn_count in ASCENDING order
                    array_multisort(array_column($assign_from_users, 'assigned_arn_count'), SORT_ASC, $assign_from_users);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t2. assign_from_users: ". print_r($assign_from_users, true);

                    // gets first element from an array
                    $found_user_assignee = current($assign_from_users);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tfound_user_assignee: ". print_r($found_user_assignee, true);

                    if(isset($found_user_assignee) && isset($found_user_assignee['id'])){
                        // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tupdating record data and conditions=". print_r(array('where' => array(array('ARN', '=', $record->ARN), array('arn_pincode', '=', $record->arn_pincode)), 'data' => array('direct_relationship_user_id' => $found_user_assignee['id']), 'arn_number' => $record->ARN, 'flag_show_all_arn_data' => true, 'logged_in_user_id' => $logged_in_user_id??0), true);
                        $query_output = self::UpdateSamcoPByArn(
                                            array('where' => array(array('ARN', '=', $record->ARN),
                                                                   array('arn_pincode', '=', $record->arn_pincode)
                                                                ),
                                                  'data' => array('direct_relationship_user_id' => $found_user_assignee['id']),
                                                  'arn_number' => $record->ARN,
                                                  'flag_show_all_arn_data' => true,
                                                  'logged_in_user_id' => $logged_in_user_id??0
                                                )
                                            );
                        if($query_output){
                            // updating already prepared users array with new value of assigned_arn_count
                            $users_data[$users_data_cadre_keys]['id_'. $found_user_assignee['id']]['assigned_arn_count'] += 1;
                            // updating count of records affected during the process
                            $affected_rows += 1;
                        }
                        unset($query_output);
                    }
                    unset($found_user_assignee, $assign_from_users);
                }
                unset($record, $users_data_cadre_keys);
                $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tList of ARN(s) whose having total ind AUM > 5 & AUM <=25 but not having any user(s) with cadre equals to 2, affected rows: ". $affected_rows;
            }

            // # Query to get list of ARN(s) whose having total ind AUM >= 0.5 & AUM <=5 and having more than one BDM with cadre equals to 1
            $retrieved_data = DB::select('SELECT a.*, users_details.cadre_of_employee, COUNT(users_details.user_id) AS no_of_cadre_users FROM (SELECT drm.ARN, drm.arn_pincode, IFNULL(drm.total_ind_aum, 0) AS total_ind_aum, COUNT(users_details.user_id) AS no_of_users FROM drm_distributor_master AS drm INNER JOIN users_details ON (FIND_IN_SET(drm.arn_pincode, users_details.serviceable_pincode)) INNER JOIN users ON (users_details.user_id = users.id) WHERE drm.direct_relationship_user_id IS NULL AND users.is_drm_user = 1 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 AND drm.total_ind_aum >= 0.5 AND drm.total_ind_aum <= 5 '. $where_conditions_field .' GROUP BY drm.ARN HAVING no_of_users > 1 ORDER BY no_of_users DESC) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) LEFT JOIN users_details ON (FIND_IN_SET(b.arn_pincode, users_details.serviceable_pincode)) WHERE a.total_ind_aum >= 0.5 AND a.total_ind_aum <= 5 AND users_details.cadre_of_employee = 1 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0  '. $subquery_where_conditions_field .'GROUP BY b.ARN, users_details.cadre_of_employee HAVING no_of_cadre_users > 1 ORDER BY b.ARN ASC, users_details.cadre_of_employee DESC, no_of_cadre_users DESC;', array_merge($where_conditions_data, $subquery_where_conditions_data));
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tRetrieving list of ARN(s) whose having total ind AUM >= 0.5 & AUM <=5 and having more than one BDM with cadre equals to 1, retrieved rows: ". count($retrieved_data);
            if(is_array($retrieved_data) && count($retrieved_data) > 0 && count($users_data) > 0){
                // checking users data for cadre 1 available or not from the system
                $users_data_cadre_keys = 1;
                // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t1. users_data_cadre_keys: ". print_r($users_data_cadre_keys, true);

                $affected_rows = 0;
                foreach($retrieved_data as $record){
                    // retrieving the list of users available for that cadre
                    $assign_from_users = $users_data[$users_data_cadre_keys];
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t1. assign_from_users: ". print_r($assign_from_users, true);

                    // keeping only those user(s)/bdm(s) which are having servicing pincode as that of looping ARN entry
                    foreach($assign_from_users as $user_record_key => $user_record){
                        if(!isset($user_record['serviceable_pincode']) || empty($user_record['serviceable_pincode'])){
                            // if serviceable_pincode is data not present for the user then removing that user
                            unset($assign_from_users[$user_record_key]);
                        }
                        elseif(!empty($user_record['serviceable_pincode']) && in_array($record->arn_pincode, explode(',', $user_record['serviceable_pincode'])) === FALSE){
                            // if serviceable_pincode is present but that user do not serve the pincode as that of ARN then removing that user
                            unset($assign_from_users[$user_record_key]);
                        }
                    }
                    unset($user_record_key, $user_record);

                    // sorting the users data based on key assigned_arn_count in ASCENDING order
                    array_multisort(array_column($assign_from_users, 'assigned_arn_count'), SORT_ASC, $assign_from_users);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t2. assign_from_users: ". print_r($assign_from_users, true);

                    // gets first element from an array
                    $found_user_assignee = current($assign_from_users);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tfound_user_assignee: ". print_r($found_user_assignee, true);

                    if(isset($found_user_assignee) && isset($found_user_assignee['id'])){
                        // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tupdating record data and conditions=". print_r(array('where' => array(array('ARN', '=', $record->ARN), array('arn_pincode', '=', $record->arn_pincode)), 'data' => array('direct_relationship_user_id' => $found_user_assignee['id']), 'arn_number' => $record->ARN, 'flag_show_all_arn_data' => true, 'logged_in_user_id' => $logged_in_user_id??0), true);
                        $query_output = self::UpdateSamcoPByArn(
                                            array('where' => array(array('ARN', '=', $record->ARN),
                                                                   array('arn_pincode', '=', $record->arn_pincode)
                                                                ),
                                                  'data' => array('direct_relationship_user_id' => $found_user_assignee['id']),
                                                  'arn_number' => $record->ARN,
                                                  'flag_show_all_arn_data' => true,
                                                  'logged_in_user_id' => $logged_in_user_id??0
                                                )
                                            );
                        if($query_output){
                            // updating already prepared users array with new value of assigned_arn_count
                            $users_data[$users_data_cadre_keys]['id_'. $found_user_assignee['id']]['assigned_arn_count'] += 1;
                            // updating count of records affected during the process
                            $affected_rows += 1;
                        }
                        unset($query_output);
                    }
                    unset($found_user_assignee, $assign_from_users);
                }
                unset($record, $users_data_cadre_keys);
                $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tList list of ARN(s) whose having total ind AUM >= 0.5 & AUM <=5 and having more than one BDM with cadre equals to 1, affected rows: ". $affected_rows;
            }

            // merging users data which is segregated based on cadre into single array
            $new_users_data = array();
            if(is_array($users_data) && count($users_data) > 0){
                array_walk($users_data, function($_value, $_key, $_user_data){
                    $_user_data[0] = array_merge($_user_data[0], $_value);
                }, [&$new_users_data]);
            }
            // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tMerged users data into single array variable new_users_data: ". print_r($new_users_data, true);

            // # Query to get list of ARN(s) whose having total ind AUM >= 0.5 & AUM <=5 but not having any user(s) with cadre equals to 1
            $retrieved_data = DB::select('SELECT a.*, users_details.cadre_of_employee, COUNT(users_details.user_id) AS no_of_cadre_users FROM (SELECT drm.ARN, drm.arn_pincode, IFNULL(drm.total_ind_aum, 0) AS total_ind_aum, COUNT(users_details.user_id) AS no_of_users FROM drm_distributor_master AS drm INNER JOIN users_details ON (FIND_IN_SET(drm.arn_pincode, users_details.serviceable_pincode)) INNER JOIN users ON (users_details.user_id = users.id) WHERE drm.direct_relationship_user_id IS NULL AND users.is_drm_user = 1 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 AND drm.total_ind_aum >= 0.5 AND drm.total_ind_aum <= 5 '. $where_conditions_field .' GROUP BY drm.ARN HAVING no_of_users > 1 ORDER BY no_of_users DESC) AS a INNER JOIN drm_distributor_master AS b ON (a.ARN = b.ARN AND a.arn_pincode = b.arn_pincode) LEFT JOIN users_details ON (FIND_IN_SET(b.arn_pincode, users_details.serviceable_pincode)) WHERE a.total_ind_aum >= 0.5 AND a.total_ind_aum <= 5 AND users_details.cadre_of_employee != 1 AND users_details.status = 1 AND users_details.skip_in_arn_mapping = 0 '. $subquery_where_conditions_field .' GROUP BY b.ARN, users_details.cadre_of_employee ORDER BY b.ARN ASC, users_details.cadre_of_employee DESC, no_of_cadre_users DESC;', array_merge($where_conditions_data, $subquery_where_conditions_data));
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tRetrieving list of ARN(s) whose having total ind AUM >= 0.5 & AUM <=5 but not having any user(s) with cadre equals to 1, retrieved rows: ". count($retrieved_data);
            if(is_array($retrieved_data) && count($retrieved_data) > 0 && count($new_users_data) > 0){
                $affected_rows = 0;
                foreach($retrieved_data as $record){
                    $assign_from_users = $new_users_data;
                    // keeping only those user(s)/bdm(s) which are having servicing pincode as that of looping ARN entry
                    foreach($assign_from_users as $user_record_key => $user_record){
                        if(!isset($user_record['serviceable_pincode']) || empty($user_record['serviceable_pincode'])){
                            // if serviceable_pincode is data not present for the user then removing that user
                            unset($assign_from_users[$user_record_key]);
                        }
                        elseif(!empty($user_record['serviceable_pincode']) && in_array($record->arn_pincode, explode(',', $user_record['serviceable_pincode'])) === FALSE){
                            // if serviceable_pincode is present but that user do not serve the pincode as that of ARN then removing that user
                            unset($assign_from_users[$user_record_key]);
                        }
                    }
                    unset($user_record_key, $user_record);

                    // sorting the users data based on key assigned_arn_count in ASCENDING order
                    array_multisort(array_column($assign_from_users, 'assigned_arn_count'), SORT_ASC, $assign_from_users);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t2. assign_from_users: ". print_r($assign_from_users, true);

                    // gets first element from an array
                    $found_user_assignee = current($assign_from_users);
                    // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tfound_user_assignee: ". print_r($found_user_assignee, true);

                    if(isset($found_user_assignee) && isset($found_user_assignee['id'])){
                        // $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tupdating record data and conditions=". print_r(array('where' => array(array('ARN', '=', $record->ARN), array('arn_pincode', '=', $record->arn_pincode)), 'data' => array('direct_relationship_user_id' => $found_user_assignee['id']), 'arn_number' => $record->ARN, 'flag_show_all_arn_data' => true, 'logged_in_user_id' => $logged_in_user_id??0), true);
                        $query_output = self::UpdateSamcoPByArn(
                                            array('where' => array(array('ARN', '=', $record->ARN),
                                                                   array('arn_pincode', '=', $record->arn_pincode)
                                                                ),
                                                  'data' => array('direct_relationship_user_id' => $found_user_assignee['id']),
                                                  'arn_number' => $record->ARN,
                                                  'flag_show_all_arn_data' => true,
                                                  'logged_in_user_id' => $logged_in_user_id??0
                                                )
                                            );
                        if($query_output){
                            // updating already prepared users array with new value of assigned_arn_count
                            $new_users_data['id_'. $found_user_assignee['id']]['assigned_arn_count'] += 1;
                            // updating count of records affected during the process
                            $affected_rows += 1;
                        }
                        unset($query_output);
                    }
                    unset($found_user_assignee, $assign_from_users);
                }
                unset($record);
                $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tList of ARN(s) whose having total ind AUM >= 0.5 & AUM <=5 but not having any user(s) with cadre equals to 1, affected rows: ". $affected_rows;
            }
            unset($new_users_data);
			*/
        }
        catch(Exception | \Illuminate\Database\QueryException $e){
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tError occurred: ". $e->getMessage();
        }

        // retrieving the field value direct_relationship_user_id for an ARN passed
        if(isset($arn_number) && !empty($arn_number)){
            $output_arr['assigned_bdm_id'] = DB::table('drm_distributor_master')
                                                ->where('ARN', $arn_number)
                                                ->select('direct_relationship_user_id')
                                                ->first();
        }

        $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tThe command execution is successful!";
        $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tScript end datetime";
        unset($affected_rows, $retrieved_data, $users_data, $users_record_count, $current_datetime, $script_directory_path, $log_filepath);
        unset($where_conditions_field, $where_conditions_data, $subquery_where_conditions_field, $subquery_where_conditions_data);

        if($flag_log_display_messages && is_array($output_arr['display_messages']) && count($output_arr['display_messages']) > 0){
            # getting directory path of currently executing script
            if(isset($calling_it_from_browser) && $calling_it_from_browser){
                $script_directory_path = dirname($_SERVER['SCRIPT_FILENAME']);
            }
            else{
                $script_directory_path = dirname($_SERVER['SCRIPT_NAME']);
            }
            $log_filepath = $script_directory_path .'/public/storage/logs/assignusertoarn_'. date('Y-m-d') .'.txt';
            @file_put_contents($log_filepath, date("Y-m-d H:i:s") ."\t----------------------------------------------------". PHP_EOL, FILE_APPEND);
            foreach($output_arr['display_messages'] as $log_message){
                @file_put_contents($log_filepath, $log_message . PHP_EOL, FILE_APPEND);
            }
            $output_arr['log_filepath'] = $log_filepath;
            unset($log_message, $log_filepath, $script_directory_path);
        }

        $output_arr['err_flag'] = $err_flag;
        $output_arr['err_msg'] = $err_msg;
        return $output_arr;
    }
    
    public static function get_linked_arn($input_arr = array())
    {
        $arr_mobile_numbers = array();
        $arr_email_ids = array();

        if(isset($input_arr['arn_telephone_r']) && !empty(trim($input_arr['arn_telephone_r']))){
            $arr_mobile_numbers[] = trim($input_arr['arn_telephone_r']);
        }

        if(isset($input_arr['arn_telephone_o']) && !empty(trim($input_arr['arn_telephone_o']))){
            $arr_mobile_numbers[] = trim($input_arr['arn_telephone_o']);
        }

        for($cntr = 1; $cntr <= 5; $cntr++){
            if(isset($input_arr['alternate_mobile_'. $cntr]) && !empty(trim($input_arr['alternate_mobile_'. $cntr]))){
                $arr_mobile_numbers[] = trim($input_arr['alternate_mobile_'. $cntr]);
            }
        }

        if(isset($input_arr['arn_email']) && !empty(trim($input_arr['arn_email']))){
            $arr_email_ids[] = trim($input_arr['arn_email']);
        }

        for($cntr = 1; $cntr <= 5; $cntr++){
            if(isset($input_arr['alternate_email_'. $cntr]) && !empty(trim($input_arr['alternate_email_'. $cntr]))){
                $arr_email_ids[] = trim($input_arr['alternate_email_'. $cntr]);
            }
        }

        if(count($arr_mobile_numbers) > 0 || count($arr_email_ids) > 0){
            $linked_arn = DB::table('drm_distributor_master')
                            ->select('drm_distributor_master.ARN','drm_distributor_master.arn_holders_name')
                            ->where('drm_distributor_master.ARN', '=', $input_arr['ARN'])
                            ->where(function($query) use($arr_mobile_numbers,$arr_email_ids){                                
                                      if(count($arr_mobile_numbers) > 0)
                                      {
                                        $query->orWhereIn('drm_distributor_master.arn_telephone_r', $arr_mobile_numbers)
                                        ->orWhereIn('drm_distributor_master.arn_telephone_o', $arr_mobile_numbers)
                                        ->orWhereIn('drm_distributor_master.alternate_mobile_1', $arr_mobile_numbers)
                                        ->orWhereIn('drm_distributor_master.alternate_mobile_2', $arr_mobile_numbers)
                                        ->orWhereIn('drm_distributor_master.alternate_mobile_3', $arr_mobile_numbers)
                                        ->orWhereIn('drm_distributor_master.alternate_mobile_4', $arr_mobile_numbers)
                                        ->orWhereIn('drm_distributor_master.alternate_mobile_5', $arr_mobile_numbers);
                                      }
                                      if(count($arr_email_ids) > 0)
                                      {
                                        $query->orWhereIn('drm_distributor_master.arn_email', $arr_email_ids)
                                        ->orWhereIn('drm_distributor_master.alternate_email_1', $arr_email_ids)
                                        ->orWhereIn('drm_distributor_master.alternate_email_2', $arr_email_ids)
                                        ->orWhereIn('drm_distributor_master.alternate_email_3', $arr_email_ids)
                                        ->orWhereIn('drm_distributor_master.alternate_email_4', $arr_email_ids)
                                        ->orWhereIn('drm_distributor_master.alternate_email_5', $arr_email_ids);
                                      }

                            })
                            ->get();
        }
        else{
            // for condition where mobile number and email id not available, returning FALSE
            return collect(array());
        }

        return $linked_arn;
    }

    public static function getCommissionStructureByARN($arn_number){
        $where_in_conditions = array();
        $data = DB::table('rate_card_partnerwise')
                    ->leftJoin('rate_card_additional', function($join){
                        $join->on('rate_card_additional.partner_arn', '=', 'rate_card_partnerwise.partner_arn');
                        $join->on('rate_card_additional.scheme_code','=','rate_card_partnerwise.scheme_code'); 
                        $join->on('rate_card_additional.month','=','rate_card_partnerwise.month'); 
                    })
                    ->select('rate_card_partnerwise.*','rate_card_additional.special_additional_first_year_trail','rate_card_additional.special_additional_first_year_trail_for_b30')
                    ->where('rate_card_partnerwise.status','=',1)
                    ->where('rate_card_partnerwise.partner_arn','=',$arn_number);

        // $data =  DB::table('rate_card_partnerwise')
        //             ->select('rate_card_partnerwise.*')
        //             ->where('rate_card_partnerwise.partner_arn', '=', $arn_number);
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $data = $data->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }
        $data = $data->OrderBy('rate_card_partnerwise.month','ASC')->get();
        // x($data);
        unset($where_in_conditions);
        return $data;
    }

    public static function getCommissionStructureByARNToExport($input_arr = array()){
        extract($input_arr);                // Import variables into the current symbol table from an array
        $where_in_conditions = array();

        $data = DB::table('rate_card_partnerwise')
                    ->leftJoin('rate_card_additional', function($join){
                        $join->on('rate_card_additional.partner_arn', '=', 'rate_card_partnerwise.partner_arn');
                        $join->on('rate_card_additional.scheme_code','=','rate_card_partnerwise.scheme_code');  
                        $join->on('rate_card_additional.month','=','rate_card_partnerwise.month');  
                        $join->on('rate_card_additional.year','=','rate_card_partnerwise.year');  
                    })
                    ->select('rate_card_partnerwise.partner_arn','rate_card_partnerwise.scheme_name','rate_card_partnerwise.month','rate_card_partnerwise.year','rate_card_partnerwise.first_year_trail','rate_card_partnerwise.second_year_trail','rate_card_partnerwise.b30','rate_card_additional.special_additional_first_year_trail','rate_card_additional.special_additional_first_year_trail_for_b30')
                    ->where('rate_card_partnerwise.status','=',1)
                    ->where('rate_card_partnerwise.partner_arn','=',$arn_number);
        if(isset($searched_text) && !empty($searched_text)){
            $data = $data->where(function($query) use($searched_text){
                $query->orWhere('rate_card_partnerwise.scheme_name', 'like', '%'. $searched_text .'%');
            });
        }
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $data = $data->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }
        $data = $data->get();
        unset($where_in_conditions);
        return $data;
    }

    public static function getCommissionStructureByID($id){
        $records = DB::table('user_account')
                ->select('user_account.*')
                ->where('user_account.ARN', '=', $id)
                ->get();
                return $records;
    }
}
