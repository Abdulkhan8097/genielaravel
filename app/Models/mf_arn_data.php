<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;


class mf_arn_data extends Model
{
    protected $collection = 'mf_arn_data';
	protected $primaryKey = 'id';
	protected $connection = 'partnermongodb';
}
