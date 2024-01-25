<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class MosMultiplierData extends Model
{
    use HasFactory;
    protected $table = 'mos_multiplier_data';

    // retrieving distinct multiplier types
    public static function get_distinct_multiplier_type(){
        $output_arr = array('multiplier_type' => array());
        $err_flag = 0;                  // err_flag is 0 means no error
        $err_msg = array();             // err_msg stores list of errors found during execution

        try{
            $retrieved_data = self::select('multiplier_type')
                                    ->groupBy('multiplier_type')
                                    ->orderBy('multiplier_type')
                                    ->get()
                                    ->toArray();
            if(is_array($retrieved_data) && count($retrieved_data) > 0){
                $output_arr['multiplier_type'] = array_column($retrieved_data, 'multiplier_type');
            }
            unset($retrieved_data);
        }
        catch(Exception $e){
            $err_flag = 1;
            $err_msg[] = 'Exception: '. $e->getMessage();
        }
        catch(\Illuminate\Database\QueryException $e){
            $err_flag = 1;
            $err_msg[] = 'Query error: '. $e->getMessage();
        }

        $output_arr['err_flag'] = $err_flag;
        $output_arr['err_msg'] = $err_msg;
        return $output_arr;
    }
}
