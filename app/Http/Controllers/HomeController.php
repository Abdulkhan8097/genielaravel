<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expression;
use App\Models\DistributorsModel;
use App\Models\MeetinglogModel;
use App\Models\BDM_Meeting_Dashboard_model;
use App\Models\UsermasterModel;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $logged_in_user_roles_and_permissions, $flag_have_all_permissions, $flag_show_all_arn_data, $logged_in_user_id;
    public function __construct()
    {
        $this->middleware('auth');

        // retrieving logged in user role and permission details
        $this->middleware(function ($request, $next) {
            // Checking whether logged in user id details available in the session or not.
            if(!session()->has('logged_in_user_id')){
                // If not then adding those details in session
                // getting logged in user id and storing it in session
                $this->logged_in_user_id = intval(Auth::user()->id)??0;
                // retrieving the permission for a logged in user and storing it in session
                $this->logged_in_user_roles_and_permissions=\App\Models\UsermasterModel::get_specific_user_role_and_permissions($this->logged_in_user_id);
                $request->session()->regenerate();
                session(array('logged_in_user_id' => $this->logged_in_user_id,
                              'logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions));
            }
            else{
                // If details available in session then retrieve them
                $this->logged_in_user_roles_and_permissions = session('logged_in_user_roles_and_permissions');
                $this->logged_in_user_id = session('logged_in_user_id');
            }

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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // checking whether any data is posted or not to this function
        if(count($request->all()) > 0){
            // if data is posted then showing records list in datatable or in any other requested format
            extract($request->all());                // Import variables into the current symbol table from an array
            $err_flag = 0;                          // err_flag is 0 means no error
            $err_msg = array();                     // err_msg stores list of errors found during execution
            $output_arr = array();                  // keeping this final output array as EMPTY by default

            if(!isset($action) || empty($action)){
                $err_flag = 1;
                $err_msg[] = 'Requested action could not be performed';
            }

            if($err_flag == 0){
                switch($action){
                    case 'load_arn_empanelment_count_details_statewise':
                        if($this->flag_show_all_arn_data){
                            $output_arr = array('recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());

                            // coming here for user whose role have flag to show all ARN data as TRUE
                            // retrieving list of empanelled and not empanelled ARN data based on states
                            $retrieved_data = DistributorsModel::getARNbyStateWise(array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                                'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                                'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                                'logged_in_user_id' => $this->logged_in_user_id
                                                            )
                                                        );
                        if(!$retrieved_data->isEmpty()){
                            $retrieved_data = $retrieved_data->toArray();
                            if(count($retrieved_data) > 0){
                                array_walk($retrieved_data, function($_value){
                                    unset($_value->score_of_date);
                                    $_value->not_empanelled = $_value->not_empanelled .' ('. round((!empty($_value->total)?(($_value->not_empanelled / $_value->total) * 100):0.00), 2) .'%)';
                                    $_value->empanelled = $_value->empanelled .' ('. round((!empty($_value->total)?(($_value->empanelled / $_value->total) * 100):0.00), 2) .'%)';
                                });
                            }
                            // showing data in JSON format
                            $output_arr['recordsTotal'] = count($retrieved_data);
                            $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                            $output_arr['data'] = $retrieved_data;
                        }
                            unset($retrieved_data);
                        }
                        break;
						case 'load_arn_empanelment_count_details_userwise':
							$output_arr = array('recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
	
							// retrieving list of empanelled and not empanelled ARN data based on users
							$retrieved_data = DistributorsModel::getARNbyStateWise(array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
																'flag_have_all_permissions' => $this->flag_have_all_permissions,
																'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
																'logged_in_user_id' => $this->logged_in_user_id,
																'show_users_data' => true
															)
														);
							if(!$retrieved_data->isEmpty()){
								$retrieved_data = $retrieved_data->toArray();
								if(count($retrieved_data) > 0){
									array_walk($retrieved_data, function($_value){
										unset($_value->score_of_date);
										$_value->not_empanelled = $_value->not_empanelled .' ('. round((!empty($_value->total)?(($_value->not_empanelled / $_value->total) * 100):0.00), 2) .'%)';
										$_value->empanelled = $_value->empanelled .' ('. round((!empty($_value->total)?(($_value->empanelled / $_value->total) * 100):0.00), 2) .'%)';
									});
								}
								// showing data in JSON format
								$output_arr['recordsTotal'] = count($retrieved_data);
								$output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
								$output_arr['data'] = $retrieved_data;
							}
							unset($retrieved_data);
							break;
							case 'load_goal_userwise':
								$output_arr = array('recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array());
		
								// retrieving list of empanelled and not empanelled ARN data based on users
								$retrieved_data = MeetinglogModel::getMeetingGoalList(array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
																	'flag_have_all_permissions' => $this->flag_have_all_permissions,
																	'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
																	'logged_in_user_id' => $this->logged_in_user_id,
																	'show_users_data' => true,
																	'period' => $period,
																	'select_user' => $select_user
																)
															);
								
								$records = [];
								//print_r($retrieved_data);
								foreach($retrieved_data['records'] as $v){
									$v = (object)$v;
									$key = $v->id;
									if(!empty($select_user)){
										$key = $v->date;
									}
									$records[$key]['user_name'] = $v->name;
									if(!isset($records[$key]['achieved_calls'])){
										$records[$key]['achieved_calls'] = 0;
										$records[$key]['achieved_meetings'] = 0;
										$records[$key]['target_calls'] = $v->target_calls;
										$records[$key]['target_meetings'] = $v->target_meetings;
										$records[$key]['date'] = $v->date;
									}
									if (preg_match("/Call/is", $v->meeting_mode)){
										$records[$key]['achieved_calls'] += $v->count;
									}elseif(preg_match("/Meeting/is", $v->meeting_mode)){
										$records[$key]['achieved_meetings'] += $v->count;
									}
									$records[$key]['achieved_percentage'] = round(((($records[$key]['achieved_calls']+$records[$key]['achieved_meetings'])*100)/($records[$key]['target_calls']+$records[$key]['target_meetings'])),2).'%';
								}

								if(!empty($retrieved_data)){
									//$retrieved_data = (array)$retrieved_data;
									//print_r($records);
									// showing data in JSON format
									$output_arr['recordsTotal'] = $retrieved_data['no_of_records'];
									$output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
									$output_arr['data'] = array_values($records);
								}
								unset($retrieved_data);
								break;
                    case 'load_arn_relationship_quality_score':
                        switch($data_shown_format){
                            case 'calendar':
                                $output_arr = array();
                                $retrieved_data = DistributorsModel::getARNQualityScore(
                                                    array_merge($request->all(),
                                                    array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                        'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                        'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                        'logged_in_user_id' => $this->logged_in_user_id)
                                                    )
                                                );
                                if(!$retrieved_data->isEmpty()){
                                    // retrieves the data sorted in ascending order of field "score_of_date" and descending order of field "calculated_score"
                                    $retrieved_data = $retrieved_data->toArray();
                                    if(count($retrieved_data) > 0){
                                        // calculating the achieved percentage score
                                        array_walk($retrieved_data, function($_value, $_key, $_user_data){
                                            // calculating looping day score
                                            $_value->start = strtotime($_value->score_of_date) * 1000;
                                            $_value->end = $_value->start;
                                            $_value->color = "#008000";
                                            $_value->textMsg = 'Sample';
                                            $_value->achieved_percentage = (!empty(intval($_value->maximum_score))?((intval($_value->calculated_score) / intval($_value->maximum_score)) * 100):0);
                                            $_value->achieved_percentage = round($_value->achieved_percentage, 2);
                                            $_value->title = $_value->achieved_percentage .'%';
                                            // checking 1 day previous score_of_date record available or not
                                            if($_key > 0 && isset($_user_data[0][($_key - 1)]) && isset($_user_data[0][($_key - 1)]->achieved_percentage)){
                                                $previous_record_title = $_user_data[0][($_key - 1)]->achieved_percentage;
                                                $percentage_diff = $_value->achieved_percentage - $previous_record_title;
                                                $_value->title .= ' ('. ((intval($percentage_diff) > 0)?'+':'') . $percentage_diff .'%)';
                                                if(floatval($percentage_diff) < 0){
                                                    $_value->color = "#ff0000";
                                                }
                                                elseif(empty(floatval($percentage_diff))){
                                                    $_value->color = "#808080";
                                                }
                                                unset($previous_record_title, $percentage_diff);
                                            }
                                        }, [$retrieved_data]);
                                    }
                                    $output_arr = $retrieved_data;
                                }
                                unset($retrieved_data);
                                break;
                            default:
                                $output_arr = array('recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array(), 'max_score_of_date' => '');

                                $retrieved_data = DistributorsModel::getARNQualityScore(
                                                    array_merge($request->all(),
                                                    array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions,
                                                        'flag_have_all_permissions' => $this->flag_have_all_permissions,
                                                        'flag_show_all_arn_data' => $this->flag_show_all_arn_data,
                                                        'logged_in_user_id' => $this->logged_in_user_id)
                                                    )
                                                );
                                if(!$retrieved_data->isEmpty()){
                                    $retrieved_data = $retrieved_data->toArray();
                                    if(count($retrieved_data) > 0){
                                        $output_arr['max_score_of_date'] = date('d M Y', strtotime($retrieved_data[0]->score_of_date))??'';
                                        array_walk($retrieved_data, function($_value){
                                            unset($_value->score_of_date);
                                            $_value->achieved_percentage = (!empty($_value->maximum_score)?(($_value->calculated_score / $_value->maximum_score) * 100):0.00);
                                            $_value->achieved_percentage = round($_value->achieved_percentage, 2);
                                        });
                                    }
                                    // showing data in JSON format
                                    $output_arr['recordsTotal'] = count($retrieved_data);
                                    $output_arr['recordsFiltered'] = $output_arr['recordsTotal'];
                                    $output_arr['data'] = $retrieved_data;
                                }
                                unset($retrieved_data);
                        }
                        break;
                }
            }

            if($err_flag == 1){
                $output_arr['err_flag'] = $err_flag;
                $output_arr['err_msg'] = $err_msg;
            }
            return response()->json($output_arr);
        }
        else{
            // loading page first time
            $data['logged_in_user_roles_and_permissions'] = $this->logged_in_user_roles_and_permissions;
            $data['flag_have_all_permissions'] = $this->flag_have_all_permissions;
            $data['flag_show_all_arn_data'] = $this->flag_show_all_arn_data;
            $data['logged_in_user_id'] = $this->logged_in_user_id;
            $data['supervised_users_list'] = array();
            if(!$this->flag_show_all_arn_data){
                $retrieved_data = \App\Models\UsermasterModel::getSupervisedUsersList(array('logged_in_user_roles_and_permissions' => $this->logged_in_user_roles_and_permissions, 'flag_have_all_permissions' => $this->flag_have_all_permissions, 'flag_show_all_arn_data' => $this->flag_show_all_arn_data, 'logged_in_user_id' => $this->logged_in_user_id));
                if(isset($retrieved_data['show_data_for_users']) && is_array($retrieved_data['show_data_for_users']) && count($retrieved_data['show_data_for_users']) > 0){
                    $data['supervised_users_list'] = $retrieved_data['show_data_for_users'];
                }
                unset($retrieved_data);
            }

            // retrieving details of specific users
            $retrieved_users_list = \App\Models\UsermasterModel::get_user_list(array('user_id' => $data['supervised_users_list']));
            if(is_array($retrieved_users_list) && count($retrieved_users_list) > 0){
                $data['supervised_users_list'] = $retrieved_users_list;
            }
            unset($retrieved_users_list);

			$data['bdmlist'] = $this->get_bdm_list();

            return view('home')->with('data', $data);
        }
    }

	public function get_bdm_list()
    {

     $all_permition_data=session('logged_in_user_roles_and_permissions')['role_details']['show_all_arn_data'];

     $list_user=array();

     if($all_permition_data!=1)
     {
        $input_arr = array('logged_in_user_id' =>session('logged_in_user_id'));
     
        $list_user=UsermasterModel::getSupervisedUsersList($input_arr);
     }
      
      $user_list_array=array();

      if(!empty($list_user)&&!empty($list_user['show_data_for_users'])){
        $user_list_array=$list_user['show_data_for_users'];
      }
      
      //x($user_list_array);
      $bdmlist = BDM_Meeting_Dashboard_model::getbdmlist($user_list_array);

     return $bdmlist;
    }
}
