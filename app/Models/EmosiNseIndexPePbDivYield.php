<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmosiNseIndexPePbDivYield extends Model
{
    use HasFactory;
    protected $table = 'emosi_nse_index_pe_pb_divyield';
    protected $fillable = ['id', 'symbol', 'record_date', 'pe', 'pb', 'div_yield', 'status', 'created_at', 'updated_at'];

    public static function get_record(){
        return self::where('status', 1);
    }
}
