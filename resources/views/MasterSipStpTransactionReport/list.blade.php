@php
$data_table_headings_html = '';
if(isset($data_table_headings) && is_array($data_table_headings) && count($data_table_headings) > 0){
    foreach($data_table_headings as $key => $value){
        $data_table_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}

// retrieving logged in user role and permission details
if(!isset($logged_in_user_roles_and_permissions)){
    $logged_in_user_roles_and_permissions = array();
}
if(!isset($flag_have_all_permissions)){
    $flag_have_all_permissions = false;
}

//searching for multiple status
$arr_status = $arr_status;
$arr_status_json = json_encode($arr_status);

//serching frequency Monthly and Quaterly
$arr_frequency = array_merge(array(array('key' => '', 'value' => 'All')), $arr_frequency);
$arr_frequency_json = json_encode($arr_frequency);

@endphp

@extends('../layout')
@section('title', 'SIP/STP registration data')
@section('breadcrumb_heading', 'SIP/STP registration data')

@section('custom_head_tags')

<link href="{{asset('css/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .faq-question::before{
        padding-right: 0px!important;
        content:unset!important;
    }
    .faq-answer::before{
        padding-right: 0px!important;
        content:unset!important;
    }
    div.form-group label{
        font-weight: bold;
    }
    div.form-group em{
        color: #8b8787;
    }
</style>

@endsection
@section('content')

<div class="row mt-4">
    <div class="col-md-12 mb-2">
        <div class="row">
            <div class="col-md-12">
                <div class="tab-content-item">
                    <ul class="nav nav-tabs new-tab mt-0" id="myTab" role="tablist">
                        @if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('MasterSipStpTransactionReport/Detailed', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE))
                        <li class="nav-item active">
                            <a class="nav-link" href="#detailed_view">Detailed Records</a>
                        </li>
                        @endif
                        @if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('getPredefinedSipStpReport', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE))
                        <li class="nav-item">
                            <a class="nav-link" href="#predefined_reports">Predefined Reports</a>
                        </li>
                        @endif
                    </ul>
                    <div class="tab-content  data-tabs">
                        @if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('MasterSipStpTransactionReport/Detailed', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE))
                        <div class="tab-pane show active tab-list" id="detailed_view">
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="faq-border-top">
                                        <div class="faq-box">
                                            <div id="accordian_filter" class="faq-question text-center">Show Filter <i class="arrow"></i></div><!--/.faq-question-->
                                            <div class="faq-answer">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="row form-inline">
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Zone</label>
                                                                    <input type="text" class="form-control datatable-input" placeholder="" data-col-index="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Branch</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="1" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Location</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="2" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Ihno</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="3" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Folio</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="4" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Investor Name</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="5" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Registration Date</label>
                                                                    <div class="row">
                                                                        <div class="col-lg-10">
                                                                            <input type="date" data-from_date="1" name="registrationDate[start]" class="form-control datatable-input" data-col-index="6" placeholder="From Date">
                                                                        </div>
                                                                        <div class="col-lg-2"><a href="javascript:void(0);" onclick="clear_date(this, 6, data_table);">X</a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-12"> - 
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-10">
                                                                            <input type="date" data-to_date="1" name="registrationDate[end]" class="form-control datatable-input"  data-col-index="6" placeholder="To Date">
                                                                        </div>
                                                                        <div class="col-lg-2"><a href="javascript:void(0);" onclick="clear_date(this, 6, data_table);">X</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Start Date</label>
                                                                    <div class="row">
                                                                        <div class="col-lg-10">
                                                                            <input type="date" data-from_date="1" name="start_Date[start]" class="form-control datatable-input" data-col-index="7" placeholder="From Date">
                                                                        </div>
                                                                        <div class="col-lg-2"><a href="javascript:void(0);" onclick="clear_date(this, 7, data_table);">X</a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-12"> - 
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-10">
                                                                            <input type="date" data-to_date="1" name="start_Date[end]" class="form-control datatable-input"  data-col-index="7" placeholder="To Date">
                                                                        </div>
                                                                        <div class="col-lg-2"><a href="javascript:void(0);" onclick="clear_date(this, 7, data_table);">X</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>End Date</label>
                                                                    <div class="row">
                                                                        <div class="col-lg-10">
                                                                            <input type="date" data-from_date="1" name="end_Date[start]" class="form-control datatable-input" data-col-index="8" placeholder="From Date">
                                                                        </div>
                                                                        <div class="col-lg-2"><a href="javascript:void(0);" onclick="clear_date(this, 8, data_table);">X</a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-12"> - 
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-10">
                                                                            <input type="date" data-to_date="1" name="end_Date[end]" class="form-control datatable-input"  data-col-index="8" placeholder="To Date">
                                                                        </div>
                                                                        <div class="col-lg-2"><a href="javascript:void(0);" onclick="clear_date(this, 8, data_table);">X</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>No Of Installments</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="9" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Amount</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="10" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Scheme Code</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="11" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Agent Code</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="12" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Agent Name</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="13" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Subbroker</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="14" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Scheme Name</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="15" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Pan</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="16" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Fund Code</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="19" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Product Code</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="20" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Frequency</label>
                                                                    <select class="form-control datatable-input" data-col-index="21">
                                                                        @foreach($arr_frequency as $key => $_value)
                                                                        <option value="{{$_value['key']}}">{{$_value['value']}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Transaction Type</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="22" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>To Scheme</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="23" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>To Plan</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="24" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>TerminateDate</label>
                                                                    <div class="row">
                                                                        <div class="col-lg-10">
                                                                            <input type="date" data-from_date="1" name="terminateDate[start]" class="form-control datatable-input" data-col-index="25" placeholder="From Date">
                                                                        </div>
                                                                        <div class="col-lg-2"><a href="javascript:void(0);" onclick="clear_date(this, 25, data_table);">X</a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-12"> - 
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-10">
                                                                            <input type="date" data-to_date="1" name="terminateDate[end]" class="form-control datatable-input"  data-col-index="25" placeholder="To Date">
                                                                        </div>
                                                                        <div class="col-lg-2"><a href="javascript:void(0);" onclick="clear_date(this, 25, data_table);">X</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>  
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select multiple class="form-control datatable-input" data-col-index="26">
                                                                        @foreach($arr_status as $_value)
                                                                        <option selected value="{{$_value}}">{{$_value}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <label><em><small><strong>Ctrl + Click to select multiple values</strong></small></em></label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>To ProductCode</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="27" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>To SchemeName</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="28" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Rejreason</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="29" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Umrncode</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="30" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Bankname</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="31" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Bankacno</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="32" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Banktype</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="33" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Bankifsc</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="34" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Sipday</label>
                                                                    <select class="form-control datatable-input" data-col-index="35">
                                                                        <option value="">Select</option> 
                                                                        @for($i=1 ; $i<=28 ; $i++)
                                                                        <option value="{{$i}}">{{$i}}</option>
                                                                        @endfor                            
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>BDM Name</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="36" placeholder="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-sm-3">
                                                                <div class="form-group">
                                                                    <label>Reporting Manager</label>
                                                                    <input type="text" class="form-control datatable-input" data-col-index="37" placeholder="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 col-sm-12">
                                                        <button type="button" class="btn btn-primary" id="kt_search" accesskey="s"><u>S</u>earch</button>
                                                        <button type="reset" class="btn btn-default" id="kt_reset" accesskey="r"><u>R</u>eset</button>
                                                    </div><!--/.col-lg-12-->
                                                </div>
                                            </div><!--/.faq-answer-->
                                        </div><!--/.faq-box-->
                                    </div><!--/.faq-border-top-->
                                </div><!--/.col-lg-12-->
                            </div><!--/.row mt-4-->
                            <div class="row mt-4">
                               <!--  <div class="col-lg-12 text-right">
                                    <a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data(this);"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>
                                </div> -->
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mt-2">
                                    <table id="Master_id" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                @php
                                                echo $data_table_headings_html;
                                                @endphp
                                            </tr>

                                        </thead>
                                        <tfoot>
                                            <tr>
                                                @php
                                                echo $data_table_headings_html;
                                                @endphp
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div><!--#detailed_view-->
                        @endif

                        @if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('getPredefinedSipStpReport', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE))
                        <div class="tab-pane show tab-list" id="predefined_reports" style="display:none;">
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Available Reports</label>
                                        <select name="report_type" class="form-control">
                                            <option value="Bdm_Wise_Count_Of_RegisteredSip">PutinSIP BDM Wise Inflows</option>
                                            <option value="Arn_wise_count_of_registered">PutinSIP Distributor Wise Inflows</option>
                                            <option value="Amount_Wise_Count_Of_registered">PutinSIP MFD Participation</option>
                                            <option value="elss_nfo_period_distributor_wise_inflows">ELSS NFO Period Distributor Wise Inflows</option>
                                            <option value="investor_lead_and_registration_data_two">Investor lead and Registration Data - {{ ((strtolower(date('D')) == 'mon')?date('dS M Y',strtotime('-3 day')):date('dS M Y',strtotime('-1 day'))) }} 4 PM to {{date('dS M Y')}} 10 AM</option>
                                            <option value="investor_lead_and_registration_data">Investor lead and Registration Data - {{date('dS M Y')}} 10 AM to {{date('dS M Y')}} 4 PM</option>
                                            <option value="event_analytics_nfo_scheme_road_shows">Event Analytics ELSS Road Shows</option>
                                            <option value="event_analytics_nfo_scheme_road_shows_summary">Event Analytics ELSS Road Shows Summary</option>
                                            <option value="distributor_wise_scheme_aum">Distributor Wise Scheme AUM</option>
                                             <option value="event_analytics_nfo_scheme_road_shows_summaryflex">Event Analytics Flex Road Shows Summary</option>
                                              <option value="event_analytics_nfo_scheme_road_shows_summaryovernight">Event Analytics OverNight Road Shows Summary</option>
                                        </select>
                                    </div>
                                </div><!--/.col-lg-6-->
                                <div class="col-lg-6 mt-3">
                                    <button type="button" class="btn btn-primary" onclick="export_csv_formatted_data(this,'predefined_reports');">Download</button>
                                </div><!--/.col-lg-6-->
                            </div><!--/.row mt-4-->
                        </div><!--#predefined_reports-->
                        @endif
                    </div>
                </div>
            </div><!--/.col-md-12-->
        </div><!--/.row-->
    </div><!--/.col-md-12-->
</div><!--/.row mt-4-->

@endsection

@section('custom_scripts')

<script>
    var data_table;
    $( document ).ready(function() {
        var data_table_columns = [];
        $('#Master_id thead tr:nth-child(1) th').each(function(idx) {
            var data_column = $(this).attr("data-column"),
            title = $.trim($(this).text()),
            columnDefJSON = {
                "data": data_column
            };

            data_table_columns.push(columnDefJSON);
        });
        data_table = $('#Master_id').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": true,
            "scrollX": true,
            "ajax": {
                "url": baseurl + "/MasterSipStpTransactionReport",
                "type": "POST",
                "data": function(d) {
                    d.load_datatable = 1;
                    d.columns[26]['search']['value'] = $("select.datatable-input[data-col-index='26']").val().join(',');
                },
                "complete": function() {
                    window.setTimeout(function() {
                        $.fn.dataTable.tables({
                            visible: true,
                            api: true
                        }).columns.adjust();
                    }, 1000);
                }
            },
            "columns": data_table_columns,
            "language": {
                "oPaginate": {
                    "sNext": '<i class="icons angle-right"></i>',
                    "sPrevious": '<i class="icons angle-left"></i>',
                    "sFirst": '<i class="icons step-backward"></i>',
                    "sLast": '<i class="icons step-forward"></i>'
                }
            },
            "order": [
                [1, 'asc']
            ]
        }); 
        $('#Master_id_filter').empty();

        // accordian click event
        $(".faq-question").on("click", function() {
            var faq_answer_obj = $(this).siblings(".faq-answer");
            faq_answer_obj.slideToggle();
            if($(this).parent().hasClass("active")){
                $("#accordian_filter").html('Show Filter<i class="arrow"></i>');
            }
            else{
                $("#accordian_filter").html('Hide Filter<i class="arrow"></i>');
            }
            $(this).parent().toggleClass("active");
            $(this).parent().siblings().removeClass("active");
            $(this).parent().siblings().children(".faq-answer").slideUp();
        });

        // preparing data for search
        $('#kt_search').on('click', function(){
            var params = {};

            $('.datatable-input').each(function(){
                var txtSearchedValue = $(this).val(), txtSearchedColumn = $(this).attr('data-col-index');
                var txtSearchInput = $('table.dataTable thead tr:first th').eq(txtSearchedColumn).closest('th').attr('data-column')
                switch(txtSearchInput){
                    case 'registrationDate':
                    case 'start_Date':
                    case 'end_Date':
                    case 'terminateDate':
                    txtSearchedValue = "";

                    if($.trim($("[name='"+ txtSearchInput +"[start]']").val()) != "" || $.trim($("[name='"+ txtSearchInput +"[end]']").val()) != ""){
                        txtSearchedValue = $.trim($("[name='"+ txtSearchInput +"[start]']").val()) +';'+ $.trim($("[name='"+ txtSearchInput +"[end]']").val());
                    }
                    params[txtSearchedColumn] = txtSearchedValue;
                    break;
                }
                params[txtSearchedColumn] = txtSearchedValue;
            });

            $.each(params, function(i, val){
                // apply search params to datatable
                data_table.column(i).search(val? val:'', false, false);
            });

            data_table.table().draw();
        });

        // clearing the searched values
        $('#kt_reset').on('click', function(){
            $("#status").removeAttr("disabled");
            $('.datatable-input').each(function(){
                $(this).val('');
                data_table.column($(this).attr('data-col-index')).search('', false, false);
            });
            data_table.table().draw();
        });

        $('.dataTables_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data(this);"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');

        // tab click event
        $(".new-tab li a").on("click", function(a) {
            a.preventDefault();
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
            var t = $(this).attr("href").split("#")[1];
            $(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id="' + t + '"]').fadeIn();
            $(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id!="' + t + '"]').hide();
            $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
        });
        if ($(".tab-content-item > .nav-tabs > .nav-item").length == 1) {
            if(!$(".tab-content-item > .nav-tabs > .nav-item:first").hasClass("active")){
                $(".tab-content-item > .nav-tabs > .nav-item:first").addClass("active").find("a:first").trigger("click");
           }
        }
    });

    function clear_date(inputObj, objectIndex, data_table_obj) {
        var closestDateObj = $(inputObj).parents().eq(1).find('[type="date"]');
        closestDateObj.val('');
        closestDateObj.trigger('change');
    }

    function export_csv_formatted_data(inputObj, report_type='sipstp_records') {
        var columns = [],
        formObj = $('#frm_export_data');
        if(report_type == 'sipstp_records'){
            var tableThObj = $('table.dataTable thead tr:first th');

            data_table.columns().indexes().each(function(idx){
                var txtSearchedValue = '';
                if($('.datatable-input[data-col-index='+ idx +']').length > 0){
                    var txtSearchInput = $('.datatable-input[data-col-index='+ idx +']');
                    txtSearchedValue = txtSearchInput.val();
                    txtSearchInput = $('table.dataTable thead tr:first th').eq(idx).closest('th').attr('data-column');
                    if($.inArray(txtSearchInput, ['registrationDate', 'start_Date', 'end_Date','terminateDate']) != -1){
                        txtSearchedValue = "";

                        if($.trim($("[name='"+ txtSearchInput +"[start]']").val()) != "" || $.trim($("[name='"+ txtSearchInput +"[end]']").val()) != ""){
                            txtSearchedValue = $.trim($("[name='"+ txtSearchInput +"[start]']").val()) +';'+ $.trim($("[name='"+ txtSearchInput +"[end]']").val());
                        }
                    }

                    if(txtSearchedValue != ""){
                        columns.push({'data':txtSearchInput, 'search':{'value':txtSearchedValue}});
                    }
                }
                else if(txtSearchedValue != ""){
                    columns.push({'data':$('table.dataTable thead tr:first th').eq(idx).attr('data-column'), 'search':{'value':''}});
                }
            });

            formObj.append('<input type="hidden" name="columns" value=\'' + JSON.stringify(columns) + '\'>');
            formObj.append('<input type="hidden" name="export_data" value="1">');
            formObj.append('<input type="hidden" name="load_datatable" value="1">');
            formObj.attr({
                'action': baseurl + '/MasterSipStpTransactionReport'
            });
            formObj.submit();
        }
        else if(report_type == 'predefined_reports'){
            formObj.append('<input type="hidden" name="report_type" value=\''+ $('[name="report_type"]').val() +'\'>');
            formObj.attr({
                'action': "{{url('getPredefinedSipStpReport')}}"
            });
            formObj.submit();
        }
        formObj.attr({'action': 'javascript:void(0);'});
        $('#frm_export_data input[name!="_token"]').remove();
    }
</script>

@endsection