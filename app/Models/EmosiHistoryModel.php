<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class EmosiHistoryModel extends Model
{
    protected $table = 'emosi_history';
    protected $fillable = ['id', 'symbol', 'record_date', 'median_beer', 'emosi_median_deviation_from_ma_1750', 'emosi_value', 'rounded_emosi', 'status', 'created_at', 'updated_at'];

    public static function get_record(){
        return self::where('status', 1);
    }

    // Helps to calculate & insert EMOSI records
    public static function emosi_history_insert_records($input_arr = array()){
        /* Possible values for $input_arr are: array('bond_symbol' => government bond symbol for whom yield value was fetched,
         *                                           'index_symbol' => index symbol for whom P/E(price to earning) & Day Closing value was fetched,
         *                                           'from_date' => date FROM which EMOSI value needs to be calculated,
         *                                           'to_date' => date TO which EMOSI value needs to be calculated,
         *                                           'calculate_for_all_dates' => whether to calculate values for all available dates. E.G: 1 = yes, 0 = no,
         *                                           'enable_query_log' => To have query log pass this parameter value as 1);
         */
        extract($input_arr);                // Import variables into the current symbol table from an array
        $output_arr = array('response' => array());
        $err_flag = 0;                  // err_flag is 0 means no error
        $err_msg = array();             // err_msg stores list of errors found during execution
        $decimalPrecision = 4;

        $flag_enable_query_log = false;
        if(isset($enable_query_log) && $enable_query_log){
            $flag_enable_query_log = true;
        }

        $median_beer_conditions = array();
        $median_deviation_conditions = array();

        $where_conditions = array();
        $where_conditions_data = array();
        if(!isset($bond_symbol) || (isset($bond_symbol) && empty($bond_symbol))){
            $err_flag = 1;
            $err_msg[] = 'Government bond symbol details not found';
        }
        else{
            $where_conditions_data[':bond_symbol'] = $bond_symbol;
            $median_beer_conditions[] = array('bond_symbol', '=', $bond_symbol);
        }

        if(!isset($index_symbol) || (isset($index_symbol) && empty($index_symbol))){
            $err_flag = 1;
            $err_msg[] = 'NSE index symbol details not found';
        }
        else{
            $where_conditions[] = array('index_symbol', '=', $index_symbol);
            $where_conditions_data[':index_symbol'] = $index_symbol;
            $median_beer_conditions[] = array('index_symbol', '=', $index_symbol);
            $median_deviation_conditions[] = array('index_symbol', '=', $index_symbol);
        }

        // default value for this variable is ZERO, if it's not available in the input parameter
        $calculate_for_all_dates = intval($calculate_for_all_dates??0);
        $str_append_query = '';
        if($calculate_for_all_dates == 0){
            if(!isset($from_date) || (isset($from_date) && empty($from_date))){
                $err_flag = 1;
                $err_msg[] = 'From date details not found';
            }
            elseif(isset($from_date) && !empty($from_date) && strtotime($from_date) === FALSE){
                $err_flag = 1;
                $err_msg[] = 'From date seems to be not in proper format';
            }
            else{
                $where_conditions[] = array('record_date', '>=', $from_date);
                $median_beer_conditions[] = array('record_date', '>=', $from_date);
                $median_deviation_conditions[] = array('record_date', '>=', $from_date);
                $where_conditions_data[':from_date'] = $from_date;
                $str_append_query .= ' AND a.record_date >= :from_date';
            }

            if(!isset($to_date) || (isset($to_date) && empty($to_date))){
                $err_flag = 1;
                $err_msg[] = 'To date details not found';
            }
            elseif(isset($to_date) && !empty($to_date) && strtotime($to_date) === FALSE){
                $err_flag = 1;
                $err_msg[] = 'To date seems to be not in proper format';
            }
            else{
                $where_conditions[] = array('record_date', '<=', $to_date);
                $median_beer_conditions[] = array('record_date', '<=', $to_date);
                $median_deviation_conditions[] = array('record_date', '<=', $to_date);
                $where_conditions_data[':to_date'] = $to_date;
                $str_append_query .= ' AND a.record_date <= :to_date';
            }
        }

        if($err_flag == 0){
            if($flag_enable_query_log){
                DB::enableQueryLog();
            }

            try{
                $current_datetime = date('Y-m-d H:i:s');

                $arr_unique_dates = array();
                // calculating BEER values for set of records

                // retrieving MEDIAN BEER datewise records used for further calculation
                $median_beer_records = EmosiBeerCalculationModel::get_record()
                                                                    ->where($median_beer_conditions)
                                                                    ->WhereNotNull('median_beer')
                                                                    ->select(array('bond_symbol', 'g_sec_yield', 'record_date', 'index_symbol', 'pe', 'median_beer'))
                                                                    ->orderBy('record_date', 'ASC')
                                                                    ->get()
                                                                    ->toArray();
                if(is_array($median_beer_records) && count($median_beer_records) > 0){
                    $median_beer_records = array_column($median_beer_records, NULL, 'record_date');
                    $arr_unique_dates = array_merge($arr_unique_dates, array_keys($median_beer_records));
                    // if($flag_enable_query_log){
                        $output_arr['response']['median_beer_records'] = $median_beer_records;
                    // }
                }

                // retrieving MEDIAN DEVIATION datewise records used for further calculation
                $median_deviation_records = EmosiMovingAverage1750CalculationModel::get_record()
                                                                        ->where($median_deviation_conditions)
                                                                        ->WhereNotNull('emosi_median_deviation_from_ma_1750')
                                                                        ->select(array('index_symbol', 'index_value', 'record_date', 'ma_1750', 'deviation_1750', 'emosi_median_deviation_from_ma_1750'))
                                                                        ->orderBy('record_date', 'ASC')
                                                                        ->get()
                                                                        ->toArray();
                if(is_array($median_deviation_records) && count($median_deviation_records) > 0){
                    $median_deviation_records = array_column($median_deviation_records, NULL, 'record_date');
                    $arr_unique_dates = array_merge($arr_unique_dates, array_keys($median_deviation_records));
                    //if($flag_enable_query_log){
                        $output_arr['response']['median_deviation_records'] = $median_deviation_records;
                    //}
                }

                // finding unique dates for an array elements, also removing blank elements
                $arr_unique_dates = array_unique(array_filter($arr_unique_dates));
                // sorting array elements in an ASCENDING order of date
                sort($arr_unique_dates);

                if(count($arr_unique_dates) > 0){
                    foreach($arr_unique_dates as $looping_date){
                        $arr_emosi_existing_records = array();
                        $arr_calculated_emosi_records = array();

                        $looping_record_err_flag = false;
                        if(!isset($median_beer_records[$looping_date]) || !isset($median_beer_records[$looping_date]['median_beer'])){
                            // checking for latest available date record of MEDIAN BEER WHERE record_date <= $looping_date
                            $retrieved_median_beer_data = EmosiBeerCalculationModel::get_record()
                                                                            ->where(
                                                                                array(
                                                                                    array('bond_symbol', '=', $bond_symbol),
                                                                                    array('index_symbol', '=', $index_symbol),
                                                                                    array('record_date', '<=', $looping_date)
                                                                                )
                                                                            )
                                                                            ->WhereNotNull('median_beer')
                                                                            ->select(array('bond_symbol', 'record_date', 'median_beer'))
                                                                            ->orderBy('record_date', 'DESC')
                                                                            ->first();
                            if($retrieved_median_beer_data){
                                $median_beer_records[$looping_date] = $retrieved_median_beer_data->toArray();
                            }
                            else{
                                // details of MEDIAN BEER not found then marking err_flag as TRUE
                                $looping_record_err_flag = true;
                            }
                            unset($retrieved_median_beer_data);
                        }
                        elseif(isset($median_beer_records[$looping_date]['median_beer']) && !is_numeric($median_beer_records[$looping_date]['median_beer'])){
                            // checking for latest available date record of MEDIAN BEER WHERE record_date <= $looping_date
                            $retrieved_median_beer_data = EmosiBeerCalculationModel::get_record()
                                                                            ->where(
                                                                                array(
                                                                                    array('bond_symbol', '=', $bond_symbol),
                                                                                    array('index_symbol', '=', $index_symbol),
                                                                                    array('record_date', '<=', $looping_date)
                                                                                )
                                                                            )
                                                                            ->WhereNotNull('median_beer')
                                                                            ->select(array('bond_symbol', 'record_date', 'median_beer'))
                                                                            ->orderBy('record_date', 'DESC')
                                                                            ->first();
                            if($retrieved_median_beer_data){
                                $median_beer_records[$looping_date] = $retrieved_median_beer_data->toArray();
                            }
                            else{
                                // details of MEDIAN BEER is not numeric then marking err_flag as TRUE
                                $looping_record_err_flag = true;
                            }
                            unset($retrieved_median_beer_data);
                        }

                        if(!isset($median_deviation_records[$looping_date]) || !isset($median_deviation_records[$looping_date]['emosi_median_deviation_from_ma_1750'])){
                            // checking for latest available date record of MEDIAN DEVIATION value WHERE record_date <= $looping_date
                            $retrieved_median_deviation_data = EmosiMovingAverage1750CalculationModel::get_record()
                                                                            ->where(
                                                                                array(
                                                                                    array('index_symbol', '=', $index_symbol),
                                                                                    array('record_date', '<=', $looping_date)
                                                                                )
                                                                            )
                                                                            ->WhereNotNull('emosi_median_deviation_from_ma_1750')
                                                                            ->select(array('index_symbol', 'record_date', 'emosi_median_deviation_from_ma_1750'))
                                                                            ->orderBy('record_date', 'DESC')
                                                                            ->first();
                            if($retrieved_median_deviation_data){
                                $median_deviation_records[$looping_date] = $retrieved_median_deviation_data->toArray();
                            }
                            else{
                                // details of MEDIAN DEVIATION value not found then marking err_flag as TRUE
                                $looping_record_err_flag = true;
                            }
                            unset($retrieved_median_deviation_data);
                        }
                        elseif(isset($median_deviation_records[$looping_date]['emosi_median_deviation_from_ma_1750']) && !is_numeric($median_deviation_records[$looping_date]['emosi_median_deviation_from_ma_1750'])){
                            // checking for latest available date record of MEDIAN DEVIATION value WHERE record_date <= $looping_date
                            $retrieved_median_deviation_data = EmosiMovingAverage1750CalculationModel::get_record()
                                                                            ->where(
                                                                                array(
                                                                                    array('index_symbol', '=', $index_symbol),
                                                                                    array('record_date', '<=', $looping_date)
                                                                                )
                                                                            )
                                                                            ->WhereNotNull('emosi_median_deviation_from_ma_1750')
                                                                            ->select(array('index_symbol', 'record_date', 'emosi_median_deviation_from_ma_1750'))
                                                                            ->orderBy('record_date', 'DESC')
                                                                            ->first();
                            if($retrieved_median_deviation_data){
                                $median_deviation_records[$looping_date] = $retrieved_median_deviation_data->toArray();
                            }
                            else{
                                // details of MEDIAN DEVIATION value is not numeric then marking err_flag as TRUE
                                $looping_record_err_flag = true;
                            }
                            unset($retrieved_median_deviation_data);
                        }

                        if(!$looping_record_err_flag){
                            $median_beer = ($median_beer_records[$looping_date]['median_beer']??0);
                            $median_beer = round($median_beer, $decimalPrecision);

                            $emosi_median_deviation_from_ma_1750 = ($median_deviation_records[$looping_date]['emosi_median_deviation_from_ma_1750']??0);
                            $emosi_median_deviation_from_ma_1750 = round($emosi_median_deviation_from_ma_1750, $decimalPrecision);

                            $emosi_value = (($median_beer * 0.70) + ($emosi_median_deviation_from_ma_1750 * 0.30));
                            $emosi_value = round($emosi_value, $decimalPrecision);

                            $rounded_emosi = round($emosi_value, 0);

                            $flag_is_it_existing_record = false;
                            $known_emosi_history_records = self::get_record()
                                                                    ->where(
                                                                        array(
                                                                            array('index_symbol', '=', $index_symbol),
                                                                            array('record_date', '=', $looping_date)
                                                                        )
                                                                    )
                                                                    ->select(array('emosi_value', 'rounded_emosi','index_symbol','median_beer','record_date','emosi_median_deviation_from_ma_1750','rounded_emosi'))
                                                                    ->get();
                            if(!$known_emosi_history_records->isEmpty()){
                                // if already an existing record for same RECORD_DATE, INDEX_SYMBOL exists but EMOSI_VALUE is different then marking earlier record as INACTIVE (status = 0)
                                $known_emosi_history_records = $known_emosi_history_records->toArray();
                                if(count($known_emosi_history_records) > 0 && isset($known_emosi_history_records[0]['emosi_value'])){
                                    if($known_emosi_history_records[0]['emosi_value'] == $emosi_value){
                                        $flag_is_it_existing_record = true;
                                        $arr_emosi_existing_records = array('index_symbol' => $known_emosi_history_records[0]['index_symbol'],
                                                                    'record_date' => $known_emosi_history_records[0]['record_date'],
                                                                    'median_beer' => $known_emosi_history_records[0]['median_beer'],
                                                                    'emosi_median_deviation_from_ma_1750' => $known_emosi_history_records[0]['emosi_median_deviation_from_ma_1750'],
                                                                    'emosi_value' => $known_emosi_history_records[0]['emosi_value'],
                                                                    'rounded_emosi' => $known_emosi_history_records[0]['rounded_emosi']);
                                    }
                                    else{
                                        // updating an record
                                        self::get_record()
                                                ->where(
                                                    array(
                                                        array('index_symbol', '=', $index_symbol),
                                                        array('record_date', '=', $looping_date),
                                                        array('status', '=', 1),
                                                        array('emosi_value', '!=', $emosi_value),
                                                    )
                                                )
                                                ->update(
                                                    array('status' => 0)
                                                );
                                    }
                                }
                            }
                            unset($known_emosi_history_records);

                            if(!$flag_is_it_existing_record){
                                $arr_calculated_emosi_records = array('index_symbol' => $index_symbol,
                                                                     'record_date' => $looping_date,
                                                                     'median_beer' => $median_beer,
                                                                     'emosi_median_deviation_from_ma_1750' => $emosi_median_deviation_from_ma_1750,
                                                                     'emosi_value' => $emosi_value,
                                                                     'rounded_emosi' => $rounded_emosi);
                                self::insertGetId($arr_calculated_emosi_records);
                            }
                            unset($median_beer, $emosi_median_deviation_from_ma_1750, $emosi_value, $emosi_value, $flag_is_it_existing_record);
                        }

                        if(count($arr_calculated_emosi_records) > 0){
                            $output_arr['response']['arr_calculated_emosi_records'][] = $arr_calculated_emosi_records;
                        }
                        unset($looping_record_err_flag, $arr_calculated_emosi_records);
                        if(count($arr_emosi_existing_records) > 0){
                            $output_arr['response']['arr_calculated_emosi_records'][] = $arr_emosi_existing_records;
                        }
                        unset($arr_emosi_existing_records);
                    }
                    unset($looping_date);
                }

                // checking how many rows got inserted in MySQL table: emosi_history
                $output_arr['response']['affected_rows'] = self::where(
                                                                    array(
                                                                        array('status', '=', 1),
                                                                        array('created_at', '>=', $current_datetime),
                                                                    )
                                                                )->count();
                unset($current_datetime);
            }
            catch(Exception $e){
                $err_flag = 1;
                $err_msg[] = 'Exception: '. $e->getMessage();
            }
            catch(\Illuminate\Database\QueryException $e){
                $err_flag = 1;
                $err_msg[] = 'Query exception: '. $e->getMessage();
            }

            if($flag_enable_query_log){
                $output_arr['query_log'] = DB::getQueryLog();
            }
        }
        unset($where_conditions, $where_conditions_data, $decimalPrecision, $str_append_query);

        $output_arr['err_flag'] = $err_flag;
        $output_arr['err_msg'] = $err_msg;
        return $output_arr;
    }

    // Helps to retrieve calculated EMOSI records
    public static function getEMOSIRecords($input_arr = array()){
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
                    case 'record_date':
                        if(isset($value['search']['value']) && !empty($value['search']['value']) && strpos($value['search']['value'], ';') !== FALSE){
                            $searching_dates = explode(';', $value['search']['value']);
                            if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE && isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                // when both FROM DATE & TO DATE both are present
                                $where_conditions[] = array('emosi_history.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                $where_conditions[] = array('emosi_history.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                            }
                            else{
                                // when either of the FROM DATE & TO DATE are present
                                if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE){
                                    $where_conditions[] = array('emosi_history.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                }
                                if(isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                    $where_conditions[] = array('emosi_history.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                                }
                            }
                            unset($searching_dates);
                        }
                        break;
                    case 'status':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                            if($value['data'] == 'status'){
                                $value['data'] = 'emosi_history.status';
                            }
                            $where_conditions[] = array($value['data'], '=', intval($value['search']['value']));
                        }
                        break;
                    case 'index_symbol':
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
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
            $order_by_clause = 'emosi_history.record_date DESC';
        }

        $records = DB::table('emosi_history')
                    ->select('emosi_history.index_symbol', 'emosi_history.status',
                             'emosi_history.median_beer', 'emosi_history.emosi_median_deviation_from_ma_1750',
                             'emosi_history.emosi_value', 'emosi_history.rounded_emosi',
                             (!$flag_export_data?'emosi_history.record_date':DB::raw('DATE_FORMAT(emosi_history.record_date, "%d/%m/%Y") AS record_date')),
                             (!$flag_export_data?'emosi_history.created_at':DB::raw('DATE_FORMAT(emosi_history.created_at, "%d/%m/%Y") AS created_at'))
                            );
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

            $records = $records->orderByRaw($order_by_clause)->get();
            unset($logged_in_user_roles_and_permissions, $flag_have_all_permissions);
        }
        catch(Exception $e){
            // if SQL query exception occurs then not showing any records
            $records = array();
        }
        unset($where_conditions, $where_in_conditions, $order_by_clause);
        return array('records' => $records, 'no_of_records' => $no_of_records);

    }
}
