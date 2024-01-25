<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\CsvHelper;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Exports\ArrayRecordsExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use DataTables;

class InterAMCSwitchMISController extends Controller
{
	protected $uploadedCasOrderStatusArr,$misOrderTypesArr;
	
    public function __construct(){

		$this->middleware('auth');
	

        $this->uploadedCasOrderStatusArr = [
			'cas_verification_pending' => 'CAS Verification Pending',
			'cas_verification_rejected' => 'CAS Verification Rejected',
			'cas_verified_redemption_orders_to_be_placed' => 'CAS Verified, Redemption Orders to be placed',
			'redemption_orders_rejected_at_initial_bse_stage' => 'Redemption Orders rejected at initial BSE stage',
			'redemption_orders_placed_pending_status_from_bse' => 'Redemption Orders placed, pending status from BSE',
			'all_redemption_orders_rejected_by_rta' => 'All Redemption Orders rejected by RTA',
			'redemption_order_leg_cancelled_by_client' => 'Redemption Order Leg Cancelled by Client',
			'bse_redemption_order_status_received_waiting_for_credit' => 'BSE Redemption Status received, waiting for credit',
			'credit_received_and_purchase_orders_pending' => 'Credit received and purchase orders pending',
			'credit_received_and_purchase_orders_placed' => 'Credit received and purchase orders placed',
			'units_allotted_for_purchase_orders' => 'Units allotted for purchase orders'
        ];

        $this->misOrderTypesArr = [
			'number_of_switch_out_orders' => 'Number of Switch out Orders',
			'number_of_redemption_on_orders_placed' => 'Number of Redemption on Orders Placed',
			'number_of_redemption_on_orders_rejected_by_bse' => 'Number of Redemption on Orders rejected by BSE',
			'number_of_redemption_on_orders_accepted_by_bse' => 'Number of Redemption on Orders accepted by BSE',
			'number_of_redemption_on_orders_accepted_by_rta' => 'Number of Redemption on Orders accepted by RTA',
			'number_of_redemption_on_orders_rejected_by_rta' => 'Number of Redemption on Orders rejected by RTA',
			'number_of_redemption_on_orders_pending_status_by_rta' => 'Number of Redemption on Orders pending status by RTA',
			'number_of_purchase_orders_to_be_placed' => 'Number of purchase orders to be placed',
			'number_of_purchase_orders_placed' => 'Number of purchase orders placed',
			'number_of_purchase_orders_for_which_units_allowed' => 'Number of purchase orders for which units allowed',
			'initiated_smart_switch' => 'Initiated Smart Switch',
			'uploaded_cas_successfully' => 'Uploaded CAS Successfully',
			'checked_improvements' => 'Checked Improvements',
			'review_and_otp_pending' => 'Review and OTP Pending',
			'confirm_switch' => 'Switch Confirmed',
			'total_number_of_schemes_detected_in_cas' => 'Total Number of Schemes Detected in CAS [A]',
			'total_number_of_schemes_suggested_for_smartswitch' => 'Total Number of Schemes Suggested for SmartSwitch [B]',
			'actual_number_of_schemes_selected_by_client' => 'Actual Number of Schemes Selected by Client [C]',
			'total_value_of_schemes_suggested_for_smartswitch' => 'Total Value of Schemes Suggested for SmartSwitch [D]',
			'total_value_of_schemes_selected_by_client_for_smartswitch' => 'Total Value of Schemes Selected by Client for SmartSwitch [E]',
			'percentage_share_of_schemes_proceeded_by_count_c_by_b' => '% Share of schemes proceeded by Count (C/B)',
			'percentage_share_of_schemes_proceeded_by_count_e_by_d' => '% Share of schemes proceeded by Value (E/D)'
        ];
		
		$filepath = base_path('public/mis_data');
		if(!is_dir($filepath)) {
			mkdir($filepath,0777,TRUE);
		}
    }

	public function index(Request $request){
		$bdm_data = DB::connection('partner-rankmf')
			->table('mfp_partner_login_master')
			->select('id')
			->where('email','=',\Auth::user()->email)//->toSql2();
			->first();
			$bdm_id='';
			if(!empty($bdm_data)){
				$bdm_id=$bdm_data->id;
			}
		
		$postedData = [];
        if ($request->isMethod('post') && count($request->all()) > 0) {
            $postedData = $request->all();
            
        }
        extract($postedData);

        $flagRefreshDatatable = false;
        $outputArr = [];

        if ( is_numeric($request->input('load_datatable')) && $request->input('load_datatable') == 1) {
            $flagRefreshDatatable = true;
        }
        $flagExportData = false;
        // if ($request->filled('export_data') && $request->input('export_data') == 1) {
        //     $flagExportData = true;
        // }
		if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
			$flagExportData = true;
		}

        if (!$flagRefreshDatatable) {
            $data = [
                'uploaded_cas_order_status_arr' => $this->uploadedCasOrderStatusArr,
                'mis_order_types_arr' => $this->misOrderTypesArr,
                'records_created_by_bdm' => $bdm_id,
            ];
			return view('inter_amc_switch_mis_v')->with($data);
        } 
		else {
			if (!$flagExportData) {
				$postedData['columns'] = json_encode($postedData['columns']);
			}
			
            // $retrievedData = $this->get_content_by_curl(env('RANKMF_URL') . '/mfbo/MutualfundSmartSwitch/cas_details_verification', $postedData);
            $retrievedData = get_curl_call(env('RANKMF_URL') . '/mfbo/MutualfundSmartSwitch/cas_details_verification', $postedData);
			// x($retrievedData);

            if ($retrievedData && json_decode($retrievedData) !== false) {
                $outputArr = json_decode($retrievedData, true);
            }

            if (!$flagExportData) {
                return response()->json($outputArr);
            } 
			else {
                $outputArr = array_map(function ($_value) {
                    $_value = (array)$_value;
					unset($_value['total_number_of_schemes_detected_in_cas'], $_value['total_number_of_schemes_suggested_for_smartswitch'], $_value['actual_number_of_schemes_selected_by_client'], $_value['total_value_of_schemes_suggested_for_smartswitch'], $_value['total_value_of_schemes_selected_by_client_for_smartswitch'], $_value['percentage_share_of_schemes_proceeded_by_count_c_by_b'], $_value['percentage_share_of_schemes_proceeded_by_count_e_by_d']);
					if($found_key = array_search('Total Number of Schemes Detected in CAS [A]', $_value)){
					  unset($_value[$found_key]);
					}
					if($found_key = array_search('Total Number of Schemes Suggested for SmartSwitch [B]', $_value)){
					  unset($_value[$found_key]);
					}
					if($found_key = array_search('Actual Number of Schemes Selected by Client [C]', $_value)){
					  unset($_value[$found_key]);
					}
					if($found_key = array_search('Total Value of Schemes Suggested for SmartSwitch [D]', $_value)){
					  unset($_value[$found_key]);
					}
					if($found_key = array_search('Total Value of Schemes Selected by Client for SmartSwitch [E]', $_value)){
					  unset($_value[$found_key]);
					}
					if($found_key = array_search('% Share of schemes proceeded by Count (C/B)', $_value)){
					  unset($_value[$found_key]);
					}
					if($found_key = array_search('% Share of schemes proceeded by Value (E/D)', $_value)){
					  unset($_value[$found_key]);
					}
                    return $_value;
                }, $outputArr);

				if (count($outputArr) > 0) {
					$csvFileName = 'interamcswitch_mis_records_' . date('YmdHis') . '.csv';
					$csvFilePath = base_path('public/mis_data/' . $csvFileName);
					$download_Path =  'mis_data'.'/'.$csvFileName;
					$fp = fopen($csvFilePath, 'w');
			
					foreach ($outputArr as $row) {
						fputcsv($fp, $row);
					}
					fclose($fp);

					// Download the CSV file
					return response()->json([
						'file' => url($download_Path),
						'base_path' => $csvFilePath,
						'file_name' => $csvFileName,
					]);
				} 
				else {
					return response()->json(['message' => 'No Record Found'], 404);
				}
		
				exit; // Note: This exit statement may not be necessary

				// return \Excel::download(new ArrayRecordsExport($output_arr),'interamcswitch_mis_records_'. date('Ymd').'.xlsx');
            }
        }
	}

	public function getMisData(Request $request){
	
		$postedData = [];
		$flagReturnData = false;
		if ($request->post() != null && is_array($request->post()) && count($request->post()) > 0){
			$postedData = $request->post();
		}
	
		extract($postedData);

		$errFlag = 0;
		$errMsg = [];
		$outputArr = [];

		$flagReturnCount = false;

		if (isset($returnCount) && ($returnCount == 1)) {
			$flagReturnCount = true;
		}

		$flagExportData = false;

		if (isset($postedData['export_data']) && !empty($postedData['export_data']) && ($postedData['export_data'] == 1)) {
			$flagExportData = true;
			$outputArr['exportingRows'] = [];
		}

		// Retrieving MIS records related to interswitch orders.
		// $response = Http::post(env('RANKMF_URL').'mf/bo/MutualfundSmartSwitch/get_mis_data', $postedData);
		
        $response = get_curl_call(env('RANKMF_URL') . '/mfbo/MutualfundSmartSwitch/get_mis_data', $postedData);

		if ($response) {
			$outputArr = $response;
		}
	

		if ($flagExportData) {
			$outputArr = json_decode($outputArr);
			// Ensure $outputArr is an array
			if (!is_array($outputArr)) {
				// Handle the case where $outputArr is not an array
				return response()->json(['message' => 'Invalid Data Type'], 400);
			}
		
			$outputArr = array_map(function ($_value) {
				$_value = (array) $_value;
				return $_value;
			}, $outputArr);
		
			if (count($outputArr) > 0) {
				$csvFileName = 'number_of_switch_out_orders_' . date('YmdHis') . '.csv';
				$csvFilePath = base_path('public/mis_data/' . $csvFileName);
				$download_Path =  'mis_data'.'/'.$csvFileName;
				$fp = fopen($csvFilePath, 'w');
		
				foreach ($outputArr as $row) {
					fputcsv($fp, $row);
				}
				fclose($fp);

				// Download the CSV file
				return response()->json([
					'file' => url($download_Path),
					'base_path' => $csvFilePath,
					'file_name' => $csvFileName,
				]);
			} 
			else {
				return response()->json(['message' => 'No Record Found'], 404);
			}
		
			exit; // Note: This exit statement may not be necessary
		}
		

		if ($flagReturnData) {
			return $outputArr;
		} 
		else {
			echo $outputArr;
		}
	}

	public function ajax_unlink_file(Request $request){

		//,'png','jpeg','jpg','gif','doc','docx','pdf','rtf','xls','xlsx','ppt','pptx','txt'
		$allowed_files = [
			'csv'
		];

		if(!isset($request->path)){
			return response()->json(['message' => 'Invalid parameters'], 200);
		}else{

			$fileNameParts = explode('.', $request->path);
			$ext = end($fileNameParts);

			// this is to prevent php or any other important from deleting
			if(!in_array($ext,$allowed_files)){
				return response()->json(['message' => 'File deletion is not allowed, Unauthorized attempt.'], 200);
			}

			if(!file_exists($request->path)){
				return response()->json(['message' => 'File doesn\'t exist'], 200);
			}
		}

		@unlink($request->path);

        if(file_exists($request->path)){
			return response()->json(['message' => 'Unable to delete file'], 200);
        }else {
			return response()->json(['message' => 'Deleted file successfully'], 200);
		}
		
	}

	public function autoswitch(Request $request){

		$bdm_data = DB::connection('partner-rankmf')
			->table('mfp_partner_login_master')
			->select('id')
			->where('email','=',\Auth::user()->email)//->toSql2();
			->first();

		$data['bdm_id']=$bdm_data->id;

		$postedData = [];

		if ($request->isMethod('post') && count($request->all()) > 0) {
            $postedData = $request->all();
   
        }

		$flagRefreshDatatable = false;
		
		if ( is_numeric($request->input('load_datatable')) && $request->input('load_datatable') == 1) {
            $flagRefreshDatatable = true;
        }
        
		if(!$flagRefreshDatatable){
			return view('auto_switclist')->with($data);
		}

		$data =  DB::connection('rankmf')->table('mf_inter_auto_switch_scheme as ass')
			->select([
				'ass.client_id',
				'cm.client_name',
				'cm.client_email',
				'cm.client_mobile',
				'cm.client_pan',
				'ass.folio_number',
				'ass.switch_amount',
				'ass.created_at',
				DB::raw('(select Scheme_Name from mf_scheme_master where Unique_No = ass.unique_no limit 1) as from_scheme'),
				DB::raw('(select Scheme_Name from mf_scheme_master where Unique_No = ass.unique_no_to limit 1) as to_scheme'),
				DB::raw("CASE
					WHEN (ass.status = 0) THEN 'Pending'
					WHEN (ass.status = 1) THEN 'Success'
					WHEN (ass.status = 2) THEN 'Failed'
					ELSE ass.status
					END as status"),
			])
			->join('mf_client_master as cm','ass.client_id','=','cm.client_id')
			->join(env('DB_DATABASE').'.drm_distributor_master as dm','dm.rankmf_partner_code','=','ass.partner_id')
			->where('dm.direct_relationship_user_id','=',\Auth::user()->id);

			// ->get()->toArray();
		if ($request->post()) {
			return Datatables::of($data)->addIndexColumn()->make(true);
		}
		
	}

	public function provisional_interswitch_orders(Request $request){

		$bdm_data = DB::connection('partner-rankmf')
			->table('mfp_partner_login_master')
			->select('id')
			->where('email','=',\Auth::user()->email)//->toSql2();
			->first();

		$bdm_id='';

		if(!empty($bdm_data)){
			$bdm_id=$bdm_data->id;
		}
		
		$postedData = [];

        if ($request->isMethod('post') && count($request->all()) > 0) {
            $postedData = $request->all();
        }

        extract($postedData);

        $flagRefreshDatatable = false;
        $outputArr = [];

        if ( is_numeric($request->input('load_datatable')) && $request->input('load_datatable') == 1) {
            $flagRefreshDatatable = true;
        }

        $flagExportData = false;
        // if ($request->filled('export_data') && $request->input('export_data') == 1) {
        //     $flagExportData = true;
        // }
		if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
			$flagExportData = true;
		}

        if (!$flagRefreshDatatable) {
            $data = [
                'uploaded_cas_order_status_arr' => $this->uploadedCasOrderStatusArr,
                'mis_order_types_arr' => [],
                'records_created_by_bdm' => $bdm_id,
            ];
			return view('provisional_interswitch_orders_mis')->with($data);
        } 
		else {
			if (!$flagExportData) {
				$postedData['columns'] = json_encode($postedData['columns']);
			}

			// $retrievedData = $this->get_content_by_curl(env('RANKMF_URL') . '/mfbo/MutualfundSmartSwitch/cas_details_verification', $postedData);
            $retrievedData = get_curl_call(env('RANKMF_URL') . '/mfbo/MutualfundSmartSwitch/view_otm_mandate_unavailable_records', $postedData);
			
            if ($retrievedData && json_decode($retrievedData) !== false) {
                $outputArr = json_decode($retrievedData, true);
            }

            if (!$flagExportData) {
                return response()->json($outputArr);
            } 
			else {
                /*$outputArr = array_map(function ($_value) {
                    $_value = (array)$_value;
					unset($_value['total_number_of_schemes_detected_in_cas'], $_value['total_number_of_schemes_suggested_for_smartswitch'], $_value['actual_number_of_schemes_selected_by_client'], $_value['total_value_of_schemes_suggested_for_smartswitch'], $_value['total_value_of_schemes_selected_by_client_for_smartswitch'], $_value['percentage_share_of_schemes_proceeded_by_count_c_by_b'], $_value['percentage_share_of_schemes_proceeded_by_count_e_by_d']);
					if($found_key = array_search('Total Number of Schemes Detected in CAS [A]', $_value)){
					  unset($_value[$found_key]);
					}
					if($found_key = array_search('Total Number of Schemes Suggested for SmartSwitch [B]', $_value)){
					  unset($_value[$found_key]);
					}
					if($found_key = array_search('Actual Number of Schemes Selected by Client [C]', $_value)){
					  unset($_value[$found_key]);
					}
					if($found_key = array_search('Total Value of Schemes Suggested for SmartSwitch [D]', $_value)){
					  unset($_value[$found_key]);
					}
					if($found_key = array_search('Total Value of Schemes Selected by Client for SmartSwitch [E]', $_value)){
					  unset($_value[$found_key]);
					}
					if($found_key = array_search('% Share of schemes proceeded by Count (C/B)', $_value)){
					  unset($_value[$found_key]);
					}
					if($found_key = array_search('% Share of schemes proceeded by Value (E/D)', $_value)){
					  unset($_value[$found_key]);
					}
                    return $_value;
                }, $outputArr);*/

				if (count($outputArr) > 0) {
					$csvFileName = 'otm_mandate_unavailable_records_' . date('YmdHis') . '.csv';
					$csvFilePath = base_path('public/mis_data/' . $csvFileName);
					$download_Path =  'mis_data'.'/'.$csvFileName;
					$fp = fopen($csvFilePath, 'w');
			
					foreach ($outputArr as $row) {
						fputcsv($fp, $row);
					}
					fclose($fp);

					// Download the CSV file
					return response()->json([
						'file' => url($download_Path),
						'base_path' => $csvFilePath,
						'file_name' => $csvFileName,
					]);
				} 
				else {
					return response()->json(['message' => 'No Record Found'], 404);
				}
            }
        }
	}
}