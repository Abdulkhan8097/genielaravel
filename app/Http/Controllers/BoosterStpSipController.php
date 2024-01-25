<?php

namespace App\Http\Controllers;
use App\Exports\ArrayRecordsWithMultipleSheetsExport;
use Illuminate\Http\Request;
use DB;

class BoosterStpSipController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $decimalPrecision;
    public function __construct(){
        $this->middleware('auth');
        $this->decimalPrecision = 2;
    }

    /**
     * Show the panel to view & choose backtest result filters.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request){
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0 && $request->isMethod('post')){
            // if data is posted then showing records list in datatable or in any other requested format
            extract($request->all());                // Import variables into the current symbol table from an array
            $err_flag = 0;                           // err_flag is 0 means no error
            $err_msg = array();                      // err_msg stores list of errors found during execution
            $output_arr = array();                   // keeping this final output array as EMPTY by default

            if(isset($btn_stp_submit) && !empty($btn_stp_submit) && ($btn_stp_submit == 'submit')){
                // preparing data for STP
                if(!isset($stp_report_download_format) || (isset($stp_report_download_format) && empty($stp_report_download_format))){
                    $stp_report_download_format = 'detailed';
                }

                $exported_records = array();         // array which holds data of all gonna be exporting records
                $retrieved_data = \App\Models\BoosterStpSipModel::get_backtest_stp_result($request->all());
                if(($retrieved_data['err_flag'] == 0) && isset($retrieved_data['exported_records']) && is_array($retrieved_data['exported_records']) && count($retrieved_data['exported_records']) > 0){
                    $exported_records = $retrieved_data['exported_records'];
                }
                $downloading_file_name = '';
                if(isset($select_stp_target_scheme_label) && !empty($select_stp_target_scheme_label)){
                    $downloading_file_name .= $select_stp_target_scheme_label .'_';
                }
                if(isset($stp_multiplier_type) && !empty($stp_multiplier_type)){
                    $downloading_file_name .= $stp_multiplier_type .'_';
                }
                $downloading_file_name = create_slug($downloading_file_name . 'booster_stp_'. $stp_report_download_format .'_data_'. date('Ymd'), '_') .'.xlsx';
                return \Excel::download(new ArrayRecordsWithMultipleSheetsExport($exported_records), $downloading_file_name);
            }
            elseif(isset($btn_sip_submit) && !empty($btn_sip_submit) && ($btn_sip_submit == 'submit')){
                // preparing data for SIP
                $sip_start_date_condition = '';
                if(isset($sip_start_date) && !empty($sip_start_date)){
                    $sip_start_date .= '-01';
                    $sip_start_date_condition = ' AND source.index_date >= :sip_start_date ';
                }

                // Query for NORMAL SIP
                DB::statement("SET @opening_balance:=". $sip_opening_balance .";");
                DB::statement("SET @cumulative_sip_amount_in_target:=0.0000;");
                DB::statement("SET @target_units:=0.0000;");
                DB::statement("SET @cumulative_target_units:=0.0000;");
                DB::statement("SET @cumulative_target_amount:=0.0000;");
                DB::statement("SET @amount_transferred_to_target:=0.0000;");
                $query = DB::select("SELECT looping_year_month.index_date, 
                                    CAST(@opening_balance AS DECIMAL(25, 2)) AS sip_amount_in_target, 
                                    @cumulative_sip_amount_in_target:=CAST(CASE WHEN (@cumulative_sip_amount_in_target <= 0) THEN @opening_balance ELSE (@cumulative_sip_amount_in_target+@opening_balance) END AS DECIMAL(25, 2)) AS cumulative_sip_amount_in_target, 
                                    IFNULL(target_scheme.close, 0) AS target_scheme_value, 
                                    @target_units:=CAST((@opening_balance / IFNULL(target_scheme.close, 1)) AS DECIMAL(25, 2)) AS target_units, 
                                    @cumulative_target_units:=CAST(CASE WHEN(@cumulative_target_units <= 0) THEN @target_units ELSE (@cumulative_target_units+@target_units) END AS DECIMAL(25, 2)) AS cumulative_target_units, 
                                    @cumulative_target_amount:=CAST((@cumulative_target_units * IFNULL(target_scheme.close, 1)) AS DECIMAL(25, 2)) AS cumulative_target_amount 
                                    FROM (SELECT DATE_FORMAT(source.index_date, '%Y-%m') AS index_year_month,MAX(source.index_date) AS index_date,source.symbol 
                                    FROM quote_data_index_history AS source 
                                    INNER JOIN quote_data_index_history AS target ON (source.index_date = target.index_date) 
                                    WHERE source.symbol = :source_scheme_symbol AND target.symbol = :target_scheme_symbol ". $sip_start_date_condition ." 
                                    GROUP BY index_year_month) AS looping_year_month 
                                    INNER JOIN quote_data_index_history AS index_history ON (looping_year_month.symbol = index_history.symbol AND index_history.index_date = looping_year_month.index_date) 
                                    INNER JOIN quote_data_index_history AS target_scheme ON (looping_year_month.index_date = target_scheme.index_date) 
                                    WHERE looping_year_month.symbol = :source_scheme_symbol AND target_scheme.symbol = :target_scheme_symbol;", array(':source_scheme_symbol' => ($select_sip_source_scheme??''), ':target_scheme_symbol' => ($select_sip_target_scheme??''), ':sip_start_date' => ($sip_start_date??date('Y-m-d'))));
                $data['normal_sip'] = $query;

                // Query for BOOSTER SIP
                DB::statement("SET @sip_amount_in_source:=". $sip_opening_balance .";");
                DB::statement("SET @cumulative_sip_amount_in_target:=0.0000;");
                DB::statement("SET @opening_balance:=". $sip_opening_balance .";");
                DB::statement("SET @base_amount:=". ($sip_base_amount??0) .";");
                DB::statement("SET @opening_source_units:=-1.0000;");
                DB::statement("SET @remaining_units_in_source:=0.0000;");
                DB::statement("SET @expected_amount_transferred_to_target:=0.0000;");
                DB::statement("SET @actual_amount_transferred_to_target:=0.0000;");
                DB::statement("SET @target_units:=0.0000;");
                DB::statement("SET @cumulative_target_units:=0.0000;");
                DB::statement("SET @cumulative_target_amount:=0.0000;");
                DB::statement("SET @source_units_to_be_transferred:=0.0000;");
                $query = DB::select("SELECT looping_year_month.index_date, 
                                    CAST(@sip_amount_in_source AS DECIMAL(25, 2)) AS sip_amount_in_source, 
                                    @cumulative_sip_amount_in_target:=CAST(CASE WHEN(@cumulative_sip_amount_in_target <= 0) THEN @sip_amount_in_source ELSE (@cumulative_sip_amount_in_target + @sip_amount_in_source) END AS DECIMAL(25, 2)) AS cumulative_sip_amount_in_target, 
                                    CAST((CASE WHEN(@opening_source_units = -1) THEN @opening_balance ELSE (@opening_source_units * IFNULL(index_history.close, 0)) END) AS DECIMAL(25, 2)) AS opening_balance_in_source, 
                                    IFNULL(index_history.close, 0) AS index_value, 
                                    @opening_source_units:=CAST((CASE WHEN(@opening_source_units = -1) THEN (@opening_balance / IFNULL(index_history.close, 1)) ELSE (@remaining_units_in_source + (@sip_amount_in_source / IFNULL(index_history.close, 1))) END) AS DECIMAL(25, 2)) AS opening_source_units, 
                                    CAST(@base_amount AS DECIMAL(25, 2)) AS base_amount, index_history.margin_of_safety, 
                                    IFNULL(mos_multiplier_data.multiplier_value, 1) AS multiplier, 
                                    @expected_amount_transferred_to_target:=CAST((@base_amount * IFNULL(mos_multiplier_data.multiplier_value, 1)) AS DECIMAL(25, 2)) AS expected_amount_transferred_to_target, 
                                    @actual_amount_transferred_to_target:=CAST(CASE WHEN(@expected_amount_transferred_to_target<@opening_balance) THEN @expected_amount_transferred_to_target ELSE @opening_balance END AS DECIMAL(25, 2)) AS actual_amount_transferred_to_target, 
                                    @source_units_to_be_transferred:=CAST((@actual_amount_transferred_to_target/ IFNULL(index_history.close, 1)) AS DECIMAL(25, 2)) AS source_units_to_be_transferred, 
                                    @remaining_units_in_source:=CAST((@opening_source_units - @source_units_to_be_transferred) AS DECIMAL(25, 2)) AS remaining_units_in_source, 
                                    CAST((@remaining_units_in_source * IFNULL(index_history.close, 0)) AS DECIMAL(25, 2)) AS remaining_amount_in_source, 
                                    IFNULL(target_scheme.close, 0) AS target_scheme_value, 
                                    @target_units:=CAST((@actual_amount_transferred_to_target / IFNULL(target_scheme.close, 1)) AS DECIMAL(25, 2)) AS target_units, 
                                    @cumulative_target_units:=@cumulative_target_units+@target_units AS cumulative_target_units, 
                                    @cumulative_target_amount:=CAST((@cumulative_target_units * IFNULL(target_scheme.close, 0)) AS DECIMAL(25, 2)) AS cumulative_target_amount, 
                                    CAST((@cumulative_target_amount + (@remaining_units_in_source * IFNULL(index_history.close, 0))) AS DECIMAL(25, 2)) AS cumulative_amount_target_plus_source 
                                    FROM (SELECT DATE_FORMAT(source.index_date, '%Y-%m') AS index_year_month,MAX(source.index_date) AS index_date,source.symbol 
                                    FROM quote_data_index_history AS source 
                                    INNER JOIN quote_data_index_history AS target ON (source.index_date = target.index_date) 
                                    WHERE source.symbol = :source_scheme_symbol AND target.symbol = :target_scheme_symbol ". $sip_start_date_condition ." 
                                    GROUP BY index_year_month) AS looping_year_month 
                                    INNER JOIN quote_data_index_history AS index_history ON (looping_year_month.symbol = index_history.symbol AND index_history.index_date = looping_year_month.index_date) 
                                    INNER JOIN quote_data_index_history AS target_scheme ON (looping_year_month.index_date = target_scheme.index_date) 
                                    LEFT JOIN mos_multiplier_data ON (mos_multiplier_data.margin_of_safety = index_history.margin_of_safety AND mos_multiplier_data.multiplier_type = :multiplier_type) 
                                    WHERE looping_year_month.symbol = :source_scheme_symbol AND target_scheme.symbol = :target_scheme_symbol;", array(':source_scheme_symbol' => ($select_sip_source_scheme??''), ':target_scheme_symbol' => ($select_sip_target_scheme??''), ':multiplier_type' => ($sip_multiplier_type??''), ':sip_start_date' => ($sip_start_date??date('Y-m-d'))));
                $data['booster_sip'] = $query;

                // preparing backtest sip result
                $data['backtest_sip_result'] = array();
                $arr_unique_setup_months = array();
                if(is_array($data['booster_sip']) && count($data['booster_sip']) > 0){
                    $arr_unique_setup_months = array_merge($arr_unique_setup_months, array_column($data['booster_sip'], 'index_date'));
                }
                if(is_array($data['normal_sip']) && count($data['normal_sip']) > 0){
                    $arr_unique_setup_months = array_merge($arr_unique_setup_months, array_column($data['normal_sip'], 'index_date'));
                }
                // removing empty & duplicate date values
                $arr_unique_setup_months = array_unique(array_filter($arr_unique_setup_months));
                // sorting dates in an ASCENDING order
                asort($arr_unique_setup_months);

                if(count($arr_unique_setup_months) > 0){
                    $arr_year_data_options = array('3' => '3 Year', '5' => '5 Year', '10' => '10 Year', '-1' => 'As on Date '. $sip_end_date);
                    $arr_backtest_result_fixed_headings = array('peak_value' => 'Peak Value', 'peak_date' => 'Peak Date', 'trough_value' => 'Trough Value', 'trough_date' => 'Trough Date', 'drawdown_from_peak_percentage' => 'Drawdown from Peak% [(Peak Value -Trough Value)/Peak Value)*100]', 'drawdown_from_peak_period' => 'Drawdown from Peak Period (Trough Date - Peak Date) in Days', 'current_percentage_in_source' => 'Current % in Source (Remaining amount in Source/(Cumulative Amount (Target + Source))*100)', 'current_percentage_in_target' => 'Current % in Target (Cumulative Target Amt./Cumulative Amount (Target + Source)*100)');
                    $heading_row = array('Setup Month');
                    $year_heading_row = array('');
                    $arr_sip_options = array('booster_sip' => 'Booster SIP', 'normal_sip' => 'Normal SIP');
                    // looping for each year option
                    foreach($arr_year_data_options as $year_option){
                        // lopping for each year and SIP option
                        foreach($arr_sip_options as $sip_option){
                            $heading_row = array_merge($heading_row, array($sip_option), array_values($arr_backtest_result_fixed_headings));
                        }
                        $heading_row = array_merge($heading_row, array('End Date'));

                        for($heading_cntr = 0; $heading_cntr < ((count($arr_backtest_result_fixed_headings) + 1) * count($arr_sip_options)); $heading_cntr++){
                            if(($heading_cntr == intval(((count($arr_backtest_result_fixed_headings) + 1) * count($arr_sip_options)) / 2)) || ($heading_cntr == 0)) {
                                $year_heading_row[] = $year_option;
                            }
                            else{
                                $year_heading_row[] = '';
                            }
                        }
                        $year_heading_row[] = '';
                        unset($sip_option);
                    }
                    unset($year_option);

                    $data['backtest_sip_result'][] = $year_heading_row;
                    $data['backtest_sip_result'][] = $heading_row;

                    foreach($arr_unique_setup_months as $setup_month){
                        $looping_record = array('setup_month' => $setup_month);
                        // lopping for each year and SIP option
                        foreach($arr_year_data_options as $year_option_key => $year_option){
                            $setup_month_end_date = date_create($setup_month);
                            if($year_option_key == -1){
                                $setup_month_end_date = ($sip_end_date??date('Y-m-d'));
                            }
                            else{
                                date_add($setup_month_end_date, date_interval_create_from_date_string($year_option));
                                $setup_month_end_date = date_format($setup_month_end_date, 'Y-m-d');
                            }
                            $difference_in_days = date_diff(date_create($setup_month_end_date), date_create($setup_month));
                            $difference_in_days = $difference_in_days->format('%a');

                            foreach($arr_sip_options as $sip_option_key => $sip_option){
                                $arr_lookup_records = ($data[$sip_option_key]??array());
                                $arr_lookup_records = array_column($arr_lookup_records, NULL, 'index_date');

                                $arr_setup_date_record = array();
                                $peak_value = 0;
                                $peak_date = '';
                                $trough_value = 0;
                                $trough_date = '';
                                $cumulative_amount_target_plus_source = 0;
                                $cumulative_target_amount = 0;
                                $remaining_amount_in_source = 0;

                                $looping_record[$year_option_key . '_' . $sip_option_key . '_cagr'] = '';

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
                                        $looping_record[$year_option_key . '_' . $sip_option_key . '_cagr'] = pow(($cumulative_amount_target_plus_source/$sip_opening_balance), (1/(!empty(($difference_in_days/365))?($difference_in_days/365):1)));
                                        $looping_record[$year_option_key . '_' . $sip_option_key . '_cagr'] = (($looping_record[$year_option_key . '_' . $sip_option_key. '_cagr'] - 1) * 100);
                                        $looping_record[$year_option_key . '_' . $sip_option_key . '_cagr'] = round($looping_record[$year_option_key . '_' . $sip_option_key . '_cagr'], $this->decimalPrecision);
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

                                foreach($arr_backtest_result_fixed_headings as $heading_key => $heading_label){
                                    switch($heading_key){
                                        case 'peak_value':
                                            // Highest Value in Cumulative Amount (Target + Source) between End Date and Setup Date for respective sheet (Booster SIP/ Normal Short Duration SIP/ Normal Long Duration SIP)
                                            $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = round($peak_value, $this->decimalPrecision);
                                            break;
                                        case 'peak_date':
                                            // Corresponding Date for Peak Value
                                            $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = $peak_date;
                                            break;
                                        case 'trough_value':
                                            // Lowest Value in Cumulative Amount (Target + Source) post peak date till End Date for respective sheet (Booster SIP/ Normal Short Duration SIP/ Normal Long Duration SIP)
                                            if(!empty($trough_value) && is_numeric($trough_value)){
                                                $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = round($trough_value, $this->decimalPrecision);
                                            }
                                            else{
                                                $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = 'NA';
                                            }
                                            break;
                                        case 'trough_date':
                                            // Corresponding Date for Trough Value
                                            $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = $trough_date;
                                            break;
                                        case 'drawdown_from_peak_percentage':
                                            // [(Peak Value -Trough Value)/Peak Value)*100]
                                            $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = '';
                                            if(!empty($peak_date) && !empty($trough_date)){
                                                $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = (!empty($peak_value)?((($peak_value - $trough_value) / $peak_value) * 100):'');
                                                $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = round($looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key], $this->decimalPrecision);
                                            }
                                            break;
                                        case 'drawdown_from_peak_period':
                                            // (Trough Date - Peak Date)
                                            $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = '';
                                            if(!empty($peak_date) && strtotime($peak_date) !== FALSE && !empty($trough_date) && strtotime($trough_date) !== FALSE){
                                                $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = date_diff(date_create($peak_date), date_create($trough_date));
                                                $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key]->format('%a');
                                            }
                                            break;
                                        case 'current_percentage_in_source':
                                            // (Remaining amount in Source as on End Date/(Cumulative Amount (Target + Source)) as on End Date*100)
                                            $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = (!empty($cumulative_amount_target_plus_source)?(($remaining_amount_in_source / $cumulative_amount_target_plus_source) * 100):0);
                                            $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = round($looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key], $this->decimalPrecision);
                                            break;
                                        case 'current_percentage_in_target':
                                            // (Cumulative Target Amt. as on End Date /Cumulative Amount (Target + Source) as on End Date *100)
                                            $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = (!empty($cumulative_amount_target_plus_source)?(($cumulative_target_amount / $cumulative_amount_target_plus_source) * 100):0);
                                            $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = round($looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key], $this->decimalPrecision);
                                            break;
                                        default:
                                            $looping_record[$year_option_key . '_' . $sip_option_key . '_'. $heading_key] = '';
                                    }
                                }
                                unset($heading_key, $heading_label);
                                unset($arr_lookup_records, $peak_value, $peak_date, $trough_value, $trough_date, $cumulative_amount_target_plus_source, $cumulative_target_amount, $remaining_amount_in_source);
                            }
                            $looping_record[$year_option_key . '_setup_month_end_date'] = $setup_month_end_date;
                            unset($sip_option_key, $sip_option);
                            unset($setup_month_end_date, $difference_in_days);
                        }
                        unset($year_option_key, $year_option);

                        $data['backtest_sip_result'][] = $looping_record;
                        unset($looping_record);
                    }
                    unset($setup_month, $arr_backtest_result_fixed_headings, $heading_row, $year_heading_row, $arr_sip_options, $arr_year_data_options);
                }
                unset($arr_unique_setup_months);

                $target_scheme_name_text = 'Target Scheme';
                if(isset($select_sip_target_scheme_label) && !empty($select_sip_target_scheme_label)){
                    $target_scheme_name_text = $select_sip_target_scheme_label;
                }
                $source_scheme_name_text = 'Source';
                if(isset($select_sip_source_scheme_label) && !empty($select_sip_source_scheme_label)){
                    $source_scheme_name_text = $select_sip_source_scheme_label;
                }

                if(is_array($data['normal_sip']) && count($data['normal_sip']) > 0){
                    // $data['normal_sip'] = array_merge(array((object) array_keys((array) $data['normal_sip'][0])), $data['normal_sip']);
                    $data['normal_sip'] = array_merge(array(array('SIP Month', 'SIP Amount in Target', 'Cumulative SIP Amount in Target', $target_scheme_name_text .' NAV', 'Target Units', 'Cumulative Target Units', 'Cumulative Target Amount')), $data['normal_sip']);
                }
                if(is_array($data['booster_sip']) && count($data['booster_sip']) > 0){
                    // $data['booster_sip'] = array_merge(array((object) array_keys((array) $data['booster_sip'][0])), $data['booster_sip']);
                    $data['booster_sip'] = array_merge(array(array('SIP Month', 'SIP Amount in Source', 'Cumulative SIP Amount in Target', 'Opening Balance in Source', $source_scheme_name_text .' NAV', 'Opening Source Units', 'Base Amount', 'EMOSI', 'Multiplier', 'Expected Amount transferred to Target', 'Actual Amount transferred to Target', 'Source Units to be  transferred', 'Remaining Units in Source', 'Remaning Amount in Source', $target_scheme_name_text .' NAV', 'Target Units', 'Cumulative Target Units', 'Cumulative Target Amount', 'Cumulative Amount (Target + Source)')), $data['booster_sip']);
                }
                $downloading_file_name = '';
                if(isset($select_sip_target_scheme_label) && !empty($select_sip_target_scheme_label)){
                    $downloading_file_name .= $select_sip_target_scheme_label .'_';
                }
                if(isset($sip_multiplier_type) && !empty($sip_multiplier_type)){
                    $downloading_file_name .= $sip_multiplier_type .'_';
                }
                $downloading_file_name = create_slug($downloading_file_name . 'booster_sip_data_'. date('Ymd'), '_') .'.xlsx';
                return \Excel::download(new ArrayRecordsWithMultipleSheetsExport(
                                            array(
                                                array('data' => $data['backtest_sip_result'], 'title' => 'Backtest Result SIP', 'extra_params' => array('merg_cells' => array('B1:T1', 'U1:AM1', 'AN1:BF1', 'BG1:BY1'), 'freeze_row' => 'B3')),
                                                array('data' => $data['booster_sip'], 'title' => 'Booster SIP', 'extra_params' => array('freeze_row' => '')),
                                                array('data' => $data['normal_sip'], 'title' => 'Normal SIP', 'extra_params' => array('freeze_row' => '')),
                                            )
                                        ), $downloading_file_name);
            }

            $output_arr['err_flag'] = $err_flag;
            $output_arr['err_msg'] = $err_msg;
            return response()->json($output_arr);
        }
        else{
            // loading page first time
            $data = array('arr_multiplier_type' => array());

            // retrieving distinct multiplier type values from MySQL table: mos_multiplier_data
            $retrieved_data = \App\Models\MosMultiplierData::get_distinct_multiplier_type();
            if($retrieved_data['err_flag'] == 0 && isset($retrieved_data['multiplier_type']) && is_array($retrieved_data['multiplier_type']) && count($retrieved_data['multiplier_type']) > 0){
                $data['arr_multiplier_type'] = $retrieved_data['multiplier_type'];
            }
            unset($retrieved_data);

            // retrieving index symbol which can be further bifurcated into SOURCE & TARGET schemes
            $data['arr_index_symbol'] = \App\Models\QuoteDataIndexDetails::all('symbol', 'display_name', 'source_target')->toArray();
            return view('booster-stp-sip/backtest-result', $data);
        }
    }
}
