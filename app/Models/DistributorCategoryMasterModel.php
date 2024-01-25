<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class DistributorCategoryMasterModel extends Model
{
    use HasFactory;
    // fetch director category master records
    public static function getDistributorCategory($input_arr = array()){
        $flag_status_condition_added = false;       // checking whether STATUS condition is added or not while fetching the data
        $where_conditions = array();
        if(is_array($input_arr) && count($input_arr) > 0){
            foreach($input_arr as $key => $value){
                if(in_array($key, array('print_query', 'get_count', 'order_by')) === FALSE){
                    $where_conditions[] = array($key, '=', $value);
                }

                // if STATUS already mentioned from input parameters then marking the flag_status_condition_added as TRUE
                if($key == 'status'){
                    $flag_status_condition_added = true;
                }
            }
            unset($key, $value);
        }

        // flag_status_condition_added is FALSE then fetching only ACTIVE directors only
        if(!$flag_status_condition_added){
            $where_conditions[] = array('status', '=', 1);
        }

        $enable_query_log = false;
        if(isset($input_arr['print_query']) && (intval($input_arr['print_query']) == 1)){
            $enable_query_log = true;
            DB::enableQueryLog();
        }

        // retrieving data from MySQL table: drm_distributor_category_master
        $records = DB::table('drm_distributor_category_master')
        ->select('drm_distributor_category_master.*')
        ->where($where_conditions);

        // checking whether want to retrieve count of records or not
        if(isset($input_arr['get_count']) && ($input_arr['get_count'] == 1)){
            $records = $records->count();
        }
        else{
            // get field wise data for a record
            $order_by_clause = 'drm_distributor_category_master.id ASC';
            if(isset($input_arr['order_by']) && !empty($input_arr['order_by'])){
                $order_by_clause = $input_arr['order_by'];
            }
            $records = $records->orderByRaw($order_by_clause)->get();
            unset($order_by_clause);
        }

        if($enable_query_log){
            $query = DB::getQueryLog();
            dd($query);
        }

        return $records;
    }
}
