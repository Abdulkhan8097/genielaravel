<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BDM_Meeting_Dashboard_model;
use App\Models\UsermasterModel;
use App\Exports\ArrayRecordsExport;
use App\Exports\ArrayRecordsWithMultipleSheetsExport;
class BDM_Meeting_Dashboard extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function index()
    {
        $data=array();
        $data['bdmlist']=$this->get_bdm_list();
       return view('meetinglog/bdm_meeting',$data);
    }
    public function get_bdm_list()
    {
      //$allSessions = session()->all();
        //x($allSessions);
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
    function get_view_detail_bdm(Request $request)
    {
        $input_data=$request->all();
        $empledetail=array();
        $daily_meeting=getSettingsTableValue('BDM_TARGET_MEETINGS');
        $month_meeting_detail=array();
        $sinceexp_meeting_detail=array();
        $ARNs_havent_met_till_list=array('tilltoal_arn_meatnot'=>0,'bdm_id'=>$input_data['bdmid']);

        $Count_of_non_empanelled_ARNs_havent_met_at_all_till_list=array('tilltoal_arn_notempnotmeat'=>0,'bdm_id'=>$input_data['bdmid']);

        $count_of_ARNs_not_met_since_last_90_days_till_list=array('total_count'=>0,'bdm_id'=>$input_data['bdmid']);
        $empanelled_and_non_Active_ARNS_not_met_at_all_till_list=array('tilltoal_arn_notempnotmeat'=>0,'bdm_id'=>$input_data['bdmid']);
        $mapped_partners_only_met_once_till_list=array('meetonectimecount'=>0,'bdm_id'=>$input_data['bdmid']);
        $total_month_meeting_attendenc=0;
        $met_count_month=array();
        $arn_detail = BDM_Meeting_Dashboard_model::get_total_arn_detail($input_data);
        //x($arn_detail);
        $total_meeting_month = BDM_Meeting_Dashboard_model::get_total_meetingmfd($input_data);
        /////ARNs haven’t met at all
        $ARNs_havent_met_till = BDM_Meeting_Dashboard_model::ARNs_havent_met_at_all($input_data);
        //Count of non empanelled ARNs haven’t met at all
        $Count_of_non_empanelled_ARNs_havent_met_at_all_till = BDM_Meeting_Dashboard_model::Count_of_non_empanelled_ARNs_havent_met_at_all($input_data);
        //Count of ARNs not met since last 90 days
        $count_of_ARNs_not_met_since_last_90_days_till= BDM_Meeting_Dashboard_model::count_of_ARNs_not_met_since_last_90_days($input_data);

        //Empanelled and non Active ARNS not met at all
        $empanelled_and_non_Active_ARNS_not_met_at_all_till= BDM_Meeting_Dashboard_model::empanelled_and_non_Active_ARNS_not_met_at_all($input_data);

        //Mapped partners only met once
        $mapped_partners_only_met_once_till= BDM_Meeting_Dashboard_model::mapped_partners_only_met_once($input_data);
        $total_meeting_month_sinceexp = BDM_Meeting_Dashboard_model::get_total_meetingmfd_sinceexp($input_data);
        if(!empty($arn_detail))
        {
        $empledetail=$arn_detail[0];
        $empledetail['non_empanelled_arn']=$empledetail['total_arn_mapped']-$empledetail['total_emplement'];
        $empledetail['non_active']=$empledetail['total_arn_mapped']-$empledetail['active_partner'];
        }
        if(!empty($total_meeting_month))
        {
        $month_meeting_detail=$total_meeting_month[0];
        $total_month_meeting_attendenc=!empty($month_meeting_detail['tota_month_arn_meeting'])?$month_meeting_detail['tota_month_arn_meeting']:0;
        }
        if(!empty($total_meeting_month_sinceexp))
        {
        $sinceexp_meeting_detail=$total_meeting_month_sinceexp[0];
        }
        if(!empty($month_meeting_detail))
        { //x($month_meeting_detail);
          $met_count_month=array('total_meeting_month'=>$month_meeting_detail['tota_unique_arn_meeting_month'],
               'total_meeting_month_emp'=>$month_meeting_detail['empanelled_arn'],
               'total_meeting_month_nonemp'=>$month_meeting_detail['nonempanelled_arn'],
               'total_meeting_month_active'=>$month_meeting_detail['month_met_active'],
               'total_meeting_month_nonactive'=>$month_meeting_detail['nonmonth_met_active'],
               'attendence_total_project_focus_unique'=>$month_meeting_detail['attendence_total_project_focus_unique'],
               'attendence_total_project_green_shoots_unique'=>$month_meeting_detail['attendence_total_project_green_shoots_unique'],
               'total_other_meetingcount'=>($month_meeting_detail['tota_month_arn_meeting']-($month_meeting_detail['attendence_total_project_focus']+$month_meeting_detail['attendence_total_project_green_shoots']-$month_meeting_detail['attendence_total_project_focus_green_both'])),
               'total_other_uniquearn'=>$month_meeting_detail['attendence_total_project_other_unqi'],
               'attendence_total_project_emerging_stars_unique'=>$month_meeting_detail['attendence_total_project_emerging_stars_unique']
           );
        }else{
           $met_count_month=array('total_meeting_month'=>0,
               'total_meeting_month_emp'=>0,
               'total_meeting_month_nonemp'=>0,
               'total_meeting_month_active'=>0,
               'total_meeting_month_nonactive'=>0,
               'attendence_total_project_focus_unique'=>0,
               'attendence_total_project_green_shoots_unique'=>0,
               'total_other_meetingcount'=>0,
               'total_other_uniquearn'=>0,
               'attendence_total_project_emerging_stars_unique'=>0);
        }
        //since exp
        if(!empty($sinceexp_meeting_detail))
        {
          $met_count_sicneexp=array('total_meeting_month'=>$sinceexp_meeting_detail['tota_unique_arn_meeting_month'],
               'total_meeting_month_emp'=>$sinceexp_meeting_detail['empanelled_arn'],
               'total_meeting_month_nonemp'=>$sinceexp_meeting_detail['nonempanelled_arn'],
               'total_meeting_month_active'=>$sinceexp_meeting_detail['month_met_active'],
               'total_meeting_month_nonactive'=>$sinceexp_meeting_detail['nonmonth_met_active'],
               'attendence_total_project_focus_unique'=>$sinceexp_meeting_detail['attendence_total_project_focus_unique'],
               'attendence_total_project_green_shoots_unique'=>$sinceexp_meeting_detail['attendence_total_project_green_shoots_unique'],
               'total_other_meetingcount'=>($sinceexp_meeting_detail['tota_month_arn_meeting']-($sinceexp_meeting_detail['attendence_total_project_focus']+$sinceexp_meeting_detail['attendence_total_project_green_shoots']-$sinceexp_meeting_detail['attendence_total_project_focus_green_both'])),
               'total_other_uniquearn'=>$sinceexp_meeting_detail['attendence_total_project_other_unqi'],
               'attendence_total_project_emerging_stars_unique'=>$sinceexp_meeting_detail['attendence_total_project_emerging_stars_unique']
           );
        }else{
           $met_count_sicneexp=array('total_meeting_month'=>0,
               'total_meeting_month_emp'=>0,
               'total_meeting_month_nonemp'=>0,
               'total_meeting_month_active'=>0,
               'total_meeting_month_nonactive'=>0,
               'attendence_total_project_focus_unique'=>0,
               'attendence_total_project_green_shoots_unique'=>0,
               'total_other_meetingcount'=>0,
               'total_other_uniquearn'=>0,
               'attendence_total_project_emerging_stars_unique'=>0);
        }
        //end
        if(!empty($ARNs_havent_met_till))
        {
         $ARNs_havent_met_till_list=$ARNs_havent_met_till[0];
        }
        if(!empty($Count_of_non_empanelled_ARNs_havent_met_at_all_till))
        {
         $Count_of_non_empanelled_ARNs_havent_met_at_all_till_list=$Count_of_non_empanelled_ARNs_havent_met_at_all_till[0];
        }
        if(!empty($count_of_ARNs_not_met_since_last_90_days_till))
        {
         $count_of_ARNs_not_met_since_last_90_days_till_list=$count_of_ARNs_not_met_since_last_90_days_till[0];
        }
        if(!empty($empanelled_and_non_Active_ARNS_not_met_at_all_till))
        {
         $empanelled_and_non_Active_ARNS_not_met_at_all_till_list=$empanelled_and_non_Active_ARNS_not_met_at_all_till[0];
        }
        if(!empty($mapped_partners_only_met_once_till))
        {
         $mapped_partners_only_met_once_till_list=$mapped_partners_only_met_once_till[0];
        }
        $workingdetail=$this->calculate_from_to_date($input_data);
        //x($workingdetail);
        $total_month_meeting=$workingdetail['workingDays']*$daily_meeting;
        $total_val_attendence=0;
        $absent_till_month=$total_month_meeting-$total_month_meeting_attendenc;
        $total_val_attendence=$total_month_meeting-$total_month_meeting_attendenc;
        //x($absent_till_month);
        if($absent_till_month<0)
        {
            $absent_till_month=0;
        }
        
        $data['arn_detail']=$empledetail;
        $data['meeting_summery']=array('total_month_meeting'=>$total_month_meeting,
                                      'total_month_meeting_attendenc'=>$total_month_meeting_attendenc,
                                      'absent_till_month'=>$absent_till_month,
                                      'month'=>$workingdetail['months'],
                                      'total_val_attendence'=>$total_val_attendence);
        $data['ARNs_havent_met_till_list']=$ARNs_havent_met_till_list;

        $data['Count_of_non_empanelled_ARNs_havent_met_at_all_till_list']=$Count_of_non_empanelled_ARNs_havent_met_at_all_till_list;

        $data['count_of_ARNs_not_met_since_last_90_days_till_list']=$count_of_ARNs_not_met_since_last_90_days_till_list;

        $data['empanelled_and_non_Active_ARNS_not_met_at_all_till_list']=$empanelled_and_non_Active_ARNS_not_met_at_all_till_list;

        $data['mapped_partners_only_met_once_till_list']=$mapped_partners_only_met_once_till_list;

        $data['month_meeting_detail']=$met_count_month;
        $data['sinceexp_meeting_detail']=$met_count_sicneexp;
        $data['daily_meting_log']=$this->daily_meeting_log($input_data);
        //x($data);
        echo json_encode($data);
        
    }
    public function getWorkingDays($startDate,$endDate,$holidays)
    {
                // do strtotime calculations just once
                $endDate = strtotime($endDate);
                $startDate = strtotime($startDate);


                //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
                //We add one to inlude both dates in the interval.
                $days = ($endDate - $startDate) / 86400 + 1;

                $no_full_weeks = floor($days / 7);
                $no_remaining_days = fmod($days, 7);

                //It will return 1 if it's Monday,.. ,7 for Sunday
                $the_first_day_of_week = date("N", $startDate);
                $the_last_day_of_week = date("N", $endDate);

                //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
                //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
                if ($the_first_day_of_week <= $the_last_day_of_week) {
                    if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
                    /*if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;*/
                }
                else {
                    // (edit by Tokes to fix an edge case where the start day was a Sunday
                    // and the end day was NOT a Saturday)

                    // the day of the week for start is later than the day of the week for end
                    if ($the_first_day_of_week == 7) {
                        // if the start date is a Sunday, then we definitely subtract 1 day
                        $no_remaining_days--;

                        /*if ($the_last_day_of_week == 6) {
                            // if the end date is a Saturday, then we subtract another day
                            $no_remaining_days--;
                        }*/
                    }
                    else {
                        // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
                        // so we skip an entire weekend and subtract 2 days
                        $no_remaining_days -= 2;
                    }
                }

                //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
            //---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
               $workingDays = $no_full_weeks * 6;
                if ($no_remaining_days > 0 )
                {
                  $workingDays += $no_remaining_days;
                }

                //We subtract the holidays
                foreach($holidays as $holiday){
                    $time_stamp=strtotime($holiday);
                    //If the holiday doesn't fall in weekend
                    if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6)
                        $workingDays--;//&& date("N",$time_stamp) != 7
                }

                return $workingDays;
    }
    public function calculate_from_to_date($data)
    {
            $form_date=$data['month'].'-01 00:00:00';
            $month_of=date('m',strtotime($form_date));
            $year_of=date('Y',strtotime($form_date));
            $day=cal_days_in_month(CAL_GREGORIAN, $month_of, $year_of);
            $to_date=$data['month'].'-'.$day.' 23:59:59';
            $today_date=date('Y-m-d H:i:s');
            if($to_date>$today_date)
            {
            $to_date=$today_date;
            }
            //$holidays=array('2023-04-04','2023-04-07','2023-04-14','2023-04-22');
            //$form_date='2022-01-01';
            $mf_holidays= BDM_Meeting_Dashboard_model::get_holidays($form_date,$to_date);
			
			if(!is_array($mf_holidays)){
				$mf_holidays = []; 
			}

            $holidays=array_column($mf_holidays, 'date');

            $workingDays=$this->getWorkingDays($form_date,$to_date,$holidays);
            $number_of_week=$this->number_of_weekend_days($form_date,$to_date);
            $month_of_int = ltrim($month_of, "0"); 
            $months = array('','January','February','March','April','May','June','July ','August','September','October','November','December');
            return array('workingDays'=>floor($workingDays),'number_of_week'=>$number_of_week,'months'=>$months[$month_of_int]);//$workingDays;
    }
    function number_of_weekend_days($startDate, $endDate)
    {
        $weekendDays = 0;
        $startTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate);
        for ($i = $startTimestamp; $i <= $endTimestamp; $i = $i + (60 * 60 * 24)) {
        if (date("N", $i) > 6) $weekendDays = $weekendDays + 1;
        }
        return $weekendDays;
    }
   public function download_data($bdm_id='',$type='')
   {
      $exportData1=array();
        if($type==1)
        {
            $csv_headers = array('ARN', 'ARN Name', 'ARN Email', 'BDM Name', 'BDM Email');
            $exportData[] = $csv_headers;
            $ARNs_havent_met_till = BDM_Meeting_Dashboard_model::ARNs_havent_met_at_all_download($bdm_id);
            foreach($ARNs_havent_met_till as $key => $value)
                {
                    $exportData1[$key][] = $value['arn'];
                    $exportData1[$key][] = $value['arn_holders_name'];
                    $exportData1[$key][] = $value['arn_email'];
                    $exportData1[$key][] = $value['bdmname'];
                    $exportData1[$key][] = $value['bdmemail'];
                }
                return \Excel::download(new ArrayRecordsExport(array_merge($exportData,$exportData1)), 'ARNs_havent_met_at_all_'. date('Ymd') .'.xlsx');
        }
        elseif($type==2)
        {
            $csv_headers = array('ARN', 'ARN Name', 'ARN Email', 'BDM Name', 'BDM Email');
            $exportData[] = $csv_headers;
            $Count_of_non_empanelled_ARNs_havent_met_at_all_till_list = BDM_Meeting_Dashboard_model::Count_of_non_empanelled_ARNs_havent_met_at_all_download($bdm_id);
            foreach($Count_of_non_empanelled_ARNs_havent_met_at_all_till_list as $key => $value)
                {
                    $exportData1[$key][] = $value['arn'];
                    $exportData1[$key][] = $value['arn_holders_name'];
                    $exportData1[$key][] = $value['arn_email'];
                    $exportData1[$key][] = $value['bdmname'];
                    $exportData1[$key][] = $value['bdmemail'];
                }
                return \Excel::download(new ArrayRecordsExport(array_merge($exportData,$exportData1)), 'Count_of_non_empanelled_ARNs_havent_met_at_all_'. date('Ymd') .'.xlsx');
        }
        elseif($type==3)
        {
            $csv_headers = array('ARN', 'ARN Name', 'ARN Email','Last Meeting Date','Last Meeting days count','BDM Name', 'BDM Email');
            $exportData[] = $csv_headers;
            $count_of_ARNs_not_met_since_last_90_days_download_list = BDM_Meeting_Dashboard_model::count_of_ARNs_not_met_since_last_90_days_download($bdm_id);
            foreach($count_of_ARNs_not_met_since_last_90_days_download_list as $key => $value)
                {
                    $exportData1[$key][] = $value['arn'];
                    $exportData1[$key][] = $value['arn_holders_name'];
                    $exportData1[$key][] = $value['arn_email'];
                    $exportData1[$key][] = $value['start_datetime'];
                    $exportData1[$key][] = $value['notmeetdays'];
                    $exportData1[$key][] = $value['bdmname'];
                    $exportData1[$key][] = $value['bdmemail'];
                }
                return \Excel::download(new ArrayRecordsExport(array_merge($exportData,$exportData1)), 'Count_of_ARNs_not_met_since_last_90_days_'. date('Ymd') .'.xlsx');
        }
        elseif($type==4)
        {
            $csv_headers = array('ARN', 'ARN Name', 'ARN Email','BDM Name','BDM Email');
            $exportData[] = $csv_headers;
            $empanelled_and_non_Active_ARNS_not_met_at_all_download_list = BDM_Meeting_Dashboard_model::empanelled_and_non_Active_ARNS_not_met_at_all_download($bdm_id);
            foreach($empanelled_and_non_Active_ARNS_not_met_at_all_download_list as $key => $value)
                {
                    $exportData1[$key][] = $value['arn'];
                    $exportData1[$key][] = $value['arn_holders_name'];
                    $exportData1[$key][] = $value['arn_email'];
                    $exportData1[$key][] = $value['bdmname'];
                    $exportData1[$key][] = $value['bdmemail'];
                }
                return \Excel::download(new ArrayRecordsExport(array_merge($exportData,$exportData1)), 'Empanelled_and_non_Active_ARNS_not_met_at_all_'. date('Ymd') .'.xlsx');
        }
        elseif($type==5)
        {
            $csv_headers = array('ARN', 'ARN Name', 'ARN Email','BDM Name','BDM Email');
            $exportData[] = $csv_headers;
            $mapped_partners_only_met_once_list = BDM_Meeting_Dashboard_model::mapped_partners_only_met_once_download($bdm_id);
            foreach($mapped_partners_only_met_once_list as $key => $value)
                {
                    $exportData1[$key][] = $value['arn'];
                    $exportData1[$key][] = $value['arn_holders_name'];
                    $exportData1[$key][] = $value['arn_email'];
                    $exportData1[$key][] = $value['bdmname'];
                    $exportData1[$key][] = $value['bdmemail'];
                }
                return \Excel::download(new ArrayRecordsExport(array_merge($exportData,$exportData1)), 'Mapped_partners_only_met_once_'. date('Ymd') .'.xlsx');
        }
        
   }
   public function daily_meeting_log($input_data)
   {
    $dailymeating_log=array();
    $get_daily_log= BDM_Meeting_Dashboard_model::get_Day_wise_meeting_log($input_data);
    foreach($get_daily_log as $val)
    {
      $date=date('Y-m-d',strtotime($val['start_datetime']));
      if(array_key_exists($date,$dailymeating_log))
      {
       $dailymeating_log[$date][]=array('bdm_id'=>$val['bdm_id'],'meeting_mode'=>$val['meeting_mode'],'arn'=>$val['arn'],'arn_holders_name'=>$val['arn_holders_name'],'start_datetime'=>date('Y-m-d',strtotime($val['start_datetime'])));
      }else{
        $dailymeating_log[$date][]=array('bdm_id'=>$val['bdm_id'],'meeting_mode'=>$val['meeting_mode'],'arn'=>$val['arn'],'arn_holders_name'=>$val['arn_holders_name'],'start_datetime'=>date('Y-m-d',strtotime($val['start_datetime'])));
      }
    }
    return $dailymeating_log;
   }
}
