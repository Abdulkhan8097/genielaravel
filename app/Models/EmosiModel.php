<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class EmosiModel extends Model
{
    public static function getEmosiAllValues($data){
            if(!empty($data)){

                if(isset($data['close']))
                {
                 $insert_bond = DB::table('emosi_bond_data_history')->insertGetId([
                    'close' => $data['close'],
                    'symbol'=> 'india_10y',
                    'record_date'=> $data['record_date'],
                    'status'=> $data['status'] 
                ]);
             }

             if(isset($data['pe']))
             {
                $insert_nifty_fifty = DB::table('emosi_nse_index_pe_pb_divyield')->insertGetId([
                    'pe' => $data['pe'],
                    'record_date'=> $data['record_date'],
                    'symbol'=> 'nifty_50',
                    'status'=> $data['status'] 
                ]);
            }

            if(isset($data['nifty_fifty_day'])){
                $symbol_nifty_fifty_day = '-21';

                $insert_nifty_fifty_day = DB::table('quote_data_index_history')->insertGetId([
                    'close' => $data['nifty_fifty_day'],
                    'index_date'=> $data['record_date'],
                    'symbol'=> $symbol_nifty_fifty_day
                ]);
            }


            return array('insert_bond'=>1 ,'insert_nifty_fifty'=>1, 'insert_nifty_fifty_day'=>1);
        }
    }

    public static function allSelectedValues($record_date){  

        $select_bond_value = DB::table('emosi_bond_data_history')->select('record_date','close')->where('symbol','=','india_10y')->where('status','=', 1)->where('record_date','=',$record_date)->first();

        $select_nifty_fifty = DB::table('emosi_nse_index_pe_pb_divyield')->select('record_date','pe')->where('symbol','=','nifty_50')->where('status','=', 1)->where('record_date','=',$record_date)->first();

        $select_nifty_fifty_day = DB::table('quote_data_index_history')->select('index_date','close')->where('symbol','=','-21')->where('index_date','=',$record_date)->first();

        return array('select_bond_value'=>$select_bond_value ,'select_nifty_fifty'=>$select_nifty_fifty, 'select_nifty_fifty_day'=>$select_nifty_fifty_day);
    }
}