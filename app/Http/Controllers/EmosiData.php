<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\ArrayRecordsExport;
use App\Models\EmosiHistoryModel;
use App\Models\EmosiBeerCalculationModel;
use App\Models\EmosiMovingAverage1750CalculationModel;
use App\Models\EmosiModel;
use DB;

class EmosiData extends Controller
{
    protected $data_table_median_beer_headings, $data_table_median_deviation_headings, $data_table_emosi_headings,$data_table_kfin_emosi_headings;
    protected $logged_in_user_roles_and_permissions, $flag_have_all_permissions, $flag_show_all_arn_data, $logged_in_user_id;
    private $APP_ENV, $DOT_NET_API_BASE_URL_WEB_API, $DOT_NET_API_BASE_URL_TRANSACTION_API, $NODEJS_API_BASE_URL, $API_REQUEST_HEADERS;
    public function __construct(){
        $this->middleware('auth');
        $this->data_table_median_beer_headings = array('record_date' => array('label' => 'Record Date'),
                                                       'bond_symbol' => array('label' => 'Bond Symbol'),
                                                       'g_sec_yield' => array('label' => 'G-Sec Yield'),
                                                       'index_symbol' => array('label' => 'NSE Symbol'),
                                                       'pe' => array('label' => 'Price to Earnings Ratio'),
                                                       'earnings_yield' => array('label' => 'Earnings Yield'),
                                                       'beer' => array('label' => 'BEER<br>(Bond Yield to Equity Earnings Return)'),
                                                       'median_beer' => array('label' => 'Median BEER'),
                                                       'created_at' => array('label' => 'Record Created Date'),
                                                    );
        $this->data_table_median_deviation_headings = array('record_date' => array('label' => 'Record Date'),
                                                            'index_symbol' => array('label' => 'NSE Symbol'),
                                                            'index_value' => array('label' => 'NSE Closing Value'),
                                                            'ma_1750' => array('label' => 'Moving average for 1750 days'),
                                                            'deviation_1750' => array('label' => 'Deviation for 1750 days'),
                                                            'emosi_median_deviation_from_ma_1750' => array('label' => 'Median Deviation for 1750 days'),
                                                            'created_at' => array('label' => 'Record Created Date'),
                                                        );
        $this->data_table_emosi_headings = array('record_date' => array('label' => 'Record Date'),
                                                 'index_symbol' => array('label' => 'NSE Symbol'),
                                                 'median_beer' => array('label' => 'Median BEER'),
                                                 'emosi_median_deviation_from_ma_1750' => array('label' => 'Median Deviation for 1750 days'),
                                                 'emosi_value' => array('label' => 'EMOSI Value'),
                                                 'rounded_emosi' => array('label' => 'Rounded EMOSI Value'),
                                                 'created_at' => array('label' => 'Record Created Date'),
                                            );
        $this->data_table_kfin_emosi_headings = array('record_date' => array('label' => 'Record Date'),
                                                    'emosi_value' => array('label' => 'EMOSI Value'),
                                                    'entdt' => array('label' => 'Record Created Date'),
                                                    'upddt' => array('label' => 'Record Updated Date'),
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

        // retrieving application environment, whether it's set to PRODUCTION or DEVELOPMENT.
		$this->APP_ENV = env('APP_ENV');
        $save_emosi_kfin_production_api = getSettingsTableValue('SAVE_EMOSI_KFIN_PRODUCTION_API');
		if((strtolower($this->APP_ENV) == 'production') && ($save_emosi_kfin_production_api == 1)){
			// setting up PRODUCTION credentials
			$this->DOT_NET_API_BASE_URL_WEB_API = 'https://kfadmin.samcomf.com/SamcoApplicationApi';
			$this->DOT_NET_API_BASE_URL_TRANSACTION_API = 'https://kfadmin.samcomf.com/SamcoTransactionApi';
			$this->NODEJS_API_BASE_URL = 'https://kfadmin.samcomf.com';
			$this->API_REQUEST_HEADERS = array();
		}
		else{
			// setting up DEVELOPMENT credentials
			$this->DOT_NET_API_BASE_URL_WEB_API = 'https://clientwebsitesuat2.kfintech.com/SamcoWebapi';
			$this->DOT_NET_API_BASE_URL_TRANSACTION_API = 'https://clientwebsitesuat2.kfintech.com/SamcoTransactionWebapi';
			$this->NODEJS_API_BASE_URL = 'https://clientwebsitesuat3.kfintech.com';
			$this->API_REQUEST_HEADERS = array();
		}
    }

    /**
     * Author: Prasad Wargad
     * Purpose: JIRA ID: SMF-276. Helps to show list of data used for calculation of EMOSI value
     * Created:
     * Modified:
     * Modified by:
     */
    public function index(Request $request){
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // if data is posted then showing EMOSI/ MEDIAN BEER/ MEDIAN DEVIATION list in datatable or exporting them as per input parameters
            $output_arr = array();              // keeping this final output array as EMPTY by default
            $flag_export_data = false;          // decides whether request came for exporting the data or not
            if($request->input('export_data') !== null && !empty($request->input('export_data')) && (intval($request->input('export_data')) == 1)){
                $flag_export_data = true;
            }
            else{
                // when showing data in tabular format, keeping some data as default for an array output_arr
                $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            }

            $tab_type = ($request->input('tab_type')??'data_table_median_beer');
            // Read value from Model method
            switch($tab_type){
                case 'data_table_median_deviation':
                    $retrieved_data = EmosiMovingAverage1750CalculationModel::getMedianDeviationRecords(array_merge($request->all(),
                                                                    array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                         'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                         'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                         'logged_in_user_id' => $this->logged_in_user_id
                                                                    )
                                                               )
                                                            );
                    break;
                case 'data_table_emosi':
                    $retrieved_data = EmosiHistoryModel::getEMOSIRecords(array_merge($request->all(),
                                                                    array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                         'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                         'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                         'logged_in_user_id' => $this->logged_in_user_id
                                                                    )
                                                               )
                                                            );
                    break;
                default:
                    $retrieved_data = EmosiBeerCalculationModel::getMedianBeerRecords(array_merge($request->all(),
                                                                    array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                         'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                         'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                         'logged_in_user_id' => $this->logged_in_user_id
                                                                    )
                                                               )
                                                            );
            }

            if(!$retrieved_data['records']->isEmpty()){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $retrieved_data['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $retrieved_data['records'];
                    echo json_encode($output_arr);
                }
                else{
                    // when exporting data, preparing HEADER ROW for an informative purpose
                    $headings_to_be_considered = array();
                    switch($tab_type){
                        case 'data_table_median_deviation':
                            $headings_to_be_considered = $this->data_table_median_deviation_headings;
                            break;
                        case 'data_table_emosi':
                            $headings_to_be_considered = $this->data_table_emosi_headings;
                            break;
                        default:
                            $headings_to_be_considered = $this->data_table_median_beer_headings;
                    }


                    $csv_headers = array_column($headings_to_be_considered, 'label');
                    // removing HTML BR tag from CSV label headings
                    array_walk($csv_headers, function(&$_value){
                        $_value = str_ireplace("<BR>", "\n", $_value);
                    });

                    // array_shift($csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    $output_arr[] = $csv_headers;
                    foreach($retrieved_data['records'] as $key => $value){
                        $row = array();
                        foreach($headings_to_be_considered as $field_name_key => $field_name_value){
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
                    unset($key, $value, $csv_headers, $headings_to_be_considered);
                    // makes data available in a CSV file format to user
                    return \Excel::download(new ArrayRecordsExport($output_arr), str_replace('data_table_', '', $tab_type) .'_data_'. date('Ymd').'.xlsx');
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
            // as no data is posted then loading the view
            $data = array('data_table_median_beer_headings' => $this->data_table_median_beer_headings,
                          'data_table_median_deviation_headings' => $this->data_table_median_deviation_headings,
                          'data_table_emosi_headings' => $this->data_table_emosi_headings,
                          'data_table_kfin_emosi_headings' => $this->data_table_kfin_emosi_headings);

            // Pass to view
            return view('emosi/list')->with($data);
        }
    }
    public function GetSTPEmosiSave($record_date='',$emosi_value=''){
        $err_flag = 0;
		$output_arr = array('response' => '');
        $err_msg = array();
        if(empty($record_date) || strtotime($record_date) === FALSE){
            $err_flag = 1;
            $err_msg[] = "Record date is required!";
        }

        if(!isset($emosi_value) || empty($emosi_value)){
            $err_flag = 1;
            $err_msg[] = "EMOSI Value  is required!";
        }

        if($err_flag == 0){
			try{
                $convert_date = date('m-d-Y', strtotime($record_date));
				$api_endpoint_url = $this->DOT_NET_API_BASE_URL_WEB_API.'/api/MyTransactions/GetSTPEmosiSave?record_date='.$convert_date.'&emosi_value='.$emosi_value;
				$api_response = get_content_by_curl($api_endpoint_url, array());
				$api_response = $this->process_response($api_response);
				if($api_response['err_flag'] == 1){
					$err_flag = 1;
					$err_msg = array_merge($err_msg, $api_response['err_msg']);
					$output_arr['api_response'] = $api_response;
				}
				elseif(isset($api_response['response'])){
					$output_arr['response'] = $api_response['response'];
				}
				else{
					$err_flag = 1;
					$err_msg[] = 'API response not received';
				}
				unset($api_endpoint_url, $api_response);
			}
			catch(Exception $e){
				$err_flag = 1;
				$err_msg[] = 'Exception: '. $e->getMessage();
			}
		}
        
        $output_arr['err_flag'] = $err_flag;
		$output_arr['err_msg'] = $err_msg;
		return $output_arr;
    }

    /* Function to read and decrypt the response retrieved from an API request
	 */
	public function process_response($api_response){
		$output_arr = array('response' => '');
		$err_flag = 0;					// err_flag is 0 means no error
		$err_msg = array();				// err_msg stores list of errors found during execution

		if(!isset($api_response) || empty($api_response)){
			// coming here when response is not received from an API
			$err_flag = 1;
			$err_msg[] = 'API response not received';
		}

		if($err_flag == 0){
			if(isJson($api_response)){
				$api_response = json_decode($api_response, true);
				if(isset($api_response['errors'])){
					$err_flag = 1;
					if(isset($api_response['title']) && !empty($api_response['title'])){
						$err_msg = array_merge($err_msg, array($api_response['title']));
					}

					foreach ($api_response['errors'] as $error_key => $error_text) {
						if(!is_array($error_text) && !empty($error_text)){
							$err_msg = array_merge($err_msg, array($error_key .': '. $error_text));
						}
						elseif(is_array($error_text) && count($error_text) > 0){
							foreach($error_text as $error){
								$err_msg = array_merge($err_msg, array($error_key .': '. $error));
							}
							unset($error);
						}
					}
					unset($error_key, $error_text);
					// $err_msg = array_merge($err_msg, array($api_response['errors']));
				}
				else{
					$output_arr['response'] = $api_response;
				}
			}
			else{
				$output_arr['response'] = $api_response;
			}
		}

		$output_arr['err_flag'] = $err_flag;
		$output_arr['err_msg'] = $err_msg;
		return $output_arr;
	}



    public function create(Request $request){
        if($request->ajax() && count($request->all()) > 0){
            extract($request->all());
            $reports = EmosiModel::allSelectedValues($record_date);
                            
            if(isset($reports) && !empty($reports['select_bond_value']) && !empty($reports['select_nifty_fifty'])&& !empty($reports['select_nifty_fifty_day'])){
                echo json_encode(array('status' => 'error','msg' => 'data alredy exist can not edit'));
            }
            else{
                 if(isset($select_Bond)){ 
                    
                    $data['close'] = trim($select_Bond);
                }

                if(isset($nifty_fifty)){
                    $data['pe'] = trim($nifty_fifty);
                }

                if(isset($nifty_fifty_day))
                {
                   $data['nifty_fifty_day'] = trim($nifty_fifty_day);
                }
                    $data['status'] ='1';
                    $data['record_date'] = trim($record_date);
                    $data['index_date'] = trim($record_date);
                   
                    $report = EmosiModel::getEmosiAllValues($data);
                    // x($report);
                    if(!empty($report) ){
                        
                         // calculating Median Beer values
                        $retrieved_data = \App\Models\EmosiBeerCalculationModel::calculate_median_beer(array(
                                                                                'bond_symbol' => 'india_10y',
                                                                                'index_symbol' => 'nifty_50',
                                                                                'from_date' => $record_date,
                                                                                'to_date' => $record_date,
                                                                                'calculate_for_all_dates' => 0,
                                                                                'enable_query_log' => 0
                                                                                )
                                                                            );
                         
                         // calculating moving average
                        $retrieved_data = \App\Models\EmosiMovingAverage1750CalculationModel::calculate_median_deviation_from_ma1750(array(
                                            'index_symbol' => 'nifty_50',
                                            'from_date' => $record_date,
                                            'to_date' => $record_date,
                                            'calculate_for_all_dates' => 0,
                                            'enable_query_log' => 0
                                                                                )
                                                                            );
                         // calculating emosi history
                        $retrieved_data = \App\Models\EmosiHistoryModel::emosi_history_insert_records(array(
                                                        'bond_symbol' => 'india_10y',
                                                        'index_symbol' => 'nifty_50',
                                                        'from_date' => $record_date,
                                                        'to_date' => $record_date,
                                                        'calculate_for_all_dates' => 0,
                                                        'enable_query_log' => 0
                                                        )
                                                     );
                    
                        echo json_encode(array('status' => 'success','msg' => 'data added successfully'));
                   }

                    else{
                        echo json_encode(array('status' => 'error','msg' => ' data not found'));
                    }

                }         
            
            }
            
        }
             

        public function Ajax_create(Request $request){
            if($request->ajax() && count($request->all()) > 0){
                extract($request->all());
                $day=date('D',strtotime($record_date));
                $days=array('Sat','Sun');
                if(in_array($day, $days) || strtotime($record_date)===false){ 
                    echo json_encode(array('status' => 'error','msg' => 'not allowed sat and sun'));
                }
                else{
                    $reports = EmosiModel::allSelectedValues($record_date);
                    echo json_encode($reports);               
                }     

            }

        }



    public function getKfinEmosiValue($start_date='',$end_date=''){
        $err_flag = 0;
		$output_arr = array('response' => '');
        $err_msg = array();
        if(empty($start_date) || strtotime($start_date) === FALSE){
            $err_flag = 1;
            $err_msg[] = "Start date is required!";
        }
        if(empty($end_date) || strtotime($end_date) === FALSE){
            $err_flag = 1;
            $err_msg[] = "End date is required!";
        }

        if($err_flag == 0){
			try{
                $start_date = date('m/d/Y', strtotime($start_date));
                $end_date = date('m/d/Y', strtotime($end_date));
				$api_endpoint_url = $this->DOT_NET_API_BASE_URL_WEB_API.'/api/MyTransactions/GetSTPEmosi?&From_date='.$start_date.'&To_date='.$end_date;
				$api_response = get_content_by_curl($api_endpoint_url, array());
				$api_response = $this->process_response($api_response);
				if($api_response['err_flag'] == 1){
					$err_flag = 1;
					$err_msg = array_merge($err_msg, $api_response['err_msg']);
					$output_arr['api_response'] = $api_response;
				}
				elseif(isset($api_response['response'])){
					$output_arr['response'] = $api_response['response'];
				}
				else{
					$err_flag = 1;
					$err_msg[] = 'API response not received';
				}
				unset($api_endpoint_url, $api_response);
			}
			catch(Exception $e){
				$err_flag = 1;
				$err_msg[] = 'Exception: '. $e->getMessage();
			}
		}

        $output_arr['err_flag'] = $err_flag;
		$output_arr['err_msg'] = $err_msg;
		return $output_arr;
    }

    public function GetKfinEmosiValueDetails(Request $request)
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


            $partnersData =self::getKfinEmosiValue($request['start_date'],$request['end_date']);

            if(!empty($partnersData['response'])){
                if(!$flag_export_data){
                    // showing data in JSON format
                    $count_partners_data = count($partnersData['response']);
                    $output_arr['recordsTotal'] = $count_partners_data;
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $partnersData['response'];
                    echo json_encode($output_arr);
                }
                else{
                    // when exporting data, preparing HEADER ROW for an informative purpose
                    $csv_headers = array_column($this->data_table_kfin_emosi_headings, 'label');
                    // array_shift($csv_headers);      // removes the 1st element from an array, here 1st element is an "ACTION" field, which is not necessary while exporting data
                    $output_arr[] = $csv_headers;
                    foreach($partnersData['response'] as $key => $value){
                        $row = array();
                        foreach($this->data_table_kfin_emosi_headings as $field_name_key => $field_name_value){
                            $row[$field_name_key] = '';
                            if(isset($value[$field_name_key])){
                                if($field_name_key == 'record_date'){
                                    $row[$field_name_key] = date('d-m-Y',strtotime($value[$field_name_key]));
                                }
                                elseif($field_name_key == 'entdt'){
                                    $row[$field_name_key] = date('d-m-Y H:i:s',strtotime($value[$field_name_key]));
                                }
                                else{
                                    $row[$field_name_key] = $value[$field_name_key];
                                }
                            }
                        }
                        $output_arr[] = $row;
                        unset($field_name_key, $field_name_value);
                    }
                    unset($key, $value, $csv_headers);
                    // makes data available in a CSV file format to user
                    return \Excel::download(new ArrayRecordsExport($output_arr),'kfin_emosi_data_'. date('Ymd').'.xlsx');
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

}
