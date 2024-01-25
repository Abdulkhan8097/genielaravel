<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SchemeMasterModel extends Model
{
	protected $table = 'scheme_master';
	protected $connection = 'invdb';

	public static function get_nav($input_arr = array()){
		/* Possible values for $input_arr are: array('nav_date' => date for which NAV needs to be retrieved,
		 *											 'scheme_code' => scheme code for whom NAV needs to be retrieved,
		 * 											 'plan_code' => plan code for whom NAV needs to be retrieved);
		 */
		extract($input_arr);
		$output_arr = array();
		$where_conditions = array(array('status', '=', 1));

		if(isset($nav_date) && !empty($nav_date) && strtotime($nav_date) !== FALSE){
			$where_conditions[] = array('nav_history.NAV_Date', '<=', $nav_date);
		}

		if(isset($scheme_code) && !empty($scheme_code)){
			$where_conditions[] = array('nav_history.Scheme_Code', '=', $scheme_code);
		}

		if(isset($plan_code) && !empty($plan_code)){
			$where_conditions[] = array('nav_history.Plan_Code', '=', $plan_code);
		}

		$nav_max_date_query = DB::table('samcomf_investor_db.nav_history')
									->select(array('Scheme_Code', 'Plan_Code', DB::raw('MAX(NAV_Date) AS NAV_Date')))
									->where($where_conditions)
									->groupBy(array('Scheme_Code', 'Plan_Code'));

		$records = DB::table('samcomf_investor_db.nav_history')
						->select(array('nav_history.Scheme_Code', 'nav_history.Plan_Code', 'nav_history.NAV', 'nav_history.NAV_Date'));
		if(count($where_conditions) > 0){
			$records = $records->where($where_conditions);
		}
		$records = $records->joinSub($nav_max_date_query, 'scheme_wise_max_nav', function($join){
			$join->on('scheme_wise_max_nav.Scheme_Code', '=', 'nav_history.Scheme_Code');
			$join->on('scheme_wise_max_nav.Plan_Code', '=', 'nav_history.Plan_Code');
			$join->on('scheme_wise_max_nav.NAV_Date', '=', 'nav_history.NAV_Date');
		})->get()->toArray();
		unset($where_conditions);
		if(is_array($records) && count($records) > 0){
			array_walk($records, function($_value, $_key, $_user_data){
				$_user_data[0][$_value->Scheme_Code . $_value->Plan_Code] = (array) $_value;
			}, [&$output_arr]);
		}
		return $output_arr;
	}
}
