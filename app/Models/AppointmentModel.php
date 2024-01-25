<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class AppointmentModel extends Model
{
    use HasFactory;
    // get_appointment_data by email
    public static function get_appointment_data($request = array())
    {
        extract($request);
        return $records = DB::table('appointments.ea_users')
                ->select('ea_users.*')
                ->where('email', '=', $email)
                ->get();
    }

    // get user list by email
    public static function get_bdm_user_list($request = array())
    {
        extract($request);
        return $records = DB::table('users')
                 ->join('users_details', 'users.id', '=','users_details.user_id')
                ->select('users.id as user_id','users.name', 'users.email','users_details.employee_code','users_details.mobile_number','users_details.designation','users_details.id','users_details.role_id','users.id as uid','users_details.reporting_to','users_details.appointment_link')
                ->where('users.is_drm_user', '=',1)
				->where('users_details.is_deleted', '=',0)
				->where('users_details.is_old', '=',0)
                ->where('users.email', '=',$email)
                ->get();
    }

    // update query
    // $table = table name 
    // $where = where conditions
    public static function update_data($table,$where = array(),$update_data = array()){
        // y($table,'table');
        // y($where,'where conditions');
        // y($update_data,'update_data');
        $update_response = DB::table($table)
            ->where($where)
            ->update($update_data);

        return $update_response;
    }

    public static function getAppintments($input_arr = array()){
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
                        if(isset($value['search']['value']) && !empty($value['search']['value']) && strpos($value['search']['value'], ';') !== FALSE){
                            $searching_dates = explode(';', $value['search']['value']);
                            if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE && isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                // when both FROM DATE & TO DATE both are present
                                $where_conditions[] = array('appointments.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                $where_conditions[] = array('appointments.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                            }
                            else{
                                // when either of the FROM DATE & TO DATE are present
                                if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE){
                                    $where_conditions[] = array('appointments.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                }
                                if(isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                    $where_conditions[] = array('appointments.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                                }
                            }
                            unset($searching_dates);
                        }
                        break;
                    case 'user_name':
                    case 'service_name':
                    case 'customer_name':
                    case 'customer_email':
                    case 'customer_number':
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            if($value['data'] == 'user_name'){
                                $value['data'] = DB::raw("CONCAT(users.first_name,' ',users.last_name)");
                            }
                            elseif($value['data'] == 'service_name'){
                                $value['data'] = 'services.name';
                            }
                            elseif($value['data'] == 'customer_name'){
                                $value['data'] = DB::raw("CONCAT(customer.first_name,' ',customer.last_name)");
                            }
                            elseif($value['data'] == 'customer_email'){
                                $value['data'] = 'customer.email';
                            }
                            elseif($value['data'] == 'customer_number'){
                                $value['data'] = 'customer.phone_number';
                            }
                            $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
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
            $order_by_clause = 'appointments.start_datetime ASC';
        }
        // DB::enableQueryLog();
        $records = DB::table('appointments.ea_appointments AS appointments')
                    ->leftjoin('appointments.ea_users as users', 'appointments.id_users_provider', '=','users.id')
                    ->leftjoin('appointments.ea_users as customer', 'appointments.id_users_customer', '=','customer.id')
                    ->leftjoin('appointments.ea_services as services', 'appointments.id_services', '=','services.id')
                    ->select('services.name AS service_name',DB::raw("CONCAT(users.first_name,' ',users.last_name) AS user_name"), (!$flag_export_data?'appointments.start_datetime':DB::raw('DATE_FORMAT(appointments.start_datetime, "%d/%m/%Y") AS start_datetime')), (!$flag_export_data?'appointments.end_datetime':DB::raw('DATE_FORMAT(appointments.end_datetime, "%d/%m/%Y") AS end_datetime')),DB::raw("CONCAT(customer.first_name,' ',customer.last_name) AS customer_name"),'customer.email AS customer_email','customer.phone_number AS customer_number','appointments.notes AS notes','customer.address AS customer_address','customer.city AS customer_city','customer.state AS customer_state','customer.zip_code AS customer_zipcode');
        
        if(count($where_conditions) > 0){
            $records = $records->where($where_conditions);
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
        $records = $records->orderByRaw($order_by_clause)->get();
        // dd(DB::getQueryLog());
        if(!$records->isEmpty()){

            foreach($records as $key => $value){
                if(!$flag_export_data){
                    // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                    $value->action = '';
                }
            }
            unset($key, $value);

        }
        unset($where_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }
    
}
