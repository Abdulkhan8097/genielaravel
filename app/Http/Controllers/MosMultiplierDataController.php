<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \App\Models\MosMultiplierDataModel;
use DB;

class MosMultiplierDataController extends Controller
{   
    protected $data_table_headings;
    protected $logged_in_user_roles_and_permissions, $flag_have_all_permissions, $flag_show_all_arn_data, $logged_in_user_id;
    public function __construct(){
        $this->middleware('auth');
        $this->data_table_headings = array('action' => array('label' => 'Action'),
                                           'multiplier_type' => array('label' => 'Multiplier Type'),
                                           'margin_of_safety' => array('label' => 'Margin Of Safety'),
                                           'multiplier_value' => array('label' => 'Multiplier Value'),
                                        );
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

    public function index(Request $request)
    {

          if(count($request->all())>0){
             extract($request->all());

            $output_arr = array(); // keeping this final output array as EMPTY by default
            $output_arr = array('draw' => $request->input('draw'), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
            
            $MostMultiplierData = MosMultiplierDataModel::getMostMultiplierDataDB(array_merge($request->all(),
                   array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                'logged_in_user_id' => $this->logged_in_user_id)
                    ));
            if(!$MostMultiplierData['records']->isEmpty()){
                
                    // showing data in JSON format
                    $output_arr['recordsTotal'] = $MostMultiplierData['no_of_records'];
                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                    $output_arr['data'] = $MostMultiplierData['records'];
                    
                // displaying data in DataTable format
                echo json_encode($output_arr);
            }else{
                // displaying data in DataTable format
                    echo json_encode($output_arr);
            }
         }
          else{

            $arr_multiplier_type = \App\Models\MosMultiplierData::get_distinct_multiplier_type();

            if(isset($arr_multiplier_type['multiplier_type']) && is_array($arr_multiplier_type['multiplier_type']) && count($arr_multiplier_type['multiplier_type']) > 0){

                // retrieved data is available in the key multiplier_type which we are using here
                $arr_multiplier_type = $arr_multiplier_type['multiplier_type'];
            }
            else{
                // setting this variable as empty array because required data not found
                $arr_multiplier_type = array();
            }
            $data = array('data_table_headings' => $this->data_table_headings, 'arr_multiplier_type' => $arr_multiplier_type);
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            $data['logged_in_user_id'] = $this->logged_in_user_id;
            return view('mosmultiplierdata/list')->with($data); 
         }  
    }

    public function create(Request $request){
        if(count($request->all()) > 0){
            extract($request->all());
             $request->validate([
                'select_multiplier' => 'required|regex:/^(?!\s)^^[a-zA-Z0-9 .]+$/',
                'select_margin' => 'required|regex:/^(?!\s)^^[0-9.]+$/',  
                'select_multipliervalue' => 'required|regex:/^(?!\s)^^[0-9.]+$/',
                 
            ],[
                'select_multiplier.required' => 'Enter a valid multiplier',
                'select_multiplier.regex'=>'Insert only numbers and characters',
                'select_margin.required' => 'Enter a valid margin',
                'select_margin.regex'=>'Insert only numbers',
                'select_multipliervalue.required' => 'Enter a valid multiplier value',
                'select_multipliervalue.regex'=> 'Insert only numbers',
                
            ]);
                $data['multiplier_type'] = TRIM($select_multiplier);
                $data['margin_of_safety'] = TRIM($select_margin);
                $data['multiplier_value'] = TRIM($select_multipliervalue);

                // DB::enableQueryLog();
                $select_records = DB::table('mos_multiplier_data')->where(array(array('margin_of_safety', '=',$data['margin_of_safety']),array('multiplier_type', '=',$data['multiplier_type']) ))->get();
                // x(DB::getQueryLog(), 'query_log');
                
                $select_records = json_decode(json_encode($select_records), true);
             
                if(isset($select_records) && is_array($select_records) && count($select_records) >=1){
                    $error_msg = "Margin of safety value already exist";
                    return redirect('mos_multiplier_data')->with('error',$error_msg);
                }
                else{
                    $employee_data=MosMultiplierDataModel::create($data);
                    return redirect('mos_multiplier_data')->with('success','MOS multiplier details added successfully.');
                }

        }
        return view('mosmultiplierdata/create');
    }

    public function edit(Request $request, $id='')
    {
         extract($request->all());
         $mos_data_select =MosMultiplierDataModel::find($id);
           
         if(count($request->all()) > 0){

            $request->validate([
                'select_multiplier' => 'required|regex:/^(?!\s)^^[a-zA-Z0-9 .]+$/',
                'select_margin' => 'required|regex:/^(?!\s)^^[0-9.]+$/',  
                'select_multipliervalue' => 'required|regex:/^(?!\s)^^[0-9.]+$/',
                 
            ],[
                'select_multiplier.required' => 'Enter a valid multiplier',
                'select_multiplier.regex'=>'Insert only numbers and characters',
                'select_margin.required' => 'Enter a valid margin',
                'select_margin.regex'=>'Insert only numbers',
                'select_multipliervalue.required' => 'Enter a valid multiplier value',
                'select_multipliervalue.regex'=> 'Insert only numbers',
                
            ]);
                $data['multiplier_type'] = TRIM($select_multiplier);
                $data['margin_of_safety'] = TRIM($select_margin);
                $data['multiplier_value'] = TRIM($select_multipliervalue);                

                // DB::enableQueryLog();
                 $record_count =DB::select("select * from `mos_multiplier_data` where `margin_of_safety` = '".$data['margin_of_safety']."' And `multiplier_type` ='".$data['multiplier_type']."' and `id` != ".$id.";");
                 // x(DB::getQueryLog(), 'query_log');
                 
                 if(isset($record_count) && is_array($record_count) && count($record_count) >=1 )
                {
                    $error_msg = "Margin of safety value already exist";
                    return redirect('mos_multiplier_data')->with('error',$error_msg );
                }else{

                    $mos_data_update=MosMultiplierDataModel::where('id',$id )->update($data);
                }

                return redirect('mos_multiplier_data')->with('success','MOS details edited data and added successfully.');
            }

                return view('mosmultiplierdata/edit',array('data'=>$mos_data_select));
        
                 
    }
    public function delete(Request $request)
    {
        extract($request->all());
        $deleted_data = MosMultiplierDataModel::where('id',$id )->delete();
             
        return response()->json(array('status'=> 'success','messages'=>'mos details deleted successfully.'));
         
    }
}


