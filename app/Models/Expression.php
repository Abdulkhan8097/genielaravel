<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Expression extends Model
{
    use HasFactory;
    public static function currentDate(){
        $date = Carbon::now();
        return $date->format("Y-m-d");
    }

    public static function yesterdayDate($number_of_days = -1){
        $date = date('Y-m-d', strtotime($number_of_days .' days'));
        return $date;
    }

    public static function longDate($date){
         //$newDate =date('Y-m-d', $date);
         $newDate =$date->format('Y-m-d');
        return $newDate;
    }
}
