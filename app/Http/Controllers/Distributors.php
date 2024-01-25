<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\ArrayRecordsExport;
use Illuminate\Support\Facades\Validator;
use App\Models\DistributorsModel;
use App\Models\DistributorCategoryMasterModel;
use App\Models\RelationshipqualityModel;
use App\Models\UsermasterModel;
use Illuminate\Support\Facades\DB;

class Distributors extends Controller
{
    protected $data_table_headings, $rankmf_stage_of_prospect, $samcomf_stage_of_prospect;
    protected $logged_in_user_roles_and_permissions, $flag_have_all_permissions, $flag_show_all_arn_data, $logged_in_user_id;
    public function __construct(){
        $this->middleware('auth');
        $this->data_table_headings = array('action' => array('label' => 'Action'),
                                           'ARN' => array('label' => 'AMFI - ARN'),
										   'is_rankmf_partner' => array('label' => 'RankMF Partner (yes/no)'),
                                           'arn_holders_name' => array('label' => 'AMFI - ARN Holder\'s Name'),
                                           'arn_address' => array('label' => 'AMFI - Address'),
                                           'arn_pincode' => array('label' => 'AMFI - Pin'),
                                           'arn_email' => array('label' => 'AMFI - Email'),
                                           'arn_city' => array('label' => 'AMFI - City'),
                                           'arn_telephone_r' => array('label' => 'AMFI - Telephone (R)'),
                                           'arn_telephone_o' => array('label' => 'AMFI - Telephone (O)'),
										   'arn_valid_from' => array('label' => 'AMFI - ARN Valid From'),
                                           'arn_valid_till' => array('label' => 'AMFI - ARN Valid Till'),
                                           'arn_kyd_compliant' => array('label' => 'AMFI - KYD Compliant'),
                                           'arn_euin' => array('label' => 'AMFI - EUIN'),
                                           'distributor_category' => array('label' => 'Distributor Category'),
                                           'reporting_to_name' => array('label' => 'Reporting Manager Name'),
                                           'alternate_mobile_1' => array('label' => 'Alternate Mobile'),
                                        //    'alternate_mobile_2' => array('label' => 'Alternate Mobile 2'),
                                        //    'alternate_mobile_3' => array('label' => 'Alternate Mobile 3'),
                                           'alternate_email_1' => array('label' => 'Alternate EMail'),
                                        //    'alternate_email_2' => array('label' => 'Alternate EMail 2'),
                                        //    'alternate_email_3' => array('label' => 'Alternate EMail 3'),
                                           'arn_avg_aum' => array('label' => 'ARN Average AUM - Last Reported (In Lakhs)'),
											/*
                                           'pincode_city' => array('label' => 'City'),
                                           'arn_state' => array('label' => 'ARN State'),
                                           'arn_zone' => array('label' => 'Zone'),
                                           'relationship_quality_with_arn' => array('label' => 'Quality of Relationship with ARN'),
                                           'project_focus' => array('label' => 'Project Focus (yes/no)'),
                                           'project_emerging_stars' => array('label' => 'Project Emerging Stars (yes/no)'),
                                           'project_green_shoots' => array('label' => 'Project Green Shoots (yes/no)'),
                                           'arn_total_commission' => array('label' => 'ARN Total Commission - Last Reported'),
                                           'arn_yield' => array('label' => 'ARN Yield'),
                                           'arn_business_focus_type' => array('label' => 'ARN Business Focus Type'),
                                           'rankmf_partner_aum' => array('label' => 'RankMF Partner AUM'),
                                           'samcomf_live_sip_amount' => array('label' => 'SamcoMF Partner Live SIP Installment Amount'),
                                           'samcomf_partner_netinflow' => array('label' => 'SamcoMF Partner Net Inflow'),
                                           'samcomf_partner_aum' => array('label' => 'SamcoMF Partner AUM'),
                                           'total_aum' => array('label' => 'Total AUM (RankMF + SamcoMF)'),
                                           'total_ind_aum' => array('label' => 'Total Industry AUM (In Crores)'),
                                           'ind_aum_as_on_date' => array('label' => 'Total industry aum as on date'),
										   */
                                           'bdm_name' => array('label' => 'Relationship Manager Name'),
                                           'bdm_email' => array('label' => 'Relationship Manager Email'),
                                           'bdm_mobile' => array('label' => 'Relationship Manager Mobile Number'),
                                           'bdm_designation' => array('label' => 'Relationship Manager Designation'),
                                           'reporting_to_email' => array('label' => 'Reporting Manager Email'),
										   /*
                                           'reporting_to_mobile' => array('label' => 'Reporting Manager Mobile'),
                                           'reporting_to_designation' => array('label' => 'Reporting Manager Designation'),
                                           'rm_relationship' => array('label' => 'RM Relationship Flag'),
										   */
                                        //    'is_rankmf_partner' => array('label' => 'RankMF Partner (yes/no)'),
										   /*
                                           'rankmf_email' => array('label' => 'RankMF Email'),
                                           'rankmf_mobile' => array('label' => 'RankMF Mobile'),
                                           'rankmf_stage_of_prospect' => array('label' => 'RankMF Stage of Prospect'),
                                           'is_samcomf_partner' => array('label' => 'SamcoMF Partner (yes/no)'),
                                           'samcomf_email' => array('label' => 'SamcoMF Email'),
                                           'samcomf_mobile' => array('label' => 'SamcoMF Mobile'),
                                           'samcomf_stage_of_prospect' => array('label' => 'SamcoMF Stage of Prospect'),
                                           'status' => array('label' => 'Record Status'),
                                           'created_at' => array('label' => 'Record Created Date')
										   */
										   'total_ind_aum' => array('label' => 'Total Industry AUM (In Crores)'),
                                           'rankmf_partner_code' => array('label' => 'RankMF Partner Code'),
                                        );

        $this->data_table_headings_meeting_log = array('action' => array('label' => 'Action'),
                                            'ARN' => array('label' => 'ARN'),
                                            'meeting_mode' => array('label' => 'Meeting Mode'),
                                            'contact_person_name' => array('label' => 'Contact Details'),
                                            'start_datetime'=>array('label' =>'Start Time'),
                                            'end_datetime'=>array('label' =>'End Time'),
                                            'meeting_hour'=>array('label' =>'Meeting Duration (Min)'),
                                            'email_sent_to_customer'=>array('label' =>'Email Sent'),
                                            'sms_sent_to_customer'=>array('label' =>'SMS Sent'),
                                            'customer_response_received_datetime'=>array('label' =>'Customer Response Received Date'),
                                            'customer_response_received'=>array('label' =>'Customer Response Received'),
                                            'customer_given_rating'=>array('label' =>'Customer Rating(1-5)'),
                                            'customer_remarks'=>array('label' =>'Customer Remarks'),
                                            'product_information_received'=>array('label' =>'Product Information Received'),
                                            'is_samcomf_partner'=>array('label' =>'Is Samcomf Partner'),
                                            'total_ind_aum'=>array('label' =>'Total Industry AUM (In Crores)'),
                                            'last_meeting_date'=>array('label' =>'Last Meeting Date'),
                                            'bdm_name'=>array('label' =>'Created By'),
                                        );
        $this->data_table_headings_aum_transactions_analytics = array('action' => array('label' => 'Action'),
                                            'ARN' => array('label' => 'ARN'),
                                            'asset_type' => array('label' => 'Asset Type'),
                                            'total_gross_inflow' => array('label' => 'Total Gross Inflows'),
                                            'total_redemptions' => array('label' => 'Total Redemptions'),
                                            'total_netflow' => array('label' => 'Total Net Inflow'),
                                            'total_aum' => array('label' => 'Total AUM'),
                                                            );
        $this->data_table_headings_sip_analytics = array('action' => array('label' => 'Action'),
                                                            'agent_code' => array('label' => 'ARN'),
                                                            'asset_type' => array('label' => 'Asset Type'),
                                                            'status' => array('label' => 'Status'),
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
        $this->data_table_headings_client_monthwise_analytics = array('action' => array('label' => 'Action'),
                                                                    'ARN' => array('label' => 'ARN'),
                                                                    'month' => array('label' => 'Month'),
																	'asset_type' => array('label' => 'Asset Type'),
                                                                    'active_clients_with_aum' => array('label' => 'Active Clients with AUM<br>(as on month)'),
                                                                    'new_clients_with_aum' => array('label' => 'New Clients with AUM<br>(in that month)'),
                                                                    'clients_without_aum' => array('label' => 'Clients without AUM<br>(as on month)'),
                                                                        );
        $this->rankmf_stage_of_prospect = array('pan', 'personal', 'communication', 'mobile verification', 'email verification', 'bank', 'arn', 'upload', 'business_detail', 'thank you');
        $this->samcomf_stage_of_prospect = array('Verification', 'Upload ARN', 'Add Bank Details', 'Nominee Details', 'Upload Documents', 'Add Signatories', 'E-sign & Verify', 'Consent', 'Thank You');

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

    /**
     * Author: Prasad Wargad
     * Purpose: JIRA ID: SMF-37. Helps to show list of available distributors
     * Created:
     * Modified:
     * Modified by:
     */
    public function index(Request $request)
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
            $partnersData = DistributorsModel::getDistributorsList(array_merge($request->all(),
                                                                               array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                                     'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                                     'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                                     'logged_in_user_id' => $this->logged_in_user_id)
                                                                           )
                                                                );
			//print_r($partnersData);
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
				// y($where_conditions);
				// x($order_by_clause);

                exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/export_csv_data.sh -i"drm_distributor_master" "'. $where_conditions .'" "'. $order_by_clause .'"', $return_var, $exit_code);
                if($exit_code != 0 || (isset($return_var[1]) && empty(intval(trim($return_var[1]))))){
                    // coming here if we face any error while exporting the data
                    // as data is requested as an EXPORT action, so displaying message and closing the newly open window
                    ?><script>alert('No records found');window.close();</script><?php
                }
                else{
                    ?><script>window.open('<?php echo env('APP_URL') . str_replace(get_server_document_root(true).'/public', '', $return_var[0]); ?>', '_parent');window.setTimeout(function(){window.close();}, 1000);</script><?php
                    //echo ('<a href="'. env('APP_URL') . str_replace(get_server_document_root(true).'/public', '', $return_var[0]) .'">Click here to get the data</a>');
                }
                unset($where_conditions, $order_by_clause);
                return false;
            }

            if(!$partnersData['records']->isEmpty()){
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
            // as no data is posted then loading the view

            // retrieving list of distributor category list
            $distributor_category_records = DistributorCategoryMasterModel::getDistributorCategory();
            // retrieving relationship quality list
            $relationship_quality_records =RelationshipqualityModel::getList(array('order' => array(array('column' => 0,'dir' => 'ASC')), 'columns' => array(array('data' => 'relationship.id'))));
            if(isset($relationship_quality_records['records']) && !$relationship_quality_records['records']->isEmpty()){
                $relationship_quality_records = $relationship_quality_records['records']->toArray();
            }
            else{
                $relationship_quality_records = array();
            }

            $data = array('data_table_headings' => $this->data_table_headings,
                          'distributor_category_records' => $distributor_category_records->toArray(),
                          'rankmf_stage_of_prospect' => $this->rankmf_stage_of_prospect,
                          'samcomf_stage_of_prospect' => $this->samcomf_stage_of_prospect,
                          'relationship_quality_records' => $relationship_quality_records);
            unset($distributor_category_records, $relationship_quality_records);

            // Pass to view
            return view('distributors/list')->with($data);
        }
    }

    /**
     * Author: Maniraj Nadar
     * Purpose: JIRA ID: SMF-40
     * Created: 06/10/2021
     * Modified:
     * Modified by:
     */
    public function view($arn_number){

        $data = array('partner_data' => array(), 'flag_record_found' => false);
		$years = DB:: table('sip_analytics_view')
		->select(['broker_id', 'ARN', DB::raw('YEAR(start_date) as year')])
		->where('ARN', $arn_number)
		->whereRaw('YEAR(start_date) >= 2017')
		->groupBy(['broker_id', 'year'])
		->orderBy('year','desc')
		->get();
		$data['years']=$years;
        $partner_data = DistributorsModel::getDistributorByARN($arn_number,
                                                               array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                    'logged_in_user_id' => $this->logged_in_user_id)
                                                            );
        if(!$partner_data->isEmpty() && isset($partner_data[0]) && is_object($partner_data[0]) && get_object_vars($partner_data[0]) > 0){
            // as data found for an ARN proceeding further with other details like AMC Wise Project Focus details
            $amc_data = DistributorsModel::getAmcWiseDataByARN($arn_number,
                                                               array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                    'logged_in_user_id' => $this->logged_in_user_id)
                                                            );
            $commission_data = DistributorsModel::getCommissionStructureByARN($arn_number);
            // retrieving relationship quality list
            $relationship_quality =RelationshipqualityModel::getList(array('order' => array(array('column' => 0,'dir' => 'ASC')),
                                                                           'columns' => array(array('data' => 'relationship.id'))
                                                                        )
                                                                );
            // retrieving distributor category list
            $distributor_category_records = DistributorCategoryMasterModel::getDistributorCategory();

            // retrieving BDM/ Regional Manager etc. details
            $user_roles_records = UsermasterModel::get_users_having_roles_assigned();

            //retrieving Linked ARN 
            $linked_arn = DistributorsModel::get_linked_arn((array) $partner_data[0]);
            // finding unique list of roles available against users
            $unique_user_roles = array_unique(array_column($user_roles_records, 'role_name'));
            // sorting the available roles in an ascending order
            sort($unique_user_roles);

            $list_of_users = array();
            if(is_array($user_roles_records) && count($user_roles_records) > 0){
                foreach($unique_user_roles as $role){
                    if(!isset($list_of_users[$role])){
                        $list_of_users[$role] = array();
                    }

                    foreach($user_roles_records as $user_record){
                        if($user_record->role_name == $role){
                            $list_of_users[$role][] = array('key' => $user_record->id, 'value' => $user_record->name, 'reporting_to_name' => $user_record->reporting_to_name);
                        }
                    }
                    unset($user_record);
                }
                unset($role);
            }
            unset($user_roles_records, $unique_user_roles);

			// To resolve ucfirst error on null value
			foreach($partner_data[0] as &$value){
				if(is_null($value)){
					$value = '';
				}
			}

            $data['partner_data'] = $partner_data[0];
            $data['arn_number'] = $arn_number;
            $data['amc_data'] = $amc_data;
            $data['commission_data'] = $commission_data;
            $data['linked_arn'] = $linked_arn;
            $data['relationship_quality'] = array();
            if(isset($relationship_quality['records']) && !$relationship_quality['records']->isEmpty()){
                $data['relationship_quality'] = $relationship_quality['records']->toArray();
            }
            $data['distributor_category_records'] = array();
            if(isset($distributor_category_records) && !$distributor_category_records->isEmpty()){
                $data['distributor_category_records'] = $distributor_category_records->toArray();
            }
            $transaction_type =  DB::table('transaction_type')->distinct()->select('type_of_transaction')->get()->toArray();
            // retrieves all transaction type as values of an array
            $transaction_type = array_column($transaction_type, 'type_of_transaction');
            // wanted to have same array elements as KEYS, so used array_combine
            $transaction_type = array_combine($transaction_type, $transaction_type);
            // also wanted to keep array KEYS as LOWER CASE, so used array_change_key_case
            $transaction_type = array_change_key_case($transaction_type, CASE_LOWER);
            $data['list_of_users'] = $list_of_users;
            $data['flag_record_found'] = true;
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            $data['meeting_log_heading'] = $this->data_table_headings_meeting_log;
            $data['aum_transactions_heading'] = $this->data_table_headings_aum_transactions_analytics;
            $data['sip_analytics_heading'] = $this->data_table_headings_sip_analytics;
            $data['client_analytics_heading'] = $this->data_table_headings_client_analytics;
            $data['client_monthwise_analytics_heading'] = $this->data_table_headings_client_monthwise_analytics;
            $data['logged_in_user_id'] = $this->logged_in_user_id;
            $data['arn_visiting_card_images_folder'] = config('constants.ARN_VISITING_CARD_IMAGES');
            $data['getListofYears'] = getListofYears();
            rsort($data['getListofYears']);
			$data['getSettingsTableYear'] = getSettingsTableYear();
			rsort($data['getSettingsTableYear']);
            $data['transaction_type'] = $transaction_type;
            // print_r($data);
        }
        return view('distributors/view')->with($data);
    }

    /**
     * Author: Maniraj Nadar
     * Purpose: JIRA ID: SMF-40
     * Created: 06/10/2021
     * Modified:
     * Modified by:
     */
    public function UpdateByArn(Request $request){
        $data = $request->all();
        extract($data);

        $err_flag = 0;              // err_flag is 0 means no error
        $err_msg = array();         // err_msg stores list of errors found during execution

        $update_data = array();
        $where_conditions = array();
        switch($updating_field){
            case 'distributor_category':
                $validator = Validator::make($data, [
                    'dcategory' => 'required'
                ]);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array('distributor_category' => $data['dcategory']);
                }
            break;
            case 'project_focus':
                $validator = Validator::make($data, [
                    'pfocus' => 'required'
                ]);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array('project_focus' => $data['pfocus']);
                }
            break;
            case 'is_rankmf_partner':
                $validator = Validator::make($data, [
                    'rankmfp' => 'required'
                ]);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array('is_rankmf_partner' => $data['rankmfp']);
                }
            break;
            case 'is_partner_active_on_rankmf':
                $validator = Validator::make($data, [
                    'rankmfap' => 'required'
                ]);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array('is_partner_active_on_rankmf' => $data['rankmfap']);
                }
            break;
            case 'is_samcomf_partner':
                $validator = Validator::make($data, [
                    'samcop' => 'required'
                ]);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array('is_samcomf_partner' => $data['samcop']);
                }
            break;
            case 'is_partner_active_on_samcomf':
                $validator = Validator::make($data, [
                    'samcoap' => 'required'
                ]);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array('is_partner_active_on_samcomf' => $data['samcoap']);
                } 
            break;
            case 'product_approval_person_name':
                $validator = Validator::make($data, [
                    'papprovename' => 'required'
                ]);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array('product_approval_person_name' => $data['papprovename']);
                } 
            break;
            case 'product_approval_person_mobile':
                $validator = Validator::make($data, [
                    'papprovemobile' => 'required'
                ]);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array('product_approval_person_mobile' => $data['papprovemobile']);
                } 
            break;
            case 'product_approval_person_email':
                $validator = Validator::make($data, [
                    'papproveemail' => 'required'
                ]);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array('product_approval_person_email' => $data['papproveemail']);
                } 
            break;
            case 'sales_drive_person_name':
                $validator = Validator::make($data, [
                    'salesdriveename' => 'required'
                ]);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array('sales_drive_person_name' => $data['salesdriveename']);
                } 
            break;
            case 'sales_drive_person_mobile':
                $validator = Validator::make($data, [
                    'salesdrivemobile' => 'required'
                ]);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array('sales_drive_person_mobile' => $data['salesdrivemobile']);
                } 
            break;
            case 'sales_drive_person_email':
                $validator = Validator::make($data, [
                    'salesdriveemail' => 'required'
                ]);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array('sales_drive_person_email' => $data['salesdriveemail']);
                } 
            break;
            case 'alternate_name_1':
            case 'alternate_name_2':
            case 'alternate_name_3':
            case 'alternate_name_4':
            case 'alternate_name_5':
            case 'alternate_mobile_1':
            case 'alternate_mobile_2':
            case 'alternate_mobile_3':
            case 'alternate_mobile_4':
            case 'alternate_mobile_5':
            case 'alternate_email_1':
            case 'alternate_email_2':
            case 'alternate_email_3':
            case 'alternate_email_4':
            case 'alternate_email_5':
            case 'rm_relationship':
            case 'relationship_quality_with_sales_person':
            case 'relationship_quality_with_product_approver':
            case 'direct_relationship_user_id':
            case 'project_emerging_stars':
            case 'project_green_shoots':
            case 'relationship_quality_with_arn':
            case 'front_visiting_card_image':
            case 'back_visiting_card_image':
                $field_validations = 'required';
                $allowed_image_mime_types = 'image/jpeg,image/jpg,image/gif,image/bmp,image/png';
                $custom_valiation_messages = array($updating_field .'.required' => (!empty($updating_field_label)?ucfirst($updating_field_label):'Field').' value is required');
                if(strpos($updating_field, 'alternate_name_') !== FALSE){
                    $field_validations .= '|string|min:3';
                }
                elseif(strpos($updating_field, 'alternate_mobile_') !== FALSE){
                    $field_validations .= '|regex:/^[1-9][0-9]{9}$/i';
                }
                elseif(strpos($updating_field, 'alternate_email_') !== FALSE){
                    $field_validations .= '|email:filter';
                }
                elseif(in_array($updating_field, array('front_visiting_card_image', 'back_visiting_card_image')) !== FALSE){
                    $field_validations .= '|file|max:5120|mimetypes:image/jpeg,image/jpg,image/gif,image/bmp,image/png';
                    $custom_valiation_messages[$updating_field.'.max'] = 'Maximum file size should not exceed 5 MB';
                    $custom_valiation_messages[$updating_field.'.mimetypes'] = 'Only PNG/JPG/GIF file is allowed';
                }

                $validator = Validator::make($data, [
                    $updating_field => $field_validations
                ], $custom_valiation_messages);

                if($validator->fails()){
                    $err_flag = 1;
                    // preparing error message needs to be shown in frontend
                    $err_msg = array_merge($err_msg, $validator->errors()->all());
                }
                else{
                    // while uploading files added code here to verify whether file getting uploaded or not.
                    // if not getting uploaded then showing an error
                    if(in_array($updating_field, array('front_visiting_card_image', 'back_visiting_card_image')) !== FALSE){
                        // checking whether already uploaded file is present in folder or not, if present then removing that file
                        $flag_is_old_file_available = false;      // helps to identify whether to remove old file or not
                        $old_file_path = '';                      // stores the path of old file
                        $get_uploaded_file = DistributorsModel::getDistributorByARN($arn_number,
                                                               array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                    'logged_in_user_id' => $this->logged_in_user_id)
                                                            );
                        if(!$get_uploaded_file->isEmpty() && isset($get_uploaded_file[0]) && isset($get_uploaded_file[0]->$updating_field)){
                          if(file_exists(storage_path() .'/'. config('constants.ARN_VISITING_CARD_IMAGES') . $get_uploaded_file[0]->$updating_field)){
                            // setting remove old file path as TRUE
                            $flag_is_old_file_available = true;
                            $old_file_path = $get_uploaded_file[0]->$updating_field;
                          }
                        }
                        unset($get_uploaded_file);

                        // uploading files to specific folders
                        $file_upload_api_url = env('APP_URL') .'/api/admin-file-uploads';
                        $file_removing_api_url = env('APP_URL') .'/api/admin-file-delete';
                        $uploadfile = $request->file($updating_field);
                        $uploadfile_saving_filename = $data['arn_number'] .'_'.uniqid(str_replace('visiting_card_image', '', $updating_field), true). '.'. $uploadfile->getClientOriginalExtension();

                        $response = get_content_by_curl($file_upload_api_url, array('file' => new \CURLFile($uploadfile->getRealPath(), $uploadfile->getClientMimeType(), $uploadfile->getClientOriginalName()),
                            'upload_path' => config('constants.ARN_VISITING_CARD_IMAGES'),
                            'allowed_types' => $allowed_image_mime_types,
                            'max_size' => (1024 * 5),
                            'file_extension' => $uploadfile->getClientOriginalExtension(),
                            'saving_file_name' => $uploadfile_saving_filename)
                        );
                        if(!empty($response) && json_decode($response) !== FALSE){
                          $response = json_decode($response, true);
                          if($response['status'] == 'success'){
                            $data[$updating_field] = $uploadfile_saving_filename;
                            // removing file as old file details available flag is TRUE and file path is present
                            if($flag_is_old_file_available && !empty($old_file_path)){
                              get_content_by_curl($file_removing_api_url, array('upload_path' => config('constants.ARN_VISITING_CARD_IMAGES'), 'removing_file_name' => $old_file_path));
                            }
                          }
                          else{
                            // showing file uploading errors to user
                            $err_flag = 1;
                            $err_msg = array_merge($err_msg, (is_array($response['data']['errors'])?$response['data']['errors']:array($response['data']['errors'])));
                          }
                        }
                        else{
                          // showing file uploading errors to user
                          $err_flag = 1;
                          $err_msg = array_merge($err_msg, array('Unable to process your request'));
                        }
                        unset($uploadfile_saving_filename, $file_upload_api_url, $uploadfile, $response);
                        unset($flag_is_old_file_available, $old_file_path);
                    }
                }

                if($err_flag == 0){
                    // updating details if everything is correct
                    $where_conditions = array(array('ARN', '=', $data['arn_number']));
                    $update_data = array($updating_field => $data[$updating_field]);

                    // if we are updating BDM against the ARN then checking whether it's relationship flag details were available in the request or not. If details present then updating those details too.
                    if(($updating_field == 'direct_relationship_user_id') && isset($rm_relationship) && !empty($rm_relationship) && in_array($rm_relationship, array('final', 'provisional')) !== FALSE){
                        $update_data['rm_relationship'] = $rm_relationship;
                    }
                    unset($allowed_image_mime_types);
                }
                unset($field_validations);
            break;
        }

        if($err_flag == 0){
            // updating details if everything is correct
            $update = DistributorsModel::UpdateSamcoPByArn(array('where' => $where_conditions,
                                                                'data' => $update_data,
                                                                'arn_number' => $data['arn_number'],
                                                                'logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                'logged_in_user_id' => $this->logged_in_user_id)
                                            );
            if($update['err_flag'] == 0){
                return response()->json([
                    'status' => 'success',
                    'msg' => 'Record Updated Successfully',
                ]);
            }
            else{
              return response()->json([
                  'status' => 'failed',
                  'msg' => $update['err_msg'],
              ]);
            }
        }
        else{
            return response()->json([
                'status' => 'failed',
                'msg' => $err_msg,
            ]);
        }
    }

    /**
     * Author: Maniraj Nadar
     * Purpose: JIRA ID: SMF-40. Function to export ARN & AMC Wise data from distributors view page
     * Created: 06/10/2021
     * Modified:
     * Modified by:
     */
    public function exporttoCSV(Request $request){
        extract($request->all());
        $amc_data = DistributorsModel::getAmcWiseDataByARNToExport(array_merge($request->all(),
                                                                            array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                            'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                            'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                            'logged_in_user_id' => $this->logged_in_user_id)
                                                                        )
                                                                );

        if(!$amc_data->isEmpty()){
            $output_arr[] = array('ARN', 'AMC Name', 'Total Commision Expenses Paid', 'Gross Inflows', 'Net Inflows', 'Average AUM for Last Reported Year', 'Closing AUM for Last Reported Year', 'Effective Yield', 'Nature of AUM', 'Reported Year');
            foreach($amc_data as $key => $value){
                $row = (array) $value;
                $output_arr[] = $row;
            }
            unset($key, $value, $csv_headers);
            // makes data available in a CSV file format to user
            return \Excel::download(new ArrayRecordsExport($output_arr), 'amc_wise_data'.date('Ymd') .'.xlsx');   
        }
        else{
            // coming here if we don't have any data
            // as data is requested as an EXPORT action, so displaying message and closing the newly open window
            ?><script>alert('No records found');window.close();</script><?php
        }
    }

    /**
     * Author: Prasad Wargad
     * Purpose: JIRA ID: SMF-57. Function to assign user/bdm to an ARN as per shared logic in below given mindmap url:
     * https://www.mindmeister.com/2055663759?t=Nza85amUKc
     * Created: 10/11/2021
     * Modified:
     * Modified by:
     */
    public function autoAssignBDM(Request $request, $arn_number){
      extract($request->all());
      $err_flag = 1;
      $err_msg = array();
      if(isset($save_data) && ($save_data == 1)){
          $retrieved_data = DistributorsModel::assign_users_to_arn(array('arn_number' => $arn_number,
                                                                         'flag_log_display_messages' => true,
                                                                         'calling_it_from_browser' => true));
          if($retrieved_data['err_flag'] == 0 && isset($retrieved_data['assigned_bdm_id']) && !empty($retrieved_data['assigned_bdm_id'])){
              return response()->json([
                  'status' => 'success',
                  'msg' => array('BDM assigned successfully'),
              ]);
          }
          else{
              return response()->json([
                  'status' => 'failed',
                  'msg' => array('Unable to assign BDM, please try again later'),
              ]);
          }
          unset($retrieved_data);
      }
      else{
          return response()->json([
              'status' => 'failed',
              'msg' => array('Unable to process your request, try again later'),
          ]);
      }
    }

    /**
     * Author: Dharmesh
     * Purpose: Function to search ARN 
     * Created: 10/11/2021
     * Modified:
     * Modified by:
     */

    public function arnSearch(Request $request){
        // checking whether any data is posted or not to this function
        $post_data = $request->all();   // stores all posted data
        extract($post_data);
        if(count($request->all()) > 0){
            // if data is posted then showing distributors list in datatable or exporting them as per input parameters
            $retrieved_data = DistributorsModel::getDistributorsList(array('columns' => array(
                                                array('data' => 'ARN',
                                                    'search' => array('value' => $search_arn, 'exact_match' => 1)
                                                )
                                            ),
                                            'flag_show_all_arn_data' => true
                                        )
                                    );
            if(isset($retrieved_data['records']) && !$retrieved_data['records']->isEmpty() && isset($retrieved_data['records'][0]) && is_object($retrieved_data['records'][0]) && get_object_vars($retrieved_data['records'][0]) > 0){
                $output_arr = array('ARN' => $retrieved_data['records'][0]->ARN,
                                    'arn_holders_name' => $retrieved_data['records'][0]->arn_holders_name,
                                    'is_rankmf_partner' => $retrieved_data['records'][0]->is_rankmf_partner,
                                    'rankmf_stage_of_prospect' => $retrieved_data['records'][0]->rankmf_stage_of_prospect,
                                    'is_samcomf_partner' => $retrieved_data['records'][0]->is_samcomf_partner,
                                    'samcomf_stage_of_prospectARN' => $retrieved_data['records'][0]->samcomf_stage_of_prospect);
                return response()->json(array('status' => 'success' , 'data' => array($output_arr)), 200);
            }
            else{
                return response()->json(array('status' => 'error' , 'data' => 'Record not found'), 200);
            }
        }
        else
        {
            return view("appointment/arn_search_list");
        }
  
    }

    public function exportCommissionStructuretoCSV(Request $request){
        extract($request->all());
        $amc_data = DistributorsModel::getCommissionStructureByARNToExport($request->all());

        if(!$amc_data->isEmpty()){
            $output_arr[] = array('ARN', 'Scheme Name','Month','Year', '1st Year Trail(p.a.)', '2nd Year Trail(p.a.)', 'Additionl Trail for B30# (1st Year Only)','Special addition for first year trail','Special addition for second year trail');
            foreach($amc_data as $key => $value){
                $row = (array) $value;
                $output_arr[] = $row;
            }
            unset($key, $value, $csv_headers);
            // makes data available in a CSV file format to user
            return \Excel::download(new ArrayRecordsExport($output_arr), 'commission_data'.date('Ymd') .'.xlsx');   
        }
        else{
            // coming here if we don't have any data
            // as data is requested as an EXPORT action, so displaying message and closing the newly open window
            ?><script>alert('No records found');window.close();</script><?php
        }
    }

    function get_edit_commission_detail(Request $request)
    {
      $records = DistributorsModel::getCommissionStructureByID($request['id']);
            echo json_encode($records);  
        
    }

    function updatecommissionstructure(Request $request)
    {   
        // DB::enableQueryLog();
        //x($request->all());
        $user = Auth::user();
        $month = $request['month'];
        $year = $request['year'];
        $scheme_code = $request['scheme_code'];
        /*$get_arn = DB::table('rate_card_partnerwise')
                    ->select('rate_card_partnerwise.partner_arn')
                    ->where('rate_card_partnerwise.partner_arn', '=', $request['commission_id'])->get();*/
        
        $arn = $request['arn_commission'];
        $earlier_data = DB::table('brokerage_category_partner_schemewise')->where('arn',$arn)->where('scheme_code',$scheme_code)->first();
        $scheme_array=array('FCRG'=>'SAMCO FLEXICAP FUND','ONRG'=>'SAMCO OVERNIGHT FUND','ELRG'=>'SAMCO ELSS TAX SAVER FUND');
        if(!empty($earlier_data))
        {
        $earlier_category = $earlier_data->category;
        }else{
            $earlier_category='';
        }

        /*if($earlier_category == $request['plan_type']){
            return redirect('/distributorslist')->with('warning','No changes were done.');
        }*/
        if(!empty($earlier_data))
        {
          $update_status =   DB::table('brokerage_category_partner_schemewise')
                ->where('arn',$arn)
                ->where('scheme_code',$scheme_code)
                ->update(['category'=>$request['plan_type']]);
        }else{
          DB::table('brokerage_category_partner_schemewise')->insert([
                'scheme_code' => $scheme_code,
                'scheme_name' => $scheme_array[$scheme_code],
                'arn' => $arn,
                'category' =>$request['plan_type']
            ]);  
        }
        

            $scheme_data = DB::table('rate_card_schemewise')
                ->where('com_category',$request['plan_type'])
                ->where('month','>=',$month)
                ->where('year','>=',$year)
                ->where('scheme_code',$scheme_code)
                ->where('last_active',1)
                /*->groupBy('month')
                ->groupBy('year')
                ->orderBy('id','ASC')*/
                ->get();
            $scheme_dataa = json_decode($scheme_data, true);
            //x($scheme_dataa);
            if(isset($scheme_dataa) && !empty($scheme_dataa)){
                foreach($scheme_dataa as $scheme){
                    DB::table('rate_card_partnerwise')
                        ->where('partner_arn', $request['commission_id'])
                        ->where('month', $scheme['month'])
                        ->where('year', $scheme['year'])
                        ->where('scheme_code', $scheme['scheme_code'])
                        ->update([
                            'scheme_code' => $scheme['scheme_code'],
                            'scheme_name'=>$scheme['scheme_name'],
                            'com_category'=>$scheme['com_category'],
                            'first_year_trail'=>$scheme['first_year_trail'],
                            'second_year_trail'=>$scheme['second_year_trail'],
                            'b30'=>$scheme['b30']
                        ]);
                    }
            }
            DB::table('change_partner_category_log')->insert([
                'ARN' => $request['commission_id'],
                'changed_from' => $earlier_category,
                'changed_to' => $request['plan_type'],
                'month' => $month,
                'year' => $year,
                'changed_by' => $user->name,
                'scheme_code'=>$scheme_code
            ]);
            return redirect('/distributorslist')->with('success','Commission structure Updated successfully.');
    }
    function get_category_name(Request $request){
        $post=$request->all();
        $scheme_data = DB::table('brokerage_category_partner_schemewise')
                                ->where('scheme_code',$post['scode'])
                                ->where('arn',$post['arn'])
                                ->get();
     if(!empty($scheme_data[0]))
     { //x($scheme_data);
      $list=$scheme_data[0];
     }else{
        $list=array();
     }
      return json_encode($list);
    }
    function getNFOSchemeRateCard(Request $req){        
        $post_data = $req->all();
        $arn = $post_data['arn'];
        if(strtolower($post_data['scheme_type']) == 'nfo'){
            $scheme_data = [];
            if(!empty($arn))
                $scheme_data = DB::connection('rankmf')->table('mutual_fund_partners.mfp_partner_registration')->select(['partner_code','category','id','name','GST as gst'])->where('arn', $arn)->get()->toArray();
            if(!empty($scheme_data)){
                if(!empty($post_data['month']) && !empty($post_data['year'])){
                    $category_number = $this->get_oldcatrgory($post_data['month'], $post_data['year'], $scheme_data[0]->partner_code);
                    if(empty($category_number)){
                        $category_number = $scheme_data[0]->category;
                    }
                    $filter=array('delete_flage'=>0);
                    $month = $post_data['month'];
                    $year  = $post_data['year'];
                    $month_operator = ">=";
                    $year_operator  = "<=";
                }
                else{
                    $category_number = $scheme_data[0]->category;
                    $filter=array('delete_flage'=>0);
                    $month = date('m');
                    $year  = date('Y');
                    $month_operator = "=";
                    $year_operator  = "=";
                }
                
                switch ($category_number) {
                    case 1:
                    $category_name = "core";
                    break;
                    case 2:
                    $category_name = "core_plus";
                    break;
                    case 3:
                    $category_name = "prime";
                    break;
                    case 4:
                    $category_name = "prime_plus";
                    break;
                    case 5:
                    $category_name = "elite";
                    break;
                    case 6:
                    $category_name = "elite_plus";
                    break;
                    default:
                    $category_name = "";
                }
                $select_data = [
                    "amc_name","scheme_name","nfo_lunch_date","nfo_close_date","nfo_open","load",
                    $category_name."_profit_share_t30 as profit_share_t30", $category_name."_additional_incentive_b30 as additional_incentive_b30",
                    "trail_1st_year", "additional_trail_b"
                ];

                $ratecard_report = DB::connection('rankmf')->table('mutual_fund_partners.mfp_nof_rate_cart')->select($select_data)->where($filter)->whereMonth('nfo_lunch_date', $month_operator, $month)->whereYear('nfo_lunch_date', $year_operator, $year)->get()->toArray();
                $rate_details        = DB::connection('rankmf')->table('mutual_fund_partners.mfp_rate_logic')->get()->toArray();
                $data['limitdata']   = $ratecard_report;
                $data['gst']         = ((!empty($scheme_data[0]->gst)) ? 1 : 0);
                $data['category']    = $category_number;
                $data['rate']        = $rate_details[$category_number-1]->trail_sharing;
                $html                = view('distributors/nfo_scheme_rate_card', $data);

                echo $html;
                exit;
            }else{
                $data['limitdata']   = [];
                $data['gst']         = 0;
                $data['category']    = 0;
                $data['rate']        = 0;
                $html                = view('distributors/nfo_scheme_rate_card', $data);
                echo $html;
                exit;
            }
        }
        else{
            die("Scheme");
        }
    }
    function getNFOProgress(Request $req){
        $post_data = $req->all();
        $arn = $post_data['arn'];
        $scheme_data = DB::connection('rankmf')->table('mutual_fund_partners.mfp_partner_registration')->select(['partner_code','category','id','name','GST'])->where('arn', $arn)->get()->toArray();
        if(!empty($scheme_data)){
            $progress['progress_bar'] = $this->getProgressBarData($scheme_data[0]->partner_code);
            $progress['category']     = $scheme_data[0]->category;
            $progress_html = view('distributors/nfo_scheme_progreshbar', $progress);
            echo $progress_html;
            exit;
        }else{
            echo "";
            exit;
        }
    }
    function get_oldcatrgory($month,$year,$partner_id)
    {
        $category_count = DB::connection('rankmf')->table('mutual_fund_partners.mfp_partner_category_log')->where('partner_code',$partner_id)
        ->where('month','>',$month)->where('year','>=',$year)->orderBy('id', 'desc')->get()->toArray();
        if(!empty($category_count)){
            $category=$category_count[0]->old_catergory;
        }
        else{
            $category='';
        }
        return $category;
    }
    function getSchemeRateCard(Request $req){        
        $post_data = $req->all();
        $arn = $post_data['arn'];
        if(strtolower($post_data['scheme_type']) == 'scheme'){
            $scheme_data = DB::connection('rankmf')->table('mutual_fund_partners.mfp_partner_registration')->select(['partner_code', 'category','id','name','GST as gst'])->where('ARN', $arn)->get()->toArray();
            if(!empty($scheme_data)){
                if(!empty($post_data['month']) && !empty($post_data['year'])){
                    $category_number = $this->get_oldcatrgory($post_data['month'], $post_data['year'], $scheme_data[0]->partner_code);
                    if(empty($category_number)){
                        $category_number = $scheme_data[0]->category;
                    }
                    $filter=array('partner_id'=>$scheme_data[0]->partner_code, 'delete_flag'=>0);
                    $month = $post_data['month'];
                    $year  = $post_data['year'];
                }
                else{
                    $category_number = $scheme_data[0]->category;
                    $filter=array('partner_id' =>$scheme_data[0]->partner_code,'delete_flag'=>0);
                    $month = date('m');
                    $year  = date('Y');
                }
                
                switch ($category_number) {
                    case 1:
                    $category_name = "core";
                    break;
                    case 2:
                    $category_name = "core_plus";
                    break;
                    case 3:
                    $category_name = "prime";
                    break;
                    case 4:
                    $category_name = "prime_plus";
                    break;
                    case 5:
                    $category_name = "elite";
                    break;
                    case 6:
                    $category_name = "elite_plus";
                    break;
                    default:
                    $category_name = "";
                }
                $select_data = [
                    "mutual_fund_house", "scheme_type", "channel_partner_code", "scheme_name", "from_date", "to_date", $category_name."_profit_share_t30 as profit_share_t30", $category_name."_t30_trail_1st_year as t30_trail_1st_year", $category_name."_additional_incentive_b30 as additional_incentive_b30","samco_trail_t30", "samco_trail_b30", "from_date"
                ];

                $ratecard_report = DB::connection('rankmf')->table('mutual_fund_partners.mfp_rate_card')->select($select_data)->where($filter)->whereMonth('from_date', $month)->whereYear('from_date', $year)->orderBy('scheme_name','asc')->get()->toArray();
                if(empty($ratecard_report)){
                    if(!empty($post_data['month']) && !empty($post_data['year'])){
                        unset($filter['partner_id']);
                        $ratecard_report = DB::connection('rankmf')->table('mutual_fund_partners.mfp_rate_card')->select($select_data)->where($filter)->where(function($query){
                            $query->where('partner_id', '=', '');
                            $query->orWhereNull('partner_id');
                        })->whereMonth('from_date', $month)->whereYear('from_date', $year)->orderBy('scheme_name','asc')->get()->toArray();
                    }
                    else{
                        unset($filter['partner_id']);
                        $ratecard_report = DB::connection('rankmf')->table('mutual_fund_partners.mfp_rate_card')->select($select_data)->where($filter)->where(function($query){
                            $query->where('partner_id','=','');
                            $query->orWhereNull('partner_id');
                        })->whereMonth('from_date', '=', $month)->whereYear('from_date','=', $year)->orderBy('scheme_name','asc')->limit(100)->get()->toArray();
                    }
                }
                $data['limitdata']   = $ratecard_report;
                $data['gst']         = ((!empty($scheme_data[0]->gst)) ? 1 : 0);
                $data['category']    = $category_number;
                $html                = view('distributors/scheme_rate_card', $data);
                echo $html;
                exit;
            }else{
                $data['limitdata']   = [];
                $data['gst']         = 0;
                $data['category']    = [];
                $html                = view('distributors/scheme_rate_card', $data);
                echo $html;
                exit;
            }
        }
    }
    function getProgressBarData($partner_id){
        $return_data      = [];
        $return_data['status']  = 'error';
        $return_data['msg']   = 'Something went wrong';
        if(!empty($partner_id)){
            $aum_data = $this->getPartnerAumFromMongo($partner_id);
            if($aum_data['status'] == 'success'){
                $aum = $aum_data['aum']/10000000;
                $aum = round($aum, 6);
                $partner_data = DB::connection('rankmf')->table('mutual_fund_partners.mfp_partner_registration')->select(['tier', 'category'])->where("partner_code", $partner_id)->get()->toArray();
    
                // get partner's progress
                if(!empty($partner_data[0]->tier)){
                    $partner_progress = DB::connection("rankmf")->select(DB::raw("SELECT progress, min, max FROM mutual_fund_partners.mfp_tier_aum_range WHERE tier=".$partner_data[0]->tier." AND (min+0) <= $aum AND (max+0) >= $aum"));
                    $all_tiers = DB::connection('rankmf')->table('mutual_fund_partners.mfp_tier_aum_range')->select(['progress', 'min', 'max'])->where("tier", $partner_data[0]->tier)->get()->toArray();
                    $partner_progress[0]->progress=$partner_data[0]->category;
                    if(!empty($partner_progress)){
                        $return_data['status']  = 'success';
                        $return_data['msg']   = 'Success';
                        $return_data['aum']   = $aum;
                        $return_data['data']    = $partner_progress;
                        $return_data['all_tiers'] = $all_tiers;
                    }
                }
            }          
        }
        return $return_data;
    }
    function getPartnerAum($partner_id){
        $return_data      = [];
        $return_data['status']  = 'error';
        $return_data['msg']   = 'Something went wrong';
        if(!empty($partner_id)){
            $aum_record = DB::connection('rankmf')->table('mutual_funds.mf_aum_data')->where('Agent_Code', $partner_id)->get()->toArray();
            if(!empty($aum_record)){
                $aum                    = array_column($aum_record, "AUM");
                $return_data['status']  = 'success';
                $return_data['msg']     = 'Success';
                $return_data['aum']     = (!empty($aum) ? array_sum($aum) : 0);
            }
            else{
                $return_data['status']  = 'success';
                $return_data['msg']   = 'Success';
                $return_data['aum']   = 0;
            }
        }    
        return $return_data;
    }
    function getPartnerAumFromMongo($partner_id){

        $match = ['$match' => ["broker_id" => strval($partner_id)]];

        $mongo_data = DB::connection('partnermongodb')->collection('mfp_amu_logs')->raw(function ($collection) use ($match) {
            return $collection->aggregate([
                $match,
                [
                    '$group' => [
                        '_id' => '$broker_id',
                        'aum' => ['$sum' => '$total_value'],
                    ],
                ],
            ]);
        })->toArray();

        if(!empty($mongo_data)){
            $aum                    = (isset($mongo_data[0]['aum']) && !empty($mongo_data[0]['aum'])) ? $mongo_data[0]['aum'] : 0;
            $return_data['status']  = 'success';
            $return_data['msg']     = 'Success';
            $return_data['aum']     = (!empty($aum) ? $aum : 0);
        }
        else{
            $return_data['status']  = 'success';
            $return_data['msg']   = 'Success';
            $return_data['aum']   = 0;
        }
        return $return_data;
    }
}
