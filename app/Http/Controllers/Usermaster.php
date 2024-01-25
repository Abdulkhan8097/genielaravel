<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ArrayRecordsExport;
use Illuminate\Support\Facades\DB;
use Hash;
use App\Models\UsermasterModel;
class Usermaster extends Controller
{
    protected $data_table_headings;
    protected $logged_in_user_roles_and_permissions, $flag_have_all_permissions, $flag_show_all_arn_data, $logged_in_user_id;
    public function __construct(){
        $this->middleware('auth');
        $this->data_table_headings = array('action' => array('label' => 'Action'),
                                           'name' => array('label' => 'Name'),
                                           'email' => array('label' => 'Email'),
                                           'mobile_number' => array('label' => 'Mobile'),
                                            'employee_code'=>array('label' =>'Employee Code'),
                                            'designation'=>array('label' =>'Designation'),
                                            'reporting_to'=>array('label' =>'Reporting To'),
                                            'role_id'=>array('label' =>'Role Name'),
                                            'cadre_of_employee'=>array('label' =>'Cadre Of Employee'),
                                            'serviceable_pincode'=>array('label' =>'Serviceable Pincode'),
                                            'status'=>array('label' =>'Status'),
                                            'rating'=>array('label' =>'Overall Rating And Response'),
                                           
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
            elseif(isset($this->logged_in_user_roles_and_permissions['role_permissions']) && is_array($this->logged_in_user_roles_and_permissions['role_permissions']) && in_array('show-all-users', $this->logged_in_user_roles_and_permissions['role_permissions']) !== FALSE){
                $this->flag_show_all_arn_data = true;
            }
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $data = array('data_table_headings' => $this->data_table_headings);
        // retrieving ACTIVE roles
        $active_roles = DB::table('role_master')
                            ->where('status', '=',1)
                            ->get();
        $data['role_list'] = $active_roles;
        unset($active_roles);

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
            //print_r($request->all());
            $partnersData =UsermasterModel::getMasteruserList(array_merge($request->all(),
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
                    //print_r($partnersData);
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
                    return \Excel::download(new ArrayRecordsExport($output_arr),'userlist_master_data_'. date('Ymd').'.xlsx');
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
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            $data['logged_in_user_id'] = $this->logged_in_user_id;
            return view('usersmaster/list')->with($data);
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email:filter',
            'designation' => 'required',
            'uname' => 'required',
            'mobile_number' => 'required',
            'employee_code' => 'required',
            'role_id' => 'required',
        ]);
        $password=1234;
        $user_add=array('name'=>$request['uname'],'email'=>$request['email'],'password' => Hash::make($password),'is_drm_user'=>1);
        $users=DB::table('users')->insert($user_add);
        $lastInsertedID=DB::getPdo()->lastInsertId();
        //print_r($lastInsertedID);die;
        $users_details=array('user_id'=>$lastInsertedID,'role_id'=>$request['role_id'],'employee_code' =>$request['employee_code'],'mobile_number'=>$request['mobile_number'],'designation'=>$request['designation'],'reporting_to'=>$request['reporting_to'],'cadre_of_employee'=>$request['cadre_of_employee'],'serviceable_pincode'=>$request['serviceable_pincode'],'skip_in_arn_mapping'=>$request['skip_in_arn_mapping'],'status'=>1);
        $users=DB::table('users_details')->insert($users_details);
        return redirect('usermasterlist')->with('success','Created successfully.');
    }
    function duplicateemail(Request $request){
        //print_r($request['email']);
        $users = DB::table('users')
             ->select(DB::raw('count(*) as id'))
             ->where('email', '=', $request['email'])
             ->get();
             $list=$users[0];
             //print_r($list->id);
             if(!empty($list->id))
             {
              return response()->json(['status'=>'error']);
             }else{
                return response()->json(['status'=>'success']);
             }
    }
    function get_edit_detail(Request $request)
    {
      $records = DB::table('users')
                 ->join('users_details', 'users.id', '=','users_details.user_id')
                ->select('users.name', 'users.email','users_details.employee_code','users_details.mobile_number','users_details.designation','users_details.id','users_details.role_id','users.id as uid','users_details.reporting_to','users_details.serviceable_pincode','users_details.cadre_of_employee','users_details.status','users_details.skip_in_arn_mapping')
                ->where('users_details.id', '=', $request['id'])
                ->where('users.is_drm_user', '=', 1)
                ->get();
              echo json_encode($records);  
    }
    function updateusermaster(Request $request)
    {
       DB::table('users')
            ->where('id', $request['editidu'])
            ->update(['name'=>$request['uname'],'email'=>$request['email'],'status'=>$request['status'],'is_drm_user'=>1]);
            DB::table('users_details')
            ->where('id', $request['editid'])
            ->update(['role_id'=>$request['role_id'],'employee_code'=>$request['employee_code'],'designation'=>$request['designation'],'mobile_number'=>$request['mobile_number'],'reporting_to'=>$request['reporting_to_edit'],'cadre_of_employee'=>$request['cadre_of_employee'],'serviceable_pincode'=>$request['serviceable_pincode'],'skip_in_arn_mapping'=>$request['skip_in_arn_mapping'],'status'=>$request['status']]);
            return redirect('usermasterlist')->with('success','Updated successfully.');
    }
    function get_reporting(Request $request){
        $role_list_user = DB::table('role_master')
        ->where('status', '=',1)
        ->get();
        $records = DB::table('users')
                 ->join('users_details', 'users.id', '=','users_details.user_id')
                ->select('users.name', 'users.email','users_details.employee_code','users_details.mobile_number','users_details.designation','users_details.id','users_details.role_id','users.id as uid')
                ->where('users_details.role_id', '<>', $request['role_id'])
                ->where('users_details.status', '=', 1)
				->where('users_details.is_deleted', '=',0)
				->where('users_details.is_old', '=',0)
                ->where('users.is_drm_user', '=', 1)
                ->orderByRaw('users_details.role_id ASC')
                ->get();
                $role_list=array();
                $role_duplicate=array();
                foreach($role_list_user as $key=>$val)
                { //print_r($val);
                  $role_list[$val->id]=$val->label;
                }
                //print_r($role_list);
                $i=0;
                $html_option='<option value=""></option>'; 
                foreach($records as $key=>$val)
                {
                  if (in_array($val->role_id, $role_duplicate)) 
                  {
                   $html_option.='<option value="'.$val->uid.'">'.$val->name.'</option>'; 
                  } else{
                    if($i==0)
                    {

                     $html_option.='<optgroup label="'.$role_list[$val->role_id].'">';

                     $html_option.='<option value="'.$val->uid.'">'.$val->name.'</option>'; 
                    }else{
                       $html_option.='</optgroup>';
                       $html_option.='<optgroup label="'.$role_list[$val->role_id].'">'; 
                       $html_option.='<option value="'.$val->uid.'">'.$val->name.'</option>'; 
                    }
                    $i++;
                    array_push($role_duplicate,$val->role_id);
                  }
                 
                }
        
                      echo $html_option;
    }
    function get_services_pincode(Request $request){
        $service_list = DB::table('users_details')
        ->where('id', '=',$request['id'])
        ->get();
        $list_pincode=explode(",", $service_list[0]->serviceable_pincode);
        $table_list='<table class="table table-striped"><thead><tr><th></th></tr></thead><tbody><tr><td>'.$service_list[0]->serviceable_pincode.' </td></tr>';
        $i=1;
        /*foreach($list_pincode as $value)
        {
          $table_list.='<tr><td>'.$i.'<t/d><td>'.$value.'</td></tr>';
          $i++;
        }*/
        $table_list.='</tbody></table>';
        echo $table_list;
    }
}
