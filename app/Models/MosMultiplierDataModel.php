<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class MosMultiplierDataModel extends Model
{
    protected $table = 'mos_multiplier_data';
    protected $fillable = ['id','multiplier_type','margin_of_safety','multiplier_value','status','created_at','updated_at'];

    public static function getMostMultiplierDataDB($input_arr = array(),$id='')
   {
      extract($input_arr);
        
      $multiplier_search= $input_arr['multiplier'];
      $search_value = $input_arr['search']['value'];
      $where_conditions = array();
    
     if( !empty($search_value)){
        $where_conditions[] = array('multiplier_type', 'like', '%'.$search_value.'%');         
        $where_conditions[] = array('margin_of_safety', 'like', '%'.$search_value.'%');         
        $where_conditions[] = array('multiplier_value', 'like', '%'.$search_value.'%');         
                        }

     $flag_refresh_datatable = false;    // decides whether to just refresh datatable or complete page
        $output_arr = array();              // stores datatable required JSON output values
        if(isset($load_datatable) && is_numeric($load_datatable) && ($load_datatable == 1)){
            $flag_refresh_datatable = true;
        }


        if(!isset($start) || empty($start) || !is_numeric($start)){
            $start = 0;
        }

        $start = intval($start);    // offset of records to be shown

        if(!isset($length) || empty($length) || !is_numeric($length)){
            $length = 10;        // default records to be shown on one page
        }

        $length = intval($length);
       
     
        $no_of_records = 0;
        $total_order = 0;
         
        $records = DB::table('mos_multiplier_data')
                    ->select('id',
                             'multiplier_type',
                             'margin_of_safety',
                             'multiplier_value'
                         );

                            if(isset($multiplier_search) && $multiplier_search != 'all'){
                            $records = $records->where('multiplier_type', $multiplier_search);
                            }

                            if(count($where_conditions) > 0){
                            $records = $records->where(function($query) use ($where_conditions) {
                               foreach($where_conditions as $_key => $_value){
                                   $query->orWhere($_value[0], $_value[1], $_value[2]);
                               }
                           });
                        }
                             
                             $no_of_records = $records->count();
                             $records = $records->skip($start)
                                                  ->take($length)
                                                  ->orderBy('id','DESC')->get();

            try{

               foreach($records as $key => $value){
               $value->action  = '';
                if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('mos_multiplier_data_edit/{role_id?}', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE)){
                 $value->action .= '<a href="'. env('APP_URL') . '/mos_multiplier_data_edit/'. $value->id .'"><i class="icons edit-icon" title="Edit setting data" alt="Edit setting data"></i></a>';
                  }
                
                 if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('mos_multiplier_data_delete/{role_id?}', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE)){
                  $value->action .= '<a href="javascript:void(0)"  onclick="return Deletemyfunction('.$value->id.')" title="delete Record"><i  title="delete Record" alt="delete Record" > Delete </i></a>';
                  }
                              
            }

               unset($key, $value);
              }catch(Exception $e){
              // if SQL query exception occurs then not showing any records
              $records = array();
              }

              return array('records' => $records, 'no_of_records' => $no_of_records); 
    }


}



