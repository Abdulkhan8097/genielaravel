<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class MfplusModel extends Model
{
    static $CONN_NAME = 'mfplus';
    public function __construct(){
    }

    public static function get_max_navdate_from_schnavbd($input_arr = array()){
        /* Possible values for $input_arr are: array('SCHEME' => scheme to be searched);
         */
        $ORACLE_DB_PREFIX_SCHEMA = env('ORACLE_DB_PREFIX_SCHEMA');
        if(!empty($ORACLE_DB_PREFIX_SCHEMA)){
            $ORACLE_DB_PREFIX_SCHEMA .= ".";
        }

        $output_arr = array('NAV_DATE' => '');
        extract($input_arr);

        try{
            $retrieved_data = DB::connection(self::$CONN_NAME)->select("SELECT MAX(NAV_DATE) AS LATEST_NAV_DATE FROM ". $ORACLE_DB_PREFIX_SCHEMA ."SCHNAVBD WHERE SCHEME = ? AND NAV_METHOD = ?", array(($SCHEME??''), 'A'));
            if($retrieved_data && isset($retrieved_data[0]) && isset($retrieved_data[0]->latest_nav_date) && !empty($retrieved_data[0]->latest_nav_date) && strtotime($retrieved_data[0]->latest_nav_date) !== FALSE){
                $output_arr['NAV_DATE'] = $retrieved_data[0]->latest_nav_date;
            }
        }
        catch(Exception $e){
            $output_arr['NAV_DATE'] = '';
        }
        return $output_arr;
    }

    public static function get_latest_scheme_portfolio($input_arr = array()){
        /* Possible values for $input_arr are: array('SCHEME' => scheme to be searched,
         *                                           'NAV_DATE' => for which date portfolio needs to be retrieved,
         *                                           'enable_query_log' => To have query log pass this parameter value as 1);
         */
        $ORACLE_DB_PREFIX_SCHEMA = env('ORACLE_DB_PREFIX_SCHEMA');
        if(!empty($ORACLE_DB_PREFIX_SCHEMA)){
            $ORACLE_DB_PREFIX_SCHEMA .= ".";
        }

        $output_arr = array('response' => array());
        $err_flag = 0;                  // err_flag is 0 means no error
        $err_msg = array();             // err_msg stores list of errors found during execution
        extract($input_arr);

        $flag_enable_query_log = false;
        if(isset($enable_query_log) && $enable_query_log){
            $flag_enable_query_log = true;
        }

        if(!isset($SCHEME)){
            $err_flag = 1;
            $err_msg[] = 'Scheme details not found';
        }
        else{
            $SCHEME = trim(strip_tags($SCHEME));
            if(empty($SCHEME)){
                $err_flag = 1;
                $err_msg[] = 'Scheme details not found';
            }
        }

        if($err_flag == 0){
            if($flag_enable_query_log){
                DB::connection(self::$CONN_NAME)->enableQueryLog();
            }

            try{
                if(isset($NAV_DATE) && !empty($NAV_DATE) && strtotime($NAV_DATE) !== FALSE){
                    // if NAV_DATE is given then retrieving portfolio for that date
                    $NAV_DATE = date('Y-m-d 00:00:00', strtotime($NAV_DATE));
                }
                else{
                    // retrieving latest NAV_DATE for input parameter SCHEME if it's not already passed as input parameter
                    $NAV_DATE = self::get_max_navdate_from_schnavbd(array('SCHEME' => $SCHEME))['NAV_DATE'];
                }

                if(empty($NAV_DATE) || strtotime($NAV_DATE) === FALSE){
                    $NAV_DATE = date('Y-m-d 00:00:00');
                }

                // retrieving scheme details
                $retrieved_data = DB::connection(self::$CONN_NAME)
                                                ->select("SELECT * FROM ". $ORACLE_DB_PREFIX_SCHEMA ."SCHNAVBD WHERE SCHEME = ? AND NAV_DATE = ? AND NAV_METHOD = ?", array($SCHEME, $NAV_DATE, 'A'));
                $output_arr['response'] = $retrieved_data;
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
                $output_arr['query_log'] = DB::connection(self::$CONN_NAME)->getQueryLog();
            }
        }

        $output_arr['err_flag'] = $err_flag;
        $output_arr['err_msg'] = $err_msg;
        return $output_arr;
    }

    public static function get_max_navdate_from_weeknav($input_arr = array()){
        /* Possible values for $input_arr are: array('SCHEME' => scheme to be searched,
         *                                           'SCHCLASS' => scheme class to be searched);
         */
        $ORACLE_DB_PREFIX_SCHEMA = env('ORACLE_DB_PREFIX_SCHEMA');
        if(!empty($ORACLE_DB_PREFIX_SCHEMA)){
            $ORACLE_DB_PREFIX_SCHEMA .= ".";
        }

        $output_arr = array('WEEKEND_DT' => '');
        extract($input_arr);

        try{
            $retrieved_data = DB::connection(self::$CONN_NAME)->select("SELECT MAX(WEEKEND_DT) AS LATEST_WEEKEND_DT FROM ". $ORACLE_DB_PREFIX_SCHEMA ."WEEKNAV WHERE SCHEME = ? AND SCHCLASS = ?", array(($SCHEME??''), ($SCHCLASS??'')));
            if($retrieved_data && isset($retrieved_data[0]) && isset($retrieved_data[0]->latest_weekend_dt) && !empty($retrieved_data[0]->latest_weekend_dt) && strtotime($retrieved_data[0]->latest_weekend_dt) !== FALSE){
                $output_arr['WEEKEND_DT'] = $retrieved_data[0]->latest_weekend_dt;
            }
        }
        catch(Exception $e){
            $output_arr['WEEKEND_DT'] = '';
        }
        return $output_arr;
    }

    public static function get_latest_scheme_aum($input_arr = array()){
        /* Possible values for $input_arr are: array('SCHEME' => scheme to be searched,
         *                                           'SCHCLASS' => scheme class to be searched,
         *                                           'enable_query_log' => To have query log pass this parameter value as 1);
         */
        $ORACLE_DB_PREFIX_SCHEMA = env('ORACLE_DB_PREFIX_SCHEMA');
        if(!empty($ORACLE_DB_PREFIX_SCHEMA)){
            $ORACLE_DB_PREFIX_SCHEMA .= ".";
        }

        $output_arr = array('response' => array());
        $err_flag = 0;                  // err_flag is 0 means no error
        $err_msg = array();             // err_msg stores list of errors found during execution
        extract($input_arr);

        $flag_enable_query_log = false;
        if(isset($enable_query_log) && $enable_query_log){
            $flag_enable_query_log = true;
        }

        if(!isset($SCHEME)){
            $err_flag = 1;
            $err_msg[] = 'Scheme details not found';
        }
        else{
            $SCHEME = trim(strip_tags($SCHEME));
            if(empty($SCHEME)){
                $err_flag = 1;
                $err_msg[] = 'Scheme details not found';
            }
        }

        if(!isset($SCHCLASS)){
            $err_flag = 1;
            $err_msg[] = 'Scheme class details not found';
        }
        else{
            $SCHCLASS = trim(strip_tags($SCHCLASS));
            if(empty($SCHCLASS)){
                $err_flag = 1;
                $err_msg[] = 'Scheme class details not found';
            }
        }

        if($err_flag == 0){
            if($flag_enable_query_log){
                DB::connection(self::$CONN_NAME)->enableQueryLog();
            }

            try{
                // retrieving latest WEEKEND_DT for input parameter SCHEME
                if(isset($WEEKEND_DT) && !empty($WEEKEND_DT) && strtotime($WEEKEND_DT) !== FALSE){
                    // if WEEKEND_DT is given then retrieving portfolio for that date
                    $WEEKEND_DT = date('Y-m-d 00:00:00', strtotime($WEEKEND_DT));
                }
                else{
                    // retrieving latest WEEKEND_DT for input parameter SCHEME if it's not already passed as input parameter
                    $WEEKEND_DT = self::get_max_navdate_from_weeknav(array('SCHEME' => $SCHEME, 'SCHCLASS' => $SCHCLASS))['WEEKEND_DT'];
                }

                if(empty($WEEKEND_DT) || strtotime($WEEKEND_DT) === FALSE){
                    $WEEKEND_DT = date('Y-m-d 00:00:00');
                }

                // retrieving scheme details
                $retrieved_data = DB::connection(self::$CONN_NAME)
                                                ->select("SELECT * FROM ". $ORACLE_DB_PREFIX_SCHEMA ."WEEKNAV WHERE SCHEME = ? AND SCHCLASS = ? AND WEEKEND_DT = ?", array($SCHEME, $SCHCLASS, $WEEKEND_DT));
                $output_arr['response'] = $retrieved_data;
                $output_arr['aum_date'] = $WEEKEND_DT;
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
                $output_arr['query_log'] = DB::connection(self::$CONN_NAME)->getQueryLog();
            }
        }

        $output_arr['err_flag'] = $err_flag;
        $output_arr['err_msg'] = $err_msg;
        return $output_arr;
    }
}
