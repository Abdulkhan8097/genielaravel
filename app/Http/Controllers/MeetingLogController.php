<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeetinglogModel;
use App\Models\DistributorsModel;
use App\Models\BDM_Meeting_Dashboard_model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Exports\ArrayRecordsExport;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use DataTables;

class MeetingLogController extends Controller
{
    protected $logged_in_user_roles_and_permissions, $flag_have_all_permissions, $flag_show_all_arn_data, $logged_in_user_id, $current_page_uri;
    public function __construct(){
        $this->current_page_uri = Route::getCurrentRoute()->uri();
        if(empty($this->current_page_uri) || (!empty($this->current_page_uri) && stripos($this->current_page_uri, 'api/') === FALSE)) {
            // adding authentication only when request is not coming from API route
            $this->middleware('auth');
        }

        $this->data_table_headings = array('action' => array('label' => 'Action'),
											'bdm_name'=>array('label' =>'Created By'),
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
                                            // 'product_information_received'=>array('label' =>'Product Information Received'),
                                            'is_rankmf_partner'=>array('label' =>'Is RankMF Partner'),
                                            'total_ind_aum'=>array('label' =>'Total Industry AUM (In Crores)'),
                                            'last_meeting_date'=>array('label' =>'Last Meeting Date'),
                                            
											'meeting_purpose'=>array('label' =>'Meeting Purpose'),          
                                            'tags'=>array('label' =>'Tags'),                         
                                            'created_at'=>array('label' =>'Created At'),
                                            'updated_at'=>array('label' =>'Updated At'),
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
        
    /**
     * Author: Maniraj Nadar
     * Purpose: JIRA ID: SMF-44
     * Created: 14/10/2021
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
            // print_r($request->all());exit;
            $partnersData =MeetinglogModel::getMeetingLogList(array_merge($request->all(),
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
                    $csv_headers = array_column($this->data_table_headings, 'label');
                    array_shift($csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    $output_arr[] = $csv_headers;
                    foreach($partnersData['records'] as $key => $value){
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
                    return \Excel::download(new ArrayRecordsExport($output_arr),'meetinglog_master_data_'. date('Ymd').'.xlsx');
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
            $data = array('data_table_headings' => $this->data_table_headings);
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            $data['logged_in_user_id'] = $this->logged_in_user_id;
            return view('meetinglog/list')->with($data);
        }
    }

    public function create(Request $request,$arn_number){
        $partner_data = DistributorsModel::getDistributorByARN($arn_number,
                                    array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                          'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                          'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                          'logged_in_user_id' => $this->logged_in_user_id)
                                );
        if($partner_data->isEmpty() || !isset($partner_data[0])){
            return redirect('distributorslist')->with('error', 'Invalid ARN');
        }
		
		$partner_pinCode=$partner_data[0]->arn_pincode;
		$ARN=$partner_data[0]->ARN;
		$NearestPinCode_data = MeetinglogModel::getNearestPinCode($partner_pinCode);
		if(!empty($NearestPinCode_data)){
		$NearestPin = explode(',', $NearestPinCode_data->mapped_pins);
		}
		else{
			$NearestPin=[];
		}
		$NearestPinCode_data = DistributorsModel::getDistributorByPincode($NearestPin, $ARN, array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
			  'flag_have_all_permissions' => $this->flag_have_all_permissions,
			  'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
			  'logged_in_user_id' => $this->logged_in_user_id)
			);
			if ($request->post()) {
				return Datatables::of($NearestPinCode_data)
					->addIndexColumn()
					->addColumn('action', function($row){
						$actionBtn = '<a target="_blank" href="'. env('APP_URL') .'/meetinglog/create/'. $row->ARN .'" class="edit btn btn-primary btn-sm">Add meeting</a>';
						return $actionBtn;
					})
					->addColumn('arn_address', function($row){
						// return wordwrap(str_replace(',',', ',$row->arn_address),60,'<br/>');
						return str_replace(',', ', ', $row->arn_address);
					})
					->filterColumn('arn_address', function ($query, $keyword) {
						$query->where('arn_address', 'LIKE', "%$keyword%");
					})
					->rawColumns(['action'])
					->make(true);
			}
			
        $data['partner_data'] = $partner_data[0];
		$data['bdmlist']=$this->get_bdm_list();

        return view('meetinglog/create')->with($data);
    }
	  public function get_bdm_list()
    {
     	$bdmlist = BDM_Meeting_Dashboard_model::getbdmlist_meeting();

     	return $bdmlist;
    }
	public function edit(Request $request,$logID){
		
        $partner_data = DistributorsModel::getMeetingEdit($logID,
                                    array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                          'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                          'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                          'logged_in_user_id' => $this->logged_in_user_id)
                                );
        if($partner_data->isEmpty() || !isset($partner_data[0])){
            return redirect('meetinglog')->with('error', 'Invalid log ID');
        }
		$partner_pinCode=$partner_data[0]->arn_pincode;
		$ARN=$partner_data[0]->ARN;
		$NearestPinCode_data = MeetinglogModel::getNearestPinCode($partner_pinCode);
		if(!empty($NearestPinCode_data)){
		$NearestPin = explode(',', $NearestPinCode_data->mapped_pins);
		}
		else{
			$NearestPin=[];
		}
		$NearestPinCode_data = DistributorsModel::getDistributorByPincode($NearestPin, $ARN, array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
			  'flag_have_all_permissions' => $this->flag_have_all_permissions,
			  'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
			  'logged_in_user_id' => $this->logged_in_user_id)
			);
			if ($request->post()) {
				return Datatables::of($NearestPinCode_data)
					->addIndexColumn()
					->addColumn('action', function($row){
						$actionBtn = '<a target="_blank" href="'. env('APP_URL') .'/meetinglog/create/'. $row->ARN .'" class="edit btn btn-primary btn-sm">Add meeting</a>';
						return $actionBtn;
					})
					->addColumn('arn_address', function($row){
						return str_replace(',', ', ', $row->arn_address);
					})
					->filterColumn('arn_address', function ($query, $keyword) {
						$query->where('arn_address', 'LIKE', "%$keyword%");
					})
					->rawColumns(['action'])
					->make(true);
			}
		
		// $start_datetime=$partner_data[0]->start_datetime;
		// if(strtotime(date('Y-m-d')) < strtotime($start_datetime)){
			$data['partner_data'] = $partner_data[0];
			$data['bdmlist']=$this->get_bdm_list();
			return view('meetinglog/edit')->with($data);
		// }else{
		// 	return redirect('meetinglog')->with('error', 'Invalid Meeting ID');
		// }
       
    }

    public function save_data(Request $request){
        $partner_data = DistributorsModel::getDistributorByARN($request['ARN'],
                                array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                      'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                      'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                      'logged_in_user_id' => $this->logged_in_user_id)
                        );
        if($partner_data->isEmpty() || !isset($partner_data[0])){
            return redirect('distributorslist')->with('error', 'Invalid ARN');
        }
		
		$tags="";
		$bdm_data="";
		$meeting_purpose=$request['meeting_purpose'];
		if($meeting_purpose){
		$meeting_purpose=implode(",",$meeting_purpose);
		}
		$bdm_data_all=$request['bdm_data'];
		if($bdm_data_all){
			$bdm_data=implode(",",$bdm_data_all);
			$data =  DB::table('users')
				->join('users_details', 'users_details.user_id', '=','users.id')
				->select('users.name','users.email','users_details.mobile_number')
				->whereIn('users.id', $bdm_data_all)
				->where('users_details.is_deleted', '=',0)
				->where('users_details.is_old', '=',0)
				->get()->toArray();

				$nameArray = array_map(function ($item) {
					return $item->name;
				}, $data);
				
				$tags = implode(',', $nameArray);
			}
	
        $user_id = $this->logged_in_user_id;
        $request->validate([
            'contact_email' => 'required|email:filter',
            'meeting_mode' => 'required',
            'start_time' => 'required|before:today + 14 days',
            'end_time' => 'required|after:start_time',
            'contact_name' => 'required',
            'contact_mobile' => 'required|regex:/^[1-9][0-9]{9}$/i',
        ], [
            'contact_email.email' => 'Enter a Valid Email address!!',
            'meeting_mode.required' => 'Select Mode of a meeting!!',
            'start_time.required' => 'Select Start DateTime of a Meeting!!',
            'end_time.required' => 'Select End DateTime of a Meeting!!',
            'contact_name.required' => 'Enter a contact name!',
            'contact_mobile.regex' => 'Enter a Valid Mobile number!',
        ]);
        $meeting_add = array(
            'ARN'=>$request['ARN'],
            'meeting_purpose'=>isset($meeting_purpose)?$meeting_purpose:'',
            'tags'=>isset($tags)?$tags:'',
            'bdm_data'=>isset($bdm_data)?$bdm_data:'',
            'contact_person_email'=>$request['contact_email'],
            'contact_person_name'=>$request['contact_name'],
            'contact_person_mobile'=>$request['contact_mobile'],
            'start_datetime'=>$request['start_time'],
            'end_datetime'=>$request['end_time'],
            'meeting_remarks'=> (isset($request['remarks'])?$request['remarks']:''),
            'meeting_mode'=>$request['meeting_mode'],
            'user_id' => $user_id,
        );

        // x($request['contact_person']);
        if($request['contact_person'] == 'other'){
            for($cntr = 1; $cntr <= 5; $cntr++){
                $alternate_name = 'alternate_name_'. $cntr;
                $alternate_mobile = 'alternate_mobile_'. $cntr;
                $alternate_email = 'alternate_email_'. $cntr;
                if(empty($partner_data[0]->$alternate_mobile) && empty($partner_data[0]->$alternate_email)){
                    $where_conditions = array(array('ARN', '=', $request['ARN']));
                    $update_data = array($alternate_name => $request['contact_name'],
                                        $alternate_mobile => $request['contact_mobile'],
                                        $alternate_email => $request['contact_email'],
                                        );
                DistributorsModel::UpdateSamcoPByArn(array('where' => $where_conditions,
                                                        'data' => $update_data,
                                                        'arn_number' => $request['ARN'],
                                                        'logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                        'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                        'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                        'logged_in_user_id' => $this->logged_in_user_id)
                                                );
                break;
                }
            }
        }
        elseif(in_array($request['contact_person'], array('product_approver_other', 'product_apporver_present')) !== FALSE){
            // updating product approver person details
            $where_conditions = array(array('ARN', '=', $request['ARN']));
            $update_data = array('product_approval_person_name' => $request['contact_name'],
                                 'product_approval_person_mobile' => $request['contact_mobile'],
                                 'product_approval_person_email' => $request['contact_email'],
                                );
            DistributorsModel::UpdateSamcoPByArn(array('where' => $where_conditions,
                                                    'data' => $update_data,
                                                    'arn_number' => $request['ARN'],
                                                    'logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                    'logged_in_user_id' => $this->logged_in_user_id)
                                            );
        }
        elseif(in_array($request['contact_person'], array('sales_provide_other', 'sales_provide_present')) !== FALSE){
            // updating product approver person details
            $where_conditions = array(array('ARN', '=', $request['ARN']));
            $update_data = array('sales_drive_person_name' => $request['contact_name'],
                                 'sales_drive_person_mobile' => $request['contact_mobile'],
                                 'sales_drive_person_email' => $request['contact_email'],
                                );
            DistributorsModel::UpdateSamcoPByArn(array('where' => $where_conditions,
                                                    'data' => $update_data,
                                                    'arn_number' => $request['ARN'],
                                                    'logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                    'logged_in_user_id' => $this->logged_in_user_id)
                                            );
        }
        elseif(in_array($request['contact_person'], array('alternate_contact_1', 'alternate_contact_2','alternate_contact_3','alternate_contact_4','alternate_contact_5')) !== FALSE){
            // updating product approver person details
            $where_conditions = array(array('ARN', '=', $request['ARN']));
            $alternate_contact_id = str_replace('alternate_contact_', '', $request['contact_person']);

            $update_data = array('alternate_name_'. $alternate_contact_id => $request['contact_name'],
                                'alternate_mobile_'. $alternate_contact_id => $request['contact_mobile'],
                                'alternate_email_'. $alternate_contact_id => $request['contact_email'],
            );
            unset($alternate_contact_id);
            DistributorsModel::UpdateSamcoPByArn(array('where' => $where_conditions,
                                                    'data' => $update_data,
                                                    'arn_number' => $request['ARN'],
                                                    'logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                    'logged_in_user_id' => $this->logged_in_user_id)
                                            );
        }
        // x($meeting_add);
        $insert=DB::table('drm_meeting_logger')->insertGetId($meeting_add);
        if(!empty($insert)){
			if(!empty($request['remarks'])){
				if(!empty($request['meeting_mode']) && $request['meeting_mode'] == 'In Person Meeting')
				{
					$this->send_sms(array('id' => $insert));
				}
			}
        }

		if(!empty($request['remarks'])){
			return redirect('meetinglog')->with('success',new HtmlString('Meeting created successfully, <a href="'.url('/reimbursement/'.$insert).'" >Add reimbursement</a>.'));
		}
        return redirect('meetinglog')->with('success','Created successfully.');
    }
	public function update_meeting_data(Request $request){
        $partner_data = DistributorsModel::getDistributorByARN($request['ARN'],
                                array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                      'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                      'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                      'logged_in_user_id' => $this->logged_in_user_id)
                        );
					
        if($partner_data->isEmpty() || !isset($partner_data[0])){
            return redirect('distributorslist')->with('error', 'Invalid ARN');
        }
		$tags="";
		$bdm_data="";
		$logid=$request['logid'];
		$smsCheck = MeetinglogModel::getARN($logid);
		$meeting_purpose=$request['meeting_purpose'];
		if($meeting_purpose){
			$meeting_purpose=implode(",",$meeting_purpose);
		}
		$bdm_data_all=$request['bdm_data'];
		if($bdm_data_all){
		$bdm_data=implode(",",$bdm_data_all);
		$data =  DB::table('users')
                    ->join('users_details', 'users_details.user_id', '=','users.id')
                    ->select('users.name','users.email','users_details.mobile_number')
					->whereIn('users.id', $bdm_data_all)
					->where('users_details.is_deleted', '=',0)
					->where('users_details.is_old', '=',0)
                    ->get()->toArray();

					$nameArray = array_map(function ($item) {
						return $item->name;
					}, $data);
					
					$tags = implode(',', $nameArray);
		}
	
        $user_id = $this->logged_in_user_id;
        $request->validate([
            'contact_email' => 'required|email:filter',
            'meeting_mode' => 'required',
            'start_time' => 'required|before:today + 14 days',
            'end_time' => 'required|after:start_time',
            'contact_name' => 'required',
            'contact_mobile' => 'required|regex:/^[1-9][0-9]{9}$/i',
        ], [
            'contact_email.email' => 'Enter a Valid Email address!!',
            'meeting_mode.required' => 'Select Mode of a meeting!!',
            'start_time.required' => 'Select Start DateTime of a Meeting!!',
            'end_time.required' => 'Select End DateTime of a Meeting!!',
            'contact_name.required' => 'Enter a contact name!',
            'contact_mobile.regex' => 'Enter a Valid Mobile number!',
        ]);
        $meeting_add = array(
            'ARN'=>$request['ARN'],
			'meeting_purpose'=>isset($meeting_purpose)?$meeting_purpose:'',
            'tags'=>isset($tags)?$tags:'',
			'bdm_data'=>isset($bdm_data)?$bdm_data:'',
            'contact_person_email'=>$request['contact_email'],
            'contact_person_name'=>$request['contact_name'],
            'contact_person_mobile'=>$request['contact_mobile'],
            'start_datetime'=>$request['start_time'],
            'end_datetime'=>$request['end_time'],
            'meeting_remarks'=> (isset($request['remarks'])?$request['remarks']:''),
            'meeting_mode'=>$request['meeting_mode'],
            'user_id' => $user_id,
        );

        // x($request['contact_person']);
        if($request['contact_person'] == 'other'){
            for($cntr = 1; $cntr <= 5; $cntr++){
                $alternate_name = 'alternate_name_'. $cntr;
                $alternate_mobile = 'alternate_mobile_'. $cntr;
                $alternate_email = 'alternate_email_'. $cntr;
                if(empty($partner_data[0]->$alternate_mobile) && empty($partner_data[0]->$alternate_email)){
                    $where_conditions = array(array('ARN', '=', $request['ARN']));
                    $update_data = array($alternate_name => $request['contact_name'],
                                        $alternate_mobile => $request['contact_mobile'],
                                        $alternate_email => $request['contact_email'],
                                        );
                DistributorsModel::UpdateSamcoPByArn(array('where' => $where_conditions,
                                                        'data' => $update_data,
                                                        'arn_number' => $request['ARN'],
                                                        'logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                        'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                        'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                        'logged_in_user_id' => $this->logged_in_user_id)
                                                );
                break;
                }
            }
        }
        elseif(in_array($request['contact_person'], array('product_approver_other', 'product_apporver_present')) !== FALSE){
            // updating product approver person details
            $where_conditions = array(array('ARN', '=', $request['ARN']));
            $update_data = array('product_approval_person_name' => $request['contact_name'],
                                 'product_approval_person_mobile' => $request['contact_mobile'],
                                 'product_approval_person_email' => $request['contact_email'],
                                );
            DistributorsModel::UpdateSamcoPByArn(array('where' => $where_conditions,
                                                    'data' => $update_data,
                                                    'arn_number' => $request['ARN'],
                                                    'logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                    'logged_in_user_id' => $this->logged_in_user_id)
                                            );
        }
        elseif(in_array($request['contact_person'], array('sales_provide_other', 'sales_provide_present')) !== FALSE){
            // updating product approver person details
            $where_conditions = array(array('ARN', '=', $request['ARN']));
            $update_data = array('sales_drive_person_name' => $request['contact_name'],
                                 'sales_drive_person_mobile' => $request['contact_mobile'],
                                 'sales_drive_person_email' => $request['contact_email'],
                                );
            DistributorsModel::UpdateSamcoPByArn(array('where' => $where_conditions,
                                                    'data' => $update_data,
                                                    'arn_number' => $request['ARN'],
                                                    'logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                    'logged_in_user_id' => $this->logged_in_user_id)
                                            );
        }
        elseif(in_array($request['contact_person'], array('alternate_contact_1', 'alternate_contact_2','alternate_contact_3','alternate_contact_4','alternate_contact_5')) !== FALSE){
            // updating product approver person details
            $where_conditions = array(array('ARN', '=', $request['ARN']));
            $alternate_contact_id = str_replace('alternate_contact_', '', $request['contact_person']);

            $update_data = array('alternate_name_'. $alternate_contact_id => $request['contact_name'],
                                'alternate_mobile_'. $alternate_contact_id => $request['contact_mobile'],
                                'alternate_email_'. $alternate_contact_id => $request['contact_email'],
            );
            unset($alternate_contact_id);
            DistributorsModel::UpdateSamcoPByArn(array('where' => $where_conditions,
                                                    'data' => $update_data,
                                                    'arn_number' => $request['ARN'],
                                                    'logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                    'logged_in_user_id' => $this->logged_in_user_id)
                                            );
        }

			$updateResult = DB::table('drm_meeting_logger')
			->where('id', $logid)
			->update($meeting_add);

			if ($updateResult === false) {
				return redirect('meetinglog')->with('error','Meeting Not Updated.');
			} else if ($updateResult > 0) {
				// Update was successful
				if(!empty($request['remarks'])){
					if($smsCheck[0]->sms_sent_to_customer!=1){
						if(!empty($request['meeting_mode']) && $request['meeting_mode'] == 'In Person Meeting')
						{
							$this->send_sms(['id' => $logid]);
						}
					}
				}
			}
			if(!empty($request['remarks'])){
				return redirect('meetinglog')->with('success',new HtmlString('Meeting Updated Successfully, <a href="'.url('/reimbursement/'.$logid).'" >Add reimbursement</a>.'));
			}else{
				return redirect('meetinglog')->with('success','Meeting Updated Successfully.');
			}
    }

    public function get_view_detail(Request $request){
        $where_in_conditions = array();
        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList(array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                    'logged_in_user_id' => $this->logged_in_user_id));
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $records = DB::table('drm_meeting_logger')
                        ->join('drm_distributor_master', 'drm_meeting_logger.ARN', '=', 'drm_distributor_master.ARN')
                        ->select('drm_meeting_logger.*')
                        ->where('drm_meeting_logger.id', '=', $request['id']);
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $records = $records->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }
        $records = $records->get();
        if(!$records->isEmpty() && isset($records[0])){
            $records[0]->start_datetime=show_date_in_display_format('d/m/Y h:i A', $records[0]->start_datetime);
            $records[0]->end_datetime=show_date_in_display_format('d/m/Y h:i A', $records[0]->end_datetime);
            $records[0]->customer_response_received_datetime=show_date_in_display_format('d/m/Y h:i A', $records[0]->customer_response_received_datetime);
        }
        echo json_encode($records);
    }

    public function meeting_feedback_notification(Request $request){
        return $this->send_sms($request->all());
    }
    public function meetingfeedback(Request $request){

		$id=$request['id'];
		$where_conditions = array(array('id', '=', $id));
		$update_data['customer_given_rating']=$request['customer_given_rating'];
		$update_data['customer_remarks']=$request['customer_remarks'];
		$update_data['customer_response_received']='1';
		$update_data['customer_response_source']='2';
		$update_data['customer_response_received_datetime']=date("Y-m-d h:i:s");
		$update = MeetinglogModel::UpdateMeetingLogByID(array('where' => $where_conditions, 'data' => $update_data));
		$data=MeetinglogModel::getARN($id);
		$ARN=$data[0]->ARN;
		$msterdata=MeetinglogModel::getcheck_empanelled($ARN);
		if(!empty($msterdata)){
			return 1; // Return 1 if there are EMPANELLED records
		}else{
			return 0; // Return 0 if there are NOT EMPANELLED records
		}

    }
	public function meetingfeedbackremark(Request $request){
		$id=$request['id'];
		$remarkCheck = MeetinglogModel::getremark($id);
		if ($remarkCheck) {
			// If 'customer_remarks' is not empty, return 1
			return 1;
		} else {
			// If 'customer_remarks' is empty or the result is empty, return 0
			return 0;
		}

	}
	public function send_sms($input_arr = array()){
        $where_in_conditions = array();
        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList(array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                    'logged_in_user_id' => $this->logged_in_user_id));
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $data =  DB::table('drm_meeting_logger')
                    ->join('users', 'users.id', '=','drm_meeting_logger.user_id')
                    ->join('drm_distributor_master', 'drm_meeting_logger.ARN', '=', 'drm_distributor_master.ARN')
                    ->select('drm_meeting_logger.contact_person_email','drm_meeting_logger.ARN','drm_meeting_logger.contact_person_name','drm_meeting_logger.start_datetime','users.name as bdm_name','drm_meeting_logger.customer_response_received','drm_meeting_logger.contact_person_mobile','drm_meeting_logger.email_sent_to_customer','drm_meeting_logger.sms_sent_to_customer')
                    ->where('drm_meeting_logger.id', '=', $input_arr['id']);
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $data = $data->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }
        $data = $data->get();

        if($data->isEmpty() || !isset($data[0])){
            return response()->json([
                'status' => 'failed',
                'msg' => array('Meeting details not found'),
            ]);
        }
        if($data[0]->customer_response_received == 1){
            return response()->json([
                'status' => 'failed',
                'msg' => array('Customer Response Already received'),
            ]);
        }

        $err_msg = array();
        $contact_name = $data[0]->contact_person_name;
        $contact_email = $data[0]->contact_person_email;
        $contact_mobile = $data[0]->contact_person_mobile;
        $bdm_name = $data[0]->bdm_name;
        $start_datetime = date('d/m/Y h:i A', strtotime($data[0]->start_datetime));
        // $update_data = array();
			// $id = encrypt_decrypt('encrypt',$input_arr['id']);
            $feedback_link = env('PARTNER_RANKMF_STATIC_WEB_URL') .'Dashboard/feedback/'. $input_arr['id'];
			$msgtxt = "Hi, Thank you for taking the time to meet ".$bdm_name.". We look forward to your continuous support. Please feel free to share your feedback by clicking on the following link: ".$feedback_link." %n- RankMF";
			$sms_data           = [];
            $sms_data['message']=$msgtxt ;
            $sms_data['numbers']=array($contact_mobile);
			sendSms($sms_data);
            $update_data['sms_sent_to_customer'] = 1;
            unset($sms_data, $msgtxt);                 
			$where_conditions = array(array('id', '=', $input_arr['id']));
			if(is_array($update_data) && count($update_data) > 0){
				$update = MeetinglogModel::UpdateMeetingLogByID(array('where' => $where_conditions, 'data' => $update_data));
            if($update == true){
                return response()->json([
                    'status' => 'success',
                    'msg' => array('Notification sent to the customer'),
                ]);
            }
        }
        else{
            if(count($err_msg) == 0){
                $err_msg = array('Unable to process your request, try again later');
            }
            return response()->json([
                'status' => 'failed',
                'msg' => $err_msg
            ]);
        }
        unset($where_in_conditions, $where_conditions, $update_data, $email_template_id);
        unset($contact_name, $contact_email,$contact_mobile, $bdm_name, $start_datetime, $feedback_link);
    }
    public function send_meeting_notification($input_arr = array()){
        $where_in_conditions = array();
        // checking whether to show all ARN data or not
        $retrieve_users_data = \App\Models\UsermasterModel::getSupervisedUsersList(array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                    'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                    'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                    'logged_in_user_id' => $this->logged_in_user_id));
        if(!$retrieve_users_data['flag_show_all_arn_data']){
            // as all ARN data should not be shown that's why assigning only supervised user list
            $where_in_conditions['drm_distributor_master.direct_relationship_user_id'] = $retrieve_users_data['show_data_for_users'];
        }
        unset($retrieve_users_data);

        $data =  DB::table('drm_meeting_logger')
                    ->join('users', 'users.id', '=','drm_meeting_logger.user_id')
                    ->join('drm_distributor_master', 'drm_meeting_logger.ARN', '=', 'drm_distributor_master.ARN')
                    ->select('drm_meeting_logger.contact_person_email','drm_meeting_logger.ARN','drm_meeting_logger.contact_person_name','drm_meeting_logger.start_datetime','users.name as bdm_name','drm_meeting_logger.customer_response_received','drm_meeting_logger.contact_person_mobile','drm_meeting_logger.email_sent_to_customer','drm_meeting_logger.sms_sent_to_customer')
                    ->where('drm_meeting_logger.id', '=', $input_arr['id']);
        if(count($where_in_conditions) > 0){
            foreach($where_in_conditions as $in_condition_field => $in_condition_data){
                $data = $data->whereIn($in_condition_field, $in_condition_data);
            }
            unset($in_condition_field, $in_condition_data);
        }
        $data = $data->get();

        if($data->isEmpty() || !isset($data[0])){
            return response()->json([
                'status' => 'failed',
                'msg' => array('Meeting details not found'),
            ]);
        }
        if($data[0]->customer_response_received == 1){
            return response()->json([
                'status' => 'failed',
                'msg' => array('Customer Response Already received'),
            ]);
        }

        $err_msg = array();
        $contact_name = $data[0]->contact_person_name;
        $contact_email = $data[0]->contact_person_email;
        $contact_mobile = $data[0]->contact_person_mobile;
        $bdm_name = $data[0]->bdm_name;
        $start_datetime = date('d/m/Y h:i A', strtotime($data[0]->start_datetime));
        $feedback_link = env('PARTNER_RANKMF_STATIC_WEB_URL') .'/meeting-feedback/email/'. Crypt::encryptString($input_arr['id']);
        // $feedback_link = GetShortUrl($feedback_link, env('PARTNER_RANKMF_STATIC_WEB_URL'), "bdm meeting feedback link for customer");

        // sending an email, if it's not already sent
        $update_data = array();
        if(isset($data[0]->email_sent_to_customer) && intval($data[0]->email_sent_to_customer) != 1){
            // by default sending email template of NON EMPANELLED user
            $email_template_id = 'SMF-DST-MEETING-FEEDBACK-NON-EMPLEMENEL';
            if(isset($data[0]->ARN) && !empty($data[0]->ARN)){
                // checking whether data is available for ARN in MySQL table: user_account or not
                $useraccount_data =  DB::table('user_account')
                                        ->select('ARN')
                                        ->where('ARN', '=', $data[0]->ARN)->get();

                if(!$useraccount_data->isEmpty() && isset($useraccount_data[0])){
                    $email_template_id = 'SMF-DST-MEETING-FEEDBACK-EMPANELLED';
                }
                unset($useraccount_data);
            }

            // sending an email notification to customer
            $email_response = sendEmailNotification(array('email_template_id' => $email_template_id,
                                                    'email_id' => $contact_email,
                                                    'parameters' => array('Name' => $contact_name,
                                                                        'rmname' => $bdm_name,
                                                                        'MEETING_START_DATETIME' => $start_datetime,
                                                                        'feedbackurl' => $feedback_link,
                                                    ),
                                                    'email_for' => 'meeting feedback notification with BDM',
                                                    'email_from_id' => 'feedback@samcomf.com',
                                                    'reply_to' => 'mfdistributor@samcomf.com'
                                                )
                                            );
            if($email_response['response'] == 'email sent'){
                $update_data['email_sent_to_customer'] = 1;
            }
            else{
                $err_msg = $email_response['err_msg'];
            }
            unset($email_response);
        }

        // sending a SMS, if it's not already sent
        if(isset($data[0]->sms_sent_to_customer) && intval($data[0]->sms_sent_to_customer) != 1){
			$input_arr = encrypt_decrypt('encrypt',$input_arr['id']);
            $feedback_link = env('PARTNER_RANKMF_STATIC_WEB_URL') .'Dashboard/feedback/'. $input_arr;
		
            // $feedback_link = 'https://www.samcomf.com';
            // sending a SMS notification to customer
            $msgtxt = "Hi {$contact_name}, Your feedback will help us improve, Please let us know your experience of today's meeting with {$bdm_name}. Feedback Form: {$feedback_link}";
            $sms_data           = [];
            $sms_data['message']=$msgtxt ;
            $sms_data['numbers']=array($contact_mobile);
            $sms_data['sender']="SMFDST";
            sendSms($sms_data);
            $update_data['sms_sent_to_customer'] = 1;
            unset($sms_data, $msgtxt);
        }
                            
        $where_conditions = array(array('id', '=', $input_arr['id']));
        if(is_array($update_data) && count($update_data) > 0){
            $update = MeetinglogModel::UpdateMeetingLogByID(array('where' => $where_conditions, 'data' => $update_data));
            if($update == true){
                return response()->json([
                    'status' => 'success',
                    'msg' => array('Notification sent to the customer'),
                ]);
            }
        }
        else{
            if(count($err_msg) == 0){
                $err_msg = array('Unable to process your request, try again later');
            }
            return response()->json([
                'status' => 'failed',
                'msg' => $err_msg
            ]);
        }
        unset($where_in_conditions, $where_conditions, $update_data, $email_template_id);
        unset($contact_name, $contact_email,$contact_mobile, $bdm_name, $start_datetime, $feedback_link);
    }

    public function getMeetingLogforAPI(Request $request){
        $input_arr = $request->all();
        $input_arr['get_user_meetings'] = 1;
        $input_arr['export_data'] = 1;
        $input_arr['from_date'] = $input_arr['from_date']??'';
        $input_arr['to_date'] = $input_arr['to_date']??'';

        if(isset($input_arr['from_date']) && !empty($input_arr['from_date'])){
            $input_arr['from_date'] = $input_arr['from_date'] .' 00:00:00';
            $input_arr['from_date'] =  date('Y-m-d H:i:s', strtotime($input_arr['from_date']));
        }

        if(isset($input_arr['to_date']) && !empty($input_arr['to_date'])){
            $input_arr['to_date'] = $input_arr['to_date'] .' 23:59:59';
            $input_arr['to_date'] =  date('Y-m-d H:i:s', strtotime($input_arr['to_date']));
        }

        $input_arr['columns'] = array(
                    array('data' => 'bdm_name', 'search' => array('value' => ($input_arr['bdm_name']??''))),
                    array('data' => 'bdm_email', 'search' => array('value' => ($input_arr['bdm_email']??''))),
                    array('data' => 'bdm_mobile', 'search' => array('value' => ($input_arr['bdm_mobile']??''))),
                    array('data' => 'bdm_employee_code', 'search' => array('value' => ($input_arr['bdm_employee_code']??''))),
                    array('data' => 'ARN', 'search' => array('value' => ($input_arr['ARN']??''))),
                    array('data' => 'contact_person_name', 'search' => array('value' => ($input_arr['contact_person_name']??''))),
                    array('data' => 'start_datetime', 'search' => array('value' => $input_arr['from_date'].';'. $input_arr['to_date']))
        );
        $input_arr['columns'] = json_encode($input_arr['columns']);

        $partnersData = MeetinglogModel::getMeetingLogList(array_merge($input_arr,
                                                        array('logged_in_user_roles_and_permissions' => true,
                                                                'flag_have_all_permissions' => true,
                                                                'flag_show_all_arn_data' => true
                                                            )
                                                        )
                                                    );
        return $partnersData;
    }
}
