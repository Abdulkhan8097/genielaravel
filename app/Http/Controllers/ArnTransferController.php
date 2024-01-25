<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ArnTransferController extends Controller
{
	public function index(Request $request){

		$data = [];

		//Fetching BDM list
		$data['bdmlist'] =  DB::table('users')
			->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
			->select('users.id','users.name')
			->where('is_drm_user', '=',1)
			->where('users_details.skip_in_arn_mapping', '=',0)
			->where('users_details.is_deleted', '=',0)
			->where('users_details.is_old', '=',0)
			->where('users.status', '=',1)
			->orderby('users.name')
			->get();

		return view('arntransfer.arntransfer',$data);
	}

	public function getARN(Request $request){
		
		//Fetching serviceable pincode of selected BDM
		$users_details = DB::table('users_details')->select(['serviceable_pincode']);

		if(!empty($request->from_direct_relationship_user_id)){
			$users_details->where('user_id','=',$request->from_direct_relationship_user_id);
		}else{
			return response()->json([
				'arns' => []
			]);
		}

		$data['pincodes'] = $users_details->first();

		//Fetching ALL ARNs of Selected BDM for ARN Select Options
		$arns = DB::table('drm_distributor_master')->orderBy('ARN');

		$arns->select(['ARN']);

		if(!empty($request->from_direct_relationship_user_id)){
			$arns->where('direct_relationship_user_id','=',$request->from_direct_relationship_user_id);
		}

		$data['arns'] = $arns->get()->toArray();

		if(!empty($request->serviceable_pincode)){
			$request->serviceable_pincode = preg_replace('/\s+/', '', $request->serviceable_pincode);
		}

		//If serviceable pincode is empty use asigned pincode 
		if(empty($request->serviceable_pincode) && $request->use_serviceable_pincode == 'true'){
			$request->serviceable_pincode = $data['pincodes']->serviceable_pincode;
		}

		//Fetching Selected ARNs
		if(!empty($request->serviceable_pincode)){
			$arns->whereIn("arn_pincode",explode(',',$request->serviceable_pincode));
		}

		if(!empty(intval($request->is_rankmf_partner))){
			$arns->where('is_rankmf_partner','=',(intval($request->is_rankmf_partner) - 1));
		}

		if(!empty($request->arns)){
			$arns->whereIn("ARN",explode(',',$request->arns));
		}

		//Get Selected ARN count
		$data['alert'][] = $arns->count().' ARN Selected';

		//Checking how many arns are belongs to pincode that are asigned to Selected BDM
		$pinarns = DB::table('drm_distributor_master');

		$pinarns->select(['ARN']);

		if(!empty($request->from_direct_relationship_user_id)){
			$pinarns->where('direct_relationship_user_id','=',$request->from_direct_relationship_user_id);
		}

		$total_pin_arn = $pinarns->count();

		$RegexQuery_ = str_replace(',','|',$data['pincodes']->serviceable_pincode);

		if($total_pin_arn && !empty(trim($RegexQuery_))){
			$pin_arn = $pinarns->whereRaw("arn_pincode REGEXP '$RegexQuery_'")->count();
			$data['alert'][] = $pin_arn.' ARNs are belongs to asigned Pincodes out of '.$total_pin_arn.'.';
		}else{
			if($total_pin_arn){
				$data['alert'][] = 'No ARN belongs to asigned Pincodes out of '.$total_pin_arn.'.';
			}
		}

		//Checking for invalid pincodes
		preg_match_all('/[1-9][0-9]{5}/is', $request->serviceable_pincode, $validpin);

		preg_match_all('/[0-9]*/is', $request->serviceable_pincode, $invalidpin);

		$invalidpins = array_diff(array_unique($invalidpin[0]),array_unique($validpin[0]));

		if(count(array_filter($invalidpins)) != 0){
			$data['alert'][] = 'Invalid Pin '.implode(', ',array_unique(array_filter($invalidpins))).'.';
		}

		//Checking Dublicate Pincodes
		if(count(array_unique($validpin[0])) != count($validpin[0])){
			$data['alert'][] = 'Pincode list contains dublicate pins.';
		}

		//Checking if same pincode is shared with other BDMs and Selected BDM
		$RegexQuery = str_replace(',','|',$request->serviceable_pincode);

		$matched_pins = [];

		if(!empty(trim($RegexQuery))){
			$matched_pins = DB::table('users')
				->select(['users.name','users.email','users_details.serviceable_pincode'])
				->join('users_details', 'users_details.user_id', '=','users.id')
				->whereRaw("users_details.serviceable_pincode REGEXP '$RegexQuery'")
				->where('users_details.is_deleted', '=',0)
				->where('users_details.is_old', '=',0)
				->where('users.id','!=',$request->from_direct_relationship_user_id)
				->get()->toArray();
		}

		$pins = explode(',',$request->serviceable_pincode);

		foreach($matched_pins as $user){
			$tmp = array_filter(array_unique(array_intersect($pins,explode(',',$user->serviceable_pincode))));
			if(!empty($tmp)){
				$data['alert'][] = "<b>{$user->name}</b> (<b>{$user->email}</b>) has matching pincodes ".implode(', ',$tmp).'.';
			}
		}
		
		// To check if BDM exist in Partner RankMF
		$from_mfp = DB::table(env('DB_DATABASE').'.users')
			->join(env('PARTNERS_MYSQL_DB_DATABASE').'.mfp_partner_login_master', 'mfp_partner_login_master.email', '=', 'users.email')
			->select('mfp_partner_login_master.id')
			->where('users.id','=',$request->from_direct_relationship_user_id)
			->first();

		//if bdm exist in Partner RankMF
		if(isset($from_mfp->id)){

			//Checking if Asigned ARN in DRM is empanelled to other BDMs in RankMF partner insted of selected BDM
			$mfp_arns =	DB::table(DB::raw('drm_distributor_master as d'))
				->select([DB::raw('count(b.id) as count'),'b.name', 'b.email','p.unit_counsellor'])
				->leftjoin(DB::raw(env('PARTNERS_MYSQL_DB_DATABASE').'.mfp_partner_registration as p'), 'p.ARN', '=', 'd.ARN')
				->leftjoin(DB::raw(env('PARTNERS_MYSQL_DB_DATABASE').'.mfp_partner_login_master as b'), 'p.unit_counsellor', '=', 'b.id')
				->where('d.direct_relationship_user_id','=',$request->from_direct_relationship_user_id)
				->groupby('b.email');

				if(!empty($request->arns)){
					$mfp_arns->whereIn("d.ARN",explode(',',$request->arns));
				}else{
					if(!empty(intval($request->is_rankmf_partner))){
						$mfp_arns->where('is_rankmf_partner','=',(intval($request->is_rankmf_partner) - 1));
					}
					if(!empty($request->serviceable_pincode)){
						$mfp_arns->whereIn("arn_pincode",explode(',',$request->serviceable_pincode));
					}
				}

				$mfp_arns = $mfp_arns->get()->toArray();

			foreach($mfp_arns as $mfp_arn){
				if(intval($mfp_arn->count) > 0){
					$data['alert'][] = "<b>{$mfp_arn->count}</b> ARNs are empanelled to BDM <b>{$mfp_arn->name}</b> ( <b>{$mfp_arn->email}</b> ) in <b>Partner RankMF</b>";
				}
			}
			
		}
		//if bdm do not exist in Partner RankMF
		else{
			$data['alert'][] = "Selected user dose not exist in <b>Partner RankMF</b>.";
		}

		return response()->json($data);
	}

	public function TransferARN(Request $request){
		
		//Checking if Remark is empty
		if(empty($request->remark)){
			return response()->json([
				'message' => 'Remark is required, Please fill remark.',
				'status' => 'fail'
			]);
		}
		
		//Checking if Target BDM is not selected
		if(empty($request->from_direct_relationship_user_id)){
			return response()->json([
				'message' => 'User not selected',
				'status' => 'fail'
			]);
		}
		
		//Checking if Target BDM is not selected
		if(empty($request->to_direct_relationship_user_id)){
			return response()->json([
				'message' => 'Target user not selected',
				'status' => 'fail'
			]);
		}

		$from_mfp = DB::table(env('DB_DATABASE').'.users')
			->join(env('PARTNERS_MYSQL_DB_DATABASE').'.mfp_partner_login_master', 'mfp_partner_login_master.email', '=', 'users.email')
			->select('mfp_partner_login_master.id')
			->where('users.id','=',$request->from_direct_relationship_user_id)
			->first();
		
		if(empty($from_mfp->id)){
			return response()->json([
				'message' => 'User doesn\'t exist in <b>Partner RankMF</b>',
				'status' => 'fail'
			]);
		}

		$to_mfp = DB::table(env('DB_DATABASE').'.users')
			->join(env('PARTNERS_MYSQL_DB_DATABASE').'.mfp_partner_login_master', 'mfp_partner_login_master.email', '=', 'users.email')
			->select('mfp_partner_login_master.id')
			->where('users.id','=',$request->to_direct_relationship_user_id)
			->first();

		if(empty($to_mfp->id)){
			return response()->json([
				'message' => 'Target user doesn\'t exist in <b>Partner RankMF</b>',
				'status' => 'fail'
			]);
		}

		//Removing white spaces from pincode set
		if(!empty($request->serviceable_pincode)){
			$request->serviceable_pincode = preg_replace('/\s+/', '', $request->serviceable_pincode);
		}

		//Removing white spaces from arn set
		if(!empty($request->arns)){
			$request->arns = preg_replace('/\s+/', '', $request->arns);
		}

		$affected = 'No';

		if(!empty($request->to_direct_relationship_user_id)){

			$mfp_arns =	DB::table(DB::raw('drm_distributor_master as d'))
				->leftjoin(DB::raw(env('PARTNERS_MYSQL_DB_DATABASE').'.mfp_partner_registration as p'), 'p.ARN', '=', 'd.ARN')
				->leftjoin(DB::raw(env('PARTNERS_MYSQL_DB_DATABASE').'.mfp_partner_login_master as b'), 'p.unit_counsellor', '=', 'b.id')
				->where('d.direct_relationship_user_id','=',$request->from_direct_relationship_user_id);

			if(!empty($request->arns)){
				$mfp_arns->whereIn("d.ARN",explode(',',$request->arns));
			}else{
				if(!empty(intval($request->is_rankmf_partner))){
					$mfp_arns->where('is_rankmf_partner','=',(intval($request->is_rankmf_partner) - 1));
				}
				if(!empty($request->serviceable_pincode)){
					$mfp_arns->whereIn("arn_pincode",explode(',',$request->serviceable_pincode));
				}
			}

			$tmp = clone $mfp_arns;

			//writing Partner RankMF transfer log
			$inserts = $tmp->select([
					DB::raw('count(d.ARN) as arn_no'),
					DB::raw('group_concat(d.ARN) as arn'),
					'p.unit_counsellor as from'
				])->groupby(['p.unit_counsellor'])->get()->toArray();

			foreach($inserts as $insert){
				if(!empty($insert->from)){
					$insert->department = 'Partner RankMF';
					$insert->to = $to_mfp->id;
					$insert->created_at = date('Y-m-d H:i:s');
					$insert->updated_at = date('Y-m-d H:i:s');
					$insert->ip = $request->ip();
					$insert->device = $request->userAgent();
					$insert->remark = $request->remark;
					$insert->updated_by = auth()->user()->id;
					if(!empty($insert->arn)){
						DB::table('drm_arn_transfer_log')->insert((array)$insert);
					}
				}
			}

			$tmp = clone $mfp_arns;

			//writing RankMF DRM transfer log
			$inserts = $tmp->select([
					DB::raw('count(d.ARN) as arn_no'),
					DB::raw('group_concat(d.ARN) as arn'),
					'd.direct_relationship_user_id as from'
				])->get()->toArray();

			foreach($inserts as $insert){
				if(!empty($insert->from)){
					$insert->department = 'RankMF DRM';
					$insert->to = $request->to_direct_relationship_user_id;
					$insert->created_at = date('Y-m-d H:i:s');
					$insert->updated_at = date('Y-m-d H:i:s');
					$insert->ip = $request->ip();
					$insert->device = $request->userAgent();
					$insert->remark = $request->remark;
					$insert->updated_by = auth()->user()->id;
					if(!empty($insert->arn)){
						DB::table('drm_arn_transfer_log')->insert((array)$insert);
					}
				}
			}

			$tmp = clone $mfp_arns;

			$mfp_affected = $tmp->update(['p.unit_counsellor' => $to_mfp->id]);

			$tmp = clone $mfp_arns;

			$affected = $tmp->update(['d.direct_relationship_user_id' => $request->to_direct_relationship_user_id]);

			// TRANSFERRING PINCODE FROM BDM TO TARGET BDM
			$users = DB::table('users_details')->select(['serviceable_pincode','user_id'])
				->whereIn('user_id',[$request->from_direct_relationship_user_id,$request->to_direct_relationship_user_id])
				->get()->toArray();
			
			$output = [];

			foreach($users as $user){
				$output[$user->user_id] = array_filter(array_unique(explode(',',$user->serviceable_pincode)));
			}

			$users = $output;
			
			$pins = explode(',',$request->serviceable_pincode);
			
			foreach($pins as $pin){
				$tmp = array_search($pin,$users[$request->from_direct_relationship_user_id]);
				unset($users[$request->from_direct_relationship_user_id][$tmp]);
				$users[$request->to_direct_relationship_user_id][] = $pin;
			}

			foreach($users as $user_id => $pins){
				DB::table('users_details')->where('user_id','=',$user_id)->update(['serviceable_pincode' => implode(',',array_filter(array_unique($pins)))]);
			}

		}
		else{
			return response()->json([
				'message' => 'Target User not selected.',
				'status' => 'fail'
			]);
		}

		return response()->json([
			'message' => "$affected ARNs Transferred and $mfp_affected  ARNs Transferred in Partner RankMF.",
			'status' => 'success'
		]);
	}
}
