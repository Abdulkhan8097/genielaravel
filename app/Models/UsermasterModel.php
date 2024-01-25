<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class UsermasterModel extends Model
{
    use HasFactory;
    public static function getMasteruserList($input_arr = array()){
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
                    // removing leading & trailing spaces from the searched value
                    if(!is_array($value['search']['value'])){
                        // for non array elements just performing TRIM
                        $value['search']['value'] = trim($value['search']['value']);
                    }
                    else{
                        // for an array elements using array_walk to loop into each element and remove the spaces from them
                        array_walk($value['search']['value'], function(&$_value){
                            $_value = trim($_value);
                        });
                        // removing empty elements and trying to keep only UNIQUE elements available in the array
                        $value['search']['value'] = array_unique(array_filter($value['search']['value']));
                    }
                }
               
                switch($value['data']){
                    case 'users.id':
                        if(isset($value['search']['value'])){
                            if(!is_array($value['search']['value']) && !empty($value['search']['value'])){
                                $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                            }
                            elseif(is_array($value['search']['value']) && count($value['search']['value']) > 0){
                                $where_in_conditions[] = array($value['data'], $value['search']['value']);
                            }
                        }
                        break;
                    case 'status':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                            if($value['data'] == 'status'){
                                $value['data'] = 'users_details.status';
                            }
                            $where_conditions[] = array($value['data'], '=', intval($value['search']['value']));
                        }
                        break;
                    case 'name':                    
                    case 'rating':           
                    case 'email':
                    case 'mobile_number':
                    case 'employee_code':
                    case 'designation':
						if ($value['data'] == 'rating' && isset($value['search']['value']) && !empty($value['search']['value'])) {
							$where_conditions[] = [DB::raw('CONCAT(m.rating,"::",m.count)'), 'like', '%' . $value['search']['value'] . '%'];
						}
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                        }
                        break;
                    default:
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                        }
                }
                //if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            //$where_conditions[] = array($value['data'], '=', $value['search']['value']);
                       // }
            }
            unset($key, $value);
        }
        $where_conditions[] = array('is_drm_user', '=', 1);
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
            $order_by_clause = 'users.id ASC';
        }

        // checking whether to show all User(s) data or not
        if(isset($show_only_logged_in_user_data) && $show_only_logged_in_user_data){
            $retrieve_users_data = self::getSupervisedUsersList($input_arr);
            if(!$retrieve_users_data['flag_show_all_arn_data']){
                // as all ARN data should not be shown that's why assigning only supervised user list
                $where_in_conditions[] = array('users.id', $retrieve_users_data['show_data_for_users']);
            }
            // $where_conditions[] = array('users.id', '!=', $logged_in_user_id);
            unset($retrieve_users_data);
        }

        $records = DB::table('users')
                    ->join('users_details', 'users.id', '=','users_details.user_id')
                    ->leftJoin('role_master', 'role_master.id', '=','users_details.role_id')
                    ->leftJoin(DB::raw('(select `user_id`, COUNT(id) as count, SUM(customer_given_rating) as total_rating, ROUND(AVG(customer_given_rating), 1) as rating from `drm_meeting_logger` where `customer_given_rating` > 0 group by `user_id`) as m'), 'm.user_id', '=','users.id')
                    ->select('users.name', 'users.email','users_details.employee_code','users_details.mobile_number','users_details.designation','users_details.id','users_details.reporting_to','users_details.role_id','users_details.serviceable_pincode','users_details.cadre_of_employee','users_details.status','role_master.label','users_details.status','users_details.appointment_link','users.id AS user_id',
					DB::raw('concat(m.rating," (",m.count,")") as rating'))
					->where('users_details.is_deleted', '=',0)
					->where('users_details.is_old', '=',0);

        if(count($where_conditions) > 0){
            $records = $records->where($where_conditions);
        }

        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition){
                $records = $records->whereIn($in_condition[0], $in_condition[1]);
            }
            unset($in_condition);
        }

        $no_of_records = 0;
        if(!$flag_export_data){
            // calculating number of records without using LIMIT & OFFSET for pagination, only when showing data inside DataTable
            $no_of_records = $records->where($where_conditions)->count();
            if(isset($start) && is_numeric($start)){
                $records = $records->offset($start);
            }

            if(isset($length) && is_numeric($length)){
                $records = $records->limit($length);
            }
        }

        $reporting_list = array();
        if(!isset($get_reporting_list) || (isset($get_reporting_list) && $get_reporting_list)){
            $reporting_list=self::get_user_list();
        }
        //print_r($reporting_list);
        $records = $records->orderByRaw($order_by_clause)->get();
        if(!$records->isEmpty()){
            // retrieving logged in user role and permission details
            if(!isset($logged_in_user_roles_and_permissions)){
                $logged_in_user_roles_and_permissions = array();
            }
            if(!isset($flag_have_all_permissions)){
                $flag_have_all_permissions = false;
            }

            foreach($records as $key => $value){
                if(!$flag_export_data){
                    // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                    $value->action  = '';
                    // showing Edit User link only when it have permission to do so
                    if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('edit-detail', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE && in_array('usermaster-update', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE)){
                        if(isset($show_only_logged_in_user_data) && $show_only_logged_in_user_data){//y($logged_in_user_id, 'logged_in_user_id');y($value->id, 'value->id');
                            if(isset($logged_in_user_id) && ($logged_in_user_id != $value->user_id)){
                                $value->action .= '<a href="javascript:void(0);" title="Edit Record"><i class="icons edit-icon" title="Edit Record" alt="Edit Record" onclick="edit_code('.$value->id.')"></i></a>';
                            }
                        }
                        else{
                            $value->action .= '<a href="javascript:void(0);" title="Edit Record"><i class="icons edit-icon" title="Edit Record" alt="Edit Record" onclick="edit_code('.$value->id.')"></i></a>';
                        }
                    }

                    // showing Appointment Page link only when it have permission to do so
                    if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('{appointment_url}', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE)){
                        // assigning appointment link to users
                        // $value->action .= '<a href="'. env('SAMCOMF_APPOINTMENT_URL').'/Appointments/auto_login/'. md5($value->email) .'" title="Appointment Link" target="_blank"><i class="icons la-money-check-alt" alt="Appointment Link"></i></a>';

                        if(isset($value->appointment_link) && (!empty($value->appointment_link)) ){
                            $value->action .= '<a href="javascript:void(0);" class="app_link" appointment_link="'.$value->appointment_link.'"  onclick="get_appointment_link()" title="Appointment Link" ><i class="icons la-money-check-alt" alt="Appointment Link"></i></a>';
                        }
                        else{
                            $value->action .= '<a href="javascript:void(0);" title="Appointment Link" onclick="generate_appointment_link(\''.$value->email.'\')"><i class="icons la-money-check-alt" alt="Appointment Link"></i></a>';
                        }
                    }

                    if(!empty($value->serviceable_pincode))
                    {
                        // showing Serviceable Pincode link only when it have permission to do so
                        if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('services-pincode', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE)){
                            $value->serviceable_pincode = '<a href="javascript:void(0);" title="Serviceable Pincode"><i class="icons view-icon" title="Serviceable Pincode" alt="Edit Record" onclick="view_servicecode('.$value->id.')"></i></a>';
                        }
                        else{
                            // If user don't have permission to view the pincodes that's why setting field value as BLANK
                            $value->serviceable_pincode='';
                        }
                    }else{
                        $value->serviceable_pincode='';
                    }
                    
                    if(!empty($value->reporting_to) && isset($reporting_list[$value->reporting_to]))
                    {
                     $value->reporting_to=$reporting_list[$value->reporting_to];
                    }
                    else{
                        $value->reporting_to='';
                    }
                    if(!isset($do_not_show_role_label) || (isset($do_not_show_role_label) && !$do_not_show_role_label)){
                        $value->role_id=$value->label;
                    }
                }
                else{
                    if(!empty($value->reporting_to) && isset($reporting_list[$value->reporting_to]))
                    {
                     $value->reporting_to=$reporting_list[$value->reporting_to];
                    }
                    else{
                        $value->reporting_to='';
                    }
                    if(!isset($do_not_show_role_label) || (isset($do_not_show_role_label) && !$do_not_show_role_label)){
                        $value->role_id=$value->label;
                    }
                }

                // assigning label to current partner a readable status i.e. Created/Activated etc.
                if(isset($value->status) && (intval($value->status) == 1)){
                    $value->status = 'Active';
                }
                else{
                    $value->status = 'Inactive';
                }
            }
            unset($key, $value);
        }
        unset($where_conditions, $where_in_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }
    public static function get_user_list($input_arr = array())
    {
        $user_list_name=array();
        $records = DB::table('users')
                        ->join('users_details', 'users.id', '=','users_details.user_id')
                        ->select('users.name', 'users.email','users_details.employee_code','users_details.mobile_number','users_details.designation','users_details.id','users_details.role_id','users.id as uid','users_details.reporting_to')
                        ->where('users.is_drm_user', '=',1)
                        ->where('users_details.status', '=',1)
						->where('users_details.is_old', '=',0)
                        ->where('users_details.is_deleted', '=',0);
        if(isset($input_arr['user_id']) && is_array($input_arr['user_id']) && count($input_arr['user_id']) > 0){
            $records = $records->whereIn('users.id', $input_arr['user_id']);
        }
        $records = $records->orderBy('users.name', 'ASC')->get();
        foreach($records as $value)
        {
            $user_list_name[$value->uid]=$value->name;
        }
        return $user_list_name;
    }
    //get amc master list
    public static function get_amc_list()
    {
         $records = DB::table('drm_amc_master')
                ->select('*')
                ->get();
                return $records;
    }

    // function to get list of available users who have roles assigned
    public static function get_users_having_roles_assigned()
    {
        $records = DB::table('users')
                    ->join('users_details', 'users.id', '=', 'users_details.user_id')
                    ->join('role_master AS roles', 'users_details.role_id', '=', 'roles.id')
                    ->leftJoin('users AS reporting_to_tbl', 'users_details.reporting_to', '=', 'reporting_to_tbl.id')
                    ->select('users.id', 'users.name', 'roles.label AS role_name', 'reporting_to_tbl.name AS reporting_to_name')
                    ->where('users_details.status', '=', 1)
                    ->where('users_details.skip_in_arn_mapping', '=', 0)
                    ->where('roles.status', '=', 1)
                    ->get();
        return $records->toArray();
    }

    // function to get list of available roles
    public static function get_roles_from_master($input_arr = array())
    {
        $flag_status_condition_added = false;       // checking whether STATUS condition is added or not while fetching the data
        $where_conditions = array();
        if(is_array($input_arr) && count($input_arr) > 0){
            foreach($input_arr as $key => $value){
                if(in_array($key, array('print_query', 'get_count', 'order_by')) === FALSE){
                    $where_conditions[] = array($key, '=', $value);
                }

                // if STATUS already mentioned from input parameters then marking the flag_status_condition_added as TRUE
                if($key == 'status'){
                    $flag_status_condition_added = true;
                }
            }
            unset($key, $value);
        }

        // flag_status_condition_added is FALSE then fetching only ACTIVE directors only
        if(!$flag_status_condition_added){
            $where_conditions[] = array('status', '=', 1);
        }

        $enable_query_log = false;
        if(isset($input_arr['print_query']) && (intval($input_arr['print_query']) == 1)){
            $enable_query_log = true;
            DB::enableQueryLog();
        }

        // retrieving data from MySQL table: role_master
        $roles = DB::table('role_master AS roles')
        ->select('roles.*')
        ->where($where_conditions);

        // checking whether want to retrieve count of records or not
        if(isset($input_arr['get_count']) && ($input_arr['get_count'] == 1)){
            $roles = $roles->count();
        }
        else{
            // get field wise data for a record
            $order_by_clause = 'roles.id ASC';
            if(isset($input_arr['order_by']) && !empty($input_arr['order_by'])){
                $order_by_clause = $input_arr['order_by'];
            }
            $roles = $roles->orderByRaw($order_by_clause)->get();
            unset($order_by_clause);
        }

        if($enable_query_log){
            $query = DB::getQueryLog();
            dd($query);
        }

        return $roles;
    }

    // function get list of ACTIVE permissions available against an user
    public static function get_specific_user_role_and_permissions($user_id = 0, $current_page_url = ''){
        $output_arr = array('role_details' => array(), 'role_permissions' => array());
        if(!empty($user_id) && !is_array($user_id)){
            $user_id = intval($user_id);
        }
        elseif(!empty($user_id) && is_array($user_id) && count($user_id) > 0){
            array_walk($user_id, function(&$_value){
                $_value = intval($_value);
            });
        }

        // retrieving the logged in user role id
        $retrieved_data = self::getMasteruserList(array('columns' => json_encode(array(
                                                                                    array('data' => 'users.id',
                                                                                          'search' => array('value' => $user_id)
                                                                                    )
                                                                                )),
                                                                   'export_data' => 1,
                                                                   'get_reporting_list' => false,
                                                                   'do_not_show_role_label' => true
                                                                )
                                                        );
        // setting default role id in case if user did not have any role assigned
        $user_assigned_role_id = 0;
        if(isset($retrieved_data['records']) && !$retrieved_data['records']->isEmpty()){
            // coming here if user have a role assigned
            if(isset($retrieved_data['records'][0]->role_id) && !empty($retrieved_data['records'][0]->role_id)){
                $user_assigned_role_id = intval($retrieved_data['records'][0]->role_id);
            }
        }
        unset($retrieved_data);

        // retrieving the permissions available for a role
        if($user_assigned_role_id > 0){
            $arr_role_permissions = array();        // stores the list of permissions available against an user assigned role
            $role_details = DB::table('role_master')
                                ->where('id', $user_assigned_role_id)
                                ->where('status', 1)
                                ->select('label', 'have_all_permissions', 'show_all_arn_data')
                                ->get();
            if(!$role_details->isEmpty()){
                $role_details = $role_details->toArray();
                if(isset($role_details[0]) && is_object($role_details[0]) && get_object_vars($role_details[0]) > 0){
                    $output_arr['role_details'] = (array) $role_details[0];
                }

                // retrieving list of ACTIVE permissions available against an role id
                $role_where_conditions = array(array('role_id', '=', $user_assigned_role_id),
                                               array('status', '=', 1));
                if(!empty($current_page_url)){
                    $role_where_conditions[] = array('page_url', '=', $current_page_url);
                }
                $arr_role_permissions = DB::table('role_permissions')
                                            ->where($role_where_conditions)
                                            ->select('page_url')
                                            ->get();
                if(!$arr_role_permissions->isEmpty()){
                    $output_arr['role_permissions'] = array_column($arr_role_permissions->toArray(), 'page_url');
                }
                unset($role_where_conditions);
            }
            unset($role_details, $arr_role_permissions);
        }
        return $output_arr;
    }

    // function get list of users for which are marked as "Reporting Person" against an input parameter $user_id
    public static function getListofReportedPersons($user_id = 0){
        $output_arr = array();
        $where_conditions = array(array('users.is_drm_user', '=', 1),
							array('users_details.status', '=', 1),
							array('users_details.is_old', '=',0),
							array('users_details.is_deleted', '=', 0));
        $where_in_conditions = array();

        if(!empty($user_id) && !is_array($user_id)){
            // if passed input parameter is not an array then retrieving data for single user
            $user_id = intval($user_id);
            if(empty($user_id)){
                // for an empty user_id found then just returning the BLANK array, so that function should not get called recursively
                return $output_arr;
            }
            $where_conditions[] = array('users_details.reporting_to', '=', $user_id);
        }
        elseif(!empty($user_id) && is_array($user_id) && count($user_id) > 0){
            // if passed input parameter is an array of elements then using IN query to get result for those input elements
            array_walk($user_id, function(&$_value){
                $_value = intval($_value);
            });
            $user_id = array_unique(array_filter($user_id));
            if(count($user_id) == 0){
                // for an empty user_id found then just returning the BLANK array, so that function should not get called recursively
                return $output_arr;
            }
            $where_in_conditions['users_details.reporting_to'] = $user_id;
        }

        try{
            $retrieved_data = DB::table('users')
                                ->join('users_details', 'users.id', '=', 'users_details.user_id')
                                ->select('users.id');
            if(count($where_conditions) > 0){
                $retrieved_data = $retrieved_data->where($where_conditions);
            }
            if(count($where_in_conditions) > 0){
                foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                    $retrieved_data = $retrieved_data->whereIn($in_condition_field, $in_condition_data);
                }
                unset($in_condition_field, $in_condition_data);
            }
            $retrieved_data = $retrieved_data->get();

            if(!$retrieved_data->isEmpty()){
                // if records found then finding the user(s) in recursive manner against the list of record(s) found here
                $found_supervised_persons = array_column($retrieved_data->toArray(), 'id');
                $output_arr = array_merge($output_arr, $found_supervised_persons);
                if(count($found_supervised_persons) > 0){
                    $arr_found_users = self::getListofReportedPersons($found_supervised_persons);
                    $output_arr = array_merge($output_arr, $arr_found_users);
                    unset($arr_found_users);
                }
                unset($found_supervised_persons);
            }
            unset($where_conditions, $retrieved_data);
        }
        catch(Exception | \Illuminate\Database\QueryException $e){
        }
        return $output_arr;
    }

    // function get list of associated ARN for the input parameter user_id
    public static function getListofAssociatedARN($input_arr = array()){
        $output_arr = array();
        extract($input_arr);

        if(!isset($user_ids) || (isset($user_ids) && !is_array($user_ids))){
            $user_ids = array(-1);
        }

        $retrieved_arn_data = DB::table('drm_distributor_master')->select('ARN')->whereIn('direct_relationship_user_id', $user_ids)->get()->toArray();
        if(is_array($retrieved_arn_data) && count($retrieved_arn_data) > 0){
            $output_arr = array_column($retrieved_arn_data, 'ARN');
        }
        unset($retrieved_arn_data);
        return $output_arr;
    }

    // function for checking whether to show all ARN/User data against an input parameter user id
    public static function getSupervisedUsersList($input_arr = array()){
        $output_arr = array('flag_show_all_arn_data' => false, 'show_data_for_users' => array());
        extract($input_arr);
        // checking whether to show all ARN data or not
        if(!isset($flag_show_all_arn_data)){
            $flag_show_all_arn_data = false;
        }
        $output_arr['flag_show_all_arn_data'] = $flag_show_all_arn_data;

        // retrieving logged_in_user_id details from posted array of elements
        if(!isset($logged_in_user_id) || empty($logged_in_user_id) || !is_numeric($logged_in_user_id)){
            $logged_in_user_id = 0;
        }
        $logged_in_user_id = intval($logged_in_user_id);

        // retrieving list of users for whom currently logged in user is marked as "Reporting Person"
        if(!$flag_show_all_arn_data){
            if($logged_in_user_id > 0){
                $retrieve_users_data = self::getListofReportedPersons($logged_in_user_id);
                $output_arr['show_data_for_users'] = $retrieve_users_data;
                unset($retrieve_users_data);

                // adding logged in user id so that data will be shown for that user also
                $output_arr['show_data_for_users'] = array_merge($output_arr['show_data_for_users'], array($logged_in_user_id));

                // checking whether want to retrieve associated ARN data for the logged_in_user_id or not
                if(isset($get_list_of_assigned_arn) && ($get_list_of_assigned_arn == 1)){
                    $output_arr['list_of_assigned_arn'] = self::getListofAssociatedARN(array('user_ids' => ($output_arr['show_data_for_users']??array())));
                }
            }

            // if users list not present then adding default value as -1 so that any data won't shown to user against an ARN
            if(!is_array($output_arr['show_data_for_users']) || (is_array($output_arr['show_data_for_users']) && count($output_arr['show_data_for_users']) == 0)){
                $output_arr['show_data_for_users'] = array(-1);
            }
        }
        return $output_arr;
    }

    public static function update_encrypted_password_for_bdm_list($input_arr = array()){
        extract($input_arr);                // Import variables into the current symbol table from an array
        $err_flag = 0;                      // err_flag is 0 means no error
        $err_msg = array();                 // err_msg stores list of errors found during execution
        // stores required JSON output values
        $output_arr = array('display_messages' => array());

        // helps to identify whether to create log file and store all display messages for future references or not
        if(!isset($flag_log_display_messages) || empty($flag_log_display_messages)){
            $flag_log_display_messages = false;
        }

        // helps to identify whether to show logged query or not
        if(!isset($enable_query_log) || empty($enable_query_log)){
            $enable_query_log = false;
        }

        $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tScript start datetime";

        $where_conditions = array();
        $where_conditions[] = array('backup.password', '!=', '');
        if(isset($user_email) && !empty($user_email) && filter_var($user_email, FILTER_VALIDATE_EMAIL)){
            $where_conditions[] = array('main.email', '=', $user_email);
        }

        try{
            $affected_rows = 0;
            if($enable_query_log){
                DB::enableQueryLog();
            }
            $retrieved_users = DB::table('drm_partners_rankmf_bdm_list AS main')
                                    ->join('drm_partners_rankmf_bdm_list_backup AS backup', function($join){
                                        $join->on('main.email', '=', 'backup.email');
                                        $join->on('main.login_master_sr_id', '=', 'backup.login_master_sr_id');
                                    })
                                    ->whereNull('main.password')
                                    ->whereNotNull('backup.password')
                                    ->where($where_conditions)
                                    ->select('backup.password', 'main.id', 'main.login_master_sr_id', 'main.email')
                                    ->get();
            if($enable_query_log){
                $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\t". print_r(DB::getQueryLog(), true);
            }

            if(!$retrieved_users->isEmpty()){
                foreach($retrieved_users as $user){
                    $update_query_result = DB::table('drm_partners_rankmf_bdm_list')
                                                ->where(array('id' => $user->id,
                                                              'login_master_sr_id' => $user->login_master_sr_id,
                                                              'email' => $user->email)
                                                    )
                                                ->update(array('password' => \Illuminate\Support\Facades\Hash::make($user->password)));
                    if($update_query_result){
                        $affected_rows += 1;
                    }
                    unset($update_query_result);
                }
                unset($user);
            }
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tPassword updated for ". $affected_rows ." user(s)";
            unset($retrieved_users, $affected_rows);
        }
        catch(Exception | \Illuminate\Database\QueryException $e){
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tError occurred: ". $e->getMessage();
        }
        unset($where_conditions);

        $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tThe command execution is successful!";
        $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tScript end datetime";

        if($flag_log_display_messages && is_array($output_arr['display_messages']) && count($output_arr['display_messages']) > 0){
            # getting directory path of currently executing script
            if(isset($calling_it_from_browser) && $calling_it_from_browser){
                $script_directory_path = dirname($_SERVER['SCRIPT_FILENAME']);
            }
            else{
                $script_directory_path = dirname($_SERVER['SCRIPT_NAME']);
            }
            $log_filepath = $script_directory_path .'/public/storage/logs/encryptedpasswordforbdm_'. date('Y-m-d') .'.txt';
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

    public static function UpdateProfile($input_arr = array()){
        try{
            $data = DB::table('users')->where($input_arr['where'])->update($input_arr['data']);
            return true;
        }
        catch(Exception | \Illuminate\Database\QueryException $e){
            return false;
        }
    }

    public static function InsertUpdateQualityRelationshipARNScore($input_arr = array()){
        extract($input_arr);                // Import variables into the current symbol table from an array
        $err_flag = 0;                      // err_flag is 0 means no error
        $err_msg = array();                 // err_msg stores list of errors found during execution
        // stores required JSON output values
        $output_arr = array('display_messages' => array());

        // helps to identify whether to create log file and store all display messages for future references or not
        if(!isset($flag_log_display_messages) || empty($flag_log_display_messages)){
            $flag_log_display_messages = false;
        }

        $current_datetime = date('Y-m-d H:i:s');
        $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tScript start datetime";

        $where_conditions = 'AND users.status = :user_status AND users_details.status = :users_details_status';
        $where_conditions_data = array();
        $where_conditions_data[':user_status'] = 1;
        $where_conditions_data[':users_details_status'] = 1;
        if(isset($user_email) && !empty($user_email) && filter_var($user_email, FILTER_VALIDATE_EMAIL)){
            $where_conditions .= 'AND users.email = :user_email';
            $where_conditions_data[':user_email'] = $user_email;
        }

        if(!isset($score_of_date) || empty($score_of_date) || strtotime($score_of_date) === FALSE){
            $score_of_date = date('Y-m-d');
        }
        else{
            $score_of_date = date('Y-m-d', strtotime($score_of_date));
        }

        $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\twhere_conditions: ". $where_conditions;
        $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\twhere_conditions_data: ". print_r($where_conditions_data, true);
        $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tscore_of_date: ". $score_of_date;

        try{
            // Query to update score of an user and score date whose details already available with us
            DB::update("UPDATE drm_users_arn_relationship_quality_score AS score INNER JOIN (SELECT drm.direct_relationship_user_id AS user_id, '". $score_of_date ."' AS score_of_date, COUNT(drm.ARN) AS no_of_assigned_arn, (COUNT(drm.ARN) * 100) AS maximum_score, SUM(IFNULL(relation.score, 0)) AS calculated_score, 1 AS status FROM users_details INNER JOIN users ON (users_details.user_id = users.id) INNER JOIN drm_distributor_master AS drm ON (users_details.user_id = drm.direct_relationship_user_id) LEFT JOIN drm_relationship_quality_master AS relation ON (drm.relationship_quality_with_arn = relation.label) WHERE drm.direct_relationship_user_id IS NOT NULL ". $where_conditions ." GROUP BY drm.direct_relationship_user_id ORDER BY score DESC) AS a ON (score.user_id = a.user_id AND score.score_of_date = a.score_of_date) SET score.no_of_assigned_arn = a.no_of_assigned_arn, score.maximum_score = a.maximum_score, score.calculated_score = a.calculated_score WHERE 1;", $where_conditions_data);

            // Query to insert records of quality of relationship with ARN against an user
            DB::insert("INSERT INTO drm_users_arn_relationship_quality_score(user_id, score_of_date, no_of_assigned_arn, maximum_score, calculated_score, status) SELECT drm.direct_relationship_user_id AS user_id, '". $score_of_date ."' AS score_of_date, COUNT(drm.ARN) AS no_of_assigned_arn, (COUNT(drm.ARN) * 100) AS maximum_score, SUM(IFNULL(relation.score, 0)) AS calculated_score, 1 AS status FROM users_details INNER JOIN users ON (users_details.user_id = users.id) INNER JOIN drm_distributor_master AS drm ON (users_details.user_id = drm.direct_relationship_user_id) LEFT JOIN drm_relationship_quality_master AS relation ON (drm.relationship_quality_with_arn = relation.label) LEFT JOIN drm_users_arn_relationship_quality_score AS score ON (users_details.user_id = score.user_id AND score.score_of_date = '". $score_of_date ."') WHERE drm.direct_relationship_user_id IS NOT NULL AND score.user_id IS NULL ". $where_conditions ." GROUP BY drm.direct_relationship_user_id ORDER BY score DESC;", $where_conditions_data);

            // Query to get list of records updated or inserted into table: drm_users_arn_relationship_quality_score after script execution started
            $affected_rows = DB::table('drm_users_arn_relationship_quality_score')
                                ->where('created_at', '>=', $current_datetime)
                                ->orWhere('updated_at', '>=', $current_datetime)
                                ->select(DB::raw('COUNT(1) AS total'))
                                ->first();
            if(isset($affected_rows) && isset($affected_rows->total)){
                $affected_rows = $affected_rows->total;
            }
            else{
                $affected_rows = 0;
            }
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tInserted/updated rows are: ". $affected_rows;
            unset($affected_rows);
        }
        catch(Exception | \Illuminate\Database\QueryException $e){
            $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tError occurred: ". $e->getMessage();
        }
        unset($current_datetime);

        $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tThe command execution is successful!";
        $output_arr['display_messages'][] = date('Y-m-d H:i:s') ."\tScript end datetime";

        if($flag_log_display_messages && is_array($output_arr['display_messages']) && count($output_arr['display_messages']) > 0){
            # getting directory path of currently executing script
            if(isset($calling_it_from_browser) && $calling_it_from_browser){
                $script_directory_path = dirname($_SERVER['SCRIPT_FILENAME']);
            }
            else{
                $script_directory_path = dirname($_SERVER['SCRIPT_NAME']);
            }
            $log_filepath = $script_directory_path .'/public/storage/logs/calculate_arn_relationship_quality_score_'. date('Y-m-d') .'.txt';
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
}
