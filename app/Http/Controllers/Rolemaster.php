<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Exports\ArrayRecordsExport;
use App\Models\RolemasterModel;

class Rolemaster extends Controller
{
    protected $data_table_headings;
    public function __construct(){
        $this->middleware('auth');
        $this->data_table_headings = array('action' => array('label' => 'Action'),
                                           'label' => array('label' => 'Label'),
                                           'status' => array('label' => 'Record Status'),
                                           'created_at' => array('label' => 'Record Created Date')
                                        );
    }

    /**
     * Author: Prasad Wargad
     * Purpose: JIRA ID: SMF-37. Helps to show list of available roles
     * Created: 25/10/2021
     * Modified:
     * Modified by:
     */
    public function index(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // if data is posted then showing records list in datatable or exporting them as per input parameters
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
            $partnersData = RolemasterModel::getRolesList($request->all());
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
                    return \Excel::download(new ArrayRecordsExport($output_arr), 'roles_list_data_'. date('Ymd') .'.xlsx');
                }
            }
            else{
                // coming here if no records are retrieved from MySQL table: esign_vendors_master
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
            // as no data is posted then loading the view
            $data = array('data_table_headings' => $this->data_table_headings);

            // Pass to view
            return view('roles/list')->with($data);
        }
    }

    /**
     * Author: Prasad Wargad
     * Purpose: JIRA ID: SMF-37. Helps to add/edit role details
     * Created: 25/10/2021
     * Modified:
     * Modified by:
     */
    public function addedit(Request $request, $role_id = '')
    {
        $flag_add_edit_record = 'add';
        $known_record_data = array();
        $arr_permissions_list = config('menulist');

        // checking whether we are on editing a record or not
        if(!empty($role_id) && is_numeric($role_id)){
            // checking is passed input parameter $role_id is a valid DB record not
            $retrieved_data = RolemasterModel::getRolesList(array('columns' => array(
                                                                                    array('data' => 'roles.id',
                                                                                          'search' => array('value' => $role_id)
                                                                                        )
                                                                                ),
                                                                 'include_role_permissions' => true,
                                                                 'include_status_check' => false
                                                                )
                                                        );
            if(isset($retrieved_data['records']) && !$retrieved_data['records']->isEmpty() && isset($retrieved_data['records'][0]) && is_object($retrieved_data['records'][0]) && get_object_vars($retrieved_data['records'][0]) > 0){
                $known_record_data = (array) $retrieved_data['records'][0];
                $known_record_data['permissions']['active'] = (isset($known_record_data['permissions']['active'])?array_column($known_record_data['permissions']['active'], 'page_url'):array());
                $known_record_data['permissions']['inactive'] = (isset($known_record_data['permissions']['inactive'])?array_column($known_record_data['permissions']['inactive'], 'page_url'):array());
                $flag_add_edit_record = 'edit';
            }
            else{
                return redirect('roles')->with('error', 'Invalid role id');
            }
            unset($retrieved_data);
        }

        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            $output_arr = array('msg' => '');
            $err_flag = 0;          // err_flag is 0 means no error
            $err_msg = array();     // err_msg stores list of errors found during execution

            $post_data = $request->all();   // stores all posted data
            extract($post_data);
            $role_id = intval($role_id??0);

            $validator = Validator::make($post_data, [
                'txt_label' => 'required|string|min:5|unique:role_master,label,'. $role_id .',id',
            ], array('txt_label.required' => 'txt_label|0|Please enter label for a role',
                     'txt_label.min' => 'txt_label|0|Label should not be less than 5 characters',
                     'txt_label.unique' => 'txt_label|0|Label is already in use')
            );

            if($validator->fails()){
                $err_flag = 1;
                // preparing error message needs to be shown in frontend
                foreach($validator->errors()->all() as $key => $value){
                    $value = explode('|', $value);
                    $err_msg[] = array('element' => $value[0], 'index' => $value[1], 'msg' => $value[2]);
                }
                unset($key, $value);
            }
            unset($validator);

            if($err_flag == 0){
                $txt_status = intval($txt_status??0);
                // $chk_sel_all variable helps to identify whether all permissions should be assigned to a role or not
                $chk_sel_all = intval($chk_sel_all??0);
                // $show_all_arn_data variable helps to identify whether all ARN data needs to be shown or not for a role
                $show_all_arn_data = intval($txt_show_all_arn_data??0);

                // $chk_permission variable helps to identify which are all permissions applied to a role
                if(!isset($chk_permission) || !is_array($chk_permission)){
                    $chk_permission = array();
                }

                // inserting/updating records into MySQL table: role_master
                $role_record_data = array('label' => $txt_label,
                                          'status' => $txt_status,
                                          'have_all_permissions' => $chk_sel_all,
                                          'show_all_arn_data' => $show_all_arn_data);
                if($role_id > 0){
                    // updating the data
                    $retrieved_data = RolemasterModel::updateRoleData($role_record_data, $role_id, $chk_permission);
                }
                else{
                    // inserting role data
                    $retrieved_data = RolemasterModel::insertRoleData($role_record_data, $chk_permission);
                }

                if($retrieved_data['err_flag'] == 1){
                    $err_flag = 1;
                    $err_msg[] = array('element' => 'txt_label', 'index' => 0, 'msg' => implode('<br>',$retrieved_data['err_msg']));
                }
                else{
                    if($role_id > 0){
                        $output_arr['msg'] = 'Role details updated successfully';
                    }
                    elseif($retrieved_data['role_id'] > 0){
                        $output_arr['msg'] = 'Role details added successfully';
                    }
                    // finding permissions available for logged in user again and updating the same in session variable
                    $logged_in_user_roles_and_permissions = \App\Models\UsermasterModel::get_specific_user_role_and_permissions(session('logged_in_user_id'));
                    session(['logged_in_user_roles_and_permissions' => $logged_in_user_roles_and_permissions]);
                    unset($logged_in_user_roles_and_permissions);
                }
                unset($retrieved_data);
            }
            unset($post_data);

            $output_arr['err_flag'] = $err_flag;
            $output_arr['err_msg'] = $err_msg;
            echo json_encode($output_arr);
        }
        else{
            // as data is not posted so user might be in either ADD/EDIT record mode
            $data = array('flag_add_edit_record' => $flag_add_edit_record,
                          'known_record_data' => $known_record_data,
                          'arr_permissions_list' => $arr_permissions_list,
                          'role_id' => $role_id);
            return view('roles/addedit')->with($data);
        }
    }
}
