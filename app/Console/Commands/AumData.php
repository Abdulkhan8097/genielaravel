<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use App\Models\AumCommissionDataBackup;

class AumData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Aumdata:getAumdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Aum Data';

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
        
        $aumdata = DB::connection('mongodb')->collection('mf_aum_nav_data')->raw(function($collection)
        {
            return $collection->aggregate([
            [
                '$project' => [
                    'year' =>  ['$year'=> '$date'],
                    "agent" => 1, // Include the agent field
                    "total" => 1, // Include the total field
                    'asset' => 1 
                ]
            ],
            [
                '$group' => [
                    "_id" => [
                        "agent" => '$agent',
                        "year"  => '$year'
                    ], 
                    'totalSum' => [ '$sum' => '$total'],
                    'asset' => ['$last'=>'$asset']
                ]
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'agent' => '$_id.agent',
                    'year' => '$_id.year',
                    'totalSum' => 1,
                    'asset' => 1
                ]
            ],
            [
                '$sort' => [
                    'agent' => 1,
                    'year' => 1
                ]
            ]
            ]);
        })->toArray();
       
        $aum_insert_arr = array();
        $aum_dataArr = array();
        foreach ($aumdata as $row) {
            $aum_hash = $row['agent']."_".$row['year'];
            $aum_dataArr[$aum_hash] = $row;
        }

        $partner_code_arr = array_unique(array_column($aumdata, 'agent'));
        $partner_data =  DB::connection('rankmf')->table('mutual_fund_partners.mfp_partner_registration')
				->select('partner_code','ARN')
				->where('partner_code', '!=', '')
                ->where('ARN', '!=', '')
                ->whereIn('partner_code', $partner_code_arr)
				->get()->toArray();

        $partner_arn_arr = [];
        foreach ($partner_data as $key => $value) {
            $partner_arn_arr[$value->partner_code] = $value->ARN;
        }

        //x($partner_arn_arr, 'partner_arn_arr ==========> ');
        // BROKERAGE RTI DATA 
        $brokerage_data = DB::select("SELECT mpr.ARN,SUM(Total_net) AS totalBrokerage, 
        YEAR(file_month) AS year, Agent_code as agent 
        FROM mutual_fund_partners.mfp_payment_invoice 
        JOIN mutual_fund_partners.mfp_partner_registration AS mpr ON Agent_Code = mpr.partner_code 
        WHERE delete_flag = 0 AND ARN !='' 
        GROUP BY Agent_code,YEAR(file_month)");
        //print_r($brokerage_data);exit;
        $brokerage_dataArr = array();
        foreach ($brokerage_data as $row) {
            $brokerage_hash = $row->agent."_".$row->year;
            $brokerage_dataArr[$brokerage_hash] = (array) $row;
        }

        $finaldataArr = [];
        foreach($brokerage_data as $key => $brokerage_data_val){
            //if($brokerage_data_val->agent=='sam_30869'){
            $b_hash_key = $brokerage_data_val->agent."_".$brokerage_data_val->year;
            $aum_arr = [];
            $finaldataArr[$b_hash_key] = (array) $brokerage_data[$key];
            if(!empty($aum_dataArr[$b_hash_key]) && count($aum_dataArr[$b_hash_key]) > 0){
                $aum_arr = (array) $aum_dataArr[$b_hash_key];
                $finaldataArr[$b_hash_key] = array_merge($finaldataArr[$b_hash_key],$aum_arr);
                
            }
            else{
                $finaldataArr[$b_hash_key]['totalSum'] = 0;
            }

            if(!isset($finaldataArr[$b_hash_key]['ARN'])){
                $finaldataArr[$b_hash_key]['ARN'] = $partner_arn_arr[$brokerage_data_val->agent];
            }
            //}
        }
        //x($finaldataArr);die;
        foreach($aumdata as $key => $aum_data_val){
            //if($aum_data_val->agent=='sam_30869'){
            $b_hash_key = $aum_data_val->agent."_".$aum_data_val->year;
            //y($b_hash_key, 'b_hash_key ===============>');
            $brokerage_arr = [];
            
            $key2 = 0;
            if(empty($finaldataArr[$b_hash_key])){
                $finaldataArr[$b_hash_key] = (array) $aumdata[$key];
            }
            
            if(!empty($brokerage_dataArr[$b_hash_key]) && count($brokerage_dataArr[$b_hash_key]) > 0){
                $brokerage_arr = $brokerage_dataArr[$b_hash_key];
                $brokerage_arr[$key] = array_merge($finaldataArr[$b_hash_key],$brokerage_arr);
            }
            else{
                $finaldataArr[$b_hash_key]['totalSum'] = 0;
            }

            if(!isset($finaldataArr[$b_hash_key]['totalBrokerage'])){
                $finaldataArr[$b_hash_key]['totalBrokerage'] = 0;
            }
            
            if(!isset($finaldataArr[$b_hash_key]['ARN'])){
                $finaldataArr[$b_hash_key]['ARN'] = $partner_arn_arr[$aum_data_val->agent];
            }
            //}
        }
        //x($finaldataArr, '================================Final data 2');
        
        $aum_insert_arr = array();
        foreach($finaldataArr as $row){
            
            $assetArr = array();
            if (isset($row["asset"])) {
                foreach ($row['asset'] as $key => $value) {
                    $assetArr[] = $key;
                }
                $assetArr = implode(',', $assetArr);
            }
            $asset_json = (!empty($assetArr))?$assetArr:"";
             
            $aum_insert_arr[] = array(
                'ARN'	=> $row['ARN'],
                'partner_code'=>$row['agent'],
                'arn_avg_aum'=> round($row['totalSum']), 
                'arn_total_commission' => round($row['totalBrokerage']),
                'aum_year' => $row['year'],
                'arn_business_focus_type' => $asset_json
            );
        }

        DB::statement("TRUNCATE TABLE `drm_uploaded_arn_average_aum_total_commission_data_backup`");
        if(!empty($aum_insert_arr)){
            $aumdata_chunk=array_chunk($aum_insert_arr, 1000);

            foreach($aumdata_chunk as $row) { 
                AumCommissionDataBackup::insert($row);
            }
        }
        // updating the records which were already present in both main and backup table
        $updatequery = "UPDATE drm_uploaded_arn_average_aum_total_commission_data_backup AS a 
        INNER JOIN drm_uploaded_arn_average_aum_total_commission_data AS b 
        ON (a.ARN = b.ARN AND a.aum_year = b.aum_year) 
        SET b.arn_avg_aum = a.arn_avg_aum, b.arn_total_commission = a.arn_total_commission, b.aum_year = a.aum_year,
        a.partner_code = b.partner_code, b.arn_yield = a.arn_yield, b.arn_business_focus_type = a.arn_business_focus_type";
        DB::statement($updatequery);

        // inserting the new records which are not present in main but available in backup table
        $insertQuery ="INSERT INTO drm_uploaded_arn_average_aum_total_commission_data
        (ARN, arn_avg_aum, arn_total_commission, arn_yield, arn_business_focus_type, status, aum_year,created_at,partner_code) 
        SELECT  a.ARN, a.arn_avg_aum, a.arn_total_commission, a.arn_yield,
        a.arn_business_focus_type, 1 AS status, a.aum_year, NOW() AS created_at , a.partner_code
        FROM drm_uploaded_arn_average_aum_total_commission_data_backup AS a 
        LEFT JOIN drm_uploaded_arn_average_aum_total_commission_data AS b 
        ON a.ARN = b.ARN AND a.partner_code = b.partner_code
        WHERE b.ARN IS NULL AND b.partner_code IS NULL";
        DB::statement($insertQuery);

        // updating average aum, total commission, arn yield & business focus type against ARN from MySQL table: drm_uploaded_arn_average_aum_total_commission_data
        $update_distributor ="UPDATE drm_distributor_master AS a 
        INNER JOIN drm_uploaded_arn_average_aum_total_commission_data AS b ON (a.ARN = b.ARN) 
        SET a.arn_avg_aum = b.arn_avg_aum, a.arn_total_commission = b.arn_total_commission, 
        a.arn_yield = b.arn_yield, a.arn_business_focus_type = b.arn_business_focus_type, 
        a.record_last_available_in_amfi = a.record_last_available_in_amfi WHERE 1";
        DB::statement($update_distributor);
    }
}