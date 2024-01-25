<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReimbsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:hrms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to check approved attendance';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

		$url = getSettingsTableValue('HRMS_STATUS_CURL_URL');

		$days_ago = date('Y-m-d', strtotime('-45 days', strtotime(date('Y-m-d'))));

		$result = DB::table('drm_reimbursement')->select(DB::raw('GROUP_CONCAT(`hrms_id`) as hrms_ids'))->where('created_at','>',$days_ago)->first();

		$data = [];
		$data['id'] = $result->hrms_ids;
		
		$curl_responce = get_curl_call($url,$data);

		if(!empty($curl_responce)){
			if(isJson($curl_responce)){
				echo "Curl Responce is valid\n";
			}else{
				echo "Curl Responce is not valid json\n";
				echo "-----------------------\n";
				echo "$curl_responce\n";
				echo "-----------------------\n";
				return false;
			}
		}else{
			echo "Curl Responce is blank\n";
			return false;
		}

		$output = json_decode($curl_responce,1);

		if($output['response'] == 'data not found'){
			echo "No data found.\n";
			echo "Completed Successfully\n";
			return true;
		}

		if(isset($output['status'])){
			if($output['status'] == 'fail'){
				echo "Something went wrong\n";
			}
		}else{
			echo "Something went wrong\n";
		}

		$output = array_map(function($item){
			return [
				'hrms_id' => $item['id'],
				'status' => $item['status'],
				'remark' => $item['remark'],
			];
		},$output['response']);

		try {
			foreach($output as $item){
				DB::table('drm_reimbursement')
				->where('hrms_id','=',$item['hrms_id'])
				->update([
					'status' => $item['status'],
					'remark' => $item['remark']
				]);
				echo "\033[31mHRMS ID\033[0m : {$item['hrms_id']},";
				echo "\t\033[31mSTATUS\033[0m : {$item['status']},";
				echo "\t\033[31mREMARK\033[0m : {$item['remark']}\n";
			}
		} catch(\Illuminate\Database\QueryException $ex){ 
			echo "Something went wrong\n";
		}
		echo "Completed Successfully\n";
    }
}
