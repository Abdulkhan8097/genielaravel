<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UsresGoalController extends Controller
{

	function index(Request $request){
		$data = [];
		$data['target_meetings'] = getSettingsTableValue('BDM_TARGET_MEETINGS');
		$data['target_calls'] = getSettingsTableValue('BDM_TARGET_CALLS');
		return view('goal/user_goal',$data);
	}

	function set(Request $request){

		extract($request->all());
		
		$data = [];

		$data['msg'] = 'Target goals Updated Successfully.';
		$data['status'] = 'success';

		try{
			DB::table('settings')
				->orWhere('key', '=', 'BDM_TARGET_CALLS')
				->update([
					'value' => $target_calls
				]);
			DB::table('settings')
				->where('key', '=', 'BDM_TARGET_MEETINGS')
				->update([
					'value' => $target_meetings,
				]);
		}catch(Exception $e){
			$data['msg'] = 'Something went wrong.';
			$data['status'] = 'failed';
		}

		return response()->json($data);
	}

}
