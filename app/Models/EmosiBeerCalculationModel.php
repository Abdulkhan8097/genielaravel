<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class EmosiBeerCalculationModel extends Model
{
    protected $table = 'emosi_beer_calculation';
    protected $fillable = ['id', 'symbol', 'record_date', 'g_sec_yield', 'pe', 'beer', 'median_beer', 'status', 'created_at', 'updated_at'];

    public static function get_record(){
        return self::where('status', 1);
    }

    // Helps to calculate & insert MEDIAN BEER records
    public static function calculate_median_beer($input_arr = array()){
        /* Possible values for $input_arr are: array('bond_symbol' => government bond symbol for whom yield value needs to be fetched,
         *                                           'index_symbol' => index symbol for whom P/E(price to earning) value needs to be fetched,
         *                                           'from_date' => date FROM which median BEER value needs to be calculated,
         *                                           'to_date' => date TO which median BEER value needs to be calculated,
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

        $bond_data_history_conditions = array();
        $nse_index_pepb_conditions = array();

        if(!isset($bond_symbol) || (isset($bond_symbol) && empty($bond_symbol))){
            $err_flag = 1;
            $err_msg[] = 'Government bond symbol details not found';
        }
        else{
            $bond_data_history_conditions[] = array('symbol', '=', $bond_symbol);
        }

        if(!isset($index_symbol) || (isset($index_symbol) && empty($index_symbol))){
            $err_flag = 1;
            $err_msg[] = 'NSE index symbol details not found';
        }
        else{
            $nse_index_pepb_conditions[] = array('symbol', '=', $index_symbol);
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
                $bond_data_history_conditions[] = array('record_date', '>=', $from_date);
                $nse_index_pepb_conditions[] = array('record_date', '>=', $from_date);
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
                $bond_data_history_conditions[] = array('record_date', '<=', $to_date);
                $nse_index_pepb_conditions[] = array('record_date', '<=', $to_date);
            }
        }

        if($err_flag == 0){
            if($flag_enable_query_log){
                DB::enableQueryLog();
            }

            try{
                $arr_unique_dates = array();
                // calculating BEER values for set of records

                // retrieving bond symbol datewise records used for further calculation
                $bond_data_history_records = EmosiBondDataHistory::get_record()
                                                                    ->where($bond_data_history_conditions)
                                                                    ->select(array('symbol', 'record_date', 'close'))
                                                                    ->orderBy('record_date', 'ASC')
                                                                    ->get()
                                                                    ->toArray();
                if(is_array($bond_data_history_records) && count($bond_data_history_records) > 0){
                    $bond_data_history_records = array_column($bond_data_history_records, NULL, 'record_date');
                    $arr_unique_dates = array_merge($arr_unique_dates, array_keys($bond_data_history_records));
                    if($flag_enable_query_log){
                        $output_arr['response']['bond_data_history_records'] = $bond_data_history_records;
                    }
                }

                // retrieving index symbol datewise records used for further calculation
                $nse_index_pepb_records = EmosiNseIndexPePbDivYield::get_record()
                                                                        ->where($nse_index_pepb_conditions)
                                                                        ->select(array('symbol', 'record_date', 'pe'))
                                                                        ->orderBy('record_date', 'ASC')
                                                                        ->get()
                                                                        ->toArray();
                if(is_array($nse_index_pepb_records) && count($nse_index_pepb_records) > 0){
                    $nse_index_pepb_records = array_column($nse_index_pepb_records, NULL, 'record_date');
                    $arr_unique_dates = array_merge($arr_unique_dates, array_keys($nse_index_pepb_records));
                    if($flag_enable_query_log){
                        $output_arr['response']['nse_index_pepb_records'] = $nse_index_pepb_records;
                    }
                }

                // finding unique dates for an array elements, also removing blank elements
                $arr_unique_dates = array_unique(array_filter($arr_unique_dates));
                // sorting array elements in an ASCENDING order of date
                sort($arr_unique_dates);

                if(count($arr_unique_dates) > 0){
                    foreach($arr_unique_dates as $looping_date){
                        $arr_calculated_beer_records = array();
                        $arr_beer_values = array();     // stores list of all BEER values which further used for calculating MEDIAN from those available set of values

                        $retrieved_beer_values = self::get_record()
                                                        ->where(array(
                                                                    array('record_date', '<', $looping_date)
                                                                )
                                                            )
                                                        ->whereNotNull('beer')
                                                        ->select(array('record_date', 'beer'))
                                                        ->orderBy('record_date', 'ASC')
                                                        ->get();
                        if(!$retrieved_beer_values->isEmpty()){
                            $retrieved_beer_values = $retrieved_beer_values->toArray();
                            if(count($retrieved_beer_values) > 0 && array_column($retrieved_beer_values, 'beer') > 0){
                                $arr_beer_values = array_merge($arr_beer_values, array_column($retrieved_beer_values, 'beer'));
                            }
                        }
                        unset($retrieved_beer_values);

                        $looping_record_err_flag = false;
                        if(!isset($bond_data_history_records[$looping_date]) || !isset($bond_data_history_records[$looping_date]['close'])){
                            // checking for latest available date record for BOND yield WHERE record_date <= $looping_date
                            $retrieved_bond_data = EmosiBondDataHistory::get_record()
                                                                            ->where(
                                                                                array(
                                                                                    array('symbol', '=', $bond_symbol),
                                                                                    array('record_date', '<=', $looping_date)
                                                                                )
                                                                            )
                                                                            ->select(array('symbol', 'record_date', 'close'))
                                                                            ->orderBy('record_date', 'DESC')
                                                                            ->first();
                            if($retrieved_bond_data){
                                $bond_data_history_records[$looping_date] = $retrieved_bond_data->toArray();
                            }
                            else{
                                // details of BOND yield not found then marking err_flag as TRUE
                                $looping_record_err_flag = true;
                            }
                            unset($retrieved_bond_data);
                        }
                        elseif(isset($bond_data_history_records[$looping_date]['close']) && !is_numeric($bond_data_history_records[$looping_date]['close'])){
                            // checking for latest available date record for BOND yield WHERE record_date <= $looping_date
                            $retrieved_bond_data = EmosiBondDataHistory::get_record()
                                                                            ->where(
                                                                                array(
                                                                                    array('symbol', '=', $bond_symbol),
                                                                                    array('record_date', '<=', $looping_date)
                                                                                )
                                                                            )
                                                                            ->select(array('symbol', 'record_date', 'close'))
                                                                            ->orderBy('record_date', 'DESC')
                                                                            ->first();
                            if($retrieved_bond_data){
                                $bond_data_history_records[$looping_date] = $retrieved_bond_data->toArray();
                            }
                            else{
                                // details of BOND yield is not numeric then marking err_flag as TRUE
                                $looping_record_err_flag = true;
                            }
                            unset($retrieved_bond_data);
                        }

                        if(!isset($nse_index_pepb_records[$looping_date]) || !isset($nse_index_pepb_records[$looping_date]['pe'])){
                            // checking for latest available date record for NSE index close value WHERE record_date <= $looping_date
                            $retrieved_nse_index_closing_data = EmosiNseIndexPePbDivYield::get_record()
                                                                            ->where(
                                                                                array(
                                                                                    array('symbol', '=', $index_symbol),
                                                                                    array('record_date', '<=', $looping_date)
                                                                                )
                                                                            )
                                                                            ->select(array('symbol', 'record_date', 'pe'))
                                                                            ->orderBy('record_date', 'DESC')
                                                                            ->first();
                            if($retrieved_nse_index_closing_data){
                                $nse_index_pepb_records[$looping_date] = $retrieved_nse_index_closing_data->toArray();
                            }
                            else{
                                // details of NSE index close value not found then marking err_flag as TRUE
                                $looping_record_err_flag = true;
                            }
                            unset($retrieved_nse_index_closing_data);
                        }
                        elseif(isset($nse_index_pepb_records[$looping_date]['pe']) && !is_numeric($nse_index_pepb_records[$looping_date]['pe'])){
                            // checking for latest available date record for NSE index close value WHERE record_date <= $looping_date
                            $retrieved_nse_index_closing_data = EmosiNseIndexPePbDivYield::get_record()
                                                                            ->where(
                                                                                array(
                                                                                    array('symbol', '=', $index_symbol),
                                                                                    array('record_date', '<=', $looping_date)
                                                                                )
                                                                            )
                                                                            ->select(array('symbol', 'record_date', 'pe'))
                                                                            ->orderBy('record_date', 'DESC')
                                                                            ->first();
                            if($retrieved_nse_index_closing_data){
                                $nse_index_pepb_records[$looping_date] = $retrieved_nse_index_closing_data->toArray();
                            }
                            else{
                                // details of NSE index close value is not numeric then marking err_flag as TRUE
                                $looping_record_err_flag = true;
                            }
                            unset($retrieved_nse_index_closing_data);
                        }

                        if(!$looping_record_err_flag){
                            $g_sec_yield = ($bond_data_history_records[$looping_date]['close']??0);
                            $g_sec_yield = round($g_sec_yield, $decimalPrecision);

                            $price_to_earnings_ratio = ($nse_index_pepb_records[$looping_date]['pe']??0);
                            $price_to_earnings_ratio = round($price_to_earnings_ratio, $decimalPrecision);

                            $earnings_yield = ((!empty($price_to_earnings_ratio)) ? ((1/$price_to_earnings_ratio) * 100) : 0);
                            $earnings_yield = round($earnings_yield, $decimalPrecision);

                            $beer = ((!empty($earnings_yield)) ? ($g_sec_yield / $earnings_yield) : 0);
                            $beer = round($beer, $decimalPrecision);

                            $arr_beer_values = array_merge($arr_beer_values, array($beer));
                            $median_beer = ((!empty($beer)) ? ((getMedian($arr_beer_values) / $beer) * 100) : 0);
                            $median_beer = round($median_beer, $decimalPrecision);

                            $flag_is_it_existing_record = false;
                            $known_beer_calculation_record = self::get_record()
                                                                    ->where(
                                                                        array(
                                                                            array('record_date', '=', $looping_date),
                                                                            array('bond_symbol', '=', $bond_symbol),
                                                                            array('index_symbol', '=', $index_symbol)
                                                                        )
                                                                    )
                                                                    ->select(array('beer', 'median_beer'))
                                                                    ->get();
                            if(!$known_beer_calculation_record->isEmpty()){
                                // if already an existing record for same RECORD_DATE, BOND_SYMBOL, INDEX_SYMBOL exists but BEER value is different then marking earlier record as INACTIVE (status = 0)
                                $known_beer_calculation_record = $known_beer_calculation_record->toArray();
                                if(count($known_beer_calculation_record) > 0 && isset($known_beer_calculation_record[0]['beer'])){
                                    if($known_beer_calculation_record[0]['beer'] == $beer){
                                        $flag_is_it_existing_record = true;
                                    }
                                    else{
                                        // updating an record
                                        self::get_record()
                                                ->where(
                                                    array(
                                                        array('record_date', '=', $looping_date),
                                                        array('bond_symbol', '=', $bond_symbol),
                                                        array('index_symbol', '=', $index_symbol),
                                                        array('status', '=', 1),
                                                        array('beer', '!=', $beer),
                                                    )
                                                )
                                                ->update(
                                                    array('status' => 0)
                                                );
                                    }
                                }
                            }
                            unset($known_beer_calculation_record);

                            if(!$flag_is_it_existing_record){
                                $arr_calculated_beer_records = array('record_date' => $looping_date,
                                                                     'bond_symbol' => $bond_symbol,
                                                                     'g_sec_yield' => $g_sec_yield,
                                                                     'index_symbol' => $index_symbol,
                                                                     'pe' => $price_to_earnings_ratio,
                                                                     'beer' => $beer,
                                                                     'median_beer' => $median_beer);
                                self::insertGetId($arr_calculated_beer_records);
                            }
                            unset($g_sec_yield, $price_to_earnings_ratio, $earnings_yield, $beer, $median_beer, $flag_is_it_existing_record);
                        }

                        if(count($arr_calculated_beer_records) > 0){
                            $output_arr['response']['arr_calculated_beer_records'][] = $arr_calculated_beer_records;
                        }
                        unset($looping_record_err_flag, $arr_beer_values, $arr_calculated_beer_records);
                    }
                    unset($looping_date);
                }
                unset($arr_unique_dates, $bond_data_history_records, $nse_index_pepb_records);
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
        unset($bond_data_history_conditions, $nse_index_pepb_conditions, $decimalPrecision);

        $output_arr['err_flag'] = $err_flag;
        $output_arr['err_msg'] = $err_msg;
        return $output_arr;
    }

    // Helps to retrieve calculated MEDIAN BEER records
    public static function getMedianBeerRecords($input_arr = array()){
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
                                $where_conditions[] = array('emosi_beer_calculation.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                $where_conditions[] = array('emosi_beer_calculation.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                            }
                            else{
                                // when either of the FROM DATE & TO DATE are present
                                if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE){
                                    $where_conditions[] = array('emosi_beer_calculation.'. $value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                }
                                if(isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                    $where_conditions[] = array('emosi_beer_calculation.'. $value['data'], '<=', $searching_dates[1] .' 23:59:59');
                                }
                            }
                            unset($searching_dates);
                        }
                        break;
                    case 'status':
                        if(isset($value['search']['value']) && is_numeric($value['search']['value'])){
                            if($value['data'] == 'status'){
                                $value['data'] = 'emosi_beer_calculation.status';
                            }
                            $where_conditions[] = array($value['data'], '=', intval($value['search']['value']));
                        }
                        break;
                    case 'bond_symbol':
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
            $order_by_clause = 'emosi_beer_calculation.record_date DESC';
        }

        $records = DB::table('emosi_beer_calculation')
                    ->select('emosi_beer_calculation.bond_symbol', 'emosi_beer_calculation.g_sec_yield',
                             'emosi_beer_calculation.index_symbol', 'emosi_beer_calculation.pe',
                             'emosi_beer_calculation.earnings_yield', 'emosi_beer_calculation.beer',
                             'emosi_beer_calculation.median_beer', 'emosi_beer_calculation.status',
                             (!$flag_export_data?'emosi_beer_calculation.record_date':DB::raw('DATE_FORMAT(emosi_beer_calculation.record_date, "%d/%m/%Y") AS record_date')),
                             (!$flag_export_data?'emosi_beer_calculation.created_at':DB::raw('DATE_FORMAT(emosi_beer_calculation.created_at, "%d/%m/%Y") AS created_at'))
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
