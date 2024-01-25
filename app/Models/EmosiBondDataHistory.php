<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmosiBondDataHistory extends Model
{
    use HasFactory;
    protected $table = 'emosi_bond_data_history';
    protected $fillable = ['id', 'symbol', 'record_date', 'open', 'high', 'low', 'close', 'status', 'created_at', 'updated_at'];

    public static function get_record(){
        return self::where('status', 1);
    }
}
