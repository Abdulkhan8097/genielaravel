<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Exports\ArrayRecordsExport;
use App\Exports\ArrayRecordsWithMultipleSheetsExport;
use Illuminate\Support\Facades\Validator;
use \App\Models\MasterSipStpTransactionDetailsModel;
use Illuminate\Support\Facades\Crypt;
use App\Libraries\PhpMailer;

use DB;

class MasterSipStpTransactionDetailsController extends Controller
{
    protected $data_table_headings;
    protected $logged_in_user_roles_and_permissions, $flag_have_all_permissions, $flag_show_all_arn_data, $logged_in_user_id;
    public function __construct(){
        $this->middleware('auth');

        $this->data_table_headings = array('zone' => array('label' => 'Zone'),
            'branch' => array('label' => 'Branch'),
            'location' => array('label' => 'Location'),
            'ihno' => array('label' => 'Ihno'),
            'folio' => array('label' => 'Folio'),
            'investor_Name' => array('label' => 'Investor Name'),
            'registrationDate' => array('label' => 'RegistrationDate'),
            'start_Date' => array('label' => 'Start Date'),
            'end_Date' => array('label' => 'End Date'),
            'no_Of_Installments' => array('label' => 'No Of Installments'),
            'amount' => array('label' => 'Amount'),
            'scheme_code' => array('label' => 'Scheme Code'),
            'agentCode' => array('label' => 'AgentCode'),
            'agentName' => array('label' => 'AgentName'),
            'subbroker' => array('label' => ' Subbroker'),
            'scheme_Name' => array('label' => 'Scheme Name'),
            'pan' => array('label' => 'Pan'),
            'sipType' => array('label' => 'Sip Type'),
            'siP_Mode' => array('label' => 'Sip Mode'),
            'fund_Code' => array('label' => 'Fund Code'),
            'product_Code' => array('label' => 'Product Code'),
            'frequency' => array('label' => 'Frequency'),
            'trtype' => array('label' => 'Transaction Type'),
            'to_Scheme' => array('label' => 'To Scheme'),
            'to_Plan' => array('label' => 'To Plan'),
            'terminateDate' => array('label' => 'TerminateDate'),
            'status' => array('label' => 'Status'),
            'toProductCode' => array('label' => 'ToProductCode'),
            'toSchemeName' => array('label' => 'ToSchemeName'),
            'rejreason' => array('label' => 'Rejreason'),
            'umrncode' => array('label' => 'Umrncode'),
            'bankname' => array('label' => 'Bankname'),
            'bankacno' => array('label' => 'Bankacno'),
            'banktype' => array('label' => 'Banktype'),
            'bankifsc' => array('label' => 'Bankifsc'),
            'sipday' => array('label' => 'Sipday'),
            'bdm_name' => array('label' => 'BDM Name'),
            'reporting_name' => array('label' => 'Reporting Manager'),

        );

        // retrieving logged in user role and permission details
        $this->middleware(function ($request, $next) {
            $this->logged_in_user_roles_and_permissions = session('logged_in_user_roles_and_permissions');
            $this->logged_in_user_id = session('logged_in_user_id');
            $this->flag_have_all_permissions = false;
            if(isset($this->logged_in_user_roles_and_permissions['role_details']) && isset($this->logged_in_user_roles_and_permissions['role_details']['have_all_permissions']) && (intval($this->logged_in_user_roles_and_permissions['role_details']['have_all_permissions']) == 1)){
                $this->flag_have_all_permissions = true;
            }
            $this->flag_show_all_arn_data = false;
            if(isset($this->logged_in_user_roles_and_permissions['role_details']) && isset($this->logged_in_user_roles_and_permissions['role_details']['show_all_arn_data']) && (intval($this->logged_in_user_roles_and_permissions['role_details']['show_all_arn_data']) == 1)){
                $this->flag_show_all_arn_data = true;
            }
            return $next($request);
        });
    }

    public function index(Request $request){
        ini_set('memory_limit', '5048M');
        ini_set('max_execution_time', 0);

        if(count($request->all()) > 0){
            extract($request->all());

            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
            // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }
            $MasterData = MasterSipStpTransactionDetailsModel::getMasterSipStpTransactionDetailsDB($request->all());

            if(!$MasterData['records']->isEmpty()){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $MasterData['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $MasterData['records'];

                    // displaying data in DataTable format
                    echo json_encode($output_arr);
                }
                else{
                    // when exporting data, preparing HEADER ROW for an informative purpose
                    $csv_headers = array_column($this->data_table_headings, 'label');
                    // array_shift($csv_headers);
                    // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    $output_arr[] = $csv_headers;
                    foreach($MasterData['records'] as $key => $value){
                        $row = array();
                        foreach($this->data_table_headings as $field_name_key => $field_name_value){
                            // skipping unnecessary fields during exporting of records
                            if(in_array($field_name_key, array('action')) !== FALSE){
                                continue;
                            }

                            $row[$field_name_key] = '';
                            if(isset($value->$field_name_key)){
                                $row[$field_name_key] = $value->$field_name_key;
                            }
                        }
                        $output_arr[] = $row;
                        unset($field_name_key, $field_name_value);
                    }
                    unset($key, $value, $csv_headers);
                    // makes data available in a CSV file format to user
                    return \Excel::download(new ArrayRecordsExport($output_arr),'Master_SipStp_data_'. date('Ymd').'.xlsx');
                }
            }
            else{
                // coming here if no records are retrieved from MySQL table: distributor_master
                if($flag_export_data){
                    // as data is requested as an EXPORT action, so displaying message and closing the newly open window
                    ?><script>alert('No records found');window.close();</script><?php
                }
                else{
                    // displaying data in DataTable format
                    echo json_encode($output_arr);
                }
            }
        }
        else{
            $status_obj = MasterSipStpTransactionDetailsModel::getSipStpStpSatus();
            foreach($status_obj as $value){
                $status_arr[] = $value->status;
            }
            $data = array('data_table_headings' => $this->data_table_headings);
            $data['arr_status'] = $status_arr;
            $data['arr_frequency'] = config('constants.FREQUENCY');
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            return view('MasterSipStpTransactionReport/list')->with($data);
        }
    }

    public function getPredefinedSipStpReport(Request $request){
        extract($request->all());

        $report = array();
        if(isset($report_type) && $report_type == 'Bdm_Wise_Count_Of_RegisteredSip'){
            $csv_headers = array('BDM', 'Reporting Manager', 'Active Distributors', 'No. of SIP Applications', 'Consolidated SIP Amount','Qualifier','Ticket Size','0 – 24999','25K - 149999','> 150000');
            $exportData[] = $csv_headers;
            $sr_no = 1;
            $report = MasterSipStpTransactionDetailsModel::get_putinsip_bdm_wise_count_of_registered_sip();
            if(!empty($report))
            {
                foreach($report as $key => $value)
                {
                    $exportData1[$key][] = $value->bdm_name;
                    $exportData1[$key][] = $value->reporting_name;
                    $exportData1[$key][] = $value->active_distributors;
                    $exportData1[$key][] = $value->no_of_sip_applications;
                    $exportData1[$key][] = $value->consolidated_sip_amount;
                    $exportData1[$key][] = $value->qualifier;
                    $exportData1[$key][] = $value->ticket_size;
                    $exportData1[$key][] = $value->zero_to_twentyfive_thousand;
                    $exportData1[$key][] = $value->twentyfive_thousand_to_onelac_fiftythousand;
                    $exportData1[$key][] = $value->more_than_onelac_fiftythousand;
                }

                return \Excel::download(new ArrayRecordsExport(array_merge($exportData,$exportData1)), 'PutinSIP_BDM_Wise_Inflows_data_'. date('Ymd') .'.xlsx');
            }else{
                ?><script>alert('No records found');window.close();</script><?php
            }
        }

        if(isset($report_type) && $report_type == 'Amount_Wise_Count_Of_registered'){
            $report = array();
            $csv_headers = array('Category', 'No Of ARN Count');
            $exportData[] = $csv_headers;

            $report=MasterSipStpTransactionDetailsModel::get_putinsip_amount_wise_count_of_registered_sip();
            if(!empty($report))
            {
                foreach($report as $key => $value)
                {
                    $exportData1[$key][] = $value->amount_category_text;
                    $exportData1[$key][] = $value->no_of_registered_sip;
                }

                return \Excel::download(new ArrayRecordsExport(array_merge($exportData,$exportData1)), 'PutinSIP_MFD_Participation_data_'. date('Ymd') .'.xlsx');
            }else{
                ?><script>alert('No records found');window.close();</script><?php
            }
        }

        if(isset($report_type) && $report_type == 'Arn_wise_count_of_registered'){
            $report = array();
            $csv_headers = array('Agent Code','Agent Name','Agent Zone','BDM','Reporting Manager','No. of SIP Applications','Consolidated SIP Amount','Qualifier','Ticket Size','0 – 24999','25K - 149999','> 150000');
            $exportData[] = $csv_headers;

            $report = MasterSipStpTransactionDetailsModel::get_putinsip_arn_wise_count_of_registered_sip();
            if(!empty($report)){
                foreach($report as $key => $value)
                {
                    if(isset($value->consolidated_sip_amount) && $value->consolidated_sip_amount >= 150000){
                        $qualifier = 1;
                    }
                    else{
                        $qualifier = 0;
                    }

                    if(isset($value->consolidated_sip_amount) && $value->consolidated_sip_amount > 0 && $value->consolidated_sip_amount <= 24999 ){
                        $zero_to_twentyfive_thousand = 1;
                    }
                    else{
                        $zero_to_twentyfive_thousand = 0;
                    }

                    if(isset($value->consolidated_sip_amount) && $value->consolidated_sip_amount >= 25000 && $value->consolidated_sip_amount <= 149999 ){
                        $twentyfive_thousand_to_onelac_fiftythousand = 1;
                    }
                    else{
                        $twentyfive_thousand_to_onelac_fiftythousand = 0;
                    }

                    if(isset($value->consolidated_sip_amount) && $value->consolidated_sip_amount >= 150000){
                        $more_than_onelac_fiftythousand = 1;
                    }
                    else{
                        $more_than_onelac_fiftythousand = 0;
                    }
                    $exportData1[$key][] = $value->agentCode;
                    $exportData1[$key][] = $value->agentName;
                    $exportData1[$key][] = $value->arn_zone;
                    $exportData1[$key][] = $value->bdm_name;
                    $exportData1[$key][] = $value->reporting_name;
                    $exportData1[$key][] = $value->no_of_sip_applications;
                    $exportData1[$key][] = $value->consolidated_sip_amount;
                    $exportData1[$key][] = $qualifier;
                    $exportData1[$key][] = floor(($value->consolidated_sip_amount??0)/150000);
                    $exportData1[$key][] = $zero_to_twentyfive_thousand;
                    $exportData1[$key][] = $twentyfive_thousand_to_onelac_fiftythousand;
                    $exportData1[$key][] = $more_than_onelac_fiftythousand;
                }

                return \Excel::download(new ArrayRecordsExport(array_merge($exportData,$exportData1)), 'PutinSIP_Distributor_Wise_Inflows_data_'. date('Ymd') .'.xlsx');
            }
            else{
                ?><script>alert('No records found');window.close();</script><?php
            }
        }

        if(isset($report_type) && ($report_type == 'elss_nfo_period_distributor_wise_inflows')){
            $report = array();
            $csv_headers = array('ARN Number' => array('label' => 'ARN Number'),
                'ARN Name' => array('label' => 'ARN Name'),
                'ARN Email' => array('label' => 'ARN Email'),
                'ARN Contact Number' => array('label' => 'ARN Contact Number'),
                'BDM Name' => array('label' => 'Relationship Manager\'s Name'),
                'BDM Contact Number' => array('label' => 'Relationship Manager\'s Contact Number'),
                'Reporting Name' => array('label' => 'Reporting Name'),
                'Reporting Email' => array('label' => 'Reporting Email'),
                'Reporting Contact Number' => array('label' => 'Reporting Contact Number'),
                'Reporting Designation' => array('label' => 'Reporting Designation'),
                'overall_amount' => array('label' => 'Total Amount Contributed in Samco ELSS Fund NFO'),
                'autologin_url' => array('label' => 'Auto Login Dashboard Link'),
                'cobranded_link' => array('label' => 'Co-Branded Materials Section Link'),
                'open_url' => array('label' => 'Smart Transaction Link (ARN & EUIN embedded link)')
            );
            $exportData[] = $csv_headers;

            $report=MasterSipStpTransactionDetailsModel::get_elss_nfo_period_distributor_wise_inflows();
            $output_arr = array();
            $output_arr[] = array_column($csv_headers, 'label');
            if(!empty($report)){
                foreach($report as $key => $value){
                    $row = array();
                    foreach($csv_headers as $field_name_key => $field_name_value){
                        $row[$field_name_key] = '';
                        switch($field_name_key){
                            case 'autologin_url':
                            $row[$field_name_key] = env('SAMCOMF_PARTNERS_URL')."/autoLoginEmpanelURL/".Crypt::encryptString($row['ARN Number']);
                            break;
                            case 'cobranded_link':
                            $row[$field_name_key] = env('SAMCOMF_PARTNERS_URL')."/autoLoginEmpanelURL/".Crypt::encryptString($row['ARN Number']).'/co-branded-video';
                            break;
                            case 'open_url':
                            $row[$field_name_key] = env('SAMCOMF_STATIC_WEB_URL')."/d/register/".$row['ARN Number'];
                            if(isset($value->amfi_euin) && !empty($value->amfi_euin)){
                                $row[$field_name_key] .= '/'. $value->amfi_euin;
                            }
                            break;
                            default:
                            if(isset($value->$field_name_key)){
                                $row[$field_name_key] = $value->$field_name_key;
                            }
                        }
                    }
                    $output_arr[] = $row;
                    unset($field_name_key, $field_name_value);
                }

                if(count($output_arr) > 0){
                    return \Excel::download(new ArrayRecordsExport($output_arr), 'ELSS_Nfo_Period_Distributor_Wise_Inflows_Data_'. date('Ymd') .'.xlsx');
                }
                else{
                    ?><script>alert('No records found');window.close();</script><?php
                }
            }
            else{
                ?><script>alert('No records found');window.close();</script><?php
            }
        }

        if(isset($report_type) && ($report_type == 'investor_lead_and_registration_data_two')){
            $report = array();
            $csv_headers = array('Pan', 'Name' ,'Email' ,'Mobile' ,'Form Site','Whatsapp Optin','Ip Address','Broker id','Created At');
            $exportData[] = $csv_headers;

            $csv_headers1 = array('Pan', 'Name' ,'Email' ,'Mobile' ,'Form Site','Whatsapp Optin','Ip Address','Broker id','Created At','Order Count');
            $exportData_new[] = $csv_headers1;

            $exportData1 = array();
            $exportData2 = array();

            $report = MasterSipStpTransactionDetailsModel::get_investor_lead_and_registration_data_four_to_ten();

            if(is_array($report) && count($report) > 0)
            {
                if ((count($report['records_registrstion']) > 0) || count($report['records_leads']) > 0) {
                    if (is_array($report['records_registrstion']) && count($report['records_registrstion']) > 0) {
                        foreach($report['records_registrstion'] as $key => $value)
                        { 
                            $exportData1[$key][] = $value->pan;
                            $exportData1[$key][] = $value->name;
                            $exportData1[$key][] = $value->email;
                            $exportData1[$key][] = $value->mobile;
                            $exportData1[$key][] = $value->from_site;
                            $exportData1[$key][] = $value->whatsapp_optin;
                            $exportData1[$key][] = $value->ip_address;
                            $exportData1[$key][] = $value->broker_id;
                            $exportData1[$key][] = $value->created_at;
                        }
                    }

                    if (is_array($report['records_leads']) && count($report['records_leads']) > 0) {
                        foreach($report['records_leads'] as $key => $value)
                        { 
                            $exportData2[$key][] = $value->pan;
                            $exportData2[$key][] = $value->name;
                            $exportData2[$key][] = $value->email;
                            $exportData2[$key][] = $value->mobile;
                            $exportData2[$key][] = $value->from_site;
                            $exportData2[$key][] = $value->whatsapp_optin;
                            $exportData2[$key][] = $value->ip_address;
                            $exportData2[$key][] = $value->broker_id;
                            $exportData2[$key][] = $value->created_at;
                            $exportData2[$key][] = $value->order_count;
                        }
                    }

                    return \Excel::download(new ArrayRecordsWithMultipleSheetsExport(
                        array(
                            array('data' => array_merge($exportData,$exportData1), 'title' => 'Investor Registration Data', 'extra_params' => array('freeze_row' => '')),
                            array('data' => array_merge($exportData_new,$exportData2), 'title' => 'Investor Leads Without Orders', 'extra_params' => array('freeze_row' => '')),
                        )
                    ), 'Investor_Lead_registration_data_'. date('Ymd') .'.xlsx');
                }
                else{
                    ?><script>alert('No records found');window.close();</script><?php
                }
            }
            else{
                ?><script>alert('No records found');window.close();</script><?php
            }
        }

        if(isset($report_type) && ($report_type == 'investor_lead_and_registration_data')){
            $report = array();
            $csv_headers = array('Pan', 'Name' ,'Email' ,'Mobile' ,'Form Site','Whatsapp Optin','Ip Address','Broker id','Created At');
            $exportData[] = $csv_headers;

            $csv_headers1 = array('Pan', 'Name' ,'Email' ,'Mobile' ,'Form Site','Whatsapp Optin','Ip Address','Broker id','Created At','Order Count');
            $exportData_new[] = $csv_headers1;

            $exportData1 = array();
            $exportData2 = array();

            $report = MasterSipStpTransactionDetailsModel::get_investor_lead_and_registration_data_ten_to_four();

            if(is_array($report) && count($report) > 0)
            {
                if ((count($report['records_registrstion']) > 0) || count($report['records_leads']) > 0) {

                    if (is_array($report['records_registrstion']) && count($report['records_registrstion']) > 0) {
                        foreach($report['records_registrstion'] as $key => $value)
                        { 
                            $exportData1[$key][] = $value->pan;
                            $exportData1[$key][] = $value->name;
                            $exportData1[$key][] = $value->email;
                            $exportData1[$key][] = $value->mobile;
                            $exportData1[$key][] = $value->from_site;
                            $exportData1[$key][] = $value->whatsapp_optin;
                            $exportData1[$key][] = $value->ip_address;
                            $exportData1[$key][] = $value->broker_id;
                            $exportData1[$key][] = $value->created_at;
                        }
                    }

                    if (is_array($report['records_leads']) && count($report['records_leads']) > 0) {
                        foreach($report['records_leads'] as $key => $value)
                        { 
                            $exportData2[$key][] = $value->pan;
                            $exportData2[$key][] = $value->name;
                            $exportData2[$key][] = $value->email;
                            $exportData2[$key][] = $value->mobile;
                            $exportData2[$key][] = $value->from_site;
                            $exportData2[$key][] = $value->whatsapp_optin;
                            $exportData2[$key][] = $value->ip_address;
                            $exportData2[$key][] = $value->broker_id;
                            $exportData2[$key][] = $value->created_at;
                            $exportData2[$key][] = $value->order_count;
                        }
                    }

                    return \Excel::download(new ArrayRecordsWithMultipleSheetsExport(
                        array(
                            array('data' => array_merge($exportData,$exportData1), 'title' => 'Investor Registration Data', 'extra_params' => array('freeze_row' => '')),
                            array('data' => array_merge($exportData_new,$exportData2), 'title' => 'Investor Leads Without Orders', 'extra_params' => array('freeze_row' => '')),
                        )
                    ), 'Investor_Lead_registration_data_'. date('Ymd') .'.xlsx');
                }else{
                    ?><script>alert('No records found');window.close();</script><?php
                }
            }else{
                ?><script>alert('No records found');window.close();</script><?php
            }
        }

        if(isset($report_type) && ($report_type == 'event_analytics_nfo_scheme_road_shows')){
            $report = array();
            $csv_headers = array('Event City' => array('label' => 'Event City'),
                'Event Date' => array('label' => 'Event Date'),
                'ARN' => array('label' => 'ARN'),
                'ARN Name' => array('label' => 'ARN Name'),
                'ARN Email' => array('label' => 'ARN Email'),
                'ARN Number' => array('label' => 'ARN Number'),
                'BDM Name' => array('label' => 'BDM Name'),
                'BDM Number' => array('label' => 'BDM Number'),
                'BDM Email' => array('label' => 'BDM Email'),
                'Reporting Name' => array('label' => 'Reporting Name'),
                'Reporting Number' => array('label' => 'Reporting Number'),
                'Reporting Email' => array('label' => 'Reporting Email'),
                'Empanelled' => array('label' => 'Empanelled'),
                'Over All Active' => array('label' => 'Over All Active'),
                'Active in ELSS' => array('label' => 'Active in ELSS'),
                'autologin_url' => array('label' => 'Dashboard Link'),
                'cobranded_link' => array('label' => 'Co-Branded Link'),
                'Smart Transaction Link' => array('label' => 'Smart Transaction Link (ARN & EUIN embedded link)'),
                'Contribution in ELSS Amount' => array('label' => 'Contribution in ELSS Amount'),
            );
            $exportData[] = $csv_headers;

            $report=MasterSipStpTransactionDetailsModel::get_event_analytics_nfo_scheme_road_shows();
            $output_arr = array();
            $output_arr[] = array_column($csv_headers, 'label');
            if(!empty($report)){
                foreach($report as $key => $value){
                    $row = array();
                    foreach($csv_headers as $field_name_key => $field_name_value){
                        $row[$field_name_key] = '';
                        switch($field_name_key){
                            case 'autologin_url':
                            $row[$field_name_key] = env('SAMCOMF_PARTNERS_URL')."/autoLoginEmpanelURL/".Crypt::encryptString($row['ARN']);
                            break;
                            case 'cobranded_link':
                            $row[$field_name_key] = env('SAMCOMF_PARTNERS_URL')."/autoLoginEmpanelURL/".Crypt::encryptString($row['ARN']).'/co-branded-video';
                            break;
                            default:
                            if(isset($value->$field_name_key)){
                                $row[$field_name_key] = $value->$field_name_key;
                            }
                        }
                    }
                    $output_arr[] = $row;
                    unset($field_name_key, $field_name_value);
                }

                if(count($output_arr) > 0){
                    return \Excel::download(new ArrayRecordsExport($output_arr), 'Event_Analytics_Nfo_Scheme_Road_Shows_Data_'. date('Ymd') .'.xlsx');
                }
                else{
                    ?><script>alert('No records found');window.close();</script><?php
                }
            }
            else{
                ?><script>alert('No records found');window.close();</script><?php
            }
        }
        if(isset($report_type) && ($report_type == 'event_analytics_nfo_scheme_road_shows_summary')){
            $report = array();
            $csv_headers = array('City' => array('label' => 'Event City'),
                'Event Date' => array('label' => 'Event Date'),
                'Invited' => array('label' => 'Invited'),
                'Registered' => array('label' => 'Registered'),
                'Attended' => array('label' => 'Attended'),
                'registered_status ' => array('label' => 'Registereds Status'),
                'attendance' => array('label' => 'Attendance'),
                'arn' => array('label' => 'ARN'),
                'status' => array('label' => 'Status'),
                'fcrg_aum' => array('label' => 'FCRG AUM'),
                'onrg_aum' => array('label' => 'ONRG AUM'),
                'elrg_aum' => array('label' => 'ELRG AUM'),
                'Total Empanelled from Invite List' => array('label' => 'Total Empanelled from Invite List'),
                'Total_Empanelled_from_Registered' => array('label' => 'Total Empanelled From Registered'),
                'Total Empanelled from Attended List' => array('label' => 'Total Empanelled From Attended List'),
                'Contributed to Flexi from Attended aum' => array('label' => 'Contributed to Flexi from Attended aum'),
                'Contributed To Liquid From Attended aum' => array('label' => 'Contributed To Liquid From Attended aum'),
                'Contributed to Flexi from Attended count' => array('label' => 'Contributed to Flexi from Attended count'),
                'Contributed To Liquid From Attended count' => array('label' => 'Contributed To Liquid From Attended count'),
                'Contributed To ELSS From Attended aum' => array('label' => 'Contributed To ELSS From Attended aum'),
                'Contributed To ELSS From Attended count' => array('label' => 'Contributed To ELSS From Attended count'),
                'Attended and Non Active' => array('label' => 'Attended and Non Active'),
                'Attended and Active' => array('label' => 'Attended and Active'),
                'Attended and Active (F+L)' => array('label' => 'Attended and Active (F+L)'),
                'Attended Em Unique to ELSS' => array('label' => 'Attended Em Unique to ELSS'),
                'project focus count' => array('label' => 'Project focus count'),
                'project emerging stars count' => array('label' => 'Attended Em Unique to ELSS'),
                'project green shoots count' => array('label' => 'Project green shoots count')
            );
            $exportData[] = $csv_headers;

            $report=MasterSipStpTransactionDetailsModel::get_event_analytics_nfo_scheme_road_shows_summary();
            $output_arr = array();
            $output_arr[] = array_column($csv_headers, 'label');
            if(!empty($report)){
                foreach($report as $key => $value){
                    $row = array();
                    foreach($csv_headers as $field_name_key => $field_name_value){
                        $row[$field_name_key] = '';
                        switch($field_name_key){
                            case 'autologin_url':
                            $row[$field_name_key] = env('SAMCOMF_PARTNERS_URL')."/autoLoginEmpanelURL/".Crypt::encryptString($row['ARN']);
                            break;
                            case 'cobranded_link':
                            $row[$field_name_key] = env('SAMCOMF_PARTNERS_URL')."/autoLoginEmpanelURL/".Crypt::encryptString($row['ARN']).'/co-branded-video';
                            break;
                            default:
                            if(isset($value->$field_name_key)){
                                $row[$field_name_key] = $value->$field_name_key;
                            }
                        }
                    }
                    $output_arr[] = $row;
                    unset($field_name_key, $field_name_value);
                }

                if(count($output_arr) > 0){
                    return \Excel::download(new ArrayRecordsExport($output_arr), 'Event_Analytics_Nfo_Scheme_Road_Shows_Data_summary_'. date('Ymd') .'.xlsx');
                }
                else{
                    ?><script>alert('No records found');window.close();</script><?php
                }
            }
            else{
                ?><script>alert('No records found');window.close();</script><?php
            }
        }

        if(isset($report_type) && ($report_type == 'distributor_wise_scheme_aum')){
            $report = array();

            $report=MasterSipStpTransactionDetailsModel::get_distributor_wise_scheme_aum();
            $csv_headers = array('ARN Number' => array('label' => 'ARN Number'),
                'ARN Name' => array('label' => 'ARN Name'),
                'ARN Email' => array('label' => 'ARN Email'),
                'ARN Contact Number' => array('label' => 'ARN Contact Number'),
                'Is SAMCOMF Partner?' => array('label' => 'Is SAMCOMF Partner?'),
                'BDM Name' => array('label' => 'Relationship Manager\'s Name'),
                'BDM Contact Number' => array('label' => 'Relationship Manager\'s Contact Number'),
                'Reporting Name' => array('label' => 'Reporting Name'),
                'Reporting Email' => array('label' => 'Reporting Email'),
                'Reporting Contact Number' => array('label' => 'Reporting Contact Number'),
                'Reporting Designation' => array('label' => 'Reporting Designation')
            );
            if(isset($report['record_headings']) && is_array($report['record_headings']) && count($report['record_headings']) > 0){
                foreach($report['record_headings'] as $record_heading_key => $record_heading_label){
                    $csv_headers[$record_heading_key] = array('label' => $record_heading_label);
                }
                unset($record_heading_key, $record_heading_label);
            }
            $csv_headers = array_merge($csv_headers,
                array(
                    'autologin_url' => array('label' => 'Auto Login Dashboard Link'),
                    'cobranded_link' => array('label' => 'Co-Branded Materials Section Link'),
                    'open_url' => array('label' => 'Smart Transaction Link (ARN & EUIN embedded link)')
                )
            );
            $exportData[] = $csv_headers;

            $output_arr = array();
            $output_arr[] = array_column($csv_headers, 'label');
            if(isset($report['records']) && is_array($report['records']) && count($report['records']) > 0){
                foreach($report['records'] as $key => $value){
                    $row = array();
                    foreach($csv_headers as $field_name_key => $field_name_value){
                        $row[$field_name_key] = '';
                        switch($field_name_key){
                            case 'autologin_url':
                            if(isset($value['arn_status']) && (intval($value['arn_status']) == 2)){
                                $row[$field_name_key] = env('SAMCOMF_PARTNERS_URL')."/autoLoginEmpanelURL/".Crypt::encryptString($row['ARN Number']);
                            }
                            break;
                            case 'cobranded_link':
                            if(isset($value['arn_status']) && (intval($value['arn_status']) == 2)){
                                $row[$field_name_key] = env('SAMCOMF_PARTNERS_URL')."/autoLoginEmpanelURL/".Crypt::encryptString($row['ARN Number']).'/co-branded-video';
                            }
                            break;
                            case 'open_url':
                            $row[$field_name_key] = env('SAMCOMF_STATIC_WEB_URL')."/d/register/".$row['ARN Number'];
                            if(isset($value['amfi_euin']) && !empty($value['amfi_euin'])){
                                $row[$field_name_key] .= '/'. $value['amfi_euin'];
                            }
                            break;
                            default:
                            if(isset($value[$field_name_key])){
                                $row[$field_name_key] = $value[$field_name_key];
                            }
                        }
                    }
                    $output_arr[] = $row;
                    unset($field_name_key, $field_name_value);
                }

                if(count($output_arr) > 0){
                    return \Excel::download(new ArrayRecordsExport($output_arr), 'Distributor_Wise_Scheme_Aum_'. date('Ymd') .'.xlsx');
                }
                else{
                    ?><script>alert('No records found');window.close();</script><?php
                }
            }
            else{
                ?><script>alert('No records found');window.close();</script><?php
            }
        }
        if(isset($report_type) && ($report_type == 'event_analytics_nfo_scheme_road_shows_summaryflex')){
            $report = array();
            $csv_headers = array('City' => array('label' => 'Event City'),
                'Event Date' => array('label' => 'Event Date'),
                'Invited' => array('label' => 'Invited'),
                'Registered' => array('label' => 'Registered'),
                'Attended' => array('label' => 'Attended'),
                'registered_status ' => array('label' => 'Registereds Status'),
                'attendance' => array('label' => 'Attendance'),
                'arn' => array('label' => 'ARN'),
                'status' => array('label' => 'Status'),
                'fcrg_aum' => array('label' => 'FCRG AUM'),
                'onrg_aum' => array('label' => 'ONRG AUM'),
                'elrg_aum' => array('label' => 'ELRG AUM'),
                'Total Empanelled from Invite List' => array('label' => 'Total Empanelled from Invite List'),
                'Total_Empanelled_from_Registered' => array('label' => 'Total Empanelled From Registered'),
                'Total Empanelled from Attended List' => array('label' => 'Total Empanelled From Attended List'),
                'Contributed to Flexi from Attended aum' => array('label' => 'Contributed to Flexi from Attended aum'),
                'Contributed To Liquid From Attended aum' => array('label' => 'Contributed To Liquid From Attended aum'),
                'Contributed to Flexi from Attended count' => array('label' => 'Contributed to Flexi from Attended count'),
                'Contributed To Liquid From Attended count' => array('label' => 'Contributed To Liquid From Attended count'),
                'Contributed To ELSS From Attended aum' => array('label' => 'Contributed To ELSS From Attended aum'),
                'Contributed To ELSS From Attended count' => array('label' => 'Contributed To ELSS From Attended count'),
                'Attended and Non Active' => array('label' => 'Attended and Non Active'),
                'Attended and Active' => array('label' => 'Attended and Active'),
                'Attended and Active (F+L)' => array('label' => 'Attended and Active (F+L)'),
                'Attended Em Unique to ELSS' => array('label' => 'Attended Em Unique to ELSS'),
                'project focus count' => array('label' => 'Project focus count'),
                'project emerging stars count' => array('label' => 'Attended Em Unique to ELSS'),
                'project green shoots count' => array('label' => 'Project green shoots count'),
                'Unique Active in (Equity + Hybrid)' => array('label' => 'Unique Active in (Equity + Hybrid)'),
                'Active in Flexi' => array('label' =>'Active in Flexi'),
                'Active in Liquid' => array('label' =>'Active in Liquid'),
                'Active in ELSS' => array('label' =>'Active in ELSS'),

            );
            $exportData[] = $csv_headers;

            $report=MasterSipStpTransactionDetailsModel::get_event_analytics_nfo_scheme_road_shows_summaryflexcap();
            $output_arr = array();
            $output_arr[] = array_column($csv_headers, 'label');
            if(!empty($report)){
                foreach($report as $key => $value){
                    $row = array();
                    foreach($csv_headers as $field_name_key => $field_name_value){
                        $row[$field_name_key] = '';
                        switch($field_name_key){
                            case 'autologin_url':
                            $row[$field_name_key] = env('SAMCOMF_PARTNERS_URL')."/autoLoginEmpanelURL/".Crypt::encryptString($row['ARN']);
                            break;
                            case 'cobranded_link':
                            $row[$field_name_key] = env('SAMCOMF_PARTNERS_URL')."/autoLoginEmpanelURL/".Crypt::encryptString($row['ARN']).'/co-branded-video';
                            break;
                            default:
                            if(isset($value->$field_name_key)){
                                $row[$field_name_key] = $value->$field_name_key;
                            }
                        }
                    }
                    $output_arr[] = $row;
                    unset($field_name_key, $field_name_value);
                }

                if(count($output_arr) > 0){
                    return \Excel::download(new ArrayRecordsExport($output_arr), 'Event_Analytics_Nfo_Scheme_Road_Shows_Data_summary_flexi'. date('Ymd') .'.xlsx');
                }
                else{
                    ?><script>alert('No records found');window.close();</script><?php
                }
            }
            else{
                ?><script>alert('No records found');window.close();</script><?php
            }
        }
        if(isset($report_type) && ($report_type == 'event_analytics_nfo_scheme_road_shows_summaryovernight')){
            $report = array();
            $csv_headers = array('City' => array('label' => 'Event City'),
                'Event Date' => array('label' => 'Event Date'),
                'Invited' => array('label' => 'Invited'),
                'Registered' => array('label' => 'Registered'),
                'Attended' => array('label' => 'Attended'),
                'registered_status ' => array('label' => 'Registereds Status'),
                'attendance' => array('label' => 'Attendance'),
                'arn' => array('label' => 'ARN'),
                'status' => array('label' => 'Status'),
                'fcrg_aum' => array('label' => 'FCRG AUM'),
                'onrg_aum' => array('label' => 'ONRG AUM'),
                'elrg_aum' => array('label' => 'ELRG AUM'),
                'Total Empanelled from Invite List' => array('label' => 'Total Empanelled from Invite List'),
                'Total_Empanelled_from_Registered' => array('label' => 'Total Empanelled From Registered'),
                'Total Empanelled from Attended List' => array('label' => 'Total Empanelled From Attended List'),
                'Contributed to Flexi from Attended aum' => array('label' => 'Contributed to Flexi from Attended aum'),
                'Contributed To Liquid From Attended aum' => array('label' => 'Contributed To Liquid From Attended aum'),
                'Contributed to Flexi from Attended count' => array('label' => 'Contributed to Flexi from Attended count'),
                'Contributed To Liquid From Attended count' => array('label' => 'Contributed To Liquid From Attended count'),
                'Contributed To ELSS From Attended aum' => array('label' => 'Contributed To ELSS From Attended aum'),
                'Contributed To ELSS From Attended count' => array('label' => 'Contributed To ELSS From Attended count'),
                'Attended and Non Active' => array('label' => 'Attended and Non Active'),
                'Attended and Active' => array('label' => 'Attended and Active'),
                'Attended and Active (F+L)' => array('label' => 'Attended and Active (F+L)'),
                'Attended Em Unique to ELSS' => array('label' => 'Attended Em Unique to ELSS'),
                'project focus count' => array('label' => 'Project focus count'),
                'project emerging stars count' => array('label' => 'Attended Em Unique to ELSS'),
                'project green shoots count' => array('label' => 'Project green shoots count'),
                'Unique Active in (Equity + Hybrid)' => array('label' => 'Unique Active in (Equity + Hybrid)'),
                'Active in Flexi' => array('label' =>'Active in Flexi'),
                'Active in Liquid' => array('label' =>'Active in Liquid'),
                'Active in ELSS' => array('label' =>'Active in ELSS'),

            );
            $exportData[] = $csv_headers;

            $report=MasterSipStpTransactionDetailsModel::get_event_analytics_nfo_scheme_road_shows_summary_overnight();
            $output_arr = array();
            $output_arr[] = array_column($csv_headers, 'label');
            if(!empty($report)){
                foreach($report as $key => $value){
                    $row = array();
                    foreach($csv_headers as $field_name_key => $field_name_value){
                        $row[$field_name_key] = '';
                        switch($field_name_key){
                            case 'autologin_url':
                            $row[$field_name_key] = env('SAMCOMF_PARTNERS_URL')."/autoLoginEmpanelURL/".Crypt::encryptString($row['ARN']);
                            break;
                            case 'cobranded_link':
                            $row[$field_name_key] = env('SAMCOMF_PARTNERS_URL')."/autoLoginEmpanelURL/".Crypt::encryptString($row['ARN']).'/co-branded-video';
                            break;
                            default:
                            if(isset($value->$field_name_key)){
                                $row[$field_name_key] = $value->$field_name_key;
                            }
                        }
                    }
                    $output_arr[] = $row;
                    unset($field_name_key, $field_name_value);
                }

                if(count($output_arr) > 0){
                    return \Excel::download(new ArrayRecordsExport($output_arr), 'Event_Analytics_Nfo_Scheme_Road_Shows_Data_summary_overnight'. date('Ymd') .'.xlsx');
                }
                else{
                    ?><script>alert('No records found');window.close();</script><?php
                }
            }
            else{
                ?><script>alert('No records found');window.close();</script><?php
            }
        }
    }

    public function inflow_outflow_order(){
        if(intval(date('H'))<9){
            return false;
        }
        $inflowoutflow = MasterSipStpTransactionDetailsModel::getInflow_Order();
        $outflowoutflow = MasterSipStpTransactionDetailsModel::getOutflow_Order();
        $netflow = MasterSipStpTransactionDetailsModel::getNetflow_Order();
        $to_mail = getSettingsTableValue('INFLOW_OUTFLOW_NETFLOW_ORDER');
        if(isset($to_mail) && !empty($to_mail)){
            $to_mail = explode(',',$to_mail);
            $expload_to_mail = array();
            foreach($to_mail as $v){
                $expload_to_mail[] = array($v);
            }
        }

        $tottaelss=0;$totalflex=0;$totalover=0;$totalnet=0;
        $outovernightlumpsum=0;$outovernightsip=0;$outovernightstp=0;$outovernightswitch=0;
        $outflexlumpsum=0;$outflexsip=0;$outflexstp=0;$outflexswitch=0;
        $outelsslumpsum=0;$outelsssip=0;$outelssstp=0;$outelssswitch=0;$totallumoutflow=0;
        //inflow
        $inovernightlumpsum=0;$inovernightsip=0;$inovernightstp=0;$inovernightswitch=0;
        $inflexlumpsum=0;$inflexsip=0;$inflexstp=0;$inflexswitch=0;
        $inelsslumpsum=0;$inelsssip=0;$inelssstp=0;$inelssswitch=0;
        //netflow
        $netovernightlumpsum=0;$netovernightsip=0;$netovernightstp=0;$netovernightswitch=0;
        $netflexlumpsum=0;$netflexsip=0;$netflexstp=0;$netflexswitch=0;
        $netelsslumpsum=0;$netelsssip=0;$netelssstp=0;$netelssswitch=0;
        $totaloutflowelss=0;$totaloutflexcap=0;$totalaover=0;$totalout=0;
        $nettotalelss=0;$nettotalflex=0;$nettotalover=0;$nettotal=0;

        $table = "<table border=1 width='100%' style='border-collapse: collapse;' cellpadding='5'>";
        $table .= "<tr> <th colspan=5>Inflow</th></tr>";
        $table .= "<tr>";
        $table .= "<th>Description</th>";
        $table .= "<th>ELSS</th>";
        $table .= "<th>Flexi Cap</th>";
        $table .= "<th>Overnight Fund</th>";
        $table .= "<th>TOTAL</th>";
        $table .= "</tr>";
        foreach($inflowoutflow as $_key => $_value)
        {
            if(strtolower($_value['scheme'])=='fc')
            {
                $inflexlumpsum=$_value['totalumpsumamt'];
                $inflexsip=$_value['totalsipamt'];
                $inflexstp=$_value['totalstpamt'];
                $inflexswitch=$_value['totalswitchamt'];
                $totalflex=($inflexlumpsum+$inflexsip+$inflexstp+$inflexswitch);
            }
            if(strtolower($_value['scheme'])=='el')
            {
                $inelsslumpsum=$_value['totalumpsumamt'];
                $inelsssip=$_value['totalsipamt'];
                $inelssstp=$_value['totalstpamt'];
                $inelssswitch=$_value['totalswitchamt'];
                $tottaelss=($inelsslumpsum+$inelsssip+$inelssstp+$inelssswitch);
            }
            if(strtolower($_value['scheme'])=='on')
            {
                $inovernightlumpsum=$_value['totalumpsumamt'];
                $inovernightsip=$_value['totalsipamt'];
                $inovernightstp=$_value['totalstpamt'];
                $inovernightswitch=$_value['totalswitchamt'];
                $totalover=($inovernightlumpsum+$inovernightsip+$inovernightstp+$inovernightswitch);
            }
        }
        $totalnet=($totalover+$tottaelss+$totalflex);
        $table .= "<tr>";
        $table .= "<td width='20%'>Lumpsum/Direct purchase</td><td width='20%' align ='right'>".$this->moneyFormatIndia($inelsslumpsum)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($inflexlumpsum)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($inovernightlumpsum)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($inelsslumpsum+$inflexlumpsum+$inovernightlumpsum))."</td>";
        $table .= "</tr>";
        $table .= "<tr>";
        $table .= "<td width='20%'>Through SIP</td><td width='20%' align ='right'>".$this->moneyFormatIndia($inelsssip)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($inflexsip)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($inovernightsip)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($inelsssip+$inflexsip+$inovernightsip))."</td>";
        $table .= "</tr>";
        $table .= "<tr>";
        $table .= "<td width='20%'>Through STP</td><td width='20%' align ='right'>".$this->moneyFormatIndia($inelssstp)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($inflexstp)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($inovernightstp)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($inelssstp+$inflexstp+$inovernightstp))."</td>";
        $table .= "</tr>";
        $table .= "<tr>";
        $table .= "<td width='20%'>Through Switch</td><td width='20%' align ='right'>".$this->moneyFormatIndia($inelssswitch)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($inflexswitch)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($inovernightswitch)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($inelssswitch+$inflexswitch+$inovernightswitch))."</td>";

        $table .= "</tr>";
        $table .= "<tr>";
        $table .= "<td width='20%'>Net Inflow</td><td width='20%' align ='right'>".$this->moneyFormatIndia($tottaelss)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($totalflex)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($totalover)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($totalnet)."</td>";

        $table .= "</tr>";
        $table .= "</table>";
        // out flow
        $table .= "<br/><table border=1 width='100%' style='border-collapse: collapse;' cellpadding='5'>";
        $table .= "<tr> <th colspan=5>Outflow</th></tr>";
        $table .= "<tr>";
        $table .= "<th>Description</th>";
        $table .= "<th>ELSS</th>";
        $table .= "<th>Flexi Cap</th>";
        $table .= "<th>Overnight Fund</th>";
        $table .= "<th>TOTAL</th>";
        $table .= "</tr>";
        foreach($outflowoutflow as $_key => $_value)
        {
            if(strtolower($_value['scheme'])=='fc')
            {
                $outflexlumpsum=$_value['totalumpsumamt'];
                $outflexsip=$_value['totalsipamt'];
                $outflexstp=$_value['totalstpamt'];
                $outflexswitch=$_value['totalswitchamt'];
                $totaloutflexcap=$outflexlumpsum+$outflexsip+$outflexstp+$outflexswitch;
            }
            if(strtolower($_value['scheme'])=='el')
            {
                $outelsslumpsum=$_value['totalumpsumamt'];
                $outelsssip=$_value['totalsipamt'];
                $outelssstp=$_value['totalstpamt'];
                $outelssswitch=$_value['totalswitchamt'];
                $totaloutflowelss=$outelsslumpsum+$outelsssip+$outelssstp+$outelssswitch;
            }
            if(strtolower($_value['scheme'])=='on')
            {
                $outovernightlumpsum=$_value['totalumpsumamt'];
                $outovernightsip=$_value['totalsipamt'];
                $outovernightstp=$_value['totalstpamt'];
                $outovernightswitch=$_value['totalswitchamt'];
                $totalaover=$outovernightlumpsum+$outovernightsip+$outovernightstp+$outovernightswitch;
            }
        }
        $totalout=$totaloutflowelss+$totaloutflexcap+$totalaover;
        $totallumoutflow=($outelsssip+$outflexsip+$outovernightsip);
        $table .= "<tr>";
        $table .= "<td width='20%'>Lumpsum</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($outelsslumpsum+$outelsssip))."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($outflexlumpsum+$outflexsip))."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($outovernightlumpsum+$outovernightsip))."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($outelsslumpsum+$outflexlumpsum+$outovernightlumpsum+$totallumoutflow))."</td>";
        $table .= "</tr>";
        /*$table .= "<tr>";
        $table .= "<td>Through SIP</td><td>".$outelsssip."</td><td>".$outflexsip."</td><td>".$outovernightsip."</td><td>".($outelsssip+$outflexsip+$outovernightsip)."</td>";
        $table .= "</tr>";*/
        $table .= "<tr>";
        $table .= "<td width='20%'>Through STP</td><td width='20%' align ='right'>".$this->moneyFormatIndia($outelssstp)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($outflexstp)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($outovernightstp)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($outelssstp+$outflexstp+$outovernightstp))."</td>";
        $table .= "</tr>";
        $table .= "<tr>";
        $table .= "<td width='20%'>Through Switch</td><td width='20%' align ='right'>".$this->moneyFormatIndia($outelssswitch)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($outflexswitch)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($outovernightswitch)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($outelssswitch+$outflexswitch+$outovernightswitch))."</td>";

        $table .= "</tr>";
        $table .= "<tr>";
        $table .= "<td width='20%'>Net Outflow</td><td width='20%' align ='right'>".$this->moneyFormatIndia($totaloutflowelss)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($totaloutflexcap)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($totalaover)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($totalout)."</td>";

        $table .= "</tr>";
        $table .= "</table>";
        //end
        // Net flow
        $table .= "<br/><table border=1 width='100%' style='border-collapse: collapse;' cellpadding='5'>";
        $table .= "<tr> <th colspan=5>Netflow</th></tr>";
        $table .= "<tr>";
        $table .= "<th>Description</th>";
        $table .= "<th>ELSS</th>";
        $table .= "<th>Flexi Cap</th>";
        $table .= "<th>Overnight Fund</th>";
        $table .= "<th>TOTAL</th>";
        $table .= "</tr>";
        foreach($netflow as $_key => $_value)
        {
            if(strtolower($_value['Scheme_Code'])=='fc')
            {
                $netflexlumpsum=$_value['lumpsum_amount'];
                $netflexsip=$_value['sip_amount'];
                $netflexstp=$_value['stp_amount'];
                $netflexswitch=$_value['switchamount'];
                $nettotalflex=$netflexlumpsum+$netflexsip+$netflexstp+$netflexswitch;
            }
            if(strtolower($_value['Scheme_Code'])=='el')
            {
                $netelsslumpsum=$_value['lumpsum_amount'];
                $netelsssip=$_value['sip_amount'];
                $netelssstp=$_value['stp_amount'];
                $netelssswitch=$_value['switchamount'];
                $nettotalelss=$netelsslumpsum+$netelsssip+$netelssstp+$netelssswitch;
            }
            if(strtolower($_value['Scheme_Code'])=='on')
            {
                $netovernightlumpsum=$_value['lumpsum_amount'];
                $netovernightsip=$_value['sip_amount'];
                $netovernightstp=$_value['stp_amount'];
                $netovernightswitch=$_value['switchamount'];
                $nettotalover=$netovernightlumpsum+$netovernightsip+$netovernightstp+$netovernightswitch;
            }
        }

        $nettotal=$nettotalover+$nettotalelss+$nettotalflex;
        $table .= "<tr>";
        $table .= "<td width='20%'>Lumpsum/Direct purchase</td><td width='20%' align ='right'>".$this->moneyFormatIndia($netelsslumpsum)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($netflexlumpsum)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($netovernightlumpsum)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($netelsslumpsum+$netflexlumpsum+$netovernightlumpsum))."</td>";
        $table .= "</tr>";
        $table .= "<tr>";
        $table .= "<td width='20%'>Through SIP</td><td width='20%' align ='right'>".$this->moneyFormatIndia($netelsssip)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($netflexsip)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($netovernightsip)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($netelsssip+$netflexsip+$netovernightsip))."</td>";
        $table .= "</tr>";
        $table .= "<tr>";
        $table .= "<td width='20%'>Through STP</td><td width='20%' align ='right'>".$this->moneyFormatIndia($netelssstp)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($netflexstp)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($netovernightstp)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($netelssstp+$netflexstp+$netovernightstp))."</td>";
        $table .= "</tr>";
        $table .= "<tr>";
        $table .= "<td width='20%'>Through Switch</td><td width='20%' align ='right'>".$this->moneyFormatIndia($netelssswitch)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($netflexswitch)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($netovernightswitch)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia(($netelssswitch+$netflexswitch+$netovernightswitch))."</td>";

        $table .= "</tr>";
        $table .= "<tr>";
        $table .= "<td width='20%'>Net flow</td><td width='20%' align ='right'>".$this->moneyFormatIndia($nettotalelss)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($nettotalflex)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($nettotalover)."</td><td width='20%' align ='right'>".$this->moneyFormatIndia($nettotal)."</td>";

        $table .= "</tr>";
        $table .= "</table>";
        //end

        if(!empty($expload_to_mail))
        {
            $mailer = new PhpMailer();
            $params = [];
            $template = "SAMCOMF-GENERAL-NOTIFICATION";
            $params['templateName'] = $template;
            $params['channel']      = $template;
            $params['from_email']   = "alerts@samcomf.com";
            $params['to']           = $expload_to_mail;
            $params['merge_vars'] = array('MAIL_BODY' => $table);
            $params['subject'] ='['. date('d M Y H:i:A') .'] Provisional DTR Inflow Outflow';
            $email_send = $mailer->mandrill_send($params);
            $output_arr[] = array("message"=>"Email Sent Successully","status"=>"success");
        }
        else{
            $output_arr[] = array("message"=>'To email not fund',"status"=>"error");
        }
        return $output_arr;
    }

    public function moneyFormatIndia($number){
        $number=round($number);
        if($number<0)
        {
            $sign='-';
            $number=$this->checkmin($number);
        }
        else{
            $sign='';
            $number=$number;
        }
        $decimal = (string)($number - floor($number));
        $money = floor($number);
        $length = strlen($money);
        $delimiter = '';
        $money = strrev($money);

        for($i=0;$i<$length;$i++){
            if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i!=$length){
                $delimiter .=',';
            }
            $delimiter .=$money[$i];
        }

        $result = strrev($delimiter);
        $decimal = preg_replace("/0\./i", ".", $decimal);
        $decimal = substr($decimal, 0, 3);

        if( $decimal != '0'){
            $result = $result.$decimal;
        }

        return $sign.$result;
    }

    function checkmin($str){
        $res = str_replace( array( '\'', '"',
            ',' , ';', '<', '>' ,'-'), '', $str);
        return $res;
    }

    function empanelment_alert(){
        $list=MasterSipStpTransactionDetailsModel::getdmlistemplement();
        $partner_stage=array('1' =>'Verification',
            '7' =>'Add Bank Details',
            '8' =>'Nominee Details',
            '2' =>'Upload Documents',
            '5' =>'Add Signatories',
            '6' =>'E-sign & Verify',
            '3' =>'Consent',
            '4' =>'Thank You',
            '9' =>'ARN');
        $status=array('0' =>'Created',
            '1' =>'Approved',
            '2' =>'Activated',
            '3' =>'Deactivated');

        $prepair_array=[];
        array_walk($list,function($value, $key, $user_data)
        {
            if(!isset($user_data[0][$value['bdm_email']])){
                $user_data[0][$value['bdm_email']] = array('personal' => array('bdm_name' => $value['bdm_name'],'bdm_email' => $value['bdm_email'],'bdm_mobile_number' => $value['bdm_mobile_number'],'empanelment_date'=>$value['created_at']), 'records' => array());
            }
            $user_data[0][$value['bdm_email']]['records'][] = $value;
        },[&$prepair_array]);
        if(!empty($prepair_array))
        {
            foreach($prepair_array as $key=>$value)
            {
                if(empty($value['personal']['bdm_name']))
                {
                    $bdm_email = getSettingsTableValue('EMPANELEMENT_ALERT_EMAIL');
                    $name = getSettingsTableValue('EMPANELEMENT_ALERT_NAME');
                    $arn_mobile = getSettingsTableValue('EMPANELEMENT_ALERT_MOBILE');
                }
                else
                {
                    $name=$value['personal']['bdm_name'];
                    $bdm_email=$value['personal']['bdm_email'];
                    $arn_mobile=$value['personal']['bdm_mobile_number'];
                }
                $empanelment_date=date('Y-m-d',strtotime($value['personal']['empanelment_date']));
                $totalcount=count($value['records']);
                $expload_to_mail=array($bdm_email);
                //$expload_to_mail=array('mannu.rajak@samcomf.com');
                $current_date_time = date('Y-m-d_H-i').rand();
                $partnercsvfile = sys_get_temp_dir().'/LEAD_PARTNERS_DATA_'. $current_date_time .'.csv';
                //x($expload_to_mail);
                $file_csv = fopen($partnercsvfile,'w');
                //y($file_csv);
                fputcsv($file_csv, array('ARN', 'ARN Holder Name', 'ARN Email ID', 'ARN Mobile Number','Relationship Manager Name', 'Relationship Manager Email ID', 'Relationship Manager Mobile Number', 'Reporting Manager Name', 'Reporting Manager Email ID', 'Reporting Manager Mobile Number','Latest Stage','Record Status','Record Source','Created At'));
                foreach($value['records'] as $nestvalue)
                {
                    $nestvalue['form_status']=$partner_stage[$nestvalue['form_status']];
                    $nestvalue['status']=$status[$nestvalue['status']];
                    fputcsv($file_csv, (array) $nestvalue);
                }
                $email_body='<table style="width: 600px; margin-right: auto;margin-left: auto">
                <tr>
                <td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;font-weight: 500;padding: 5px;">Hi,'.$name.'</td>
                </tr>
                <tr>
                <td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;font-weight: 500;padding: 5px;">You have '.$totalcount.' New empanelment leads created, Kindly login to your DRM and initiate follow up.
                </td>
                </tr>
                <tr>
                <td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;font-weight: 500;padding: 5px;">Please find attached list of new empanelment leads created on '.$empanelment_date.'</td>
                </tr>
                <tr>
                <td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;font-weight: 500;padding: 5px;">Pro Tip:</td>
                </tr>
                <tr>
                <td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;font-weight: 500;padding: 5px;">
                <ul>
                <li>Make a courtesy call</li>
                <li>Ask them for assistance</li>
                <li>Track their empanelment status</li>
                <li>Ensure 100% of them complete their empanelment </li>
                </ul>
                </td>
                </tr>
                <tr>
                <td>__</td>
                </tr>
                <tr>
                <td style="font-family: Heebo, sans-serif; font-size:16px;color:#434343;line-height:24px;font-weight: 500;padding: 5px;";>
                Thank you <br />
                Team Growth
                </td>
                </tr>
                </table>';
                fclose($file_csv);
                $mailer = new \App\Libraries\PhpMailer();
                $params = [];
                $template = "SAMCOMF-GENERAL-NOTIFICATION";
                $params['templateName'] = $template;
                $params['channel']      = $template;
                $params['from_email']   = "alerts@samcomf.com";
                $params['to']           = array($expload_to_mail);
                $params['attachment']   = array();
                if(file_exists($partnercsvfile)){
                    $params['attachment'] = array_merge($params['attachment'], array(array($partnercsvfile)));
                }
                $params['merge_vars'] = array('MAIL_BODY' => $email_body);
                $params['subject'] ='['. date('d M Y H:i:A') .']'.'New Empanelment Leads Created';
                $email_send = $mailer->mandrill_send($params);
                //x($email_send);
                unset($email_body, $params, $mailer, $email_send, $template,$expload_to_mail);
            }
            $output_arr[] = array("message"=>"Email Sent Successully","status"=>"success");
        }
        else
        {
            $output_arr[] = array("message"=>'To email not fund',"status"=>"error");
        }
        return $output_arr;
    }
}
