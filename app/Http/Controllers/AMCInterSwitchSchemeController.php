<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Providers\RouteServiceProvider;

class AMCInterSwitchSchemeController extends Controller
{

    public function index(Request $request){
		$data = [];
		return view('Interamcswitch.view',$data);
	}

	public function api(Request $request){

		ini_set('max_execution_time', '3000');

		$apis = [
			'get_client_details',
			'get_scheme_details',
			'sendOtpToClient',
			'verifyOTPClient',
			'saveInterSwitchSchemes',
			'check_for_autoswitch',
			'checkMandate',
		];

		if(empty($request->is_bdm)){
			if($request->method() == 'GET'){
				$apis = [
					'fixjson',
				];
			}else{
				return response()->json([
					'status' => 'fail',
					'msg' => 'API is only for BDM use.'
				]);
			}
		}

		if(in_array($request->api_url,$apis)){
			return $this->{$request->api_url}($request);
		}else{
			return response()->json([
				'status' => 'fail',
				'msg' => 'API doesnt exist.'
			]);
		}
	}

	private function checkMandate(Request $request){

		// fetching mandate amount for Client

		if(isset($request->client_id)){
			$mandate = DB::connection('rankmf')
				->table('mf_mandate_regitrations_mfd')
				->select(['mandate_amt as amt'])
				->where('mandate_status', '=', 'APPROVED')
				->orderby('mandate_amt','desc')
				->where('client_id', '=', $request->client_id)
				->first();
		}

		if(isset($mandate->amt)){
			return response()->json([
				'status' => 'success',
				'amount' => $mandate->amt,
			]);
		}else{
			return response()->json([
				'status' => 'success',
				'amount' => 0,
			]);
		}
	}

	private function get_client_details(Request $request){

		if(empty($request->search_term)){
			return response()->json([]);
		}

		return response()->json(json_decode(get_curl_call(env('PARTNER_RANKMF_STATIC_WEB_URL').'/admin/InterAMCSwitch/'.$request->api_url ,[
			'api' => 8.4,
			'user' => 'BDM_'.$request->bdm_id,
			'access_token' => $request->token,
			'search_term' => $request->search_term,
			'bdm_id' => $request->bdm_id,
		]),1));
	}

	private function get_scheme_details(Request $request){

		if(empty($request->search_term)){
			return response()->json([]);
		}

		$responce = json_decode(get_curl_call(env('PARTNER_RANKMF_STATIC_WEB_URL').'/admin/InterAMCSwitch/'.$request->api_url ,[
			'api' => 8.3,
			'user' => 'BDM_'.$request->bdm_id,
			'access_token' => $request->token,
			'search_term' => $request->search_term,
			'show_schemes_for' => $request->show_schemes_for,
			'view_nfo_schemes' => $request->view_nfo_schemes,
			'redeeming_scheme_uniqueno' => $request->redeeming_scheme_uniqueno,
			'api_requested_from_source' => $request->api_requested_from_source
		]),1);

		return response()->json($responce);
	}

	private function sendOtpToClient(Request $request){
		return response()->json(json_decode(get_curl_call(env('PARTNER_RANKMF_STATIC_WEB_URL').'/admin/InterAMCSwitch/'.$request->api_url ,[
			'client_id' => $request->client_id,
			'autoswitch' => $request->autoswitch,
		]),1));
	}

	private function verifyOTPClient(Request $request){
		return response()->json(json_decode(get_curl_call(env('PARTNER_RANKMF_STATIC_WEB_URL').'/admin/InterAMCSwitch/'.$request->api_url ,[
			'client_id' => $request->client_id,
			'otp' => $request->otp,
		]),1));
	}

	private function saveInterSwitchSchemes(Request $request){

		//fetching from to amc unique code and amc scheme code

		$from_unique_code = getSettingsTableValue('AUTO_SWITCH_FROM_UNIQUE','rankmf');
		$to_unique_code = getSettingsTableValue('AUTO_SWITCH_TO_UNIQUE','rankmf');
		$from_scheme = getSettingsTableValue('AUTO_SWITCH_FROM_AMC_SCHEME_CODE','rankmf');
		$to_scheme[$from_scheme] = getSettingsTableValue('AUTO_SWITCH_TO_AMC_SCHEME_CODE','rankmf');
		$Purchase_Amount_Multiplier = getSettingsTableValue('Purchase_Amount_Multiplier','rankmf');

		// Use only for order request json
		$from_scheme_json_request = getSettingsTableValue('FROM_AMC_SCHEME_CODE_JSON_REQUEST','rankmf');

		// fetching mandate amount for Client
		$mandate = DB::connection('rankmf')
			->table('mf_mandate_regitrations_mfd')
			->select(['mandate_amt as amt'])
			->where('mandate_status', '=', 'APPROVED')
			->where('client_id', '=', $request->client_id)
			->first();

		// To get nav and amc scheme and multipliercode against samco scheme Unique_No
		$tmp = DB::connection('rankmf')
			->table('mf_scheme_master as sm')
			->select([
				DB::raw('IFNULL(cna.Navrs,0) AS cur_nav_accord'),
				DB::raw('sm.Unique_No AS id'),
				'AMC_Scheme_Code',
				'Purchase_Amount_Multiplier',
			])
			->join('mf_schemeisinmaster_accord AS sim', 'sim.isin', '=', 'sm.isin')
			->join('mf_currentnav_accord AS cna', 'sim.Schemecode', '=', 'cna.Schemecode')
			->whereIn('sm.Unique_No',array_merge($request->unique_no,$request->unique_no_to))
			->get()->toArray();

		$tmp = json_decode(json_encode($tmp),1);

		$navs = array_combine(array_column($tmp,'id'), array_column($tmp,'cur_nav_accord'));

		$AMC_Scheme_Code = array_combine(array_column($tmp,'id'), array_column($tmp,'AMC_Scheme_Code'));

		//$AMC_Scheme_Code[$to_unique_code] = $to_scheme[$from_scheme];
		//$AMC_Scheme_Code[$from_unique_code] = $from_scheme;

		//$Purchase_Amount_Multiplier = array_combine(array_column($tmp,'id'), array_column($tmp,'Purchase_Amount_Multiplier'));
		
		//fetching schemes selected for auto switch
		$schemes_for_auto_switch = array_keys($request->select_auto_switch, 1);

		//check if amount is greater than mandate amount for smart switch
		/*
		foreach($schemes_for_auto_switch as $key){
			if(isset($request->units[$key]) && isset($navs[$request->unique_no[$key]])){
				$amount = $request->units[$key] * $navs[$request->unique_no[$key]];
				if(isset($mandate->amt)){
					if($amount > $mandate->amt){
						return response()->json([
							'err_flag' => 1,
							'err_msg' => [
								'Your order value exceeds by '.($amount - $mandate->amt),
								'Your order value exceeds the OTM value, kindly split the amount accordingly into separate orders.'
								]
						]);
					}
				}
			}else{
				return response()->json([
					'err_flag' => 1,
					'err_msg' => ['something went wrong']
				]);
			}
		}*/

		// fetching client and partners details
		$client_details = DB::connection('rankmf')
			->table(env('RANKMF_MYSQL_DB_DATABASE').'.mf_client_master as mf')
			->join(env('PARTNERS_MYSQL_DB_DATABASE').'.mfp_partner_registration as mfp','mfp.partner_code','=','mf.broker_id')
			->select(['mf.client_pan','mf.broker_id','mfp.ARN'])
			->where('mf.client_id', '=', $request->client_id)
			->first();

		//fetching amc charges in percentage
		$amc_charges = getSettingsTableValue('AMC_CHARGES');

		$tmp = get_curl_call(env('PARTNER_RANKMF_STATIC_WEB_URL').'/admin/InterAMCSwitch/'.$request->api_url ,[
			'client_id' => $request->client_id,
			'for_user' => 'BDM_'.$request->bdm_id,
			'for_user_token' => $request->token,
			'unique_no' => $request->unique_no,
			'folio_num' => $request->folio_num,
			'units' => $request->units,
			'unique_no_to' => $request->unique_no_to,
			'select_all_units' => $request->select_all_units,
			'select_auto_switch' => $request->select_auto_switch,
			'bdm_id' => $request->bdm_id,
		]);

		$response = json_decode($tmp,1);

		foreach($schemes_for_auto_switch as $key){

			//print_r($this->getAMCSchemeCode($request,$AMC_Scheme_Code[$request->unique_no_to[$key]]));

			$folio_num_to = null;

			if(isset($request->folio_num_to[$key])){
				$folio_num_to = $request->folio_num_to[$key];
			}

			$amount = $navs[$request->unique_no[$key]]*$request->units[$key];

			$amount = $this->getFinalAmount($amount, $Purchase_Amount_Multiplier);

			$amount = $amount['purchase_amount'];

			$arr = [
				"pan" => $client_details->client_pan,
				"scheme_id" => $AMC_Scheme_Code[$request->unique_no_to[$key]],
				// round : Switch amount should be integer
				"switch_amount" => round($amount*(1-($amc_charges/100))),
				"order_type" => "switch",
				"folio_number" => $folio_num_to,
				"request_source" => "RANKMF",
				"scheme_id_to" => $to_scheme[$AMC_Scheme_Code[$request->unique_no_to[$key]]],
				"quantity" => "",
				"all_redeem" => $request->select_all_units[$key],
				"is_auto_switch" => $request->select_auto_switch[$key],
				"broker_id" => '120121',//$client_details->ARN,
				"broker_euin" => "E525597",
				"sub_broker_arn" => $client_details->ARN, //Partners ARN
				"sub_broker_internal_code" => $client_details->broker_id,
				"ria_code" => "",
				"euin_declaration_terms" => "0",
				"switch_type" => "amount", 
				"switch_units" => $request->units[$key],
				"unique_no" => $from_unique_code,
				"unique_no_to" => $to_unique_code,
			];

			print_r(''); // for some reoson without this curl call give internal server error

			if(!empty($response['cas_uploaded_id'])){
				$auto_switch_cas_uploaded_id = $response['cas_uploaded_id'];
			}else{
				return response()->json([
					'err_flag' => 1,
					'err_msg' => 'cas_uploaded_id not found.',
				]);
			}
			
			$tmp = [];

			//If No folio found 
			$start_date = getSettingsTableValue('AUTO_SWITCH_DAFF_STARTDATE','rankmf');
			$end_date = getSettingsTableValue('AUTO_SWITCH_DAFF_ENDDATE','rankmf');

			$insert_data = [
				'cas_uploaded_id' => $auto_switch_cas_uploaded_id,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
				'pan' => $arr['pan'],
				'scheme_id' => $arr['scheme_id'],
				'partner_id' => $arr['sub_broker_internal_code'],
				'scheme_id_to' => $arr['scheme_id_to'],
				'folio_number' => $arr['folio_number'],
				'broker_id' => $arr['broker_id'],
				'client_id' => $request->client_id,
				'switch_amount' => $arr['switch_amount'],
				'unique_no' => $arr['unique_no'],
				'unique_no_to' => $arr['unique_no_to'],
			];

			$arr['scheme_id'] = $from_scheme_json_request;
			$insert_data['json_request'] = json_encode($arr);

			if(
				empty($request->folio_num_to[$key]) ||
				!isset($mandate->amt) ||
				!(strtotime($start_date) < time() && 
				strtotime($end_date) > time())
			){
				$provisional_auto_switch = true;
			}
			//If folio found 
			else{
				$insert_data['process_at'] = date('Y-m-d H:i:s');
				unset($arr['unique_no'],$arr['unique_no_to']);
				$tmp = json_decode(get_curl_call($request->amc_api_url.'/autoswitch-order-details-save', $arr, $request->amc_header,true),1);
			}

			if(count($tmp) > 0){
				$insert_data['json_response'] = json_encode($tmp);
			}

			if(isset($tmp['order_status'])){
				if($tmp['order_status'] == 'success'){
					$insert_data['status'] = 1;
				}else{
					$insert_data['status'] = 2;
				}
			}

			DB::connection('rankmf')->table('mf_inter_auto_switch_scheme')->insert($insert_data);

			unset($arr['unique_no'],$arr['unique_no_to']);
			
			DB::connection('mongodb')->collection('mf_drm_autoswitch_order_log')->insert([
				'date' => new \MongoDB\BSON\UTCDateTime(time()*1000),//date('Y-m-d H:i:s'),
				'bdm' => $request->bdm_id,
				'client_id' => $request->client_id,
				'broker_id' => $client_details->broker_id,
				'Request' => json_encode($arr),
				'Response' => json_encode($tmp),
				'cas_uploaded_id' => $auto_switch_cas_uploaded_id,
			]);

			if(isset($provisional_auto_switch)){
				$response['auto_switch_response'][] = true;
			}else{
				$response['auto_switch_response'][] = $tmp;
			}
		}
		// Post InterAMCSwitch API to partner RankMF
		return response()->json($response);
	}

	private function getAMCSchemeCode($request,$AMC_Scheme_Code){
		
		$scheme = DB::connection('rankmf')->table('mf_scheme_master')
			->where('Unique_No','=',$request->scheme_id)
			->first();

		$post = [];
		$post['searched_columns'] = json_encode([
			'scheme_code' => $AMC_Scheme_Code,
		]);
		$post['selected_fields'] = '*';

		$response = json_decode(get_curl_call($request->amc_api_url.'/filter-sipschemes', $post, $request->amc_header,true),1);

		return $response;
	}

	private function check_for_autoswitch(Request $request){
		
		$now = time();

		$data = [];

		$data['autoswitch'] = 'false';
		$autoswitch = false;

		if(isset($request->id)){
			
			$id = trim($request->id);
			$unique_id = getSettingsTableValue('AUTO_SWITCH_FROM_UNIQUE','rankmf');
			$start_date = getSettingsTableValue('AUTO_SWITCH_DAFF_STARTDATE','rankmf');
			$end_date = getSettingsTableValue('AUTO_SWITCH_DAFF_ENDDATE','rankmf');
			$pre_closing_days = getSettingsTableValue('AUTO_SWITCH_PRE_CLOSING_DAYS','rankmf');

			//to check if auto switch is available for scheme
			if(($unique_id == $id)){
				if($now < strtotime($end_date)){
					$data['autoswitch'] = 'true';
					$autoswitch = true;
				}
			}

		}

		// fetching client and partners details
		$client_details = DB::connection('rankmf')
			->table(env('RANKMF_MYSQL_DB_DATABASE').'.mf_client_master as mf')
			->join(env('PARTNERS_MYSQL_DB_DATABASE').'.mfp_partner_registration as mfp','mfp.partner_code','=','mf.broker_id')
			->select(['mf.client_pan','mf.broker_id','mfp.ARN'])
			->where('mf.client_id', '=', $request->client_id)
			->first();

		//to get client folio
		if(isset($client_details->client_pan) && $autoswitch){
			$post = [
				'pan' => $client_details->client_pan,
				'broker_id' => 120121,//$client_details->ARN,
				'request_source' => 'RANKMF',
			];
			$response = json_decode(get_curl_call(env('APP_URL').'/api/pan_validation', $post, $request->amc_header,true));
		}

		//print_r($response);
		$data['investor_folios'] = [];
		if(isset($response->investor_folios) && $autoswitch){
			$data['investor_folios'] = $response->investor_folios;
		}

		return response()->json($data);
	}

	private function fixjson(Request $request){

		$unique_no = getSettingsTableValue('AUTO_SWITCH_FROM_UNIQUE','rankmf');
		$unique_no_to = getSettingsTableValue('AUTO_SWITCH_TO_UNIQUE','rankmf');
		$scheme_id = getSettingsTableValue('AUTO_SWITCH_FROM_AMC_SCHEME_CODE','rankmf');
		$scheme_id_to = getSettingsTableValue('AUTO_SWITCH_TO_AMC_SCHEME_CODE','rankmf');
		
		$from_scheme_json_request = getSettingsTableValue('FROM_AMC_SCHEME_CODE_JSON_REQUEST','rankmf');
		
		$prov_data = DB::connection('rankmf')
			->table('mf_inter_auto_switch_scheme');

		// Update all unique_no, unique_no_to, scheme_id and scheme_id_to
		$prov_data->update([
				'unique_no' => $unique_no,
				'unique_no_to' => $unique_no_to,
				'scheme_id' => $scheme_id,
				'scheme_id_to' => $scheme_id_to,
				'json_request' => DB::raw("JSON_SET(json_request,
					'$.unique_no', '$unique_no',
					'$.unique_no_to', '$unique_no_to',
					'$.scheme_id', '$from_scheme_json_request',
					'$.scheme_id_to', '$scheme_id_to'
				)"),
				'partner_id' => DB::raw("JSON_UNQUOTE(JSON_EXTRACT(json_request,'$.sub_broker_internal_code'))")
		]);
		
		/*
		$mongo_prov_datas = DB::connection('mongodb')->collection('mf_drm_autoswitch_order_log');

		$selects = $mongo_prov_datas->get()->toArray();

		foreach($selects as $select){

			$tmp = clone $mongo_prov_datas;

			$json = json_decode($select['Request'],1);

			$json['scheme_id'] = $from_scheme_json_request;
			$json['scheme_id_to'] = $scheme_id_to;

			$tmp->where('_id','=',new \MongoDB\BSON\ObjectId($select['_id']))
			->update([
				'Request' => json_encode($json)
			]);
		}
		*/

		echo "Complete";
	}
	
	private function getFinalAmount($amt, $multiplier = 1) {
        $purchase_amount = explode('.',$amt)[0];
        $residual_amount = ($purchase_amount%$multiplier);
        $finalamount['residual_amount'] = round(($amt - $purchase_amount + $residual_amount), 2);
        $finalamount['purchase_amount'] = $purchase_amount - $residual_amount;
        return $finalamount;
    }

}
