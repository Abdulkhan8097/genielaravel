<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Exports\ArrayRecordsExport;
use App\Models\AumdataModel;

class ReportsController extends Controller
{
    protected $logged_in_user_roles_and_permissions, $flag_have_all_permissions, $flag_show_all_arn_data, $logged_in_user_id;
    public function __construct(){
        $this->middleware('auth');

        $this->data_table_headings_project_focus = array(
                                            'ARN' => array('label' => 'ARN'),
                                            'arn_holders_name' => array('label' => 'ARN Holder`s Name'),
                                            'arn_email' => array('label' => 'Email'),
                                            'arn_telephone_r' => array('label' => 'AMFI Telephone (R)'),
                                            'arn_telephone_o' => array('label' => 'AMFI Telephone (O)'),
                                            'arn_city'=>array('label' =>'AMFI City'),
                                            'relationship_mapped_to'=>array('label' =>'Relationship Mapped to '),
                                            'last_meeting_date'=>array('label' =>'Last Meeting Date'),
                                            'total_ind_aum'=>array('label' =>'Industry AUM (In Crores)'),
                                            'is_samcomf_partner'=>array('label' =>'SamcoMF Partner (Yes/No)'),
                                            'samcomf_partner_aum'=>array('label' =>'Samcomf partner AUM'),
                                        );
        $this->data_table_headings_project_emerge = array(
                                            'ARN' => array('label' => 'ARN'),
                                            'arn_holders_name' => array('label' => 'ARN Holder`s Name'),
                                            'arn_email' => array('label' => 'Email'),
                                            'arn_telephone_r' => array('label' => 'AMFI Telephone (R)'),
                                            'arn_telephone_o' => array('label' => 'AMFI Telephone (O)'),
                                            'arn_city'=>array('label' =>'AMFI City'),
                                            'relationship_mapped_to'=>array('label' =>'Relationship Mapped to '),
                                            'last_meeting_date'=>array('label' =>'Last Meeting Date'),
                                            'total_ind_aum'=>array('label' =>'Industry AUM (In Crores)'),
                                            'is_samcomf_partner'=>array('label' =>'SamcoMF Partner (Yes/No)'),
                                            'samcomf_partner_aum'=>array('label' =>'Samcomf partner AUM'),
                                        );
        $this->data_table_headings_partner_aum_no_transactions = array(
                                            'ARN' => array('label' => 'ARN'),
                                            'arn_holders_name' => array('label' => 'ARN Holder`s Name'),
                                            'arn_email' => array('label' => 'Email'),
                                            'arn_telephone_r' => array('label' => 'AMFI Telephone (R)'),
                                            'arn_telephone_o' => array('label' => 'AMFI Telephone (O)'),
                                            'arn_city'=>array('label' =>'AMFI City'),
                                            'relationship_mapped_to'=>array('label' =>'Relationship Mapped to '),
                                            'reporting_to'=>array('label' =>'Reporting Manager'),
                                            'last_meeting_date'=>array('label' =>'Last Meeting Date'),
                                            'total_ind_aum'=>array('label' =>'Industry AUM (In Crores)'),
                                            'is_samcomf_partner'=>array('label' =>'SamcoMF Partner (Yes/No)'),
                                            'samcomf_partner_aum'=>array('label' =>'Samcomf partner AUM'),
                                            'last_purchase_transaction_date'=>array('label' =>'Last Purchase Transaction Date'),
                                        );
        $this->data_table_headings_partner_aum_no_active_sip = array(
                                            'ARN' => array('label' => 'ARN'),
                                            'arn_holders_name' => array('label' => 'ARN Holder`s Name'),
                                            'arn_email' => array('label' => 'Email'),
                                            'arn_telephone_r' => array('label' => 'AMFI Telephone (R)'),
                                            'arn_telephone_o' => array('label' => 'AMFI Telephone (O)'),
                                            'arn_city'=>array('label' =>'AMFI City'),
                                            'relationship_mapped_to'=>array('label' =>'Relationship Mapped to '),
                                            'reporting_to'=>array('label' =>'Reporting Manager'),
                                            'last_meeting_date'=>array('label' =>'Last Meeting Date'),
                                            'total_ind_aum'=>array('label' =>'Industry AUM (In Crores)'),
                                            'is_samcomf_partner'=>array('label' =>'SamcoMF Partner (Yes/No)'),
                                            'samcomf_partner_aum'=>array('label' =>'Samcomf partner AUM'),
                                            'last_purchase_transaction_date'=>array('label' =>'Last Purchase Transaction Date'),
                                        );
        $this->data_table_headings_partner_aum_unique_client = array(
                                            'ARN' => array('label' => 'ARN'),
                                            'arn_holders_name' => array('label' => 'ARN Holder`s Name'),
                                            'arn_email' => array('label' => 'Email'),
                                            'arn_telephone_r' => array('label' => 'AMFI Telephone (R)'),
                                            'arn_telephone_o' => array('label' => 'AMFI Telephone (O)'),
                                            'arn_city'=>array('label' =>'AMFI City'),
                                            'relationship_mapped_to'=>array('label' =>'Relationship Mapped to '),
                                            'reporting_to'=>array('label' =>'Reporting Manager'),
                                            'last_meeting_date'=>array('label' =>'Last Meeting Date'),
                                            'total_ind_aum'=>array('label' =>'Industry AUM (In Crores)'),
                                            'is_samcomf_partner'=>array('label' =>'SamcoMF Partner (Yes/No)'),
                                            'samcomf_partner_aum'=>array('label' =>'Samcomf partner AUM'),
                                            'no_of_uniue_pan_linked'=>array('label' =>'Count Of Unique PAN'),
                                        );
        $this->data_table_headings_aum_transactions_analytics = array('action' => array('label' => 'Action'),
                                            'ARN' => array('label' => 'ARN'),
                                            'asset_type' => array('label' => 'Asset Type'),
                                            'total_gross_inflow' => array('label' => 'Total Gross Inflows'),
                                            'total_redemptions' => array('label' => 'Total Redemptions'),
                                            'total_netflow' => array('label' => 'Total Net Inflow'),
                                            'total_aum' => array('label' => 'Total AUM'),
                                        );
        $this->data_table_headings_daywise_transactions_analytics = array('ARN' => array('label' => 'ARN'),
                                            'arn_holder_name' => array('label' => 'ARN Holder Name'),
                                            'scheme_name' => array('label' => 'Scheme Name'),
                                            'total_netflow' => array('label' => 'Total NetInflow'),
                                            'total_gross_inflow'=>array('label' =>'Total Gross Inflows'),
                                            'total_redemptions'=>array('label' =>'Total Redemptions'),
                                            'total_aum'=>array('label' =>'Total AUM'),
                                            'lumpsum_purchases'=>array('label' =>'Lumpsum Purchases'),
                                            'sip_purchases'=>array('label' =>'SIP Purchases'),
                                            'total_purchases'=>array('label' =>'Total Purchases'),
                                        );
        $this->data_table_headings_sip_analytics = array('action' => array('label' => 'Action'),
                                            'ARN' => array('label' => 'ARN'),
                                            'asset_type' => array('label' => 'Asset Type'),
                                            'order_status' => array('label' => 'Status'),
                                            'installment_amount' => array('label' => 'Installment Amount'),
                                            'no_of_sip' => array('label' => 'No of SIP'),
                                        );
        $this->data_table_headings_client_analytics = array('action' => array('label' => 'Action'),
                                            'ARN' => array('label' => 'ARN'),
											'asset_type' => array('label' => 'Asset Type'),
                                            // 'no_of_clients' => array('label' => 'No of Client'),
                                            'total_gross_inflow' => array('label' => 'Total Gross Inflow'),
                                            'total_redemptions' => array('label' => 'Total Redemptions'),
                                            'total_netflow' => array('label' => 'Total Net Inflow'),
                                            'total_aum' => array('label' => 'Total AUM'),
                                        );
        $this->data_table_headings_client_monthwise_analytics = array(
                                            'ARN' => array('label' => 'ARN'),
                                            'm1' => array('label' => 'Month'),
                                            'asset_type' => array('label' => 'Asset Type'),
                                            'active_clients_with_aum' => array('label' => 'Active Clients with AUM'),
                                            'new_clients_with_aum' => array('label' => 'New Clients with AUM'),
                                            'clients_without_aum' => array('label' => 'Clients without AUM'),
                                        );
        $this->data_table_headings_bdmwise_inflows = array('bdm_name' => array('label' => 'BDM Name'),
                                            'reporting_manager' => array('label' => 'Reporting Manager'),
                                            'number_of_arn_mapped' => array('label' => 'Number of ARN\'s Mapped'),
                                            'number_of_arn_empanelled' => array('label' => 'Number of ARN\'s Empanelled'),
                                            'sip_gross_inflow_till_date'=>array('label' =>'Gross Inflow Equity Funds for Month till date - SIP'),
                                            'otherthan_sip_gross_inflow_till_date'=>array('label' =>'Gross Inflow Equity Funds for Month till date - Others'),
                                            'total_gross_inflow_till_date'=>array('label' =>'Gross Inflow Equity Funds for Month till date - Total Gross Inflows'),
                                            'gross_redemptions_till_date'=>array('label' =>'Redemptions for Month till date - Total Gross Inflows'),
                                            'net_inflow_till_date'=>array('label' =>'Net Inflow Equity Funds for Month till date'),
                                            'net_inflow_financial_year_till_date'=>array('label' =>'Net Inflow Equity Funds for FY till Month (April of FY till Date Selected)'),
                                            'net_inflow_current_quarter_till_date'=>array('label' =>'Net Inflow Equity Funds for Quarter till Month (Quarter begining till Date Selected)'),
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
    
    public function getReportofProjectFocusPartner(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // if data is posted then showing distributors list in datatable or exporting them as per input parameters
            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
                // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }

            // Read value from Model method
            // print_r($request->all());
            $partnersData =ReportModel::getProjectFocusPartnerList(array_merge($request->all(),
                                                                           array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                                 'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                                 'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                                 'logged_in_user_id' => $this->logged_in_user_id,
                                                                                 'show_only_logged_in_user_data' => true)
                                                                       )
                                                            );

            if(!$partnersData['records']->isEmpty()){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $partnersData['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $partnersData['records'];
                    echo json_encode($output_arr);
                }
                else{
                    // when exporting data, preparing HEADER ROW for an informative purpose
                    $csv_headers = array_column($this->data_table_headings_project_focus, 'label');
                    // array_shift($csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    $output_arr[] = $csv_headers;
                    foreach($partnersData['records'] as $key => $value){
                        $row = array();
                        foreach($this->data_table_headings_project_focus as $field_name_key => $field_name_value){
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
                    return \Excel::download(new ArrayRecordsExport($output_arr),'project_focus_partner_master_data_'. date('Ymd').'.xlsx');
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
            $data = array('data_table_headings_project_focus' => $this->data_table_headings_project_focus);
            $data['UserDetails'] = \App\Models\User::where(array(array('is_drm_user', '=', 1)))->get()->toArray();
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            $data['logged_in_user_id'] = $this->logged_in_user_id;
            return view('reports/project-focus-partner')->with($data);
        }
    }

    public function getReportofProjectEmergePartner(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // if data is posted then showing distributors list in datatable or exporting them as per input parameters
            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
                // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }

            // Read value from Model method
            // print_r($request->all());
            $partnersData =ReportModel::getProjectEmergePartnerList(array_merge($request->all(),
                                                                           array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                                 'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                                 'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                                 'logged_in_user_id' => $this->logged_in_user_id,
                                                                                 'show_only_logged_in_user_data' => true)
                                                                       )
                                                            );

            if(!$partnersData['records']->isEmpty()){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $partnersData['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $partnersData['records'];
                    echo json_encode($output_arr);
                }
                else{
                    // when exporting data, preparing HEADER ROW for an informative purpose
                    $csv_headers = array_column($this->data_table_headings_project_emerge, 'label');
                    // array_shift($csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    $output_arr[] = $csv_headers;
                    foreach($partnersData['records'] as $key => $value){
                        $row = array();
                        foreach($this->data_table_headings_project_emerge as $field_name_key => $field_name_value){
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
                    return \Excel::download(new ArrayRecordsExport($output_arr),'project_emerge_partner_master_data_'. date('Ymd').'.xlsx');
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
            $data = array('data_table_headings_project_emerge' => $this->data_table_headings_project_emerge);
            $data['UserDetails'] = \App\Models\User::where(array(array('is_drm_user', '=', 1)))->get()->toArray();
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            $data['logged_in_user_id'] = $this->logged_in_user_id;
            return view('reports/project-emerge-partner')->with($data);
        }
    }

    public function getPartnerwithAumButNoTransactions(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // if data is posted then showing distributors list in datatable or exporting them as per input parameters
            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
                // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }

            // Read value from Model method
            // print_r($request->all());
            $partnersData =ReportModel::getPartnerWithuAUMList(array_merge($request->all(),
                                                                           array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                                 'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                                 'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                                 'logged_in_user_id' => $this->logged_in_user_id,
                                                                                 'show_only_logged_in_user_data' => true)
                                                                       )
                                                            );

            if(!$partnersData['records']->isEmpty()){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $partnersData['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $partnersData['records'];
                    echo json_encode($output_arr);
                }
                else{
                    // when exporting data, preparing HEADER ROW for an informative purpose
                    $csv_headers = array_column($this->data_table_headings_partner_aum_no_transactions, 'label');
                    // array_shift($csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    $output_arr[] = $csv_headers;
                    foreach($partnersData['records'] as $key => $value){
                        $row = array();
                        foreach($this->data_table_headings_partner_aum_no_transactions as $field_name_key => $field_name_value){
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
                    return \Excel::download(new ArrayRecordsExport($output_arr),'partner_aum_with_no_transaction_master_data_'. date('Ymd').'.xlsx');
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
            $data = array('data_table_headings_partner_aum_no_transactions' => $this->data_table_headings_partner_aum_no_transactions);
            $data['UserDetails'] = \App\Models\User::where(array(array('is_drm_user', '=', 1)))->get()->toArray();
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            $data['logged_in_user_id'] = $this->logged_in_user_id;
            return view('reports/partner-aum-no-transaction')->with($data);
        }
    }

    public function getPartnerwithAumButNoActiveSIP(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // if data is posted then showing distributors list in datatable or exporting them as per input parameters
            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
                // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }

            // Read value from Model method
            // print_r($request->all());
            $partnersData =ReportModel::getPartnerWithuAUMNoSipList(array_merge($request->all(),
                                                                           array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                                 'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                                 'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                                 'logged_in_user_id' => $this->logged_in_user_id,
                                                                                 'show_only_logged_in_user_data' => true)
                                                                       )
                                                            );

            if(!$partnersData['records']->isEmpty()){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $partnersData['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $partnersData['records'];
                    echo json_encode($output_arr);
                }
                else{
                    // when exporting data, preparing HEADER ROW for an informative purpose
                    $csv_headers = array_column($this->data_table_headings_partner_aum_no_active_sip, 'label');
                    // array_shift($csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    $output_arr[] = $csv_headers;
                    foreach($partnersData['records'] as $key => $value){
                        $row = array();
                        foreach($this->data_table_headings_partner_aum_no_active_sip as $field_name_key => $field_name_value){
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
                    return \Excel::download(new ArrayRecordsExport($output_arr),'partner_aum_with_no_active_sip_master_data_'. date('Ymd').'.xlsx');
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
            $data = array('data_table_headings_partner_aum_no_active_sip' => $this->data_table_headings_partner_aum_no_active_sip);
            $data['UserDetails'] = \App\Models\User::where(array(array('is_drm_user', '=', 1)))->get()->toArray();
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            $data['logged_in_user_id'] = $this->logged_in_user_id;
            return view('reports/partner-aum-no-active-sip')->with($data);
        }
    }
    public function getPartnerwithAumUniqueClient(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // if data is posted then showing distributors list in datatable or exporting them as per input parameters
            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
                // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }

            // Read value from Model method
            // print_r($request->all());
            $partnersData =ReportModel::getPartnerWithuAUMUniqueClient(array_merge($request->all(),
                                                                           array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                                 'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                                 'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                                 'logged_in_user_id' => $this->logged_in_user_id,
                                                                                 'show_only_logged_in_user_data' => true)
                                                                       )
                                                            );

            if(!$partnersData['records']->isEmpty()){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $partnersData['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $partnersData['records'];
                    echo json_encode($output_arr);
                }
                else{
                    // when exporting data, preparing HEADER ROW for an informative purpose
                    $csv_headers = array_column($this->data_table_headings_partner_aum_unique_client, 'label');
                    // array_shift($csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    $output_arr[] = $csv_headers;
                    foreach($partnersData['records'] as $key => $value){
                        $row = array();
                        foreach($this->data_table_headings_partner_aum_unique_client as $field_name_key => $field_name_value){
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
                    return \Excel::download(new ArrayRecordsExport($output_arr),'partner_aum_with_unique_clients_master_data_'. date('Ymd').'.xlsx');
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
            $data = array('data_table_headings_partner_aum_unique_client' => $this->data_table_headings_partner_aum_unique_client);
            $data['UserDetails'] = \App\Models\User::where(array(array('is_drm_user', '=', 1)))->get()->toArray();
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            $data['logged_in_user_id'] = $this->logged_in_user_id;
            return view('reports/partner-aum-unique-pan')->with($data);
        }
    }

    public function getAumTransactionAnalyitcs(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // if data is posted then showing distributors list in datatable or exporting them as per input parameters
            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
                // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }

            // Read value from Model method
            $flag = 0;
            if(isset($request['view_to_be_loaded']) && !empty($request['view_to_be_loaded'])  && $request['view_to_be_loaded'] == 'date_wise_data' && $flag == 1){
                $myarray['searched_columns']['broker_id'] = array("value" => ($request['arn_number']??''), "table_alias" => "investor_order");
                $myarray['searched_columns']['global_search'] = array("value" => ($request['global_search']??''),"searched_fields" => array("full_name","folio_number"));
                $myarray['searched_columns']['created_at']  =  array("value" => $request['selected_date'].";".$request['selected_date'],"table_alias" => "investor_order");
                $myarray['searched_columns']['order_status'] = array("value" => 2,"table_alias" => "investor_order");
                $myarray['searched_columns']['scheme_code']  =  array("value" => ($request['scheme_code']??''),"table_alias" => "investor_order");
                $myarray['searched_columns']['order_type']  =  array("value" => ($request['order_type']??''),"table_alias" => "investor_order");
                $myarray['pagination_required'] = ($request['pagination_required']??true);
                $myarray['get_all_arn_data'] = ($request['get_all_arn_data']??'');
                $myarray['start'] = ($request['start']??0);
                $myarray['length'] = ($request['length']??10);
                if(!empty($myarray['length'])){
                    $myarray['start'] = $myarray['start'] / $myarray['length'];
                }
                $myarray['searched_columns'] = json_encode($myarray['searched_columns']);
                $jsonmyarray =  json_encode($myarray);    
                $api_request_headers = array('Content-Length:'. strlen($jsonmyarray), 'Content-Type:application/json', 'Authorization:Bearer '. getSettingsTableValue('1960295626_SAMCOMF_PARTNERS_API_TOKEN'), 'Accept:application/json');
                $partnersData = get_content_by_curl(env("SAMCOMF_STATIC_WEB_URL").'/api/order-transactions', $jsonmyarray, $api_request_headers);
                $partnersData = json_decode($partnersData, true);
            }
            else{
                $partnersData =AumdataModel::getAumTransactionAnalytics(array_merge($request->all(),
                    array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                        'flag_have_all_permissions' => $this->flag_have_all_permissions,
                        'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                        'logged_in_user_id' => $this->logged_in_user_id,
                        'show_only_logged_in_user_data' => true)
                    )
                );
            }

            if(!empty($partnersData['records'])){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $partnersData['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $partnersData['records'];
                    echo json_encode($output_arr);
                }
                else{
                    // when exporting data, preparing HEADER ROW for an informative purpose
                    $export_csv_headers = $this->data_table_headings_aum_transactions_analytics;
                   
                    if(isset($request['view_to_be_loaded']) && ($request['view_to_be_loaded'] == 'date_wise_data')){
                        $export_csv_headers = array('action' => array('label' => 'Action'),
                            'ARN' => array('label' => 'ARN'),
                            'schemename' => array('label' => 'Scheme'),
                            'folio_number' => array('label' => 'Folio'),
                            'clientname' => array('label' => 'Investor'),
                            'trxn_type_name' => array('label' => 'Trx Type'),
                            'sub_trxntype_name'=>array('label' =>'Order Type'),
                            'units'=>array('label' =>'Units'),
                            'amount'=>array('label' =>'Amount'),
                            'nav'=>array('label' =>'NAV'),
                            'trdt_date'=>array('label' =>'Created'),
                            //'order_response'=>array('label' =>'Status'),
                        );
                    }
                    else if($request['view_to_be_loaded'] == 'month_wise_data'){
                        $export_csv_headers = array('action' => array('label' => 'Action'),
                            'ARN' => array('label' => 'ARN'),
                            'asset_type' => array('label' => 'Asset Type'),
                            'trdt_month' => array('label' => 'Month'),
                            'total_gross_inflow' => array('label' => 'Total Gross Inflows'),
                            'total_redemptions' => array('label' => 'Total Redemptions'),
                            'total_netflow' => array('label' => 'Total Net Inflow'),
                            'total_aum' => array('label' => 'Total AUM'),
                        );
                    }
                    else if($request['view_to_be_loaded'] == 'day_wise_data'){
                        $export_csv_headers = array('action' => array('label' => 'Action'),
                            'ARN' => array('label' => 'ARN'),
                            'asset_type' => array('label' => 'Asset Type'),
                            'trdt_date' => array('label' => 'Date'),
                            'total_gross_inflow' => array('label' => 'Total Gross Inflows'),
                            'total_redemptions' => array('label' => 'Total Redemptions'),
                            'total_netflow' => array('label' => 'Total Net Inflow'),
                            'total_aum' => array('label' => 'Total AUM'),
                        );
                    }
                    $csv_headers = array_column($export_csv_headers, 'label');
                    array_shift($csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    $output_arr[] = $csv_headers;
                    foreach($partnersData['records'] as $key => $value){
                        $row = array();
                        foreach($export_csv_headers as $field_name_key => $field_name_value){
                            // skipping unnecessary fields during exporting of records
                            if(in_array($field_name_key, array('action')) !== FALSE){
                                continue;
                            }
                            $row[$field_name_key] = '';
                            if(isset($value->$field_name_key)){
                                $row[$field_name_key] = $value->$field_name_key;
                            }
                            elseif(isset($value->field_name_key)){
                                $row->field_name_key = $value->field_name_key;
                            }
                        }
                        $output_arr[] = $row;
                        unset($field_name_key, $field_name_value);
                    }
                    unset($key, $value, $csv_headers, $export_csv_headers);
                    // makes data available in a CSV file format to user
                    return \Excel::download(new ArrayRecordsExport($output_arr),'aum_transactions_master_data_'. date('Ymd').'.xlsx');
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
    }

    public function getSipAnalyitcs(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // x($request->all());
            // if data is posted then showing distributors list in datatable or exporting them as per input parameters
            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
                // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }

            // Read value from Model method
            // x($request->all());

                $partnersData =ReportModel::getSipAnalytics(array_merge($request->all(),
                                                                array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                    'logged_in_user_id' => $this->logged_in_user_id,
                                                                    'show_only_logged_in_user_data' => true)
                                                                )
                                                                );
        


            if(!empty($partnersData['records'])){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $partnersData['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $partnersData['records'];
                    echo json_encode($output_arr);
                }
                else{
                    // when exporting data, preparing HEADER ROW for an informative purpose
                    $csv_headers = $this->data_table_headings_sip_analytics;
                    if(isset($request['view_to_be_loaded']) && ($request['view_to_be_loaded'] == 'month_wise_data')){
                        $csv_headers = array('action' => array('label' => 'Action'),
                                                    'ARN' => array('label' => 'ARN'),
                                                    'sip_registration_year' => array('label' => 'SIP Registration Year'),
                                                    'sip_registration_month' => array('label' => 'SIP Registration Month'),
                                                    'sip_registration_amount' => array('label' => 'SIP Registration Amount'),
                                                    'no_of_sip' => array('label' => 'No of SIP'),
                                                    'sip_live_amount'=>array('label' =>'SIP Live Amount'),
                                                    'no_of_live_sip'=>array('label' =>'No of Live SIP'),
                                                    // 'sip_pending_registration_amount'=>array('label' =>'SIP Pending Registration Amount'),
                                                    // 'no_of_pending_registration_sip'=>array('label' =>'No of Pending SIP Registration'),
                                                    'sip_closures_amount'=>array('label' =>'SIP Closures Amount'),
                                                    'no_of_closed_sip'=>array('label' =>'No of Closed SIP'),
                                            );
                    }
                    elseif(isset($request['view_to_be_loaded']) && ($request['view_to_be_loaded'] == 'day_wise_data')){
                        $csv_headers = array('action' => array('label' => 'Action'),
                                                    'ARN' => array('label' => 'ARN'),
                                                    'scheme_name' => array('label' => 'Scheme'),
                                                    'client_name' => array('label' => 'Client Name'),
                                                    'pan' => array('label' => 'PAN'),
                                                    'sip_registered_since' => array('label' => 'SIP Registererd Since'),
                                                    'umrncode' => array('label' => 'UMRN Code'),
                                                    'client_aum'=>array('label' =>'Current AUM'),
                                                    'sip_amount'=>array('label' =>'SIP Amount'),
                                                    'sip_status'=>array('label' =>'Status'),
                                            );
                    }
					elseif(isset($request['view_to_be_loaded']) && ($request['view_to_be_loaded'] == 'year_wise_data')){
                        $csv_headers = array('action' => array('label' => 'Action'),
                                                    'ARN' => array('label' => 'ARN'),
                                                    'asset_type' => array('label' => 'Asset Type'),
                                                    'order_status' => array('label' => 'Status'),
                                                    'installment_amount' => array('label' => 'Installment Amount'),
                                                    'no_of_sip' => array('label' => 'No of SIP'),
                                             
                                            );
                    }
                    $export_csv_headers = array_column($csv_headers, 'label');
                    array_shift($export_csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    $output_arr[] = $export_csv_headers;

                    foreach($partnersData['records'] as $key => $value){
                        $row = array();
                        foreach($csv_headers as $field_name_key => $field_name_value){
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
                    unset($key, $value, $csv_headers,$export_csv_headers);
                    // makes data available in a CSV file format to user
                    return \Excel::download(new ArrayRecordsExport($output_arr),'sip_analytics_master_data_'. date('Ymd').'.xlsx');
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
    }

    public function getClientAnalyitcs(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // x($request->all());
            // if data is posted then showing distributors list in datatable or exporting them as per input parameters
            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
                // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }

            // Read value from Model method
            // x($request->all());

                $partnersData =ReportModel::getClientAnalytics(array_merge($request->all(),
                                                                array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                    'logged_in_user_id' => $this->logged_in_user_id,
                                                                    'show_only_logged_in_user_data' => true)
                                                                )
                                                                );
        

            if(!empty($partnersData['records'])){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $partnersData['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $partnersData['records'];
                    echo json_encode($output_arr);
                }
                else{
                     // when exporting data, preparing HEADER ROW for an informative purpose
                     $csv_headers = $this->data_table_headings_client_analytics;
                     if(isset($request['view_to_be_loaded']) && ($request['view_to_be_loaded'] == 'month_wise_data')){
                         $csv_headers = array('action' => array('label' => 'Action'),
                                                     'ARN' => array('label' => 'ARN'),
                                                     'asset_type' => array('label' => 'Asset Type'),
                                                     'clientname' => array('label' => 'Client Name'),
                                                     'pan' => array('label' => 'PAN'),
                                                     'active_sip_registration_amount' => array('label' => 'Active SIP Registration Amount'),
                                                     'total_gross_inflow' => array('label' => 'Total Gross Inflows'),
                                                     'total_redemptions'=>array('label' =>'Totat Redemptions'),
                                                     'total_netflow'=>array('label' =>'Total NetInflow'),
                                                     'total_aum'=>array('label' =>'Total AUM'),
                                                     'last_transaction_date'=>array('label' =>'Last Transaction Date'),
                                             );
                     }
                     $export_csv_headers = array_column($csv_headers, 'label');
                     array_shift($export_csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                     $output_arr[] = $export_csv_headers;
 
                     foreach($partnersData['records'] as $key => $value){
                         $row = array();
                         foreach($csv_headers as $field_name_key => $field_name_value){
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
                     unset($key, $value, $csv_headers,$export_csv_headers);
                    // makes data available in a CSV file format to user
                    return \Excel::download(new ArrayRecordsExport($output_arr),'client_analytics_master_data_'. date('Ymd').'.xlsx');
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
    }

    public function getDaywiseTransactionAnalyitcs(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // if data is posted then showing distributors list in datatable or exporting them as per input parameters
            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
                // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }

            // Read value from Model method
            $partnersData =ReportModel::getDaywiseTransactionAnalytics(array_merge($request->all(),
                                                            array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                'logged_in_user_id' => $this->logged_in_user_id,
                                                                'show_only_logged_in_user_data' => true)
                                                            )
                                                        );
            // exporting data using shell script
            if($flag_export_data){
                $where_conditions = '';
                $order_by_clause = '';
                if(isset($partnersData['where_conditions']) && is_array($partnersData['where_conditions']) && count($partnersData['where_conditions']) > 0){
                    foreach($partnersData['where_conditions'] as $condition){
                        $where_conditions .= $condition[0] ." ". $condition[1] ." '". addslashes($condition[2]) ."' AND ";
                    }
                    unset($condition);
                }
                if(isset($partnersData['where_in_conditions']) && is_array($partnersData['where_in_conditions']) && count($partnersData['where_in_conditions']) > 0){
                    foreach($partnersData['where_in_conditions'] as $in_condition_field => $in_condition_data){
                        $where_conditions .= $in_condition_field ." IN (". implode(',', $in_condition_data) .") AND ";
                    }
                    unset($in_condition_field, $in_condition_data);
                }
                $where_conditions = trim($where_conditions);
                if(substr($where_conditions, -3) == 'AND'){
                    $where_conditions = substr($where_conditions, 0, -3);
                }
                if(isset($partnersData['order_by_clause']) && !empty($partnersData['order_by_clause'])){
                    $order_by_clause = $partnersData['order_by_clause'];
                }

                if(!empty($where_conditions)){
                    $where_conditions = ' WHERE '. $where_conditions;
                }
                if(!empty($order_by_clause)){
                    $order_by_clause = ' ORDER BY '. $order_by_clause;
                }

                exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/export_csv_data.sh -i"daywise_transaction_analytics" "'. $where_conditions .'" "'. $order_by_clause .'"', $return_var, $exit_code);
                if($exit_code != 0 || (isset($return_var[1]) && empty(intval(trim($return_var[1]))))){
                    // coming here if we face any error while exporting the data
                    // as data is requested as an EXPORT action, so displaying message and closing the newly open window
                    ?><script>alert('No records found');window.close();</script><?php
                }
                else{
                    ?><script>window.open('<?php echo env('APP_URL') . str_replace(get_server_document_root(true).'/public', '', $return_var[0]); ?>', '_parent');window.setTimeout(function(){window.close();}, 1000);</script><?php
                }
                unset($where_conditions, $order_by_clause);
                return false;
            }

            if(!empty($partnersData['records'])){
                // showing data in JSON format
                $output_arr['recordsTotal'] = $partnersData['no_of_records'];
                $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                $output_arr['data'] = $partnersData['records'];
                echo json_encode($output_arr);
            }
            else{
                // coming here if no records are retrieved from MySQL table: distributor_master
                // displaying data in DataTable format
                echo json_encode($output_arr);
            }
        }
        else{
            $data = array('data_table_headings_daywise_transactions_analytics' => $this->data_table_headings_daywise_transactions_analytics);
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            $data['logged_in_user_id'] = $this->logged_in_user_id;
            return view('reports/day-wise-transaction-analytics')->with($data);
        }
    }

    public function getClientMonthwiseAnalytics(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // if data is posted then showing distributors list in datatable or exporting them as per input parameters
            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
                // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }
			// x($request->all());
            // Read value from Model method
            $partnersData =ReportModel::getClientMonthwiseAnalytics(array_merge($request->all(),
                                                            array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                'logged_in_user_id' => $this->logged_in_user_id,
                                                                'show_only_logged_in_user_data' => true)
                                                            )
                                                        );
            if(!empty($partnersData['records'])){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $partnersData['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $partnersData['records'];
                    echo json_encode($output_arr);
                }
                else{
                    // when exporting data, preparing HEADER ROW for an informative purpose
                    $csv_headers = array_column($this->data_table_headings_client_monthwise_analytics, 'label');
                    // array_shift($csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    $output_arr[] = $csv_headers;
                    foreach($partnersData['records'] as $key => $value){
                        $row = array();
                        foreach($this->data_table_headings_client_monthwise_analytics as $field_name_key => $field_name_value){
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
                    return \Excel::download(new ArrayRecordsExport($output_arr),'client_monthwise_analytics_master_data_'. date('Ymd').'.xlsx');
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
    }

    public function getBDMMonthwiseInflows(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // if data is posted then showing distributors list in datatable or exporting them as per input parameters
            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
                // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }

            // Read value from Model method
            $partnersData =ReportModel::getBDMMonthwiseInflows(array_merge($request->all(),
                                                            array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                'logged_in_user_id' => $this->logged_in_user_id,
                                                                'show_only_logged_in_user_data' => true)
                                                            )
                                                        );
            if(is_array($partnersData['records']) && count($partnersData['records']) > 0){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $partnersData['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $partnersData['records'];
                    echo json_encode($output_arr);
                }
                else{
                     // when exporting data, preparing HEADER ROW for an informative purpose
                     $csv_headers = $this->data_table_headings_bdmwise_inflows;
                     $export_csv_headers = array_column($csv_headers, 'label');
                     $output_arr[] = $export_csv_headers;

                     foreach($partnersData['records'] as $key => $value){
                         $row = array();
                         foreach($csv_headers as $field_name_key => $field_name_value){
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
                     unset($key, $value, $csv_headers,$export_csv_headers);
                    // makes data available in a CSV file format to user
                    return \Excel::download(new ArrayRecordsExport($output_arr),'month_wise_bdm_wise_data_'. date('Ymd').'.xlsx');
                }
            }
            else{
                // coming here if no records are retrieved
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
            $data = array('data_table_headings_bdmwise_inflows' => $this->data_table_headings_bdmwise_inflows);
            $data['arr_schemes'] = \App\Models\SchemeMasterModel::where(array(
                                                                            array('scheme_flag', '=', 1),
                                                                            array('Scheme_Type', '=', 'Equity')
                                                                        )
                                                                    )
                                                                    ->select(array('scheme', 'Scheme_Code', 'SETTLEMENT_TYPE'))
                                                                    ->groupBy('Scheme_Code')
                                                                    ->orderBy('nfo_end_date', 'DESC')
                                                                    ->get()
                                                                    ->toArray();
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            $data['logged_in_user_id'] = $this->logged_in_user_id;
            return view('reports/bdm-wise-monthly-inflows')->with($data);
        }
    }
}
