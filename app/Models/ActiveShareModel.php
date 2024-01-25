<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Libraries\PhpMailer;

class ActiveShareModel extends Model
{
    use HasFactory;
    public static function get_scheme_list($request = array()){
        extract($request);
        if(!isset($search)){
            $search = '';
        }
        // DB::enableQueryLog();
        $scheme_list=array();
        $records = DB::connection('rankmf')
        ->table('mf_aggregated_search_data')
        ->select('Scheme_Code','schemecode','Scheme_Name','primary_fd_code')
        ->whereIn('Purchase_Transaction_mode_ag',['DP','D'])
        ->where('primary_scheme',1)
        ->where('rank_srno','!=',0)
        ->where('soft_delete_status',1)
        ->where('Scheme_Name', 'like', '%' . $search . '%')
        ->get();
        // dd(DB::getQueryLog());
        foreach($records as $key => $value){
            $scheme_list[] = array(
                'id' => $value->schemecode, 
                'text' => $value->Scheme_Name
            );
        }
         $return_data =  json_encode(array('items' => $scheme_list));
        // x($return_data);
        echo $return_data;
    }

    public static function getActiveShare($request = array()){
        extract($request);
        // x($request);
        $query = "select t.*, Coalesce(im.weightage,0) as index_weightage, 
ABS(t.Holdpercentage - Coalesce(im.weightage,0)) as abs_diff, 
ABS(t.Holdpercentage - Coalesce(im.weightage,0))/2 as active_share_contribution, latest_nse.monthYear from (
#scheme wise index code
select sm.schemecode,si.Scheme_Name,si.classname,si.IndexCode,si.IndexName,sm.ISIN,sm.symbol,sm.fincode,sm.COMPNAME,sm.AUM,sm.Holdpercentage,index_name_master from (
select ag.SchemeCode,ag.Scheme_Name,ag.classname, im.IndexCode, im.IndexName, sim.IndicesName as index_name_master
from
(SELECT schemecode,scheme_name,classname FROM `mf_aggregated_search_data` WHERE `classname` NOT LIKE '%Index Funds%' group by schemecode ) as ag,
`mf_scheme_index_part_accord` ip, `mf_index_mst_accord` im,
samco_index_master as sim
where ip.IndexCode=im.IndexCode and ag.schemecode = ip.SchemeCode and ip.IndexCode = sim.IndexCode
) as si,


#scheme wise company holding
(select mf_aggregated_search_data.schemecode,cpa.Invenddate,cpa.AUM,cpa.Holdpercentage,cm.fincode,cm.symbol,cm.COMPNAME,cm.isin from (SELECT a.*
FROM mf_current_portfolio_accord AS a 
INNER JOIN (SELECT schemecode, MAX(InvDate) AS Invdate 
FROM mf_current_portfolio_accord 
GROUP BY schemecode) AS b ON (a.schemecode = b.schemecode AND a.Invdate = b.Invdate) 
WHERE 1) cpa,
(SELECT * FROM `mf_companymaster_accord` group by fincode) as cm, mf_aggregated_search_data 
where cpa.fincode=cm.fincode AND cpa.Schemecode = mf_aggregated_search_data.primary_fd_code) as sm

where si.schemecode = sm.schemecode 
and sm.schemecode = ". $schemecode ." 
#and si.indexcode=154
) as t
left join nseIndicesActiveShares im
on t.symbol=im.symbol and BINARY t.index_name_master = BINARY im.indicesName 
left join (SELECT indicesName, MAX(monthYear) AS monthYear FROM nseIndicesActiveShares GROUP BY indicesName) AS latest_nse on (im.indicesName = latest_nse.indicesName AND im.monthYear = latest_nse.monthYear) 
group by t.schemecode, t.indexcode, t.fincode
UNION ALL
select si.schemecode, si.Scheme_Name, si.classname, si.IndexCode, si.IndexName, comp.ISIN AS 'ISIN', ind.symbol, comp.fincode, comp.COMPNAME AS 'compname', AUM, 0.00 AS 'Holdpercentage', si.index_name_master, ind.weightage AS 'index_weightage', ind.weightage AS 'abs_diff', (ind.weightage / 2) AS 'active_share_contribution', latest_nse.monthYear 
from (
select ag.SchemeCode,ag.Scheme_Name,ag.classname, im.IndexCode, im.IndexName, sim.IndicesName as index_name_master
from
(SELECT schemecode,scheme_name,classname FROM `mf_aggregated_search_data` WHERE `classname` NOT LIKE '%Index Funds%' group by schemecode ) as ag,
`mf_scheme_index_part_accord` ip, `mf_index_mst_accord` im,
samco_index_master as sim
where ip.IndexCode=im.IndexCode and ag.schemecode = ip.SchemeCode and ip.IndexCode = sim.IndexCode
) as si 
INNER JOIN nseIndicesActiveShares as ind ON (si.index_name_master = ind.indicesName) 
INNER JOIN (SELECT indicesName, MAX(monthYear) AS monthYear FROM nseIndicesActiveShares GROUP BY indicesName) AS latest_nse on (ind.indicesName = latest_nse.indicesName AND ind.monthYear = latest_nse.monthYear) 
LEFT JOIN (SELECT fincode, SYMBOL, ISIN, COMPNAME FROM `mf_companymaster_accord` group by SYMBOL) as comp ON (ind.symbol = comp.SYMBOL) 
LEFT JOIN (select mf_aggregated_search_data.schemecode,cpa.Invenddate,cpa.AUM,cpa.Holdpercentage,cm.symbol,cm.COMPNAME,cm.isin from (SELECT a.*
FROM mf_current_portfolio_accord AS a 
INNER JOIN (SELECT schemecode, MAX(InvDate) AS Invdate 
FROM mf_current_portfolio_accord 
GROUP BY schemecode) AS b ON (a.schemecode = b.schemecode AND a.Invdate = b.Invdate) 
WHERE 1) cpa,
(SELECT * FROM `mf_companymaster_accord` group by fincode) as cm, mf_aggregated_search_data 
where cpa.fincode=cm.fincode AND cpa.Schemecode = mf_aggregated_search_data.primary_fd_code and cm.symbol <> '') as sm ON (sm.schemecode = si.schemecode AND sm.symbol = ind.symbol) 
WHERE si.schemecode = ". $schemecode ." AND sm.symbol IS NULL;";
        
        return $records = DB::connection('rankmf')
        ->select($query);

        // x($records,'shemecode');
    }

    public static function calculate_active_share($input_arr = array()){
        /* Possible values for $input_arr are: array('rta_scheme_code' => RTA schemecode to be searched,
         *                                           'enable_query_log' => To have query log pass this parameter value as 1);
         */
        $to_mail = getSettingsTableValue('ACTIVE_SHARE_EMAIL_NOTIFY_TO');
        if(isset($to_mail) && !empty($to_mail)){
            $to_mail = explode(',',$to_mail);
            $expload_to_mail = array();
            foreach($to_mail as $v){
                $expload_to_mail[] = array($v);
            }
        }

        $check_holidays = DB::connection('invdb')->table('mf_holidays')->where('date','=',date('Y-m-d'))->get();
        if(!$check_holidays->isEmpty()){
            // SENDING AN EMAIL WITH Holiday Message
            if(isset($expload_to_mail) && is_array($expload_to_mail) && !empty($expload_to_mail)){
                $mailer = new PhpMailer();
                $params = [];
                $template = "SAMCOMF-GENERAL-NOTIFICATION";
                $params['templateName'] = $template;
                $params['channel']      = $template;
                $params['from_email']   = "alerts@samcomf.com";
                $params['to']           = $expload_to_mail;
                $params['merge_vars'] = array('MAIL_BODY' => 'ACTIVE SHARE IS NOT CALCULATED DUE TO HOLIDAY');
                $params['subject'] = '['. date('d M Y H:i:s') . ']: ACTIVE SHARE IS NOT CALCULATED DUE TO HOLIDAY';
                $email_send = $mailer->mandrill_send($params);
            }
            return false;
        }
        $output_arr = array('response' => array());
        $err_flag = 0;                  // err_flag is 0 means no error
        $err_msg = array();             // err_msg stores list of errors found during execution
        extract($input_arr);

        $flag_enable_query_log = false; // helps to get list of queries executed during execution if it's value passed as TRUE
        if(isset($enable_query_log) && $enable_query_log){
            $flag_enable_query_log = true;
        }

        // if active_share_date not passed as input parameter then considering it as BLANK
        if(!isset($active_share_date)){
            $active_share_date = '';
        }
        if(!empty($active_share_date) && strtotime($active_share_date) !== FALSE){
            $active_share_date = date('Y-m-d', strtotime($active_share_date));
        }

        if($flag_enable_query_log){
            DB::connection('rankmf')->enableQueryLog();
            DB::connection('invdb')->enableQueryLog();
            DB::enableQueryLog();
        }

        $where_conditions = array();
        $where_conditions[] = array('mfplus_scheme', '!=', '');
        if(isset($rta_scheme_code) && !empty($rta_scheme_code)){
            $where_conditions[] = array('RTA_Scheme_Code', '=', $rta_scheme_code);
        }

        $calculated_schemes_active_share = array();
        $active_share_records = array();
        $active_share_calculated_dates = array();   // helps to identify dates for whom active share got calculated and we need to remove data for those dates if it's already present in MySQL table: active_share
        try{
            // retrieving list of schemes for whom active share needs to be calculated
            $retrieved_schemes = DB::connection('invdb')->table('scheme_master')
                                                     ->select('scheme', 'RTA_Scheme_Code', 'Scheme_Plan', 'Scheme_Plan_Code', 'Scheme_Name', 'mfplus_scheme')
                                                     ->whereNotNull('mfplus_scheme')
                                                     ->groupBy('mfplus_scheme');
            if(count($where_conditions) > 0){
                $retrieved_schemes = $retrieved_schemes->where($where_conditions);
            }
            $retrieved_schemes = $retrieved_schemes->get();

            if($retrieved_schemes && !$retrieved_schemes->isEmpty()){
                foreach($retrieved_schemes as $scheme_record){
                    $mfplus_scheme_name = $scheme_record->mfplus_scheme;
                    // retrieving benchmarks for looping scheme
                    $retrieved_scheme_benchmarks = DB::connection('invdb')->table('scheme_master_benchmarks')
                                                                          ->select('Index_code', 'Index_name')
                                                                          ->where('RTA_Scheme_Code', $scheme_record->RTA_Scheme_Code)
                                                                          ->where('status', 1)
                                                                          ->get()->toArray();
                    if(!$retrieved_scheme_benchmarks || (count($retrieved_scheme_benchmarks) == 0)){
                        // skipping active share calculation for looping scheme because it's benchmark details not available
                        continue;
                    }
                    $scheme_record->benchmarks = array_column($retrieved_scheme_benchmarks, NULL, 'Index_code');
                    unset($retrieved_scheme_benchmarks);

                    if(!isset($calculated_schemes_active_share[$mfplus_scheme_name])){
                        $calculated_schemes_active_share[$mfplus_scheme_name] = array('portfolio_records' => array(),
                                                                                      'aum' => array(),
                                                                                      'active_share' => array()
                                                                                );

                        // finding portfolio details for scheme
                        $scheme_portfolio_records = \App\Models\MfplusModel::get_latest_scheme_portfolio(array('SCHEME' => $mfplus_scheme_name,
                                                                                   'enable_query_log' => $flag_enable_query_log,
                                                                                   'NAV_DATE' => $active_share_date
                                                                                )
                                                                            );
                        if($scheme_portfolio_records['err_flag'] == 0 && isset($scheme_portfolio_records['response']) && count($scheme_portfolio_records['response']) > 0){
                            array_walk($scheme_portfolio_records['response'], function(&$_value){
                                // JIRA ID: SMF-461. STARTS
                                if(strtolower($_value->security) == 'lti'){
                                    $_value->security = 'LTIM';
                                }
                                // JIRA ID: SMF-461. ENDS
                            });
                            $calculated_schemes_active_share[$mfplus_scheme_name]['portfolio_records'] = $scheme_portfolio_records['response'];
                        }
                        unset($scheme_portfolio_records);

                        // finding AUM for scheme
                        $scheme_aum = \App\Models\MfplusModel::get_latest_scheme_aum(array('SCHEME' => $mfplus_scheme_name,
                                                                              'SCHCLASS' => 'GLOBAL',
                                                                              'WEEKEND_DT' => $active_share_date
                                                                            )
                                                                    );
                        if($scheme_aum['err_flag'] == 0 && isset($scheme_aum['response']) && count($scheme_aum['response']) > 0){
                            $calculated_schemes_active_share[$mfplus_scheme_name]['aum'] = $scheme_aum['response'];
                            $calculated_schemes_active_share[$mfplus_scheme_name]['latest_aum'] = ($calculated_schemes_active_share[$mfplus_scheme_name]['aum'][0]->net_assets??0);
                            $calculated_schemes_active_share[$mfplus_scheme_name]['aum_date'] = ($scheme_aum['aum_date']??date('Y-m-d'));
                        }
                        unset($scheme_aum);

                        if(count($calculated_schemes_active_share[$mfplus_scheme_name]['portfolio_records']) > 0 && count($calculated_schemes_active_share[$mfplus_scheme_name]['aum']) > 0){
                            // calculating active share for scheme based on retrieved portfolio values
                            $looping_scheme_aum = ($calculated_schemes_active_share[$mfplus_scheme_name]['aum'][0]->net_assets??0);
                            $looping_scheme_aum_date = ($calculated_schemes_active_share[$mfplus_scheme_name]['aum_date']??date('Y-m-d'));
                            $looping_scheme_portfolio_active_share = array();

                            foreach($calculated_schemes_active_share[$mfplus_scheme_name]['portfolio_records'] as $portfolio_record){
                                $looping_scheme_holdpercentage = 0;
                                if($looping_scheme_aum > 0){
                                    $looping_scheme_holdpercentage = ((($portfolio_record->amount * -1) / $looping_scheme_aum) * 100);
                                }

                                if(empty($portfolio_record->name)){
                                    $portfolio_record->name = 'Net Current Assets';
                                }

                                $looping_scheme_portfolio_active_share[$portfolio_record->security] = array('compname' => $portfolio_record->name,
                                                                        'schemecode' => '',
                                                                        'indexcode' => '',
                                                                        'indexname' => '',
                                                                        'symbol' => $portfolio_record->security,
                                                                        'aum' => $calculated_schemes_active_share[$mfplus_scheme_name]['latest_aum'],
                                                                        // 'mktval' => ($portfolio_record->amount * -1),
                                                                        'holdpercentage' => $looping_scheme_holdpercentage,
                                                                        'index_weightage' => 0,
                                                                        'abs_diff' => abs($looping_scheme_holdpercentage),
                                                                        'active_share_contribution' => (abs($looping_scheme_holdpercentage) / 2),
                                                                        'active_share_date' => $calculated_schemes_active_share[$mfplus_scheme_name]['aum_date']
                                                                    );

                                // adding active_share_date in an array $active_share_calculated_dates if it's not already present in it
                                if(in_array($calculated_schemes_active_share[$mfplus_scheme_name]['aum_date'], $active_share_calculated_dates) === FALSE){
                                    $active_share_calculated_dates[] = $calculated_schemes_active_share[$mfplus_scheme_name]['aum_date'];
                                }
                                unset($looping_scheme_holdpercentage);
                            }
                            unset($portfolio_record);

                            $calculated_schemes_active_share[$mfplus_scheme_name]['active_share'] = $looping_scheme_portfolio_active_share;
                            unset($looping_scheme_aum, $looping_scheme_aum_date, $looping_scheme_portfolio_active_share);
                        }
                    }

                    if(is_array($calculated_schemes_active_share[$mfplus_scheme_name]) && count($calculated_schemes_active_share[$mfplus_scheme_name]) > 0){
                        // preparing data to be entered into MySQL table: active_share

                        $scheme_benchmarks_keys = $scheme_record->benchmarks;
                        if(is_array($scheme_benchmarks_keys) && count($scheme_benchmarks_keys) > 0){
                            foreach($scheme_benchmarks_keys as $scheme_benchmark){
                                $scheme_benchmark_index_code = $scheme_benchmark->Index_code;
                                $scheme_benchmark_index_name = $scheme_benchmark->Index_name;
                                $active_share_scheme_code = $scheme_record->mfplus_scheme;
                                $active_share_records[$active_share_scheme_code][$scheme_benchmark_index_code] = $calculated_schemes_active_share[$mfplus_scheme_name]['active_share'];
                                // assigning index code and index name for portfolio records
                                array_walk($active_share_records[$active_share_scheme_code][$scheme_benchmark_index_code], function(&$_value) use($scheme_benchmark_index_code, $scheme_benchmark_index_name, $scheme_record, $active_share_scheme_code){
                                    $_value['schemecode'] = $active_share_scheme_code;
                                    $_value['indexcode'] = $scheme_benchmark_index_code;
                                    $_value['indexname'] = $scheme_benchmark_index_name;
                                });

                                // retrieving NSE indices constituents based on benchmark retrieved for a looping scheme
                                $retrieved_nseindices_active_share = DB::connection('rankmf')->table('nseIndicesActiveShares AS a')
                                                                        ->select('a.*')
                                                                        ->join('samco_index_master AS b', 'a.indicesName', '=', 'b.IndicesName')
                                                                        ->join(DB::raw('(SELECT indicesName, MAX(monthYear) AS monthYear FROM nseIndicesActiveShares GROUP BY indicesName) AS latest_nse'), function($join){
                                                                            $join->on('a.indicesName', '=', 'latest_nse.indicesName');
                                                                            $join->on('a.monthYear', '=', 'latest_nse.monthYear');
                                                                        })
                                                                        ->where('b.IndexCode', $scheme_benchmark_index_code)
                                                                        ->get()->toArray();
                                if($retrieved_nseindices_active_share && count($retrieved_nseindices_active_share) > 0){
                                    // coming here if indices data found, checking those records against portfolio records
                                    foreach($retrieved_nseindices_active_share as $indices_record){
                                        if(isset($active_share_records[$active_share_scheme_code][$scheme_benchmark_index_code][$indices_record->symbol])){
                                            // if symbol already present with portfolio records then just subtracting weightage from holding percentage
                                            $active_share_records[$active_share_scheme_code][$scheme_benchmark_index_code][$indices_record->symbol]['indexcode'] = $scheme_benchmark_index_code;
                                            $active_share_records[$active_share_scheme_code][$scheme_benchmark_index_code][$indices_record->symbol]['indexname'] = $scheme_benchmark_index_name;
                                            $active_share_records[$active_share_scheme_code][$scheme_benchmark_index_code][$indices_record->symbol]['index_weightage'] = $indices_record->weightage;
                                            $active_share_records[$active_share_scheme_code][$scheme_benchmark_index_code][$indices_record->symbol]['abs_diff'] -= $indices_record->weightage;
                                            $active_share_records[$active_share_scheme_code][$scheme_benchmark_index_code][$indices_record->symbol]['abs_diff'] = abs($active_share_records[$active_share_scheme_code][$scheme_benchmark_index_code][$indices_record->symbol]['abs_diff']);
                                            $active_share_records[$active_share_scheme_code][$scheme_benchmark_index_code][$indices_record->symbol]['active_share_contribution'] = ($active_share_records[$active_share_scheme_code][$scheme_benchmark_index_code][$indices_record->symbol]['abs_diff'] / 2);
                                        }
                                        else{
                                            // as symbol is not present in portfolio record then adding that record
                                            $active_share_records[$active_share_scheme_code][$scheme_benchmark_index_code][$indices_record->symbol] = array('compname' => $indices_record->securityName,
                                                'schemecode' => $active_share_scheme_code,
                                                'indexcode' => $scheme_benchmark_index_code,
                                                'indexname' => $scheme_benchmark_index_name,
                                                'symbol' => $indices_record->symbol,
                                                'aum' => $calculated_schemes_active_share[$mfplus_scheme_name]['latest_aum'],
                                                // 'mktval' => $indices_record->indexMcap,
                                                'holdpercentage' => 0,
                                                'index_weightage' => $indices_record->weightage,
                                                'abs_diff' => abs($indices_record->weightage),
                                                'active_share_contribution' => (abs($indices_record->weightage) / 2),
                                                'active_share_date' => $calculated_schemes_active_share[$mfplus_scheme_name]['aum_date']
                                            );

                                            // adding active_share_date in an array $active_share_calculated_dates if it's not already present in it
                                            if(in_array($calculated_schemes_active_share[$mfplus_scheme_name]['aum_date'], $active_share_calculated_dates) === FALSE){
                                                $active_share_calculated_dates[] = $calculated_schemes_active_share[$mfplus_scheme_name]['aum_date'];
                                            }
                                        }
                                    }
                                    unset($indices_record);
                                }
                                // $output_arr['retrieved_nseindices_active_share'] = $retrieved_nseindices_active_share;
                                unset($retrieved_nseindices_active_share, $scheme_benchmark_index_code, $scheme_benchmark_index_name, $active_share_scheme_code);
                            }
                            unset($scheme_benchmark);
                        }
                        unset($scheme_benchmarks_keys);
                    }
                    unset($mfplus_scheme_name);
                }
                unset($scheme_record);
            }
            unset($retrieved_schemes);
        }
        catch(Exception $e){
            $err_flag = 1;
            $err_msg[] = 'Exception: '. $e->getMessage();
        }
        catch(\Illuminate\Database\QueryException $e){
            $err_flag = 1;
            $err_msg[] = 'Query exception: '. $e->getMessage();
        }

        $output_arr['active_share_records'] = $active_share_records;
        // $output_arr['calculated_schemes_active_share'] = $calculated_schemes_active_share;

        // removing older records of calculated active share for dates present in variable $active_share_calculated_dates from MySQL table: active_share
        if(is_array($active_share_calculated_dates) && count($active_share_calculated_dates) > 0){
            DB::connection('invdb')->table('active_share')->whereIn('active_share_date', $active_share_calculated_dates)->delete();
        }
        //header for ACTIVE SHARE CSV EXPORT
        $excel_header = array('compname' => array('label' => 'Company Name'),
                                            'schemecode' => array('label' => 'Scheme Code'),
                                            'indexcode' => array('label' => 'Index Code'),
                                            'indexname' => array('label' => 'Index Name'),
                                            'symbol'=>array('label' =>'Symbol'),
                                            'aum'=>array('label' =>'AUM'),
                                            'holdpercentage'=>array('label' =>'Hold Percentage'),
                                            'index_weightage'=>array('label' =>'Index Weightage'),
                                            'abs_diff'=>array('label' =>'Absolute Difference'),
                                            'active_share_contribution'=>array('label' =>'Active Share Contribution'),
                                            'active_share_date'=>array('label' =>'Active Share Date'),
                                        );

        $csv_headers = array_column($excel_header, 'label');
        $export_arr[] = $csv_headers;
        // inserting newly created active share records into MySQL table: active_share
        if(is_array($active_share_records) && count($active_share_records) > 0){
            foreach($active_share_records as $scheme_name => $scheme_record){
                foreach($scheme_record as $index_code => $index_record){
                    DB::connection('invdb')->table('active_share')->insert($index_record);
                    //assigning value for csv headers for export
                    foreach($index_record as $key => $value) {
                    $row = array();
                    foreach($excel_header as $field_name_key => $field_name_value){
                        $row[$field_name_key] = '';
                        if(isset($value[$field_name_key])){
                            $row[$field_name_key] = $value[$field_name_key];
                        }
                    }
                    $export_arr[] = $row;
                    unset($field_name_key, $field_name_value);
                    }
                    unset($key, $value, $csv_headers);
                }
                unset($index_code, $index_record);
            }
            unset($scheme_name, $scheme_record);
        }
        $file_csv =  fopen(sys_get_temp_dir().'/ACTIVE_SHARE_DATA_'. date('Y-m-d') .'.csv','w');
        // Loop through file pointer and a line
        foreach ($export_arr as $fields) {
            fputcsv($file_csv, $fields);
        }
        fclose($file_csv);

        $csv_file_path = sys_get_temp_dir().'/ACTIVE_SHARE_DATA_'. date('Y-m-d') .'.csv';
        $to_mail = getSettingsTableValue('ACTIVE_SHARE_EMAIL_NOTIFY_TO');
        if(isset($to_mail) && !empty($to_mail)){
            $to_mail = explode(',',$to_mail);
            $expload_to_mail = array();
            foreach($to_mail as $v){
                $expload_to_mail[] = array($v);
            }
        }
        // SENDING AN EMAIL WITH ACTIVE SHARE CSV STORED FILE
        if(isset($expload_to_mail) && is_array($expload_to_mail) && !empty($expload_to_mail)){
            $mailer = new PhpMailer();
            $params = [];
            $template = "SAMCOMF-GENERAL-NOTIFICATION";
            $params['templateName'] = $template;
            $params['channel']      = $template;
            $params['from_email']   = "alerts@samcomf.com";
            $params['to']           = $expload_to_mail;
            $params['attachment']   = array(array($csv_file_path));
            $params['merge_vars'] = array('MAIL_BODY' => 'PFA');
            $params['subject'] = '['. date('d M Y H:i:s') . ']: ACTIVE SHARE CALCULATED';
            $email_send = $mailer->mandrill_send($params);
        }

        $output_arr['response'] = $active_share_records;

        if($flag_enable_query_log){
            $output_arr['query_log']['rankmf'] = DB::connection('rankmf')->getQueryLog();
            $output_arr['query_log']['invdb'] = DB::connection('invdb')->getQueryLog();
            $output_arr['query_log']['default'] = DB::getQueryLog();
        }
        unset($where_conditions, $flag_enable_query_log, $active_share_calculated_dates);

        $output_arr['err_flag'] = $err_flag;
        $output_arr['err_msg'] = $err_msg;
        return $output_arr;
    }
}
