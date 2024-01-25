<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\AppointmentModel;
use App\Models\UsermasterModel;
use App\Exports\ArrayRecordsExport;
use App\Libraries\PhpMailer;

class AppointmentController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->data_table_headings = array(
                                           //'action' => array('label' => 'Action'),
                                           'service_name' => array('label' => 'Service'),
                                           'user_name' => array('label' => 'Provider'),
                                           'start_datetime' => array('label' => 'Start'),
                                           'end_datetime' => array('label' => 'End'),
                                           'notes' => array('label' => 'Notes'),
                                           'customer_name' => array('label' => 'Customer'),
                                           'customer_email' => array('label' => 'Customer Email'),
                                           'customer_number' => array('label' => 'Customer Number'),
                                           'customer_address' => array('label' => 'Customer Address'),
                                           'customer_city' => array('label' => 'Customer City'),
                                           'customer_state' => array('label' => 'Customer State'),
                                           'customer_zipcode' => array('label' => 'Customer Zipcode'),
                                        );
    }

    public function index(Request $request){
        $data = array('data_table_headings' => $this->data_table_headings);
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
            $partnersData =AppointmentModel::getAppintments($request->all());
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
                    if(isset($csv_headers[0]) && (strtolower($csv_headers[0]) == 'action')){
                        array_shift($csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    }

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
                    return \Excel::download(new ArrayRecordsExport($output_arr),'appointments_data_'. date('Ymd').'.xlsx');
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
        else
        {
            return view("appointment/appointment_list")->with($data);
        }
    }

    public function generate_appointment_list(Request $request){
        // y($request->all());
        extract($request->all());
        // $retrieved_data = UsermasterModel::getMasteruserList(array('columns' => array(
        //                                                             array('data' => 'email',
        //                                                                   'search' => array('value' => $email)
        //                                                                 )
        //                                                         )
        //                                                     )
        //                                                 );
        // x($retrieved_data, 'retrieved_data');
        
        $appointment_data = AppointmentModel::get_appointment_data($request->all());
        $get_bdm_user_list = AppointmentModel::get_bdm_user_list($request->all());
        $response_data = array();
        $update_data = array();
        $where_conditions = array();
        if($appointment_data->isEmpty()){
            $response_data = array('status' => 'warning', 'message' => 'User not found','appointment_url' => '');
        }
        else{
            $user_id = '|'.$appointment_data[0]->id;
            $user_encrypted_id = encrypt_decrypt('encrypt', $user_id);
            $appointment_link = env('SAMCOMF_APPOINTMENT_URL').'/Appointments/index/null/'.$user_encrypted_id;
            
            // update appointment link in users_details table
            $update_data['appointment_link'] = $appointment_link;
            $where_conditions[] = array('user_id', '=', $get_bdm_user_list[0]->user_id);
            $update_data = AppointmentModel::update_data('users_details',$where_conditions,$update_data);
            if(!empty($update_data)){
                $response_data = array('status' => 'success', 'message' => 'Record updated and link generated successfully', 'appointment_url' => $appointment_link);
            }elseif(!isset($get_bdm_user_list[0]->appointment_link) && empty($get_bdm_user_list[0]->appointment_link)){
                $response_data = array('status' => 'error', 'message' => 'User not found link not generated','appointment_url' => '');
            }
            else{
                $response_data = array('status' => 'success', 'message' => 'No record updated', 'appointment_url' => $get_bdm_user_list[0]->appointment_link);
            }
        }
        return response()->json($response_data, 200);
    }

    // public function test_mail(){
    //     $url = "https://api.elasticemail.com/v2/email/send?apikey=8475390EFDD4C1E781B61E4AA6F99A478AA6F65FEBC80D2D1FDB9ACA36DBFBBAA8DEBCE28BAF4F64CF3AFBF874F5D60D&subject=testing mail using api&from=alerts@samcomf.com&fromName=Samco Mutual Fund&to=dharmesh.patel@samco.in&bodyHtml=testing mail api&poolName=transactional&isTransactional=true";

    //     $tranaction = "https://api.elasticemail.com/v2/email/getstatus?apikey=8475390EFDD4C1E781B61E4AA6F99A478AA6F65FEBC80D2D1FDB9ACA36DBFBBAA8DEBCE28BAF4F64CF3AFBF874F5D60D&transactionID=2414d064-7fd8-81ba-8064-aeb46b046c1f";

    //     $status = "https://api.elasticemail.com/v2/email/status?apikey=8475390EFDD4C1E781B61E4AA6F99A478AA6F65FEBC80D2D1FDB9ACA36DBFBBAA8DEBCE28BAF4F64CF3AFBF874F5D60D&messageID=";
       
    // }
}
