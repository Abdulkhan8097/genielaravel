<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class pan_validationController extends Controller
{
    public function index(Request $request){

		$output = [];

		$output['err_flag'] = 0;

		$output['err_msg'] = '';

		$investor_folios = DB::connection('rankmf')
			->table('mf_aum_data')
			->select('Folio_Number')
			->where('pan','=',$request->pan)
			->where('date','=',function($query) use ($request)
			{
				$query->select(DB::raw('MAX(date)'))
					->from('mf_aum_data');
					//->where('pan','=',$request->pan)
					//->where('SchemeName','like',"%Samco Overnight%");
			})
			->where('SchemeName','like',"%Samco Overnight%")
			//->toSql2(); echo $investor_folios; die;
			->get()->toArray();
			
			//->where('date','=',DB::raw('(SELECT MAX(date) FROM mf_aum_data)'))
			

		$investor_folios = json_decode(json_encode($investor_folios),1);

		$investor_folios = array_column($investor_folios,'Folio_Number');

		$output['investor_folios'] = $investor_folios;

		if(count($investor_folios) > 0){
			$output['pan_data']['pan_aadhaar_msg'] = "Existing and Valid. PAN is Inoperative.";
		}else{
			$output['err_flag'] = 1;
			$output['err_msg'] = 'You have entered an invalid PAN. Kindly enter a valid PAN.';
		}

		return $output;
	}
}
