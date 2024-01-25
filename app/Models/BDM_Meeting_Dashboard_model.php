<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class BDM_Meeting_Dashboard_model extends Model
{
    public static function getbdmlist($user_id)
    {
      $data =  DB::table('users')
                 ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
                    ->select('users.id','users.name')
                    ->where('is_drm_user', '=',1)
                    ->where('users_details.skip_in_arn_mapping', '=',0)
                    ->where('users.status', '=',1)
                    ->where('users_details.is_deleted', '=',0)
					->where('users_details.is_old', '=',0);
                    if(!empty($user_id))
                    {
                      $data->whereIn('users.id',$user_id);
                    }
                    
                    $data=$data->get();
        return json_decode(json_encode($data),true);
    }

	public static function getbdmlist_meeting()
    {
      $data =  DB::table('users')
                 ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
                    ->select('users.id','users.name')
                    ->where('is_drm_user', '=',1)
                    // ->where('users_details.role_id', '=',4)
                    ->where('users.status', '=',1);
                    $data=$data->get();
        return json_decode(json_encode($data),true);
    }
    public static function get_total_arn_detail($data)
    {
      $records = DB::select("
                            select 
                        bdm_detail.* 
                      from 
                        (
                          select 
                            SUM(
                              CASE WHEN(t.is_rankmf_partner = '1') THEN IFNULL(1, 0) ELSE IFNULL(0, 0) END
                            ) AS total_emplement, 
                            count(t.arn) as total_arn_mapped, 
                            SUM(
                              CASE WHEN(t.samcomf_partner_aum > 0) THEN IFNULL(1, 0) ELSE IFNULL(0, 0) END
                            ) AS active_partner, 
                            sum(
                              CASE WHEN(t.project_focus = 'yes') THEN 1 ELSE 0 END
                            ) as total_project_focus, 
                            sum(
                              CASE WHEN(t.project_green_shoots = 'yes') THEN 1 ELSE 0 END
                            ) as total_project_green_shoots, 
                            sum(
                              CASE WHEN(t.project_emerging_stars = 'yes') THEN 1 ELSE 0 END
                            ) as total_emerging_stars,
                            sum(
                            CASE WHEN(IFNULL(t.project_focus,'') != 'yes' and IFNULL(t.project_green_shoots,'') != 'yes' and IFNULL(t.project_emerging_stars,'') != 'yes') THEN 1 ELSE 0 END
                              ) as total_project_other,
                            t.bdm_id, 
                            t.bdmname as bdmname, 
                            t.bdmemail as bdmemail, 
                            t.reporting_name as reporting_name, 
                            t.reporting_email as reporting_email, 
                            t.reporting_mobile_number as reporting_mobile_number 
                          from 
                            (
                              SELECT 
                                u.id as bdm_id, 
                                u.name as bdmname, 
                                u.email as bdmemail, 
                                reporting.name AS reporting_name, 
                                reporting.email AS reporting_email, 
                                reporting_details.mobile_number AS reporting_mobile_number, 
                                drmm.ARN as arn, 
                                drmm.project_focus as project_focus, 
                                drmm.project_green_shoots as project_green_shoots, 
                                drmm.project_emerging_stars as project_emerging_stars, 
                                drmm.is_rankmf_partner, 
                                drmm.samcomf_partner_aum 
                              FROM 
                                users as u 
                                left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                left join users_details as bdm_details on u.id = bdm_details.user_id 
                                left join users as reporting on bdm_details.reporting_to = reporting.id 
                                left join users_details as reporting_details on reporting.id = reporting_details.user_id 
                              where 
                                u.is_drm_user = 1 
                                and u.status = 1
                            ) as t 
                          group by 
                            t.bdm_id
                        ) as bdm_detail 
                      where 
                        bdm_detail.bdm_id =".$data['bdmid']);
         return json_decode(json_encode($records),true);
    }
    public static function get_total_meetingmfd($data)
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
      //x($to_date);
      $records = DB::select("select 
                              monthly.bdm_id, 
                              count(
                                distinct monthly.tota_unique_arn_meeting
                              ) as tota_unique_arn_meeting_month, 
                              sum(monthly.total_monht_meeting) as tota_month_arn_meeting, 
                              sum(
                                monthly.attendence_total_project_focus
                              ) as attendence_total_project_focus, 
                              sum(
                                monthly.attendence_total_project_green_shoots
                              ) as attendence_total_project_green_shoots, 
                              sum(
                                monthly.attendence_total_project_emerging_stars
                              ) as attendence_total_project_emerging_stars, 
                              sum(
                                CASE WHEN(
                                  monthly.attendence_total_project_focus_unqi = '1'
                                ) THEN 1 ELSE 0 END
                              ) as attendence_total_project_focus_unique, 
                              sum(
                                CASE WHEN(
                                  monthly.attendence_total_project_emerging_stars_unqi = '1'
                                ) THEN 1 ELSE 0 END
                              ) as attendence_total_project_emerging_stars_unique, 
                              sum(
                                CASE WHEN(
                                  monthly.attendence_total_project_green_shoots_unqi = '1'
                                ) THEN 1 ELSE 0 END
                              ) as attendence_total_project_green_shoots_unique, 
                              sum(
                                CASE WHEN(
                                  monthly.attendence_total_project_focus_green_both_unqi = '1'
                                ) THEN 1 ELSE 0 END
                              ) as attendence_total_project_focus_green_both_unqi, 
                            sum(
                            monthly.total_project_other
                            ) as attendence_total_project_other, 
                              sum(
                                CASE WHEN(
                                  monthly.total_project_other_unqi = '1'
                                ) THEN 1 ELSE 0 END
                              ) as attendence_total_project_other_unqi, 
                              sum(
                                CASE WHEN(monthly.is_rankmf_partner = '1') THEN 1 ELSE 0 END
                              ) as empanelled_arn, 
                              sum(
                                CASE WHEN(monthly.is_rankmf_partner = '0') THEN 1 ELSE 0 END
                              ) as nonempanelled_arn, 
                              sum(
                                CASE WHEN(monthly.month_met_active = '1') THEN 1 ELSE 0 END
                              ) as month_met_active, 
                              sum(
                                CASE WHEN(monthly.month_met_active = '0') THEN 1 ELSE 0 END
                              ) as nonmonth_met_active, 
                              sum(
                                monthly.attendence_total_project_focus_green_both
                              ) as attendence_total_project_focus_green_both 
                            from 
                              (
                                select 
                                  monthly.* 
                                from 
                                  (
                                    select 
                                      t.arn as tota_unique_arn_meeting, 
                                      count(t.arn) as total_monht_meeting, 
                                      sum(
                                        CASE WHEN(t.project_focus = 'yes') THEN 1 ELSE 0 END
                                      ) as attendence_total_project_focus, 
                                      sum(
                                        CASE WHEN(t.project_green_shoots = 'yes') THEN 1 ELSE 0 END
                                      ) as attendence_total_project_green_shoots, 
                                      sum(
                                        CASE WHEN(t.project_emerging_stars = 'yes') THEN 1 ELSE 0 END
                                      ) as attendence_total_project_emerging_stars, 
                                      CASE WHEN(t.is_rankmf_partner = '1') THEN 1 ELSE 0 END as is_rankmf_partner, 
                                      CASE WHEN(t.samcomf_partner_aum > 0) THEN 1 ELSE 0 END as month_met_active, 
                                      sum(
                                        DISTINCT CASE WHEN(t.project_focus = 'yes') THEN 1 ELSE 0 END
                                      ) as attendence_total_project_focus_unqi, 
                                      sum(
                                        DISTINCT CASE WHEN(t.project_emerging_stars = 'yes') THEN 1 ELSE 0 END
                                      ) as attendence_total_project_emerging_stars_unqi, 
                                      sum(
                                        DISTINCT CASE WHEN(t.project_green_shoots = 'yes') THEN 1 ELSE 0 END
                                      ) as attendence_total_project_green_shoots_unqi, 
                                      sum(
                                        CASE WHEN(
                                          t.project_focus = 'yes' 
                                          and t.project_green_shoots = 'yes' 
                                          and t.project_emerging_stars = 'yes'
                                        ) THEN 1 ELSE 0 END
                                      ) as attendence_total_project_focus_green_both, 
                                      sum(
                                        DISTINCT CASE WHEN(
                                          t.project_focus = 'yes' 
                                          and t.project_green_shoots = 'yes' 
                                          and t.project_emerging_stars = 'yes'
                                        ) THEN 1 ELSE 0 END
                                      ) as attendence_total_project_focus_green_both_unqi, 
                                      sum(
                                        CASE WHEN(
                                          IFNULL(t.project_focus, '') != 'yes' 
                                          and IFNULL(t.project_green_shoots, '') != 'yes' 
                                          and IFNULL(t.project_emerging_stars, '') != 'yes'
                                        ) THEN 1 ELSE 0 END
                                      ) as total_project_other, 
                                      sum(
                                        DISTINCT CASE WHEN(
                                          IFNULL(t.project_focus, '') != 'yes' 
                                          and IFNULL(t.project_green_shoots, '') != 'yes' 
                                          and IFNULL(t.project_emerging_stars, '') != 'yes'
                                        ) THEN 1 ELSE 0 END
                                      ) as total_project_other_unqi, 
                                      t.bdm_id 
                                    from 
                                      (
                                        select 
                                          ed.*, 
                                          drml.ARN as meating_arn 
                                        from 
                                          (
                                            SELECT 
                                              u.id as bdm_id, 
                                              drmm.ARN as arn, 
                                              drmm.project_focus as project_focus, 
                                              drmm.project_emerging_stars as project_emerging_stars, 
                                              drmm.project_green_shoots as project_green_shoots, 
                                              drmm.is_rankmf_partner, 
                                              drmm.samcomf_partner_aum 
                                            FROM 
                                              users as u 
                                              left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                            where 
                                              u.is_drm_user = 1 
                                              and u.status = 1
                                          ) as ed 
                                          left join drm_meeting_logger as drml on (
                                            ed.bdm_id = drml.user_id 
                                            and ed.arn = drml.ARN
                                          ) 
                                        where 
                                          drml.start_datetime >= '".$form_date."' 
                                          and drml.start_datetime <= '".$to_date."'
                                      ) as t 
                                    group by 
                                      t.bdm_id, 
                                      t.arn
                                  ) as monthly
                              ) as monthly 
                            where 
                              monthly.bdm_id = ".$data['bdmid']
                            );
         return json_decode(json_encode($records),true);
    }
    public static function ARNs_havent_met_at_all($data)
    {
      $records = DB::select("select 
                            count(distinct totalarn_notmeeat.arn) as tilltoal_arn_meatnot, 
                            totalarn_notmeeat.bdm_id 
                          from 
                            (
                              select 
                                yed.* 
                              from 
                                (
                                  SELECT 
                                    u.id as bdm_id, 
                                    drmm.ARN as arn 
                                  FROM 
                                    users as u 
                                    left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                  where 
                                    u.is_drm_user = 1 
                                    and u.status = 1
                                ) as yed 
                                left join drm_meeting_logger as drml on (
                                  drml.user_id = yed.bdm_id 
                                  and drml.ARN = yed.arn
                                ) 
                              where 
                                drml.ARN IS NULL
                            ) as totalarn_notmeeat 
                          where 
                            totalarn_notmeeat.bdm_id =".$data['bdmid'] 
                          );
      return json_decode(json_encode($records),true);
    }
    public static function ARNs_havent_met_at_all_download($bdmid)
    {
        $records = DB::select("select 
                                yed.* 
                              from 
                                (
                                  SELECT 
                                    u.id as bdm_id, 
                                    u.name as bdmname, 
                                    u.email as bdmemail, 
                                    drmm.ARN as arn, 
                                    drmm.arn_holders_name as arn_holders_name, 
                                    drmm.arn_email as arn_email 
                                  FROM 
                                    users as u 
                                    left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                  where 
                                    u.is_drm_user = 1 
                                    and u.status = 1
                                ) as yed 
                                left join drm_meeting_logger as drml on (
                                  drml.user_id = yed.bdm_id 
                                  and drml.ARN = yed.arn
                                ) 
                              where 
                                drml.ARN IS NULL 
                                and yed.bdm_id =".$bdmid
                          );
        return json_decode(json_encode($records),true);
    }
    public static function Count_of_non_empanelled_ARNs_havent_met_at_all($data)
    {
        $records = DB::select("select 
                          count(
                            distinct totalarn_notempnotmeeat.arn
                          ) as tilltoal_arn_notempnotmeat, 
                          totalarn_notempnotmeeat.bdm_id 
                        from 
                          (
                            select 
                              yed.* 
                            from 
                              (
                                SELECT 
                                  u.id as bdm_id, 
                                  drmm.ARN as arn 
                                FROM 
                                  users as u 
                                  left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                where 
                                  u.is_drm_user = 1 
                                  and u.status = 1 
                                  and drmm.is_rankmf_partner = 0
                              ) as yed 
                              left join drm_meeting_logger as drml on (
                                drml.user_id = yed.bdm_id 
                                and drml.ARN = yed.arn
                              ) 
                            where 
                              drml.ARN IS NULL
                          ) as totalarn_notempnotmeeat 
                        where 
                          totalarn_notempnotmeeat.bdm_id =".$data['bdmid'] 
                        );
        return json_decode(json_encode($records),true);
    }
    public static function Count_of_non_empanelled_ARNs_havent_met_at_all_download($bdmid)
    {
        $records = DB::select("select 
                                yed.* 
                              from 
                                (
                                  SELECT 
                                    u.id as bdm_id, 
                                    u.name as bdmname, 
                                    u.email as bdmemail, 
                                    drmm.ARN as arn, 
                                    drmm.arn_holders_name as arn_holders_name, 
                                    drmm.arn_email as arn_email 
                                  FROM 
                                    users as u 
                                    left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                  where 
                                    u.is_drm_user = 1 
                                    and u.status = 1 
                                    and drmm.is_rankmf_partner = 0
                                ) as yed 
                                left join drm_meeting_logger as drml on (
                                  drml.user_id = yed.bdm_id 
                                  and drml.ARN = yed.arn
                                ) 
                              where 
                                drml.ARN IS NULL 
                                and yed.bdm_id =".$bdmid
                            );
        return json_decode(json_encode($records),true);
    }
    public static function count_of_ARNs_not_met_since_last_90_days($data)
    {
        $records = DB::select("
                              select 
                        ninedaysmeet.bdm_id, 
                        count(arn) as total_count 
                      from 
                        (
                          select 
                            yed.*, 
                            DATEDIFF(totday, start_datetime) AS notmeetdays 
                          from 
                            (
                              select 
                                yed.*, 
                                max(drml.start_datetime) as start_datetime, 
                                CURRENT_TIMESTAMP() as totday 
                              from 
                                (
                                  SELECT 
                                    u.id as bdm_id, 
                                    drmm.ARN as arn, 
                                    drmm.arn_holders_name as arn_holders_name, 
                                    drmm.arn_email as arn_email 
                                  FROM 
                                    users as u 
                                    left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                  where 
                                    u.is_drm_user = 1 
                                    and u.status = 1
                                ) as yed 
                                left join drm_meeting_logger as drml on (
                                  drml.user_id = yed.bdm_id 
                                  and drml.ARN = yed.arn
                                ) 
                              where 
                                drml.ARN IS not NULL 
                              group by 
                                yed.arn
                            ) as yed 
                          having 
                            notmeetdays >= 90
                        ) as ninedaysmeet 
                      where 
                        ninedaysmeet.bdm_id =".$data['bdmid']
                        );
        return json_decode(json_encode($records),true);
    }
    public static function count_of_ARNs_not_met_since_last_90_days_download($bdmid)
    {
        $records = DB::select("select 
                              yed.*, 
                              DATEDIFF(totday, start_datetime) AS notmeetdays 
                            from 
                              (
                                select 
                                  yed.*, 
                                  max(drml.start_datetime) as start_datetime, 
                                  CURRENT_TIMESTAMP() as totday 
                                from 
                                  (
                                    SELECT 
                                      u.id as bdm_id, 
                                      u.name as bdmname, 
                                      u.email as bdmemail, 
                                      drmm.ARN as arn, 
                                      drmm.arn_holders_name as arn_holders_name, 
                                      drmm.arn_email as arn_email 
                                    FROM 
                                      users as u 
                                      left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                    where 
                                      u.is_drm_user = 1 
                                      and u.status = 1
                                  ) as yed 
                                  left join drm_meeting_logger as drml on (
                                    drml.user_id = yed.bdm_id 
                                    and drml.ARN = yed.arn
                                  ) 
                                where 
                                  drml.ARN IS not NULL 
                                group by 
                                  yed.arn
                              ) as yed 
                            WHERE 
                              yed.bdm_id = '".$bdmid."'
                            having 
                              notmeetdays >= 90"
                        );
        return json_decode(json_encode($records),true);
    }
    public static function empanelled_and_non_Active_ARNS_not_met_at_all($data)
    {
        $records = DB::select("select 
                                count(
                                  distinct totalarn_notempnotmeeat.arn
                                ) as tilltoal_arn_notempnotmeat, 
                                totalarn_notempnotmeeat.bdm_id 
                              from 
                                (
                                  select 
                                    yed.*, 
                                    drml.created_at as created_at 
                                  from 
                                    (
                                      SELECT 
                                        u.id as bdm_id, 
                                        drmm.ARN as arn 
                                      FROM 
                                        users as u 
                                        left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                      where 
                                        u.is_drm_user = 1 
                                        and u.status = 1 
                                        and drmm.is_rankmf_partner = 1 
                                        and (
                                          CASE WHEN drmm.samcomf_partner_aum <= 0 THEN '1' WHEN drmm.samcomf_partner_aum is NUll THEN '2' ELSE '0' END
                                        ) IN (1, 2)
                                    ) as yed 
                                    left join drm_meeting_logger as drml on (
                                      drml.user_id = yed.bdm_id 
                                      and drml.ARN = yed.arn
                                    ) 
                                  where 
                                    drml.ARN IS NULL
                                ) as totalarn_notempnotmeeat 
                              where 
                                totalarn_notempnotmeeat.bdm_id =".$data['bdmid']
                            );
        return json_decode(json_encode($records),true);
    }
    public static function empanelled_and_non_Active_ARNS_not_met_at_all_download($bdmid)
    {
        $records = DB::select("select 
                                yed.* 
                              from 
                                (
                                  SELECT 
                                    u.id as bdm_id, 
                                    u.name as bdmname, 
                                    u.email as bdmemail, 
                                    drmm.ARN as arn, 
                                    drmm.arn_holders_name as arn_holders_name, 
                                    drmm.arn_email as arn_email 
                                  FROM 
                                    users as u 
                                    left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                  where 
                                    u.is_drm_user = 1 
                                    and u.status = 1 
                                    and drmm.is_rankmf_partner = 1 
                                    and (
                                      CASE WHEN drmm.samcomf_partner_aum <= 0 THEN '1' WHEN drmm.samcomf_partner_aum is NUll THEN '2' ELSE '0' END
                                    ) IN (1, 2)
                                ) as yed 
                                left join drm_meeting_logger as drml on (
                                  drml.user_id = yed.bdm_id 
                                  and drml.ARN = yed.arn
                                ) 
                              where 
                                drml.ARN IS NULL 
                                and yed.bdm_id =".$bdmid
                              );
        return json_decode(json_encode($records),true);
    }
    public static function mapped_partners_only_met_once($data)
    {
        $records = DB::select("select 
                                count(distinct meetonectime.arn) as meetonectimecount, 
                                meetonectime.bdm_id 
                              from 
                                (
                                  select 
                                    yed.*, 
                                    count(yed.ARN) as meetcount 
                                  from 
                                    (
                                      SELECT 
                                        u.id as bdm_id, 
                                        drmm.ARN as arn, 
                                        drmm.arn_holders_name as arn_holders_name, 
                                        drmm.arn_email as arn_email 
                                      FROM 
                                        users as u 
                                        left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                      where 
                                        u.is_drm_user = 1 
                                        and u.status = 1
                                    ) as yed 
                                    left join drm_meeting_logger as drml on (
                                      drml.user_id = yed.bdm_id 
                                      and drml.ARN = yed.arn
                                    ) 
                                  where 
                                    drml.ARN IS not NULL 
                                  group by 
                                    yed.arn 
                                  having 
                                    meetcount = 1
                                ) as meetonectime 
                              where 
                                meetonectime.bdm_id =".$data['bdmid']
                          );
        return json_decode(json_encode($records),true);
    }
    public static function mapped_partners_only_met_once_download($bdmid)
    {
        $records = DB::select("select 
                                yed.*, 
                                count(yed.ARN) as meetcount 
                              from 
                                (
                                  SELECT 
                                    u.id as bdm_id, 
                                    u.name as bdmname, 
                                    u.email as bdmemail, 
                                    drmm.ARN as arn, 
                                    drmm.arn_holders_name as arn_holders_name, 
                                    drmm.arn_email as arn_email 
                                  FROM 
                                    users as u 
                                    left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                  where 
                                    u.is_drm_user = 1 
                                    and u.status = 1
                                ) as yed 
                                left join drm_meeting_logger as drml on (
                                  drml.user_id = yed.bdm_id 
                                  and drml.ARN = yed.arn
                                ) 
                              where 
                                drml.ARN IS not NULL 
                                and yed.bdm_id ='".$bdmid."'
                              group by 
                                yed.arn 
                              having 
                                meetcount =1"
                          );
        return json_decode(json_encode($records),true);
    }
    public static function get_total_meetingmfd_sinceexp($data)
    {  
      $form_date='2019-01-01 00:00:00';
      
      $to_date=date('Y-m-d H:i:s');

     
      //x($to_date);
      $records = DB::select("select 
                              monthly.bdm_id, 
                              count(
                                distinct monthly.tota_unique_arn_meeting
                              ) as tota_unique_arn_meeting_month, 
                              sum(monthly.total_monht_meeting) as tota_month_arn_meeting, 
                              sum(
                                monthly.attendence_total_project_focus
                              ) as attendence_total_project_focus, 
                              sum(
                                monthly.attendence_total_project_green_shoots
                              ) as attendence_total_project_green_shoots, 
                              sum(
                                monthly.attendence_total_project_emerging_stars
                              ) as attendence_total_project_emerging_stars, 
                              sum(
                                CASE WHEN(
                                  monthly.attendence_total_project_focus_unqi = '1'
                                ) THEN 1 ELSE 0 END
                              ) as attendence_total_project_focus_unique, 
                              sum(
                                CASE WHEN(
                                  monthly.attendence_total_project_emerging_stars_unqi = '1'
                                ) THEN 1 ELSE 0 END
                              ) as attendence_total_project_emerging_stars_unique, 
                              sum(
                                CASE WHEN(
                                  monthly.attendence_total_project_green_shoots_unqi = '1'
                                ) THEN 1 ELSE 0 END
                              ) as attendence_total_project_green_shoots_unique, 
                              sum(
                                CASE WHEN(
                                  monthly.attendence_total_project_focus_green_both_unqi = '1'
                                ) THEN 1 ELSE 0 END
                              ) as attendence_total_project_focus_green_both_unqi, 
                            sum(
                            monthly.total_project_other
                            ) as attendence_total_project_other, 
                              sum(
                                CASE WHEN(
                                  monthly.total_project_other_unqi = '1'
                                ) THEN 1 ELSE 0 END
                              ) as attendence_total_project_other_unqi, 
                              sum(
                                CASE WHEN(monthly.is_rankmf_partner = '1') THEN 1 ELSE 0 END
                              ) as empanelled_arn, 
                              sum(
                                CASE WHEN(monthly.is_rankmf_partner = '0') THEN 1 ELSE 0 END
                              ) as nonempanelled_arn, 
                              sum(
                                CASE WHEN(monthly.month_met_active = '1') THEN 1 ELSE 0 END
                              ) as month_met_active, 
                              sum(
                                CASE WHEN(monthly.month_met_active = '0') THEN 1 ELSE 0 END
                              ) as nonmonth_met_active, 
                              sum(
                                monthly.attendence_total_project_focus_green_both
                              ) as attendence_total_project_focus_green_both 
                            from 
                              (
                                select 
                                  monthly.* 
                                from 
                                  (
                                    select 
                                      t.arn as tota_unique_arn_meeting, 
                                      count(t.arn) as total_monht_meeting, 
                                      sum(
                                        CASE WHEN(t.project_focus = 'yes') THEN 1 ELSE 0 END
                                      ) as attendence_total_project_focus, 
                                      sum(
                                        CASE WHEN(t.project_green_shoots = 'yes') THEN 1 ELSE 0 END
                                      ) as attendence_total_project_green_shoots, 
                                      sum(
                                        CASE WHEN(t.project_emerging_stars = 'yes') THEN 1 ELSE 0 END
                                      ) as attendence_total_project_emerging_stars, 
                                      CASE WHEN(t.is_rankmf_partner = '1') THEN 1 ELSE 0 END as is_rankmf_partner, 
                                      CASE WHEN(t.samcomf_partner_aum > 0) THEN 1 ELSE 0 END as month_met_active, 
                                      sum(
                                        DISTINCT CASE WHEN(t.project_focus = 'yes') THEN 1 ELSE 0 END
                                      ) as attendence_total_project_focus_unqi, 
                                      sum(
                                        DISTINCT CASE WHEN(t.project_emerging_stars = 'yes') THEN 1 ELSE 0 END
                                      ) as attendence_total_project_emerging_stars_unqi, 
                                      sum(
                                        DISTINCT CASE WHEN(t.project_green_shoots = 'yes') THEN 1 ELSE 0 END
                                      ) as attendence_total_project_green_shoots_unqi, 
                                      sum(
                                        CASE WHEN(
                                          t.project_focus = 'yes' 
                                          and t.project_green_shoots = 'yes' 
                                          and t.project_emerging_stars = 'yes'
                                        ) THEN 1 ELSE 0 END
                                      ) as attendence_total_project_focus_green_both, 
                                      sum(
                                        DISTINCT CASE WHEN(
                                          t.project_focus = 'yes' 
                                          and t.project_green_shoots = 'yes' 
                                          and t.project_emerging_stars = 'yes'
                                        ) THEN 1 ELSE 0 END
                                      ) as attendence_total_project_focus_green_both_unqi, 
                                      sum(
                                        CASE WHEN(
                                          IFNULL(t.project_focus, '') != 'yes' 
                                          and IFNULL(t.project_green_shoots, '') != 'yes' 
                                          and IFNULL(t.project_emerging_stars, '') != 'yes'
                                        ) THEN 1 ELSE 0 END
                                      ) as total_project_other, 
                                      sum(
                                        DISTINCT CASE WHEN(
                                          IFNULL(t.project_focus, '') != 'yes' 
                                          and IFNULL(t.project_green_shoots, '') != 'yes' 
                                          and IFNULL(t.project_emerging_stars, '') != 'yes'
                                        ) THEN 1 ELSE 0 END
                                      ) as total_project_other_unqi, 
                                      t.bdm_id 
                                    from 
                                      (
                                        select 
                                          ed.*, 
                                          drml.ARN as meating_arn 
                                        from 
                                          (
                                            SELECT 
                                              u.id as bdm_id, 
                                              drmm.ARN as arn, 
                                              drmm.project_focus as project_focus, 
                                              drmm.project_emerging_stars as project_emerging_stars, 
                                              drmm.project_green_shoots as project_green_shoots, 
                                              drmm.is_rankmf_partner, 
                                              drmm.samcomf_partner_aum 
                                            FROM 
                                              users as u 
                                              left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                            where 
                                              u.is_drm_user = 1 
                                              and u.status = 1
                                          ) as ed 
                                          left join drm_meeting_logger as drml on (
                                            ed.bdm_id = drml.user_id 
                                            and ed.arn = drml.ARN
                                          ) 
                                        where 
                                          drml.start_datetime >= '".$form_date."' 
                                          and drml.start_datetime <= '".$to_date."'
                                      ) as t 
                                    group by 
                                      t.bdm_id, 
                                      t.arn
                                  ) as monthly
                              ) as monthly 
                            where 
                              monthly.bdm_id = ".$data['bdmid']
                            );
         return json_decode(json_encode($records),true);
    }
    public static function get_Day_wise_meeting_log($data)
    { 
      $form_date=$data['month'].'-01 00:00:00';
      $month_of=date('m',strtotime($form_date));
      $year_of=date('Y',strtotime($form_date));
      $day=cal_days_in_month(CAL_GREGORIAN, $month_of, $year_of);
      $to_date=$data['month'].'-'.$day.' 23:59:59';
      $today_date=date('Y-m-d H:i:s');
	  $to_date = date('Y-m-t 23:59:59', strtotime("$year_of-$month_of-01"));
    //   if($to_date>$today_date)
    //   {
    //    $to_date=$today_date;
    //   }
     
      //x($to_date);
      $records = DB::select("select 
                              ed.*, 
                              drml.start_datetime,
							  drml.meeting_mode 
                            from 
                              (
                                SELECT 
                                  u.id as bdm_id, 
                                  drmm.ARN as arn, 
                                  drmm.arn_holders_name as arn_holders_name 
                                FROM 
                                  users as u 
                                  left join drm_distributor_master as drmm on drmm.direct_relationship_user_id = u.id 
                                where 
                                  u.is_drm_user = 1 
                                  and u.status = 1
                              ) as ed 
                              left join drm_meeting_logger as drml on (
                                ed.bdm_id = drml.user_id 
                                and ed.arn = drml.ARN
                              ) 
                            where 
                              drml.start_datetime >= '".$form_date."' 
                              and drml.start_datetime <= '".$to_date."' 
                              and ed.bdm_id = '".$data['bdmid']."'
							  ORDER BY drml.start_datetime DESC
                            ");
							
         return json_decode(json_encode($records),true);
    }

    public static function get_holidays($from,$to)
    {
		$records = DB::connection('rankmf')
		->table('mf_holidays')
		->select("date")
		->where('date','>=',$from)
		->where('date','<',$to)
		->getRankMFCurl();

        return json_decode(json_encode($records),true);
    }
}
