<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\FileUploadController;
use App\Models\UsermasterModel;
use DB;

class ImportFileController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $amc_master_list = UsermasterModel::get_amc_list();
        return view("upload/upload-file")->with('data',array('amc_list' => $amc_master_list));
    }

    public function saveUploadData(Request $request){
        $post_data = $request->all();   // stores all posted data
        extract($post_data);
        // x($post_data,'post_data');
        $file_upload_api_url = env('APP_URL') .'/api/admin-file-uploads';
        $allowed_mime_types = 'text/csv,text/plain,application/csv,text/comma-separated-values,text/anytext,application/octet-stream,application/txt,text/x-comma-separated-values,application/x-csv,text/x-csv';
        $uploadfile = $request->file('uploadfile');
        $uploadfile_saving_filename = pathinfo($uploadfile->getClientOriginalName())['filename'] .'_'.uniqid(). '.'. $uploadfile->getClientOriginalExtension();
        $uploadfile_saving_filename = str_replace(' ', '_', $uploadfile_saving_filename);

        $response = get_content_by_curl($file_upload_api_url, array('file' => new \CURLFile($uploadfile->getRealPath(), $uploadfile->getClientMimeType(), $uploadfile->getClientOriginalName()),
                                                                    'upload_path' => 'app/public/'. $file_type,
                                                                    'allowed_types' => $allowed_mime_types,
                                                                    'max_size' => (1024 * 10),
                                                                    'file_extension' => $uploadfile->getClientOriginalExtension(),
                                                                    'saving_file_name' => $uploadfile_saving_filename)
        );
        // y($response, 'response');

        // replacing COMMA separated values with DOUBLE SEMICOLON so that those values should get processed properly
        switch($file_type){
            case 'amficity_mapped_zone':
            case 'aum_data':
                $saved_file_details = storage_path() . '/app/public/'. $file_type .'/'. $uploadfile_saving_filename;
                $file_data = array_map('str_getcsv', file($saved_file_details));
                array_walk_recursive($file_data, function(&$_value){
                    $_value = str_replace(',', ';;', trim(strip_tags($_value)));
                });
                $fp = fopen($saved_file_details, 'w');
                foreach($file_data as $records){
                    fputcsv($fp, $records);
                }
                fclose($fp);
                unset($records, $saved_file_details, $file_data, $fp);
                break;
            case 'quote_data_index_date_and_mosdex':
                $saved_file_details = storage_path() . '/app/public/'. $file_type .'/'. $uploadfile_saving_filename;
                $file_data = array_map('str_getcsv', file($saved_file_details));
                // removing starting SINGLE QUOTE coming before date column
                array_walk_recursive($file_data, function(&$_value){
                    $_value = str_replace("'", '', trim(strip_tags($_value)));
                });

                $arr_available_quote_data_index_symbols = \App\Models\QuoteDataIndexDetails::select('symbol')->get()->toArray();
                if($arr_available_quote_data_index_symbols && is_array($arr_available_quote_data_index_symbols) && count($arr_available_quote_data_index_symbols) > 0){
                    $arr_available_quote_data_index_symbols = array_column($arr_available_quote_data_index_symbols, 'symbol');
                }

                // if index symbols are not available then creating an array with symbol element as -1 which further result in finding no records during update queries instead of updating MosDex values against all symbols
                if(!is_array($arr_available_quote_data_index_symbols) || (is_array($arr_available_quote_data_index_symbols) && count($arr_available_quote_data_index_symbols) == 0)){
                    $arr_available_quote_data_index_symbols = array(-1);
                }

                // importing records uploaded from CSV file into MySQL table: quote_data_index_history
                if(is_array($file_data) && count($file_data) > 0){
                    $no_of_records_imported = 0;
                    foreach($file_data as $record_key => $records){
                        if($record_key == 0){
                            // considering first row as heading only, so skipping it from getting inserted
                            continue;
                        }

                        // 0th column is MosDex, if it's data is either blank or not numeric then keeping it as zero
                        if(!isset($records[0]) || (isset($records[0]) && (empty($records[0]) || strtotime($records[0]) === FALSE))){
                            $records[0] = '';
                        }
                        // 1st column is Multiplier Value, if it's data is either blank or not numeric then keeping it as zero
                        if(!isset($records[1]) || (isset($records[1]) && !is_numeric($records[1]))){
                            $records[1] = 0;
                        }

                        if(!empty($records[0])){
                            try{
                                $record_updating_conditions = array();
                                // checking whether do we have one more record after this looping record, if yes then updating MosDex between current loop date and next record date
                                if(isset($file_data[($record_key + 1)]) && isset($file_data[($record_key + 1)][0]) && !empty($file_data[($record_key + 1)][0]) && strtotime($file_data[($record_key + 1)][0]) !== FALSE){
                                    $record_updating_conditions[] = array('index_date', '>=', $records[0]);
                                    $record_updating_conditions[] = array('index_date', '<', $file_data[($record_key + 1)][0]);
                                }
                                else{
                                    $record_updating_conditions[] = array('index_date', '=', $records[0]);
                                }
                                $arr_updating_data['margin_of_safety'] = $records[1];
                                \App\Models\QuoteDataIndexHistory::where($record_updating_conditions)->whereIn('symbol', $arr_available_quote_data_index_symbols)->update($arr_updating_data);
                                $no_of_records_imported++;
                                unset($record_updating_conditions, $arr_updating_data);
                            }
                            catch(Exception $e){
                                $response = [
                                    'status' => 'error',
                                    'message' => 'Validation Errors',
                                    'data' => 'Error in importing index datewise mosdex data records: '. $e->getMessage()
                                ];
                                return response()->json($response, 200);
                            }
                            catch(\Illuminate\Database\QueryException $e){
                                $response = [
                                    'status' => 'error',
                                    'message' => 'Validation Errors',
                                    'data' => 'Error in importing index datewise mosdex data records: '. $e->getMessage()
                                ];
                                return response()->json($response, 200);
                            }
                        }
                    }
                    unset($file_data, $record_key, $records);
                    $output_arr = array('status' => 'success' ,'message' => "File uploaded successfully\nNumber of records processed are total: ". $no_of_records_imported);
                    return response()->json($output_arr, 200);
                }
                else{
                    $output_arr = array('status' => 'success' ,'message' => "No records found in file for importing");
                    return response()->json($output_arr, 200);
                }
                break;
            case 'mos_multiplier_data':
                $saved_file_details = storage_path() . '/app/public/'. $file_type .'/'. $uploadfile_saving_filename;
                $file_data = array_map('str_getcsv', file($saved_file_details));

                $validator = Validator::make($request->all(), [
                    'multiplier_type' => 'required|max:10',
                ]);

                if($validator->fails()){
                    $errors = $validator->errors();
                    $data['errors'] = $errors->all();
                    $response = [
                        'status' => 'error',
                        'message' => 'Validation Errors',
                        'data' => implode("\n", $data['errors'])
                    ];
                    return response()->json($response, 200);
                }

                // deleting any existing records from MySQL table: mos_multiplier_data
                if(isset($multiplier_type) && !empty($multiplier_type)){
                    try{
                        \App\Models\MosMultiplierData::where(
                                                        array(
                                                            array('multiplier_type', '=', ($multiplier_type??''))
                                                        )
                                                    )->delete();
                    }
                    catch(Exception $e){
                        $response = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => 'Error in deleting existing multiplier type records: '. $e->getMessage()
                        ];
                        return response()->json($response, 200);
                    }
                    catch(\Illuminate\Database\QueryException $e){
                        $response = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => 'Error in deleting existing multiplier type records: '. $e->getMessage()
                        ];
                        return response()->json($response, 200);
                    }
                }

                // importing records uploaded from CSV file into MySQL table: mos_multiplier_data
                if(is_array($file_data) && count($file_data) > 0){
                    $arr_inserting_records = array();
                    foreach($file_data as $record_key => $records){
                        if($record_key == 0){
                            // considering first row as heading only, so skipping it from getting inserted
                            continue;
                        }

                        // 0th column is MosDex, if it's data is either blank or not numeric then keeping it as zero
                        if(!isset($records[0]) || (isset($records[0]) && !is_numeric($records[0]))){
                            $records[0] = 0;
                        }
                        // 1st column is Multiplier Value, if it's data is either blank or not numeric then keeping it as zero
                        if(!isset($records[1]) || (isset($records[1]) && !is_numeric($records[1]))){
                            $records[1] = 0;
                        }
                        $arr_inserting_records[] = array('multiplier_type' => $multiplier_type,
                                                         'margin_of_safety' => $records[0],
                                                         'multiplier_value' => $records[1]);
                    }
                    unset($file_data, $record_key, $records);

                    if(is_array($arr_inserting_records) && count($arr_inserting_records) > 0){
                        try{
                            \App\Models\MosMultiplierData::insert($arr_inserting_records);
                            $no_of_records_imported = \App\Models\MosMultiplierData::where(
                                                                        array(
                                                                            array('multiplier_type', '=', ($multiplier_type??''))
                                                                        )
                                                                    )->count();
                            $output_arr = array('status' => 'success' ,'message' => "File uploaded successfully\nNumber of records processed are total: ". $no_of_records_imported);
                            return response()->json($output_arr, 200);
                        }
                        catch(Exception $e){
                            $response = [
                                'status' => 'error',
                                'message' => 'Validation Errors',
                                'data' => 'Error in importing mosdex multiplier data records: '. $e->getMessage()
                            ];
                            return response()->json($response, 200);
                        }
                        catch(\Illuminate\Database\QueryException $e){
                            $response = [
                                'status' => 'error',
                                'message' => 'Validation Errors',
                                'data' => 'Error in importing mosdex multiplier data records: '. $e->getMessage()
                            ];
                            return response()->json($response, 200);
                        }
                    }
                    else{
                        $output_arr = array('status' => 'success' ,'message' => "No records found importing");
                        return response()->json($output_arr, 200);
                    }
                    unset($arr_inserting_records);
                }
                else{
                    $output_arr = array('status' => 'success' ,'message' => "No records found in file for importing");
                    return response()->json($output_arr, 200);
                }
                break;
        }

        if(!empty($response) && json_decode($response) !== FALSE){
            $response = json_decode($response, true);
            if(isset($response['status']) && ($response['status'] == 'error') && isset($response['data']['errors']) && is_array($response['data']['errors']) && count($response['data']['errors']) > 0){
                $response = [
                    'status' => 'error',
                    'message' => 'Validation Errors',
                    'data' => implode("\n", $response['data']['errors'])
                ];
                return response()->json($response, 200);
            }
            elseif($response['status'] == 'success'){
                $return_var = array();
                $output_arr = array();
                // calling shell script to import the data drm_uploaded_arn_project_focus_yes_no
                if(!empty($file_type) && $file_type == 'arn_project_focus_yesno'){
                    exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/import_csv_data.sh -i"'. storage_path('app/public/'. $file_type .'/'. $uploadfile_saving_filename) .'" "drm_uploaded_arn_project_focus_yes_no" "IGNORE 1 LINES (@ARN, @project_focus, @status) SET ARN = TRIM(REPLACE(REPLACE(@ARN, \'\r\', \'\'), \'\n\', \'\')), project_focus = TRIM(REPLACE(REPLACE(@project_focus, \'\r\', \'\'), \'\n\', \'\')), status = 1" "no"', $return_var, $exit_code);
                    $return_var_msg = array();
                    if(is_array($return_var) && count($return_var) >= 2){
                        $return_var_msg[] = end($return_var);
                        $return_var_msg[0] = prev($return_var) .': '. $return_var_msg[0];
                    }
                    if($exit_code != 0){
                        $output_arr = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => array_merge(array('Unable to process your request'), $return_var_msg)
                        ];
                    }
                    $output_arr = array('status' => 'success' ,'message' => implode("\n",array_merge(array('File uploaded successfully'), $return_var_msg)));
                }
                // calling shell script to import the data drm_uploaded_arn_distributor_category
                elseif(!empty($file_type) && $file_type == 'arn_distributor_category'){
                    exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/import_csv_data.sh -i"'. storage_path('app/public/'. $file_type .'/'. $uploadfile_saving_filename) .'" "drm_uploaded_arn_distributor_category" "IGNORE 1 LINES (@ARN, @distributor_category, @status) SET ARN = TRIM(REPLACE(REPLACE(@ARN, \'\r\', \'\'), \'\n\', \'\')), distributor_category = TRIM(REPLACE(REPLACE(@distributor_category, \'\r\', \'\'), \'\n\', \'\')), status = 1" "no"', $return_var, $exit_code);
                    $return_var_msg = array();
                    if(is_array($return_var) && count($return_var) >= 2){
                        $return_var_msg[] = end($return_var);
                        $return_var_msg[0] = prev($return_var) .': '. $return_var_msg[0];
                    }
                    if($exit_code != 0){
                        $output_arr = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => array_merge(array('Unable to process your request'), $return_var_msg)
                        ];
                    }
                    $output_arr = array('status' => 'success' ,'message' => implode("\n",array_merge(array('File uploaded successfully'), $return_var_msg)));
                }
                // calling shell script to import the data drm_uploaded_pincode_city_state
                elseif(!empty($file_type) && $file_type == 'pincode_data'){
                    exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/import_csv_data.sh -i"'. storage_path('app/public/'. $file_type .'/'. $uploadfile_saving_filename) .'" "drm_uploaded_pincode_city_state" "IGNORE 1 LINES (@pincode, @city, @state, @status) SET pincode = TRIM(REPLACE(REPLACE(@pincode, \'\r\', \'\'), \'\n\', \'\')), city = TRIM(REPLACE(REPLACE(@city, \'\r\', \'\'), \'\n\', \'\')), state = TRIM(REPLACE(REPLACE(@state, \'\r\', \'\'), \'\n\', \'\')), status = 1" "no"', $return_var, $exit_code);
                    $return_var_msg = array();
                    if(is_array($return_var) && count($return_var) >= 2){
                        $return_var_msg[] = end($return_var);
                        $return_var_msg[0] = prev($return_var) .': '. $return_var_msg[0];
                    }
                    if($exit_code != 0){
                        $output_arr = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => array_merge(array('Unable to process your request'), $return_var_msg)
                        ];
                        return response()->json($response, 200);
                    }
                    $output_arr = array('status' => 'success' ,'message' => implode("\n",array_merge(array('File uploaded successfully'), $return_var_msg)));
                }
                // calling shell script to import the data drm_uploaded_arn_average_aum_total_commission_data
                elseif(!empty($file_type) && $file_type == 'aum_data'){
                    exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/import_csv_data.sh -i"'. storage_path('app/public/'. $file_type .'/'. $uploadfile_saving_filename) .'" "drm_uploaded_arn_average_aum_total_commission_data" "IGNORE 1 LINES (@ARN, @arn_avg_aum, @arn_total_commission, @arn_yield, @arn_business_focus_type, @status) SET ARN = TRIM(REPLACE(REPLACE(@ARN, \'\r\', \'\'), \'\n\', \'\')), arn_avg_aum = TRIM(REPLACE(REPLACE(REPLACE(@arn_avg_aum,\',\',\'\'), \'\r\', \'\'), \'\n\', \'\')), arn_total_commission = TRIM(REPLACE(REPLACE(REPLACE(@arn_total_commission,\',\',\'\'), \'\r\', \'\'), \'\n\', \'\')), arn_yield = TRIM(REPLACE(REPLACE(@arn_yield, \'\r\', \'\'), \'\n\', \'\')), arn_business_focus_type = TRIM(REPLACE(REPLACE(REPLACE(@arn_business_focus_type, \'\r\', \'\'), \'\n\', \'\'), \';;\', \',\')), status = 1" "no"',$return_var, $exit_code);
                    $return_var_msg = array();
                    if(is_array($return_var) && count($return_var) >= 2){
                        $return_var_msg[] = end($return_var);
                        $return_var_msg[0] = prev($return_var) .': '. $return_var_msg[0];
                    }
                    if($exit_code != 0){
                        $output_arr = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => array_merge(array('Unable to process your request'), $return_var_msg)
                        ];
                    }
                    $output_arr = array('status' => 'success' ,'message' => implode("\n",array_merge(array('File uploaded successfully'), $return_var_msg)));
                }
                // calling shell script to import the data drm_project_focus_amc_wise_details
                elseif(!empty($file_type) && $file_type == 'arn_amc_project_focus'){
                    exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/import_csv_data.sh -i"'. storage_path('app/public/'. $file_type .'/'. $uploadfile_saving_filename) .'" "drm_project_focus_amc_wise_details" "IGNORE 1 LINES (@ARN, @total_commission_expenses_paid, @gross_inflows, @net_inflows, @avg_aum_for_last_reported_year, @closing_aum_for_last_financial_year, @amc_name, @reported_year, @status) SET ARN = TRIM(REPLACE(REPLACE(@ARN, \'\r\', \'\'), \'\n\', \'\')), total_commission_expenses_paid = TRIM(REPLACE(REPLACE(REPLACE(@total_commission_expenses_paid,\',\',\'\'), \'\r\', \'\'), \'\n\', \'\')), gross_inflows = TRIM(REPLACE(REPLACE(REPLACE(@gross_inflows,\',\',\'\'), \'\r\', \'\'), \'\n\', \'\')), net_inflows = TRIM(REPLACE(REPLACE(REPLACE(@net_inflows,\',\',\'\'), \'\r\', \'\'), \'\n\', \'\')), avg_aum_for_last_reported_year = TRIM(REPLACE(REPLACE(REPLACE(@avg_aum_for_last_reported_year,\',\',\'\'), \'\r\', \'\'), \'\n\', \'\')), closing_aum_for_last_financial_year = TRIM(REPLACE(REPLACE(REPLACE(@closing_aum_for_last_financial_year,\',\',\'\'), \'\r\', \'\'), \'\n\', \'\')), amc_name = \"'. ($post_data['amc_name'] ?? '') .'\", reported_year = '. ($post_data['reporting_year'] ?? date('Y')) .', status = 1" "no"',$return_var, $exit_code);
                    $return_var_msg = array();
                    if(is_array($return_var) && count($return_var) >= 2){
                        $return_var_msg[] = end($return_var);
                        $return_var_msg[0] = prev($return_var) .': '. $return_var_msg[0];
                    }
                    if($exit_code != 0){
                        $output_arr = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => array_merge(array('Unable to process your request'), $return_var_msg)
                        ];
                    }
                    $output_arr = array('status' => 'success' ,'message' => implode("\n",array_merge(array('File uploaded successfully'), $return_var_msg)));
                }
                // calling shell script to import the data alternate_mobile_email_data
                elseif(!empty($file_type) && $file_type == 'alternate_mobile_email_data'){
                    exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/import_csv_data.sh -i"'. storage_path('app/public/'. $file_type .'/'. $uploadfile_saving_filename) .'" "arn_alternate_details" "IGNORE 1 LINES (@ARN, @alternate_mobile_1, @alternate_mobile_2, @alternate_mobile_3, @alternate_mobile_4, @alternate_mobile_5, @alternate_email_1, @alternate_email_2, @alternate_email_3, @alternate_email_4, @alternate_email_5, @status) SET ARN = TRIM(REPLACE(REPLACE(@ARN, \'\r\', \'\'), \'\n\', \'\')), alternate_mobile_1 = TRIM(REPLACE(REPLACE(@alternate_mobile_1, \'\r\', \'\'), \'\n\', \'\')), alternate_mobile_2 = TRIM(REPLACE(REPLACE(@alternate_mobile_2, \'\r\', \'\'), \'\n\', \'\')), alternate_mobile_3 = TRIM(REPLACE(REPLACE(@alternate_mobile_3, \'\r\', \'\'), \'\n\', \'\')), alternate_mobile_4 = TRIM(REPLACE(REPLACE(@alternate_mobile_4, \'\r\', \'\'), \'\n\', \'\')), alternate_mobile_5 = TRIM(REPLACE(REPLACE(@alternate_mobile_5, \'\r\', \'\'), \'\n\', \'\')), alternate_email_1 = TRIM(REPLACE(REPLACE(@alternate_email_1, \'\r\', \'\'), \'\n\', \'\')), alternate_email_2 = TRIM(REPLACE(REPLACE(@alternate_email_2, \'\r\', \'\'), \'\n\', \'\')), alternate_email_3 = TRIM(REPLACE(REPLACE(@alternate_email_3, \'\r\', \'\'), \'\n\', \'\')), alternate_email_4 = TRIM(REPLACE(REPLACE(@alternate_email_4, \'\r\', \'\'), \'\n\', \'\')), alternate_email_5 = TRIM(REPLACE(REPLACE(@alternate_email_5, \'\r\', \'\'), \'\n\', \'\')), status = 1" "no"',$return_var, $exit_code);
                    $return_var_msg = array();
                    if(is_array($return_var) && count($return_var) >= 2){
                        $return_var_msg[] = end($return_var);
                        $return_var_msg[0] = prev($return_var) .': '. $return_var_msg[0];
                    }
                    if($exit_code != 0){
                        $output_arr = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => array_merge(array('Unable to process your request'), $return_var_msg)
                        ];
                    }
                    $output_arr = array('status' => 'success' ,'message' => implode("\n",array_merge(array('File uploaded successfully'), $return_var_msg)));
                }
                // calling shell script to import the data drm_uploaded_arn_ind_aum_data
                elseif(!empty($file_type) && $file_type == 'arn_ind_aum_data'){
                    exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/import_csv_data.sh -i"'. storage_path('app/public/'. $file_type .'/'. $uploadfile_saving_filename) .'" "drm_uploaded_arn_ind_aum_data" "IGNORE 1 LINES (@ARN, @total_ind_aum, @ind_aum_as_on_date, @status) SET ARN = TRIM(REPLACE(REPLACE(@ARN, \'\r\', \'\'), \'\n\', \'\')), total_ind_aum = TRIM(REPLACE(REPLACE(@total_ind_aum, \'\r\', \'\'), \'\n\', \'\')), ind_aum_as_on_date = STR_TO_DATE(@ind_aum_as_on_date,\'%Y-%m-%d\'), status = 1" "no"',$return_var, $exit_code);
                    $return_var_msg = array();
                    if(is_array($return_var) && count($return_var) >= 2){
                        $return_var_msg[] = end($return_var);
                        $return_var_msg[0] = prev($return_var) .': '. $return_var_msg[0];
                    }
                    if($exit_code != 0){
                        $output_arr = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => array_merge(array('Unable to process your request'), $return_var_msg)
                        ];
                    }
                    $output_arr = array('status' => 'success' ,'message' => implode("\n",array_merge(array('File uploaded successfully'), $return_var_msg)));
                }
                // calling shell script to import the data drm_uploaded_arn_bdm_mapping
                elseif(!empty($file_type) && $file_type == 'arn_bdm_mapping'){
                    // parameter overwrite decides whether to map BDM against an ARN even though it's RM relationship field is marked as FINAL
                    if(!isset($overwrite)){
                        $overwrite = 'no';
                    }

                    exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/import_csv_data.sh -i"'. storage_path('app/public/'. $file_type .'/'. $uploadfile_saving_filename) .'" "drm_uploaded_arn_bdm_mapping" "IGNORE 1 LINES (@ARN, @bdm_email, @rm_relationship, @status) SET ARN = TRIM(REPLACE(REPLACE(@ARN, \'\r\', \'\'), \'\n\', \'\')), bdm_email = TRIM(REPLACE(REPLACE(@bdm_email, \'\r\', \'\'), \'\n\', \'\')), rm_relationship = TRIM(REPLACE(REPLACE(@rm_relationship, \'\r\', \'\'), \'\n\', \'\')), status = 1" "no" "'. $overwrite .'"',$return_var, $exit_code);
                    $return_var_msg = array();
                    if(is_array($return_var) && count($return_var) >= 2){
                        $return_var_msg[] = end($return_var);
                        $return_var_msg[0] = prev($return_var) .': '. $return_var_msg[0];
                    }
                    if($exit_code != 0){
                        $output_arr = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => array_merge(array('Unable to process your request'), $return_var_msg)
                        ];
                    }
                    $output_arr = array('status' => 'success' ,'message' => implode("\n",array_merge(array('File uploaded successfully'), $return_var_msg)));
                }
                // calling shell script to import the data drm_uploaded_arn_project_emerging_stars_yes_no
                elseif(!empty($file_type) && $file_type == 'arn_project_emerging_stars'){
                    exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/import_csv_data.sh -i"'. storage_path('app/public/'. $file_type .'/'. $uploadfile_saving_filename) .'" "drm_uploaded_arn_project_emerging_stars_yes_no" "IGNORE 1 LINES (@ARN, @project_emerging_stars, @status) SET ARN = TRIM(REPLACE(REPLACE(@ARN, \'\r\', \'\'), \'\n\', \'\')), project_emerging_stars = TRIM(REPLACE(REPLACE(@project_emerging_stars, \'\r\', \'\'), \'\n\', \'\')), status = 1" "no"', $return_var, $exit_code);
                    $return_var_msg = array();
                    if(is_array($return_var) && count($return_var) >= 2){
                        $return_var_msg[] = end($return_var);
                        $return_var_msg[0] = prev($return_var) .': '. $return_var_msg[0];
                    }
                    if($exit_code != 0){
                        $output_arr = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => array_merge(array('Unable to process your request'), $return_var_msg)
                        ];
                    }
                    $output_arr = array('status' => 'success' ,'message' => implode("\n",array_merge(array('File uploaded successfully'), $return_var_msg)));
                }
                // calling shell script to import the data drm_uploaded_project_green_shoots_yes_no
                elseif(!empty($file_type) && $file_type == 'arn_project_green_shoots'){
                    exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/import_csv_data.sh -i"'. storage_path('app/public/'. $file_type .'/'. $uploadfile_saving_filename) .'" "drm_uploaded_project_green_shoots_yes_no" "IGNORE 1 LINES (@ARN, @project_green_shoots, @status) SET ARN = TRIM(REPLACE(REPLACE(@ARN, \'\r\', \'\'), \'\n\', \'\')), project_green_shoots = TRIM(REPLACE(REPLACE(@project_green_shoots, \'\r\', \'\'), \'\n\', \'\')), status = 1" "no"', $return_var, $exit_code);
                    $return_var_msg = array();
                    if(is_array($return_var) && count($return_var) >= 2){
                        $return_var_msg[] = end($return_var);
                        $return_var_msg[0] = prev($return_var) .': '. $return_var_msg[0];
                    }
                    if($exit_code != 0){
                        $output_arr = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => array_merge(array('Unable to process your request'), $return_var_msg)
                        ];
                    }
                    $output_arr = array('status' => 'success' ,'message' => implode("\n",array_merge(array('File uploaded successfully'), $return_var_msg)));
                }
                // calling shell script to import the data drm_uploaded_amfi_city_zone_mapping
                elseif(!empty($file_type) && $file_type == 'amficity_mapped_zone'){
                    exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/import_csv_data.sh -i"'. storage_path('app/public/'. $file_type .'/'. $uploadfile_saving_filename) .'" "drm_uploaded_amfi_city_zone_mapping" "IGNORE 1 LINES (@amfi_city, @mapped_zone, @status) SET amfi_city = TRIM(REPLACE(REPLACE(REPLACE(@amfi_city, \'\r\', \'\'), \'\n\', \'\'), \';;\', \',\')), mapped_zone = TRIM(REPLACE(REPLACE(REPLACE(@mapped_zone, \'\r\', \'\'), \'\n\', \'\'), \';;\', \',\')), status = 1" "no"', $return_var, $exit_code);
                    $return_var_msg = array();
                    if(is_array($return_var) && count($return_var) >= 2){
                        $return_var_msg[] = end($return_var);
                        $return_var_msg[0] = prev($return_var) .': '. $return_var_msg[0];
                    }
                    if($exit_code != 0){
                        $output_arr = [
                            'status' => 'error',
                            'message' => 'Validation Errors',
                            'data' => array_merge(array('Unable to process your request'), $return_var_msg)
                        ];
                    }
                    $output_arr = array('status' => 'success' ,'message' => implode("\n",array_merge(array('File uploaded successfully'), $return_var_msg)));
                }

                if(is_array($return_var) && count($return_var) > 0){
                    $log_file_path = getcwd() .'/storage/logs/import_csv_data_'. date('Y-m-d').'.txt';
                    foreach($return_var as $log_message){
                        @file_put_contents($log_file_path, $log_message. PHP_EOL, FILE_APPEND);
                    }
                    unset($log_file_path);
                }
                return response()->json($output_arr, 200);
            }
        }
    }
}
