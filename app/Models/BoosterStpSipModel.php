<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class BoosterStpSipModel extends Model
{
    use HasFactory;

    // function to generate booster stp data in formats like Backtest Result, Booster STP, Normal Short Term & Normal Long Term
    public static function get_backtest_stp_result($input_arr = array()){
        error_reporting(E_ALL);ini_set('display_errors', 'on');
        extract($input_arr);                     // Import variables into the current symbol table from an array

        $err_flag = 0;                           // err_flag is 0 means no error
        $err_msg = array();                      // err_msg stores list of errors found during execution
        $output_arr = array('exported_records' => array());

        if(!isset($stp_report_download_format) || (isset($stp_report_download_format) && empty($stp_report_download_format))){
            $stp_report_download_format = 'detailed';
        }

        // if decimal precision is not available in input parameters then keeping it's default value as 2
        if(!isset($decimalPrecision) || (isset($decimalPrecision) && (empty($decimalPrecision) || !is_numeric($decimalPrecision)))){
            $decimalPrecision = 2;
        }

        if(!isset($stp_end_date) || (isset($stp_end_date) && (empty($stp_end_date) || strtotime($stp_end_date) === FALSE))){
            // if STP END DATE is not given then considering it as TODAY DATE
            $stp_end_date = date('Y-m-d');
        }
        $og_stp_end_date = $stp_end_date;

        $arr_stp_start_date = array();           // helps to store list of dates for whom data needs to get generated
        if(!isset($stp_start_date) || (isset($stp_start_date) && (empty($stp_start_date) || strtotime($stp_start_date . '-01') === FALSE))){
            // if STP START DATE is not given then keeping 1900-01-01 as default start date
            $stp_start_date = '1900-01';
        }
        $stp_start_date .= '-01';
        $arr_stp_start_date[] = $stp_start_date;

        if(!isset($stp_report_month_date) || (isset($stp_report_month_date) && empty($stp_report_month_date))){
            $stp_report_month_date = 'MAX';
        }

        switch($stp_report_download_format){
            case 'summary':
                // getting list of stp_start_date from STP START DATE till STP END DATE mentioned in input parameter
                $arr_stp_start_date = array_merge($arr_stp_start_date, self::get_month_dates_from_input_date($stp_start_date, '1 month', $stp_end_date));
                $output_arr['summary_backtest_result'] = array();
                break;
        }

        if(is_array($arr_stp_start_date) && count($arr_stp_start_date) > 0){

            foreach($arr_stp_start_date as $stp_start_date){
                $stp_start_date_condition = '';
                if(isset($stp_start_date) && !empty($stp_start_date) && strtotime($stp_start_date) !== FALSE){
                    $stp_start_date_condition = ' AND source.index_date >= :stp_start_date ';
                }
                else{
                    // skipping loop here due to either START DATE is not available or it's not in date format
                    continue;
                }

                try{
                    // Query for NORMAL SHORT DURATION STP
                    DB::statement("SET @opening_balance:=". ($stp_opening_balance??0) .";");
                    DB::statement("SET @base_amount:=@opening_balance/12;");
                    DB::statement("SET @opening_units:=-1.0000;");
                    DB::statement("SET @target_units:=0.0000;");
                    DB::statement("SET @cumulative_target_units:=0.0000;");
                    DB::statement("SET @cumulative_target_amount:=0.0000;");
                    DB::statement("SET @cumulative_target_amount:=0.0000;");
                    DB::statement("SET @amount_transferred_to_target:=0.0000;");
                    DB::statement("SET @opening_source_units:=0.0000;");
                    DB::statement("SET @source_units_to_be_transferred:=0.0000;");
                    $query = DB::select("SELECT looping_year_month.index_date, 
                                        @opening_balance:=CAST(CASE WHEN(@opening_units = -1) THEN @opening_balance ELSE (IFNULL(index_history.close, 0) * @opening_units) END AS DECIMAL(25, 2)) AS opening_balance_in_source, 
                                        IFNULL(index_history.close, 0) AS index_value, 
                                        @opening_source_units:=CAST(CASE WHEN(@opening_units = -1) THEN (@opening_balance/IFNULL(index_history.close, 1)) ELSE @opening_units END AS DECIMAL(25, 2)) AS opening_source_units, 
                                        CAST(@base_amount AS DECIMAL(25, 2)) AS base_amount, 
                                        @amount_transferred_to_target:=CAST(CASE WHEN(@base_amount<@opening_balance) THEN @base_amount ELSE @opening_balance END AS DECIMAL(25, 2)) AS amount_transferred_to_target, 
                                        @source_units_to_be_transferred:=CAST((@amount_transferred_to_target/ IFNULL(index_history.close, 1)) AS DECIMAL(25, 2)) AS source_units_to_be_transferred, 
                                        @opening_units:=CAST((@opening_source_units - @source_units_to_be_transferred) AS DECIMAL(25, 2)) AS remaining_units_in_source, 
                                        CAST((@opening_units * IFNULL(index_history.close, 0)) AS DECIMAL(25, 2)) AS remaining_amount_in_source, 
                                        IFNULL(target_scheme.close, 0) AS target_scheme_value, 
                                        @target_units:=CAST((@amount_transferred_to_target / IFNULL(target_scheme.close, 1)) AS DECIMAL(25, 2)) AS target_units, 
                                        @cumulative_target_units:=@cumulative_target_units+@target_units AS cumulative_target_units, 
                                        @cumulative_target_amount:=CAST((@cumulative_target_units * IFNULL(target_scheme.close, 0)) AS DECIMAL(25, 2)) AS cumulative_target_amount, 
                                        CAST((@cumulative_target_amount + (@opening_units * IFNULL(index_history.close, 0))) AS DECIMAL(25, 2)) AS cumulative_amount_target_plus_source 
                                        FROM (SELECT DATE_FORMAT(source.index_date, '%Y-%m') AS index_year_month,". $stp_report_month_date ."(source.index_date) AS index_date,source.symbol 
                                        FROM quote_data_index_history AS source 
                                        INNER JOIN quote_data_index_history AS target ON (source.index_date = target.index_date) 
                                        WHERE source.symbol = :source_scheme_symbol AND target.symbol = :target_scheme_symbol ". $stp_start_date_condition ." 
                                        GROUP BY index_year_month) AS looping_year_month 
                                        INNER JOIN quote_data_index_history AS index_history ON (looping_year_month.symbol = index_history.symbol AND index_history.index_date = looping_year_month.index_date) 
                                        INNER JOIN quote_data_index_history AS target_scheme ON (looping_year_month.index_date = target_scheme.index_date) 
                                        WHERE looping_year_month.symbol = :source_scheme_symbol AND target_scheme.symbol = :target_scheme_symbol;", array(':source_scheme_symbol' => ($select_stp_source_scheme??''), ':target_scheme_symbol' => ($select_stp_target_scheme??''), ':stp_start_date' => ($stp_start_date??date('Y-m-d'))));
                    $data['normal_short_duration_stp'] = $query;

                    // Query for NORMAL LONG DURATION STP
                    DB::statement("SET @opening_balance:=". ($stp_opening_balance??0) .";");
                    DB::statement("SET @base_amount:=@opening_balance/36;");
                    DB::statement("SET @opening_units:=-1.0000;");
                    DB::statement("SET @target_units:=0.0000;");
                    DB::statement("SET @cumulative_target_units:=0.0000;");
                    DB::statement("SET @cumulative_target_amount:=0.0000;");
                    DB::statement("SET @cumulative_target_amount:=0.0000;");
                    DB::statement("SET @amount_transferred_to_target:=0.0000;");
                    DB::statement("SET @opening_source_units:=0.0000;");
                    DB::statement("SET @source_units_to_be_transferred:=0.0000;");
                    $query = DB::select("SELECT looping_year_month.index_date, 
                                        @opening_balance:=CAST(CASE WHEN(@opening_units = -1) THEN @opening_balance ELSE (IFNULL(index_history.close, 0) * @opening_units) END AS DECIMAL(25, 2)) AS opening_balance_in_source, 
                                        IFNULL(index_history.close, 0) AS index_value, 
                                        @opening_source_units:=CAST(CASE WHEN(@opening_units = -1) THEN (@opening_balance/IFNULL(index_history.close, 1)) ELSE @opening_units END AS DECIMAL(25, 2)) AS opening_source_units, 
                                        CAST(@base_amount AS DECIMAL(25, 2)) AS base_amount, 
                                        @amount_transferred_to_target:=CAST(CASE WHEN(@base_amount<@opening_balance) THEN @base_amount ELSE @opening_balance END AS DECIMAL(25, 2)) AS amount_transferred_to_target, 
                                        @source_units_to_be_transferred:=CAST((@amount_transferred_to_target/ IFNULL(index_history.close, 1)) AS DECIMAL(25, 2)) AS source_units_to_be_transferred, 
                                        @opening_units:=CAST((@opening_source_units - @source_units_to_be_transferred) AS DECIMAL(25, 2)) AS remaining_units_in_source, 
                                        CAST((@opening_units * IFNULL(index_history.close, 0)) AS DECIMAL(25, 2)) AS remaining_amount_in_source, 
                                        IFNULL(target_scheme.close, 0) AS target_scheme_value, 
                                        @target_units:=CAST((@amount_transferred_to_target / IFNULL(target_scheme.close, 1)) AS DECIMAL(25, 2)) AS target_units, 
                                        @cumulative_target_units:=@cumulative_target_units+@target_units AS cumulative_target_units, 
                                        @cumulative_target_amount:=CAST((@cumulative_target_units * IFNULL(target_scheme.close, 0)) AS DECIMAL(25, 2)) AS cumulative_target_amount, 
                                        CAST((@cumulative_target_amount + (@opening_units * IFNULL(index_history.close, 0))) AS DECIMAL(25, 2)) AS cumulative_amount_target_plus_source 
                                        FROM (SELECT DATE_FORMAT(source.index_date, '%Y-%m') AS index_year_month,". $stp_report_month_date ."(source.index_date) AS index_date,source.symbol 
                                        FROM quote_data_index_history AS source 
                                        INNER JOIN quote_data_index_history AS target ON (source.index_date = target.index_date) 
                                        WHERE source.symbol = :source_scheme_symbol AND target.symbol = :target_scheme_symbol ". $stp_start_date_condition ." 
                                        GROUP BY index_year_month) AS looping_year_month 
                                        INNER JOIN quote_data_index_history AS index_history ON (looping_year_month.symbol = index_history.symbol AND index_history.index_date = looping_year_month.index_date) 
                                        INNER JOIN quote_data_index_history AS target_scheme ON (looping_year_month.index_date = target_scheme.index_date) 
                                        WHERE looping_year_month.symbol = :source_scheme_symbol AND target_scheme.symbol = :target_scheme_symbol;", array(':source_scheme_symbol' => ($select_stp_source_scheme??''), ':target_scheme_symbol' => ($select_stp_target_scheme??''), ':stp_start_date' => ($stp_start_date??date('Y-m-d'))));
                    $data['normal_long_duration_stp'] = $query;

                    // Query for BOOSTER STP
                    DB::statement("SET @opening_balance:=". ($stp_opening_balance??0) .";");
                    DB::statement("SET @base_amount:=".($stp_base_amount??"@opening_balance/12").";");
                    DB::statement("SET @opening_units:=-1.0000;");
                    DB::statement("SET @target_units:=0.0000;");
                    DB::statement("SET @cumulative_target_units:=0.0000;");
                    DB::statement("SET @cumulative_target_amount:=0.0000;");
                    DB::statement("SET @cumulative_target_amount:=0.0000;");
                    DB::statement("SET @amount_transferred_to_target:=0.0000;");
                    DB::statement("SET @opening_source_units:=0.0000;");
                    DB::statement("SET @source_units_to_be_transferred:=0.0000;");
                    $query = DB::select("SELECT looping_year_month.index_date, 
                                        @opening_balance:=CAST(CASE WHEN(@opening_units = -1) THEN @opening_balance ELSE (IFNULL(index_history.close, 0) * @opening_units) END AS DECIMAL(25, 2)) AS opening_balance_in_source, 
                                        IFNULL(index_history.close, 0) AS index_value, 
                                        @opening_source_units:=CAST(CASE WHEN(@opening_units = -1) THEN (@opening_balance/IFNULL(index_history.close, 1)) ELSE @opening_units END AS DECIMAL(25, 2)) AS opening_source_units, 
                                        CAST(@base_amount AS DECIMAL(25, 2)) AS base_amount, index_history.margin_of_safety, 
                                        IFNULL(mos_multiplier_data.multiplier_value, 1) AS multiplier, 
                                        @expected_amount_transferred_to_target:=CAST((@base_amount * IFNULL(mos_multiplier_data.multiplier_value, 1)) AS DECIMAL(25, 2)) AS expected_amount_transferred_to_target, 
                                        @actual_amount_transferred_to_target:=CAST(CASE WHEN(@expected_amount_transferred_to_target<@opening_balance) THEN @expected_amount_transferred_to_target ELSE @opening_balance END AS DECIMAL(25, 2)) AS actual_amount_transferred_to_target, 
                                        @source_units_to_be_transferred:=CAST((@actual_amount_transferred_to_target/ IFNULL(index_history.close, 1)) AS DECIMAL(25, 2)) AS source_units_to_be_transferred, 
                                        @opening_units:=CAST((@opening_source_units - @source_units_to_be_transferred) AS DECIMAL(25, 2)) AS remaining_units_in_source, 
                                        CAST((@opening_units * IFNULL(index_history.close, 0)) AS DECIMAL(25, 2)) AS remaining_amount_in_source, 
                                        IFNULL(target_scheme.close, 0) AS target_scheme_value, 
                                        @target_units:=CAST((@actual_amount_transferred_to_target / IFNULL(target_scheme.close, 1)) AS DECIMAL(25, 2)) AS target_units, 
                                        @cumulative_target_units:=@cumulative_target_units+@target_units AS cumulative_target_units, 
                                        @cumulative_target_amount:=CAST((@cumulative_target_units * IFNULL(target_scheme.close, 0)) AS DECIMAL(25, 2)) AS cumulative_target_amount, 
                                        CAST((@cumulative_target_amount + (@opening_units * IFNULL(index_history.close, 0))) AS DECIMAL(25, 2)) AS cumulative_amount_target_plus_source 
                                        FROM (SELECT DATE_FORMAT(source.index_date, '%Y-%m') AS index_year_month,". $stp_report_month_date ."(source.index_date) AS index_date,source.symbol 
                                        FROM quote_data_index_history AS source 
                                        INNER JOIN quote_data_index_history AS target ON (source.index_date = target.index_date) 
                                        WHERE source.symbol = :source_scheme_symbol AND target.symbol = :target_scheme_symbol ". $stp_start_date_condition ." 
                                        GROUP BY index_year_month) AS looping_year_month 
                                        INNER JOIN quote_data_index_history AS index_history ON (looping_year_month.symbol = index_history.symbol AND index_history.index_date = looping_year_month.index_date) 
                                        INNER JOIN quote_data_index_history AS target_scheme ON (looping_year_month.index_date = target_scheme.index_date) 
                                        LEFT JOIN mos_multiplier_data ON (mos_multiplier_data.margin_of_safety = index_history.margin_of_safety AND mos_multiplier_data.multiplier_type = :multiplier_type) WHERE looping_year_month.symbol = :source_scheme_symbol AND target_scheme.symbol = :target_scheme_symbol;", array(':source_scheme_symbol' => ($select_stp_source_scheme??''), ':target_scheme_symbol' => ($select_stp_target_scheme??''), ':multiplier_type' => ($stp_multiplier_type??''), ':stp_start_date' => ($stp_start_date??date('Y-m-d'))));
                    $data['booster_stp'] = $query;

                    // preparing backtest stp result
                    $data['backtest_stp_result'] = array();
                    $arr_unique_setup_months = array();
                    if(is_array($data['booster_stp']) && count($data['booster_stp']) > 0){
                        $arr_unique_setup_months = array_merge($arr_unique_setup_months, array_column($data['booster_stp'], 'index_date'));
                    }
                    if(is_array($data['normal_short_duration_stp']) && count($data['normal_short_duration_stp']) > 0){
                        $arr_unique_setup_months = array_merge($arr_unique_setup_months, array_column($data['normal_short_duration_stp'], 'index_date'));
                    }
                    if(is_array($data['normal_long_duration_stp']) && count($data['normal_long_duration_stp']) > 0){
                        $arr_unique_setup_months = array_merge($arr_unique_setup_months, array_column($data['normal_long_duration_stp'], 'index_date'));
                    }
                    // removing empty & duplicate date values
                    $arr_unique_setup_months = array_unique(array_filter($arr_unique_setup_months));
                    // sorting dates in an ASCENDING order
                    asort($arr_unique_setup_months);

                    if(count($arr_unique_setup_months) > 0){
                        $arr_year_data_options = array('3' => '3 Year', '5' => '5 Year', '10' => '10 Year', '-1' => 'As on Date '. $stp_end_date);
                        $arr_backtest_result_fixed_headings = array('peak_value' => 'Peak Value', 'peak_date' => 'Peak Date', 'trough_value' => 'Trough Value', 'trough_date' => 'Trough Date', 'drawdown_from_peak_percentage' => 'Drawdown from Peak% [(Peak Value -Trough Value)/Peak Value)*100]', 'drawdown_from_peak_period' => 'Drawdown from Peak Period (Trough Date - Peak Date) in Days', 'current_percentage_in_source' => 'Current % in Source (Remaining amount in Source/(Cumulative Amount (Target + Source))*100)', 'current_percentage_in_target' => 'Current % in Target (Cumulative Target Amt./Cumulative Amount (Target + Source)*100)');
                        $heading_row = array('Setup Month');
                        $year_heading_row = array('');
                        $arr_stp_options = array('booster_stp' => 'Booster STP', 'normal_long_duration_stp' => 'Normal STP - Long', 'normal_short_duration_stp' => 'Normal STP - Short');
                        // looping for each year option
                        foreach($arr_year_data_options as $year_option){
                            // lopping for each year and STP option
                            foreach($arr_stp_options as $stp_option){
                                $heading_row = array_merge($heading_row, array($stp_option), array_values($arr_backtest_result_fixed_headings));
                            }
                            $heading_row = array_merge($heading_row, array('End Date'));

                            for($heading_cntr = 0; $heading_cntr < ((count($arr_backtest_result_fixed_headings) + 1) * count($arr_stp_options)); $heading_cntr++){
                                if(($heading_cntr == intval(((count($arr_backtest_result_fixed_headings) + 1) * count($arr_stp_options)) / 2)) || ($heading_cntr == 0)) {
                                    $year_heading_row[] = $year_option;
                                }
                                else{
                                    $year_heading_row[] = '';
                                }
                            }
                            $year_heading_row[] = '';
                            unset($stp_option);
                        }
                        unset($year_option);

                        $data['backtest_stp_result'][] = $year_heading_row;
                        $data['backtest_stp_result'][] = $heading_row;

                        if((strtolower($stp_report_download_format) == 'summary') && is_array($output_arr['summary_backtest_result']) && (count($output_arr['summary_backtest_result']) == 0)){
                            $output_arr['summary_backtest_result'][] = $year_heading_row;
                            $output_arr['summary_backtest_result'][] = $heading_row;
                        }

                        foreach($arr_unique_setup_months as $setup_month){
                            $looping_record = array('setup_month' => $setup_month);
                            // lopping for each year and STP option
                            foreach($arr_year_data_options as $year_option_key => $year_option){
                                if($stp_report_month_date == 'MIN'){
                                    $year_option .= ' 1 months';
                                }
                                $setup_month_end_date = date_create($setup_month);
                                if($year_option_key == -1){
                                    if(($stp_report_month_date == 'MIN') && isset($stp_end_date) && ($og_stp_end_date == $stp_end_date)){
                                        $stp_end_date = date('Y-m-d', strtotime("+1 month", strtotime($stp_end_date)));
                                    }
                                    $setup_month_end_date = ($stp_end_date??date('Y-m-d'));
                                }
                                else{
                                    date_add($setup_month_end_date, date_interval_create_from_date_string($year_option));
                                    $setup_month_end_date = date_format($setup_month_end_date, 'Y-m-d');
                                }
                                $difference_in_days = date_diff(date_create($setup_month_end_date), date_create($setup_month));
                                $difference_in_days = $difference_in_days->format('%a');

                                foreach($arr_stp_options as $stp_option_key => $stp_option){
                                    $arr_lookup_records = ($data[$stp_option_key]??array());
                                    $arr_lookup_records = array_column($arr_lookup_records, NULL, 'index_date');

                                    $arr_setup_date_record = array();
                                    $peak_value = 0;
                                    $peak_date = '';
                                    $trough_value = 0;
                                    $trough_date = '';
                                    $cumulative_amount_target_plus_source = 0;
                                    $cumulative_target_amount = 0;
                                    $remaining_amount_in_source = 0;

                                    $looping_record[$year_option_key . '_' . $stp_option_key . '_cagr'] = '';

                                    foreach($arr_lookup_records as $_lookup_row => $_lookup_record){
                                        $_lookup_record = (array) $_lookup_record;
                                        if((strtotime($_lookup_record['index_date']) >= strtotime($setup_month)) && (strtotime($_lookup_record['index_date']) <= strtotime($setup_month_end_date))){
                                            $arr_setup_date_record[strtotime($_lookup_record['index_date'])] = array('cumulative_amount_target_plus_source' => ($_lookup_record['cumulative_amount_target_plus_source']??0), 'remaining_amount_in_source' => ($_lookup_record['remaining_amount_in_source']??0), 'cumulative_target_amount' => ($_lookup_record['cumulative_target_amount']??0));
                                        }
                                    }
                                    if(count($arr_setup_date_record) > 0){
                                        // calculating CAGR formula is: (((Cumulative Amount (Target + Source) on End Date)/ Input Amount as on Setup Date) ^ (1/ ((End Date-Setup Month)/365)))-1 *100
                                        $latest_setup_month_end_date = max(array_keys($arr_setup_date_record));
                                        if(!empty(($arr_setup_date_record[$latest_setup_month_end_date]['cumulative_amount_target_plus_source']??0))){
                                            $cumulative_amount_target_plus_source = ($arr_setup_date_record[$latest_setup_month_end_date]['cumulative_amount_target_plus_source']??0);
                                            $looping_record[$year_option_key . '_' . $stp_option_key . '_cagr'] = pow(($cumulative_amount_target_plus_source/$stp_opening_balance), (1/(!empty(($difference_in_days/365))?($difference_in_days/365):1)));
                                            $looping_record[$year_option_key . '_' . $stp_option_key . '_cagr'] = (($looping_record[$year_option_key . '_' . $stp_option_key. '_cagr'] - 1) * 100);
                                            $looping_record[$year_option_key . '_' . $stp_option_key . '_cagr'] = round($looping_record[$year_option_key . '_' . $stp_option_key . '_cagr'], $decimalPrecision);
                                        }

                                        if(!empty(($arr_setup_date_record[$latest_setup_month_end_date]['remaining_amount_in_source']??0))){
                                            $remaining_amount_in_source = ($arr_setup_date_record[$latest_setup_month_end_date]['remaining_amount_in_source']??0);
                                        }

                                        if(!empty(($arr_setup_date_record[$latest_setup_month_end_date]['cumulative_target_amount']??0))){
                                            $cumulative_target_amount = ($arr_setup_date_record[$latest_setup_month_end_date]['cumulative_target_amount']??0);
                                        }
                                        unset($latest_setup_month_end_date);

                                        // retrieving MAX & MIN values of cumulative_amount_target_plus_source
                                        $arr_setup_date_record = array_combine(array_keys($arr_setup_date_record), array_column($arr_setup_date_record, 'cumulative_amount_target_plus_source'));
                                        $peak_value = max($arr_setup_date_record);
                                        $peak_date = (array_search($peak_value, $arr_setup_date_record)?array_search($peak_value, $arr_setup_date_record):'');
                                        $peak_date = date('Y-m-d', $peak_date);

                                        // calculating trough value/date from after the peak date till end date
                                        $arr_setup_date_record = array();
                                        foreach($arr_lookup_records as $_lookup_row => $_lookup_record){
                                            $_lookup_record = (array) $_lookup_record;
                                            if((strtotime($_lookup_record['index_date']) > strtotime($peak_date)) && (strtotime($_lookup_record['index_date']) <= strtotime($setup_month_end_date))){
                                                $arr_setup_date_record[strtotime($_lookup_record['index_date'])] = array('cumulative_amount_target_plus_source' => ($_lookup_record['cumulative_amount_target_plus_source']??0), 'remaining_amount_in_source' => ($_lookup_record['remaining_amount_in_source']??0), 'cumulative_target_amount' => ($_lookup_record['cumulative_target_amount']??0));
                                            }
                                        }

                                        if(count($arr_setup_date_record) > 0){
                                            $arr_setup_date_record = array_combine(array_keys($arr_setup_date_record), array_column($arr_setup_date_record, 'cumulative_amount_target_plus_source'));
                                            $trough_value = min($arr_setup_date_record);
                                            $trough_date = (array_search($trough_value, $arr_setup_date_record)?array_search($trough_value, $arr_setup_date_record):'');
                                            $trough_date = date('Y-m-d', $trough_date);
                                        }
                                    }
                                    unset($_lookup_row, $_lookup_record, $arr_setup_date_record);

                                    foreach($arr_backtest_result_fixed_headings as $heading_key => $heading_label){
                                        switch($heading_key){
                                            case 'peak_value':
                                                // Highest Value in Cumulative Amount (Target + Source) between End Date and Setup Date for respective sheet (Booster STP/ Normal Short Duration STP/ Normal Long Duration STP)
                                                $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = round($peak_value, $decimalPrecision);
                                                break;
                                            case 'peak_date':
                                                // Corresponding Date for Peak Value
                                                $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = $peak_date;
                                                break;
                                            case 'trough_value':
                                                // Lowest Value in Cumulative Amount (Target + Source) post peak date till End Date for respective sheet (Booster STP/ Normal Short Duration STP/ Normal Long Duration STP)
                                                if(!empty($trough_value) && is_numeric($trough_value)){
                                                    $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = round($trough_value, $decimalPrecision);
                                                }
                                                else{
                                                    $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = 'NA';
                                                }
                                                break;
                                            case 'trough_date':
                                                // Corresponding Date for Trough Value
                                                $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = $trough_date;
                                                break;
                                            case 'drawdown_from_peak_percentage':
                                                // [(Peak Value -Trough Value)/Peak Value)*100]
                                                $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = '';
                                                if(!empty($peak_date) && !empty($trough_date)){
                                                    $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = (!empty($peak_value)?((($peak_value - $trough_value) / $peak_value) * 100):'');
                                                    $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = round($looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key], $decimalPrecision);
                                                }
                                                break;
                                            case 'drawdown_from_peak_period':
                                                // (Trough Date - Peak Date)
                                                $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = '';
                                                if(!empty($peak_date) && strtotime($peak_date) !== FALSE && !empty($trough_date) && strtotime($trough_date) !== FALSE){
                                                    $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = date_diff(date_create($peak_date), date_create($trough_date));
                                                    $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key]->format('%a');
                                                }
                                                break;
                                            case 'current_percentage_in_source':
                                                // (Remaining amount in Source as on End Date/(Cumulative Amount (Target + Source)) as on End Date*100)
                                                $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = (!empty($cumulative_amount_target_plus_source)?(($remaining_amount_in_source / $cumulative_amount_target_plus_source) * 100):0);
                                                $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = round($looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key], $decimalPrecision);
                                                break;
                                            case 'current_percentage_in_target':
                                                // (Cumulative Target Amt. as on End Date /Cumulative Amount (Target + Source) as on End Date *100)
                                                $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = (!empty($cumulative_amount_target_plus_source)?(($cumulative_target_amount / $cumulative_amount_target_plus_source) * 100):0);
                                                $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = round($looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key], $decimalPrecision);
                                                break;
                                            default:
                                                $looping_record[$year_option_key . '_' . $stp_option_key . '_'. $heading_key] = '';
                                        }
                                    }
                                    unset($heading_key, $heading_label);
                                    unset($arr_lookup_records, $peak_value, $peak_date, $trough_value, $trough_date, $cumulative_amount_target_plus_source, $cumulative_target_amount, $remaining_amount_in_source);
                                }
                                $looping_record[$year_option_key . '_setup_month_end_date'] = $setup_month_end_date;
                                unset($stp_option_key, $stp_option);
                                unset($setup_month_end_date, $difference_in_days);
                            }
                            unset($year_option_key, $year_option);

                            $data['backtest_stp_result'][] = $looping_record;
                            if(strtolower($stp_report_download_format) == 'summary'){
                                $output_arr['summary_backtest_result'][] = $looping_record;
                                // for summary sheet, we have asked to get only first record and skip records after that
                                break;
                            }
                            unset($looping_record);
                        }
                        unset($setup_month, $arr_backtest_result_fixed_headings, $heading_row, $year_heading_row, $arr_stp_options, $arr_year_data_options);
                    }
                    unset($arr_unique_setup_months, $arr_stp_start_date);

                    $target_scheme_name_text = 'Target Scheme';
                    if(isset($select_stp_target_scheme_label) && !empty($select_stp_target_scheme_label)){
                        $target_scheme_name_text = $select_stp_target_scheme_label;
                    }
                    $source_scheme_name_text = 'Source';
                    if(isset($select_stp_source_scheme_label) && !empty($select_stp_source_scheme_label)){
                        $source_scheme_name_text = $select_stp_source_scheme_label;
                    }

                    if(is_array($data['normal_short_duration_stp']) && count($data['normal_short_duration_stp']) > 0){
                        // $data['normal_short_duration_stp'] = array_merge(array((object) array_keys((array) $data['normal_short_duration_stp'][0])), $data['normal_short_duration_stp']);
                        $data['normal_short_duration_stp'] = array_merge(array(array('STP Month', 'Opening Balance in Source', $source_scheme_name_text .' NAV', 'Opening Source Units', 'Base Amount', 'Amount Transferred to Target', 'Source Units to be transferred', 'Remaining Units in Source', 'Remaining Amount in Source', $target_scheme_name_text .' Nav', 'Target Units', 'Cumulative Target Units', 'Cumulative Target Amount', 'Cumulative Amount (Target + Source)')), $data['normal_short_duration_stp']);
                    }
                    if(is_array($data['normal_long_duration_stp']) && count($data['normal_long_duration_stp']) > 0){
                        // $data['normal_long_duration_stp'] = array_merge(array((object) array_keys((array) $data['normal_long_duration_stp'][0])), $data['normal_long_duration_stp']);
                        $data['normal_long_duration_stp'] = array_merge(array(array('STP Month', 'Opening Balance in Source', $source_scheme_name_text .' NAV', 'Opening Source Units', 'Base Amount', 'Amount Transferred to Target', 'Source Units to be transferred', 'Remaining Units in Source', 'Remaining Amount in Source', $target_scheme_name_text .' Nav', 'Target Units', 'Cumulative Target Units', 'Cumulative Target Amount', 'Cumulative Amount (Target + Source)')), $data['normal_long_duration_stp']);
                    }
                    if(is_array($data['booster_stp']) && count($data['booster_stp']) > 0){
                        // $data['booster_stp'] = array_merge(array((object) array_keys((array) $data['booster_stp'][0])), $data['booster_stp']);
                        $data['booster_stp'] = array_merge(array(array('STP Month', 'Opening Balance in Source', $source_scheme_name_text .' NAV', 'Opening Source Units', 'Base Amount', 'EMOSI', 'Multiplier', 'Expected Amount transferred to Target', 'Actual Amount transferred to Target', 'Source Units to be transferred', 'Remaining Units in Source', 'Remaining Amount in Source', $target_scheme_name_text .' Nav', 'Target Units', 'Cumulative Target Units', 'Cumulative Target Amount', 'Cumulative Amount (Target + Source)')), $data['booster_stp']);
                    }

                    if(strtolower($stp_report_download_format) != 'summary'){
                        $output_arr['exported_records'] = array(
                                                            array('data' => $data['backtest_stp_result'], 'title' => 'Backtest Result STP', 'extra_params' => array('merg_cells' => array('B1:AC1', 'AD1:BE1', 'BF1:CG1', 'CH1:DI1'), 'freeze_row' => 'B3')),
                                                            array('data' => $data['normal_short_duration_stp'], 'title' => 'Normal Short Duration STP', 'extra_params' => array('freeze_row' => '')),
                                                            array('data' => $data['booster_stp'], 'title' => 'Booster STP', 'extra_params' => array('freeze_row' => '')),
                                                            array('data' => $data['normal_long_duration_stp'], 'title' => 'Normal Long Duration STP', 'extra_params' => array('freeze_row' => ''))
                                                        );
                    }
                    unset($data);
                }
                catch(Exception $e){
                    $err_flag = 1;
                    $err_msg[] = 'General error: '. $e->getMessage();
                }
                catch(\Illuminate\Database\QueryException $e){
                    $err_flag = 1;
                    $err_msg[] = 'Query error: '. $e->getMessage();
                }
            }
            unset($stp_start_date);

            if(strtolower($stp_report_download_format) == 'summary'){
                $output_arr['exported_records'] = array(
                                                    array('data' => $output_arr['summary_backtest_result'], 'title' => 'Summary Backtest Result STP', 'extra_params' => array('merg_cells' => array('B1:AC1', 'AD1:BE1', 'BF1:CG1', 'CH1:DI1'), 'freeze_row' => 'B3')),
                                                );
                unset($output_arr['summary_backtest_result']);
            }
        }

        $output_arr['err_flag'] = $err_flag;
        $output_arr['err_msg'] = $err_msg;
        return $output_arr;
    }

    // function to get list of all month dates starting from the input_start_date
    public static function get_month_dates_from_input_date($input_start_date, $input_date_interval, $input_end_date){
        $output_arr = array();
        if(!empty($input_start_date) && strtotime($input_start_date) !== FALSE && !empty($input_end_date) && strtotime($input_end_date) !== FALSE){
            $input_start_date = date_create($input_start_date);
            date_add($input_start_date, date_interval_create_from_date_string($input_date_interval));
            $input_start_date = date_format($input_start_date, 'Y-m-d');
            if(strtotime($input_start_date) <= strtotime($input_end_date)){
                $output_arr = array_merge($output_arr, array($input_start_date), self::get_month_dates_from_input_date($input_start_date, $input_date_interval, $input_end_date));
            }
        }
        return $output_arr;
    }
}
