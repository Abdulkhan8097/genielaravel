<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ArrayRecordsExport;
use App\Models\AumdataModel;

class Aumdata extends Controller
{
    protected $data_table_headings;
    public function __construct(){
        $this->middleware('auth');
        $this->data_table_headings = array('ARN' => array('label' => 'ARN'),
                                           'arn_avg_aum' => array('label' => 'ARN Average AUM - Last Reported'),
                                           'arn_total_commission' => array('label' => 'ARN Total Commission - Last Reported'),
                                           'arn_yield' => array('label' => 'ARN Yield'),
                                           'arn_business_focus_type' => array('label' => 'ARN Business Focus Type'),
                                           'aum_year' => array('label' => 'Year'),
                                           //'created_at' => array('label' => 'Record Created Date')
                                        );
    }

    /**
     * Author: Prasad Wargad
     * Purpose: JIRA ID: SMF-37. Helps to show list of available ARN wise AUM data
     * Created:
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
            $partnersData = AumdataModel::getARNAumList($request->all());
            // exporting data using shell script
            if($flag_export_data){
                $where_conditions = '';
                $order_by_clause = '';
                if(isset($partnersData['where_conditions']) && is_array($partnersData['where_conditions']) && count($partnersData['where_conditions']) > 0){
                    foreach($partnersData['where_conditions'] as $condition){
                        $where_conditions .= $condition[0] ." ". $condition[1] ." '". addslashes($condition[2]) ."' AND ";
                    }
                    unset($condition);
                    $where_conditions = trim($where_conditions);
                    if(substr($where_conditions, -3) == 'AND'){
                        $where_conditions = substr($where_conditions, 0, -3);
                    }
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

                exec('sh '. get_server_document_root(true) . '/vendor/shell_scripts/export_csv_data.sh -i"drm_uploaded_arn_average_aum_total_commission_data" "'. $where_conditions .'" "'. $order_by_clause .'"', $return_var, $exit_code);
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
            $data = array('data_table_headings' => $this->data_table_headings);
            unset($distributor_category_records);

            // Pass to view
            return view('aumdata/list')->with($data);
        }
    }
}
