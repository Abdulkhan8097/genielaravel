<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class AumdataModel extends Model
{
    use HasFactory;
    public static function getARNAumList($input_arr = array()){
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
                    case 'created_at':
                        if(isset($value['search']['value']) && !empty($value['search']['value']) && strpos($value['search']['value'], ';') !== FALSE){
                            $searching_dates = explode(';', $value['search']['value']);
                            if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE && isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                // when both FROM DATE & TO DATE both are present
                                $where_conditions[] = array('aumdata.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                $where_conditions[] = array('aumdata.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                            }
                            else{
                                // when either of the FROM DATE & TO DATE are present
                                if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE){
                                    $where_conditions[] = array('aumdata.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                }
                                if(isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                    $where_conditions[] = array('aumdata.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                                }
                            }
                            unset($searching_dates);
                        }
                    break;
                    case 'aum_year':
                            if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                                if($value['data'] == 'aum_year'){
                                    $value['data'] = 'aumdata.aum_year';
                                }
                                $where_conditions[] = array($value['data'], '=', intval($value['search']['value']));
                            }else{
                               // $where_conditions[] = array($value['data'], '=', '');
                            }
                    break;
                    case 'status':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                            if($value['data'] == 'status'){
                                $value['data'] = 'aumdata.status';
                            }
                            $where_conditions[] = array($value['data'], '=', intval($value['search']['value']));
                        }
                        break;
                    case 'ARN':
                    case 'arn_business_focus_type':
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
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
            $order_by_clause = 'aumdata.ARN ASC';
        }

        $records = DB::table('drm_uploaded_arn_average_aum_total_commission_data AS aumdata')
                    ->select('aumdata.ARN', 'aumdata.arn_avg_aum', 'aumdata.arn_total_commission', 'aumdata.arn_yield',
                             'aumdata.arn_business_focus_type', 'aumdata.status','aumdata.aum_year',
                             (!$flag_export_data?'aumdata.created_at':DB::raw('DATE_FORMAT(aumdata.created_at, "%d/%m/%Y") AS created_at')));
        if(count($where_conditions) > 0){
            $records = $records->where($where_conditions);
        }

        $no_of_records = 0;
        if(!$flag_export_data){
            // calculating number of records without using LIMIT & OFFSET for pagination, only when showing data inside DataTable
            try{
                $no_of_records = $records->where($where_conditions)->count();
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
                return array('where_conditions' => $where_conditions, 'order_by_clause' => $order_by_clause);
            }

            $records = $records->orderByRaw($order_by_clause)->get();
            if(!$records->isEmpty()){
                foreach($records as $key => $value){
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
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
        }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        
        unset($where_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getAumTransactionAnalytics($input_arr = array()){
        ini_set('memory_limit', -1);
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
                                $ARN = $value['search']['value'];
                                if($value['data'] == 'ARN' && isset($exact_arn_match) && (intval($exact_arn_match) == 1)){
                                    $where_conditions[] = array($value['data'], '=', $value['search']['value']);
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
        $order_by_field = "agentcode";
        if(isset($order[0]) && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
            if($columns[$order[0]['column']]['data'] == 'total_netflow'){
            //    $order_by_field = DB::connection("rankmf")->raw('(total_gross_inflow - total_redemptions)');
            }
            elseif($columns[$order[0]['column']]['data'] == 'total_aum'){
            //    $order_by_field = DB::connection("rankmf")->raw('((purchased_units - redeemed_units) * latest_nav)');
            }
            else{
                $order_by_field = $columns[$order[0]['column']]['data'];
            }
        }
        
        $dir = "ASC";
        if(isset($order[0]) && isset($order[0]['dir'])) {
            //$dir = $order[0]['dir'];
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
        if($scheme_filter){
            $arr_groupby_fields[] = 'asset_type';
        }
        $arr_select_fields = array(
            "asset_type", 'mpr.ARN', "agentcode AS agent_code", 
            DB::connection("rankmf")->raw("YEAR(trxn_date) AS trdt_year"),
            DB::connection("rankmf")->raw("SUM(CASE WHEN trxn_type_name IN ('REDEMPTION', 'DIVIDEND PAYOUT', 'SWITCH OUT', 'TRANFER OUT') AND sub_trxntype_name = '' THEN ROUND(amount, 2) ELSE 0 END) AS total_redemptions"),
            DB::connection("rankmf")->raw("SUM(CASE WHEN trxn_type_name IN ('PURCHASE', 'DIVIDEND REINVESTMENT', 'SWITCH IN', 'TRANSFER IN', 'NFO') THEN ROUND(amount, 2) ELSE 0 END) AS total_gross_inflow"),
            DB::connection("rankmf")->raw("SUM(CASE WHEN trxn_type_name IN ('PURCHASE', 'DIVIDEND REINVESTMENT', 'SWITCH IN', 'TRANSFER IN', 'NFO') THEN ROUND(amount, 2) ELSE 0 END) - SUM(CASE WHEN trxn_type_name IN ('REDEMPTION', 'DIVIDEND PAYOUT', 'SWITCH OUT', 'TRANFER OUT') AND sub_trxntype_name = '' THEN ROUND(amount, 2) ELSE 0 END) AS total_netflow"),
            DB::connection("rankmf")->raw("'' AS total_aum")
        );

        $arr_groupby_fields = array('trdt_year');
        if($scheme_filter){
            $arr_groupby_fields[] = 'asset_type';
        }

        // preparing SELECT & GROUP BY CLAUSE based on specific conditions
        switch($view_to_be_loaded){
            case 'month_wise_data':
                $arr_select_fields[] = DB::connection("rankmf")->raw("MONTH(trxn_date) AS trdt_month");
                $arr_groupby_fields[] = 'trdt_month';
                break;
            case 'day_wise_data':
                $arr_select_fields[] = DB::connection("rankmf")->raw("MONTH(trxn_date) AS trdt_month");
                $arr_select_fields[] = DB::connection("rankmf")->raw("DAY(trxn_date) AS trdt_day");
                $arr_select_fields[] = DB::connection("rankmf")->raw("DATE(trxn_date) AS trdt_date");
                $arr_groupby_fields[] = 'trdt_month';    
                $arr_groupby_fields[] = 'trdt_day';    
                break;
            case 'date_wise_data':
                $arr_select_fields = array(
                    "schemename", 'folio_number', 'clientname', 'trxn_type_name', 'sub_trxntype_name', 'units', 'amount', 'nav', 'asset_type', DB::connection("rankmf")->raw("DATE(trxn_date) AS trdt_date"), 'mpr.ARN', "agentcode AS agent_code", 
                    DB::connection("rankmf")->raw("YEAR(trxn_date) AS trdt_year"),
                    DB::connection("rankmf")->raw("'' AS total_aum")
                );
                $arr_groupby_fields = [];
            break;
        }
            
        $records = DB::connection("rankmf")
                    ->table('mf_transaction_data')
                    ->join("mutual_fund_partners.mfp_partner_registration as mpr", "mpr.partner_code", "=", "mf_transaction_data.agentcode")
                    ->select($arr_select_fields);
        if(is_array($arr_groupby_fields) && count($arr_groupby_fields) > 0){
            $records = $records->groupBy($arr_groupby_fields);
        }
        unset($arr_select_fields, $arr_groupby_fields);

        if(isset($asset_type) && !empty($asset_type)){
            $records = $records->where('asset_type','=',$asset_type);
        }
        
        if($view_to_be_loaded == 'date_wise_data'){
            if(isset($global_search) && !empty($global_search) ){
                $records = $records->where('folio_number', 'LIKE', '%'.$global_search.'%')->orWhere('clientname', 'LIKE', '%'.$global_search.'%');
            }
            
            if(isset($order_type) && !empty($order_type) ){
                $records = $records->where('trxn_type_name', 'LIKE', '%'.$order_type.'%')->orWhere('sub_trxntype_name', 'LIKE', '%'.$order_type.'%');
            }
        }

        $aum_year = ($aum_year??date('Y'));
        if(isset($aum_year) && is_numeric($aum_year) && empty($selected_date)){
            $records = $records->whereYear('trxn_date','=',$aum_year);
        }

        if(isset($aum_month) && is_numeric($aum_month)){
            $records = $records->whereMonth('trxn_date','=',$aum_month);
        }

        if(isset($selected_date) && !empty($selected_date)){
            $where_conditions[] = array('trxn_date', '>=', $selected_date .' 00:00:00');
            $where_conditions[] = array('trxn_date', '<=', $selected_date .' 23:59:59');
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
            $records = $records->whereRaw("ARN = '". $ARN ."'");
        }
        //echo $records->toSql();die;
        // $records = $records->orderBy($order_by_field,$dir);
        if(!$scheme_filter){
            switch($view_to_be_loaded){
                case 'month_wise_data':
                    // $records = DB::table(DB::raw('('. $records->toSql() .') AS scheme_wise_transactions'))->selectRaw("'' AS scheme_code, '' AS agent_code, trdt_year, trdt_month, SUM(scheme_wise_transactions.total_gross_inflow) AS total_gross_inflow, SUM(scheme_wise_transactions.total_redemptions) AS total_redemptions, SUM(scheme_wise_transactions.total_netflow) AS total_netflow, '' AS total_aum")->mergeBindings($records)->groupBy('trdt_year', 'trdt_month');
                    $records = DB::table($records, 'scheme_wise_transactions')->select(array(DB::raw("'' AS asset_type"), "trdt_year", "trdt_month", DB::raw("scheme_wise_transactions.ARN AS ARN"), DB::raw("scheme_wise_transactions.total_gross_inflow AS total_gross_inflow"), DB::raw("scheme_wise_transactions.total_redemptions AS total_redemptions"), DB::raw("scheme_wise_transactions.total_netflow AS total_netflow"), DB::raw("'' AS total_aum")))->groupBy('trdt_year', 'trdt_month');
                    break;
                case 'day_wise_data':
                    // $records = DB::table(DB::raw('('. $records->toSql() .') AS scheme_wise_transactions'))->selectRaw("'' AS scheme_code, '' AS agent_code, trdt_year, trdt_month, trdt_day,trdt_date, SUM(scheme_wise_transactions.total_gross_inflow) AS total_gross_inflow, SUM(scheme_wise_transactions.total_redemptions) AS total_redemptions, SUM(scheme_wise_transactions.total_netflow) AS total_netflow, '' AS total_aum")->mergeBindings($records)->groupBy('trdt_year', 'trdt_month', 'trdt_day');
                    $records = DB::table($records, 'scheme_wise_transactions')->select(array(DB::raw("'' AS asset_type"), 'trdt_year', 'trdt_month', 'trdt_day', 'trdt_date', DB::raw("scheme_wise_transactions.ARN AS ARN"), DB::raw("scheme_wise_transactions.total_gross_inflow AS total_gross_inflow"), DB::raw("scheme_wise_transactions.total_redemptions AS total_redemptions"), DB::raw("scheme_wise_transactions.total_netflow AS total_netflow"), DB::raw("'' AS total_aum")))->groupBy('trdt_year', 'trdt_month', 'trdt_day');
                    break;
                case 'date_wise_data':
                    break;
                default:
                    // $records = DB::table(DB::raw('('. $records->toSql() .') AS scheme_wise_transactions'))->selectRaw("'' AS scheme_code, '' AS agent_code, SUM(scheme_wise_transactions.total_gross_inflow) AS total_gross_inflow, SUM(scheme_wise_transactions.total_redemptions) AS total_redemptions, SUM(scheme_wise_transactions.total_netflow) AS total_netflow, '' AS total_aum")->mergeBindings($records);
                    $records = DB::table($records, 'scheme_wise_transactions')->selectRaw("'' AS asset_type, scheme_wise_transactions.ARN AS ARN, scheme_wise_transactions.total_gross_inflow AS total_gross_inflow, scheme_wise_transactions.total_redemptions AS total_redemptions, scheme_wise_transactions.total_netflow AS total_netflow, '' AS total_aum");
            }
        }
        //echo $records->toSql();die;
        // DB::enableQueryLog();
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
            
            $records = $records->get();
            
            if(!$records->isEmpty()){   
                //$get_scheme_code = self::getSchemeDetailbyCode();
                //$get_scheme_code = array_column($get_scheme_code, NULL, 'RTA_Scheme_Code');
                $get_aum_details = array();
                switch($view_to_be_loaded){
                    case 'month_wise_data':
                       // $get_aum_details = self::getAumForMonthwise($scheme_filter,$ARN, $aum_year.'-01-01',$scheme_code);
                        break;
                    case 'year_wise_data':
                       // $get_aum_details = self::getAumForARN($scheme_filter,$ARN, $aum_year.'-12-31');
                        break;
                    case 'day_wise_data':
                        $arr_transaction_dates = array();
                        foreach($records as $_key => $_value){
                            if(isset($_value->trdt_date) && !empty($_value->trdt_date) && strtotime($_value->trdt_date) !== FALSE && in_array($_value->trdt_date, $arr_transaction_dates) === FALSE){
                                $arr_transaction_dates[] = $_value->trdt_date;
                            }
                        }
                        // y($arr_transaction_dates, 'arr_transaction_dates');
                     //   $get_aum_details = self::getAumForDayWise($scheme_filter,$ARN, $arr_transaction_dates,$scheme_code);
                        // y($get_aum_details, '$get_aum_details');
                        unset($arr_transaction_dates);
                        break;
                }

                $aum_result = [];
                $asset_type_string = '';
                if($view_to_be_loaded == 'year_wise_data'){
                    $arn_number = $ARN;

                    if($aum_year == date('Y')){
                        $start = new \Datetime("-31 days");
                        $start = $start->format('Y-m-d')." 00:00:00";
                        $start_date = new \MongoDB\BSON\UTCDateTime(strtotime($start) * 1000);

                        $end = new \Datetime("-1 days");
                        $end = $end->format('Y-m-d')." 23:59:59";
                        $end_date = new \MongoDB\BSON\UTCDateTime(strtotime($end) * 1000);
                    }
                    else{
                        $start = $aum_year."-12-01 00:00:00";
                        $start_date = new \MongoDB\BSON\UTCDateTime(strtotime($start) * 1000);

                        $end = $aum_year."-12-31 23:59:59";
                        $end_date = new \MongoDB\BSON\UTCDateTime(strtotime($end) * 1000);
                    }

                    if($scheme_filter == 1){
                        $match = [
                            '$match' => [
                                'date' => [
                                    '$gte' => $start_date,
                                    '$lte' => $end_date,
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
                                'equity' => ['$last' => '$equity'],
                                'debt' => ['$last' => '$debt'],
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
                                'hybrid' => 1,
                                'commodity' => 1,
                            ],
                        ];
                    }
                    else{
                        $match = [
                            '$match' => [
                                'date' => [
                                    '$gte' => $start_date,
                                    '$lte' => $end_date,
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
                else if($view_to_be_loaded == 'month_wise_data'){
                    $arn_number = $ARN;
                    $asset_type_string = strtolower($asset_type);

                    $start = $aum_year."-01-31 00:00:00";
                    $start_date = new \MongoDB\BSON\UTCDateTime(strtotime($start) * 1000);

                    if($aum_year == date('Y')){
                        $end = date('Y-m-d')." 23:59:59";
                        $end_date = new \MongoDB\BSON\UTCDateTime(strtotime($end) * 1000);
                    }
                    else{
                        $end = $aum_year."-12-31 23:59:59";
                        $end_date = new \MongoDB\BSON\UTCDateTime(strtotime($end) * 1000);
                    }

                    $sort = [
                        '$sort' => [
                            'date' => 1
                        ]
                    ];

                    $limit = [
                        '$limit' => 10
                    ];

                    if($scheme_filter == 1){
                        $match = [
                            '$match' => [
                                'date' => [
                                    '$gte' => $start_date,
                                    '$lte' => $end_date,
                                ],
                                'arn' => $arn_number
                            ],
                        ];

                        $group = [
                            '$group' => [
                                '_id' => [
                                    'year' => ['$year' => '$date'],
                                    'month' => ['$month' => '$date'],
                                    'client' => '$client',
                                ],
                                "$asset_type_string" => ['$last' => "$$asset_type_string"],
                                'date'      => ['$last' => '$date'],
                            ],
                        ];

                        $project = [
                            '$project' => [
                                '_id' => 0,
                                'year' => '$_id.year',
                                'month' => '$_id.month',
                                'client' => '$_id.client',
                                "$asset_type_string" => 1,
                                'date'  => 1
                            ],
                        ];
                    }
                    else{
                        $match = [
                            '$match' => [
                                'date' => [
                                    '$gte' => $start_date,
                                    '$lte' => $end_date,
                                ],
                                'arn' => $arn_number
                            ],
                        ];
                        
                        $group = [
                            '$group' => [
                                '_id' => [
                                    'year' => ['$year' => '$date'],
                                    'month' => ['$month' => '$date'],
                                    'client' => '$client',
                                ],
                                'total_aum' => ['$last' => '$total_aum'],
                                'date'      => ['$last' => '$date'],
                            ],
                        ];

                        $project = [
                            '$project' => [
                                '_id' => 0,
                                'year' => '$_id.year',
                                'month' => '$_id.month',
                                'client' => '$_id.client',
                                'total_aum' => 1,
                                'date'  => 1
                            ],
                        ];
                    }
                    
                    $aum_result = DB::connection('mongodb')->collection('mf_aum_nav_data')->raw(function ($collection) use($start_date, $end_date, $arn_number, $match, $group, $project, $sort, $limit) {
                        return $collection->aggregate([
                            $match,
                            $group,
                            $project,
                            $sort,
                            //$limit
                        ]);
                    })->toArray();
                }
                else if($view_to_be_loaded == 'day_wise_data'){
                    $arn_number = $ARN;
                    $asset_type_string = strtolower($asset_type);

                    $start = $aum_year."-".$aum_month."-01 00:30:00";
                    $start_date = new \MongoDB\BSON\UTCDateTime(strtotime($start) * 1000);

                    $a_date = $aum_year."-".$aum_month."-01";
                    $last_date = date("Y-m-t", strtotime($a_date));

                    $end = $last_date." 23:59:59";
                    $end_date = new \MongoDB\BSON\UTCDateTime(strtotime($end) * 1000);

                    $sort = [
                        '$sort' => [
                            'date' => 1
                        ]
                    ];

                    $limit = [
                        '$limit' => 10
                    ];

                    if($scheme_filter == 1){
                        $match = [
                            '$match' => [
                                'date' => [
                                    '$gte' => $start_date,
                                    '$lte' => $end_date,
                                ],
                                'arn' => $arn_number
                            ],
                        ];

                        $group = [
                            '$group' => [
                                '_id' => [
                                    'date' => '$date',
                                    'client' => '$client',
                                ],
                                "$asset_type_string" => ['$last' => "$$asset_type_string"],
                                'date'      => ['$last' => '$date'],
                            ],
                        ];

                        $project = [
                            '$project' => [
                                '_id' => 0,
                                'date' => '$_id.date',
                                'client' => '$_id.client',
                                "$asset_type_string" => 1,
                                'date'  => 1
                            ],
                        ];
                    }
                    else{
                        $match = [
                            '$match' => [
                                'date' => [
                                    '$gte' => $start_date,
                                    '$lte' => $end_date,
                                ],
                                'arn' => $arn_number
                            ],
                        ];
                        
                        $group = [
                            '$group' => [
                                '_id' => [
                                    'date' => '$date',
                                    'client' => '$client',
                                ],
                                'total_aum' => ['$last' => '$total_aum'],
                                'date'      => ['$last' => '$date'],
                            ],
                        ];

                        $project = [
                            '$project' => [
                                '_id' => 0,
                                'date' => '$_id.date',
                                'client' => '$_id.client',
                                'total_aum' => 1,
                            ],
                        ];
                    }
                    
                    $aum_result = DB::connection('mongodb')->collection('mf_aum_nav_data')->raw(function ($collection) use($start_date, $end_date, $arn_number, $match, $group, $project, $sort, $limit) {
                        return $collection->aggregate([
                            $match,
                            $group,
                            $project,
                            $sort,
                            //$limit
                        ]);
                    })->toArray();
                }

                $total_aum = $total_equity_aum = $total_debt_aum = $total_commodity_aum = $total_hybrid_aum = $total_other_aum = 0;

                $total_aum_month_wise_arr = $total_date_wise_aum = [];
                $month_arr = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov' , 'Dec'];

                
                if(!empty($aum_result) && count($aum_result) > 0 && is_array($aum_result)){
                    if($view_to_be_loaded == 'year_wise_data'){
                        if($scheme_filter == 1){
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
                    else if($view_to_be_loaded == 'month_wise_data'){
                        if($scheme_filter == 0){
                            foreach ($aum_result as $key => $value) {
                                $value['month'] = (int) $value['month'];

                                $date_array = (array) $value['date'];
                                $date_strtotime = ($date_array['milliseconds'] / 1000);
                                $trdt_date = date('Y-m-d', $date_strtotime);
                                $trdt_month = date('m', strtotime($trdt_date));

                                if(!empty($total_aum_month_wise_arr[$trdt_month]['total_aum'])){
                                    $total_aum_month_wise_arr[$trdt_month]['total_aum'] = $total_aum_month_wise_arr[$trdt_month]['total_aum'] + $value['total_aum'];
                                }
                                else{
                                    $total_aum_month_wise_arr[$trdt_month]['total_aum'] = $value['total_aum'];
                                }
                            }
                        }
                        else{
                            foreach ($aum_result as $key => $value) {
                                $value['month'] = (int) $value['month'];

                                $date_array = (array) $value['date'];
                                $date_strtotime = ($date_array['milliseconds'] / 1000);
                                $trdt_date = date('Y-m-d', $date_strtotime);
                                $trdt_month = date('m', strtotime($trdt_date));

                                if(!empty($total_aum_month_wise_arr[$trdt_month][$asset_type_string])){
                                    $total_aum_month_wise_arr[$trdt_month][$asset_type_string] = $total_aum_month_wise_arr[$trdt_month][$asset_type_string] + $value[$asset_type_string];
                                }
                                else{
                                    $total_aum_month_wise_arr[$trdt_month][$asset_type_string] = $value[$asset_type_string];
                                }
                            }
                        }
                    }
                    else if($view_to_be_loaded == 'day_wise_data'){
                        
                        if($scheme_filter == 0){
                            foreach ($aum_result as $key => $value) {
                                $date_array = (array) $value['date'];
                                $date_strtotime = ($date_array['milliseconds'] / 1000);
                                $trdt_date = date('Y-m-d', $date_strtotime);

                                if(!empty($total_date_wise_aum[$trdt_date]['total_aum'])){
                                    $total_date_wise_aum[$trdt_date]['total_aum'] = $total_date_wise_aum[$trdt_date]['total_aum'] + $value['total_aum'];
                                }
                                else{
                                    $total_date_wise_aum[$trdt_date]['total_aum'] = $value['total_aum'];
                                }
                            }
                        }
                        else{
                            foreach ($aum_result as $key => $value) {
                                $date_array = (array) $value['date'];
                                $date_strtotime = ($date_array['milliseconds'] / 1000);
                                $trdt_date = date('Y-m-d', $date_strtotime);
                                
                                if(!empty($total_date_wise_aum[$trdt_date][$asset_type_string])){
                                    $total_date_wise_aum[$trdt_date][$asset_type_string] = $total_date_wise_aum[$trdt_date][$asset_type_string] + $value[$asset_type_string];
                                }
                                else{
                                    $total_date_wise_aum[$trdt_date][$asset_type_string] = $value[$asset_type_string];
                                }
                            }
                        }
                    }
                }
                
                foreach($records as $key => $value){
                    if(!$flag_export_data){
                        // preparing list of actionable buttons which will be helpful to perform EDIT/ACTIVE/INACTIVE record operations
                        switch($view_to_be_loaded){
                            case 'month_wise_data':
                                $value->action  = "<a href='javascript:void(0);' title='View Daywise data' style='display:flex;align-items:center;' onclick=\"load_aum_transaction_datatable('day_wise_data', '". $value->asset_type ."', '". $value->trdt_month ."')\"><img src='". env('APP_URL') ."/images/view-icon.png' style='margin-right:7px;' title='View Daywise data'>View</a>";
                            break;
                            case 'day_wise_data':
                                $value->action  = "<a href='javascript:void(0);' title='View Datewise data' style='display:flex;align-items:center;' onclick=\"load_aum_transaction_datatable('date_wise_data', '". $value->asset_type ."', '". $value->trdt_month ."', '". $value->trdt_day ."')\"><img src='". env('APP_URL') ."/images/view-icon.png' style='margin-right:7px;' title='View Datewise data'>View</a>";
                            break;
                            case 'date_wise_data':
                                $value->action  = "";
                            break;
                            default:
                                $value->action  = "<a href='javascript:void(0);' title='View monthwise data' style='display:flex;align-items:center;' onclick=\"load_aum_transaction_datatable('month_wise_data', '". $value->asset_type ."')\"><img src='". env('APP_URL') ."/images/view-icon.png' style='margin-right:7px;' title='View monthwise data'>View</a>";
                        }
                    }

                    switch($view_to_be_loaded){
                        case 'year_wise_data':
                            if($scheme_filter == 1){
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
                        break;

                        case 'month_wise_data':
                            $value->total_aum = 0;
                            if($scheme_filter == 1){
                                if(!empty($total_aum_month_wise_arr[$value->trdt_month][$asset_type_string])){
                                    $value->total_aum = $total_aum_month_wise_arr[$value->trdt_month][$asset_type_string];
                                }
                            }
                            else{
                                if(!empty($total_aum_month_wise_arr[$value->trdt_month]['total_aum'])){
                                    $value->total_aum = $total_aum_month_wise_arr[$value->trdt_month]['total_aum'];
                                }
                            }
                        break;
                        
                        case 'day_wise_data':
                            $value->total_aum = 0;
                            if($scheme_filter == 1){
                                if(!empty($total_date_wise_aum[$value->trdt_date][$asset_type_string])){
                                    $value->total_aum = $total_date_wise_aum[$value->trdt_date][$asset_type_string];
                                }
                            }
                            else{
                                if(!empty($total_date_wise_aum[$value->trdt_date]['total_aum'])){
                                    $value->total_aum = $total_date_wise_aum[$value->trdt_date]['total_aum'];
                                }
                            }

                            if(isset($value->trdt_date) && !empty($value->trdt_date) && strtotime($value->trdt_date) !== FALSE && is_array($get_aum_details) && isset($get_aum_details[$value->trdt_date]) && isset($get_aum_details[$value->trdt_date]->total_aum) && !empty($get_aum_details[$value->trdt_date]->total_aum)){
                                $value->total_aum = $get_aum_details[$value->trdt_date]->total_aum;
                            }
                        break;
                        default:
                            $value->total_aum = 0;
                            if($scheme_filter){
                                // showing data scheme wise
                                if(is_array($get_aum_details) && isset($get_aum_details[$value->asset_type]) && isset($get_aum_details[$value->asset_type]->scheme_wise_aum) && !empty($get_aum_details[$value->scheme_code]->scheme_wise_aum)){
                                    //$value->total_aum = $get_aum_details[$value->scheme_code]->scheme_wise_aum;
                                }
                            }
                            else{
                                // showing data for all schemes
                                if(is_array($get_aum_details) && isset($get_aum_details[0]) && isset($get_aum_details[0]->scheme_wise_aum) && !empty($get_aum_details[0]->scheme_wise_aum)){
                                   // $value->total_aum = $get_aum_details[0]->scheme_wise_aum;
                                }
                            }
                    }
                    if(isset($value->asset_type) && !empty($value->asset_type)){
                        $value->asset_type = $value->asset_type;
                    }
                    if(isset($value->scheme_code) && !empty($value->scheme_code) && isset($get_scheme_code[$value->scheme_code]) && isset($get_scheme_code[$value->scheme_code]->meta_title) && !empty($get_scheme_code[$value->scheme_code]->meta_title)){
                    //   $value->scheme_code = $get_scheme_code[$value->scheme_code]->meta_title;
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

}
