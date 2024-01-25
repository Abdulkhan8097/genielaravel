<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AumCommissionDataBackup extends Model
{

    use HasFactory;
    protected $table = "drm_uploaded_arn_average_aum_total_commission_data_backup";
    protected $primaryKey = "id";
    protected $fillable = ['ARN','arn_avg_aum','arn_total_commission','arn_yield','arn_business_focus_type','status','aum_year','created_at','updated_at','partner_code'];
}
