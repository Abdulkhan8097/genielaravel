<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class QuoteDataIndexHistory extends Model
{
    use HasFactory;
    protected $table = 'quote_data_index_history';
    protected $fillable = ['symbol','index_date','open','high','low','close'];
}
