<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class ReportModel extends Model
{
    use HasFactory;

    public static function getProjectFocusPartnerList($input_arr = array()){
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
                    case 'start_datetime':
                    case 'end_datetime':
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
                    case 'arn_holders_name':
                    case 'relationship_mapped_to':
                    case 'arn_email':
                    case 'arn_telephone_r':
                    case 'arn_telephone_o':
                    case 'arn_city':
                            if(isset($value['search']['value']) && !empty($value['search']['value'])){
                                if($value['data'] == 'relationship_mapped_to'){
                                    $value['data'] = 'b.name';
                                }
                                else{
                                    $value['data'] = 'a.'. $value['data'];
                                }
    
                                if($value['data'] == 'a.ARN' && isset($exact_arn_match) && (intval($exact_arn_match) == 1)){
                                    $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                                }
                                else{
                                        $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                                }
                            }
                            break;
                    case 'is_samcomf_partner':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                                $where_conditions[] = array('a.'. $value['data'], '=', $value['search']['value']);           
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
        $order_by_field = "a.ARN";
        if(isset($order[0]) && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
            if($columns[$order[0]['column']]['data'] == 'last_meeting_date'){
                $order_by_field = "last_meeting_date";
            }
            elseif($columns[$order[0]['column']]['data'] == 'relationship_mapped_to'){
                $order_by_field = "b.name";
            }
            else{
                $order_by_field = "a.". $columns[$order[0]['column']]['data'];
            }
        }
        
        $dir = "ASC";
        if(isset($order[0]) && isset($order[0]['dir'])) {
            $dir = $order[0]['dir'];
        }

        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['a.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $records = DB::table('drm_distributor_master AS a')
                        ->select('a.ARN','a.arn_holders_name','a.arn_email','a.arn_telephone_o','a.arn_telephone_r','a.arn_city','b.name as relationship_mapped_to','a.total_ind_aum','a.ind_aum_as_on_date','a.is_samcomf_partner','a.samcomf_partner_aum',DB::raw("(SELECT IFNULL(b.start_datetime, NULL) AS last_meeting_date FROM drm_meeting_logger AS b WHERE a.ARN = b.ARN ORDER BY b.start_datetime DESC LIMIT 0, 1) AS last_meeting_date"))
                        ->leftjoin('users as b', 'a.direct_relationship_user_id', '=', 'b.id')
                        ->where('a.project_focus', '=','yes')
                        ->havingRaw('DATEDIFF(CURDATE(), IFNULL(last_meeting_date, CURDATE())) > 90');
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
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        // $value->action  = '';
                        // showing View Distributors page link only when it have permission to do so
                         $value->is_samcomf_partner = !empty($value->is_samcomf_partner) ? 'Yes':'No';
                        
                }else{
                    // $value->action  = '';
                    $value->is_samcomf_partner = !empty($value->is_samcomf_partner) ? 'Yes':'No';
            }
                unset($key, $value);
            }
            unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions);
        }
    }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        unset($where_conditions, $where_in_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getProjectEmergePartnerList($input_arr = array()){

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
                    case 'start_datetime':
                    case 'end_datetime':
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
                    case 'arn_holders_name':
                    case 'relationship_mapped_to':
                    case 'arn_email':
                    case 'arn_telephone_r':
                    case 'arn_telephone_o':
                    case 'arn_city':
                            if(isset($value['search']['value']) && !empty($value['search']['value'])){
                                if($value['data'] == 'relationship_mapped_to'){
                                    $value['data'] = 'b.name';
                                }
                                else{
                                    $value['data'] = 'a.'. $value['data'];
                                }
    
                                if($value['data'] == 'a.ARN' && isset($exact_arn_match) && (intval($exact_arn_match) == 1)){
                                    $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                                }
                                else{
                                        $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                                }
                            }
                            break;
                    case 'is_samcomf_partner':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                                $where_conditions[] = array('a.'. $value['data'], '=', $value['search']['value']);           
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
        $order_by_field = "a.ARN";
        if(isset($order[0]) && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
            if($columns[$order[0]['column']]['data'] == 'last_meeting_date'){
                $order_by_field = "last_meeting_date";
            }
            elseif($columns[$order[0]['column']]['data'] == 'relationship_mapped_to'){
                $order_by_field = "b.name";
            }
            else{
                $order_by_field = "a.". $columns[$order[0]['column']]['data'];
            }
        }
        
        $dir = "ASC";
        if(isset($order[0]) && isset($order[0]['dir'])) {
            $dir = $order[0]['dir'];
        }

        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['a.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);
        
        $records = DB::table('drm_distributor_master AS a')
                        ->select('a.ARN','a.arn_holders_name','a.arn_email','a.arn_telephone_o','a.arn_telephone_r','a.arn_city','b.name as relationship_mapped_to','a.total_ind_aum','a.ind_aum_as_on_date','a.is_samcomf_partner','a.samcomf_partner_aum',DB::raw("(SELECT IFNULL(b.start_datetime, NULL) AS last_meeting_date FROM drm_meeting_logger AS b WHERE a.ARN = b.ARN ORDER BY b.start_datetime DESC LIMIT 0, 1) AS last_meeting_date"))
                        ->leftjoin('users as b', 'a.direct_relationship_user_id', '=', 'b.id')
                        ->where('a.project_emerging_stars', '=','yes')
                        ->havingRaw('DATEDIFF(CURDATE(), IFNULL(last_meeting_date, CURDATE())) > 90');
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
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        // $value->action  = '';
                        // showing View Distributors page link only when it have permission to do so
                         $value->is_samcomf_partner = !empty($value->is_samcomf_partner) ? 'Yes':'No';
                        
                }else{
                    // $value->action  = '';
                    $value->is_samcomf_partner = !empty($value->is_samcomf_partner) ? 'Yes':'No';
            }
                unset($key, $value);
            }
            unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions);
        }
    }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        unset($where_conditions, $where_in_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getPartnerWithuAUMList($input_arr = array()){
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
                    case 'start_datetime':
                    case 'end_datetime':
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
                    case 'arn_holders_name':
                    case 'arn_email':
                    case 'arn_telephone_r':
                    case 'arn_telephone_o':
                    case 'arn_city':
                            if(isset($value['search']['value']) && !empty($value['search']['value'])){
                                if($value['data'] == 'relationship_mapped_to'){
                                    $value['data'] = 'b.name';
                                }
                                else{
                                    $value['data'] = 'a.'. $value['data'];
                                }
    
                                if($value['data'] == 'a.ARN' && isset($exact_arn_match) && (intval($exact_arn_match) == 1)){
                                    $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                                }
                                else{
                                        $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                                }
                            }
                            break;
                    case 'is_samcomf_partner':
                    case 'relationship_mapped_to':
                    case 'reporting_to':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                            if($value['data'] == 'relationship_mapped_to'){
                                $value['data'] = 'a.direct_relationship_user_id';
                            }
                            elseif($value['data'] == 'is_samcomf_partner'){
                                $value['data'] = 'a.is_samcomf_partner';
                            }
                            elseif($value['data'] == 'reporting_to'){
                                $value['data'] = 'b.reporting_to';
                            }
                            else{
                                $value['data'] = 'a.'. $value['data'];
                            }
                                $where_conditions[] = array($value['data'], '=', $value['search']['value']);           
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
        $order_by_field = "a.ARN";
        if(isset($order[0]) && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
            if($columns[$order[0]['column']]['data'] == 'last_meeting_date'){
                $order_by_field = "last_meeting_date";
            }
            elseif($columns[$order[0]['column']]['data'] == 'last_purchase_transaction_date'){
                $order_by_field = "last_purchase_transaction_date";
            }
            elseif($columns[$order[0]['column']]['data'] == 'reporting_to'){
                $order_by_field = "b.reporting_to";
            }
            elseif($columns[$order[0]['column']]['data'] == 'relationship_mapped_to'){
                $order_by_field = "a.direct_relationship_user_id";
            }
            else{
                $order_by_field = "a.". $columns[$order[0]['column']]['data'];
            }
        }
        
        $dir = "ASC";
        if(isset($order[0]) && isset($order[0]['dir'])) {
            $dir = $order[0]['dir'];
        }

        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['a.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $back_date = Date('Y-m-d', strtotime('-30 days'));
        $records = DB::table('drm_distributor_master AS a')
                        ->select('a.ARN','a.arn_holders_name','a.arn_email','a.arn_telephone_o','a.arn_telephone_r','a.arn_city','a.direct_relationship_user_id as relationship_mapped_to','b.reporting_to','a.total_ind_aum','a.ind_aum_as_on_date','a.is_samcomf_partner','a.samcomf_partner_aum',DB::raw("(SELECT IFNULL(b.start_datetime, NULL) AS last_meeting_date FROM drm_meeting_logger AS b WHERE a.ARN = b.ARN ORDER BY b.start_datetime DESC LIMIT 0, 1) AS last_meeting_date"),DB::raw("(SELECT COUNT(1) AS total FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails WHERE agent_code = a.ARN AND purred = 'P' AND trdt > '$back_date') AS no_of_purchase_transactions"),DB::raw("(SELECT trdt FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails WHERE agent_code = a.ARN AND purred = 'P' ORDER BY trdt DESC LIMIT 0, 1) AS last_purchase_transaction_date"))
                        ->leftjoin('users_details as b', 'a.direct_relationship_user_id', '=', 'b.user_id')
                        ->where('a.is_samcomf_partner', '=',1)
                        ->where('a.samcomf_partner_aum', '>=',50000)
                        ->havingRaw('IFNULL(no_of_purchase_transactions, 0) = 0');
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
            // retrieving logged in user role and permission details
            if(!isset($logged_in_user_roles_and_permissions)){
                $logged_in_user_roles_and_permissions = array();
            }
            if(!isset($flag_have_all_permissions)){
                $flag_have_all_permissions = false;
            }
            $records = $records->orderBy($order_by_field,$dir)->get();
            if(!$records->isEmpty()){
                $UserDetails = \App\Models\User::all()->toArray();
                $user_array_column = array_column($UserDetails, NULL, 'id');
                foreach($records as $key => $value){
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        // $value->action  = '';
                        // showing View Distributors page link only when it have permission to do so
                         $value->is_samcomf_partner = !empty($value->is_samcomf_partner) ? 'Yes':'No';

                         if(isset($user_array_column[$value->relationship_mapped_to]) && isset($user_array_column[$value->relationship_mapped_to]['name']) && !empty($user_array_column[$value->relationship_mapped_to]['name'])){
                            $value->relationship_mapped_to = $user_array_column[$value->relationship_mapped_to]['name'];
                        }

                        if(isset($user_array_column[$value->reporting_to]) && isset($user_array_column[$value->reporting_to]['name']) && !empty($user_array_column[$value->reporting_to]['name'])){
                            $value->reporting_to = $user_array_column[$value->reporting_to]['name'];
                        }
                        
                }else{
                    // $value->action  = '';
                    $value->is_samcomf_partner = !empty($value->is_samcomf_partner) ? 'Yes':'No';

                    if(isset($user_array_column[$value->relationship_mapped_to]) && isset($user_array_column[$value->relationship_mapped_to]['name']) && !empty($user_array_column[$value->relationship_mapped_to]['name'])){
                        $value->relationship_mapped_to = $user_array_column[$value->relationship_mapped_to]['name'];
                    }

                    if(isset($user_array_column[$value->reporting_to]) && isset($user_array_column[$value->reporting_to]['name']) && !empty($user_array_column[$value->reporting_to]['name'])){
                        $value->reporting_to = $user_array_column[$value->reporting_to]['name'];
                    }
            }
                unset($key, $value);
            }
            unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions);
        }
    }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        unset($where_conditions, $where_in_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getPartnerWithuAUMNoSipList($input_arr = array()){
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
                    case 'start_datetime':
                    case 'end_datetime':
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
                    case 'arn_holders_name':
                    case 'arn_email':
                    case 'arn_telephone_r':
                    case 'arn_telephone_o':
                    case 'arn_city':
                            if(isset($value['search']['value']) && !empty($value['search']['value'])){
                                if($value['data'] == 'relationship_mapped_to'){
                                    $value['data'] = 'b.name';
                                }
                                else{
                                    $value['data'] = 'a.'. $value['data'];
                                }
    
                                if($value['data'] == 'a.ARN' && isset($exact_arn_match) && (intval($exact_arn_match) == 1)){
                                    $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                                }
                                else{
                                        $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                                }
                            }
                            break;
                    case 'is_samcomf_partner':
                    case 'relationship_mapped_to':
                    case 'reporting_to':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                            if($value['data'] == 'relationship_mapped_to'){
                                $value['data'] = 'a.direct_relationship_user_id';
                            }
                            elseif($value['data'] == 'is_samcomf_partner'){
                                $value['data'] = 'a.is_samcomf_partner';
                            }
                            elseif($value['data'] == 'reporting_to'){
                                $value['data'] = 'b.reporting_to';
                            }
                            else{
                                $value['data'] = 'a.'. $value['data'];
                            }
                                $where_conditions[] = array($value['data'], '=', $value['search']['value']);           
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
        $order_by_field = "a.ARN";
        if(isset($order[0]) && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
            if($columns[$order[0]['column']]['data'] == 'last_meeting_date'){
                $order_by_field = "last_meeting_date";
            }
            elseif($columns[$order[0]['column']]['data'] == 'last_purchase_transaction_date'){
                $order_by_field = "last_purchase_transaction_date";
            }
            elseif($columns[$order[0]['column']]['data'] == 'reporting_to'){
                $order_by_field = "b.reporting_to";
            }
            elseif($columns[$order[0]['column']]['data'] == 'relationship_mapped_to'){
                $order_by_field = "a.direct_relationship_user_id";
            }
            else{
                $order_by_field = "a.". $columns[$order[0]['column']]['data'];
            }
        }
        
        $dir = "ASC";
        if(isset($order[0]) && isset($order[0]['dir'])) {
            $dir = $order[0]['dir'];
        }

        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['a.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $records = DB::table('drm_distributor_master AS a')
                        ->select('a.ARN','a.arn_holders_name','a.arn_email','a.arn_telephone_o','a.arn_telephone_r','a.arn_city','a.direct_relationship_user_id as relationship_mapped_to','b.reporting_to','a.total_ind_aum','a.ind_aum_as_on_date','a.is_samcomf_partner','a.samcomf_partner_aum',DB::raw("(SELECT IFNULL(b.start_datetime, NULL) AS last_meeting_date FROM drm_meeting_logger AS b WHERE a.ARN = b.ARN ORDER BY b.start_datetime DESC LIMIT 0, 1) AS last_meeting_date"),DB::raw("(SELECT COUNT(1) AS total FROM samcomf_investor_db.kfintec_MasterSipStp_TransactionDetails WHERE agent_code = a.ARN AND status IN ('Live SIP')) AS no_of_purchase_transactions"),DB::raw("(SELECT trdt FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails WHERE agent_code = a.ARN AND purred = 'P' ORDER BY trdt DESC LIMIT 0, 1) AS last_purchase_transaction_date"))
                        ->leftjoin('users_details as b', 'a.direct_relationship_user_id', '=', 'b.user_id')
                        ->where('a.is_samcomf_partner', '=',1)
                        ->where('a.samcomf_partner_aum', '>=',50000)
                        ->havingRaw('IFNULL(no_of_purchase_transactions, 0) = 0');
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
            // retrieving logged in user role and permission details
            if(!isset($logged_in_user_roles_and_permissions)){
                $logged_in_user_roles_and_permissions = array();
            }
            if(!isset($flag_have_all_permissions)){
                $flag_have_all_permissions = false;
            }
            $records = $records->orderBy($order_by_field,$dir)->get();
            if(!$records->isEmpty()){
                $UserDetails = \App\Models\User::all()->toArray();
                $user_array_column = array_column($UserDetails, NULL, 'id');

                foreach($records as $key => $value){
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        // $value->action  = '';
                        // showing View Distributors page link only when it have permission to do so
                         $value->is_samcomf_partner = !empty($value->is_samcomf_partner) ? 'Yes':'No';

                         if(isset($user_array_column[$value->relationship_mapped_to]) && isset($user_array_column[$value->relationship_mapped_to]['name']) && !empty($user_array_column[$value->relationship_mapped_to]['name'])){
                            $value->relationship_mapped_to = $user_array_column[$value->relationship_mapped_to]['name'];
                        }

                        if(isset($user_array_column[$value->reporting_to]) && isset($user_array_column[$value->reporting_to]['name']) && !empty($user_array_column[$value->reporting_to]['name'])){
                            $value->reporting_to = $user_array_column[$value->reporting_to]['name'];
                        }
                        
                }else{
                    // $value->action  = '';
                    $value->is_samcomf_partner = !empty($value->is_samcomf_partner) ? 'Yes':'No';

                    if(isset($user_array_column[$value->relationship_mapped_to]) && isset($user_array_column[$value->relationship_mapped_to]['name']) && !empty($user_array_column[$value->relationship_mapped_to]['name'])){
                        $value->relationship_mapped_to = $user_array_column[$value->relationship_mapped_to]['name'];
                    }

                    if(isset($user_array_column[$value->reporting_to]) && isset($user_array_column[$value->reporting_to]['name']) && !empty($user_array_column[$value->reporting_to]['name'])){
                        $value->reporting_to = $user_array_column[$value->reporting_to]['name'];
                    }
            }
                unset($key, $value);
            }
            unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions);
        }
    }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        unset($where_conditions, $where_in_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getPartnerWithuAUMUniqueClient($input_arr = array()){
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
                    case 'start_datetime':
                    case 'end_datetime':
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
                    case 'arn_holders_name':
                    case 'arn_email':
                    case 'arn_telephone_r':
                    case 'arn_telephone_o':
                    case 'arn_city':
                            if(isset($value['search']['value']) && !empty($value['search']['value'])){
                                if($value['data'] == 'relationship_mapped_to'){
                                    $value['data'] = 'b.name';
                                }
                                else{
                                    $value['data'] = 'a.'. $value['data'];
                                }
    
                                if($value['data'] == 'a.ARN' && isset($exact_arn_match) && (intval($exact_arn_match) == 1)){
                                    $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                                }
                                else{
                                        $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                                }
                            }
                            break;
                    case 'is_samcomf_partner':
                    case 'relationship_mapped_to':
                    case 'reporting_to':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                            if($value['data'] == 'relationship_mapped_to'){
                                $value['data'] = 'a.direct_relationship_user_id';
                            }
                            elseif($value['data'] == 'is_samcomf_partner'){
                                $value['data'] = 'a.is_samcomf_partner';
                            }
                            elseif($value['data'] == 'reporting_to'){
                                $value['data'] = 'b.reporting_to';
                            }
                            else{
                                $value['data'] = 'a.'. $value['data'];
                            }
                                $where_conditions[] = array($value['data'], '=', $value['search']['value']);           
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
        $order_by_field = "a.ARN";
        if(isset($order[0]) && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
            if($columns[$order[0]['column']]['data'] == 'last_meeting_date'){
                $order_by_field = "last_meeting_date";
            }
            elseif($columns[$order[0]['column']]['data'] == 'no_of_uniue_pan_linked'){
                $order_by_field = "no_of_uniue_pan_linked";
            }
            elseif($columns[$order[0]['column']]['data'] == 'reporting_to'){
                $order_by_field = "b.reporting_to";
            }
            elseif($columns[$order[0]['column']]['data'] == 'relationship_mapped_to'){
                $order_by_field = "a.direct_relationship_user_id";
            }
            else{
                $order_by_field = "a.". $columns[$order[0]['column']]['data'];
            }
        }
        
        $dir = "ASC";
        if(isset($order[0]) && isset($order[0]['dir'])) {
            $dir = $order[0]['dir'];
        }

        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['a.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $records = DB::table('drm_distributor_master AS a')
                        ->select('a.ARN','a.arn_holders_name','a.arn_email','a.arn_telephone_o','a.arn_telephone_r','a.arn_city','a.direct_relationship_user_id as relationship_mapped_to','b.reporting_to','a.total_ind_aum','a.ind_aum_as_on_date','a.is_samcomf_partner','a.samcomf_partner_aum',DB::raw("(SELECT IFNULL(b.start_datetime, NULL) AS last_meeting_date FROM drm_meeting_logger AS b WHERE a.ARN = b.ARN ORDER BY b.start_datetime DESC LIMIT 0, 1) AS last_meeting_date"),DB::raw("(SELECT COUNT(DISTINCT paN1) AS no_of_uniue_pan_linked FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final WHERE agent_code = a.ARN) AS no_of_uniue_pan_linked"))
                        ->leftjoin('users_details as b', 'a.direct_relationship_user_id', '=', 'b.user_id')
                        ->where('a.is_samcomf_partner', '=',1)
                        ->where('a.samcomf_partner_aum', '>=',50000)
                        ->havingRaw('no_of_uniue_pan_linked < 5');
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
            // retrieving logged in user role and permission details
            if(!isset($logged_in_user_roles_and_permissions)){
                $logged_in_user_roles_and_permissions = array();
            }
            if(!isset($flag_have_all_permissions)){
                $flag_have_all_permissions = false;
            }
            $records = $records->orderBy($order_by_field,$dir)->get();
            if(!$records->isEmpty()){
                $UserDetails = \App\Models\User::all()->toArray();
                $user_array_column = array_column($UserDetails, NULL, 'id');
                
                foreach($records as $key => $value){
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        // $value->action  = '';
                        // showing View Distributors page link only when it have permission to do so
                         $value->is_samcomf_partner = !empty($value->is_samcomf_partner) ? 'Yes':'No';

                         if(isset($user_array_column[$value->relationship_mapped_to]) && isset($user_array_column[$value->relationship_mapped_to]['name']) && !empty($user_array_column[$value->relationship_mapped_to]['name'])){
                            $value->relationship_mapped_to = $user_array_column[$value->relationship_mapped_to]['name'];
                        }

                        if(isset($user_array_column[$value->reporting_to]) && isset($user_array_column[$value->reporting_to]['name']) && !empty($user_array_column[$value->reporting_to]['name'])){
                            $value->reporting_to = $user_array_column[$value->reporting_to]['name'];
                        }
                        
                }else{
                    // $value->action  = '';
                    $value->is_samcomf_partner = !empty($value->is_samcomf_partner) ? 'Yes':'No';

                    if(isset($user_array_column[$value->relationship_mapped_to]) && isset($user_array_column[$value->relationship_mapped_to]['name']) && !empty($user_array_column[$value->relationship_mapped_to]['name'])){
                        $value->relationship_mapped_to = $user_array_column[$value->relationship_mapped_to]['name'];
                    }

                    if(isset($user_array_column[$value->reporting_to]) && isset($user_array_column[$value->reporting_to]['name']) && !empty($user_array_column[$value->reporting_to]['name'])){
                        $value->reporting_to = $user_array_column[$value->reporting_to]['name'];
                    }
            }
                unset($key, $value);
            }
            unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions);
        }
    }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        unset($where_conditions, $where_in_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getAumTransactionAnalytics($input_arr = array()){
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

        if(!isset($view_to_be_loaded) || (isset($view_to_be_loaded) && empty($view_to_be_loaded))){
            $view_to_be_loaded = 'year_wise_data';
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
                    case 'agent_code':
                            if(isset($value['search']['value']) && !empty($value['search']['value'])){
                                $ARN = $value['search']['value'];
                                /*if($value['data'] == 'agent_code' && isset($exact_arn_match) && (intval($exact_arn_match) == 1)){
                                    $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                                }
                                else{
                                        $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                                }*/
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
        $order_by_field = "agent_code";
        if(isset($order[0]) && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
            if($columns[$order[0]['column']]['data'] == 'total_netflow'){
                $order_by_field = DB::raw('(total_gross_inflow - total_redemptions)');
            }
            elseif($columns[$order[0]['column']]['data'] == 'total_aum'){
                $order_by_field = DB::raw('((purchased_units - redeemed_units) * latest_nav)');
            }
            else{
                $order_by_field = $columns[$order[0]['column']]['data'];
            }
        }
        
        $dir = "ASC";
        if(isset($order[0]) && isset($order[0]['dir'])) {
            $dir = $order[0]['dir'];
        }

        // checking whether to show all ARN data or not
        $input_arr['get_list_of_assigned_arn'] = 1;
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            // $where_in_conditions['a.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
            if(isset($retrieve_users_data['list_of_assigned_arn']) && is_array($retrieve_users_data['list_of_assigned_arn'])){
                if(in_array($ARN, $retrieve_users_data['list_of_assigned_arn']) === FALSE){
                    // seems that input ARN number is not assigned to the logged in user
                    $ARN = -1;
                }
            }
            else{
                // seems that logged in user don't have any distributors assigned, that's why not showing any details to them for the input ARN number
                $ARN = -1;
            }
        }
        unset($retrieve_users_data);

        $arr_select_fields = array('scheme_code',
                                   'scheme_code AS scheme_name',
                                   'agent_code',
                                   DB::raw("YEAR(trdt) AS trdt_year"),
                                   DB::raw("SUM(CASE WHEN(purred = 'P') THEN IFNULL(amt, 0) ELSE 0 END) AS total_gross_inflow"),
                                   DB::raw("SUM(CASE WHEN(purred = 'R') THEN IFNULL(amt, 0) ELSE 0 END) AS total_redemptions"),
                                   DB::raw("(SUM(CASE WHEN(purred = 'P') THEN IFNULL(amt, 0) ELSE 0 END) - SUM(CASE WHEN(purred = 'R') THEN IFNULL(amt, 0) ELSE 0 END)) AS total_netflow"),
                                   DB::raw("'' AS total_aum")
                                );
        $arr_groupby_fields = array('trdt_year', 'scheme_code');
        /*if($scheme_filter){
            $arr_groupby_fields[] = 'scheme_code';
        }*/

        // preparing SELECT & GROUP BY CLAUSE based on specific conditions
        switch($view_to_be_loaded){
            case 'month_wise_data':
                $arr_select_fields[] = DB::raw("MONTH(trdt) AS trdt_month");
                $arr_groupby_fields[] = 'trdt_month';
                break;
            case 'day_wise_data':
                $arr_select_fields[] = DB::raw("MONTH(trdt) AS trdt_month");
                $arr_select_fields[] = DB::raw("DAY(trdt) AS trdt_day");
                $arr_select_fields[] = DB::raw("DATE(trdt) AS trdt_date");
                $arr_groupby_fields[] = 'trdt_month';    
                $arr_groupby_fields[] = 'trdt_day';    
                break;
            case 'date_wise_data':
                $arr_select_fields = array(DB::raw("kfintec_Postendorsement_TransactionDetails_final.*"), DB::raw("scheme_master_details.nav AS latest_nav"), DB::raw("scheme_master_details.meta_title AS scheme_name"),DB::raw("'' AS total_aum"));
                break;
        }

        $records = DB::table('samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final')
                        ->select($arr_select_fields);
        if(is_array($arr_groupby_fields) && count($arr_groupby_fields) > 0){
            $records = $records->groupBy($arr_groupby_fields);
        }
        unset($arr_select_fields, $arr_groupby_fields);

        if(isset($scheme_code) && !empty($scheme_code) && $scheme_filter){
            $records = $records->where('scheme_code','=',$scheme_code);
        }

        $aum_year = ($aum_year??date('Y'));
        if(isset($aum_year) && is_numeric($aum_year)){
            $records = $records->whereYear('trdt','=',$aum_year);
        }

        if(isset($aum_month) && is_numeric($aum_month)){
            $records = $records->whereMonth('trdt','=',$aum_month);
        }

        if(isset($selected_date) && !empty($selected_date)){
            $where_conditions[] = array('trdt', '>=', $selected_date .' 00:00:00');
            $where_conditions[] = array('trdt', '<=', $selected_date .' 23:59:59');
        }

        if(count($where_conditions) > 0){
            $records = $records->where($where_conditions);
        }
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $records = $records->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }

        if(isset($ARN) && !empty($ARN)){
            $records = $records->whereRaw("agent_code = '". $ARN ."'");
        }

        $records = $records->orderBy($order_by_field,$dir);
        if(!$scheme_filter){
            switch($view_to_be_loaded){
                case 'month_wise_data':
                    // $records = DB::table(DB::raw('('. $records->toSql() .') AS scheme_wise_transactions'))->selectRaw("'' AS scheme_code, '' AS agent_code, trdt_year, trdt_month, SUM(scheme_wise_transactions.total_gross_inflow) AS total_gross_inflow, SUM(scheme_wise_transactions.total_redemptions) AS total_redemptions, SUM(scheme_wise_transactions.total_netflow) AS total_netflow, '' AS total_aum")->mergeBindings($records)->groupBy('trdt_year', 'trdt_month');
                    $records = DB::table($records, 'scheme_wise_transactions')->select(array(DB::raw("'' AS scheme_code"), DB::raw("'' AS agent_code"), "trdt_year", "trdt_month", DB::raw("SUM(scheme_wise_transactions.total_gross_inflow) AS total_gross_inflow"), DB::raw("SUM(scheme_wise_transactions.total_redemptions) AS total_redemptions"), DB::raw("SUM(scheme_wise_transactions.total_netflow) AS total_netflow"), DB::raw("'' AS total_aum")))->groupBy('trdt_year', 'trdt_month');
                    break;
                case 'day_wise_data':
                    // $records = DB::table(DB::raw('('. $records->toSql() .') AS scheme_wise_transactions'))->selectRaw("'' AS scheme_code, '' AS agent_code, trdt_year, trdt_month, trdt_day,trdt_date, SUM(scheme_wise_transactions.total_gross_inflow) AS total_gross_inflow, SUM(scheme_wise_transactions.total_redemptions) AS total_redemptions, SUM(scheme_wise_transactions.total_netflow) AS total_netflow, '' AS total_aum")->mergeBindings($records)->groupBy('trdt_year', 'trdt_month', 'trdt_day');
                    $records = DB::table($records, 'scheme_wise_transactions')->select(array(DB::raw("'' AS scheme_code"), DB::raw("'' AS agent_code"), 'trdt_year', 'trdt_month', 'trdt_day', 'trdt_date', DB::raw("SUM(scheme_wise_transactions.total_gross_inflow) AS total_gross_inflow"), DB::raw("SUM(scheme_wise_transactions.total_redemptions) AS total_redemptions"), DB::raw("SUM(scheme_wise_transactions.total_netflow) AS total_netflow"), DB::raw("'' AS total_aum")))->groupBy('trdt_year', 'trdt_month', 'trdt_day');
                    break;
                case 'date_wise_data':
                    break;
                default:
                    // $records = DB::table(DB::raw('('. $records->toSql() .') AS scheme_wise_transactions'))->selectRaw("'' AS scheme_code, '' AS agent_code, SUM(scheme_wise_transactions.total_gross_inflow) AS total_gross_inflow, SUM(scheme_wise_transactions.total_redemptions) AS total_redemptions, SUM(scheme_wise_transactions.total_netflow) AS total_netflow, '' AS total_aum")->mergeBindings($records);
                    $records = DB::table($records, 'scheme_wise_transactions')->selectRaw("'' AS scheme_code, '' AS agent_code, SUM(scheme_wise_transactions.total_gross_inflow) AS total_gross_inflow, SUM(scheme_wise_transactions.total_redemptions) AS total_redemptions, SUM(scheme_wise_transactions.total_netflow) AS total_netflow, '' AS total_aum");
            }
        }

        // DB::enableQueryLog();
        $no_of_records = 0;
        if(!$flag_export_data){
            // calculating number of records without using LIMIT & OFFSET for pagination, only when showing data inside DataTable
            try{
                $no_of_records = $records->pluck('scheme_code')->count();
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
            // retrieving logged in user role and permission details
            if(!isset($logged_in_user_roles_and_permissions)){
                $logged_in_user_roles_and_permissions = array();
            }
            if(!isset($flag_have_all_permissions)){
                $flag_have_all_permissions = false;
            }
            $records = $records->get();
            if(!$records->isEmpty()){   
                $get_scheme_code = self::getSchemeDetailbyCode();
                $get_scheme_code = array_column($get_scheme_code, NULL, 'RTA_Scheme_Code');
                $get_aum_details = array();
                switch($view_to_be_loaded){
                    case 'month_wise_data':
                        $get_aum_details = self::getAumForMonthwise($scheme_filter,$ARN, $aum_year.'-01-01',$scheme_code);
                        break;
                    case 'year_wise_data':
                        $get_aum_details = self::getAumForARN($scheme_filter,$ARN, $aum_year.'-12-31');
                        break;
                    case 'day_wise_data':
                        $arr_transaction_dates = array();
                        foreach($records as $_key => $_value){
                            if(isset($_value->trdt_date) && !empty($_value->trdt_date) && strtotime($_value->trdt_date) !== FALSE && in_array($_value->trdt_date, $arr_transaction_dates) === FALSE){
                                $arr_transaction_dates[] = $_value->trdt_date;
                            }
                        }
                        // y($arr_transaction_dates, 'arr_transaction_dates');
                        $get_aum_details = self::getAumForDayWise($scheme_filter,$ARN, $arr_transaction_dates,$scheme_code);
                        // y($get_aum_details, '$get_aum_details');
                        unset($arr_transaction_dates);
                        break;
                }

                foreach($records as $key => $value){
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        switch($view_to_be_loaded){
                            case 'month_wise_data':
                                $value->action  = "<a href='javascript:void(0);' title='View Daywise data' style='display:flex;align-items:center;' onclick=\"load_aum_transaction_datatable('day_wise_data', '". $value->scheme_code ."', '". $value->trdt_month ."')\"><img src='". env('APP_URL') ."/images/view-icon.png' style='margin-right:7px;' title='View Daywise data'>View</a>";
                            break;
                            case 'day_wise_data':
                                $value->action  = "<a href='javascript:void(0);' title='View Datewise data' style='display:flex;align-items:center;' onclick=\"load_aum_transaction_datatable('date_wise_data', '". $value->scheme_code ."', '". $value->trdt_month ."', '". $value->trdt_day ."')\"><img src='". env('APP_URL') ."/images/view-icon.png' style='margin-right:7px;' title='View Datewise data'>View</a>";
                            break;
                            case 'date_wise_data':
                                $value->action  = "";
                            break;
                            default:
                                $value->action  = "<a href='javascript:void(0);' title='View monthwise data' style='display:flex;align-items:center;' onclick=\"load_aum_transaction_datatable('month_wise_data', '". $value->scheme_code ."')\"><img src='". env('APP_URL') ."/images/view-icon.png' style='margin-right:7px;' title='View monthwise data'>View</a>";
                        }
                    }

                    switch($view_to_be_loaded){
                        case 'month_wise_data':
                            $value->total_aum = 0;
                            if(is_array($get_aum_details) && isset($get_aum_details[$value->trdt_year .'-'. str_pad($value->trdt_month, 2, '0', STR_PAD_LEFT) .'-01']) && isset($get_aum_details[$value->trdt_year .'-'. str_pad($value->trdt_month, 2, '0', STR_PAD_LEFT) .'-01']->total_aum) && !empty($get_aum_details[$value->trdt_year .'-'. str_pad($value->trdt_month, 2, '0', STR_PAD_LEFT) .'-01']->total_aum)){
                                $value->total_aum = $get_aum_details[$value->trdt_year .'-'. str_pad($value->trdt_month, 2, '0', STR_PAD_LEFT) .'-01']->total_aum;
                            }
                            break;
                        case 'day_wise_data':
                            $value->total_aum = 0;
                            if(isset($value->trdt_date) && !empty($value->trdt_date) && strtotime($value->trdt_date) !== FALSE && is_array($get_aum_details) && isset($get_aum_details[$value->trdt_date]) && isset($get_aum_details[$value->trdt_date]->total_aum) && !empty($get_aum_details[$value->trdt_date]->total_aum)){
                                $value->total_aum = $get_aum_details[$value->trdt_date]->total_aum;
                            }
                            break;
                        default:
                            $value->total_aum = 0;
                            if($scheme_filter){
                                // showing data scheme wise
                                if(is_array($get_aum_details) && isset($get_aum_details[$value->scheme_code]) && isset($get_aum_details[$value->scheme_code]->scheme_wise_aum) && !empty($get_aum_details[$value->scheme_code]->scheme_wise_aum)){
                                    $value->total_aum = $get_aum_details[$value->scheme_code]->scheme_wise_aum;
                                }
                            }
                            else{
                                // showing data for all schemes
                                if(is_array($get_aum_details) && isset($get_aum_details[0]) && isset($get_aum_details[0]->scheme_wise_aum) && !empty($get_aum_details[0]->scheme_wise_aum)){
                                    $value->total_aum = $get_aum_details[0]->scheme_wise_aum;
                                }
                            }
                    }

                    if(isset($value->scheme_code) && !empty($value->scheme_code) && isset($get_scheme_code[$value->scheme_code]) && isset($get_scheme_code[$value->scheme_code]->meta_title) && !empty($get_scheme_code[$value->scheme_code]->meta_title)){
                        $value->scheme_code = $get_scheme_code[$value->scheme_code]->meta_title;
                    }
                    if(isset($value->trdt_month) && !empty($value->trdt_month)){
                        $current_year = date("Y");
                        $month_text = str_pad($value->trdt_month, 2, 0, STR_PAD_LEFT);
                        $month_text = date('M', strtotime($current_year.'-'. $month_text .'-01'));
                        $value->trdt_month = $month_text;
                    }
                    unset($key, $value);
                }
                unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions, $get_scheme_code);
            }
        }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        // x(DB::getQueryLog());
        unset($where_conditions, $where_in_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getSipAnalytics($input_arr = array()){
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

        if(!isset($view_to_be_loaded) || (isset($view_to_be_loaded) && empty($view_to_be_loaded))){
            $view_to_be_loaded = 'year_wise_data';
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
                            if(isset($value['search']['value']) && !empty($value['search']['value'])){
                                // checking whether to show all ARN data or not
                                $input_arr['get_list_of_assigned_arn'] = 1;
                                $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
                                if(!$retrieve_users_data['flag_show_all_arn_data']){
                                    // as all ARN data should not be shown that's why assigning only supervised user list
                                    // $where_in_conditions['a.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
                                    if(isset($retrieve_users_data['list_of_assigned_arn']) && is_array($retrieve_users_data['list_of_assigned_arn'])){
                                        if(in_array($value['search']['value'], $retrieve_users_data['list_of_assigned_arn']) === FALSE){
                                            // seems that input ARN number is not assigned to the logged in user
                                            $value['search']['value'] = -1;
                                        }
                                    }
                                    else{
                                        // seems that logged in user don't have any distributors assigned, that's why not showing any details to them for the input ARN number
                                        $value['search']['value'] = -1;
                                    }
                                }
                                unset($retrieve_users_data);
								// if(isset($asset_filter) && !empty($asset_filter)){
								// 	// x($asset_filter);
								// 	$where_conditions[] = array('asset_type', '=', $asset_filter);
								// }
								
								// if((isset($sip_year) && is_numeric($sip_year))){
								// 	$where_conditions[] = array(DB::raw('year(start_date)'), '=',$sip_year);
								// }

                                if($value['data'] == 'ARN' && isset($exact_arn_match) && (intval($exact_arn_match) == 1)){
									// x($value['search']['value']);
                                    $where_conditions[] = array('ARN', '=', $value['search']['value']);
                                    // $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                                }
                                else{
                                        $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                                }
                            }
                            break;
                    case 'order_status':
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
							if($value['search']['value'] == 'active'){
								$where_conditions[] = array('order_status', '=', '0');
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
        $order_by_field = "start_date";
        if(isset($order[0]) && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
            if($columns[$order[0]['column']]['data'] == 'total_netflow'){
                $order_by_field = DB::raw('(total_gross_inflow - total_redemptions)');
            }
            elseif($columns[$order[0]['column']]['data'] == 'total_aum'){
                $order_by_field = DB::raw('((purchased_units - redeemed_units) * latest_nav)');
            }
            else{
                $order_by_field = $columns[$order[0]['column']]['data'];
            }
        }
        
        $dir = "ASC";
        if(isset($order[0]) && isset($order[0]['dir'])) {
            $dir = $order[0]['dir'];
        }

        if(!isset($asset_filter)){
            $asset_filter = '';
        }
        $arr_select_fields = array(DB::raw('if(order_status = \'0\',\'Live SIP\',order_status) as order_status'),
		                           'ARN',
								   (!empty($asset_filter) && $asset_filter)?'asset_type':DB::raw("'' AS asset_type"),
		                        //    'asset_type',
                                   DB::raw("SUM(installment_amount) AS installment_amount"),
                                   DB::raw("COUNT(1) AS no_of_sip"),
                                   DB::raw("Scheme_Name AS scheme_name"),
                                );
        $arr_groupby_fields = array();
        if($asset_filter){
            $arr_groupby_fields[] = 'asset_type';
        }
        // preparing SELECT & GROUP BY CLAUSE based on specific conditions
			switch($view_to_be_loaded){
			    case 'month_wise_data':
				
			        $arr_select_fields = array('ARN',
			                                    'asset_type',
			                                    DB::raw("YEAR(start_date) AS sip_registration_year"),
			                                    DB::raw("MONTH(start_date) AS sip_registration_month"),
			                                    DB::raw("SUM(IFNULL(installment_amount, 0)) AS sip_registration_amount"),
			                                    // DB::raw("SUM(CASE WHEN(order_status = 'Pending Registration') THEN IFNULL(installment_amount, 0) ELSE 0 END) AS sip_pending_registration_amount"),
			                                    DB::raw("SUM(CASE WHEN(order_status = '0') THEN IFNULL(installment_amount, 0) ELSE 0 END) AS sip_live_amount"),
			                                    DB::raw("SUM(CASE WHEN(order_status != '0') THEN IFNULL(installment_amount, 0) ELSE 0 END) AS sip_closures_amount"),
			                                    DB::raw("COUNT(1) AS no_of_sip"),
			                                    DB::raw("SUM(CASE WHEN(order_status = '0') THEN 1 ELSE 0 END) AS no_of_live_sip"),
			                                    // DB::raw("SUM(CASE WHEN(order_status = 'Pending Registration') THEN 1 ELSE 0 END) AS no_of_pending_registration_sip"),
			                                    DB::raw("SUM(CASE WHEN(order_status != '0') THEN 1 ELSE 0 END) AS no_of_closed_sip"),
			                                    'scheme_name',
			                                );
			        $arr_groupby_fields[] = 'sip_registration_year';
			        $arr_groupby_fields[] = 'sip_registration_month';
			        break;
			    case 'day_wise_data':
			        // retrieving current aum
			        $nav_to_be_retrieved_date = '';
			        if(!isset($sip_year) || (isset($sip_year) && !is_numeric($sip_year))){
			            $nav_to_be_retrieved_date .= date('Y');
			        }
			        else{
			            $nav_to_be_retrieved_date .= $sip_year;
			        }

			        if(!isset($sip_month) || (isset($sip_month) && !is_numeric($sip_month))){
			            $nav_to_be_retrieved_date .= '-12';
			        }
			        else{
			            $nav_to_be_retrieved_date .= '-'.str_pad($sip_month, 2, 0, STR_PAD_LEFT);
			        }
			        $nav_to_be_retrieved_date = date('Y-m-t', strtotime($nav_to_be_retrieved_date .'-01'));

			        // $retrieved_nav_data = \App\Models\SchemeMasterModel::get_nav(array('nav_date' => $nav_to_be_retrieved_date));
			        // $client_aum_query = "(SELECT 0 AS available_units FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final WHERE trdt <= '". $nav_to_be_retrieved_date ."' AND agent_code = kfintec_MasterSipStp_TransactionDetails.agent_code AND pan = kfintec_MasterSipStp_TransactionDetails.pan GROUP BY pan) AS client_aum";
			        // $nav_data_query = '';
			        // if(is_array($retrieved_nav_data) && count($retrieved_nav_data) > 0){
			        //     $nav_data_query = array();
			        //     foreach($retrieved_nav_data as $nav_scheme_code => $nav_data){
			        //         $nav_data_query[] = "SELECT '". $nav_scheme_code ."' AS scheme_code, ". $nav_data['NAV'] ." AS NAV, '". $nav_data['NAV_Date'] ."' AS NAV_Date";
			        //     }
			        //     unset($nav_scheme_code, $nav_data);
			        //     $nav_data_query = implode(' UNION ', $nav_data_query);
			        //     $nav_data_query = "SELECT NAV FROM (". $nav_data_query .") AS scheme_nav";
			        //     $client_aum_query = "(SELECT ((IFNULL(SUM((CASE WHEN(purred = 'P') THEN IFNULL(units, 0) ELSE 0 END)),0) - IFNULL(SUM((CASE WHEN(purred = 'R') THEN IFNULL(units, 0) ELSE 0 END)),0)) * IFNULL((". $nav_data_query ." WHERE scheme_code = kfintec_Postendorsement_TransactionDetails_final.scheme_code), 0)) AS available_units FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final WHERE trdt <= '". $nav_to_be_retrieved_date ."' AND agent_code = kfintec_MasterSipStp_TransactionDetails.agent_code AND pan = kfintec_MasterSipStp_TransactionDetails.pan GROUP BY pan) AS client_aum";
			        // }

			        $arr_select_fields = array('ARN',
											   'asset_type',
											   'client_id',
											   'start_date',
											   'scheme_code',
											   'accord_scheme_code',
			                                DB::raw("client_name AS client_name"),
			                                'client_pan',
			                                DB::raw("start_date AS sip_registered_since"),
			                                // 'umrncode',
			                                // DB::raw($client_aum_query),
			                                DB::raw("installment_amount AS sip_amount"),
			                                DB::raw("scheme_name"),
											DB::raw("CASE order_status
											WHEN 0 THEN 'Live SIP' 
											WHEN 1 THEN 'Rejected' 
											WHEN 3 THEN 'Cancelled' 
											ELSE order_status
											END AS sip_status"),
			                                DB::raw("utr_no AS umrncode"),
			                                DB::raw("client_pan AS pan"),
			                                DB::raw("current_aum AS client_aum"),
			                               
			                            );
			        $arr_groupby_fields = array();
			        // unset($retrieved_nav_data, $client_aum_query, $nav_to_be_retrieved_date);
			        break;
			    case 'date_wise_data':
			        $arr_select_fields = array(DB::raw("*"));
			        $arr_groupby_fields = array();
			        break;
			    default:
			        $arr_groupby_fields[] = 'order_status';
			}
        $records = DB::table('sip_analytics_view')
                        ->select($arr_select_fields);
                      
			

        if(is_array($arr_groupby_fields) && count($arr_groupby_fields) > 0){
            $records = $records->groupBy($arr_groupby_fields);
        }
        unset($arr_select_fields, $arr_groupby_fields);
        if(isset($asset_type) && !empty($asset_type) ){
            $records = $records->where('asset_type','=',$asset_type);
        }

        if(isset($sip_year) && is_numeric($sip_year)){
            $records = $records->whereYear('start_date','=',$sip_year);
        }

        if(isset($sip_month) && is_numeric($sip_month)){
            $records = $records->whereMonth('start_date','=',$sip_month);
        }

        if(isset($search_pan) && !empty($search_pan)){
            $records = $records->where('client_pan','like','%'. $search_pan .'%');
        }

        if(isset($search_name) && !empty($search_name)){
            $records = $records->where('client_name','like','%'. $search_name .'%');
        }
		if(isset($search_status) && !empty($search_status)){
            switch($search_status){
                case 'active':
                    $records = $records->where('order_status','=','0');
                    break;
                case 'rejected':
                    $records = $records->where('order_status','=','1');
                    break;
                case 'cancelled':
                    $records = $records->where('order_status','=','3');
                    break;
            }
        }
        // if(isset($selected_date) && !empty($selected_date)){
        //     $where_conditions[] = array('registrationDate', '>=', $selected_date .' 00:00:00');
        //     $where_conditions[] = array('registrationDate', '<=', $selected_date .' 23:59:59');
        // }
        if(count($where_conditions) > 0){
            $records = $records->where($where_conditions);
        }
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $records = $records->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }

        // DB::enableQueryLog();
        $no_of_records = 0;
        if(!$flag_export_data){
            // calculating number of records without using LIMIT & OFFSET for pagination, only when showing data inside DataTable
            try{
                $no_of_records = $records->pluck('ARN')->count();
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
            // retrieving logged in user role and permission details
            if(!isset($logged_in_user_roles_and_permissions)){
                $logged_in_user_roles_and_permissions = array();
            }
            if(!isset($flag_have_all_permissions)){
                $flag_have_all_permissions = false;
            }
		
            // $records = $records->toSql2();
			$records = $records->orderBy($order_by_field, $dir)->get()->toArray();
			if (count($records) == 1) {
				if (empty($records[0]->ARN)) {
					$records = [];
				}
			}
			if ($view_to_be_loaded == 'day_wise_data') {
				$aggregationResult = []; 
				foreach ($records as $record) {
					//if($record->sip_status=='Live SIP'){
							$client_id = $record->client_id;
							$start_date = $record->start_date;
							$scheme_code = $record->accord_scheme_code;
							
							$aggregationResult = []; 
							$aggregationResult = DB::connection('mongodb')
						->collection('mf_aum_nav_data')
						->where('client', (string)$client_id)
						->where('date_string', $start_date)
						->where('scheme_details.scheme_code', (string)$scheme_code)
						->get()->toArray();
						if(isset($aggregationResult[0]['scheme'][$scheme_code])){
							$record->client_aum = $aggregationResult[0]['scheme'][$scheme_code];
						}else{
							$record->client_aum = '0';
						}
					//}
					
				}
				
			}
            // x(DB::getQueryLog());
            if(!empty($records)){                
                foreach($records as $key => $value){
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        switch($view_to_be_loaded){
                            case 'month_wise_data':
                                $value->action  = "<a href='javascript:void(0);' title='View Daywise data' style='display:flex;align-items:center;' onclick=\"load_sip_analytics_datatable('day_wise_data', '". $asset_type ."', '". $value->sip_registration_month ."')\"><img src='". env('APP_URL') ."/images/view-icon.png' style='margin-right:7px;' title='View Daywise data'>View</a>";
                            break;
                            case 'day_wise_data':
                                $value->action  = "";
                                // $value->action  = "<a href='javascript:void(0);' title='View Datewise data' style='display:flex;align-items:center;' onclick=\"load_aum_transaction_datatable('date_wise_data', '". $value->sip_registration_month ."', '". $value->trdt_day ."')\"><img src='". env('APP_URL') ."/images/view-icon.png' style='margin-right:7px;' title='View Datewise data'>View</a>";
                            break;
                            case 'date_wise_data':
                                $value->action  = "";
                            break;
                            default:
                                $value->action  = "<button type='button' class='btn btn-outline-primary' onclick=\"load_sip_analytics_datatable('month_wise_data', '". $value->asset_type ."')\" style='display:block;margin-bottom:10px;'>View monthwise data</button>";
                                $value->action .= "<button type='button' class='btn btn-outline-primary' onclick=\"load_sip_analytics_datatable('day_wise_data', '". $value->asset_type ."', '')\">View sip register</button>";
                        }

 
                    }
                    if(isset($value->sip_registration_month) && !empty($value->sip_registration_month)){
                        $current_year = date("Y");
                        $month_text = str_pad($value->sip_registration_month, 2, 0, STR_PAD_LEFT);
                        $month_text = date('M', strtotime($current_year.'-'. $month_text .'-01'));
                        $value->sip_registration_month = $month_text;
                    }
                    unset($key, $value);
                }
                unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions);
            }
        }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        // x(DB::getQueryLog());
        unset($where_conditions, $where_in_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getClientAnalytics($input_arr = array()){
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

        if(!isset($view_to_be_loaded) || (isset($view_to_be_loaded) && empty($view_to_be_loaded))){
            $view_to_be_loaded = 'year_wise_data';
        }

        if(!isset($client_year) || (isset($client_year) && empty($client_year))){
            $client_year = date('Y');
        }

        if(!isset($scheme_filter)){
            $scheme_filter = '';
        }

        $where_conditions = array();
        $where_in_conditions = array();
        // $columns variable have list of all Table Headings/ Column names associated against the datatable
        if(isset($columns) && is_array($columns) && count($columns) > 0){
			foreach ($columns as $key => $value) {
				// Sanitize the search value
				if (isset($value['search']['value'])) {
					$searchValue = trim($value['search']['value']);
				} else {
					$searchValue = '';
				}
			
				switch ($value['data']) {
					case 'start_datetime':
					case 'end_datetime':
						if (!empty($searchValue) && strpos($searchValue, ';') !== false) {
							$searchingDates = explode(';', $searchValue);
							if (count($searchingDates) === 2 && strtotime($searchingDates[0]) !== false && strtotime($searchingDates[1]) !== false) {
								$where_conditions[] = ['a.' . $value['data'], '>=', $searchingDates[0] . ' 00:00:00'];
								$where_conditions[] = ['a.' . $value['data'], '<=', $searchingDates[1] . ' 23:59:59'];
							} else {
								if (!empty($searchingDates[0]) && strtotime($searchingDates[0]) !== false) {
									$where_conditions[] = ['a.' . $value['data'], '>=', $searchingDates[0] . ' 00:00:00'];
								}
								if (!empty($searchingDates[1]) && strtotime($searchingDates[1]) !== false) {
									$where_conditions[] = ['a.' . $value['data'], '<=', $searchingDates[1] . ' 23:59:59'];
								}
							}
						}
						break;
					case 'ARN':
					case 'clientname':
					case 'pan':
						if ($value['data'] === 'ARN' && isset($exact_arn_match) && intval($exact_arn_match) === 1) {
							$where_conditions[] = ['b.ARN', '=', $searchValue];
							$ARN = $searchValue;
						} else {
							$where_conditions[] = ['a.'.$value['data'], 'like', '%' . $searchValue . '%'];
						}
			
						if ($value['data'] === 'clientname') {
							$value['data'] = 'clientname';
						}
			
						if ($value['data'] === 'ARN' && isset($exact_arn_match) && intval($exact_arn_match) === 1) {
							$input_arr['get_list_of_assigned_arn'] = 1;
							$retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
			
							if (!$retrieve_users_data['flag_show_all_arn_data']) {
								if (isset($retrieve_users_data['list_of_assigned_arn']) && is_array($retrieve_users_data['list_of_assigned_arn'])) {
									if (!in_array($searchValue, $retrieve_users_data['list_of_assigned_arn'])) {
										$searchValue = -1;
									}
								} else {
									$searchValue = -1;
								}
							}
							unset($retrieve_users_data);
			
							$arn_search = $searchValue;
						}
						break;
					default:
						if (!empty($searchValue)) {
							$where_conditions[] = [$value['data'], '=', $searchValue];
						}
				}
			}
			
            unset($key, $value);
        }

        $order_by_clause = '';
        // $order is the POST variable sent by Datatable plugin. We are taking on single column ordering that's why considering $order[0] element only.
        $order_by_field = "agentcode";
        if(isset($order[0]) && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
            if($columns[$order[0]['column']]['data'] == 'total_netflow'){
                // $order_by_field = DB::raw('(total_gross_inflow - total_redemptions)');
                $order_by_field = 'total_netflow';
            }
            elseif($columns[$order[0]['column']]['data'] == 'total_aum'){
                // $order_by_field = DB::raw('((purchased_units - redeemed_units) * latest_nav)');
                $order_by_field = 'total_aum';
            }
            else{
                $order_by_field = $columns[$order[0]['column']]['data'];
            }
        }
        
        $dir = "ASC";
        if(isset($order[0]) && isset($order[0]['dir'])) {
            $dir = $order[0]['dir'];
        }
		$arr_select_fields = array(
			(!empty($asset_type_filter) && $asset_type_filter)?"asset_type":DB::connection("rankmf")->raw("'' AS asset_type"),
			"agentcode AS agent_code",
			"b.ARN",
			"a.client_code",
			DB::connection("rankmf")->raw("COUNT(DISTINCT (a.pan)) AS no_of_clients"),
            DB::connection("rankmf")->raw("YEAR(trxn_date) AS trdt_year"),
            DB::connection("rankmf")->raw("SUM(CASE WHEN trxn_type_name IN ('REDEMPTION', 'DIVIDEND PAYOUT', 'SWITCH OUT', 'TRANFER OUT') AND sub_trxntype_name = '' THEN ROUND(amount, 2) ELSE 0 END) AS total_redemptions"),
            DB::connection("rankmf")->raw("SUM(CASE WHEN trxn_type_name IN ('PURCHASE', 'DIVIDEND REINVESTMENT', 'SWITCH IN', 'TRANSFER IN', 'NFO') THEN ROUND(amount, 2) ELSE 0 END) AS total_gross_inflow"),
            DB::connection("rankmf")->raw("SUM(CASE WHEN trxn_type_name IN ('PURCHASE', 'DIVIDEND REINVESTMENT', 'SWITCH IN', 'TRANSFER IN', 'NFO') THEN ROUND(amount, 2) ELSE 0 END) - SUM(CASE WHEN trxn_type_name IN ('REDEMPTION', 'DIVIDEND PAYOUT', 'SWITCH OUT', 'TRANFER OUT') AND sub_trxntype_name = '' THEN ROUND(amount, 2) ELSE 0 END) AS total_netflow"),
            DB::connection("rankmf")->raw("'' AS total_aum")
        );
        // $arr_groupby_fields = array('agent_code');
        // if(!empty($scheme_filter) && $scheme_filter){
        //     $arr_groupby_fields[] = 'scheme_code';
        // }
		$arr_groupby_fields = array('trdt_year');
        if(!empty($asset_type_filter)){
			
            $arr_groupby_fields[] = 'asset_type';
        }

        // preparing SELECT & GROUP BY CLAUSE based on specific conditions
        switch($view_to_be_loaded){
            case 'month_wise_data':
			$arn_number = $ARN;

			if($client_year == date('Y')){
				$end = date("Y-m-d")." 23:59:59";
			}
			else{
				$end = $client_year."-12-31";
			}
			$asset_type_string = strtolower($asset_type);
			if(empty($asset_type_string)){
				$asset_type_string = 'total_aum';
			}
			$end_date = new \MongoDB\BSON\UTCDateTime(strtotime($end) * 1000);
			if($asset_type_filter == 1){
				$match = [
					'date' => [
						'$lte' => $end_date,
					],
					'arn' => $arn_number,
					//'total_aum' => ['$gt' => 0],
					'client' => ['$ne' => '']
				];
				
				$group = [
					'_id' => [
						'year' => ['$year' => '$date'],
						'month' => ['$month' => '$date'],
						'client' => '$client',
					],
					'total_aum' => ['$last' => '$total_aum'],
					"$asset_type_string" => ['$last' => "$$asset_type_string"],
					'date' => ['$last' => '$date'],
				];
				
				$project = [
					'_id' => 0,
					'year' => '$_id.year',
					'month' => '$_id.month',
					'client' => '$_id.client',
					'total_aum' => 1,
					"$asset_type_string" => 1,
					'date' => 1
				];
				
				$sort = [
					'date' => 1,
				];

				// Build the aggregation pipeline
				$pipeline = [
					['$match' => $match],
					['$group' => $group],
					['$sort' => $sort],
					['$project' => $project],
				];
			}else{
				$match = [
					'date' => [
						'$lte' => $end_date,
					],
					'arn' => $arn_number,
					//'total_aum' => ['$gt' => 0],
					'client' => ['$ne' => '']
				];
				
				$group = [
					'_id' => [
						'year' => ['$year' => '$date'],
						'month' => ['$month' => '$date'],
						'client' => '$client',
					],
					'total_aum' => ['$last' => '$total_aum'],
					'date' => ['$last' => '$date'],
				];
				
				$project = [
					'_id' => 0,
					'year' => '$_id.year',
					'month' => '$_id.month',
					'client' => '$_id.client',
					'total_aum' => 1,
					'date' => 1
				];
				
				$sort = [
					'date' => 1,
				];

				// Build the aggregation pipeline
				$pipeline = [
					['$match' => $match],
					['$group' => $group],
					['$sort' => $sort],
					['$project' => $project],
				];
			}
				
				$allowDiskUse = ['allowDiskUse' => true];
				
				// Execute the aggregation using the Query Builder
				$aum_result = DB::connection('mongodb')->collection('mf_aum_nav_data')->raw(function ($collection) use ($pipeline, $allowDiskUse) {
					return $collection->aggregate($pipeline, $allowDiskUse);
				})->toArray();
				// x($aum_result);
				$year = $client_year;
				$month_arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

				$month_wise_aum_data = $client_without_aum_data = [];
				$previous_year = $year - 1;

				foreach ($aum_result as $document) {
					$date_array = (array) $document['date'];
					$date_strtotime = ($date_array['milliseconds'] / 1000);
					$trdt_date = date('Y-m-d', $date_strtotime);
					/*if($asset_type_filter == 0 || $asset_type_filter == ''){
						$asset_type_string = "total_aum";
					}*/

					$aum_amount = (!empty($document[$asset_type_string]) ? $document[$asset_type_string] : 0);

					if($aum_amount > 0){
						$month_wise_aum_data[date('Y-m', strtotime($trdt_date))][] = [
							"$asset_type_string" => $aum_amount,
							'date' => $trdt_date,
							'year' => $document['year'],
							'month' => (strlen($document['month']) < 2 ? '0' : '' ).$document['month'],
							'client' => $document['client'],
						];
					}
					else{
						$client_without_aum_data[date('Y-m', strtotime($trdt_date))][] = [
							"$asset_type_string" => $aum_amount,
							'date' => $trdt_date,
							'year' => $document['year'],
							'month' => (strlen($document['month']) < 2 ? '0' : '' ).$document['month'],
							'client' => $document['client'],
						];
					}
				}
				foreach($client_without_aum_data as $non_aum_date => $non_aum_data){
					foreach($month_wise_aum_data as $aum_date => $aum_data){
						if(strtotime($aum_date) <= strtotime($non_aum_date)){
							foreach($non_aum_data as $key => $result){
								$key = array_search($result['client'], array_column($aum_data, 'client'));
								//y($key, 'key ========> ');
								if($key != ''){
									unset($month_wise_aum_data[$aum_date][$key]);
									//y('array deleted ========> ');
								}
							}
						}
					}
				}

				unset($aum_result);

				$new_aum_clients = $overall_aum_clients = $client_without_aum = [];

				if(!empty($month_wise_aum_data) && count($month_wise_aum_data) > 0){
					if(!empty($client_register_month)){
						$month_arr = [$client_register_month];
					}else{
						
						// $month_arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
						$client_year = $client_year; // Replace with your variable
						$currentYear = date('Y'); // Get the current year
						$currentMonth = date('n'); // Get the current month
	
						if ($client_year == $currentYear) {
							$month_arr = [$currentMonth];
						} else {
							$month_arr = [12]; // Set to December (last month) if it's not the current year
						}
	
					}

					//Overall Month Wise AUM
					$aum_year = $client_year;
					foreach($month_arr as $key => $month){
						$aum_month = $month;
						$aum_month = str_pad($aum_month, 2, "0", STR_PAD_LEFT);

						if($aum_year == date('Y') && $aum_month > date('m')){
							break;
						}

						$created_date = $aum_year."-".$aum_month;
						$created_date = date('Y-m', strtotime($created_date));

						$overall_aum_clients[date('Y-m', strtotime($created_date))]['count'] = 0;
						$overall_aum_clients[date('Y-m', strtotime($created_date))]['data'] = $overall_aum_clients[date('Y-m', strtotime($created_date))]['client_id'] = [];
					
						foreach($month_wise_aum_data as $aum_date => $aum_data){
							if(strtotime($aum_date) <= strtotime($created_date)){
								foreach($aum_data as $aum_detail){
									
									$aum_amount = (!empty($aum_detail[$asset_type_string]) ? $aum_detail[$asset_type_string] : 0);

									if($aum_amount <= 0){
										continue;
									}

									$overall_aum_clients[date('Y-m', strtotime($created_date))]['count'] = $overall_aum_clients[date('Y-m', strtotime($created_date))]['count'] + count($aum_detail);

									$overall_aum_clients[date('Y-m', strtotime($created_date))]['data'][] = $aum_detail;

									$client_ids = $overall_aum_clients[date('Y-m', strtotime($created_date))]['client_id'];

									/*$non_aum_client_arr =[];                                        
									if(!empty($client_without_aum_data[date('Y-m', strtotime($created_date))])){
										$non_aum_client_arr = array_unique(array_column($client_without_aum_data[date('Y-m', strtotime($created_date))], 'client'));	
									}*/

									//y($non_aum_client_arr, 'non_aum_client_arr ======>  ');

									if(!in_array($aum_detail['client'], $client_ids)) {
										$overall_aum_clients[date('Y-m', strtotime($created_date))]['client_id'][] = $aum_detail['client'];
									}

									/*if(!in_array($aum_detail['client'], $client_ids)) {
										if(!empty($non_aum_client_arr) 
											&& count($non_aum_client_arr) > 0 ){
											if(!in_array($aum_detail['client'], $non_aum_client_arr )){
											   $overall_aum_clients[date('Y-m', strtotime($created_date))]['client_id'][] = $aum_detail['client'];
											}
										}
										else{
											$overall_aum_clients[date('Y-m', strtotime($created_date))]['client_id'][] = $aum_detail['client'];
											$overall_aum_clients[date('Y-m', strtotime($created_date))]['data'][] = $aum_detail;
										}
									}*/
								}
							}
						}
					}

					// New Client AUM
					$aum_year = $client_year;
					foreach($month_arr as $key => $month){
						$previous_year = $aum_month = 0;
						$previous_aum_data = $previous_aum_clients = $aum_data = $aum_clients = [];
						$aum_month = $month - 1;

						if($aum_year == date('Y') && $month > date('m')){
							break;
						}

						if(strlen($aum_month) < 2 && $aum_month > 0){
							$aum_month = "0".$aum_month;
						}

						if(strlen($month) < 2 && $month > 0){
							$month = "0".$month;
						}

						$new_aum_clients[$aum_year."-".$month]['count'] = 0;
						$new_aum_clients[$aum_year."-".$month]['data'] = $new_aum_clients[$aum_year."-".$month]['client_id'] = [];
						
						$client_without_aum[$aum_year."-".$month]['count'] = 0;
						$client_without_aum[$aum_year."-".$month]['data'] = $client_without_aum[$aum_year."-".$month]['client_id'] = [];

						if(!empty($month_wise_aum_data[$aum_year."-".$month]) && count($month_wise_aum_data[$aum_year."-".$month]) > 0){
							$aum_data = $month_wise_aum_data[$aum_year."-".$month];

							$aum_clients = array_unique(array_column($month_wise_aum_data[$aum_year."-".$month], 'client'));
						}

						// Previous Month & Previous Year
						if($aum_month == 0){
							$aum_month = '12';
							$previous_year = date('Y', strtotime($client_year)) - 1;

							if(!empty($month_wise_aum_data[$previous_year."-".$aum_month]) && count($month_wise_aum_data[$previous_year."-".$aum_month]) > 0){
								$previous_aum_data = $month_wise_aum_data[$previous_year."-".$aum_month];
								
								$previous_aum_clients = array_unique(array_column($month_wise_aum_data[$previous_year."-".$aum_month], 'client'));
							}
						}
						else{
							// Previous Month & Current Year
							if(!empty($month_wise_aum_data[$aum_year."-".$aum_month]) && count($month_wise_aum_data[$aum_year."-".$aum_month]) > 0){
								$previous_aum_data = $month_wise_aum_data[$aum_year."-".$aum_month];

								$previous_aum_clients = array_unique(array_column($month_wise_aum_data[$aum_year."-".$aum_month], 'client'));
							}
						}

						$client_aum_diff_arr = array_diff($aum_clients, $previous_aum_clients);
						
						$new_aum_clients[$aum_year."-".$month]['client_id'] = array_values($client_aum_diff_arr);
						foreach($client_aum_diff_arr as $client){
							$new_client_aum_data = array_filter($aum_data, function ($var) use ($client) {
								return ($var['client'] == $client);
							});

							if(!empty($new_client_aum_data) 
								&& count($new_client_aum_data) > 0){
								$new_aum_clients[$aum_year."-".$month]['data'][] = array_values($new_client_aum_data)[0];
							}
						}

						$new_aum_clients[$aum_year."-".$month]['count'] = count($new_aum_clients[$aum_year."-".$month]['data']);

						//Client Without AUM
						if(!empty($client_without_aum_data[$aum_year."-".$month]) && count($client_without_aum_data[$aum_year."-".$month]) > 0){
							$client_without_aum[$aum_year."-".$month]['count'] = count($client_without_aum_data[$aum_year."-".$month]);

							$client_without_aum[$aum_year."-".$month]['data'] = $client_without_aum_data[$aum_year."-".$month];

							$client_without_aum[$aum_year."-".$month]['client_id'] = array_unique(array_column($client_without_aum_data[$aum_year."-".$month], 'client'));
						}
					}
				}
				
				// y($overall_aum_clients, 'overall_aum_clients ========> ');
				// y($new_aum_clients, 'new_aum_clients ========> ');
				// y($client_without_aum, 'client_without_aum ========> ');

				// die;
				$aum_year = $client_year;
				foreach($month_arr as $key => $month){
					$previous_year = 0;
					$aum_month = $month - 1;

					if($aum_year == date('Y') && $month > date('m')){
						break;
					}

					if(strlen($month) < 2 && $month > 0){
						$month = "0".$month;
					}
					
					if(strlen($aum_month) < 2 && $aum_month > 0){
						$aum_month = "0".$aum_month;
					}
					
					/*$client_analytics_detail[$aum_year."-".$month]['overall_client_with_aum'] = 0;
					$client_analytics_detail[$aum_year."-".$month]['new_clients_with_aum'] = 0;
					$client_analytics_detail[$aum_year."-".$month]['client_without_aum'] = 0;*/

					if($aum_month == 0){
						$aum_month = '12';
						$previous_year = $aum_year - 1;

						if(!empty($overall_aum_clients[$previous_year."-".$aum_month]['client_id'])){
						 $overall_aum_clients[$previous_year."-".$aum_month]['client_id'];
						}

						if(!empty($client_without_aum[$previous_year."-".$aum_month]['client_id'])){
							$client_without_aum[$previous_year."-".$aum_month]['client_id'];
						}
					}
					else{
						if(!empty($overall_aum_clients[$aum_year."-".$aum_month]['client_id'])){
							$overall_aum_clients[$aum_year."-".$aum_month]['client_id'];
						}

						if(!empty($client_without_aum[$aum_year."-".$aum_month]['client_id'])){
							$client_without_aum[$aum_year."-".$aum_month]['client_id'];
						}
					}

					if(!empty($new_aum_clients[$aum_year."-".$month]['client_id'])){
					$new_aum_clients[$aum_year."-".$month]['client_id'];
					}
				}
				// x($overall_aum_clients);
					if($types_of_client == 'active_clients_with_aum'){
						$aumdata=$overall_aum_clients[$aum_year."-".$month]['data'];
						$clients_id=$overall_aum_clients[$aum_year."-".$month]['client_id'];
					}else
					if($types_of_client == 'new_clients_with_aum'){
						$aumdata=$new_aum_clients[$aum_year."-".$month]['data'];
						$clients_id=$new_aum_clients[$aum_year."-".$month]['client_id'];
					}else
					if($types_of_client == 'clients_without_aum'){
						$aumdata=$client_without_aum[$aum_year."-".$month]['data'];
						$clients_id=$client_without_aum[$aum_year."-".$month]['client_id'];
					}else{
						$aumdata=$overall_aum_clients[$aum_year."-".$month]['data'];
						$clients_id=$overall_aum_clients[$aum_year."-".$month]['client_id'];
					}
                    $arr_select_fields = array( 
					'b.ARN',
					'a.client_code',
					(!empty($asset_type_filter) && $asset_type_filter)?'a.asset_type':DB::raw("'' AS asset_type"),
					'a.clientname',
					'a.pan',
					'a.amount as active_sip_registration_amount',
					'a.trxn_date',
					DB::raw('(SELECT DATE_FORMAT(MAX(trxn_date),\'%Y-%m-%d\') FROM mf_transaction_data WHERE client_code = a.client_code) AS last_transaction_date'),
					DB::raw('SUM(CASE WHEN trxn_type_name IN (\'REDEMPTION\', \'DIVIDEND PAYOUT\', \'SWITCH OUT\', \'TRANSFER OUT\') AND sub_trxntype_name = \'\' THEN ROUND(amount, 2) ELSE 0 END) as total_redemptions'),
					DB::raw('SUM(CASE WHEN trxn_type_name IN (\'PURCHASE\', \'DIVIDEND REINVESTMENT\', \'SWITCH IN\', \'TRANSFER IN\', \'NFO\') THEN ROUND(amount, 2) ELSE 0 END) as total_gross_inflow'),
					DB::raw('(SUM(CASE WHEN trxn_type_name IN (\'PURCHASE\', \'DIVIDEND REINVESTMENT\', \'SWITCH IN\', \'TRANSFER IN\', \'NFO\') THEN ROUND(amount, 2) ELSE 0 END) - SUM(CASE WHEN trxn_type_name IN (\'REDEMPTION\', \'DIVIDEND PAYOUT\', \'SWITCH OUT\', \'TRANSFER OUT\') AND sub_trxntype_name = \'\' THEN ROUND(amount, 2) ELSE 0 END)) as total_netflow'),
					DB::raw("'' as total_aum")
                                            );
				// $join_fields=1;					
               // $table_fields = DB::raw('samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final');
                $arr_groupby_fields = array('client_code');
                if(!empty($asset_type_filter) && $asset_type_filter){
                    $arr_groupby_fields[] = 'asset_type';
                }
                // }
                break;
        }
       // DB::enableQueryLog();
	   $records = DB::connection("rankmf")->table('mf_transaction_data as a')->select($arr_select_fields)
	   ->join('mutual_fund_partners.mfp_partner_registration as b','b.partner_code','=','a.agentcode');
        // if(isset($join_fields) && !empty($join_fields)){
        //     $records = $records->join(DB::raw(env('DB_DATABASE').".sip_analytics_view as c"), 'c.client_id', '=', 'a.client_code');
        // }
        // unset($join_fields);
        if(is_array($arr_groupby_fields) && count($arr_groupby_fields) > 0){
            $records = $records->groupBy($arr_groupby_fields);
        }
        unset($arr_select_fields, $arr_groupby_fields);
		if(isset($asset_type) && !empty($asset_type) && $asset_type_filter && $asset_type!='undefined'){
            $records = $records->where('a.asset_type','=',$asset_type);
        }
		if ($view_to_be_loaded != 'month_wise_data'){
			if (isset($client_year) && !empty($client_year)) {
				$records = $records->whereYear('trxn_date', '=', $client_year);
			}
		}
		
        $nav_to_be_retrieved_date = '';
        if(!isset($client_register_year) || (isset($client_register_year) && !is_numeric($client_register_year))){
            $nav_to_be_retrieved_date .= date('Y');
            $client_register_year = date('Y');
        }
        else{
            $nav_to_be_retrieved_date .= $client_register_year;
        }
		if($view_to_be_loaded == 'year_wise_data'){
			if(!isset($client_register_month) || (isset($client_register_month) && !is_numeric($client_register_month))){
				$nav_to_be_retrieved_date .= '-12';
			}
			else{
				$nav_to_be_retrieved_date .= '-'.str_pad($client_register_month, 2, 0, STR_PAD_LEFT);
			}
		}
        $nav_to_be_retrieved_date_prev_date = $nav_to_be_retrieved_date .'-01';
        $nav_to_be_retrieved_date = date('Y-m-t', strtotime($nav_to_be_retrieved_date .'-01'));

        if(isset($client_register_year) && !empty($client_register_year) && $view_to_be_loaded == 'month_wise_data'){
            $records = $records->whereYear('a.trxn_date','<=', $client_register_year);
        }

        if(isset($client_register_month) && !empty($client_register_month) && $view_to_be_loaded == 'month_wise_data'){
            $records = $records ->whereMonth('a.trxn_date','<=', $client_register_month);
        }
		if( $view_to_be_loaded == 'month_wise_data'){
			if(isset($types_of_client) && !empty($types_of_client)){
				$records = $records ->whereIn('a.client_code', $clients_id);
			}
		}
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
                $no_of_records = $records->pluck('asset_type')->count();
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
            // retrieving logged in user role and permission details
            if(!isset($logged_in_user_roles_and_permissions)){
                $logged_in_user_roles_and_permissions = array();
            }
            if(!isset($flag_have_all_permissions)){
                $flag_have_all_permissions = false;
            }
            $records = $records->orderBy($order_by_field,$dir)->get()->toArray();
			// $records = $records->orderBy($order_by_field,$dir)->toSql2();
			// x($records);
			if ($view_to_be_loaded == 'month_wise_data' && !empty($aumdata)) {
				if($asset_type_filter == 0 || $asset_type_filter == ''){
					$asset_type_string = "total_aum";
				}
				// Sort the $aumdata array by 'date' in descending order
				usort($aumdata, function ($a, $b) {
					return strtotime($b['date']) - strtotime($a['date']);
				});
				// Create an associative array with unique 'client' keys and 'total_aum' values
				$uniqueAum = [];
				foreach ($aumdata as $item) {
					$uniqueAum[$item['client']] = $item[$asset_type_string];
				}
			
				// Update $records with the 'total_aum' values
				foreach ($records as &$obj2) {
					$clientCode2 = $obj2->client_code;
					$obj2->total_aum = isset($uniqueAum[$clientCode2]) ? $uniqueAum[$clientCode2] : '';
				}
			}
			

			if(!empty($records) && count($records) > 0)
			{
				
				if($view_to_be_loaded == 'year_wise_data'){
					$arn_number = $ARN;
					if($client_year == date('Y')){
						$start = new \Datetime("-31 days");
						$start = $start->format('Y-m-d')." 00:00:00";
						$start_date = new \MongoDB\BSON\UTCDateTime(strtotime($start) * 1000);

						$end = new \Datetime("-1 days");
						$end = $end->format('Y-m-d')." 23:59:59";
						$end_date = new \MongoDB\BSON\UTCDateTime(strtotime($end) * 1000);
					}
					else{
						$start = $client_year."-12-01 00:00:00";
						$start_date = new \MongoDB\BSON\UTCDateTime(strtotime($start) * 1000);

						$end = $client_year."-12-31 23:59:59";
						$end_date = new \MongoDB\BSON\UTCDateTime(strtotime($end) * 1000);
					}
					if($asset_type_filter == 1){
						$match = [
							'$match' => [
								'date' => [
									'$gte' => $start_date,
									'$lt' => $end_date,
								],
								'arn' => $arn_number
							],
						];
						$group = [
							'$group' => [
								'_id' => [
									'year' => ['$year' => '$date'],
									'client' => '$client',
								],
								'total_aum' => ['$last' => '$total_aum'],
								'debt' => ['$last' => '$debt'],
								'equity' => ['$last' => '$equity'],
								'other' => ['$last' => '$other'],
								'hybrid' => ['$last' => '$hybrid'],
								'commodity' => ['$last' => '$commodity'],
							],
						];

						$project = [
							'$project' => [
								'_id' => 0,
								'year' => '$_id.year',
								'client' => '$_id.client',
								'equity' => 1,
								'debt'   => 1,
								'other' => 1,
								'equity' => 1,
								'total_aum' => 1,
							],
						];
					}
					else{
						$match = [
							'$match' => [
								'date' => [
									'$gte' => $start_date,
									'$lt' => $end_date,
								],
								'arn' => $arn_number
							],
						];
						
						$group = [
							'$group' => [
								'_id' => [
									'year' => ['$year' => '$date'],
									'client' => '$client',
								],
								'total_aum' => ['$last' => '$total_aum'],
							],
						];

						$project = [
							'$project' => [
								'_id' => 0,
								'year' => '$_id.year',
								'client' => '$_id.client',
								'total_aum' => 1,
							],
						];
					}
					
					$aum_result = DB::connection('mongodb')->collection('mf_aum_nav_data')->raw(function ($collection) use($start_date, $end_date, $arn_number, $match, $group, $project) {
						return $collection->aggregate([
							$match,
							$group,
							$project,
						]);
					})->toArray();
				}
			}
			if(!empty($aum_result) && count($aum_result) > 0 && is_array($aum_result)){
				if($view_to_be_loaded == 'year_wise_data'){
					if($asset_type_filter == 1){
						//Equity
						$total_aum_arr = array_column($aum_result, 'equity');
						$total_equity_aum = array_sum($total_aum_arr);

						//Debt
						$total_aum_arr = array_column($aum_result, 'debt');
						$total_debt_aum = array_sum($total_aum_arr);

						//Commodity
						$total_aum_arr = array_column($aum_result, 'commodity');
						$total_commodity_aum = array_sum($total_aum_arr);

						//Hybrid
						$total_aum_arr = array_column($aum_result, 'hybrid');
						$total_hybrid_aum = array_sum($total_aum_arr);

						//Other
						$total_aum_arr = array_column($aum_result, 'other');
						$total_other_aum = array_sum($total_aum_arr);
					}
					else{
						$total_aum_arr = array_column($aum_result, 'total_aum');
						$total_aum = array_sum($total_aum_arr);
					}
				}
			}	
         // $total_aum = array_sum(array_column($aum_result,'total_aum'));
			if(!empty($records)){
				foreach($records as $key => $value){
					if($flag_export_data){
						if(!empty($aum_result) && count($aum_result) > 0 && is_array($aum_result)){
							if($view_to_be_loaded == 'year_wise_data'){
							if($asset_type_filter == 1){
							if(strtolower($value->asset_type) == 'equity'){
								$value->total_aum = $total_equity_aum;
							}
							else if(strtolower($value->asset_type) == 'debt'){
								$value->total_aum = $total_debt_aum;
							}
							else if(strtolower($value->asset_type) == 'other'){
								$value->total_aum = $total_other_aum;
							}
							else if(strtolower($value->asset_type) == 'commodity'){
								$value->total_aum = $total_commodity_aum;
							}
							else if(strtolower($value->asset_type) == 'hybrid'){
								$value->total_aum = $total_hybrid_aum;
							}
							}
							else{
								$value->total_aum = $total_aum;
							}
						}
						}
					}
				}
			}
            if(!empty($records)){
                foreach($records as $key => $value){
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        switch($view_to_be_loaded){
                            case 'month_wise_data':
                                $value->action  = "";
                            break;
                            case 'day_wise_data':
                                $value->action  = "";
                            break;
                            case 'date_wise_data':
                                $value->action  = "";
                            break;
                            default:
                                $value->action  = "<button type='button' class='btn btn-outline-primary' onclick=\"load_client_monthwise_analytics_datatable('year_wise_data','". $value->asset_type ."')\" style='display:block;margin-bottom:10px;'>View monthwise data</button>";
                                $value->action .= "<button type='button' class='btn btn-outline-primary' onclick=\"load_client_analytics_datatable('month_wise_data','". $value->asset_type ."')\">View client register</button>";
								$value->total_aum = 0;
								if(!empty($aum_result) && count($aum_result) > 0 && is_array($aum_result)){
									if($view_to_be_loaded == 'year_wise_data'){
								if($asset_type_filter == 1){
                                if(strtolower($value->asset_type) == 'equity'){
                                    $value->total_aum = $total_equity_aum;
                                }
                                else if(strtolower($value->asset_type) == 'debt'){
                                    $value->total_aum = $total_debt_aum;
                                }
                                else if(strtolower($value->asset_type) == 'other'){
                                    $value->total_aum = $total_other_aum;
                                }
                                else if(strtolower($value->asset_type) == 'commodity'){
                                    $value->total_aum = $total_commodity_aum;
                                }
                                else if(strtolower($value->asset_type) == 'hybrid'){
                                    $value->total_aum = $total_hybrid_aum;
                                }
                            }
                            else{
                                $value->total_aum = $total_aum;
                            }
						}
					}

                        }
                    }
                    unset($key, $value);
                }
                unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions);
            }
        }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        // x(DB::getQueryLog());
        unset($where_conditions, $where_in_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getDaywiseTransactionAnalytics($input_arr = array()){
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
                    case 'start_datetime':
                    case 'end_datetime':
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
                    case 'agent_code':
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            $input_arr['get_list_of_assigned_arn'] = 1;
                            $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
                            if(!$retrieve_users_data['flag_show_all_arn_data']){
                                // as all ARN data should not be shown that's why assigning only supervised user list
                                // $where_in_conditions['a.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
                                if(isset($retrieve_users_data['list_of_assigned_arn']) && is_array($retrieve_users_data['list_of_assigned_arn'])){
                                    if(in_array($value['search']['value'], $retrieve_users_data['list_of_assigned_arn']) === FALSE){
                                        // seems that input ARN number is not assigned to the logged in user
                                        $value['search']['value'] = -1;
                                    }
                                }
                                else{
                                    // seems that logged in user don't have any distributors assigned, that's why not showing any details to them for the input ARN number
                                    $value['search']['value'] = -1;
                                }
                            }
                            unset($retrieve_users_data);
                            if($value['data'] == 'agent_code'){
                                $where_conditions[] = array('a.'.$value['data'], '=', $value['search']['value']);
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
        $order_by_field = "b.arn";
        if(isset($order[0]) && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
            if($columns[$order[0]['column']]['data'] == 'total_netflow'){
                $order_by_field = DB::raw('(total_gross_inflow - total_redemptions)');
                $order_by_clause = DB::raw('(total_gross_inflow - total_redemptions)');
            }
            elseif($columns[$order[0]['column']]['data'] == 'total_aum'){
                $order_by_field = 'total_aum';
                $order_by_clause = 'total_aum';
            }
            else{
                $order_by_field = $columns[$order[0]['column']]['data'];
                $order_by_clause = $columns[$order[0]['column']]['data'];
            }
        }
        else{
            $order_by_clause = 'b.ARN';
        }

        $dir = "ASC";
        if(isset($order[0]) && isset($order[0]['dir'])) {
            $dir = $order[0]['dir'];
        }
        $order_by_clause .= ' '. $dir;

        if(!isset($date_filter) || empty($date_filter)){
            $date_filter = date('Y-m-d');
        }
        $trdt_start = $date_filter .' 00:00:00';
        $trdt_end = $date_filter .' 23:59:59';

        $retrieved_nav_data = \App\Models\SchemeMasterModel::get_nav(array('nav_date' => $date_filter));
        $client_aum_query = "(SELECT 0 AS total_aum) AS total_aum";
        $nav_data_query = '';
        if(is_array($retrieved_nav_data) && count($retrieved_nav_data) > 0){
            $nav_data_query = array();
            foreach($retrieved_nav_data as $nav_scheme_code => $nav_data){
                $nav_data_query[] = "SELECT '". $nav_scheme_code ."' AS scheme_code, ". $nav_data['NAV'] ." AS NAV, '". $nav_data['NAV_Date'] ."' AS NAV_Date";
            }
            unset($nav_scheme_code, $nav_data);
            $nav_data_query = implode(' UNION ', $nav_data_query);
            $client_aum_query = "(SELECT ((SUM(CASE WHEN(t.purred = 'P') THEN IFNULL(t.units, 0) ELSE 0 END) - SUM(CASE WHEN(t.purred = 'R') THEN IFNULL(t.units, 0) ELSE 0 END)) * IFNULL(scheme.NAV, 0)) AS total_aum FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS t INNER JOIN (". $nav_data_query .") AS scheme ON (t.scheme_code = scheme.scheme_code) WHERE t.agent_code = a.agent_code AND t.scheme_code = a.scheme_code AND t.trdt <= '". $trdt_end ."') AS total_aum";
        }
        unset($retrieved_nav_data);

        $arr_select_fields = array('a.agent_code',
                                    DB::raw("b.arn_holders_name AS arn_holder_name"),
                                    'a.scheme_code',
                                    'a.meta_title AS scheme_name',
                                    'a.trdt',
                                    DB::raw("COUNT(DISTINCT a.pan) AS no_of_clients"),
                                    DB::raw("SUM(a.total_gross_inflow) AS total_gross_inflow"),
                                    DB::raw("SUM(a.ihno) AS total_purchases"),
                                    DB::raw("SUM(a.lumpsum_purchases) AS lumpsum_purchases"),
                                    DB::raw("SUM(a.sip_purchases) AS sip_purchases"),
                                    DB::raw("SUM(a.total_redemptions) AS total_redemptions"),
                                    DB::raw($client_aum_query),
                                );

        $table_fields = DB::raw("(SELECT agent_code,COUNT(DISTINCT ihno) AS ihno,trdt, kfintec_Postendorsement_TransactionDetails_final.scheme_code, scheme_master_details.meta_title, scheme_master_details.nav, lname AS client_name, kfintec_Postendorsement_TransactionDetails_final.pan, SUM(CASE WHEN(kfintec_Postendorsement_TransactionDetails_final.purred = 'P') THEN IFNULL(kfintec_Postendorsement_TransactionDetails_final.amt, 0) ELSE 0 END) AS total_gross_inflow, SUM(CASE WHEN(kfintec_Postendorsement_TransactionDetails_final.purred = 'R') THEN IFNULL(kfintec_Postendorsement_TransactionDetails_final.amt, 0) ELSE 0 END) AS total_redemptions, SUM(CASE WHEN(transaction_type.type_of_transaction = 'Lumpsum') THEN (CASE WHEN(kfintec_Postendorsement_TransactionDetails_final.purred = 'P') THEN IFNULL(kfintec_Postendorsement_TransactionDetails_final.amt, 0) ELSE 0 END) ELSE 0 END) AS lumpsum_purchases, SUM(CASE WHEN(transaction_type.type_of_transaction = 'SIP') THEN (CASE WHEN(kfintec_Postendorsement_TransactionDetails_final.purred = 'P') THEN IFNULL(kfintec_Postendorsement_TransactionDetails_final.amt, 0) ELSE 0 END) ELSE 0 END) AS sip_purchases, IFNULL(scheme_master_details.nav, 0) AS latest_nav, (SELECT transaction.trdt FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS transaction WHERE transaction.agent_code = kfintec_Postendorsement_TransactionDetails_final.agent_code AND transaction.pan = kfintec_Postendorsement_TransactionDetails_final.pan AND transaction.purred = 'P' ORDER BY transaction.trdt DESC LIMIT 0, 1) AS last_transaction_date 
        FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final 
        LEFT JOIN samcomf_investor_db.scheme_master_details ON (kfintec_Postendorsement_TransactionDetails_final.scheme_code = scheme_master_details.RTA_Scheme_Code) 
        INNER JOIN samcomf_investor_db.transaction_type ON (kfintec_Postendorsement_TransactionDetails_final.trtype = transaction_type.tm_trtype) 
        WHERE trdt >= '". $trdt_start ."' AND trdt <= '". $trdt_end ."' AND agent_code NOT IN ('0', '000000-0') AND agent_code IS NOT NULL 
        GROUP BY agent_code, kfintec_Postendorsement_TransactionDetails_final.pan 
        ORDER BY last_transaction_date DESC) AS a");
        $arr_groupby_fields = array('a.agent_code');
        if(isset($scheme_filter) && $scheme_filter){
            $arr_groupby_fields[] = 'a.scheme_code';
        }

        $records =  DB::table($table_fields)->select($arr_select_fields);
        $records = $records->leftJoin('samcomf.drm_distributor_master as b','a.agent_code','=','b.ARN');
        
        if(is_array($arr_groupby_fields) && count($arr_groupby_fields) > 0){
            $records = $records->groupBy($arr_groupby_fields);
        }
        unset($arr_select_fields, $arr_groupby_fields);

        if(count($where_conditions) > 0){
            $records = $records->where($where_conditions);
        }
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $records = $records->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }

        // DB::enableQueryLog();
        $no_of_records = 0;
        if(!isset($scheme_filter) || (isset($scheme_filter) && !$scheme_filter)){
            $records = DB::table($records, 'arn_wise_transactions')->select(array('agent_code', DB::raw("'' AS scheme_name"), 'arn_holder_name', DB::raw("SUM(no_of_clients) AS no_of_clients"), DB::raw("SUM(total_gross_inflow) AS total_gross_inflow"), DB::raw("SUM(total_purchases) AS total_purchases"), DB::raw("SUM(lumpsum_purchases) AS lumpsum_purchases"), DB::raw("SUM(sip_purchases) AS sip_purchases"), DB::raw("SUM(total_redemptions) AS total_redemptions"), DB::raw("SUM(total_aum) AS total_aum")))->groupBy('agent_code');
        }
        if(!$flag_export_data){
            // calculating number of records without using LIMIT & OFFSET for pagination, only when showing data inside DataTable
            try{
                // $no_of_records = $records->count();
                $no_of_records = $records->pluck('agent_code')->count();
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

            if(!$records->isEmpty()){                
                foreach($records as $key => $value){
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        $value->total_netflow = 0;
                        if(isset($value->total_gross_inflow) && !empty($value->total_gross_inflow) && isset($value->total_redemptions) && !empty($value->total_redemptions)){
                            $total_netflow =  $value->total_gross_inflow - $value->total_redemptions;
                            $value->total_netflow = $total_netflow ;
                        }
 
                    }
                    unset($key, $value);
                }
                unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions);
            }
        }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        // x(DB::getQueryLog());
        unset($where_conditions, $where_in_conditions, $order_by_clause, $client_aum_query);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getClientMonthwiseAnalytics($input_arr = array()){
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

        if(!isset($view_to_be_loaded) || (isset($view_to_be_loaded) && empty($view_to_be_loaded))){
            $view_to_be_loaded = 'year_wise_data';
        }

        if(!isset($client_year) || (isset($client_year) && empty($client_year))){
            $client_year = date('Y');
        }

        $where_conditions = array();
        $where_in_conditions = array();
        $searched_agent_code = '';
        $append_query = '';
        $append_select_clause = '';
        $append_join_conditions = '';

        $searched_scheme_code = addslashes(trim(strip_tags(($scheme_filter??''))));
        $group_by_scheme_code = '';
        // if(!empty($searched_scheme_code)){
        //     $group_by_scheme_code = ', scheme_code';
        //     $append_query .= " AND scheme_code = '". $searched_scheme_code ."'";
        //     $append_select_clause .= ", scheme_code";
        //     $append_join_conditions .= " AND current_data.scheme_code = prev_data.scheme_code";
        // }

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
                            if(isset($value['search']['value']) && !empty($value['search']['value'])){
                                $input_arr['get_list_of_assigned_arn'] = 1;
                                $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
                                if(!$retrieve_users_data['flag_show_all_arn_data']){
                                    // as all ARN data should not be shown that's why assigning only supervised user list
                                    // $where_in_conditions['a.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
                                    if(isset($retrieve_users_data['list_of_assigned_arn']) && is_array($retrieve_users_data['list_of_assigned_arn'])){
                                        if(in_array($value['search']['value'], $retrieve_users_data['list_of_assigned_arn']) === FALSE){
                                            // seems that input ARN number is not assigned to the logged in user
                                            $value['search']['value'] = -1;
                                        }
                                    }
                                    else{
                                        // seems that logged in user don't have any distributors assigned, that's why not showing any details to them for the input ARN number
                                        $value['search']['value'] = -1;
                                    }
                                }
                                unset($retrieve_users_data);

                                $searched_agent_code = $value['search']['value'];
                                if($value['data'] == 'ARN' && isset($exact_arn_match) && (intval($exact_arn_match) == 1)){
									$arn_data=$value['search']['value'];
                                    $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                                    // $arn_search = $value['search']['value'];
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
        try{
			//ini_set('max_execution_time', '1000'); 
			ini_set('memory_limit','-1');
            // retrieving logged in user role and permission details
            if(!isset($logged_in_user_roles_and_permissions)){
                $logged_in_user_roles_and_permissions = array();
            }
            if(!isset($flag_have_all_permissions)){
                $flag_have_all_permissions = false;
            }

			if($view_to_be_loaded == 'year_wise_data'){
				$arn_number = $arn_data;

				if($client_year == date('Y')){
					$end = date("Y-m-d")." 23:59:59";
				}
				else{
					$end = $client_year."-12-31";
				}
				$asset_type_string = strtolower($asset_type);

				if(empty($asset_type_string)){
					$asset_type_string = 'total_aum';
				}
				

				$end_date = new \MongoDB\BSON\UTCDateTime(strtotime($end) * 1000);
				if($asset_type_filter == 1){
					$match = [
						'date' => [
							'$lte' => $end_date,
						],
						'arn' => $arn_number,
                        //'total_aum' => ['$gt' => 0],
                        'client' => ['$ne' => '']
					];
					
					$group = [
						'_id' => [
							'year' => ['$year' => '$date'],
							'month' => ['$month' => '$date'],
							'client' => '$client',
						],
						'total_aum' => ['$last' => '$total_aum'],
						"$asset_type_string" => ['$last' => "$$asset_type_string"],
						'date' => ['$last' => '$date'],
					];
					
					$project = [
						'_id' => 0,
						'year' => '$_id.year',
						'month' => '$_id.month',
						'client' => '$_id.client',
						'total_aum' => 1,
						"$asset_type_string" => 1,
						'date' => 1
					];
					
					$sort = [
						'date' => 1,
					];

					// Build the aggregation pipeline
					$pipeline = [
						['$match' => $match],
						['$group' => $group],
						['$sort' => $sort],
						['$project' => $project],
					];
				}else{
					$match = [
						'date' => [
							'$lte' => $end_date,
						],
						'arn' => $arn_number,
                        //'total_aum' => ['$gt' => 0],
                        'client' => ['$ne' => '']
					];
					
					$group = [
						'_id' => [
							'year' => ['$year' => '$date'],
							'month' => ['$month' => '$date'],
							'client' => '$client',
						],
						'total_aum' => ['$last' => '$total_aum'],
						'date' => ['$last' => '$date'],
					];
					
					$project = [
						'_id' => 0,
						'year' => '$_id.year',
						'month' => '$_id.month',
						'client' => '$_id.client',
						'total_aum' => 1,
						'date' => 1
					];
					
					$sort = [
						'date' => 1,
					];

					// Build the aggregation pipeline
					$pipeline = [
						['$match' => $match],
						['$group' => $group],
						['$sort' => $sort],
						['$project' => $project],
						// ['$limit' => 3000],
					];
				}
					
                    $allowDiskUse = ['allowDiskUse' => true];
					
					// Execute the aggregation using the Query Builder
					$aum_result = DB::connection('mongodb')->collection('mf_aum_nav_data')->raw(function ($collection) use ($pipeline, $allowDiskUse) {
						return $collection->aggregate($pipeline, $allowDiskUse);
					})->toArray();
					// x($aum_result);
					$year = $client_year;
					$month_arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

                    $month_wise_aum_data = $client_without_aum_data = [];
                    $previous_year = $year - 1;

                    foreach ($aum_result as $document) {
                        $date_array = (array) $document['date'];
                        $date_strtotime = ($date_array['milliseconds'] / 1000);
                        $trdt_date = date('Y-m-d', $date_strtotime);
						/*if($asset_type_filter == 0 || $asset_type_filter == ''){
							$asset_type_string = "total_aum";
						}*/

						$aum_amount = (!empty($document[$asset_type_string]) ? $document[$asset_type_string] : 0);

                        if($aum_amount > 0){
                            $month_wise_aum_data[date('Y-m', strtotime($trdt_date))][] = [
                                "$asset_type_string" => $aum_amount,
                                'date' => $trdt_date,
                                'year' => $document['year'],
                                'month' => (strlen($document['month']) < 2 ? '0' : '' ).$document['month'],
                                'client' => $document['client'],
                            ];
                        }
                        else{
                            $client_without_aum_data[date('Y-m', strtotime($trdt_date))][] = [
                                "$asset_type_string" => $aum_amount,
                                'date' => $trdt_date,
                                'year' => $document['year'],
                                'month' => (strlen($document['month']) < 2 ? '0' : '' ).$document['month'],
                                'client' => $document['client'],
                            ];
                        }
                    }

                    foreach($client_without_aum_data as $non_aum_date => $non_aum_data){
                        foreach($month_wise_aum_data as $aum_date => $aum_data){
                            if(strtotime($aum_date) <= strtotime($non_aum_date)){
                                foreach($non_aum_data as $key => $result){
                                    $key = array_search($result['client'], array_column($aum_data, 'client'));
                                    //y($key, 'key ========> ');
                                    if($key != ''){
                                        unset($month_wise_aum_data[$aum_date][$key]);
                                        //y('array deleted ========> ');
                                    }
                                }
                            }
                        }
                    }

                    unset($aum_result);
					
					//y($month_wise_aum_data, 'month_wise_aum_data ==================> ');
                    //x($client_without_aum_data, 'client_without_aum_data ==================> ');

                    $new_aum_clients = $overall_aum_clients = $client_without_aum = [];

                    if(!empty($month_wise_aum_data) && count($month_wise_aum_data) > 0){
                        $month_arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

                        //Overall Month Wise AUM
                        $aum_year = $client_year;
                        foreach($month_arr as $key => $month){
                            $aum_month = $month;
                            $aum_month = str_pad($aum_month, 2, "0", STR_PAD_LEFT);

                            if($aum_year == date('Y') && $aum_month > date('m')){
                                break;
                            }

                            $created_date = $aum_year."-".$aum_month;
                            $created_date = date('Y-m', strtotime($created_date));

                            $overall_aum_clients[date('Y-m', strtotime($created_date))]['count'] = 0;
                            $overall_aum_clients[date('Y-m', strtotime($created_date))]['data'] = $overall_aum_clients[date('Y-m', strtotime($created_date))]['client_id'] = [];
                        
                            foreach($month_wise_aum_data as $aum_date => $aum_data){
                                if(strtotime($aum_date) <= strtotime($created_date)){
                                    foreach($aum_data as $aum_detail){
										
										$aum_amount = (!empty($aum_detail[$asset_type_string]) ? $aum_detail[$asset_type_string] : 0);

										if($aum_amount <= 0){
											continue;
										}

                                        $overall_aum_clients[date('Y-m', strtotime($created_date))]['count'] = $overall_aum_clients[date('Y-m', strtotime($created_date))]['count'] + count($aum_detail);

                                        $overall_aum_clients[date('Y-m', strtotime($created_date))]['data'][] = $aum_detail;

                                        $client_ids = $overall_aum_clients[date('Y-m', strtotime($created_date))]['client_id'];

                                        /*$non_aum_client_arr =[];                                        
										if(!empty($client_without_aum_data[date('Y-m', strtotime($created_date))])){
											$non_aum_client_arr = array_unique(array_column($client_without_aum_data[date('Y-m', strtotime($created_date))], 'client'));	
										}*/

                                        //y($non_aum_client_arr, 'non_aum_client_arr ======>  ');

                                        if(!in_array($aum_detail['client'], $client_ids)) {
                                            $overall_aum_clients[date('Y-m', strtotime($created_date))]['client_id'][] = $aum_detail['client'];
                                        }

                                        /*if(!in_array($aum_detail['client'], $client_ids)) {
											if(!empty($non_aum_client_arr) 
                                                && count($non_aum_client_arr) > 0 ){
                                                if(!in_array($aum_detail['client'], $non_aum_client_arr )){
                                                   $overall_aum_clients[date('Y-m', strtotime($created_date))]['client_id'][] = $aum_detail['client'];
                                                }
											}
											else{
												$overall_aum_clients[date('Y-m', strtotime($created_date))]['client_id'][] = $aum_detail['client'];
                                                $overall_aum_clients[date('Y-m', strtotime($created_date))]['data'][] = $aum_detail;
											}
                                        }*/
                                    }
                                }
                            }
                        }

						//x($overall_aum_clients, 'overall_aum_clients ==================> ');

                        // New Client AUM
                        $aum_year = $client_year;
                        foreach($month_arr as $key => $month){
                            $previous_year = $aum_month = 0;
                            $previous_aum_data = $previous_aum_clients = $aum_data = $aum_clients = [];
                            $aum_month = $month - 1;

                            if($aum_year == date('Y') && $month > date('m')){
                                break;
                            }

                            if(strlen($aum_month) < 2 && $aum_month > 0){
                                $aum_month = "0".$aum_month;
                            }

                            if(strlen($month) < 2 && $month > 0){
                                $month = "0".$month;
                            }

                            $new_aum_clients[$aum_year."-".$month]['count'] = 0;
                            $new_aum_clients[$aum_year."-".$month]['data'] = $new_aum_clients[$aum_year."-".$month]['client_id'] = [];
                            
                            $client_without_aum[$aum_year."-".$month]['count'] = 0;
                            $client_without_aum[$aum_year."-".$month]['data'] = $client_without_aum[$aum_year."-".$month]['client_id'] = [];

                            if(!empty($month_wise_aum_data[$aum_year."-".$month]) && count($month_wise_aum_data[$aum_year."-".$month]) > 0){
                                $aum_data = $month_wise_aum_data[$aum_year."-".$month];

                                $aum_clients = array_unique(array_column($month_wise_aum_data[$aum_year."-".$month], 'client'));
                            }

                            // Previous Month & Previous Year
                            if($aum_month == 0){
                                $aum_month = '12';
                                $previous_year = date('Y', strtotime($client_year)) - 1;

                                if(!empty($month_wise_aum_data[$previous_year."-".$aum_month]) && count($month_wise_aum_data[$previous_year."-".$aum_month]) > 0){
                                    $previous_aum_data = $month_wise_aum_data[$previous_year."-".$aum_month];
                                    
                                    $previous_aum_clients = array_unique(array_column($month_wise_aum_data[$previous_year."-".$aum_month], 'client'));
                                }
                            }
                            else{
                                // Previous Month & Current Year
                                if(!empty($month_wise_aum_data[$aum_year."-".$aum_month]) && count($month_wise_aum_data[$aum_year."-".$aum_month]) > 0){
                                    $previous_aum_data = $month_wise_aum_data[$aum_year."-".$aum_month];
    
                                    $previous_aum_clients = array_unique(array_column($month_wise_aum_data[$aum_year."-".$aum_month], 'client'));
                                }
                            }

                            $client_aum_diff_arr = array_diff($aum_clients, $previous_aum_clients);
                            
                            $new_aum_clients[$aum_year."-".$month]['client_id'] = array_values($client_aum_diff_arr);
                            foreach($client_aum_diff_arr as $client){
                                $new_client_aum_data = array_filter($aum_data, function ($var) use ($client) {
                                    return ($var['client'] == $client);
                                });

                                if(!empty($new_client_aum_data) 
                                    && count($new_client_aum_data) > 0){
                                    $new_aum_clients[$aum_year."-".$month]['data'][] = array_values($new_client_aum_data)[0];
                                }
                            }

                            $new_aum_clients[$aum_year."-".$month]['count'] = count($new_aum_clients[$aum_year."-".$month]['data']);

                            //Client Without AUM
                            if(!empty($client_without_aum_data[$aum_year."-".$month]) && count($client_without_aum_data[$aum_year."-".$month]) > 0){
                                $client_without_aum[$aum_year."-".$month]['count'] = count($client_without_aum_data[$aum_year."-".$month]);

                                $client_without_aum[$aum_year."-".$month]['data'] = $client_without_aum_data[$aum_year."-".$month];

                                $client_without_aum[$aum_year."-".$month]['client_id'] = array_unique(array_column($client_without_aum_data[$aum_year."-".$month], 'client'));
                            }
                        }
                    }
                    
                    /*y($overall_aum_clients, 'overall_aum_clients ========> ');
                    y($new_aum_clients, 'new_aum_clients ========> ');
                    y($client_without_aum, 'client_without_aum ========> ');

					die;*/
		 			$client_analytics_detail = [];
					$aum_year = $client_year;
					foreach($month_arr as $key => $month){
						$previous_year = 0;
						$aum_month = $month - 1;

						if($aum_year == date('Y') && $month > date('m')){
							break;
						}

						if(strlen($month) < 2 && $month > 0){
							$month = "0".$month;
						}
						
						if(strlen($aum_month) < 2 && $aum_month > 0){
							$aum_month = "0".$aum_month;
						}
						
						/*$client_analytics_detail[$aum_year."-".$month]['overall_client_with_aum'] = 0;
						$client_analytics_detail[$aum_year."-".$month]['new_clients_with_aum'] = 0;
						$client_analytics_detail[$aum_year."-".$month]['client_without_aum'] = 0;*/

						if($aum_month == 0){
							$aum_month = '12';
							$previous_year = $aum_year - 1;

							if(!empty($overall_aum_clients[$previous_year."-".$aum_month]['client_id'])){
								$client_analytics_detail[$previous_year."-".$aum_month]['overall_client_with_aum'] = count($overall_aum_clients[$previous_year."-".$aum_month]['client_id']);
							}

							if(!empty($client_without_aum[$previous_year."-".$aum_month]['client_id'])){
								$client_analytics_detail[$previous_year."-".$aum_month]['client_without_aum'] = count($client_without_aum[$previous_year."-".$aum_month]['client_id']);
							}
						}
						else{
							if(!empty($overall_aum_clients[$aum_year."-".$aum_month]['client_id'])){
								$client_analytics_detail[$aum_year."-".$aum_month]['overall_client_with_aum'] = count($overall_aum_clients[$aum_year."-".$aum_month]['client_id']);
							}

							if(!empty($client_without_aum[$aum_year."-".$aum_month]['client_id'])){
								$client_analytics_detail[$aum_year."-".$aum_month]['client_without_aum'] = count($client_without_aum[$aum_year."-".$aum_month]['client_id']);
							}
						}

						if(!empty($new_aum_clients[$aum_year."-".$month]['client_id'])){
							$client_analytics_detail[$aum_year."-".$month]['new_clients_with_aum'] = count($new_aum_clients[$aum_year."-".$month]['client_id']);
						}

						
					}

					// y($overall_aum_clients, 'overall_aum_clients ===============> ');
					// die;
					//x($client_analytics_detail, 'client_analytics_detail ===================>');

					$client_aum_detail = [];
					foreach (array_keys($client_analytics_detail) as $yearMonth) {
						$monthAbbreviation = date("M", strtotime($yearMonth));
						$client_aum_detail[$yearMonth] = [
							'action' => '',
							'ARN' => $arn_number,
							'm1' => $monthAbbreviation,
							'asset_type' => $asset_type,
							'active_clients_with_aum' => isset($client_analytics_detail[$yearMonth]['overall_client_with_aum']) ? $client_analytics_detail[$yearMonth]['overall_client_with_aum'] : 0,
							'new_clients_with_aum' => isset($client_analytics_detail[$yearMonth]['new_clients_with_aum']) ? $client_analytics_detail[$yearMonth]['new_clients_with_aum'] : 0,
							'clients_without_aum' => isset($client_analytics_detail[$yearMonth]['client_without_aum']) ? $client_analytics_detail[$yearMonth]['client_without_aum'] : 0,
						];
					}
					$records=array_values($client_aum_detail);
					$no_of_records=count($client_aum_detail);
					$records = json_decode(json_encode($records), false);
			}
			
            // x(DB::getQueryLog());
            if(!empty($records)){                
                foreach($records as $key => $value){
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        switch($view_to_be_loaded){
                            case 'month_wise_data':
                                $value->action  = "";
                            break;
                            case 'day_wise_data':
                                $value->action  = "";
                            break;
                            case 'date_wise_data':
                                $value->action  = "";
                            break;
                            default:
                                // $value->action="";
                                // $value->action  = "<a href='javascript:void(0);' title='View Client Register data' style='display:flex;align-items:center;' onclick=\"load_client_analytics_datatable('month_wise_data','". $value->scheme_code ."')\"><img src='". env('APP_URL') ."/images/view-icon.png' style='margin-right:7px;' title='View CLient Register data'>View</a>";
                        } 
                        
                        if(isset($value->active_clients_with_aum) && !empty($value->active_clients_with_aum) && $value->active_clients_with_aum > 0){
                            $value->active_clients_with_aum  = "<a href='javascript:void(0);' title='View Client Register data' onclick=\"load_client_analytics_datatable('month_wise_data', '".$asset_type."', '". date('m',strtotime($value->m1)) ."','active_clients_with_aum')\">".$value->active_clients_with_aum."</a>";
                        }
    
                        if(isset($value->new_clients_with_aum) && !empty($value->new_clients_with_aum) && $value->new_clients_with_aum > 0){
                            $value->new_clients_with_aum  = "<a href='javascript:void(0);' title='View Client Register data' onclick=\"load_client_analytics_datatable('month_wise_data', '".$asset_type."', '". date('m',strtotime($value->m1)) ."','new_clients_with_aum')\">".$value->new_clients_with_aum."</a>";
                        }
    
                        if(isset($value->clients_without_aum) && !empty($value->clients_without_aum) && $value->clients_without_aum > 0){
                            $value->clients_without_aum  = "<a href='javascript:void(0);' title='View Client Register data' onclick=\"load_client_analytics_datatable('month_wise_data', '".$asset_type."', '". date('m',strtotime($value->m1)) ."','clients_without_aum')\">".$value->clients_without_aum."</a>";
                        }
                    }
                    if(isset($value->m1) && !empty($value->m1) && strtotime($value->m1) !== FALSE){
                        $value->m1 = date("M", strtotime($value->m1));
                    }
                    unset($key, $value);
                }
                unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions);
            }
        }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        // x(DB::getQueryLog());
        unset($where_conditions, $where_in_conditions, $order_by_clause, $append_query, $append_select_clause, $append_join_conditions, $group_by_scheme_code);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getBDMMonthwiseInflows($input_arr = array()){
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

        if(!isset($month_wise) && (isset($month_wise) && empty($month_wise))){
            $month_start_date = date('Y-m-01 00:00:00');
        }
        elseif(isset($month_wise) && !empty($month_wise) && strtotime($month_wise) !== FALSE){
            $month_start_date = date('Y-m-d 00:00:00', strtotime($month_wise));
        }
        $month_end_date = date('Y-m-t 23:59:59', strtotime($month_start_date));

        $financial_year_dates = get_financial_year_range(intval(date('Y', strtotime($month_start_date))), intval(date('m', strtotime($month_start_date))));

        $where_conditions = array();
        $where_in_conditions = array();
        $extra_query_conditions = array();

        $append_bdm_queries = '';
        $append_drm_distributor_queries = '';
        $append_transaction_tbl_queries = '';
        if(isset($selected_scheme) && !empty($selected_scheme)){
            $append_transaction_tbl_queries .= " AND a.scheme = :selected_scheme";
            $extra_query_conditions[':selected_scheme'] = $selected_scheme;
        }

        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList($input_arr);
        if(!$retrieve_users_data['flag_show_all_arn_data'] && isset($retrieve_users_data['show_data_for_users']) && is_array($retrieve_users_data['show_data_for_users']) && count($retrieve_users_data['show_data_for_users']) > 0){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $append_bdm_queries .= " AND bdm.id IN (:direct_relationship_user_id)";
            $append_drm_distributor_queries .= " AND d.direct_relationship_user_id IN (:direct_relationship_user_id)";
            $extra_query_conditions[':direct_relationship_user_id'] = implode(',', $retrieve_users_data['show_data_for_users']);
        }
        unset($retrieve_users_data);

        // $is_nfo_scheme helps to identify whether selected scheme is in NFO period or not.
        // if scheme is in NFO period then data needs to be considered from MySQL table: kfintechTableTransactionDetails instead of kfintec_Postendorsement_TransactionDetails_final
        if(!isset($is_nfo_scheme) || (isset($is_nfo_scheme) && $is_nfo_scheme != '1')){
            $is_nfo_scheme = 0;
        }
        $is_nfo_scheme = intval($is_nfo_scheme);

        // $columns variable have list of all Table Headings/ Column names associated against the datatable
        if(isset($columns) && is_array($columns) && count($columns) > 0){
            foreach($columns as $key => $value){
                // data key have field names for whom data needs to be searched
                if(isset($value['search']['value'])){
                    $value['search']['value'] = trim($value['search']['value']);        // removing leading & trailing spaces from the searched value
                }
                switch($value['data']){
                    case 'bdm_name':
                    case 'reporting_manager':
                        if($value['data'] == 'bdm_name'){
                            $value['data'] = 'bdm.name';
                        }
                        elseif($value['data'] == 'reporting_manager'){
                            $value['data'] = 'reporting.name';
                        }

                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                            // $append_bdm_queries .= " AND ". $value['data'] ." LIKE '%". $value['search']['value'] ."%'";
                            $append_bdm_queries .= " AND ". $value['data'] ." LIKE :". str_replace('.', '_', $value['data']);
                            $extra_query_conditions[':'. str_replace('.', '_', $value['data'])] = '%'. $value['search']['value'] .'%';
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
        $dir = "ASC";
        if(isset($order[0]) && isset($order[0]['dir'])) {
            $dir = $order[0]['dir'];
        }

        if(isset($order[0]) && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
            switch($columns[$order[0]['column']]['data']){
                case 'bdm_name':
                case 'reporting_manager':
                case 'number_of_arn_mapped':
                case 'number_of_arn_empanelled':
                case 'sip_gross_inflow_till_date':
                case 'otherthan_sip_gross_inflow_till_date':
                case 'total_gross_inflow_till_date':
                case 'gross_redemptions_till_date':
                case 'net_inflow_till_date':
                    $order_by_clause = 'ORDER BY '. $columns[$order[0]['column']]['data'] .' '. $dir;
                    break;
                default:
                    $order_by_clause = 'ORDER BY '. $columns[$order[0]['column']]['data'] .' '. $dir;
            }
        }

        if(empty($order_by_clause)){
            $order_by_clause = 'ORDER BY bdm.name ASC';
        }
        unset($dir);

        $query_record_limit_conditions = '';
        if(!$flag_export_data){
            // calculating number of records without using LIMIT & OFFSET for pagination, only when showing data inside DataTable

            if(isset($start) && is_numeric($start) && isset($length) && is_numeric($length)){
                $query_record_limit_conditions = ' LIMIT '. $start .', '. $length;
            }
        }

        // DB::enableQueryLog();
        // Query to get transaction inflows of selected month for BDM wise
        if($is_nfo_scheme == 1){
            $records = DB::select("SELECT bdm.email AS 'bdm_email', bdm.name AS 'bdm_name', reporting.name AS 'reporting_manager', COUNT(d.direct_relationship_user_id) AS 'number_of_arn_mapped', SUM(IFNULL(d.is_samcomf_partner, 0)) AS 'number_of_arn_empanelled', SUM(IFNULL(d.sip_gross_inflow_till_date, 0)) AS sip_gross_inflow_till_date, SUM(IFNULL(d.otherthan_sip_gross_inflow_till_date, 0)) AS otherthan_sip_gross_inflow_till_date, (SUM(IFNULL(d.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.otherthan_sip_gross_inflow_till_date, 0))) AS total_gross_inflow_till_date, SUM(IFNULL(d.gross_redemptions_till_date, 0)) AS gross_redemptions_till_date, (SUM(IFNULL(d.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.otherthan_sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.gross_redemptions_till_date, 0))) AS net_inflow_till_date, 0 AS 'net_inflow_financial_year_till_date', 0 AS 'net_inflow_current_quarter_till_date' FROM samcomf.users AS bdm INNER JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id AND reporting.status = 1) LEFT JOIN (SELECT d.ARN, d.direct_relationship_user_id, d.is_samcomf_partner, SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) AS sip_gross_inflow_till_date, SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0)) AS otherthan_sip_gross_inflow_till_date, (SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0))) AS total_gross_inflow_till_date, SUM(IFNULL(arn_inflows.gross_redemptions_till_date, 0)) AS gross_redemptions_till_date, (SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.gross_redemptions_till_date, 0))) AS net_inflow_till_date FROM samcomf.drm_distributor_master AS d LEFT JOIN (SELECT a.agent_code, SUM(CASE WHEN(t.type_of_transaction IN ('SIP') AND a.purred = 'P') THEN IFNULL(a.amt, 0) ELSE 0 END) AS sip_gross_inflow_till_date, SUM(CASE WHEN(t.type_of_transaction NOT IN ('SIP') AND a.purred = 'P') THEN IFNULL(a.amt, 0) ELSE 0 END) AS otherthan_sip_gross_inflow_till_date, SUM(CASE WHEN(a.purred = 'R') THEN (-1 * IFNULL(a.amt, 0)) ELSE 0 END) AS gross_redemptions_till_date FROM samcomf_investor_db.kfintechTableTransactionDetails AS a FORCE INDEX(idx_pre_endorsement_agent_code) INNER JOIN samcomf_investor_db.transaction_type AS t ON (a.trtype = t.tm_trtype) INNER JOIN samcomf_investor_db.scheme_master ON (CONCAT(TRIM(a.scheme), TRIM(a.pln)) = scheme_master.RTA_Scheme_Code) WHERE a.agent_code NOT IN ('0') AND scheme_master.Scheme_Plan IN ('Regular') AND scheme_master.Scheme_Type IN ('Equity') AND a.trdate BETWEEN :month_start_date AND :month_end_date ". $append_transaction_tbl_queries ." GROUP BY a.agent_code) AS arn_inflows ON (arn_inflows.agent_code = d.ARN) WHERE d.direct_relationship_user_id IS NOT NULL ". $append_drm_distributor_queries ." GROUP BY d.ARN) AS d ON (bdm.id = d.direct_relationship_user_id) WHERE bdm.is_drm_user = 1 AND bdm.status = 1 AND bdm_details.status = 1 AND bdm_details.skip_in_arn_mapping = 0 ". $append_bdm_queries ." GROUP BY bdm.id ". $order_by_clause ." ". $query_record_limit_conditions .";", array_merge(array(':month_start_date' => $month_start_date, ':month_end_date' => $month_end_date), $extra_query_conditions));
        }
        else{
            $records = DB::select("SELECT bdm.email AS 'bdm_email', bdm.name AS 'bdm_name', reporting.name AS 'reporting_manager', COUNT(d.direct_relationship_user_id) AS 'number_of_arn_mapped', SUM(IFNULL(d.is_samcomf_partner, 0)) AS 'number_of_arn_empanelled', SUM(IFNULL(d.sip_gross_inflow_till_date, 0)) AS sip_gross_inflow_till_date, SUM(IFNULL(d.otherthan_sip_gross_inflow_till_date, 0)) AS otherthan_sip_gross_inflow_till_date, (SUM(IFNULL(d.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.otherthan_sip_gross_inflow_till_date, 0))) AS total_gross_inflow_till_date, SUM(IFNULL(d.gross_redemptions_till_date, 0)) AS gross_redemptions_till_date, (SUM(IFNULL(d.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.otherthan_sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.gross_redemptions_till_date, 0))) AS net_inflow_till_date, 0 AS 'net_inflow_financial_year_till_date', 0 AS 'net_inflow_current_quarter_till_date' FROM samcomf.users AS bdm INNER JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id AND reporting.status = 1) LEFT JOIN (SELECT d.ARN, d.direct_relationship_user_id, d.is_samcomf_partner, SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) AS sip_gross_inflow_till_date, SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0)) AS otherthan_sip_gross_inflow_till_date, (SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0))) AS total_gross_inflow_till_date, SUM(IFNULL(arn_inflows.gross_redemptions_till_date, 0)) AS gross_redemptions_till_date, (SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.gross_redemptions_till_date, 0))) AS net_inflow_till_date FROM samcomf.drm_distributor_master AS d LEFT JOIN (SELECT a.agent_code, SUM(CASE WHEN(t.type_of_transaction IN ('SIP') AND a.purred = 'P') THEN IFNULL(a.amt, 0) ELSE 0 END) AS sip_gross_inflow_till_date, SUM(CASE WHEN(t.type_of_transaction NOT IN ('SIP') AND a.purred = 'P') THEN IFNULL(a.amt, 0) ELSE 0 END) AS otherthan_sip_gross_inflow_till_date, SUM(CASE WHEN(a.purred = 'R') THEN (-1 * IFNULL(a.amt, 0)) ELSE 0 END) AS gross_redemptions_till_date FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS a FORCE INDEX(idx_agent_code) INNER JOIN samcomf_investor_db.transaction_type AS t ON (a.trtype = t.tm_trtype) INNER JOIN samcomf_investor_db.scheme_master ON (a.scheme_code = scheme_master.RTA_Scheme_Code) WHERE a.agent_code NOT IN ('0') AND scheme_master.Scheme_Plan IN ('Regular') AND scheme_master.Scheme_Type IN ('Equity') AND a.trdt BETWEEN :month_start_date AND :month_end_date ". $append_transaction_tbl_queries ." GROUP BY a.agent_code) AS arn_inflows ON (arn_inflows.agent_code = d.ARN) WHERE d.direct_relationship_user_id IS NOT NULL ". $append_drm_distributor_queries ." GROUP BY d.ARN) AS d ON (bdm.id = d.direct_relationship_user_id) WHERE bdm.is_drm_user = 1 AND bdm.status = 1 AND bdm_details.status = 1 AND bdm_details.skip_in_arn_mapping = 0 ". $append_bdm_queries ." GROUP BY bdm.id ". $order_by_clause ." ". $query_record_limit_conditions .";", array_merge(array(':month_start_date' => $month_start_date, ':month_end_date' => $month_end_date), $extra_query_conditions));
        }
        // y($records, 'records');

        // Query to get transaction inflows of selected month financial year for BDM wise
        if($is_nfo_scheme == 1){
            $financial_year_records = DB::select("SELECT bdm.email AS 'bdm_email', (SUM(IFNULL(d.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.otherthan_sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.gross_redemptions_till_date, 0))) AS net_inflow_till_date FROM samcomf.users AS bdm INNER JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id AND reporting.status = 1) LEFT JOIN (SELECT d.ARN, d.direct_relationship_user_id, d.is_samcomf_partner, SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) AS sip_gross_inflow_till_date, SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0)) AS otherthan_sip_gross_inflow_till_date, (SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0))) AS total_gross_inflow_till_date, SUM(IFNULL(arn_inflows.gross_redemptions_till_date, 0)) AS gross_redemptions_till_date, (SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.gross_redemptions_till_date, 0))) AS net_inflow_till_date FROM samcomf.drm_distributor_master AS d LEFT JOIN (SELECT a.agent_code, SUM(CASE WHEN(t.type_of_transaction IN ('SIP') AND a.purred = 'P') THEN IFNULL(a.amt, 0) ELSE 0 END) AS sip_gross_inflow_till_date, SUM(CASE WHEN(t.type_of_transaction NOT IN ('SIP') AND a.purred = 'P') THEN IFNULL(a.amt, 0) ELSE 0 END) AS otherthan_sip_gross_inflow_till_date, SUM(CASE WHEN(a.purred = 'R') THEN (-1 * IFNULL(a.amt, 0)) ELSE 0 END) AS gross_redemptions_till_date FROM samcomf_investor_db.kfintechTableTransactionDetails AS a FORCE INDEX(idx_pre_endorsement_agent_code) INNER JOIN samcomf_investor_db.transaction_type AS t ON (a.trtype = t.tm_trtype) INNER JOIN samcomf_investor_db.scheme_master ON (CONCAT(TRIM(a.scheme), TRIM(a.pln)) = scheme_master.RTA_Scheme_Code) WHERE a.agent_code NOT IN ('0') AND scheme_master.Scheme_Plan IN ('Regular') AND scheme_master.Scheme_Type IN ('Equity') AND a.trdate BETWEEN :month_start_date AND :month_end_date". $append_transaction_tbl_queries ." GROUP BY a.agent_code) AS arn_inflows ON (arn_inflows.agent_code = d.ARN) WHERE d.direct_relationship_user_id IS NOT NULL ". $append_drm_distributor_queries ." GROUP BY d.ARN) AS d ON (bdm.id = d.direct_relationship_user_id) WHERE bdm.is_drm_user = 1 AND bdm.status = 1 AND bdm_details.status = 1 AND bdm_details.skip_in_arn_mapping = 0 ". $append_bdm_queries ." GROUP BY bdm.id;", array_merge(array(":month_start_date" => date('Y-m-d 00:00:00', strtotime($financial_year_dates['start_date'])), ":month_end_date" => date('Y-m-d 23:59:59', strtotime($financial_year_dates['end_date']))), $extra_query_conditions));
            $financial_year_records = array_column($financial_year_records, NULL, 'bdm_email');
        }
        else{
            $financial_year_records = DB::select("SELECT bdm.email AS 'bdm_email', (SUM(IFNULL(d.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.otherthan_sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.gross_redemptions_till_date, 0))) AS net_inflow_till_date FROM samcomf.users AS bdm INNER JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id AND reporting.status = 1) LEFT JOIN (SELECT d.ARN, d.direct_relationship_user_id, d.is_samcomf_partner, SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) AS sip_gross_inflow_till_date, SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0)) AS otherthan_sip_gross_inflow_till_date, (SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0))) AS total_gross_inflow_till_date, SUM(IFNULL(arn_inflows.gross_redemptions_till_date, 0)) AS gross_redemptions_till_date, (SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.gross_redemptions_till_date, 0))) AS net_inflow_till_date FROM samcomf.drm_distributor_master AS d LEFT JOIN (SELECT a.agent_code, SUM(CASE WHEN(t.type_of_transaction IN ('SIP') AND a.purred = 'P') THEN IFNULL(a.amt, 0) ELSE 0 END) AS sip_gross_inflow_till_date, SUM(CASE WHEN(t.type_of_transaction NOT IN ('SIP') AND a.purred = 'P') THEN IFNULL(a.amt, 0) ELSE 0 END) AS otherthan_sip_gross_inflow_till_date, SUM(CASE WHEN(a.purred = 'R') THEN (-1 * IFNULL(a.amt, 0)) ELSE 0 END) AS gross_redemptions_till_date FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS a FORCE INDEX(idx_agent_code) INNER JOIN samcomf_investor_db.transaction_type AS t ON (a.trtype = t.tm_trtype) INNER JOIN samcomf_investor_db.scheme_master ON (a.scheme_code = scheme_master.RTA_Scheme_Code) WHERE a.agent_code NOT IN ('0') AND scheme_master.Scheme_Plan IN ('Regular') AND scheme_master.Scheme_Type IN ('Equity') AND a.trdt BETWEEN :month_start_date AND :month_end_date". $append_transaction_tbl_queries ." GROUP BY a.agent_code) AS arn_inflows ON (arn_inflows.agent_code = d.ARN) WHERE d.direct_relationship_user_id IS NOT NULL ". $append_drm_distributor_queries ." GROUP BY d.ARN) AS d ON (bdm.id = d.direct_relationship_user_id) WHERE bdm.is_drm_user = 1 AND bdm.status = 1 AND bdm_details.status = 1 AND bdm_details.skip_in_arn_mapping = 0 ". $append_bdm_queries ." GROUP BY bdm.id;", array_merge(array(":month_start_date" => date('Y-m-d 00:00:00', strtotime($financial_year_dates['start_date'])), ":month_end_date" => date('Y-m-d 23:59:59', strtotime($financial_year_dates['end_date']))), $extra_query_conditions));
            $financial_year_records = array_column($financial_year_records, NULL, 'bdm_email');

        }
        // y($financial_year_records, 'financial_year_records');

        // Query to get transaction inflows of selected month current quarter for BDM wise
        if($is_nfo_scheme == 1){
            $quarterly_records = DB::select("SELECT bdm.email AS 'bdm_email', (SUM(IFNULL(d.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.otherthan_sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.gross_redemptions_till_date, 0))) AS net_inflow_till_date FROM samcomf.users AS bdm INNER JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id AND reporting.status = 1) LEFT JOIN (SELECT d.ARN, d.direct_relationship_user_id, d.is_samcomf_partner, SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) AS sip_gross_inflow_till_date, SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0)) AS otherthan_sip_gross_inflow_till_date, (SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0))) AS total_gross_inflow_till_date, SUM(IFNULL(arn_inflows.gross_redemptions_till_date, 0)) AS gross_redemptions_till_date, (SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.gross_redemptions_till_date, 0))) AS net_inflow_till_date FROM samcomf.drm_distributor_master AS d LEFT JOIN (SELECT a.agent_code, SUM(CASE WHEN(t.type_of_transaction IN ('SIP') AND a.purred = 'P') THEN IFNULL(a.amt, 0) ELSE 0 END) AS sip_gross_inflow_till_date, SUM(CASE WHEN(t.type_of_transaction NOT IN ('SIP') AND a.purred = 'P') THEN IFNULL(a.amt, 0) ELSE 0 END) AS otherthan_sip_gross_inflow_till_date, SUM(CASE WHEN(a.purred = 'R') THEN (-1 * IFNULL(a.amt, 0)) ELSE 0 END) AS gross_redemptions_till_date FROM samcomf_investor_db.kfintechTableTransactionDetails AS a FORCE INDEX(idx_pre_endorsement_agent_code) INNER JOIN samcomf_investor_db.transaction_type AS t ON (a.trtype = t.tm_trtype) INNER JOIN samcomf_investor_db.scheme_master ON (CONCAT(TRIM(a.scheme), TRIM(a.pln)) = scheme_master.RTA_Scheme_Code) WHERE a.agent_code NOT IN ('0') AND scheme_master.Scheme_Plan IN ('Regular') AND scheme_master.Scheme_Type IN ('Equity') AND a.trdate BETWEEN :month_start_date AND :month_end_date". $append_transaction_tbl_queries ." GROUP BY a.agent_code) AS arn_inflows ON (arn_inflows.agent_code = d.ARN) WHERE d.direct_relationship_user_id IS NOT NULL ". $append_drm_distributor_queries ." GROUP BY d.ARN) AS d ON (bdm.id = d.direct_relationship_user_id) WHERE bdm.is_drm_user = 1 AND bdm.status = 1 AND bdm_details.status = 1 AND bdm_details.skip_in_arn_mapping = 0 ". $append_bdm_queries ." GROUP BY bdm.id;", array_merge(array(":month_start_date" => date('Y-m-d 00:00:00', strtotime($financial_year_dates['quarter_start_date'])), ":month_end_date" => $month_end_date), $extra_query_conditions));
        }
        else{
            $quarterly_records = DB::select("SELECT bdm.email AS 'bdm_email', (SUM(IFNULL(d.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.otherthan_sip_gross_inflow_till_date, 0)) + SUM(IFNULL(d.gross_redemptions_till_date, 0))) AS net_inflow_till_date FROM samcomf.users AS bdm INNER JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id AND reporting.status = 1) LEFT JOIN (SELECT d.ARN, d.direct_relationship_user_id, d.is_samcomf_partner, SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) AS sip_gross_inflow_till_date, SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0)) AS otherthan_sip_gross_inflow_till_date, (SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0))) AS total_gross_inflow_till_date, SUM(IFNULL(arn_inflows.gross_redemptions_till_date, 0)) AS gross_redemptions_till_date, (SUM(IFNULL(arn_inflows.sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.otherthan_sip_gross_inflow_till_date, 0)) + SUM(IFNULL(arn_inflows.gross_redemptions_till_date, 0))) AS net_inflow_till_date FROM samcomf.drm_distributor_master AS d LEFT JOIN (SELECT a.agent_code, SUM(CASE WHEN(t.type_of_transaction IN ('SIP') AND a.purred = 'P') THEN IFNULL(a.amt, 0) ELSE 0 END) AS sip_gross_inflow_till_date, SUM(CASE WHEN(t.type_of_transaction NOT IN ('SIP') AND a.purred = 'P') THEN IFNULL(a.amt, 0) ELSE 0 END) AS otherthan_sip_gross_inflow_till_date, SUM(CASE WHEN(a.purred = 'R') THEN (-1 * IFNULL(a.amt, 0)) ELSE 0 END) AS gross_redemptions_till_date FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS a FORCE INDEX(idx_agent_code) INNER JOIN samcomf_investor_db.transaction_type AS t ON (a.trtype = t.tm_trtype) INNER JOIN samcomf_investor_db.scheme_master ON (a.scheme_code = scheme_master.RTA_Scheme_Code) WHERE a.agent_code NOT IN ('0') AND scheme_master.Scheme_Plan IN ('Regular') AND scheme_master.Scheme_Type IN ('Equity') AND a.trdt BETWEEN :month_start_date AND :month_end_date". $append_transaction_tbl_queries ." GROUP BY a.agent_code) AS arn_inflows ON (arn_inflows.agent_code = d.ARN) WHERE d.direct_relationship_user_id IS NOT NULL ". $append_drm_distributor_queries ." GROUP BY d.ARN) AS d ON (bdm.id = d.direct_relationship_user_id) WHERE bdm.is_drm_user = 1 AND bdm.status = 1 AND bdm_details.status = 1 AND bdm_details.skip_in_arn_mapping = 0 ". $append_bdm_queries ." GROUP BY bdm.id;", array_merge(array(":month_start_date" => date('Y-m-d 00:00:00', strtotime($financial_year_dates['quarter_start_date'])), ":month_end_date" => $month_end_date), $extra_query_conditions));
        }
        $quarterly_records = array_column($quarterly_records, NULL, 'bdm_email');
        // y($quarterly_records, 'quarterly_records');

        $no_of_records = 0;
        if(!$flag_export_data){
            // calculating number of records without using LIMIT & OFFSET for pagination, only when showing data inside DataTable
            try{
                $no_of_records = DB::table('users AS bdm')
                                    ->Join('users_details AS bdm_details', 'bdm.id', '=', 'bdm_details.user_id')
                                    ->leftJoin('users AS reporting', function($join){
                                        $join->on('bdm_details.reporting_to', '=', 'reporting.id')
                                             ->on('reporting.status', '=', DB::raw('1'));
                                    })
                                    ->select(DB::raw('COUNT(bdm.id) AS total'))
                                    ->where(
                                        array(
                                            array('bdm.is_drm_user', '=', 1),
                                            array('bdm.status', '=', 1),
                                            array('bdm_details.status', '=', 1),
                                            array('bdm_details.skip_in_arn_mapping', '=', 0),
                                        )
                                    );
                if(count($where_conditions) > 0){
                    $no_of_records = $no_of_records->where($where_conditions);
                }
                $no_of_records = $no_of_records->first();
                if($no_of_records && isset($no_of_records->total) && is_numeric($no_of_records->total)){
                    $no_of_records = $no_of_records->total;
                }
                else{
                    $no_of_records = 1;
                }
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

            if($records && is_array($records) && count($records) > 0){
                foreach($records as $record){
                    if(isset($financial_year_records[$record->bdm_email]) && isset($financial_year_records[$record->bdm_email]->net_inflow_till_date) && is_numeric($financial_year_records[$record->bdm_email]->net_inflow_till_date)){
                        $record->net_inflow_financial_year_till_date = floatval($financial_year_records[$record->bdm_email]->net_inflow_till_date);
                    }
                    if(isset($quarterly_records[$record->bdm_email]) && isset($quarterly_records[$record->bdm_email]->net_inflow_till_date) && is_numeric($quarterly_records[$record->bdm_email]->net_inflow_till_date)){
                        $record->net_inflow_current_quarter_till_date = floatval($quarterly_records[$record->bdm_email]->net_inflow_till_date);
                    }
                }
                unset($request);
            }
        }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        // x(DB::getQueryLog(), 'query_log');
        unset($financial_year_dates, $where_conditions, $where_in_conditions, $extra_query_conditions, $order_by_clause, $query_record_limit_conditions, $append_bdm_queries, $append_transaction_tbl_queries);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getSchemeDetailbyCode(){
     $records =  DB::table('samcomf_investor_db.scheme_master_details')->select('RTA_Scheme_Code', 'meta_title')->get()->toArray();
     return $records;
    }
    public static function getAumForARN($scheme_filter,$arn,$date){
        $records = array();
        // return $records;
        $query_prefix = '';
        $query_suffix = ';';
        if(!$scheme_filter){
            $query_prefix = "SELECT IFNULL(SUM(scheme_wise_transactions.scheme_wise_aum), 0) AS scheme_wise_aum FROM (";
            $query_suffix = " GROUP BY scheme_wise_transactions.scheme_code) AS scheme_wise_transactions;";
        }

        $records = DB::select($query_prefix . "SELECT scheme_wise_transactions.scheme_code, SUM(scheme_wise_transactions.available_units) AS available_units, scheme_wise_transactions.purchase_amt, scheme_wise_transactions.redeemed_amt, (SUM(scheme_wise_transactions.available_units) * scheme_wise_nav.NAV) AS scheme_wise_aum, scheme_wise_nav.NAV, scheme_wise_nav.NAV_Date FROM (SELECT kfintec_Postendorsement_TransactionDetails_final.scheme_code, SUM(CASE WHEN(purred = 'P') THEN IFNULL(amt, 0) ELSE 0 END) AS purchase_amt, SUM(CASE WHEN(purred = 'R') THEN IFNULL(units, 0) ELSE 0 END) AS redeemed_amt, (SUM(CASE WHEN(purred = 'P') THEN IFNULL(units, 0) ELSE 0 END) - (SUM(CASE WHEN(purred = 'R') THEN IFNULL(units, 0) ELSE 0 END))) AS available_units 
                            FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final 
                            WHERE agent_code = :agent_code AND kfintec_Postendorsement_TransactionDetails_final.trdt <= :nav_date 
                            GROUP BY kfintec_Postendorsement_TransactionDetails_final.scheme_code) AS scheme_wise_transactions 
                            INNER JOIN (SELECT CONCAT(nav_history.Scheme_Code, nav_history.Plan_Code) AS Scheme_Plan_Code, nav_history.Scheme_Code, nav_history.Plan_Code, nav_history.NAV, nav_history.NAV_Date 
                            FROM samcomf_investor_db.nav_history 
                            INNER JOIN (SELECT Scheme_Code, Plan_Code, MAX(NAV_Date) AS NAV_Date 
                            FROM samcomf_investor_db.nav_history 
                            WHERE status = 1 AND NAV_Date <= :nav_date 
                            GROUP BY Scheme_Code, Plan_Code) AS scheme_wise_max_nav 
                            ON (scheme_wise_max_nav.Scheme_Code = nav_history.Scheme_Code AND scheme_wise_max_nav.Plan_Code = nav_history.Plan_Code AND scheme_wise_max_nav.NAV_Date = nav_history.NAV_Date) 
                            WHERE nav_history.status = 1 AND nav_history.NAV_Date <= :nav_date) AS scheme_wise_nav 
                            ON (scheme_wise_nav.Scheme_Plan_Code = scheme_wise_transactions.scheme_code)". $query_suffix, array(':agent_code' => $arn, ':nav_date' => $date));

        if(is_array($records) && count($records) > 0 && $scheme_filter){
            $records = array_column($records, NULL, 'scheme_code');
        }
        return $records;

    }

    public static function getAumForMonthwise($scheme_filter,$arn,$date,$scheme_code){
        $records = array();
        // return $records;
        if($scheme_filter){
            // retrieving scheme and monthwise aum data
            $records = DB::select("SELECT RTA_Scheme_Code, m1, IFNULL((SELECT (a.available_units * b.NAV) AS total_aum 
                                    FROM (SELECT kfintec_Postendorsement_TransactionDetails_final.scheme_code, 
                                          (SUM(CASE WHEN(purred = 'P') THEN IFNULL(units, 0) ELSE 0 END) - (SUM(CASE WHEN(purred = 'R') THEN IFNULL(units, 0) ELSE 0 END))) AS available_units 
                                          FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final 
                                          WHERE agent_code = :agent_code AND kfintec_Postendorsement_TransactionDetails_final.trdt <= LAST_DAY(m1) AND kfintec_Postendorsement_TransactionDetails_final.scheme_code = scheme_master.RTA_Scheme_Code 
                                          GROUP BY kfintec_Postendorsement_TransactionDetails_final.scheme_code) AS a 
                                    INNER JOIN (SELECT CONCAT(nav_history.Scheme_Code, nav_history.Plan_Code) AS Scheme_Plan_Code, nav_history.Scheme_Code, nav_history.Plan_Code, 
                                                nav_history.NAV, nav_history.NAV_Date 
                                                FROM samcomf_investor_db.nav_history 
                                                INNER JOIN (SELECT Scheme_Code, Plan_Code, MAX(NAV_Date) AS NAV_Date 
                                                            FROM samcomf_investor_db.nav_history 
                                                            WHERE status = 1 AND NAV_Date <= LAST_DAY(m1) 
                                                            GROUP BY Scheme_Code, Plan_Code) AS scheme_wise_max_nav 
                                                ON (scheme_wise_max_nav.Scheme_Code = nav_history.Scheme_Code AND scheme_wise_max_nav.Plan_Code = nav_history.Plan_Code AND scheme_wise_max_nav.NAV_Date = nav_history.NAV_Date) 
                                                WHERE nav_history.status = 1 AND nav_history.NAV_Date <= LAST_DAY(m1)) AS b 
                                    ON (a.scheme_code = b.Scheme_Plan_Code) 
                                    GROUP BY a.scheme_code), 0) AS total_aum 
                                    FROM samcomf_investor_db.scheme_master, 
                                    (select DATE_ADD(:aum_year, INTERVAL m MONTH) as m1 
                                          from (SELECT 0 AS m UNION SELECT 1 AS m UNION SELECT 2 AS m UNION SELECT 3 AS m UNION SELECT 4 AS m UNION SELECT 5 AS m UNION SELECT 6 AS m UNION SELECT 7 AS m UNION SELECT 8 AS m UNION SELECT 9 AS m UNION SELECT 10 AS m UNION SELECT 11 AS m) d1
                                    ) d2 
                                    WHERE scheme_master.RTA_Scheme_Code = :RTA_Scheme_Code AND m1 < '". date('Y-m-d') ."';", array(":agent_code" => $arn, ":aum_year" => $date, ":RTA_Scheme_Code" => $scheme_code));
        }
        else{
            // retrieving monthwise aum data for all schemes
            $records = DB::select("SELECT m1, (SELECT SUM(scheme_aum) AS total_aum FROM (SELECT a.scheme_code, a.available_units, (a.available_units * b.NAV) AS scheme_aum, b.NAV, b.NAV_Date 
                                    FROM (SELECT kfintec_Postendorsement_TransactionDetails_final.scheme_code, 
                                          (SUM(CASE WHEN(purred = 'P') THEN IFNULL(units, 0) ELSE 0 END) - (SUM(CASE WHEN(purred = 'R') THEN IFNULL(units, 0) ELSE 0 END))) AS available_units 
                                          FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final 
                                          WHERE agent_code = :agent_code AND kfintec_Postendorsement_TransactionDetails_final.trdt <= LAST_DAY(m1) 
                                          GROUP BY kfintec_Postendorsement_TransactionDetails_final.scheme_code) AS a 
                                    INNER JOIN (SELECT CONCAT(nav_history.Scheme_Code, nav_history.Plan_Code) AS Scheme_Plan_Code, nav_history.Scheme_Code, nav_history.Plan_Code, 
                                                nav_history.NAV, nav_history.NAV_Date 
                                                FROM samcomf_investor_db.nav_history 
                                                INNER JOIN (SELECT Scheme_Code, Plan_Code, MAX(NAV_Date) AS NAV_Date 
                                                            FROM samcomf_investor_db.nav_history 
                                                            WHERE status = 1 AND NAV_Date <= LAST_DAY(m1) 
                                                            GROUP BY Scheme_Code, Plan_Code) AS scheme_wise_max_nav 
                                                ON (scheme_wise_max_nav.Scheme_Code = nav_history.Scheme_Code AND scheme_wise_max_nav.Plan_Code = nav_history.Plan_Code AND scheme_wise_max_nav.NAV_Date = nav_history.NAV_Date) 
                                                WHERE nav_history.status = 1 AND nav_history.NAV_Date <= LAST_DAY(m1)) AS b 
                                    ON (a.scheme_code = b.Scheme_Plan_Code) 
                                    GROUP BY a.scheme_code) AS t) AS total_aum 
                                    FROM (select DATE_ADD(:aum_year, INTERVAL m MONTH) as m1 
                                          from (SELECT 0 AS m UNION SELECT 1 AS m UNION SELECT 2 AS m UNION SELECT 3 AS m UNION SELECT 4 AS m UNION SELECT 5 AS m UNION SELECT 6 AS m UNION SELECT 7 AS m UNION SELECT 8 AS m UNION SELECT 9 AS m UNION SELECT 10 AS m UNION SELECT 11 AS m) d1
                                    ) d2 
                                    WHERE m1 < CURDATE();", array(":agent_code" => $arn, ":aum_year" => $date));
        }

        if(is_array($records) && count($records) > 0){
            $records = array_column($records, NULL, 'm1');
        }
        return $records;
    }
    public static function getAumForDayWise($scheme_filter,$arn,$arr_transaction_dates,$scheme_code){
        $records = array();
        // return $records;
        if(is_array($arr_transaction_dates) && count($arr_transaction_dates) > 0){
            $query = array();
            array_walk($arr_transaction_dates, function($_value, $_key, $_user_data){
                $_user_data[0][] = "SELECT '". $_value ."' AS m";
            }, [&$query]);
            $query = implode(' UNION ', $query);
            $query = "(". $query .")";
        }
        if($scheme_filter){
            $records = DB::select("SELECT RTA_Scheme_Code, m, IFNULL((SELECT (a.available_units * b.NAV) AS total_aum 
                                    FROM (SELECT kfintec_Postendorsement_TransactionDetails_final.scheme_code, 
                                          (SUM(CASE WHEN(purred = 'P') THEN IFNULL(units, 0) ELSE 0 END) - (SUM(CASE WHEN(purred = 'R') THEN IFNULL(units, 0) ELSE 0 END))) AS available_units 
                                          FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final 
                                          WHERE agent_code = :agent_code AND kfintec_Postendorsement_TransactionDetails_final.trdt <= m AND kfintec_Postendorsement_TransactionDetails_final.scheme_code = scheme_master.RTA_Scheme_Code 
                                          GROUP BY kfintec_Postendorsement_TransactionDetails_final.scheme_code) AS a 
                                    INNER JOIN (SELECT CONCAT(nav_history.Scheme_Code, nav_history.Plan_Code) AS Scheme_Plan_Code, nav_history.Scheme_Code, nav_history.Plan_Code, 
                                                nav_history.NAV, nav_history.NAV_Date 
                                                FROM samcomf_investor_db.nav_history 
                                                INNER JOIN (SELECT Scheme_Code, Plan_Code, MAX(NAV_Date) AS NAV_Date 
                                                            FROM samcomf_investor_db.nav_history 
                                                            WHERE status = 1 AND NAV_Date <= m 
                                                            GROUP BY Scheme_Code, Plan_Code) AS scheme_wise_max_nav 
                                                ON (scheme_wise_max_nav.Scheme_Code = nav_history.Scheme_Code AND scheme_wise_max_nav.Plan_Code = nav_history.Plan_Code AND scheme_wise_max_nav.NAV_Date = nav_history.NAV_Date) 
                                                WHERE nav_history.status = 1 AND nav_history.NAV_Date <= m) AS b 
                                    ON (a.scheme_code = b.Scheme_Plan_Code) 
                                    GROUP BY a.scheme_code), 0) AS total_aum 
                                    FROM samcomf_investor_db.scheme_master, 
                                    ". $query ." d2 
                                    WHERE scheme_master.RTA_Scheme_Code = :RTA_Scheme_Code;", array(":agent_code" => $arn, ":RTA_Scheme_Code" => $scheme_code));
        }
        else{
            $records = DB::select("SELECT m, (SELECT SUM(scheme_aum) AS total_aum FROM (SELECT a.scheme_code, a.available_units, (a.available_units * scheme_wise_nav.NAV) AS scheme_aum, scheme_wise_nav.NAV, scheme_wise_nav.NAV_Date 
                                    FROM (SELECT kfintec_Postendorsement_TransactionDetails_final.scheme_code, (SUM(CASE WHEN(purred = 'P') THEN IFNULL(units, 0) ELSE 0 END) - (SUM(CASE WHEN(purred = 'R') THEN IFNULL(units, 0) ELSE 0 END))) AS available_units 
                                                                    FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final 
                                                                    WHERE agent_code = :agent_code AND kfintec_Postendorsement_TransactionDetails_final.trdt <= m 
                                                                    GROUP BY kfintec_Postendorsement_TransactionDetails_final.scheme_code) AS a 
                                    INNER JOIN (SELECT CONCAT(nav_history.Scheme_Code, nav_history.Plan_Code) AS Scheme_Plan_Code, nav_history.Scheme_Code, nav_history.Plan_Code, nav_history.NAV, nav_history.NAV_Date 
                                                                                FROM samcomf_investor_db.nav_history 
                                                                                INNER JOIN (SELECT Scheme_Code, Plan_Code, MAX(NAV_Date) AS NAV_Date 
                                                                                            FROM samcomf_investor_db.nav_history 
                                                                                            WHERE status = 1 AND NAV_Date <= m 
                                                                                            GROUP BY Scheme_Code, Plan_Code) AS scheme_wise_max_nav 
                                                                                ON (scheme_wise_max_nav.Scheme_Code = nav_history.Scheme_Code AND scheme_wise_max_nav.Plan_Code = nav_history.Plan_Code AND scheme_wise_max_nav.NAV_Date = nav_history.NAV_Date) 
                                                                                WHERE nav_history.status = 1 AND nav_history.NAV_Date <= m) AS scheme_wise_nav 
                                    ON (scheme_wise_nav.Scheme_Plan_Code = a.scheme_code) GROUP BY a.scheme_code) AS t) AS total_aum 
                                                                    FROM ". $query ." AS d2;", array(":agent_code" => $arn));
        }

        if(is_array($records) && count($records) > 0){
            $records = array_column($records, NULL, 'm');
        }
        return $records;
    }

    /** JIRA ID: SMF-388
      * Get MIS of order details placed by DIRECT investors
      */
    public static function get_daily_direct_orders($input_arr = array()){
        /* Possible values for $input_arr are: array('send_email' => Possible values are 1 = send an email or 0 = do not send an email);
         */
        extract($input_arr);

        if(!isset($send_email)){
            $send_email = 0;
        }
        $send_email = intval($send_email);

        $output_arr = array('total_direct_investors' => 0,
                            'direct_investment_folios' => 0,
                            'total_direct_aum' => 0,
                            'scheme_wise_aum' => array(),
                            'scheme_wise_investors' => array(),
                            'total_leads_as_on_date' => 0,
                            'total_leads_received_yesterday' => 0);
        try{
            // Retrieving Total Direct Investors data
            $retrieved_total_direct_investors = DB::select("SELECT COUNT(DISTINCT post_endorsement.pan) AS no_of_direct_investors FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS post_endorsement INNER JOIN samcomf_investor_db.scheme_master AS scheme ON (post_endorsement.scheme_code = scheme.RTA_Scheme_Code) WHERE post_endorsement.agent_code = '0' AND scheme.Scheme_Plan = 'direct';");
            if(isset($retrieved_total_direct_investors[0]) && isset($retrieved_total_direct_investors[0]->no_of_direct_investors)){
                $output_arr['total_direct_investors'] = $retrieved_total_direct_investors[0]->no_of_direct_investors;
            }
            unset($retrieved_total_direct_investors);

            // Retrieving Direct Investment Folios
            $retrieved_total_direct_folios = DB::select("SELECT COUNT(DISTINCT post_endorsement.acno) AS no_of_direct_folios FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS post_endorsement INNER JOIN samcomf_investor_db.scheme_master AS scheme ON (post_endorsement.scheme_code = scheme.RTA_Scheme_Code) WHERE post_endorsement.agent_code = '0' AND scheme.Scheme_Plan = 'direct' AND IFNULL(post_endorsement.acno, '') != '' AND post_endorsement.acno != 0;");
            if(isset($retrieved_total_direct_folios[0]) && isset($retrieved_total_direct_folios[0]->no_of_direct_folios)){
                $output_arr['direct_investment_folios'] = $retrieved_total_direct_folios[0]->no_of_direct_folios;
            }
            unset($retrieved_total_direct_folios);

            // Retrieving Direct Scheme Wise AUM
            $retrieved_scheme_wise_aum = DB::select("SELECT scheme_investment.scheme_code, scheme_investment.Scheme_Name, IFNULL(scheme_investment.scheme_units, 0) AS scheme_units, (IFNULL(scheme_investment.scheme_units, 0) * IFNULL(scheme_master_details.nav, 0)) AS scheme_aum FROM (SELECT scheme.RTA_Scheme_Code AS scheme_code, scheme.Scheme_Name, SUM(CASE WHEN(post_endorsement.purred NOT IN ('P') AND IFNULL(post_endorsement.units, 0) > 0) THEN (IFNULL(post_endorsement.units, 0) * -1) ELSE IFNULL(post_endorsement.units, 0) END) AS scheme_units FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS post_endorsement INNER JOIN samcomf_investor_db.scheme_master AS scheme ON (post_endorsement.scheme_code = scheme.RTA_Scheme_Code) WHERE post_endorsement.agent_code = '0' AND scheme.Scheme_Plan = 'direct' GROUP BY scheme.RTA_Scheme_Code) AS scheme_investment INNER JOIN samcomf_investor_db.scheme_master_details ON (scheme_investment.scheme_code = scheme_master_details.RTA_Scheme_Code) WHERE 1 GROUP BY scheme_investment.scheme_code;");
            if(is_array($retrieved_scheme_wise_aum) && count($retrieved_scheme_wise_aum) > 0){
                $output_arr['scheme_wise_aum'] = array_column($retrieved_scheme_wise_aum, NULL, 'Scheme_Name');
                $output_arr['total_direct_aum'] = array_sum(array_column($retrieved_scheme_wise_aum, 'scheme_aum'));
            }
            unset($retrieved_scheme_wise_aum);

            // Retrieving Direct Scheme Wise Investors Count
            $retrieved_scheme_wise_investors = DB::select("SELECT scheme.RTA_Scheme_Code AS scheme_code, scheme.Scheme_Name, COUNT(DISTINCT post_endorsement.pan) AS scheme_direct_investors FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS post_endorsement INNER JOIN samcomf_investor_db.scheme_master AS scheme ON (post_endorsement.scheme_code = scheme.RTA_Scheme_Code) WHERE post_endorsement.agent_code = '0' AND scheme.Scheme_Plan = 'direct' GROUP BY scheme.RTA_Scheme_Code;");
            if(is_array($retrieved_scheme_wise_investors) && count($retrieved_scheme_wise_investors) > 0){
                $output_arr['scheme_wise_investors'] = array_column($retrieved_scheme_wise_investors, NULL, 'Scheme_Name');
            }
            unset($retrieved_scheme_wise_investors);

            // Retrieving Total Leads As On Date
            $retrieving_leads_as_on_date = DB::select("SELECT `lead`.`pan`, `lead`.`name`, `lead`.`email`, `lead`.`mobile`, `lead`.`from_site`, `lead`.`whatsapp_optin`, `lead`.`ip_address`, `lead`.`broker_id`, `lead`.`created_at` FROM `samcomf_investor_db`.`investor_lead` AS `lead` LEFT JOIN `samcomf_investor_db`.`investor_account` AS `account` ON (`lead`.`pan` = `account`.`pan`) WHERE `account`.`pan` IS NULL AND IFNULL(`lead`.`broker_id`, '') = '' AND lead.created_at <= NOW() ORDER BY `lead`.`created_at` ASC,`lead`.`pan` ASC;");
            if(isset($retrieving_leads_as_on_date) && is_array($retrieving_leads_as_on_date) && count($retrieving_leads_as_on_date) > 0){
                $output_arr['total_leads_as_on_date'] = count($retrieving_leads_as_on_date);
            }
            unset($retrieving_leads_as_on_date);

            // Retrieving Total Leads Yesterday
            $retrieving_leads_on_yesterday = DB::select("SELECT `lead`.`pan`, `lead`.`name`, `lead`.`email`, `lead`.`mobile`, `lead`.`from_site`, `lead`.`whatsapp_optin`, `lead`.`ip_address`, `lead`.`broker_id`, `lead`.`created_at` FROM `samcomf_investor_db`.`investor_lead` AS `lead` LEFT JOIN `samcomf_investor_db`.`investor_account` AS `account` ON (`lead`.`pan` = `account`.`pan`) WHERE `account`.`pan` IS NULL AND IFNULL(`lead`.`broker_id`, '') = '' AND lead.created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 Day) AND CURDATE() ORDER BY `lead`.`created_at` ASC,`lead`.`pan` ASC;");
            if(isset($retrieving_leads_on_yesterday) && is_array($retrieving_leads_on_yesterday) && count($retrieving_leads_on_yesterday) > 0){
                $output_arr['total_leads_received_yesterday'] = count($retrieving_leads_on_yesterday);
            }
            unset($retrieving_leads_on_yesterday);

            // sending an email
            if($send_email == 1){
                $expload_to_mail = array();
                $to_mail = getSettingsTableValue('DAILY_DIRECT_ORDERS_UPDATE_EMAIL_NOTIFY_TO');
                if(isset($to_mail) && !empty($to_mail)){
                    $expload_to_mail = explode(',', $to_mail);
                    array_walk($expload_to_mail, function(&$_value){
                        $_value = (array) trim(strip_tags($_value));
                    });
                }
                $output_arr['email_notify_to'] = $expload_to_mail;

                $txtDecimalPrecision = 0;
                if(isset($expload_to_mail) && is_array($expload_to_mail) && count($expload_to_mail) > 0){
                    $direct_investors_count_html = '';
                    if(isset($output_arr['scheme_wise_investors']) && is_array($output_arr['scheme_wise_investors']) && count($output_arr['scheme_wise_investors']) > 0){
                        foreach($output_arr['scheme_wise_investors'] as $record){
                            $direct_investors_count_html .= '<tr align="left">'.
                                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Direct Investors Count in '. ($record->Scheme_Name??'') .':</td>'.
                                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($record->scheme_direct_investors??0) .'</td>'.
                                                            '</tr>';
                        }
                        unset($record);
                    }

                    if(!empty($direct_investors_count_html)){
                        $direct_investors_count_html ='<tr>'.
                                                        '<td style="text-align: center;">'.
                                                          '<table cellpadding="0" cellspacing="0" style="border-collapse: collapse;border-spacing: 0px;margin: 0px;" width="100%">'.
                                                            '<tbody>'.
                                                              '<tr align="left">'.
                                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Total Direct Investors:</td>'.
                                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($output_arr['total_direct_investors']??0) .'</td>'.
                                                              '</tr>'.
                                                              $direct_investors_count_html .
                                                            '</tbody>'.
                                                          '</table>'.
                                                        '</td>'.
                                                      '</tr>';
                    }

                    $direct_investors_aum_html = '';
                    if(isset($output_arr['scheme_wise_aum']) && is_array($output_arr['scheme_wise_aum']) && count($output_arr['scheme_wise_aum']) > 0){
                        foreach($output_arr['scheme_wise_aum'] as $record){
                            $direct_investors_aum_html .= '<tr align="left">'.
                                                            '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Direct Investors AUM in '. ($record->Scheme_Name??'') .':</td>'.
                                                            '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. number_format(($record->scheme_aum??0), $txtDecimalPrecision) .'</td>'.
                                                            '</tr>';
                        }
                        unset($record);
                    }

                    if(!empty($direct_investors_aum_html)){
                        $direct_investors_aum_html ='<tr>'.
                                                        '<td style="text-align: center;">'.
                                                          '<table cellpadding="0" cellspacing="0" style="border-collapse: collapse;border-spacing: 0px;margin: 0px;" width="100%">'.
                                                            '<tbody>'.
                                                              '<tr align="left">'.
                                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Total Direct AUM:</td>'.
                                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. number_format(($output_arr['total_direct_aum']??0), $txtDecimalPrecision) .'</td>'.
                                                              '</tr>'.
                                                              $direct_investors_aum_html .
                                                            '</tbody>'.
                                                          '</table>'.
                                                        '</td>'.
                                                      '</tr>';
                    }

                    $email_body = '<table cellpadding="0" cellspacing="0" style="background:#ffffff;border-collapse:collapse;border-spacing:0;" width="100%">'.
                                      '<tr>'.
                                        '<td></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="border-bottom: 1px solid #E5E5E5;"></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="height:10px;line-height:10px;"><br></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:22px;font-weight:400;">Hi,</td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="height:20px;line-height:20px;"></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style=" text-align: justify; font-family: Heebo, sans-serif;font-size: 17px;line-height: 25px;font-weight: 400;color: #434343;">Please find below daily MIS of Direct Orders</td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="height:20px;line-height:20px;"><br></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="text-align: center;">'.
                                          '<table cellpadding="0" cellspacing="0" style="border-collapse: collapse;border-spacing: 0px;margin: 0px;" width="100%">'.
                                            '<tbody>'.
                                              '<tr align="left">'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Direct Investment Folios:</td>'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($output_arr['direct_investment_folios']??0) .'</td>'.
                                              '</tr>'.
                                            '</tbody>'.
                                          '</table>'.
                                        '</td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="height:20px;line-height:20px;"><br></td>'.
                                      '</tr>'.
                                        $direct_investors_count_html.
                                      '<tr>'.
                                        '<td style="height:20px;line-height:20px;"><br></td>'.
                                      '</tr>'.
                                        $direct_investors_aum_html.
                                      '<tr>'.
                                        '<td style="height:20px;line-height:20px;"><br></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="text-align: center;">'.
                                          '<table cellpadding="0" cellspacing="0" style="border-collapse: collapse;border-spacing: 0px;margin: 0px;" width="100%">'.
                                            '<tbody>'.
                                              '<tr align="left">'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Total Leads as on Date:</td>'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($output_arr['total_leads_as_on_date']??0) .'</td>'.
                                              '</tr>'.
                                              '<tr align="left">'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Leads Received Yesterday:</td>'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($output_arr['total_leads_received_yesterday']??0) .'</td>'.
                                              '</tr>'.
                                            '</tbody>'.
                                          '</table>'.
                                        '</td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="height:20px;line-height:30px;"><br></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style=" text-align: justify; font-family: Heebo, sans-serif;font-size: 17px;line-height: 25px;font-weight: 400;color: #434343;">APP_URL: '. env('APP_URL') .'</td>'.
                                      '</tr>'.
                                    '</table>';

                    $mailer = new \App\Libraries\PhpMailer();
                    $params = [];
                    $template = "SAMCOMF-GENERAL-NOTIFICATION";
                    $params['templateName'] = $template;
                    $params['channel']      = $template;
                    $params['from_email']   = "alerts@samcomf.com";
                    $params['to']           = $expload_to_mail;
                    $params['merge_vars'] = array('MAIL_BODY' => $email_body);
                    $params['subject'] = '['. date('d M Y H:i:s') . ']: Daily Direct Orders Update';
                    $email_send = $mailer->mandrill_send($params);
                    unset($email_body, $direct_investors_count_html, $direct_investors_aum_html, $params, $mailer, $email_send, $template);
                }
                unset($expload_to_mail, $to_mail, $txtDecimalPrecision);
            }
        }
        catch(Exception $e){
            $output_arr['err_flag'] = 1;
            $output_arr['err_msg'] = 'General error: '. $e->getMessage();
        }
        catch(\Illuminate\Database\QueryException $e){
            $output_arr['err_flag'] = 1;
            $output_arr['err_msg'] = 'Query error: '. $e->getMessage();
        }
        return $output_arr;
    }

    /** JIRA ID: SMF-388
      * Get MIS of distributors empanelment and activation
      */
    public static function daily_empanelment_and_activation_data($input_arr = array()){
        /* Possible values for $input_arr are: array('send_email' => Possible values are 1 = send an email or 0 = do not send an email);
         */
        extract($input_arr);

        if(!isset($send_email)){
            $send_email = 0;
        }
        $send_email = intval($send_email);
        $current_date_time = date('Y-m-d_H-i');

        $output_arr = array('total_empanelment_leads' => 0,
                            'new_leads_received_yesterday' => 0,
                            'new_empanelment_done_yesterday' => 0,
                            'total_empanelled_partners' => 0,
                            'total_active_partners' => 0,
                            'total_active_partners_in_equity_and_hybrid_scheme' => 0,
                            'total_active_partners_in_liquid_scheme' => 0,
                            'total_non_active_partners' => 0);
        try{
            // Retrieving Total Empanelment Leads data
            $retrieved_total_empanelment_leads = DB::select("SELECT COUNT(DISTINCT ARN) AS total_empanelment_leads FROM user_account WHERE status NOT IN (2);");
            if(isset($retrieved_total_empanelment_leads[0]) && isset($retrieved_total_empanelment_leads[0]->total_empanelment_leads)){
                $output_arr['total_empanelment_leads'] = $retrieved_total_empanelment_leads[0]->total_empanelment_leads;
            }
            unset($retrieved_total_empanelment_leads);

            // Retrieving New Leads Received Yesterday data
            $retrieved_new_leads_received_yesterday = DB::select("SELECT COUNT(DISTINCT ARN) AS total_empanelment_leads FROM user_account WHERE status NOT IN (2) AND created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 Day) AND CURDATE();");
            if(isset($retrieved_new_leads_received_yesterday[0]) && isset($retrieved_new_leads_received_yesterday[0]->total_empanelment_leads)){
                $output_arr['new_leads_received_yesterday'] = $retrieved_new_leads_received_yesterday[0]->total_empanelment_leads;
            }
            unset($retrieved_new_leads_received_yesterday);

            // Retrieving New Empanelment Done Yesterday data
            $retrieved_new_empanelment_done_yesterday = DB::select("SELECT COUNT(DISTINCT ARN) AS total_empanelment_leads FROM user_account WHERE status IN (2) AND created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 Day) AND CURDATE();");
            if(isset($retrieved_new_empanelment_done_yesterday[0]) && isset($retrieved_new_empanelment_done_yesterday[0]->total_empanelment_leads)){
                $output_arr['new_empanelment_done_yesterday'] = $retrieved_new_empanelment_done_yesterday[0]->total_empanelment_leads;
            }
            unset($retrieved_new_empanelment_done_yesterday);

            // Retrieving Total Empanelled Partners
            $retrieved_total_empanelled_partners = DB::select("SELECT COUNT(DISTINCT ARN) AS total_empanelment_leads FROM user_account WHERE status IN (2);");
            if(isset($retrieved_total_empanelled_partners[0]) && isset($retrieved_total_empanelled_partners[0]->total_empanelment_leads)){
                $output_arr['total_empanelled_partners'] = $retrieved_total_empanelled_partners[0]->total_empanelment_leads;
            }
            unset($retrieved_total_empanelled_partners);

            // Retrieving Total Active Partners data
            $active_partners_csv_file_path = sys_get_temp_dir().'/ACTIVE_PARTNERS_DATA_'. $current_date_time .'.csv';
            $file_csv = fopen($active_partners_csv_file_path,'w');
            fputcsv($file_csv, array('ARN', 'ARN Holder Name', 'ARN Email ID', 'ARN Mobile Number', 'Empanelment Completed', 'Relationship Manager Name', 'Relationship Manager Email ID', 'Relationship Manager Mobile Number', 'Reporting Manager Name', 'Reporting Manager Email ID', 'Reporting Manager Mobile Number', 'Available Units as on '. date('Y-m-d'), 'AUM as on '. date('Y-m-d')));
            $retrieving_total_active_partners = DB::select("SELECT arn_investment.ARN, arn_investment.arn_name, arn_investment.arn_email, arn_investment.arn_mobile, arn_investment.empanelled_or_not, IFNULL(bdm.name, '') AS bdm_name, IFNULL(bdm.email, '') AS bdm_email, IFNULL(bdm_details.mobile_number, '') AS bdm_mobile_number, IFNULL(reporting.name, '') AS reporting_name, IFNULL(reporting.email, '') AS reporting_email, IFNULL(reporting_details.mobile_number, '') AS reporting_mobile_number, SUM(IFNULL(arn_investment.scheme_units, 0)) AS arn_available_units, SUM(IFNULL(arn_investment.scheme_aum, 0)) AS arn_aum FROM (SELECT arn_investment.ARN, arn_investment.arn_name, arn_investment.arn_email, arn_investment.arn_mobile, arn_investment.empanelled_or_not, arn_investment.scheme_code, SUM(IFNULL(arn_investment.scheme_units, 0)) AS scheme_units, (SUM(IFNULL(arn_investment.scheme_units, 0)) * scheme_master_details.nav) AS scheme_aum FROM (SELECT user_account.ARN, user_account.name AS arn_name, user_account.email AS arn_email, user_account.mobile AS arn_mobile, CASE WHEN(user_account.status IN (2)) THEN 'yes' ELSE 'no' END AS empanelled_or_not, post_endorsement.scheme_code, SUM(CASE WHEN(post_endorsement.purred NOT IN ('P') AND IFNULL(post_endorsement.units, 0) > 0) THEN (IFNULL(post_endorsement.units, 0) * -1) ELSE IFNULL(post_endorsement.units, 0) END) AS scheme_units FROM samcomf.user_account INNER JOIN samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS post_endorsement FORCE INDEX(idx_agent_code) ON (user_account.ARN = post_endorsement.agent_code) INNER JOIN samcomf_investor_db.scheme_master AS scheme ON (post_endorsement.scheme_code = scheme.RTA_Scheme_Code) WHERE scheme.Scheme_Plan = 'regular' GROUP BY user_account.ARN, post_endorsement.scheme_code HAVING scheme_units > 0) AS arn_investment INNER JOIN samcomf_investor_db.scheme_master_details ON (arn_investment.scheme_code = scheme_master_details.RTA_Scheme_Code) WHERE 1 GROUP BY arn_investment.ARN, arn_investment.scheme_code) AS arn_investment LEFT JOIN samcomf.drm_distributor_master AS drm ON (arn_investment.ARN = drm.ARN) LEFT JOIN samcomf.users AS bdm ON (drm.direct_relationship_user_id = bdm.id AND bdm.is_drm_user = 1) LEFT JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id) LEFT JOIN samcomf.users_details AS reporting_details ON (reporting.id = reporting_details.user_id) WHERE 1 GROUP BY arn_investment.ARN ORDER BY arn_aum DESC;");
            if(isset($retrieving_total_active_partners) && is_array($retrieving_total_active_partners) && count($retrieving_total_active_partners) > 0){
                $output_arr['total_active_partners'] = count($retrieving_total_active_partners);
                // Loop through file pointer and a line
                foreach ($retrieving_total_active_partners as $looping_record) {
                    fputcsv($file_csv, (array) $looping_record);
                }
                unset($looping_record);
            }
            fclose($file_csv);
            unset($retrieving_total_active_partners, $file_csv);

            // Retrieving Total Active Partners in Equity & Hybrid Schemes
            $active_partners_in_equity_and_hybrid_scheme_csv_file_path = sys_get_temp_dir().'/ACTIVE_PARTNERS_IN_EQUITY_AND_HYBRID_SCHEMES_DATA_'. $current_date_time .'.csv';
            $file_csv = fopen($active_partners_in_equity_and_hybrid_scheme_csv_file_path,'w');
            fputcsv($file_csv, array('ARN', 'ARN Holder Name', 'ARN Email ID', 'ARN Mobile Number', 'Empanelment Completed', 'Relationship Manager Name', 'Relationship Manager Email ID', 'Relationship Manager Mobile Number', 'Reporting Manager Name', 'Reporting Manager Email ID', 'Reporting Manager Mobile Number', 'Available Units as on '. date('Y-m-d'), 'AUM as on '. date('Y-m-d')));
            $retrieving_active_partners_in_equity_and_hybrid_scheme = DB::select("SELECT arn_investment.ARN, arn_investment.arn_name, arn_investment.arn_email, arn_investment.arn_mobile, arn_investment.empanelled_or_not, IFNULL(bdm.name, '') AS bdm_name, IFNULL(bdm.email, '') AS bdm_email, IFNULL(bdm_details.mobile_number, '') AS bdm_mobile_number, IFNULL(reporting.name, '') AS reporting_name, IFNULL(reporting.email, '') AS reporting_email, IFNULL(reporting_details.mobile_number, '') AS reporting_mobile_number, SUM(IFNULL(arn_investment.scheme_units, 0)) AS arn_available_units, SUM(IFNULL(arn_investment.scheme_aum, 0)) AS arn_aum FROM (SELECT arn_investment.ARN, arn_investment.arn_name, arn_investment.arn_email, arn_investment.arn_mobile, arn_investment.empanelled_or_not, arn_investment.scheme_code, SUM(IFNULL(arn_investment.scheme_units, 0)) AS scheme_units, (SUM(IFNULL(arn_investment.scheme_units, 0)) * scheme_master_details.nav) AS scheme_aum FROM (SELECT user_account.ARN, user_account.name AS arn_name, user_account.email AS arn_email, user_account.mobile AS arn_mobile, CASE WHEN(user_account.status IN (2)) THEN 'yes' ELSE 'no' END AS empanelled_or_not, post_endorsement.scheme_code, SUM(CASE WHEN(post_endorsement.purred NOT IN ('P') AND IFNULL(post_endorsement.units, 0) > 0) THEN (IFNULL(post_endorsement.units, 0) * -1) ELSE IFNULL(post_endorsement.units, 0) END) AS scheme_units FROM samcomf.user_account INNER JOIN samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS post_endorsement FORCE INDEX(idx_agent_code) ON (user_account.ARN = post_endorsement.agent_code) INNER JOIN samcomf_investor_db.scheme_master AS scheme ON (post_endorsement.scheme_code = scheme.RTA_Scheme_Code) WHERE scheme.Scheme_Plan = 'regular' AND scheme.Scheme_Type IN ('Equity', 'Hybrid') GROUP BY user_account.ARN, post_endorsement.scheme_code HAVING scheme_units > 0) AS arn_investment INNER JOIN samcomf_investor_db.scheme_master_details ON (arn_investment.scheme_code = scheme_master_details.RTA_Scheme_Code) WHERE 1 GROUP BY arn_investment.ARN, arn_investment.scheme_code) AS arn_investment LEFT JOIN samcomf.drm_distributor_master AS drm ON (arn_investment.ARN = drm.ARN) LEFT JOIN samcomf.users AS bdm ON (drm.direct_relationship_user_id = bdm.id AND bdm.is_drm_user = 1) LEFT JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id) LEFT JOIN samcomf.users_details AS reporting_details ON (reporting.id = reporting_details.user_id) WHERE 1 GROUP BY arn_investment.ARN ORDER BY arn_aum DESC;");
            if(isset($retrieving_active_partners_in_equity_and_hybrid_scheme) && is_array($retrieving_active_partners_in_equity_and_hybrid_scheme) && count($retrieving_active_partners_in_equity_and_hybrid_scheme) > 0){
                $output_arr['total_active_partners_in_equity_and_hybrid_scheme'] = count($retrieving_active_partners_in_equity_and_hybrid_scheme);
                // Loop through file pointer and a line
                foreach ($retrieving_active_partners_in_equity_and_hybrid_scheme as $looping_record) {
                    fputcsv($file_csv, (array) $looping_record);
                }
                unset($looping_record);
            }
            fclose($file_csv);
            unset($retrieving_active_partners_in_equity_and_hybrid_scheme, $file_csv);

            // Retrieving Total Active Partners in Liquid Schemes
            $active_partners_in_liquid_scheme_csv_file_path = sys_get_temp_dir().'/ACTIVE_PARTNERS_IN_LIQUID_SCHEMES_DATA_'. $current_date_time .'.csv';
            $file_csv = fopen($active_partners_in_liquid_scheme_csv_file_path,'w');
            fputcsv($file_csv, array('ARN', 'ARN Holder Name', 'ARN Email ID', 'ARN Mobile Number', 'Empanelment Completed', 'Relationship Manager Name', 'Relationship Manager Email ID', 'Relationship Manager Mobile Number', 'Reporting Manager Name', 'Reporting Manager Email ID', 'Reporting Manager Mobile Number', 'Available Units as on '. date('Y-m-d'), 'AUM as on '. date('Y-m-d')));
            $retrieving_active_partners_in_liquid_scheme = DB::select("SELECT arn_investment.ARN, arn_investment.arn_name, arn_investment.arn_email, arn_investment.arn_mobile, arn_investment.empanelled_or_not, IFNULL(bdm.name, '') AS bdm_name, IFNULL(bdm.email, '') AS bdm_email, IFNULL(bdm_details.mobile_number, '') AS bdm_mobile_number, IFNULL(reporting.name, '') AS reporting_name, IFNULL(reporting.email, '') AS reporting_email, IFNULL(reporting_details.mobile_number, '') AS reporting_mobile_number, SUM(IFNULL(arn_investment.scheme_units, 0)) AS arn_available_units, SUM(IFNULL(arn_investment.scheme_aum, 0)) AS arn_aum FROM (SELECT arn_investment.ARN, arn_investment.arn_name, arn_investment.arn_email, arn_investment.arn_mobile, arn_investment.empanelled_or_not, arn_investment.scheme_code, SUM(IFNULL(arn_investment.scheme_units, 0)) AS scheme_units, (SUM(IFNULL(arn_investment.scheme_units, 0)) * scheme_master_details.nav) AS scheme_aum FROM (SELECT user_account.ARN, user_account.name AS arn_name, user_account.email AS arn_email, user_account.mobile AS arn_mobile, CASE WHEN(user_account.status IN (2)) THEN 'yes' ELSE 'no' END AS empanelled_or_not, post_endorsement.scheme_code, SUM(CASE WHEN(post_endorsement.purred NOT IN ('P') AND IFNULL(post_endorsement.units, 0) > 0) THEN (IFNULL(post_endorsement.units, 0) * -1) ELSE IFNULL(post_endorsement.units, 0) END) AS scheme_units FROM samcomf.user_account INNER JOIN samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS post_endorsement FORCE INDEX(idx_agent_code) ON (user_account.ARN = post_endorsement.agent_code) INNER JOIN samcomf_investor_db.scheme_master AS scheme ON (post_endorsement.scheme_code = scheme.RTA_Scheme_Code) WHERE scheme.Scheme_Plan = 'regular' AND scheme.Scheme_Type IN ('Debt') GROUP BY user_account.ARN, post_endorsement.scheme_code HAVING scheme_units > 0) AS arn_investment INNER JOIN samcomf_investor_db.scheme_master_details ON (arn_investment.scheme_code = scheme_master_details.RTA_Scheme_Code) WHERE 1 GROUP BY arn_investment.ARN, arn_investment.scheme_code) AS arn_investment LEFT JOIN samcomf.drm_distributor_master AS drm ON (arn_investment.ARN = drm.ARN) LEFT JOIN samcomf.users AS bdm ON (drm.direct_relationship_user_id = bdm.id AND bdm.is_drm_user = 1) LEFT JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id) LEFT JOIN samcomf.users_details AS reporting_details ON (reporting.id = reporting_details.user_id) WHERE 1 GROUP BY arn_investment.ARN ORDER BY arn_aum DESC;");
            if(isset($retrieving_active_partners_in_liquid_scheme) && is_array($retrieving_active_partners_in_liquid_scheme) && count($retrieving_active_partners_in_liquid_scheme) > 0){
                $output_arr['total_active_partners_in_liquid_scheme'] = count($retrieving_active_partners_in_liquid_scheme);
                // Loop through file pointer and a line
                foreach ($retrieving_active_partners_in_liquid_scheme as $looping_record) {
                    fputcsv($file_csv, (array) $looping_record);
                }
                unset($looping_record);
            }
            fclose($file_csv);
            unset($retrieving_active_partners_in_liquid_scheme, $file_csv);

            // Retrieving Total Non Active Distributors data
            $non_active_partners_csv_file_path = sys_get_temp_dir().'/NON_ACTIVE_PARTNERS_DATA_'. $current_date_time .'.csv';
            $file_csv = fopen($non_active_partners_csv_file_path,'w');
            fputcsv($file_csv, array('ARN', 'ARN Holder Name', 'ARN Email ID', 'ARN Mobile Number', 'Empanelment Completed', 'Relationship Manager Name', 'Relationship Manager Email ID', 'Relationship Manager Mobile Number', 'Reporting Manager Name', 'Reporting Manager Email ID', 'Reporting Manager Mobile Number', 'Available Units as on '. date('Y-m-d')));
            $retrieving_non_active_partners = DB::select("SELECT arn_investment.ARN, arn_investment.arn_name, arn_investment.arn_email, arn_investment.arn_mobile, arn_investment.empanelled_or_not, IFNULL(bdm.name, '') AS bdm_name, IFNULL(bdm.email, '') AS bdm_email, IFNULL(bdm_details.mobile_number, '') AS bdm_mobile_number, IFNULL(reporting.name, '') AS reporting_name, IFNULL(reporting.email, '') AS reporting_email, IFNULL(reporting_details.mobile_number, '') AS reporting_mobile_number, arn_investment.scheme_units FROM (SELECT user_account.ARN, user_account.name AS arn_name, user_account.email AS arn_email, user_account.mobile AS arn_mobile, CASE WHEN(user_account.status IN (2)) THEN 'yes' ELSE 'no' END AS empanelled_or_not, IFNULL(SUM(CASE WHEN(post_endorsement.purred NOT IN ('P') AND IFNULL(post_endorsement.units, 0) > 0) THEN (IFNULL(post_endorsement.units, 0) * -1) ELSE IFNULL(post_endorsement.units, 0) END), 0) AS scheme_units FROM samcomf.user_account LEFT JOIN samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS post_endorsement FORCE INDEX(idx_agent_code) ON (user_account.ARN = post_endorsement.agent_code) LEFT JOIN samcomf_investor_db.scheme_master AS scheme ON (post_endorsement.scheme_code = scheme.RTA_Scheme_Code) WHERE 1 GROUP BY user_account.ARN HAVING scheme_units <= 0) AS arn_investment LEFT JOIN samcomf.drm_distributor_master AS drm ON (arn_investment.ARN = drm.ARN) LEFT JOIN samcomf.users AS bdm ON (drm.direct_relationship_user_id = bdm.id AND bdm.is_drm_user = 1) LEFT JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id) LEFT JOIN samcomf.users_details AS reporting_details ON (reporting.id = reporting_details.user_id) WHERE 1 GROUP BY arn_investment.ARN ORDER BY arn_investment.ARN;");
            if(isset($retrieving_non_active_partners) && is_array($retrieving_non_active_partners) && count($retrieving_non_active_partners) > 0){
                $output_arr['total_non_active_partners'] = count($retrieving_non_active_partners);
                // Loop through file pointer and a line
                foreach ($retrieving_non_active_partners as $looping_record) {
                    fputcsv($file_csv, (array) $looping_record);
                }
                unset($looping_record);
            }
            fclose($file_csv);
            unset($retrieving_non_active_partners, $file_csv);

            // sending an email
            if($send_email == 1){
                $expload_to_mail = array();
                $to_mail = getSettingsTableValue('DAILY_DISTRIBUTOR_EMPANELMENT_UPDATE_EMAIL_NOTIFY_TO');
                if(isset($to_mail) && !empty($to_mail)){
                    $expload_to_mail = explode(',', $to_mail);
                    array_walk($expload_to_mail, function(&$_value){
                        $_value = (array) trim(strip_tags($_value));
                    });
                }
                $output_arr['email_notify_to'] = $expload_to_mail;

                $txtDecimalPrecision = 0;
                if(isset($expload_to_mail) && is_array($expload_to_mail) && count($expload_to_mail) > 0){
                    $email_body = '<table cellpadding="0" cellspacing="0" style="background:#ffffff;border-collapse:collapse;border-spacing:0;" width="100%">'.
                                      '<tr>'.
                                        '<td></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="border-bottom: 1px solid #E5E5E5;"></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="height:10px;line-height:10px;"><br></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:22px;font-weight:400;">Hi,</td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="height:20px;line-height:20px;"></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style=" text-align: justify; font-family: Heebo, sans-serif;font-size: 17px;line-height: 25px;font-weight: 400;color: #434343;">Please find below daily MIS of partners empanelment and activation</td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="height:20px;line-height:20px;"></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style=" text-align: justify; font-family: Heebo, sans-serif;font-size: 17px;line-height: 25px;font-weight: 400;color: #434343;"><u>Empanelment Status</u></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="height:20px;line-height:20px;"><br></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="text-align: center;">'.
                                          '<table cellpadding="0" cellspacing="0" style="border-collapse: collapse;border-spacing: 0px;margin: 0px;" width="100%">'.
                                            '<tbody>'.
                                              '<tr align="left">'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Total Empanelment Leads:</td>'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($output_arr['total_empanelment_leads']??0) .'</td>'.
                                              '</tr>'.
                                              '<tr align="left">'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">New Leads Received Yesterday:</td>'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($output_arr['new_leads_received_yesterday']??0) .'</td>'.
                                              '</tr>'.
                                              '<tr align="left">'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">New Empanelment Done Yesterday:</td>'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($output_arr['new_empanelment_done_yesterday']??0) .'</td>'.
                                              '</tr>'.
                                            '</tbody>'.
                                          '</table>'.
                                        '</td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="height:20px;line-height:20px;"></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style=" text-align: justify; font-family: Heebo, sans-serif;font-size: 17px;line-height: 25px;font-weight: 400;color: #434343;"><u>Empanelled and active partners</u></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="height:20px;line-height:20px;"><br></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="text-align: center;">'.
                                          '<table cellpadding="0" cellspacing="0" style="border-collapse: collapse;border-spacing: 0px;margin: 0px;" width="100%">'.
                                            '<tbody>'.
                                              '<tr align="left">'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Total Empanelled Partners:</td>'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($output_arr['total_empanelled_partners']??0) .'</td>'.
                                              '</tr>'.
                                              '<tr align="left">'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Total Active Partners:</td>'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($output_arr['total_active_partners']??0) .'</td>'.
                                              '</tr>'.
                                              '<tr align="left">'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Total Active Partners in Equity + Hybrid:</td>'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($output_arr['total_active_partners_in_equity_and_hybrid_scheme']??0) .'</td>'.
                                              '</tr>'.
                                              '<tr align="left">'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Total Active Partners in Liquid:</td>'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($output_arr['total_active_partners_in_liquid_scheme']??0) .'</td>'.
                                              '</tr>'.
                                              '<tr align="left">'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 500;padding: 5px;background-color: #f5f5f5" width="75%">Total Non Active Partners:</td>'.
                                                '<td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;border: 1px solid #d4d4d4;font-weight: 400;padding: 5px;background-color: #f5f5f5; text-align:right;" width="25%">'. ($output_arr['total_non_active_partners']??0) .'</td>'.
                                              '</tr>'.
                                            '</tbody>'.
                                          '</table>'.
                                        '</td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style="height:20px;line-height:30px;"><br></td>'.
                                      '</tr>'.
                                      '<tr>'.
                                        '<td style=" text-align: justify; font-family: Heebo, sans-serif;font-size: 17px;line-height: 25px;font-weight: 400;color: #434343;">APP_URL: '. env('APP_URL') .'</td>'.
                                      '</tr>'.
                                    '</table>';

                    $mailer = new \App\Libraries\PhpMailer();
                    $params = [];
                    $template = "SAMCOMF-GENERAL-NOTIFICATION";
                    $params['templateName'] = $template;
                    $params['channel']      = $template;
                    $params['from_email']   = "alerts@samcomf.com";
                    $params['to']           = $expload_to_mail;
                    $params['attachment']   = array();
                    if(file_exists($active_partners_csv_file_path)){
                        $params['attachment'] = array_merge($params['attachment'], array(array($active_partners_csv_file_path)));
                    }
                    if(file_exists($active_partners_in_equity_and_hybrid_scheme_csv_file_path)){
                        $params['attachment'] = array_merge($params['attachment'], array(array($active_partners_in_equity_and_hybrid_scheme_csv_file_path)));
                    }
                    if(file_exists($active_partners_in_liquid_scheme_csv_file_path)){
                        $params['attachment'] = array_merge($params['attachment'], array(array($active_partners_in_liquid_scheme_csv_file_path)));
                    }
                    if(file_exists($non_active_partners_csv_file_path)){
                        $params['attachment'] = array_merge($params['attachment'], array(array($non_active_partners_csv_file_path)));
                    }
                    $params['merge_vars'] = array('MAIL_BODY' => $email_body);
                    $params['subject'] = '['. date('d M Y H:i:s') . ']: Daily Empanelment and Activation Update';
                    $email_send = $mailer->mandrill_send($params);
                    unset($email_body, $params, $mailer, $email_send, $template);
                }
                unset($expload_to_mail, $to_mail, $txtDecimalPrecision);
            }
        }
        catch(Exception $e){
            $output_arr['err_flag'] = 1;
            $output_arr['err_msg'] = 'General error: '. $e->getMessage();
        }
        catch(\Illuminate\Database\QueryException $e){
            $output_arr['err_flag'] = 1;
            $output_arr['err_msg'] = 'Query error: '. $e->getMessage();
        }
        return $output_arr;
    }
}
