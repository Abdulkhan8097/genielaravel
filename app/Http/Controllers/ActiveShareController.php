<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\ActiveShareModel;
use App\Exports\ArrayRecordsExport;

class ActiveShareController extends Controller
{
    public function index(){
        return view("active-share-contribution")->with('data',array());
    }

    public function get_scheme_list(Request $request){
        $err_flag = 0;              // err_flag is 0 means no error
        $err_msg = array();         // err_msg stores list of errors found during execution
        $output_arr = array('data' => array());
        
        // x($request->all());
        $scheme_master_list = ActiveShareModel::get_scheme_list($request->all());
        // x($scheme_master_list);
    }

    public function get_active_share(Request $request){
        // x($request->all());
        $scheme_master_list = ActiveShareModel::getActiveShare($request->all());
        if(empty($scheme_master_list)){
            $response = '<div class="alert alert-danger mt-2">
                        Records not found
                        </div>';
            return $response;
        }
        // x($scheme_master_list,"scheme_master_list");
        $data_array = array();
        foreach($scheme_master_list as $m_key => $m_val){
            $data_array[$m_val->IndexCode][] = $m_val;
        }
        // return response()->json($data_array, 200);
        $data_array = json_decode(json_encode($data_array), true);
        // x($data_array,"data_array");
        $index_array = array_keys($data_array);
        // y($index_array);
        // x($data_array);
        $tab_html ='';
        $tab_html .= '<ul class="nav nav-tabs new-tab mt-0" id="myTab" role="tablist">';
                foreach($index_array   as $key_index_array =>   $val){
                    if($key_index_array == 0)
                        {
                            $tab_html .='<li class="nav-item active">
                                    <a class="nav-link" href="#'.$data_array[$val][0]['IndexName'].'" >'.$data_array[$val][0]['IndexName'].'</a>
                                </li>';
                        }
                        else{
                            $tab_html .='<li class="nav-item">
                                    <a class="nav-link" href="#'.$data_array[$val][0]['IndexName'].'" >'.$data_array[$val][0]['IndexName'].'</a>
                                </li>';
                        }
                    
                };
                        
               $tab_html .='</ul>
                    <div class="tab-content  data-tabs">';
                    
                    foreach($index_array  as $key_index_array =>  $val){
                        $sr_no = 1;
                        if($key_index_array == 0)
                        {
                            $tab_html .='<div class="tab-pane show active tab-list" id="'.$data_array[$val][0]['IndexName'].'">';
                        }
                        else{
                            $tab_html .='<div class="tab-pane tab-list" id="'.$data_array[$val][0]['IndexName'].'">';
                        }
                        
                        $val_array = $data_array[$val];
                        //y($val_array);
                        $sum_active_share_contribution = array_sum(array_column($val_array, 'active_share_contribution'));
                        if($sum_active_share_contribution < 60 ){
                            $font_class = 'red-font';
                        }else{
                            $font_class = 'green-font';
                        }
                        $tab_html .= " <h2>Active Share - <span class='".$font_class."'>".round($sum_active_share_contribution,2)."</span></h2>";
                        $tab_html .= '<div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="border-bottom display-flex">
                                                <h2 class="">Active Share List </h2>
                                                <div class="form-group ml-auto calendar-width">
                                                    <div class="input-group-append">
                                                        <button type="button" indexcode="'.$val.'" id="csv_dwn_'.$val.'" schemecode="'.$val.'" class="btn btn-primary csv_download">Download XLSX</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mt-2">
                                            <table id="example" class="display" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Schemecode</th>
                                                        <th>Index Code</th>
                                                        <th>Index Name</th>
                                                        <th>ISIN</th>
                                                        <th>Symbol</th>
                                                        <th>Company</th>
                                                        <th>Holding Percentage</th>
                                                        <th>Index Weightage</th>
                                                        <th>Abs Diff</th>
                                                        <th>Active Share Contribution</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                ';
                        foreach($val_array as $key => $value){
                            
                            $tab_html .='<tr>
                                            <td>'.($value['Schemecode']??($value['schemecode']??'')).'</td>
                                            <td>'.$value['IndexCode'].'</td>
                                            <td>'.$value['IndexName'].'</td>
                                            <td>'.$value['isin'].'</td>
                                            <td>'.$value['symbol'].'</td>
                                            <td>'.$value['COMPNAME'].'</td>
                                            <td>'.round($value['Holdpercentage'],2).'</td>
                                            <td>'.round($value['index_weightage'],2).'</td>
                                            <td>'.round($value['abs_diff'],2).'</td>
                                            <td>'.round($value['active_share_contribution'],2).'</td>
                                        </tr>';
                                        $sr_no++;
                        }
                        $tab_html .='</tbody>
                                        </table>
                                        </div>
                                    </div>
                                </div>';
                                // closing table 
                        $tab_html .= '</div>';
                    }
                        
                       
                    $tab_html .='</div>';
        // x($tab_html);
        return $tab_html;
    }

    public function get_active_share_csv(Request $request){
        // y($request->all());
        extract($request->all());
        // x($shemecode);
        if($export_data == 1){
            

            $exportData[1] = array('Scheme Name', $schemename, '', '', '', '', '', '', '', '');
            $exportData[2] = array( '', '', '', '', '', '', '', '', '', '');
            $csv_headers = array('Schemecode','Index Code','Index Name','ISIN','Symbol','Company','Holding Percentage','Index Weightage','Abs Diff','Active Share Contribution');
            $exportData[] = $csv_headers;
            $scheme_master_list = ActiveShareModel::getActiveShare($request->all());
            foreach($scheme_master_list as $m_key => $m_val){
                $data_array[$m_val->IndexCode][] = $m_val;
            }
            $data_array = json_decode(json_encode($data_array), true);
            // x($data_array);
            $sr_no = 4;
            if(!empty($data_array))
            {
                foreach($data_array[$indexcode] as $key => $value)
                {
                    $exportData[$sr_no]['Schemecode'] = ($value['Schemecode']??($value['schemecode']??''));
                    $exportData[$sr_no]['IndexCode'] = $value['IndexCode'];
                    $exportData[$sr_no]['IndexName'] = $value['IndexName'];
                    $exportData[$sr_no]['isin'] = $value['isin'];
                    $exportData[$sr_no]['symbol'] = $value['symbol'];
                    $exportData[$sr_no]['COMPNAME'] = $value['COMPNAME'];
                    $exportData[$sr_no]['Holdpercentage'] = $value['Holdpercentage'];
                    $exportData[$sr_no]['index_weightage'] = $value['index_weightage'];
                    $exportData[$sr_no]['abs_diff'] = $value['abs_diff'];
                    $exportData[$sr_no]['active_share_contribution'] = $value['active_share_contribution'];

                    $sr_no++;
                }
                $exportData[$sr_no++] = array('', '', '', '', '', '', '', '', '', '');
                $exportData[$sr_no++] = array('', '', '', '', '', '', '', '', 'Total', array_sum(array_column($data_array[$indexcode], 'active_share_contribution')) );
                    // makes data available in a CSV file format to user
                    return \Excel::download(new ArrayRecordsExport($exportData), 'active_share_contribution_'.$indexcode.'_'. date('Ymd') .'.xlsx');
            }
            else{
                ?><script>alert('No records found');window.close();</script><?php
            }
        }
    }
}
