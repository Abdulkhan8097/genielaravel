<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class EmosiMovingAverage1750CalculationModel extends Model
{
    protected $table = 'emosi_moving_average_1750_calculation';
    protected $fillable = ['id', 'symbol', 'record_date', 'index_value', 'ma_1750', 'deviation_1750', 'emosi_median_deviation_from_ma_1750', 'status', 'created_at', 'updated_at'];

    public static function get_record(){
        return self::where('status', 1);
    }

    // Helps to calculate & insert Median Deviation records
    public static function calculate_median_deviation_from_ma1750($input_arr = array()){
        /* Possible values for $input_arr are: array('index_symbol' => index symbol for whom P/E(price to earning) value needs to be fetched,
         *                                           'from_date' => date FROM which median DEVIATION value needs to be calculated,
         *                                           'to_date' => date TO which median DEVIATION value needs to be calculated,
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

        $nse_index_conditions = array();

        if(!isset($index_symbol) || (isset($index_symbol) && empty($index_symbol))){
            $err_flag = 1;
            $err_msg[] = 'NSE index symbol details not found';
        }
        else{
            $quote_index_symbol = $index_symbol;
            if(strtolower($quote_index_symbol) == 'nifty_50'){
                $quote_index_symbol = '-21';
            }
            $nse_index_conditions[] = array('symbol', '=', $quote_index_symbol);
        }

        // default value for this variable is ZERO, if it's not available in the input parameter
        $calculate_for_all_dates = intval($calculate_for_all_dates??0);
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
                $nse_index_conditions[] = array('index_date', '>=', $from_date);
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
                $nse_index_conditions[] = array('index_date', '<=', $to_date);
            }
        }

        if($err_flag == 0){
            if($flag_enable_query_log){
                DB::enableQueryLog();
            }

            try{
                $arr_unique_dates = array();
                // calculating MEDIAN DEVIATION FROM MOVING AVERAGE OF 1750 set of records

                // retrieving index symbol datewise records used for further calculation
                $nse_index_records = QuoteDataIndexHistory::where($nse_index_conditions)
                                                                ->select(array('symbol', 'index_date', 'close'))
                                                                ->orderBy('index_date', 'ASC')
                                                                ->get()
                                                                ->toArray();
                if(is_array($nse_index_records) && count($nse_index_records) > 0){
                    $nse_index_records = array_column($nse_index_records, NULL, 'index_date');
                    $arr_unique_dates = array_merge($arr_unique_dates, array_keys($nse_index_records));
                    if($flag_enable_query_log){
                        $output_arr['response']['nse_index_records'] = $nse_index_records;
                    }
                }

                // finding unique dates for an array elements, also removing blank elements
                $arr_unique_dates = array_unique(array_filter($arr_unique_dates));
                // sorting array elements in an ASCENDING order of date
                sort($arr_unique_dates);

                if(count($arr_unique_dates) > 0){
                    foreach($arr_unique_dates as $looping_date){
                        // retrieving AVG closing price from $looping_date till last 1750 records
                        // if we don't have 1750 records prior to $looping_date then keep value NULL for fields like ma_1750, deviation_1750, emosi_median_deviation_from_ma_1750
                        $flag_prior_1750_available = false;
                        $moving_average_records = DB::table(function($query) use($quote_index_symbol, $looping_date){
                            $query->from('quote_data_index_history')
                                  ->where(
                                        array(
                                            array('symbol', '=', $quote_index_symbol),
                                            array('index_date', '<=', $looping_date),
                                        )
                                  )
                                  ->select(array('id', 'close'))
                                  ->orderBy('index_date', 'DESC')
                                  ->limit(1750);
                        }, 'index_values')->select(array(DB::raw('COUNT(id) AS no_of_records'), DB::raw('AVG(close) AS avg_close')))->get();
                        if(!$moving_average_records->isEmpty()){
                            $moving_average_records = $moving_average_records->toArray();
                            if(isset($moving_average_records[0]) && isset($moving_average_records[0]->no_of_records) && (intval($moving_average_records[0]->no_of_records) >= 1750)){
                                $flag_prior_1750_available = true;
                            }
                        }

                        $arr_calculated_moving_average_records = array('index_symbol' => $index_symbol,
                                                                       'record_date' => $looping_date,
                                                                       'index_value' => ($nse_index_records[$looping_date]['close']??null),
                                                                       'ma_1750' => 0);
                        if(isset($arr_calculated_moving_average_records['index_value']) && !empty($arr_calculated_moving_average_records['index_value']) && is_numeric($arr_calculated_moving_average_records['index_value'])){
                            $arr_calculated_moving_average_records['index_value'] = round($arr_calculated_moving_average_records['index_value'], $decimalPrecision);
                        }
                        if($flag_prior_1750_available){
                            $arr_calculated_moving_average_records['deviation_1750'] = 0;
                            $arr_calculated_moving_average_records['emosi_median_deviation_from_ma_1750'] = 0;

                            // coming here as we 1750 records available prior to the $looping_date
                            if(isset($moving_average_records[0]) && isset($moving_average_records[0]->avg_close) && !empty($moving_average_records[0]->avg_close) && is_numeric($moving_average_records[0]->avg_close)){
                                $arr_calculated_moving_average_records['ma_1750'] = $moving_average_records[0]->avg_close;
                                $arr_calculated_moving_average_records['ma_1750'] = round($arr_calculated_moving_average_records['ma_1750'], $decimalPrecision);
                                $arr_calculated_moving_average_records['deviation_1750'] = ($arr_calculated_moving_average_records['index_value']/$arr_calculated_moving_average_records['ma_1750']);
                                $arr_calculated_moving_average_records['deviation_1750'] = round($arr_calculated_moving_average_records['deviation_1750'], $decimalPrecision);
                            }

                            // calculating MEDIAN from the available set of records
                            $arr_median_values = array();     // stores list of all MEDIAN DEVIATION values which further used for calculating MEDIAN from those available set of values
                            $retrieved_median_values = self::get_record()
                                                            ->where(array(
                                                                        array('index_symbol', '=', $index_symbol),
                                                                        array('record_date', '<', $looping_date)
                                                                    )
                                                                )
                                                            ->whereNotNull('deviation_1750')
                                                            ->select(array('record_date', 'deviation_1750'))
                                                            ->orderBy('record_date', 'ASC')
                                                            ->get();
                            if(!$retrieved_median_values->isEmpty()){
                                $retrieved_median_values = $retrieved_median_values->toArray();
                                if(count($retrieved_median_values) > 0 && array_column($retrieved_median_values, 'deviation_1750') > 0){
                                    $arr_median_values = array_merge($arr_median_values, array_column($retrieved_median_values, 'deviation_1750'));
                                }
                            }
                            unset($retrieved_median_values);

                            $arr_median_values = array_merge($arr_median_values, array($arr_calculated_moving_average_records['deviation_1750']));
                            $arr_calculated_moving_average_records['emosi_median_deviation_from_ma_1750'] = ((!empty($arr_calculated_moving_average_records['deviation_1750'])) ? ((getMedian($arr_median_values) / $arr_calculated_moving_average_records['deviation_1750']) * 100) : 0);
                            $arr_calculated_moving_average_records['emosi_median_deviation_from_ma_1750'] = round($arr_calculated_moving_average_records['emosi_median_deviation_from_ma_1750'], $decimalPrecision);
                            unset($arr_median_values);
                        }
                        unset($moving_average_records);

                        $flag_is_it_existing_record = false;
                        $known_median_deviation_record = self::get_record()
                                                                ->where(
                                                                    array(
                                                                        array('record_date', '=', $looping_date),
                                                                        array('index_symbol', '=', $index_symbol)
                                                                    )
                                                                )
                                                                ->select(array(DB::raw('IFNULL(ma_1750, 0) AS ma_1750'), 'deviation_1750'))
                                                                ->get();
                        if(!$known_median_deviation_record->isEmpty()){
                            // if already an existing record for same RECORD_DATE, INDEX_SYMBOL exists but MOVING AVERAGE value is different then marking earlier record as INACTIVE (status = 0)
                            $known_median_deviation_record = $known_median_deviation_record->toArray();
                            if(count($known_median_deviation_record) > 0 && isset($known_median_deviation_record[0]['ma_1750'])){
                                if($known_median_deviation_record[0]['ma_1750'] == $arr_calculated_moving_average_records['ma_1750']){
                                    $flag_is_it_existing_record = true;
                                }
                                else{
                                    // updating an record
                                    self::get_record()
                                            ->where(
                                                array(
                                                    array('record_date', '=', $looping_date),
                                                    array('index_symbol', '=', $index_symbol),
                                                    array('status', '=', 1),
                                                    array(DB::raw('IFNULL(ma_1750, 0)'), '!=', $arr_calculated_moving_average_records['ma_1750']),
                                                )
                                            )
                                            ->update(
                                                array('status' => 0)
                                            );
                                }
                            }
                        }
                        unset($known_median_deviation_record);

                        if(!$flag_is_it_existing_record && count($arr_calculated_moving_average_records) > 0){
                            self::insertGetId($arr_calculated_moving_average_records);
                        }
                        unset($flag_prior_1750_available, $flag_is_it_existing_record);
                    }
                    unset($looping_date);
                }
                $output_arr['response']['arr_unique_dates'] = $arr_unique_dates;
                unset($arr_unique_dates, $nse_index_records);
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
        unset($nse_index_conditions, $decimalPrecision);

        $output_arr['err_flag'] = $err_flag;
        $output_arr['err_msg'] = $err_msg;
        return $output_arr;
    }

    // Helps to retrieve calculated MEDIAN DEVIATION records
    public static function getMedianDeviationRecords($input_arr = array()){
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
                                $where_conditions[] = array('emosi_moving_average_1750_calculation.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                $where_conditions[] = array('emosi_moving_average_1750_calculation.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                            }
                            else{
                                // when either of the FROM DATE & TO DATE are present
                                if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE){
                                    $where_conditions[] = array('emosi_moving_average_1750_calculation.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                }
                                if(isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                    $where_conditions[] = array('emosi_moving_average_1750_calculation.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                                }
                            }
                            unset($searching_dates);
                        }
                        break;
                    case 'status':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                            if($value['data'] == 'status'){
                                $value['data'] = 'emosi_moving_average_1750_calculation.status';
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
            $order_by_clause = 'emosi_moving_average_1750_calculation.record_date DESC';
        }

        $records = DB::table('emosi_moving_average_1750_calculation')
                    ->select('emosi_moving_average_1750_calculation.index_symbol', 'emosi_moving_average_1750_calculation.status',
                             'emosi_moving_average_1750_calculation.index_value', 'emosi_moving_average_1750_calculation.ma_1750',
                             'emosi_moving_average_1750_calculation.deviation_1750', 'emosi_moving_average_1750_calculation.emosi_median_deviation_from_ma_1750',
                             (!$flag_export_data?'emosi_moving_average_1750_calculation.record_date':DB::raw('DATE_FORMAT(emosi_moving_average_1750_calculation.record_date, "%d/%m/%Y") AS record_date')),
                             (!$flag_export_data?'emosi_moving_average_1750_calculation.created_at':DB::raw('DATE_FORMAT(emosi_moving_average_1750_calculation.created_at, "%d/%m/%Y") AS created_at'))
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
