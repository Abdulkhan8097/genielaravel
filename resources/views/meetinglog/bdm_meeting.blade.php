@php
$data_table_headings_html = '';
@endphp
@extends('../layout')
@section('title', 'BDM Meeting Dashboard')
@section('breadcrumb_heading', 'BDM Meeting Dashboard')

@section('custom_head_tags')


<link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('css/meetinglog.css')}}">
@endsection

@section('content')

<section class="mb-4 mt-4">
                    <div class="row">
                       <div class="col-lg-4">
                          <nav class="breacrumbs" aria-label="breadcrumb">
                             <div>
                                <h3 class="breadcream-text">Meetings Analysis Dash Board</h3>
                                <!-- <ol class="breadcrumb breadcream">
                                   <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                   <li class="breadcrumb-item active" aria-current="page">Client Profile</li>
                                </ol> -->
                             </div>
                          </nav>
                       </div>
                    </div>
                 </section>
<!--/.row mt-4-->
<section class="mb-4 mt-4">
                    <div class="col-lg-12">
                       <div class="row reduce-padding" style="">
                          <div class="col-lg-4">
                             <div class="form-data">
                                <div class="input-area">
                                   <select class="form-input select" id="bdmlist">
                                      <option>Select BDM with Search </option>
                                     @foreach($bdmlist as $val)
                                     <option value="{{$val['id']}}">{{$val['name']}}</option>
                                     @endforeach
                                   </select>
                                   <label class="input-label active">Choose BDM Name</label>
                                   <i class="check-mark"></i>
                                      <div class="alert errorclass" id="bdmlist_error">
                                        <i class="alert-i"></i>
                                        <span>Please Select BDM</span>
                                      </div>
                                </div>
                             </div>
                          </div>
                          <div class="col-lg-4">
                             <div class="form-data">
                                <div class="input-area">
                                   <input type="month" class="form-input" placeholder="" id="month" max="{{date('Y-m')}}">
                                   <label class="input-label active">Choose Month & Year</label>
                                   <i class="check-mark"></i>
                                      <div class="alert errorclass" id="month_error">
                                        <i class="alert-i"></i>
                                        <span>Please Select Month</span>
                                      </div>
                                </div>
                             </div>
                          </div>
                          <div class="col-lg-4">
                             <button class="btn btn-primary mr-1" id="getbdm">Search</button>
                             <button class="btn btn-outline-secondary" id="reset_button">Reset</button>
                          </div>
                       </div>
                    </div>
                 </section>
                 <div id="maincntent" class="hide">
                    <section class="mb-4 mt-4">
                       <div class="col-lg-12">
                          <h2>Profile Summary</h2>
                          <div  class="profile-sm-head">
                             <div class="pro-sm-child border-right">
                                <span> <img src="{{asset('images/profile.png')}}" alt=""></span> 
                                <div class="font-bd-24" id="bdm_name"> Vijay Soni</div>
                             </div>
                             <div class="pro-sm-child-1">
                                <div class="font-14 mb-1"> Reporting Manager</div>
                                <div class="font-bd-18" id="bdm_reporting_name"> Viraj Gandhi</div>
                             </div>
                          </div>
                          <div class="pro-sm-child-2">
                             <ul>
                                <li><label for="" class="font-14">Mapped ARNS
                                    <div class="info-item"><img src="{{asset('images/info-icon.svg')}}"></div> 
                                    <div class="tooltip-box">
                                    Total ARN mapped to you
                                    </div>
                                </label> <span class="font-bd-18" id="Mapped_ARNS">9</span> </li>
                                <li><label for="" class="font-14">Empanelled ARNS
                                <div class="info-item"><img src="{{asset('images/info-icon.svg')}}"></div> 
                                    <div class="tooltip-box">
                                    ARN Registeration is Done as Samcomf Distributor
                                    </div></label> <span class="font-bd-18" id="Empanelled_ARNS">8</span></li>
                                <li><label for="" class="font-14"> Active ARNS
                                 <div class="info-item"><img src="{{asset('images/info-icon.svg')}}"></div> 
                                    <div class="tooltip-box">
                                    ARN who brought the Investment through any Scheme
                                    </div>
                                </label> <span class="font-bd-18" id="Active_ARNS">7</span></li>
                             </ul>
                          </div>
                       </div>
                    </section>
                    <section class="mb-4 mt-4">
                       <div class="col-lg-12">
                          <h2 id="month_summary_title">Meeting Summary For March</h2>
                          <div class="summary-dv">
                             <div class="summary-child">
                                <div class="sm-value" id="Target_Meetings"> 15</div>
                                <div class="sm-title">Target Meetings
                                 <div class="info-item"><img src="{{asset('images/info-icon.svg')}}"></div> 
                                    <div class="tooltip-box">
                                   Number of Meeting that need to Done in a Month
                                    </div>
                                </div>
                                <div class="sm-icon"> <img src="{{asset('images/user-blue.png')}}" alt=""> </div>
                             </div>
                             <div class="summary-child">
                                <div class="sm-value" id="Total_Meetings_MTD">12</div>
                                <div class="sm-title">Total Meetings MTD
                                 <div class="info-item"><img src="{{asset('images/info-icon.svg')}}"></div> 
                                    <div class="tooltip-box">
                                    Total Meeting Done in a Month
                                    </div>
                                </div>
                                <div class="sm-icon"> <img src="{{asset('images/user-green.png')}}" alt=""> </div>
                             </div>
                             <div class="summary-child active" id="box_id_class">
                                <div class="sm-value" id="Missed_Achieved_Meetings">5</div>
                                <div class="sm-title" id="Missed_Achieved_Meetings_text">Missed / Over Achieved Meetings Target By
                                 <div class="info-item"><img src="{{asset('images/info-icon.svg')}}">
                                 </div> 
                                    <div class="tooltip-box">
                                   You have meet the Meeting Target or NO
                                    </div>
                                </div>
                                <div class="sm-icon"> <img src="{{asset('images/user-red.png')}}" alt=""> </div>
                             </div>
                          </div>
                       </div>
                    </section>
                    <section class="mb-4 mt-4">
                       <div class="col-lg-12">
                          <h2>Summary / Opportunities</h2>
                          <div class="sum-opp-table table-responsive ">
                             <table id="smm-opp">
                                <thead>
                                   <tr>
                                      <th>Opportunities</th>
                                      <th>Count</th>
                                      <th>Download</th>
                                   </tr>
                                </thead>
                                <tbody>
                                   <tr>
                                      <td class="font-15">ARNs haven’t met at all
                                          <div class="info-item"><img src="{{asset('images/info-icon.svg')}}">
                                          </div> 
                                          <div class="tooltip-box">
                                          Count of ARNS who have not met till now
                                          </div>
                                      </td>
                                      <td id="ARNs_havent_met_at_all">10</td>
                                      <td><a href="javascript:void(0)" class="download_csv" id="ARNs_havent_met_at_all_click"><img src="{{asset('images/down.png')}}" alt=""></a></td>
                                   </tr>
                                   <tr>
                                      <td class="font-15">Count of non empanelled ARNs haven’t met at all
                                          <div class="info-item"><img src="{{asset('images/info-icon.svg')}}">
                                          </div> 
                                          <div class="tooltip-box">
                                          Total of ARNs empanelled but not met till now
                                          </div>
                                      </td>
                                      <td id="Count_of_non_empanelled_ARNs_havent_met_at_all">15</td>
                                      <td><a href="javascript:void(0)" id="Count_of_non_empanelled_ARNs_havent_met_at_all_click"><img src="{{asset('images/down.png')}}" alt=""></a></td>
                                   </tr>
                                   <tr>
                                      <td class="font-15">Count of ARNs not met since last 90 days
                                      <div class="info-item"><img src="{{asset('images/info-icon.svg')}}">
                                       </div> 
                                       <div class="tooltip-box">
                                       Total of ARNs not met since last 90 days 
                                       </div>
                                    </td>
                                      <td id="Count_of_ARNs_not_met_since_last_90_days">6</td>
                                      <td><a href="javascript:void(0);" id="Count_of_ARNs_not_met_since_last_90_days_click"><img src="{{asset('images/down.png')}}" alt=""></a></td>
                                   </tr>
                                   <tr>
                                      <td class="font-15">Empanelled and non Active ARNS not met at all
                                       <div class="info-item"><img src="{{asset('images/info-icon.svg')}}">
                                          </div> 
                                          <div class="tooltip-box">
                                          Count of ARNs empanelled and not active but not met till now 
                                          </div>
                                      </td>
                                      <td id="Empanelled_and_non_Active_ARNS_not_met_at_all">15</td>
                                      <td><a href="javascript:void(0);" id="Empanelled_and_non_Active_ARNS_not_met_at_all_click"><img src="{{asset('images/down.png')}}" alt=""></a></td>
                                   </tr>
                                   <tr>
                                      <td class="font-15">Mapped partners only met once
                                       <div class="info-item"><img src="{{asset('images/info-icon.svg')}}">
                                       </div> 
                                       <div class="tooltip-box">
                                       Total mapped ARNs met only once till now
                                       </div>
                                      </td>
                                      <td id="Mapped_partners_only_met_once">18</td>
                                      <td><a href="#" id="Mapped_partners_only_met_once_click"><img src="{{asset('images/down.png')}}" alt=""></a></td>
                                   </tr>
                                </tbody>
                             </table>
                          </div>
                       </div>
                    </section>
                    <div>
                     <section class="mb-4 mt-4" id="statewise_details">
                        <div class="col-lg-12">
                           <h2>Day Wise Meeting Log</h2>
                           <div class="day-wise-mt-log table-responsive">
                              <table>
                                 <thead>
                                    <tr>
                                       <th>Date</th>
                                       <th>ARNs | Name</th>
                                       <th>Total Meeting Count</th>
                                       <th>Meeting Mode Wise Breakup</th>
                                    </tr>
                                 </thead>
                                 <tbody id="Day_wise_meeting_log">
                                    <tr>
                                       <td>01/01/2022</td>
                                       <td> <span style="display: block;">120121 | Chirag Joshi</span> <span style="display: block;"> 123432 | Viraj Gandhi</span> <span style="display: block;"> 123432 | Viraj Gandhi</span><span style="display: block;">120121 | Chirag Joshi</span> <span style="display: block;"> 123432 | Viraj Gandhi</span> <span style="display: block;"> 123432 | Viraj Gandhi</span> </td>
                                       </td>
                                       <td>2</td>
                                    </tr>
                                    <tr>
                                       <td>01/01/2022</td>
                                       <td> <span style="display: block;">120121 | Chirag Joshi</span><span> 123432 | Viraj Gandhi</span></td>
                                       </td>
                                       <td>2</td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </section>
                    </div>
                    </div>
              </div>
            <!-- end:: Header-->

        </div>
    </div>
<!-- Modal -->
<div class="modal fade" id="AnalysisModal" tabindex="-1" role="dialog" aria-labelledby="AnalysisModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AnalysisModalLabel">ARN Holders Name</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="Analysisbody" style="margin:20px;line-height:25px;" >
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('custom_after_footer_html')

<!-- End View modal -->

@endsection

@section('custom_scripts')

    <script src="{{asset('js/dashboard.js')}}"></script>
    <script src="{{asset('js/select2.min.js')}}"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      $("#bdmlist").select2();
        //load_meetinglog_datatable();
        $('#getbdm').on('click',function()
        {
               let month=$('#month').val();
               let bdmlist=$('#bdmlist').val();
               //alert(bdmlist);
               let errro=1;
               if(month==undefined ||month=='')
               {
                $('#month_error').show();
                errro=0;
               }else{
                  $('#month_error').hide();
               }
               if(bdmlist==undefined ||bdmlist=='Select BDM with Search')
               {
                $('#bdmlist_error').show();
                errro=0;
               }else{
                  $('#bdmlist_error').hide();
               }
               if(errro==1)
               {
                  $.ajax({
                  type: 'POST',
                  url: baseurl +'/view-detail-BDM',
                  data: {
                  bdmid: bdmlist,
                  month: month
                  },
                  error: function(jqXHR, textStatus, errorThrown){
                  if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                  prepare_error_text(jqXHR.responseJSON);
                  }
                  else{
                  swal('', unable_to_process_request_text, 'warning');
                  }
                  },
                  success: function(data) {
                  var resultdata = JSON.parse(data);
                  let arn_detail=resultdata['arn_detail'];
                  let meeting_summery=resultdata['meeting_summery'];
                  let arn_havent_met_till_list=resultdata['ARNs_havent_met_till_list'];
                  let count_of_non_empanelled_ARNs_havent_met_at_all_till_list=resultdata['Count_of_non_empanelled_ARNs_havent_met_at_all_till_list'];
                  let count_of_ARNs_not_met_since_last_90_days_till_list=resultdata['count_of_ARNs_not_met_since_last_90_days_till_list'];
                  let empanelled_and_non_Active_ARNS_not_met_at_all_till_list=resultdata['empanelled_and_non_Active_ARNS_not_met_at_all_till_list'];
                  let mapped_partners_only_met_once_till_list=resultdata['mapped_partners_only_met_once_till_list'];
                  let month_meeting_detail=resultdata['month_meeting_detail'];
                  let sinceexp_meeting_detail=resultdata['sinceexp_meeting_detail'];
                  let daily_meting_log=resultdata['daily_meting_log'];
                   console.log(daily_meting_log);
                   /*const keys = daily_meting_log.keys();
                   console.log(keys);*/
                   
                   //bdm profile
                   $('#bdm_name').html(arn_detail['bdmname']);
                   $('#bdm_reporting_name').html(arn_detail['reporting_name']);
                   $('#Mapped_ARNS').html(checkminval(arn_detail['total_arn_mapped']));
                   $('#Empanelled_ARNS').html(checkminval(arn_detail['total_emplement']));
                   $('#Active_ARNS').html(checkminval(arn_detail['active_partner']));
                   //================ end ==========
                   //summary of month
                   $('#month_summary_title').html('Meeting Summary For '+meeting_summery['month']);
                   $('#Target_Meetings').html(checkminval(meeting_summery['total_month_meeting']));
                   $('#Total_Meetings_MTD').html(checkminval(meeting_summery['total_month_meeting_attendenc']));
                   add_sms_class(checkminval(meeting_summery['absent_till_month']),meeting_summery['total_val_attendence']);
                   //============== end ========
                   //ARNs haven’t met at all
                   $('#ARNs_havent_met_at_all').html(checkminval(arn_havent_met_till_list['tilltoal_arn_meatnot']));
                   if(checkminval(arn_havent_met_till_list['tilltoal_arn_meatnot'])>0)
                   {
                   $("#ARNs_havent_met_at_all_click").attr("href", "download-detail-BDM/"+arn_havent_met_till_list['bdm_id']+'/1');
                   $("#ARNs_havent_met_at_all_click").show();
                   }else{
                     $("#ARNs_havent_met_at_all_click").hide();
                   }
                   // ================= end ======
                   //Count of non empanelled ARNs haven’t met at all
                    $('#Count_of_non_empanelled_ARNs_havent_met_at_all').html(checkminval(count_of_non_empanelled_ARNs_havent_met_at_all_till_list['tilltoal_arn_notempnotmeat']));
                    if(checkminval(count_of_non_empanelled_ARNs_havent_met_at_all_till_list['tilltoal_arn_notempnotmeat'])>0)
                    {
                    $("#Count_of_non_empanelled_ARNs_havent_met_at_all_click").attr("href", "download-detail-BDM/"+count_of_non_empanelled_ARNs_havent_met_at_all_till_list['bdm_id']+'/2');
                    $("#Count_of_non_empanelled_ARNs_havent_met_at_all_click").show();
                   }else{
                     $("#Count_of_non_empanelled_ARNs_havent_met_at_all_click").hide();
                   }
                  //============ end ============

                    //Count of ARNs not met since last 90 days
                    $('#Count_of_ARNs_not_met_since_last_90_days').html(checkminval(count_of_ARNs_not_met_since_last_90_days_till_list['total_count']));
                    if(checkminval(count_of_ARNs_not_met_since_last_90_days_till_list['total_count'])>0)
                    {
                    $("#Count_of_ARNs_not_met_since_last_90_days_click").attr("href", "download-detail-BDM/"+count_of_ARNs_not_met_since_last_90_days_till_list['bdm_id']+'/3');
                    $("#Count_of_ARNs_not_met_since_last_90_days_click").show();
                    }else{
                     $("#Count_of_ARNs_not_met_since_last_90_days_click").hide();
                    }
                  //============ end ============
                    //Empanelled and non Active ARNS not met at all
                    $('#Empanelled_and_non_Active_ARNS_not_met_at_all').html(checkminval(empanelled_and_non_Active_ARNS_not_met_at_all_till_list['tilltoal_arn_notempnotmeat']));
                    if(checkminval(empanelled_and_non_Active_ARNS_not_met_at_all_till_list['tilltoal_arn_notempnotmeat'])>0)
                    {
                    $("#Empanelled_and_non_Active_ARNS_not_met_at_all_click").attr("href", "download-detail-BDM/"+empanelled_and_non_Active_ARNS_not_met_at_all_till_list['bdm_id']+'/4');
                    $("#Empanelled_and_non_Active_ARNS_not_met_at_all_click").show();
                    }else{
                     $("#Empanelled_and_non_Active_ARNS_not_met_at_all_click").hide();
                    }
                  //============ end ============
                    //Mapped partners only met once
                    $('#Mapped_partners_only_met_once').html(checkminval(mapped_partners_only_met_once_till_list['meetonectimecount']));
                    if(checkminval(mapped_partners_only_met_once_till_list['meetonectimecount'])>0)
                    {
                    $("#Mapped_partners_only_met_once_click").attr("href", "download-detail-BDM/"+mapped_partners_only_met_once_till_list['bdm_id']+'/5');
                    $("#Mapped_partners_only_met_once_click").show();
                    }else{
                     $("#Mapped_partners_only_met_once_click").hide();
                    }
                  //============ end ============

                    //Meeting Width analys monthly
                    //Mapped Count
                    $('#Mapped_Count_total').html(checkminval(arn_detail['total_arn_mapped']));
                    $('#Mapped_Count_total_Empanelled_ARN').html(checkminval(arn_detail['total_emplement']));
                    $('#Mapped_Count_total_Non_Empanelled_ARNS').html(checkminval(arn_detail['non_empanelled_arn']));
                    $('#Mapped_Count_total_active').html(checkminval(arn_detail['active_partner']));
                    $('#Mapped_Count_total_non_active').html(checkminval(arn_detail['non_active']));
                    //Met Count
                    $('#Met_Count_Mapped').html(checkminval(month_meeting_detail['total_meeting_month']));
                    $('#Met_Count_Empanelled_ARN').html(checkminval(month_meeting_detail['total_meeting_month_emp']));
                    $('#Met_Count_Non_Empanelled_ARNS').html(checkminval(month_meeting_detail['total_meeting_month_nonemp']));
                    $('#Met_Count_active').html(checkminval(month_meeting_detail['total_meeting_month_active']));
                    $('#Met_Count_non_active').html(checkminval(month_meeting_detail['total_meeting_month_nonactive']));
                    //Not Met
                    $('#Not_Met_Mapped').html(minvalue(arn_detail['total_arn_mapped'],month_meeting_detail['total_meeting_month']));
                    $('#Not_Met_Empanelled_ARN').html(minvalue(arn_detail['total_emplement'],month_meeting_detail['total_meeting_month_emp']));
                    $('#Not_Met_Non_Empanelled_ARNS').html(minvalue(arn_detail['non_empanelled_arn'],month_meeting_detail['total_meeting_month_nonemp']));
                    $('#Not_Met_active').html(minvalue(arn_detail['active_partner'],month_meeting_detail['total_meeting_month_active']));
                    $('#Not_Met_nonactive').html(minvalue(arn_detail['non_active'],month_meeting_detail['total_meeting_month_nonactive']));
                    //Not Met  per
                    $('#Not_Met_Mapped_pre').html(calculate_per(arn_detail['total_arn_mapped'],minvalue(arn_detail['total_arn_mapped'],month_meeting_detail['total_meeting_month']))+'%');
                    $('#Not_Met_Empanelled_ARN_per').html(calculate_per(arn_detail['total_emplement'],minvalue(arn_detail['total_emplement'],month_meeting_detail['total_meeting_month_emp']))+'%');
                    $('#Not_Met_Non_Empanelled_ARNS_per').html(calculate_per(arn_detail['non_empanelled_arn'],minvalue(arn_detail['non_empanelled_arn'],month_meeting_detail['total_meeting_month_nonemp']))+'%');
                    $('#Not_Met_active_per').html(calculate_per(arn_detail['active_partner'],minvalue(arn_detail['active_partner'],month_meeting_detail['total_meeting_month_active']))+'%');
                    $('#Not_Met_nonactive_per').html(calculate_per(arn_detail['non_active'],minvalue(arn_detail['non_active'],month_meeting_detail['total_meeting_month_nonactive']))+'%');
                  //============ end ============
                  //Project Category Wise
                    $('#Mapped_Count_Project_Focus').html(checkminval(arn_detail['total_project_focus']));
                    $('#Mapped_Count_Project_Green_shoots').html(checkminval(arn_detail['total_project_green_shoots']));
                    $('#Mapped_Count_Project_Emerging_Stars').html(checkminval(arn_detail['total_emerging_stars']));
                    $('#Mapped_Count_Others').html(checkminval(arn_detail['total_project_other']));

                    $('#Met_Count_Project_Focus').html(checkminval(month_meeting_detail['attendence_total_project_focus_unique']));
                   $('#Met_Count_Project_Green_shoots').html(checkminval(month_meeting_detail['attendence_total_project_green_shoots_unique']));

                   $('#Met_Count_Project_Emerging_Stars').html(checkminval(month_meeting_detail['attendence_total_project_emerging_stars_unique']));
                   $('#Met_Count_Others').html(checkminval(month_meeting_detail['total_other_uniquearn']));

                  $('#Not_Met_Project_Focus').html(minvalue(arn_detail['total_project_focus'],month_meeting_detail['attendence_total_project_focus_unique']));
                  $('#Not_Met_Project_Green_shoots').html(minvalue(arn_detail['total_project_green_shoots'],month_meeting_detail['attendence_total_project_green_shoots_unique']));

                  $('#Not_Met_Project_Emerging_Stars').html(minvalue(arn_detail['total_emerging_stars'],month_meeting_detail['attendence_total_project_emerging_stars_unique']));
                  $('#Not_Met_Others').html(minvalue(arn_detail['total_project_other'],month_meeting_detail['total_other_uniquearn']));

                     $('#Not_Met_Project_Focus_per').html(calculate_per(arn_detail['total_project_focus'],minvalue(arn_detail['total_project_focus'],month_meeting_detail['attendence_total_project_focus_unique']))+'%');
                     $('#Not_Met_Project_Green_shoots_per').html(calculate_per(arn_detail['total_project_green_shoots'],minvalue(arn_detail['total_project_green_shoots'],month_meeting_detail['attendence_total_project_green_shoots_unique']))+'%');

                     $('#Not_Met_Project_Emerging_Stars_per').html(calculate_per(arn_detail['total_emerging_stars'],minvalue(arn_detail['total_emerging_stars'],month_meeting_detail['attendence_total_project_emerging_stars_unique']))+'%');
                     $('#Not_Met_Others_per').html(calculate_per(arn_detail['total_project_other'],minvalue(arn_detail['total_project_other'],month_meeting_detail['total_other_uniquearn']))+'%');


                    //======== end======
                     //Meeting Width analys since exp
                    //Mapped Count
                    $('#Mapped_Count_total_since').html(checkminval(arn_detail['total_arn_mapped']));
                    $('#Mapped_Count_total_Empanelled_ARN_since').html(checkminval(arn_detail['total_emplement']));
                    $('#Mapped_Count_total_Non_Empanelled_ARNS_since').html(checkminval(arn_detail['non_empanelled_arn']));
                    $('#Mapped_Count_total_active_since').html(arn_detail['active_partner']);
                    $('#Mapped_Count_total_non_active_since').html(checkminval(arn_detail['non_active']));
                    //Met Count
                    $('#Met_Count_Mapped_since').html(checkminval(sinceexp_meeting_detail['total_meeting_month']));
                    $('#Met_Count_Empanelled_ARN_since').html(checkminval(sinceexp_meeting_detail['total_meeting_month_emp']));
                    $('#Met_Count_Non_Empanelled_ARNS_since').html(checkminval(sinceexp_meeting_detail['total_meeting_month_nonemp']));
                    $('#Met_Count_active_since').html(checkminval(sinceexp_meeting_detail['total_meeting_month_active']));
                    $('#Met_Count_non_active_since').html(checkminval(sinceexp_meeting_detail['total_meeting_month_nonactive']));
                    //Not Met
                    $('#Not_Met_Mapped_since').html(minvalue(arn_detail['total_arn_mapped'],sinceexp_meeting_detail['total_meeting_month']));
                    $('#Not_Met_Empanelled_ARN_since').html(minvalue(arn_detail['total_emplement'],sinceexp_meeting_detail['total_meeting_month_emp']));
                    $('#Not_Met_Non_Empanelled_ARNS_since').html(minvalue(arn_detail['non_empanelled_arn'],sinceexp_meeting_detail['total_meeting_month_nonemp']));
                    $('#Not_Met_active_since').html(minvalue(arn_detail['active_partner'],sinceexp_meeting_detail['total_meeting_month_active']));
                    $('#Not_Met_nonactive_since').html(minvalue(arn_detail['non_active'],sinceexp_meeting_detail['total_meeting_month_nonactive']));
                    //Not Met  per
                    $('#Not_Met_Mapped_pre_since').html(calculate_per(arn_detail['total_arn_mapped'],minvalue(arn_detail['total_arn_mapped'],sinceexp_meeting_detail['total_meeting_month']))+'%');
                    $('#Not_Met_Empanelled_ARN_per_since').html(calculate_per(arn_detail['total_emplement'],minvalue(arn_detail['total_emplement'],sinceexp_meeting_detail['total_meeting_month_emp']))+'%');
                    $('#Not_Met_Non_Empanelled_ARNS_per_since').html(calculate_per(arn_detail['non_empanelled_arn'],minvalue(arn_detail['non_empanelled_arn'],sinceexp_meeting_detail['total_meeting_month_nonemp']))+'%');
                    $('#Not_Met_active_per_since').html(calculate_per(arn_detail['active_partner'],minvalue(arn_detail['active_partner'],sinceexp_meeting_detail['total_meeting_month_active']))+'%');
                    $('#Not_Met_nonactive_per_since').html(calculate_per(arn_detail['non_active'],minvalue(arn_detail['non_active'],sinceexp_meeting_detail['total_meeting_month_nonactive']))+'%');
                  //============ end ============
                    //Project Category Wise since
                    $('#Mapped_Count_Project_Focus_since').html(checkminval(arn_detail['total_project_focus']));
                    $('#Mapped_Count_Project_Green_shoots_since').html(checkminval(arn_detail['total_project_green_shoots']));

                     $('#Mapped_Count_Project_Emerging_Stars_since').html(checkminval(arn_detail['total_emerging_stars']));
                    $('#Mapped_Count_Others_since').html(checkminval(arn_detail['total_project_other']));

                    $('#Met_Count_Project_Focus_since').html(checkminval(sinceexp_meeting_detail['attendence_total_project_focus_unique']));
                     $('#Met_Count_Project_Green_shoots_since').html(checkminval(sinceexp_meeting_detail['attendence_total_project_green_shoots_unique']));
                      
                      $('#Met_Count_Project_Emerging_Stars_since').html(checkminval(sinceexp_meeting_detail['attendence_total_project_emerging_stars_unique']));
                     $('#Met_Count_Others_since').html(checkminval(sinceexp_meeting_detail['total_other_uniquearn']));

                     $('#Not_Met_Project_Focus_since').html(minvalue(arn_detail['total_project_focus'],sinceexp_meeting_detail['attendence_total_project_focus_unique']));
                     $('#Not_Met_Project_Green_shoots_since').html(minvalue(arn_detail['total_project_green_shoots'],sinceexp_meeting_detail['attendence_total_project_green_shoots_unique']));
                       
                     $('#Not_Met_Project_Emerging_Stars_since').html(minvalue(arn_detail['total_emerging_stars'],sinceexp_meeting_detail['attendence_total_project_emerging_stars_unique']));
                     $('#Not_Met_Others_since').html(minvalue(arn_detail['total_project_other'],sinceexp_meeting_detail['total_other_uniquearn']));

                     $('#Not_Met_Project_Focus_per_since').html(calculate_per(arn_detail['total_project_focus'],minvalue(arn_detail['total_project_focus'],sinceexp_meeting_detail['attendence_total_project_focus_unique']))+'%');
                     $('#Not_Met_Project_Green_shoots_per_since').html(calculate_per(arn_detail['total_project_green_shoots'],minvalue(arn_detail['total_project_green_shoots'],sinceexp_meeting_detail['attendence_total_project_green_shoots_unique']))+'%');

                     $('#Not_Met_Project_Emerging_Stars_per_since').html(calculate_per(arn_detail['total_emerging_stars'],minvalue(arn_detail['total_emerging_stars'],sinceexp_meeting_detail['attendence_total_project_emerging_stars_unique']))+'%');
                     $('#Not_Met_Others_per_since').html(calculate_per(arn_detail['total_project_other'],minvalue(arn_detail['total_project_other'],sinceexp_meeting_detail['total_other_uniquearn']))+'%');

                    //======== end======
                     //Day wise meeting log
                     let htmlcontent=myFunction(daily_meting_log);
                     $('#Day_wise_meeting_log').html(htmlcontent);
                  }
                  }).then(function() {
                  $("#maincntent").removeClass("hide");
                  $('#maincntent').show();
                  });
               }
        });
      
    });

 function minvalue(from,to)
 {
   let minval=parseInt(from)-parseInt(to);
   console.log(minval+'minvalue');
   if(isNaN(minval))
   {
      minval=0;
   }
   return minval;
 }
 function calculate_per(total,current)
 {
   let val=Math.floor(((parseInt(current)*100)/parseInt(total)));
   //console.log(val);
   if(isNaN(val))
   {
      val=0;
   }
   return val;
 }
//  function myFunction(list_array) 
//  {
//    let html='';
//    for (var key in list_array) 
//    {
//       html+='<tr><td>';
//       html+=key;
//       html+='</td><td><ul class="list_item">';
//       let sub_array=list_array[key];
//       let p=0;
//       for (var keys in sub_array)
//       {
//          html+='<li>'+sub_array[keys]['arn']+' | '+sub_array[keys]['arn_holders_name']+'</li>';
//          p++;
//          //console.log(sub_array[keys]);
//       }
//        html+='</ul></td><td>'+p+'</td></tr>';
//    }
//    return html;
//   }

function showAnalysisModel(txt){
	$("#Analysisbody").html(txt);
	$('#AnalysisModal').modal('show');
}

function myFunction(list_array) {
	let html = '';
	for (var date in list_array) {

		html += '<tr><td>';
		html += date;
		html += '</td><td><ul class="list_item">';
		let meetingData = list_array[date];
		let p = 0;
		let meetingModeCounts = {};
		let users = {};

		for (var i = 0; i < meetingData.length; i++) {
			// console.log(users);
			let meeting = meetingData[i];
			html += '<li>' + meeting['arn'] + ' | ' + meeting['arn_holders_name'] + '</li>';
			p++;

			let meetingMode = meeting['meeting_mode'];

			if (meetingMode in meetingModeCounts) {
				meetingModeCounts[meetingMode]++;
				users[meetingMode] += meeting['arn_holders_name'] + "<br>";
			} else {
				meetingModeCounts[meetingMode] = 1;
				users[meetingMode] = meeting['arn_holders_name'] + "<br>";
			}
		}
		html += '</ul></td><td>' + p + '</td>';

		// Display "View" icons for "Virtual Meeting" and "Phone Call"
		html += '<td>';
		@php
			$constants = config("constants");
			$constants = $constants['MEETING_MODE'];
		@endphp
		console.log(meetingModeCounts);
		@foreach($constants as $constant)
		if ("{{ $constant }}" in meetingModeCounts) {
			html += '{{ $constant }}: ' + meetingModeCounts["{{ $constant }}"] + ' ';
			html += '<div class="info-item"><img onclick="showAnalysisModel(\'' + users["{{ $constant }}"] + '\');" src="{{asset('images/info-icon.svg')}}"></div>';
			html += '<br>';
		}
		@endforeach
		html += '</td>';
		html += '</tr>';
	}
	return html;
}



function checkminval(val)
   {
   let minval=parseInt(val);
   console.log(minval+'checkminval');
   if(isNaN(minval))
   {
   minval=0;
   }
   return minval;
   }
$('#reset_button').on('click',function(){
   $('#month').val('');
   $("#bdmlist").select2("val", '0');
   $("#maincntent").hide();
})
function add_sms_class(val,val_two)
{
 if(val==0)
 {
   $('#box_id_class').removeClass('inactive');
   $('#box_id_class').addClass('active');
   $('#Missed_Achieved_Meetings').html(val_two*-1);
   $('#Missed_Achieved_Meetings_text').html('Over Achieved Meetings Target By');
 }else{
   $('#box_id_class').removeClass('active');
   $('#box_id_class').addClass('inactive');
   $('#Missed_Achieved_Meetings').html(val_two);
   $('#Missed_Achieved_Meetings_text').html('Missed')
 }
}

    </script>


@endsection
