<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class MasterSipStpTransactionDetailsModel extends Model
{
    protected $table = 'kfintec_MasterSipStp_TransactionDetails';
    protected $fillable = ['id','zone','branch','location','ihno','folio','investor_Name','registrationDate','start_Date','end_Date','no_Of_Installments','amount','scheme','plan','scheme_code','agentCode','agentName','subbroker','scheme_Name','pan','sipType','siP_Mode','fund_Code','product_Code','frequency','trtype','to_Scheme','to_Plan','terminateDate','status','agent_code','toProductCode','toSchemeName','rejreason','umrncode','bankname','bankacno','banktype','bankifsc','sipday','sipstatus','created_at','updated_at'];

    public static function getMasterSipStpTransactionDetailsDB($input_arr = array(),$id=''){
        extract($input_arr);                // Import variables into the current symbol table from an array

        $flag_refresh_datatable = false;    // decides whether to just refresh datatable or complete page
        $output_arr = array();              // stores datatable required JSON output values
        if(isset($load_datatable) && is_numeric($load_datatable) && ($load_datatable == 1)){
            $flag_refresh_datatable = true;
        }

        $flag_export_data = false;          // decides whether request came for exporting the data or not
        if(isset($export_data) && !empty($export_data) && ($export_data == 1)){
            $flag_export_data = true;
        }

        if($flag_export_data){
            if(isset($columns) && !empty($columns) && json_decode($columns) !== FALSE){
                $columns = json_decode($columns, true);     // json_decode with parameter TRUE returns data in an array format
            }
            else{  $columns = array();  }
        }

        if(!isset($start) || empty($start) || !is_numeric($start)){
            $start = 0;
        }
        $start = intval($start);    // offset of records to be shown

        if(!isset($length) || empty($length) || !is_numeric($length)){
            $length = 10;        // default records to be shown on one page
        }
        $length = intval($length);

        $where_conditions = array();
        $where_in_conditions =array();
        $where_null_conditions = array();

        // $columns variable have list of all Table Headings/ Column names associated against the datatable
        if(isset($columns) && is_array($columns) && count($columns) > 0){
            foreach($columns as $key => $value){

                // data key have field names for whom data needs to be searched
                if(isset($value['search']['value'])){
                    if(!is_array($value['search']['value'])){
                        $value['search']['value'] = trim($value['search']['value']);        // removing leading & trailing spaces from the searched value
                    }
                }
                switch($value['data']){
                    case 'created_at':
                    case 'registrationDate':
                    case 'start_Date':
                    case 'end_Date':
                    case 'terminateDate':
                        if(isset($value['search']['value']) && !empty($value['search']['value']) && strpos($value['search']['value'], ';') !== FALSE){
                            $searching_dates = explode(';', $value['search']['value']);
                            if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE && isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                    // when both FROM DATE & TO DATE both are present
                                $where_conditions[] = array($value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                $where_conditions[] = array($value['data'], '<=', $searching_dates[1] .' 23:59:59');
                            }
                            else{
                                    // when either of the FROM DATE & TO DATE are present
                                if(isset($searching_dates[0]) && !empty($searching_dates) && strtotime($searching_dates[0]) !== FALSE){
                                    $where_conditions[] = array($value['data'], '>=', $searching_dates[0] .' 00:00:00');
                                }
                                if(isset($searching_dates[1]) && !empty($searching_dates) && strtotime($searching_dates[1]) !== FALSE){
                                    $where_conditions[] = array($value['data'], '<=', $searching_dates[1] .' 23:59:59');
                                }
                            }
                            unset($searching_dates);
                        }
                        break;
                    case 'full_name':
                    case 'folio':
                    case 'investor_Name':
                    case 'agentName':
                    case 'scheme_Name':
                    case 'pan':
                    // case 'sipType':
                    // case 'siP_Mode':
                    case 'frequency':
                    case 'trtype':
                    case 'to_Scheme':
                    case 'to_Plan':
                    case 'toSchemeName':
                    case 'rejreason':
                    case 'bankname':
                    case 'banktype':
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                        }
                        break;
                    case 'bdm_name':
                    case 'reporting_name':
                        // overwriting field name based on conditions, because same field name not present in DB tables
                        if($value['data'] == 'bdm_name'){
                            $value['data'] = 'bdm.name';
                        }
                        elseif($value['data'] == 'reporting_name'){
                            $value['data'] = 'reporting.name';
                        }

                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                                // coming here if searched text have some value
                            if(strtolower($value['search']['value']) != 'direct'){
                                    // coming here if searched text is not equal to DIRECT, which means searching record have an broker id and that broker id have some BDM assigned to it
                                $where_conditions[] = array($value['data'], 'like', '%'. $value['search']['value'] .'%');
                            }
                            else{
                                    // coming here in case of searching record don't have any broker id assigned to it, that's why checking records which are having NULL values in fields like BDM NAME & REPORTING MANAGER
                                $where_null_conditions[] = $value['data'];
                            }
                        }
                        break;
                    case 'status':
                        if($value['data'] == 'status'){
                            $value['data'] = 'sip_master.status';
                        }
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            if(!is_array($value['search']['value'])){

                                $arr_Status= explode(',', $value['search']['value']);
                            }
                            else{
                                $arr_Status= $value['search']['value'];
                            }
                            $where_in_conditions[] = array($value['data'],$arr_Status);

                        }
                        break;
                    case 'broker_id':
                    case 'branch':
                    case 'ihno':
                    case 'no_Of_Installments':
                    case 'amount':
                    case 'scheme_code':
                    case 'agentCode':
                    case 'subbroker':
                    case 'fund_Code':
                    case 'product_Code':
                    case 'toProductCode':
                    case 'umrncode':
                    case 'bankacno':
                    case 'bankifsc':
                    case 'zone':
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array($value['data'],'=',$value['search']['value']);
                        }
                        break;
                    default:
                        if(isset($value['search']['value']) && !empty($value['search']['value'])){
                            $where_conditions[] = array($value['data'], '=', $value['search']['value']);
                        }
                }
            }
            unset($key, $value);
        }

        $order_by_clause = '';
        // $order is the POST variable sent by Datatable plugin. We are taking on single column ordering that's why considering $order[0] element only.
        if(isset($order[0]['column']) && isset($order[0]['dir']) && isset($columns[$order[0]['column']]['data']) && !empty($columns[$order[0]['column']]['data'])){
            // $columns variable have list of all Table Headings/ Column names associated against the datatable
            switch($columns[$order[0]['column']]['data']){
                default:
                $order_by_clause = $columns[$order[0]['column']]['data'];
            }
            $order_by_clause .= ' ' .$order[0]['dir'];
        }
        else{
            $order_by_clause = 'sip_master.investor_Name ASC';
        }
        
        $records = DB::table('samcomf_investor_db.kfintec_MasterSipStp_TransactionDetails as sip_master')
        ->select('sip_master.*',
           DB::raw('IFNULL(bdm.name, "DIRECT") AS bdm_name'),
           DB::raw('IFNULL(reporting.name, "DIRECT") AS reporting_name')
        )
        ->leftjoin('samcomf.drm_distributor_master AS drm','sip_master.agent_code', '=','drm.ARN')
        ->leftJoin('samcomf.users AS bdm' ,'drm.direct_relationship_user_id','=','bdm.id')
        ->leftJoin('samcomf.users_details AS bdm_details' ,'bdm.id' ,'=' ,'bdm_details.user_id')
        ->leftJoin('samcomf.users AS reporting' ,'bdm_details.reporting_to', '=' ,'reporting.id');

        if(count($where_conditions) > 0){
            $records = $records->where($where_conditions);
        }
        if(count($where_null_conditions) > 0){
            foreach($where_null_conditions as $null_condition){
                $records = $records->whereNull($null_condition);
            }
            unset($null_condition);
        }

        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition){
                $records = $records->whereIn($in_condition[0], $in_condition[1]);
            }
            unset($in_condition);
        }
        $no_of_records = 0;
        if(!$flag_export_data){
            // calculating number of records without using LIMIT & OFFSET for pagination, only when showing data inside DataTable
            $no_of_records = $records->count();
            if(isset($start) && is_numeric($start)){
                $records = $records->offset($start);
            }

            if(isset($length) && is_numeric($length)){
                $records = $records->limit($length);
            }
        }

        $frequqncy_array = array('Q' => 'Quaterly','M' => 'Monthly','D' => 'Daily','H' => 'Half Yearly','W' => 'Weekly','F' => 'Fortnightly');
        $records = $records->orderByRaw($order_by_clause)->get();
        foreach ($records as $key => $value) {
            if(isset($value->frequency) && !empty($value->frequency)){
                $records[$key]->frequency = ($frequqncy_array[$value->frequency]??$records[$key]->frequency);
            }
        }
        unset($where_conditions, $order_by_clause, $date_field_from_query);
        return array('records' => $records, 'no_of_records' => $no_of_records);
    }

    public static function getSipStpStpSatus(){
        return $records = DB::table('samcomf_investor_db.kfintec_MasterSipStp_TransactionDetails')
        ->select('status')
        ->distinct('status')
        ->where('status', '!=','')
        ->where('status', '!=',null)
        ->get();
    } 

    public static function get_putinsip_bdm_wise_count_of_registered_sip(){
        $to_date = '2022-11-05 23:59:59';
        $current_date=date("Y-m-d 00:00:00");

        if(strtotime($current_date) < strtotime($to_date)){
            $to_date = $current_date;
        }

        $records= DB::select("SELECT b.bdm_name, b.reporting_name, COUNT(DISTINCT b.agentCode) AS active_distributors, SUM(b.no_of_sip_applications) AS no_of_sip_applications, SUM(IFNULL(b.consolidated_sip_amount, 0)) AS consolidated_sip_amount, SUM(b.qualifier) AS qualifier, SUM(b.ticket_size) AS ticket_size, SUM(b.zero_to_twentyfive_thousand) AS zero_to_twentyfive_thousand, SUM(b.twentyfive_thousand_to_onelac_fiftythousand) AS twentyfive_thousand_to_onelac_fiftythousand,SUM(b.more_than_onelac_fiftythousand) AS more_than_onelac_fiftythousand
                  FROM (SELECT a.*, IF(a.consolidated_sip_amount >= 150000, 1, 0) AS qualifier,
                  FLOOR(a.consolidated_sip_amount/150000) AS ticket_size,
                  CASE WHEN(a.consolidated_sip_amount > 0 AND a.consolidated_sip_amount <= 24999) THEN 1 ELSE 0 END AS zero_to_twentyfive_thousand,
                  CASE WHEN(a.consolidated_sip_amount >= 25000 AND a.consolidated_sip_amount <= 149999) THEN 1 ELSE 0 END AS twentyfive_thousand_to_onelac_fiftythousand,
                  CASE WHEN(a.consolidated_sip_amount >= 150000) THEN 1 ELSE 0 END AS more_than_onelac_fiftythousand
                  FROM (SELECT sip_master.agentCode, sip_master.agentName, IFNULL(drm.arn_zone, '') AS agent_zone, IFNULL(bdm.name, 'DIRECT') AS bdm_name, IFNULL(reporting.name, 'DIRECT') AS reporting_name, COUNT(sip_master.ihno) AS no_of_sip_applications, SUM(IFNULL(sip_master.amount, 0)) AS consolidated_sip_amount 
                    FROM samcomf_investor_db.kfintec_MasterSipStp_TransactionDetails AS sip_master 
                    LEFT JOIN samcomf.drm_distributor_master AS drm ON (sip_master.agent_code = drm.ARN) 
                    LEFT JOIN samcomf.users AS bdm ON (drm.direct_relationship_user_id = bdm.id) 
                    LEFT JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) 
                    LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id) 
                    WHERE sip_master.registrationDate >= :campaign_start_date AND (sip_master.status = 'Live SIP' OR (sip_master.terminateDate NOT IN ('1970-01-01 05:30:00', '1900-01-01 00:00:00') AND sip_master.terminateDate >= '2023-01-01 00:00:00')) AND sip_master.scheme = 'FC' 
                    AND sip_master.start_date >= :campaign_start_date AND sip_master.registrationDate < :campaign_end_date 
                    AND sip_master.agent_code NOT IN (0) 
                    GROUP BY bdm_name, reporting_name, sip_master.agentCode, sip_master.agentName 
                    ORDER BY sip_master.agentCode, sip_master.agentName, bdm_name, reporting_name) AS a) AS b 
            GROUP BY b.bdm_name, b.reporting_name 
            ORDER BY active_distributors DESC;" , array(':campaign_start_date' => '2022-07-01 00:00:00', ':campaign_end_date' => $to_date));
        return $records;
    }



    public static function get_putinsip_amount_wise_count_of_registered_sip(){
        $to_date = '2022-11-05 23:59:59';
        $current_date=date("Y-m-d 00:00:00");

        if(strtotime($current_date) < strtotime($to_date)){
            $to_date = $current_date;
        }

        $records = DB::select("SELECT amount_category.start_range, amount_category.end_range, CONCAT('Greater than equal to ', amount_category.start_range, CASE WHEN(amount_category.end_range > 150001) THEN '' ELSE CONCAT(' - Less than equal to ', amount_category.end_range) END) AS amount_category_text, COUNT(DISTINCT sip_master.agentCode) AS no_of_registered_sip 
            FROM (SELECT 0 AS start_range, 10000 AS end_range 
                UNION SELECT 10001 AS start_range, 30000 AS end_range 
                UNION SELECT 30001 AS start_range, 50000 AS end_range 
                UNION SELECT 50001 AS start_range, 75000 AS end_range 
                UNION SELECT 75001 AS start_range, 100000 AS end_range 
                UNION SELECT 100001 AS start_range, 125000 AS end_range 
                UNION SELECT 125001 AS start_range, 150000 AS end_range 
                UNION SELECT 150001 AS start_range, 9999999999999 AS end_range) AS amount_category 
            INNER JOIN (SELECT sip_master.agentCode, COUNT(sip_master.ihno) AS no_of_sip_applications, SUM(IFNULL(sip_master.amount, 0)) AS consolidated_sip_amount 
                FROM samcomf_investor_db.kfintec_MasterSipStp_TransactionDetails AS sip_master 
                LEFT JOIN samcomf.drm_distributor_master AS drm ON (sip_master.agent_code = drm.ARN) 
                LEFT JOIN samcomf.users AS bdm ON (drm.direct_relationship_user_id = bdm.id) 
                LEFT JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) 
                LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id) 
                WHERE sip_master.registrationDate >= :campaign_start_date AND sip_master.registrationDate < :campaign_end_date 
                AND (sip_master.status = 'Live SIP' OR (sip_master.terminateDate NOT IN ('1970-01-01 05:30:00', '1900-01-01 00:00:00') AND sip_master.terminateDate >= '2023-01-01 00:00:00')) AND sip_master.scheme = 'FC' 
                AND sip_master.agent_code NOT IN (0) AND sip_master.start_date >= :campaign_start_date 
                GROUP BY sip_master.agentCode 
                ORDER BY sip_master.agentCode) AS sip_master 
            ON (sip_master.consolidated_sip_amount >= amount_category.start_range AND sip_master.consolidated_sip_amount <= amount_category.end_range) 
            WHERE 1 
            GROUP BY amount_category.start_range, amount_category.end_range, amount_category_text;", array(':campaign_start_date' => '2022-07-01 00:00:00', ':campaign_end_date' => $to_date));
        return $records;
    } 

    public static function get_putinsip_arn_wise_count_of_registered_sip(){
        $to_date = '2022-11-05 23:59:59';
        $current_date=date("Y-m-d 00:00:00");

        if(strtotime($current_date) < strtotime($to_date)){
            $to_date = $current_date;
        }

        $records = DB::select("SELECT sip_master.agentCode, sip_master.agentName, IFNULL(bdm.name, 'DIRECT') AS bdm_name,
           IFNULL(reporting.name, 'DIRECT') AS reporting_name, 
           COUNT(sip_master.ihno) AS no_of_sip_applications,
           drm.arn_zone,
           SUM(IFNULL(sip_master.amount, 0)) AS consolidated_sip_amount
           FROM samcomf_investor_db.kfintec_MasterSipStp_TransactionDetails AS sip_master
           LEFT JOIN samcomf.drm_distributor_master AS drm ON (sip_master.agent_code = drm.ARN) 
           LEFT JOIN samcomf.users AS bdm ON (drm.direct_relationship_user_id = bdm.id)
           LEFT JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id)
           LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id) 
           WHERE sip_master.registrationDate >= :campaign_start_date 
           AND (sip_master.status = 'Live SIP' OR (sip_master.terminateDate NOT IN ('1970-01-01 05:30:00', '1900-01-01 00:00:00') AND sip_master.terminateDate >= '2023-01-01 00:00:00')) 
           AND sip_master.scheme = 'FC' 
           AND sip_master.start_date >= :campaign_start_date 
           AND sip_master.registrationDate < :campaign_end_date 
           AND sip_master.agent_code NOT IN (0) GROUP BY bdm_name, reporting_name,
           sip_master.agentCode, sip_master.agentName ORDER BY sip_master.agentCode,
           sip_master.agentName, bdm_name, reporting_name;",array(':campaign_start_date' => '2022-07-01 00:00:00', ':campaign_end_date' => $to_date));
        return $records;
 
    }

    public static function get_elss_nfo_period_distributor_wise_inflows($input_arr = array()){
        $to_date = '2022-12-16 00:00:00';
        $current_date = date("Y-m-d 00:00:00");
        if(strtotime($current_date) < strtotime($to_date)){
            /*$current_date = date_create($current_date);
            date_sub($current_date, date_interval_create_from_date_string("1 days"));
            $to_date = date_format($current_date, "Y-m-d 00:00:00");*/
            $to_date = $current_date;
        }

        $records = DB::select("SELECT d.ARN AS 'ARN Number', IFNULL(d.name, '') AS 'ARN Name', IFNULL(d.amfi_euin, '') AS amfi_euin, CASE WHEN(c.is_samcomf_partner = 1) THEN 'Yes' ELSE 'No' END AS 'Is SAMCOMF Partner?', IFNULL(d.email, c.arn_email) AS 'ARN Email', CASE WHEN(IFNULL(d.mobile, '') <> '') THEN d.mobile WHEN(IFNULL(c.arn_telephone_r, '') <> '') THEN c.arn_telephone_r WHEN(IFNULL(c.arn_telephone_o, '') <> '') THEN c.arn_telephone_o ELSE '' END AS 'ARN Contact Number', IFNULL(e.name, '') AS 'BDM Name', IFNULL(e.email, '') AS 'BDM Email', IFNULL(f.mobile_number, '') AS 'BDM Contact Number', IFNULL(g.name, '') AS 'Reporting Name', IFNULL(g.email, '') AS 'Reporting Email', IFNULL(h.mobile_number, '') AS 'Reporting Contact Number', IFNULL(h.designation, '') AS 'Reporting Designation', SUM(CASE WHEN (b.type_of_transaction = 'Lumpsum') THEN 1 ELSE 0 END) AS no_of_lumpsum_applications, SUM(CASE WHEN (b.type_of_transaction = 'Lumpsum') THEN IFNULL(CASE WHEN(a.purred IN ('R', 'Z')) THEN (a.amt * -1) ELSE a.amt END, 0) ELSE 0 END) AS lumpsum_amount, SUM(CASE WHEN (b.type_of_transaction = 'SIP') THEN 1 ELSE 0 END) AS no_of_sip_applications, SUM(CASE WHEN (b.type_of_transaction = 'SIP') THEN IFNULL(CASE WHEN(a.purred IN ('R', 'Z')) THEN (a.amt * -1) ELSE a.amt END, 0) ELSE 0 END) AS sip_amount, SUM(CASE WHEN (b.type_of_transaction = 'STP') THEN 1 ELSE 0 END) AS no_of_stp_applications, SUM(CASE WHEN (b.type_of_transaction = 'STP') THEN IFNULL(CASE WHEN(a.purred IN ('R', 'Z')) THEN (a.amt * -1) ELSE a.amt END, 0) ELSE 0 END) AS stp_amount, SUM(CASE WHEN (b.type_of_transaction = 'Switch') THEN 1 ELSE 0 END) AS no_of_switch_applications, SUM(CASE WHEN (b.type_of_transaction = 'Switch') THEN IFNULL(CASE WHEN(a.purred IN ('R', 'Z')) THEN (a.amt * -1) ELSE a.amt END, 0) ELSE 0 END) AS switch_amount, SUM(CASE WHEN (b.type_of_transaction NOT IN ('Lumpsum', 'SIP', 'STP', 'Switch')) THEN 1 ELSE 0 END) AS no_of_other_order_type_applications, SUM(CASE WHEN (b.type_of_transaction NOT IN ('Lumpsum', 'SIP', 'STP', 'Switch')) THEN IFNULL(CASE WHEN(a.purred IN ('R', 'Z')) THEN (a.amt * -1) ELSE a.amt END, 0) ELSE 0 END) AS other_order_type_amount, COUNT(a.ihno) AS overall_no_of_applications, SUM(IFNULL(CASE WHEN(a.purred IN ('R', 'Z')) THEN (a.amt * -1) ELSE a.amt END, 0)) AS overall_amount FROM samcomf.user_account AS d INNER JOIN samcomf.drm_distributor_master AS c ON (d.ARN = c.ARN) LEFT JOIN samcomf.users AS e ON (c.direct_relationship_user_id = e.id) LEFT JOIN samcomf.users_details AS f ON (e.id = f.user_id) LEFT JOIN samcomf.users AS g ON (f.reporting_to = g.id) LEFT JOIN samcomf.users_details AS h ON (g.id = h.user_id) LEFT JOIN samcomf_investor_db.kfintechTableTransactionDetails AS a ON (d.ARN = a.agent_code AND (TRIM(a.scheme) = 'EL' OR TRIM(a.dd_tscheme) = 'EL') AND a.agent_code IS NOT NULL AND a.distributor NOT IN ('000000-0') AND a.trdate < :nfo_end_date) LEFT JOIN samcomf_investor_db.transaction_type AS b ON (a.trtype = b.tm_trtype) WHERE d.status = 2 GROUP BY d.ARN ORDER BY d.ARN ASC;", array(":nfo_end_date" => $to_date));
        return $records;
    }

    public static function get_investor_lead_and_registration_data_four_to_ten(){
        $investor_lead_and_registration = array();
        $records_registrstion = array();
        $records_leads = array();
        
        $day = date('D');
        if(strtolower($day)  == 'mon')
        {
          $current_date=date("Y-m-d 16:00:00", strtotime('-3 day'));
        }else{
          $current_date=date('Y-m-d 16:00:00' , strtotime('-1 day')); 
        }
        $till_date=date("Y-m-d 10:00:00");

        $records_leads= DB::select("SELECT `lead`.`pan`, `lead`.`name`, `lead`.`email`, `lead`.`mobile`, `lead`.`from_site`, `lead`.`whatsapp_optin`, `lead`.`ip_address`, `lead`.`broker_id`, `lead`.`created_at`, COUNT(`order`.`ihno`) AS `order_count` FROM `samcomf_investor_db`.`investor_lead` AS `lead` 
        INNER JOIN `samcomf_investor_db`.`investor_account` AS `account` ON (`lead`.`pan` = `account`.`pan`) 
        LEFT JOIN `samcomf_investor_db`.`kfintec_Postendorsement_TransactionDetails_final` AS `order` ON (`account`.`pan` = `order`.`pan`) WHERE `account`.`pan` IS NOT NULL AND IFNULL(`lead`.`broker_id`, '') = '' AND `lead`.`created_at` >= :param_current_date and `lead`.`created_at` <= :param_till_date and lead.from_site NOT Like '%register/%' AND lead.from_site NOT IN ('offline') GROUP BY `lead`.`pan` HAVING `order_count` = 0 ORDER BY `lead`.`pan` ASC;", array(':param_current_date' => $current_date, ':param_till_date' => $till_date));

         $records_registrstion = DB::select("SELECT `lead`.`pan`, `lead`.`name`, `lead`.`email`, `lead`.`mobile`, `lead`.`from_site`, `lead`.`whatsapp_optin`, `lead`.`ip_address`, `lead`.`broker_id`, `lead`.`created_at` FROM `samcomf_investor_db`.`investor_lead` AS `lead` 
        LEFT JOIN `samcomf_investor_db`.`investor_account` AS `account` ON (`lead`.`pan` = `account`.`pan`) 
        WHERE `account`.`pan` IS NULL AND IFNULL(`lead`.`broker_id`, '') = '' AND `lead`.`created_at` >= :param_current_date and `lead`.`created_at` <= :param_till_date and lead.from_site NOT Like '%register/%' AND lead.from_site NOT IN ('offline') ORDER BY `lead`.`created_at` ASC, `lead`.`pan` ASC;", array(':param_current_date' => $current_date, ':param_till_date' => $till_date));

        $investor_lead_and_registration = array('records_registrstion' => $records_registrstion, 'records_leads' => $records_leads);
        return $investor_lead_and_registration;
    }
         
    public static function get_investor_lead_and_registration_data_ten_to_four(){
        $investor_lead_and_registration = array();
        $records_registrstion = array();
        $records_leads = array();

        $current_date=date("Y-m-d 10:00:00");
        $till_date=date("Y-m-d 16:00:00");

        $records_leads= DB::select("SELECT `lead`.`pan`, `lead`.`name`, `lead`.`email`, `lead`.`mobile`, `lead`.`from_site`, `lead`.`whatsapp_optin`, `lead`.`ip_address`, `lead`.`broker_id`, `lead`.`created_at`, COUNT(`order`.`ihno`) AS `order_count` FROM `samcomf_investor_db`.`investor_lead` AS `lead` 
        INNER JOIN `samcomf_investor_db`.`investor_account` AS `account` ON (`lead`.`pan` = `account`.`pan`) 
        LEFT JOIN `samcomf_investor_db`.`kfintec_Postendorsement_TransactionDetails_final` AS `order` ON (`account`.`pan` = `order`.`pan`) WHERE `account`.`pan` IS NOT NULL AND IFNULL(`lead`.`broker_id`, '') = '' AND `lead`.`created_at` >= :param_current_date and `lead`.`created_at` <= :param_till_date and lead.from_site NOT Like '%register/%' AND lead.from_site NOT IN ('offline') GROUP BY `lead`.`pan` HAVING `order_count` = 0 ORDER BY `lead`.`pan` ASC;", array(':param_current_date' => $current_date, ':param_till_date' => $till_date));

         $records_registrstion = DB::select("SELECT `lead`.`pan`, `lead`.`name`, `lead`.`email`, `lead`.`mobile`, `lead`.`from_site`, `lead`.`whatsapp_optin`, `lead`.`ip_address`, `lead`.`broker_id`, `lead`.`created_at` FROM `samcomf_investor_db`.`investor_lead` AS `lead` 
        LEFT JOIN `samcomf_investor_db`.`investor_account` AS `account` ON (`lead`.`pan` = `account`.`pan`) 
        WHERE `account`.`pan` IS NULL AND IFNULL(`lead`.`broker_id`, '') = '' AND `lead`.`created_at` >= :param_current_date and `lead`.`created_at` <= :param_till_date and lead.from_site NOT Like '%register/%' AND lead.from_site NOT IN ('offline') ORDER BY `lead`.`created_at` ASC, `lead`.`pan` ASC;", array(':param_current_date' => $current_date, ':param_till_date' => $till_date));

        $investor_lead_and_registration = array('records_registrstion' => $records_registrstion, 'records_leads' => $records_leads);
        return $investor_lead_and_registration;
    }

    public static function get_event_analytics_nfo_scheme_road_shows($input_arr = array()){
        $to_date = '2022-12-16 00:00:00';
        $current_date = date("Y-m-d 00:00:00");
        if(strtotime($current_date) < strtotime($to_date)){
            $to_date = $current_date;
        }

        $records = DB::select("SELECT e.event_city AS 'Event City', e.event_date AS 'Event Date', ec.arn AS 'ARN', IFNULL(partner.name, drm.arn_holders_name) AS 'ARN Name', IFNULL(partner.email, drm.arn_email) AS 'ARN Email', IFNULL(partner.mobile, IFNULL(drm.arn_telephone_r, drm.arn_telephone_o)) AS 'ARN Number', bdm.name AS 'BDM Name', bdm_details.mobile_number AS 'BDM Number', bdm.email AS 'BDM Email', reporting.name AS 'Reporting Name', reporting_details.mobile_number AS 'Reporting Number', reporting.email AS 'Reporting Email', CASE WHEN(drm.is_samcomf_partner = 1) THEN 'yes' ELSE 'no' END AS 'Empanelled', CASE WHEN(IFNULL(drm.samcomf_partner_aum, 0) > 0) THEN 'yes' ELSE 'no' END AS 'Over All Active', CASE WHEN(IFNULL(pre_endorsement.invested_amount, 0) > 0) THEN 'yes' ELSE 'no' END AS 'Active in ELSS', IFNULL(pre_endorsement.invested_amount, 0) AS 'Contribution in ELSS Amount', CONCAT('https://samcomf.com/d/register/', drm.ARN, CASE WHEN(IFNULL(drm.arn_euin,'') != '') THEN CONCAT('/', drm.arn_euin) ELSE '' END) 'Smart Transaction Link' FROM samcomf_scom_db.sm_ep_events e, samcomf_scom_db.sm_ep_events_clients ec LEFT JOIN samcomf.drm_distributor_master AS drm ON ((TRIM(REGEXP_REPLACE(TRIM(REGEXP_REPLACE(TRIM(REGEXP_REPLACE(ec.arn, 'ARN', '')), 'AR-', '')), '-', '')) + 0) COLLATE utf8mb4_0900_ai_ci = drm.ARN) LEFT JOIN samcomf.user_account AS partner ON ((TRIM(REGEXP_REPLACE(TRIM(REGEXP_REPLACE(TRIM(REGEXP_REPLACE(ec.arn, 'ARN', '')), 'AR-', '')), '-', '')) + 0) COLLATE utf8mb4_0900_ai_ci = partner.ARN) LEFT JOIN samcomf.users AS bdm ON (drm.direct_relationship_user_id = bdm.id) LEFT JOIN samcomf.users_details AS bdm_details ON (bdm.id = bdm_details.user_id) LEFT JOIN samcomf.users AS reporting ON (bdm_details.reporting_to = reporting.id) LEFT JOIN samcomf.users_details AS reporting_details ON (reporting.id = reporting_details.user_id) LEFT JOIN (SELECT pre_endorsement.agent_code, SUM(CASE WHEN(pre_endorsement.purred IN ('R', 'Z')) THEN (pre_endorsement.amt * -1) ELSE pre_endorsement.amt END) AS invested_amount FROM samcomf_investor_db.kfintechTableTransactionDetails AS pre_endorsement WHERE (TRIM(pre_endorsement.scheme) = 'EL' OR TRIM(pre_endorsement.dd_tscheme) = 'EL') AND pre_endorsement.pln = 'RG' AND pre_endorsement.trdate < :nfo_end_date GROUP BY pre_endorsement.agent_code) AS pre_endorsement ON ((TRIM(REGEXP_REPLACE(TRIM(REGEXP_REPLACE(TRIM(REGEXP_REPLACE(ec.arn, 'ARN', '')), 'AR-', '')), '-', '')) + 0) COLLATE utf8mb4_0900_ai_ci = pre_endorsement.agent_code) WHERE e.id = ec.event_id AND e.event_date >= :event_start_date AND e.event_date <= :event_end_date AND e.event_name != 'Samco Mutual Fund Closed-door Meetup Test' AND ec.attendance = 1 GROUP BY e.event_date, e.event_city, ec.arn ORDER BY e.event_date ASC, e.event_city, ec.arn ASC;", array(":event_start_date" => '2022-10-30', ":event_end_date" => '2022-12-05', ":nfo_end_date" => $to_date));
        return $records;
    }

    public static function get_distributor_wise_scheme_aum($input_arr = array()){
        $arr_record_headings = array();
        $records = array();
        try{
            $scheme_master_records = \App\Models\SchemeMasterModel::select(array('RTA_Scheme_Code', 'Scheme_Name', 'SETTLEMENT_TYPE'))
                                                                    ->where(
                                                                        array(
                                                                            array('Scheme_Plan', '=', 'Regular')
                                                                        )
                                                                    )
                                                                    ->groupBy('scheme')
                                                                    ->get()
                                                                    ->toArray();
            $post_endorsement_select_conditions = array();
            $pre_endorsement_select_conditions = array();
            if(is_array($scheme_master_records) && count($scheme_master_records) > 0){
                foreach($scheme_master_records as $_key => $_value){
                    // $arr_record_headings[$_value['RTA_Scheme_Code'] .'_available_units'] = $_value['Scheme_Name'] .' Available Units';
                    $arr_record_headings[$_value['RTA_Scheme_Code'] .'_aum'] = $_value['Scheme_Name'] .' AUM';
                    if(isset($_value['SETTLEMENT_TYPE']) && !empty($_value['SETTLEMENT_TYPE'])){
                        switch(strtolower($_value['SETTLEMENT_TYPE'])){
                            case 'mf':
                                $pre_endorsement_select_conditions[] = "SUM(CASE WHEN(b.RTA_Scheme_Code = '". $_value['RTA_Scheme_Code'] ."') THEN IFNULL(b.available_units, 0) ELSE 0 END) AS ". $_value['RTA_Scheme_Code'] ."_available_units, SUM(CASE WHEN(b.RTA_Scheme_Code = '". $_value['RTA_Scheme_Code'] ."') THEN IFNULL(b.scheme_aum, 0) ELSE 0 END) AS ". $_value['RTA_Scheme_Code'] ."_aum";
                                break;
                            default:
                                $post_endorsement_select_conditions[] = "SUM(CASE WHEN(b.RTA_Scheme_Code = '". $_value['RTA_Scheme_Code'] ."') THEN IFNULL(b.available_units, 0) ELSE 0 END) AS ". $_value['RTA_Scheme_Code'] ."_available_units, SUM(CASE WHEN(b.RTA_Scheme_Code = '". $_value['RTA_Scheme_Code'] ."') THEN IFNULL(b.scheme_aum, 0) ELSE 0 END) AS ". $_value['RTA_Scheme_Code'] ."_aum";
                        }
                    }
                }
                unset($_key, $_value);
            }
            else{
                // in case of scheme details not found then returning an empty array
                return array();
            }

            if(is_array($post_endorsement_select_conditions) && count($post_endorsement_select_conditions) > 0){
                $post_endorsement_select_conditions = implode(', ', $post_endorsement_select_conditions);
            }
            else{
                $post_endorsement_select_conditions = '';
            }

            if(is_array($pre_endorsement_select_conditions) && count($pre_endorsement_select_conditions) > 0){
                $pre_endorsement_select_conditions = implode(', ', $pre_endorsement_select_conditions);
            }
            else{
                $pre_endorsement_select_conditions = '';
            }

            $post_endorsement_records = DB::select("SELECT (b.agent_code +0 ) AS agent_code". (!empty($post_endorsement_select_conditions)?",". $post_endorsement_select_conditions:'') ." FROM (SELECT a.agent_code, a.RTA_Scheme_Code, IFNULL(a.available_units, 0) AS available_units, (IFNULL(a.available_units, 0) * IFNULL(scheme_master_details.nav, 0)) AS scheme_aum FROM (SELECT transactions.agent_code, scheme_master.RTA_Scheme_Code, SUM(CASE WHEN (transactions.purred IN ('R', 'Z')) THEN (IFNULL(transactions.units, 0) * -1) ELSE IFNULL(transactions.units, 0) END) AS available_units FROM samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final AS transactions FORCE INDEX(idx_agent_code) INNER JOIN samcomf_investor_db.scheme_master ON (transactions.scheme_code = scheme_master.RTA_Scheme_Code) WHERE scheme_master.Scheme_Plan = 'Regular' AND transactions.agent_code NOT IN ('0') GROUP BY transactions.agent_code, scheme_master.RTA_Scheme_Code ORDER BY transactions.agent_code ASC) AS a INNER JOIN samcomf_investor_db.scheme_master_details ON (a.RTA_Scheme_Code = scheme_master_details.RTA_Scheme_Code) WHERE 1 GROUP BY a.agent_code, a.RTA_Scheme_Code) AS b GROUP BY b.agent_code ORDER BY b.agent_code ASC;");

            $pre_endorsement_records = DB::select("SELECT (b.agent_code +0 ) AS agent_code". (!empty($pre_endorsement_select_conditions)?",". $pre_endorsement_select_conditions:'') ." FROM (SELECT transactions.agent_code, scheme_master.RTA_Scheme_Code, SUM(CASE WHEN (transactions.purred IN ('R', 'Z')) THEN (IFNULL(transactions.units, 0) * -1) ELSE IFNULL(transactions.units, 0) END) AS available_units, SUM(CASE WHEN (transactions.purred IN ('R', 'Z')) THEN (IFNULL(transactions.amt, 0) * -1) ELSE IFNULL(transactions.amt, 0) END) AS scheme_aum FROM samcomf_investor_db.kfintechTableTransactionDetails AS transactions FORCE INDEX(idx_pre_endorsement_agent_code) INNER JOIN samcomf_investor_db.scheme_master ON (CONCAT(TRIM(transactions.scheme), TRIM(transactions.pln)) = scheme_master.RTA_Scheme_Code OR CONCAT(TRIM(transactions.dd_tscheme), TRIM(transactions.dd_tplan)) = scheme_master.RTA_Scheme_Code) WHERE scheme_master.Scheme_Plan = 'Regular' AND transactions.agent_code NOT IN ('0') AND scheme_master.SETTLEMENT_TYPE = 'MF' AND transactions.trdate >= scheme_master.nfo_start_date AND transactions.trdate <= scheme_master.nfo_end_date GROUP BY transactions.agent_code, scheme_master.RTA_Scheme_Code) AS b GROUP BY b.agent_code ORDER BY b.agent_code ASC;");

            $arr_unique_agent_codes = array();
            if($post_endorsement_records && is_array($post_endorsement_records) && count($post_endorsement_records) > 0){
                $arr_unique_agent_codes = array_merge($arr_unique_agent_codes, array_column($post_endorsement_records, 'agent_code'));
                $post_endorsement_records = array_column($post_endorsement_records, NULL, 'agent_code');
            }
            if($pre_endorsement_records && is_array($pre_endorsement_records) && count($pre_endorsement_records) > 0){
                $arr_unique_agent_codes = array_merge($arr_unique_agent_codes, array_column($pre_endorsement_records, 'agent_code'));
                $pre_endorsement_records = array_column($pre_endorsement_records, NULL, 'agent_code');
            }

            $arr_arn_details = array();
            if(is_array($arr_unique_agent_codes) && count($arr_unique_agent_codes) > 0){
                $arr_arn_details = DB::table('samcomf.drm_distributor_master AS c')
                                        ->leftJoin('samcomf.user_account AS d', 'c.ARN', '=', 'd.ARN')
                                        ->leftJoin('samcomf.users AS e', 'c.direct_relationship_user_id', '=', 'e.id')
                                        ->leftJoin('samcomf.users_details AS f', 'e.id', '=', 'f.user_id')
                                        ->leftJoin('samcomf.users AS g', 'f.reporting_to', '=', 'g.id')
                                        ->leftJoin('samcomf.users_details AS h', 'g.id', '=', 'h.user_id')
                                        ->whereIn('c.ARN', $arr_unique_agent_codes)
                                        ->select(DB::raw("c.ARN AS 'ARN Number', IFNULL(d.name, c.arn_holders_name) AS 'ARN Name', IFNULL(d.amfi_euin, IFNULL(c.arn_euin, '')) AS amfi_euin, CASE WHEN(c.is_samcomf_partner = 1) THEN 'Yes' ELSE 'No' END AS 'Is SAMCOMF Partner?', IFNULL(d.email, c.arn_email) AS 'ARN Email', CASE WHEN(IFNULL(d.mobile, '') <> '') THEN d.mobile WHEN(IFNULL(c.arn_telephone_r, '') <> '') THEN c.arn_telephone_r WHEN(IFNULL(c.arn_telephone_o, '') <> '') THEN c.arn_telephone_o ELSE '' END AS 'ARN Contact Number', IFNULL(e.name, '') AS 'BDM Name', IFNULL(e.email, '') AS 'BDM Email', IFNULL(f.mobile_number, '') AS 'BDM Contact Number', IFNULL(g.name, '') AS 'Reporting Name', IFNULL(g.email, '') AS 'Reporting Email', IFNULL(h.mobile_number, '') AS 'Reporting Contact Number', IFNULL(h.designation, '') AS 'Reporting Designation', IFNULL(d.status, 0) AS arn_status"))
                                        ->groupBy('c.ARN')
                                        ->orderBy('c.ARN', 'ASC')
                                        ->get()
                                        ->toArray();
                if($arr_arn_details && is_array($arr_arn_details) && count($arr_arn_details) > 0){
                    $arr_arn_details = array_column($arr_arn_details, NULL, 'ARN Number');
                    array_walk($arr_arn_details, function(&$_value){
                        $_value = (array) $_value;
                    });
                }

                foreach($arr_unique_agent_codes as $agent_code){
                    // if(!empty(($arr_arn_details[$agent_code]['ARN Number']??''))){
                        $row = array();
                        if(!isset($records[$agent_code])){
                            $records[$agent_code] = array();
                        }

                        if(isset($post_endorsement_records[$agent_code])){
                            // $records[$agent_code] = array_merge($records[$agent_code], (array) $post_endorsement_records[$agent_code]);
                            $row = array_merge($row, (array) $post_endorsement_records[$agent_code]);
                        }

                        if(isset($pre_endorsement_records[$agent_code])){
                            // $records[$agent_code] = array_merge($records[$agent_code], (array) $pre_endorsement_records[$agent_code]);
                            $row = array_merge($row, (array) $pre_endorsement_records[$agent_code]);
                        }

                        $records[$agent_code]['arn_status'] = ($arr_arn_details[$agent_code]['arn_status']??0);
                        $records[$agent_code]['ARN Number'] = ($arr_arn_details[$agent_code]['ARN Number']??($row['agent_code']??''));
                        $records[$agent_code]['ARN Name'] = ($arr_arn_details[$agent_code]['ARN Name']??'');
                        $records[$agent_code]['ARN Email'] = ($arr_arn_details[$agent_code]['ARN Email']??'');
                        $records[$agent_code]['ARN Contact Number'] = ($arr_arn_details[$agent_code]['ARN Contact Number']??'');
                        $records[$agent_code]['Is SAMCOMF Partner?'] = ($arr_arn_details[$agent_code]['Is SAMCOMF Partner?']??(empty($records[$agent_code]['ARN Number'])?'No':'ARN not found'));
                        $records[$agent_code]['BDM Name'] = ($arr_arn_details[$agent_code]['BDM Name']??'');
                        $records[$agent_code]['BDM Contact Number'] = ($arr_arn_details[$agent_code]['BDM Contact Number']??'');
                        $records[$agent_code]['Reporting Name'] = ($arr_arn_details[$agent_code]['Reporting Name']??'');
                        $records[$agent_code]['Reporting Email'] = ($arr_arn_details[$agent_code]['Reporting Email']??'');
                        $records[$agent_code]['Reporting Contact Number'] = ($arr_arn_details[$agent_code]['Reporting Contact Number']??'');
                        $records[$agent_code]['Reporting Designation'] = ($arr_arn_details[$agent_code]['Reporting Designation']??'');
                        foreach(array_keys($arr_record_headings) as $heading_key){
                            if(!isset($row[$heading_key]) || (isset($row[$heading_key]) && !is_numeric($row[$heading_key]))){
                                $row[$heading_key] = 0;
                            }
                            $records[$agent_code][$heading_key] = $row[$heading_key];
                            $records[$agent_code][$heading_key] = number_format($records[$agent_code][$heading_key], 2, '.', '');
                        }
                        unset($row, $heading_key);
                    // }
                }
                unset($agent_code);
            }

            unset($post_endorsement_records, $pre_endorsement_records, $post_endorsement_select_conditions, $pre_endorsement_select_conditions);
            unset($arr_unique_agent_codes, $scheme_master_records);
        }
        catch(Exception $e){
        }
        return array('records' => $records, 'record_headings' => $arr_record_headings);
    }

    public static function get_event_analytics_nfo_scheme_road_shows_summary($input_arr = array()){
        $to_date = '2022-10-30 00:00:00';
        $current_date = date("Y-m-d 00:00:00");
        if(strtotime($current_date) < strtotime($to_date)){
            $to_date = $current_date;
        }

        $records = DB::select("select 
  ed.*, 
  sum(
    CASE WHEN ed.status = 2 THEN 1 ELSE 0 END
  ) AS `Total Empanelled from Invite List`, 
  sum(
    CASE WHEN ed.registered_status = 1 
    and ed.status = 2 THEN 1 ELSE 0 END
  ) AS Total_Empanelled_from_Registered, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 THEN 1 ELSE 0 END
  ) AS `Total Empanelled from Attended List`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 THEN ed.fcrg_aum ELSE 0 END
  ) AS `Contributed to Flexi from Attended aum`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 THEN ed.onrg_aum ELSE 0 END
  ) AS `Contributed To Liquid From Attended aum`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and ed.fcrg_aum > 0 THEN 1 ELSE 0 END
  ) AS `Contributed to Flexi from Attended count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and ed.onrg_aum > 0 THEN 1 ELSE 0 END
  ) AS `Contributed To Liquid From Attended count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 THEN ed.elrg_aum ELSE 0 END
  ) AS `Contributed To ELSS From Attended aum`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and ed.elrg_aum > 0 THEN 1 ELSE 0 END
  ) AS `Contributed To ELSS From Attended count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and ed.fcrg_aum <= 0 
    and ed.onrg_aum <= 0 
    and ed.elrg_aum <= 0 THEN 1 ELSE 0 END
  ) AS `Attended and Non Active`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.fcrg_aum > 0 
      or ed.onrg_aum > 0 
      or ed.elrg_aum > 0
    ) THEN 1 ELSE 0 END
  ) AS `Attended and Active`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.fcrg_aum > 0 
      or ed.onrg_aum > 0
    ) THEN 1 ELSE 0 END
  ) AS `Attended and Active (F+L)`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.fcrg_aum <= 0 
      and ed.onrg_aum <= 0
    ) 
    and ed.elrg_aum > 0 THEN 1 ELSE 0 END
  ) AS `Attended Em Unique to ELSS`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and dm.project_focus = 'yes' THEN 1 ELSE 0 END
  ) AS `project focus count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and dm.project_emerging_stars = 'yes' THEN 1 ELSE 0 END
  ) AS `project emerging stars count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and dm.project_green_shoots = 'yes' THEN 1 ELSE 0 END
  ) AS `project green shoots count` 
from 
  (
    select 
      ed.*, 
      IFNULL(elss_aum.elrg_aum, 0) as elrg_aum 
    from 
      (
        select 
          ed.*, 
          IFNULL(aum.fcrg_aum, 0) as fcrg_aum, 
          IFNULL(aum.onrg_aum, 0) as onrg_aum 
        from 
          (
            select 
              ed.* 
            from 
              (
                select 
                  ed.*, 
                  u.status 
                from 
                  (
                    SELECT 
                      e.`event_city` AS City, 
                      e.`event_date` AS `Event Date`, 
                      e.`invite_count` AS Invited, 
                      e.`registered_count` AS `Registered`, 
                      e.`attendance_count` AS Attended, 
                      e.`id`, 
                      ec.registered_status, 
                      ec.attendance, 
                      (
                        TRIM(
                          REGEXP_REPLACE(
                            TRIM(
                              REGEXP_REPLACE(
                                TRIM(
                                  REGEXP_REPLACE(ec.arn, 'ARN', '')
                                ), 
                                'AR-', 
                                ''
                              )
                            ), 
                            '-', 
                            ''
                          )
                        ) + 0
                      ) as arn 
                    FROM 
                      samcomf_scom_db.`sm_ep_events` e, 
                      samcomf_scom_db.sm_ep_events_clients ec 
                    WHERE 
                      e.id = ec.event_id 
                      AND e.`event_date` >='".$to_date."'
                      AND e.`event_date` <='".$current_date."'
                      AND e.`event_name` != 'Samco Mutual Fund Closed-door Meetup Test'
                  ) as ed 
                  left join samcomf.user_account u on ed.arn COLLATE utf8mb4_0900_ai_ci = u.arn
              ) as ed
          ) as ed 
          left join (
            SELECT 
              a.agent_code as arn, 
              a.scheme_code, 
              sum(
                CASE WHEN a.scheme_code = 'FCRG' THEN a.scheme_aum ELSE 0 END
              ) AS fcrg_aum, 
              sum(
                CASE WHEN a.scheme_code = 'ONRG' THEN a.scheme_aum ELSE 0 END
              ) AS onrg_aum 
            FROM 
              (
                SELECT 
                  agent_code, 
                  scheme_code, 
                  SUM(available_units) AS available_units, 
                  (
                    SUM(available_units) * scheme_master_details.nav
                  ) AS scheme_aum 
                FROM 
                  (
                    SELECT 
                      agent_code, 
                      scheme_code, 
                      SUM(
                        CASE WHEN (purred = 'R') THEN (
                          IFNULL(units, 0) * -1
                        ) ELSE IFNULL(units, 0) END
                      ) AS available_units 
                    FROM 
                      samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final 
                    WHERE 
                      agent_code NOT IN ('0') 
                    GROUP BY 
                      agent_code, 
                      scheme_code
                  ) AS arn_wise_units 
                  INNER JOIN samcomf_investor_db.scheme_master_details ON (
                    arn_wise_units.scheme_code = scheme_master_details.RTA_Scheme_Code
                  ) 
                GROUP BY 
                  agent_code, 
                  scheme_code
              ) AS a 
            GROUP BY 
              a.agent_code
          ) as aum on ed.arn COLLATE utf8mb4_0900_ai_ci = aum.arn
      ) as ed 
      left join (
        SELECT 
          agent_code as arn, 
          sum(amt) as elrg_aum 
        FROM 
          samcomf_investor_db.`kfintechTableTransactionDetails` 
        WHERE 
          (TRIM(`scheme`) = 'el' OR TRIM(`dd_tscheme`) = 'el') 
          AND `amt` > '0' 
          AND `agent_code` IS NOT NULL 
          AND `agent_code` != '0' 
        group by 
          agent_code
      ) as elss_aum on ed.arn COLLATE utf8mb4_0900_ai_ci = elss_aum.arn
  ) as ed 
  left join drm_distributor_master dm on ed.arn COLLATE utf8mb4_0900_ai_ci = dm.arn 
group by 
  ed.id", array(":event_start_date" => '2022-10-30', ":event_end_date" => '2022-12-05', ":nfo_end_date" => $to_date));
        return $records;
    }

public static function getInflow_Order()
   {
                $to_date = date('Y-m-d '.'00:00:00');
                //$to_date = date('2021-01-01'.'00:00:00');
                $current_date = date("Y-m-d");
                $records = DB::connection('invdb')->select("select 
                                              ed.*, 
                                              sum(Lumpsumamt) as totalumpsumamt, 
                                              sum(Sipamt) as totalsipamt, 
                                              sum(stpamt) as totalstpamt, 
                                              sum(switchamt) as totalswitchamt 
                                            from 
                                              (
                                                SELECT 
                                                  smc.Scheme as namescheme, 
                                                  IFNULL(
                                                    tp.type_of_transaction, kft.trtype
                                                  ) AS transaction_type, 
                                                  sum(kft.amt) as amt, 
                                                  sum(
                                                    CASE WHEN IFNULL(
                                                      tp.type_of_transaction, kft.trtype
                                                    )= 'Lumpsum' 
                                                    and kft.purred = 'p' THEN kft.amt ELSE 0 END
                                                  ) as Lumpsumamt, 
                                                  sum(
                                                    CASE WHEN IFNULL(
                                                      tp.type_of_transaction, kft.trtype
                                                    )= 'SIP' 
                                                    and kft.purred = 'p' THEN kft.amt ELSE 0 END
                                                  ) as Sipamt, 
                                                  sum(
                                                    CASE WHEN (
                                                      IFNULL(
                                                        tp.type_of_transaction, kft.trtype
                                                      )= 'STP' 
                                                      and CONCAT(
                                                        TRIM(kft.dd_tscheme), 
                                                        TRIM(kft.dd_tplan)
                                                      )= smc.RTA_Scheme_Code
                                                    ) THEN kft.amt ELSE 0 END
                                                  ) as stpamt, 
                                                  sum(
                                                    CASE WHEN (
                                                      IFNULL(
                                                        tp.type_of_transaction, kft.trtype
                                                      )= 'Switch' 
                                                      and CONCAT(
                                                        TRIM(kft.dd_tscheme), 
                                                        TRIM(kft.dd_tplan)
                                                      )= smc.RTA_Scheme_Code
                                                    ) THEN kft.amt ELSE 0 END
                                                  ) as switchamt, 
                                                  smc.Scheme_Code as scheme, 
                                                  kft.pln, 
                                                  kft.dd_tscheme, 
                                                  kft.dd_tplan, 
                                                  kft.trtype, 
                                                  kft.purred, 
                                                  tp.type_of_transaction 
                                                FROM 
                                                  `kfintechTableTransactionDetails` as kft 
                                                  left join transaction_type as tp on tp.tm_trtype = kft.trtype 
                                                  left join scheme_master as smc on (
                                                    smc.RTA_Scheme_Code = CONCAT(
                                                      TRIM(kft.scheme), 
                                                      TRIM(kft.pln)
                                                    ) 
                                                    or CONCAT(
                                                      TRIM(kft.dd_tscheme), 
                                                      TRIM(kft.dd_tplan)
                                                    )= smc.RTA_Scheme_Code
                                                  ) 
                                                where 
                                                  trdate >='".$to_date."' and trdate <='".$current_date."'
                                                GROUP BY 
                                                  smc.Scheme
                                                ORDER BY 
                                                  smc.Scheme
                                              ) as ed where ed.scheme IS NOT NULL and ed.scheme !=''
                                            group by 
                                              ed.scheme
                                            ");
      return json_decode(json_encode($records),true);
    }
public static function getOutflow_Order()
   {
                $to_date = date('Y-m-d '.'00:00:00');
                //$to_date = date('2021-01-01'.'00:00:00');
                $current_date = date("Y-m-d");
                $records = DB::connection('invdb')->select("select 
                                          ed.*, 
                                          sum(Lumpsumamt) as totalumpsumamt, 
                                          sum(Sipamt) as totalsipamt, 
                                          sum(stpamt) as totalstpamt, 
                                          sum(switchamt) as totalswitchamt 
                                        from 
                                          (
                                            SELECT 
                                              smc.Scheme as namescheme, 
                                              IFNULL(
                                                tp.type_of_transaction, kft.trtype
                                              ) AS transaction_type, 
                                              sum(kft.amt) as amt, 
                                              sum(
                                                CASE WHEN IFNULL(
                                                  tp.type_of_transaction, kft.trtype
                                                ) IN ('Redemption', 'SWP','Lumpsum') 
                                                and kft.purred != 'P' THEN kft.amt ELSE 0 END
                                              ) as Lumpsumamt, 
                                              sum(
                                                CASE WHEN IFNULL(
                                                  tp.type_of_transaction, kft.trtype
                                                )= 'SIP' 
                                                and kft.purred in('z', 'c', 'r') THEN kft.amt ELSE 0 END
                                              ) as Sipamt, 
                                              sum(
                                                CASE WHEN (
                                                  IFNULL(
                                                    tp.type_of_transaction, kft.trtype
                                                  )= 'STP' 
                                                  and CONCAT(
                                                    TRIM(kft.scheme), 
                                                    TRIM(kft.pln)
                                                  )= smc.RTA_Scheme_Code
                                                ) THEN kft.amt ELSE 0 END
                                              ) as stpamt, 
                                              sum(
                                                CASE WHEN (
                                                  IFNULL(
                                                    tp.type_of_transaction, kft.trtype
                                                  )= 'Switch' 
                                                  and CONCAT(
                                                    TRIM(kft.scheme), 
                                                    TRIM(kft.pln)
                                                  )= smc.RTA_Scheme_Code
                                                ) THEN kft.amt ELSE 0 END
                                              ) as switchamt, 
                                              smc.Scheme_Code as scheme,
                                              kft.pln, 
                                              kft.dd_tscheme, 
                                              kft.dd_tplan, 
                                              kft.trtype, 
                                              kft.purred, 
                                              tp.type_of_transaction 
                                            FROM 
                                              `kfintechTableTransactionDetails` as kft 
                                              left join transaction_type as tp on tp.tm_trtype = kft.trtype 
                                              left join scheme_master as smc on (
                                                smc.RTA_Scheme_Code = CONCAT(
                                                  TRIM(kft.scheme), 
                                                  TRIM(kft.pln)
                                                ) 
                                                or CONCAT(
                                                  TRIM(kft.dd_tscheme), 
                                                  TRIM(kft.dd_tplan)
                                                )= smc.RTA_Scheme_Code
                                              ) 
                                            where 
                                              trdate >='".$to_date."' and trdate <='".$current_date."' 
                                            GROUP BY 
                                              smc.Scheme 
                                            ORDER BY 
                                              smc.Scheme
                                          ) as ed 
                                        where 
                                          ed.scheme IS NOT NULL 
                                          and ed.scheme != '' 
                                        group by 
                                          ed.scheme
                                        ");
      return json_decode(json_encode($records),true);
    }
 public static function getNetflow_Order()
   {
                $to_date = date('Y-m-d '.'00:00:00');
                //$to_date = date('2021-01-01'.'00:00:00');
                $current_date = date("Y-m-d");
                $records = DB::connection('invdb')->select("SELECT 
                                          scheme_master.Scheme_Code, 
                                          SUM(
                                            CASE WHEN (
                                              b.type_of_transaction IN ('Lumpsum','Redemption') OR a.trtype IN ('SWP')
                                            ) THEN IFNULL(
                                              CASE WHEN(
                                                a.purred IN ('R', 'Z', 'C')
                                              ) THEN (a.amt * -1) ELSE IFNULL(a.amt, 0) END, 
                                              0
                                            ) ELSE 0 END
                                          ) AS lumpsum_amount, 
                                          SUM(
                                            CASE WHEN (b.type_of_transaction = 'SIP') THEN IFNULL(
                                              CASE WHEN(
                                                a.purred IN ('R', 'Z', 'C')
                                              ) THEN (a.amt * -1) ELSE IFNULL(a.amt, 0) END, 
                                              0
                                            ) ELSE 0 END
                                          ) AS sip_amount, 
                                          SUM(
                                            CASE WHEN(
                                              b.type_of_transaction = 'STP' 
                                              OR a.trtype IN ('STP')
                                            ) THEN CASE WHEN(
                                              CONCAT(
                                                TRIM(a.dd_tscheme), 
                                                TRIM(a.dd_tplan)
                                              ) = scheme_master.RTA_Scheme_Code
                                            ) THEN IFNULL(a.amt, 0) WHEN(
                                              CONCAT(
                                                TRIM(a.scheme), 
                                                TRIM(a.pln)
                                              ) = scheme_master.RTA_Scheme_Code
                                            ) THEN IFNULL(a.amt, 0) * -1 ELSE 0 END ELSE 0 END
                                          ) AS stp_amount, 
                                            SUM(
                                            CASE WHEN(
                                              b.type_of_transaction = 'Switch' 
                                              OR a.trtype IN ('Switch')
                                            ) THEN CASE WHEN(
                                              CONCAT(
                                                TRIM(a.dd_tscheme), 
                                                TRIM(a.dd_tplan)
                                              ) = scheme_master.RTA_Scheme_Code
                                            ) THEN IFNULL(a.amt, 0) WHEN(
                                              CONCAT(
                                                TRIM(a.scheme), 
                                                TRIM(a.pln)
                                              ) = scheme_master.RTA_Scheme_Code
                                            ) THEN IFNULL(a.amt, 0) * -1 ELSE 0 END ELSE 0 END
                                          ) AS switchamount
                                        FROM 
                                          kfintechTableTransactionDetails AS a 
                                          LEFT JOIN samcomf_investor_db.transaction_type AS b ON (a.trtype = b.tm_trtype) 
                                          INNER JOIN scheme_master ON (
                                            CONCAT(
                                              TRIM(a.scheme), 
                                              TRIM(a.pln)
                                            ) = scheme_master.RTA_Scheme_Code 
                                            OR CONCAT(
                                              TRIM(a.dd_tscheme), 
                                              TRIM(a.dd_tplan)
                                            ) = scheme_master.RTA_Scheme_Code
                                          ) 
                                        WHERE 
                                          a.trdate >='".$to_date."' and a.trdate <='".$current_date."'
                                        GROUP BY 
                                          scheme_master.Scheme_Code");
      return json_decode(json_encode($records),true);
    }
 public static function get_event_analytics_nfo_scheme_road_shows_summaryflexcap($input_arr = array()){
        $to_date = '2022-10-30 00:00:00';
        $current_date = date("Y-m-d 00:00:00");
        if(strtotime($current_date) < strtotime($to_date)){
            $to_date = $current_date;
        }

        $records = DB::select("select 
  ed.*, 
  sum(
    CASE WHEN ed.status = 2 THEN 1 ELSE 0 END
  ) AS `Total Empanelled from Invite List`, 
  sum(
    CASE WHEN ed.registered_status = 1 
    and ed.status = 2 THEN 1 ELSE 0 END
  ) AS Total_Empanelled_from_Registered, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 THEN 1 ELSE 0 END
  ) AS `Total Empanelled from Attended List`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 THEN ed.fcrg_aum ELSE 0 END
  ) AS `Contributed to Flexi from Attended aum`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 THEN ed.onrg_aum ELSE 0 END
  ) AS `Contributed To Liquid From Attended aum`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and ed.fcrg_aum > 0 THEN 1 ELSE 0 END
  ) AS `Contributed to Flexi from Attended count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and ed.onrg_aum > 0 THEN 1 ELSE 0 END
  ) AS `Contributed To Liquid From Attended count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 THEN ed.elrg_aum ELSE 0 END
  ) AS `Contributed To ELSS From Attended aum`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and ed.elrg_aum > 0 THEN 1 ELSE 0 END
  ) AS `Contributed To ELSS From Attended count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and ed.fcrg_aum <= 0 
    and ed.onrg_aum <= 0 
    and ed.elrg_aum <= 0 THEN 1 ELSE 0 END
  ) AS `Attended and Non Active`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.fcrg_aum > 0 
      or ed.onrg_aum > 0 
      or ed.elrg_aum > 0
    ) THEN 1 ELSE 0 END
  ) AS `Attended and Active`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.fcrg_aum > 0 
      or ed.onrg_aum > 0
    ) THEN 1 ELSE 0 END
  ) AS `Attended and Active (F+L)`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.fcrg_aum <= 0 
      and ed.onrg_aum <= 0
    ) 
    and ed.elrg_aum > 0 THEN 1 ELSE 0 END
  ) AS `Attended Em Unique to ELSS`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and dm.project_focus = 'yes' THEN 1 ELSE 0 END
  ) AS `project focus count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and dm.project_emerging_stars = 'yes' THEN 1 ELSE 0 END
  ) AS `project emerging stars count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and dm.project_green_shoots = 'yes' THEN 1 ELSE 0 END
  ) AS `project green shoots count` ,
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.equity_hybrid_aum > 0
    ) THEN 1 ELSE 0 END
  ) AS `Unique Active in (Equity + Hybrid)`,
sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.debt_aum > 0
    ) THEN 1 ELSE 0 END
  ) AS `Unique Active in Liquid`,

CASE  WHEN ed.attendance = 1 
    and ed.status = 2 
    and (ed.fcrg_aum > 0) THEN 'Yes' ELSE 'NO' END
  AS `Active in Flexi`,
CASE  WHEN ed.attendance = 1 
    and ed.status = 2 
    and (ed.onrg_aum > 0) THEN 'Yes' ELSE 'NO' END
  AS `Active in Liquid`,
CASE  WHEN ed.attendance = 1 
    and ed.status = 2 
    and (ed.elrg_aum > 0) THEN 'Yes' ELSE 'NO' END
  AS `Active in ELSS`
from 
  (
    
        select 
          ed.*, 
          IFNULL(aum.fcrg_aum, 0) as fcrg_aum, 
          IFNULL(aum.onrg_aum, 0) as onrg_aum ,
          IFNULL(aum.elrg_aum, 0) as elrg_aum,
          IFNULL(aum.equity_hybrid_aum, 0) as equity_hybrid_aum,
          IFNULL(aum.debt_aum, 0) as debt_aum
        from 
          (
            select 
              ed.* 
            from 
              (
                select 
                  ed.*, 
                  u.status 
                from 
                  (
                    SELECT 
                      e.`event_city` AS City, 
                      e.`event_date` AS `Event Date`, 
                      e.`invite_count` AS Invited, 
                      e.`registered_count` AS `Registered`, 
                      e.`attendance_count` AS Attended, 
                      e.`id`, 
                      ec.registered_status, 
                      ec.attendance, 
                      (
                        TRIM(
                          REGEXP_REPLACE(
                            TRIM(
                              REGEXP_REPLACE(
                                TRIM(
                                  REGEXP_REPLACE(ec.arn, 'ARN', '')
                                ), 
                                'AR-', 
                                ''
                              )
                            ), 
                            '-', 
                            ''
                          )
                        ) + 0
                      ) as arn 
                    FROM 
                      samcomf_scom_db.`sm_ep_events` e, 
                      samcomf_scom_db.sm_ep_events_clients ec 
                    WHERE 
                      e.id = ec.event_id 
                      AND e.`event_date` >='2021-10-15'
                      AND e.`event_date` <='2022-01-17'
                      AND e.`event_name` != 'Samco Mutual Fund Closed-door Meetup Test'
                  ) as ed 
                  left join samcomf.user_account u on ed.arn COLLATE utf8mb4_0900_ai_ci = u.arn
              ) as ed
          ) as ed 
          left join (
            SELECT 
              a.agent_code as arn, 
              a.scheme_code, 
              sum(
                CASE WHEN a.scheme_code = 'FCRG' THEN a.scheme_aum ELSE 0 END
              ) AS fcrg_aum, 
              sum(
                CASE WHEN a.scheme_code = 'ONRG' THEN a.scheme_aum ELSE 0 END
              ) AS onrg_aum,
              sum(
                CASE WHEN a.scheme_code = 'ELRG' THEN a.scheme_aum ELSE 0 END
              ) AS elrg_aum,
              sum(CASE WHEN a.Scheme_Type IN ('Equity', 'Hybrid') THEN a.scheme_aum ELSE 0 END) AS equity_hybrid_aum, 
              sum(CASE WHEN a.Scheme_Type IN ('Debt') THEN a.scheme_aum ELSE 0 END) AS debt_aum 
            FROM 
              (
                SELECT 
                  agent_code, 
                  arn_wise_units.scheme_code,
                  Scheme_Type, 
                  SUM(available_units) AS available_units, 
                  (
                    SUM(available_units) * scheme_master_details.nav
                  ) AS scheme_aum 
                FROM 
                  (
                    SELECT 
                      agent_code, 
                      scheme_code,
                      SUM(
                        CASE WHEN (purred = 'R') THEN (
                          IFNULL(units, 0) * -1
                        ) ELSE IFNULL(units, 0) END
                      ) AS available_units 
                    FROM 
                      samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final 
                    WHERE 
                      agent_code NOT IN ('0') 
                    GROUP BY 
                      agent_code, 
                      scheme_code
                  ) AS arn_wise_units 
                  INNER JOIN samcomf_investor_db.scheme_master_details ON (
                    arn_wise_units.scheme_code = scheme_master_details.RTA_Scheme_Code)
                     INNER JOIN samcomf_investor_db.scheme_master ON (
                    arn_wise_units.scheme_code = scheme_master.RTA_Scheme_Code)
                GROUP BY 
                  agent_code, 
                  scheme_code
              ) AS a 
            GROUP BY 
              a.agent_code
          ) as aum on ed.arn COLLATE utf8mb4_0900_ai_ci = aum.arn
      ) as ed 
  left join drm_distributor_master dm on ed.arn COLLATE utf8mb4_0900_ai_ci = dm.arn 
group by 
  ed.id");
        return $records;
    }
    public static function get_event_analytics_nfo_scheme_road_shows_summary_overnight($input_arr = array()){
        $to_date = '2022-10-30 00:00:00';
        $current_date = date("Y-m-d 00:00:00");
        if(strtotime($current_date) < strtotime($to_date)){
            $to_date = $current_date;
        }

        $records = DB::select("select 
  ed.*, 
  sum(
    CASE WHEN ed.status = 2 THEN 1 ELSE 0 END
  ) AS `Total Empanelled from Invite List`, 
  sum(
    CASE WHEN ed.registered_status = 1 
    and ed.status = 2 THEN 1 ELSE 0 END
  ) AS Total_Empanelled_from_Registered, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 THEN 1 ELSE 0 END
  ) AS `Total Empanelled from Attended List`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 THEN ed.fcrg_aum ELSE 0 END
  ) AS `Contributed to Flexi from Attended aum`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 THEN ed.onrg_aum ELSE 0 END
  ) AS `Contributed To Liquid From Attended aum`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and ed.fcrg_aum > 0 THEN 1 ELSE 0 END
  ) AS `Contributed to Flexi from Attended count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and ed.onrg_aum > 0 THEN 1 ELSE 0 END
  ) AS `Contributed To Liquid From Attended count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 THEN ed.elrg_aum ELSE 0 END
  ) AS `Contributed To ELSS From Attended aum`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and ed.elrg_aum > 0 THEN 1 ELSE 0 END
  ) AS `Contributed To ELSS From Attended count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and ed.fcrg_aum <= 0 
    and ed.onrg_aum <= 0 
    and ed.elrg_aum <= 0 THEN 1 ELSE 0 END
  ) AS `Attended and Non Active`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.fcrg_aum > 0 
      or ed.onrg_aum > 0 
      or ed.elrg_aum > 0
    ) THEN 1 ELSE 0 END
  ) AS `Attended and Active`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.fcrg_aum > 0 
      or ed.onrg_aum > 0
    ) THEN 1 ELSE 0 END
  ) AS `Attended and Active (F+L)`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.fcrg_aum <= 0 
      and ed.onrg_aum <= 0
    ) 
    and ed.elrg_aum > 0 THEN 1 ELSE 0 END
  ) AS `Attended Em Unique to ELSS`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and dm.project_focus = 'yes' THEN 1 ELSE 0 END
  ) AS `project focus count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and dm.project_emerging_stars = 'yes' THEN 1 ELSE 0 END
  ) AS `project emerging stars count`, 
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and dm.project_green_shoots = 'yes' THEN 1 ELSE 0 END
  ) AS `project green shoots count` ,
  sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.equity_hybrid_aum > 0
    ) THEN 1 ELSE 0 END
  ) AS `Unique Active in (Equity + Hybrid)`,
sum(
    CASE WHEN ed.attendance = 1 
    and ed.status = 2 
    and (
      ed.debt_aum > 0
    ) THEN 1 ELSE 0 END
  ) AS `Unique Active in Liquid`,

CASE  WHEN ed.attendance = 1 
    and ed.status = 2 
    and (ed.fcrg_aum > 0) THEN 'Yes' ELSE 'NO' END
  AS `Active in Flexi`,
CASE  WHEN ed.attendance = 1 
    and ed.status = 2 
    and (ed.onrg_aum > 0) THEN 'Yes' ELSE 'NO' END
  AS `Active in Liquid`,
CASE  WHEN ed.attendance = 1 
    and ed.status = 2 
    and (ed.elrg_aum > 0) THEN 'Yes' ELSE 'NO' END
  AS `Active in ELSS`
from 
  (
    
        select 
          ed.*, 
          IFNULL(aum.fcrg_aum, 0) as fcrg_aum, 
          IFNULL(aum.onrg_aum, 0) as onrg_aum ,
          IFNULL(aum.elrg_aum, 0) as elrg_aum,
          IFNULL(aum.equity_hybrid_aum, 0) as equity_hybrid_aum,
          IFNULL(aum.debt_aum, 0) as debt_aum
        from 
          (
            select 
              ed.* 
            from 
              (
                select 
                  ed.*, 
                  u.status 
                from 
                  (
                    SELECT 
                      e.`event_city` AS City, 
                      e.`event_date` AS `Event Date`, 
                      e.`invite_count` AS Invited, 
                      e.`registered_count` AS `Registered`, 
                      e.`attendance_count` AS Attended, 
                      e.`id`, 
                      ec.registered_status, 
                      ec.attendance, 
                      (
                        TRIM(
                          REGEXP_REPLACE(
                            TRIM(
                              REGEXP_REPLACE(
                                TRIM(
                                  REGEXP_REPLACE(ec.arn, 'ARN', '')
                                ), 
                                'AR-', 
                                ''
                              )
                            ), 
                            '-', 
                            ''
                          )
                        ) + 0
                      ) as arn 
                    FROM 
                      samcomf_scom_db.`sm_ep_events` e, 
                      samcomf_scom_db.sm_ep_events_clients ec 
                    WHERE 
                      e.id = ec.event_id 
                      AND e.`event_date` >'2022-01-17'
                      AND e.`event_date` <='2022-09-28'
                      AND e.`event_name` != 'Samco Mutual Fund Closed-door Meetup Test'
                  ) as ed 
                  left join samcomf.user_account u on ed.arn COLLATE utf8mb4_0900_ai_ci = u.arn
              ) as ed
          ) as ed 
          left join (
            SELECT 
              a.agent_code as arn, 
              a.scheme_code, 
              sum(
                CASE WHEN a.scheme_code = 'FCRG' THEN a.scheme_aum ELSE 0 END
              ) AS fcrg_aum, 
              sum(
                CASE WHEN a.scheme_code = 'ONRG' THEN a.scheme_aum ELSE 0 END
              ) AS onrg_aum,
              sum(
                CASE WHEN a.scheme_code = 'ELRG' THEN a.scheme_aum ELSE 0 END
              ) AS elrg_aum,
              sum(CASE WHEN a.Scheme_Type IN ('Equity', 'Hybrid') THEN a.scheme_aum ELSE 0 END) AS equity_hybrid_aum, 
              sum(CASE WHEN a.Scheme_Type IN ('Debt') THEN a.scheme_aum ELSE 0 END) AS debt_aum 
            FROM 
              (
                SELECT 
                  agent_code, 
                  arn_wise_units.scheme_code,
                  Scheme_Type, 
                  SUM(available_units) AS available_units, 
                  (
                    SUM(available_units) * scheme_master_details.nav
                  ) AS scheme_aum 
                FROM 
                  (
                    SELECT 
                      agent_code, 
                      scheme_code,
                      SUM(
                        CASE WHEN (purred = 'R') THEN (
                          IFNULL(units, 0) * -1
                        ) ELSE IFNULL(units, 0) END
                      ) AS available_units 
                    FROM 
                      samcomf_investor_db.kfintec_Postendorsement_TransactionDetails_final 
                    WHERE 
                      agent_code NOT IN ('0') 
                    GROUP BY 
                      agent_code, 
                      scheme_code
                  ) AS arn_wise_units 
                  INNER JOIN samcomf_investor_db.scheme_master_details ON (
                    arn_wise_units.scheme_code = scheme_master_details.RTA_Scheme_Code)
                     INNER JOIN samcomf_investor_db.scheme_master ON (
                    arn_wise_units.scheme_code = scheme_master.RTA_Scheme_Code)
                GROUP BY 
                  agent_code, 
                  scheme_code
              ) AS a 
            GROUP BY 
              a.agent_code
          ) as aum on ed.arn COLLATE utf8mb4_0900_ai_ci = aum.arn
      ) as ed 
  left join drm_distributor_master dm on ed.arn COLLATE utf8mb4_0900_ai_ci = dm.arn 
group by 
  ed.id");
        return $records;
    }
public static function getdmlistemplement()
{
 $date=date('Y-m-d');
 $prev_date =date('Y-m-d', strtotime($date .' -1 day'));
 //$prev_date ='2020-01-01';
 //x($prev_date);
 $user_details = DB::table('user_account as arn_investment')
        ->leftJoin('drm_distributor_master as drm', 'arn_investment.ARN', '=', 'drm.ARN')
        ->leftJoin('users as bdm', array(array('drm.direct_relationship_user_id','=','bdm.id')),'bdm.is_drm_user', '1')
        ->leftJoin('users_details as bdm_details','bdm.id','=','bdm_details.user_id')
        ->leftJoin('users as reporting','bdm_details.reporting_to','=','reporting.id')
        ->leftJoin('users_details as reporting_details','reporting.id','=','reporting_details.user_id')
        ->select('arn_investment.ARN','arn_investment.name AS arn_name','arn_investment.email as email_amfi AS arn_email',DB::Raw('IFNULL(arn_investment.mobile, arn_investment.mobile_amfi) as arn_mobile'),'bdm.name AS bdm_name','bdm.email AS bdm_email','bdm_details.mobile_number AS bdm_mobile_number','reporting.name AS reporting_name','reporting.email AS reporting_email','reporting_details.mobile_number AS reporting_mobile_number','arn_investment.form_status','arn_investment.status','arn_investment.from_site','arn_investment.created_at as created_at')
        ->where([['arn_investment.created_at','>=',$prev_date.' 00:00:00'],['arn_investment.created_at','<=',$prev_date.' 23:23:59']])->get()->toArray();
        return json_decode(json_encode($user_details), true);
}
}

