@php
// preparing JSON values which are getting used in JAVASCRIPT for creating dropdown options. STARTS
$arr_relationship_quality = array();
if(isset($relationship_quality) && is_array($relationship_quality) && count($relationship_quality) > 0){
    array_walk($relationship_quality, function($_value, $_key, $_user_data){
        $_user_data[0][] = array('key' => $_value->label, 'value' => $_value->label. (!empty(($_value->description??''))?(' - '. ($_value->description??'')):''), 'label' => $_value->label, 'description' => $_value->description );
    }, [&$arr_relationship_quality]);
}

$arr_distributor_category_records = array();
if(isset($distributor_category_records) && is_array($distributor_category_records) && count($distributor_category_records) > 0){
    array_walk($distributor_category_records, function($_value, $_key, $_user_data){
        $_user_data[0][] = array('key' => $_value->label, 'value' => $_value->label);
    }, [&$arr_distributor_category_records]);
}

$arr_project_focus_options = array(array('key' => '', 'value' => 'Select'),
                                   array('key' => 'no', 'value' => 'No'),
                                   array('key' => 'yes', 'value' => 'Yes'));
$arr_rm_relationship_options = array(array('key' => '', 'value' => 'Select'),
                                   array('key' => 'provisional', 'value' => 'Provisional'),
                                   array('key' => 'final', 'value' => 'Final'));

$arr_yes_no_options = array(array('key' => '', 'value' => 'Select'),
                            array('key' => 0, 'value' => 'No'),
                            array('key' => 1, 'value' => 'Yes'));
// preparing JSON values which are getting used in JAVASCRIPT for creating dropdown options. ENDS

// retrieving logged in user role and permission details
if(!isset($logged_in_user_roles_and_permissions)){
    $logged_in_user_roles_and_permissions = array();
}
if(!isset($flag_have_all_permissions)){
    $flag_have_all_permissions = false;
}

// checking whether logged in user have edit permission or not
$flag_have_edit_permission = $flag_have_all_permissions?true:false;
if(!$flag_have_all_permissions && isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions'])){
    if(in_array('distributor/UpdateByArn', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE){
        $flag_have_edit_permission = true;
    }
}

// checking whether logged in user have permission to view AUM & transaction list data or not
$flag_have_aum_transaction_analytics_permission = $flag_have_all_permissions?true:false;
$flag_have_commission_structure_permission = $flag_have_all_permissions?true:false;
$flag_have_sip_analytics_permission = $flag_have_all_permissions?true:false;
$flag_have_client_analytics_permission = $flag_have_all_permissions?true:false;
$flag_have_export_aum_transaction_analytics_permission = $flag_have_all_permissions?true:false;
$flag_have_export_sip_analytics_permission = $flag_have_all_permissions?true:false;
$flag_have_export_client_analytics_permission = $flag_have_all_permissions?true:false;

if(!$flag_have_all_permissions && isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions'])){
    if(in_array('report-of-aum-transaction-analytics', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE){
        $flag_have_aum_transaction_analytics_permission = true;
    }
    if(in_array('scheme-rate-card', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE){
        $flag_have_commission_structure_permission = true;
    }
    if(in_array('report-of-sip-analytics', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE){
        $flag_have_sip_analytics_permission = true;
    }

    if(in_array('report-of-client-analytics', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE){
        $flag_have_client_analytics_permission = true;
    }

    if(in_array('export-aum-transaction-analytics', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE){
        $flag_have_export_aum_transaction_analytics_permission = true;
    }

    if(in_array('export-sip-analytics', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE){
        $flag_have_export_sip_analytics_permission = true;
    }

    if(in_array('export-client-analytics', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE){
        $flag_have_export_client_analytics_permission = true;
    }

    $flag_have_export_aum_transaction_analytics_permission = json_encode($flag_have_export_aum_transaction_analytics_permission);
    $flag_have_export_sip_analytics_permission = json_encode($flag_have_export_sip_analytics_permission);
    $flag_have_export_client_analytics_permission = json_encode($flag_have_export_client_analytics_permission);
}

// checking whether logged in user have auto assign user/bdm functionality permission or not
$flag_have_auto_assign_bdm_permission = $flag_have_all_permissions?true:false;
if(!$flag_have_all_permissions && isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions'])){
    if(in_array('distributor/auto-assign-bdm/{arn_number}', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE){
        $flag_have_auto_assign_bdm_permission = true;
    }
}
@endphp
@php
$checkmonth = '';
  $end_year = date('Y');
  $start_year = date('Y', strtotime('-5 years'));
  if($start_year < 2022){
      $start_year = 2022;
  } 
  if(!empty(session()->get('month'))){
    $checkmonth = session()->get('month');
    $monthNum  = session()->get('month');
    $dateObj   = DateTime::createFromFormat('!m', $monthNum);
    $monthName = $dateObj->format('F');
  } 

$meeting_log_heading_html = '';
if(isset($meeting_log_heading) && is_array($meeting_log_heading) && count($meeting_log_heading) > 0){
foreach($meeting_log_heading as $key => $value){
    $meeting_log_heading_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
}
unset($key, $value);
}

$aum_transactions_heading_html = '';
if(isset($aum_transactions_heading) && is_array($aum_transactions_heading) && count($aum_transactions_heading) > 0){
foreach($aum_transactions_heading as $key => $value){
    $aum_transactions_heading_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
}
unset($key, $value);
}

$sip_analytics_heading_html = '';
if(isset($sip_analytics_heading) && is_array($sip_analytics_heading) && count($sip_analytics_heading) > 0){
foreach($sip_analytics_heading as $key => $value){
    $sip_analytics_heading_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
}
unset($key, $value);
}

$client_analytics_heading_html = '';
if(isset($client_analytics_heading) && is_array($client_analytics_heading) && count($client_analytics_heading) > 0){
foreach($client_analytics_heading as $key => $value){
    $client_analytics_heading_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
}
unset($key, $value);
}

$client_monthwise_analytics_heading_html = '';
if(isset($client_monthwise_analytics_heading) && is_array($client_monthwise_analytics_heading) && count($client_monthwise_analytics_heading) > 0){
foreach($client_monthwise_analytics_heading as $key => $value){
    $client_monthwise_analytics_heading_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
}
unset($key, $value);
}

$transaction_type_json = json_encode($transaction_type);
@endphp

@extends('../layout')
@section('title', 'View Distributor')
@section('breadcrumb_heading', 'Distributor Master >> View Distributor')

@section('custom_head_tags')

    <link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
    <style type="text/css">
        a.anchor_uploaded_img_file img{
            border: 0px;
        }
        img.uploaded_img_file{
            width:100px;
        }
        .min-w-250{min-width:200px;}
    </style>
@endsection

@section('content')
@php
//x($commission_data);
@endphp
<div class="row mt-4">
    <div class="col-md-12">
        @if($flag_record_found)
        <div class="tab-content-item">
            <ul class="nav nav-tabs new-tab mt-0" id="myTab" role="tablist">
                <li class="nav-item active">
                    <a class="nav-link" href="#amfi">AMFI Details</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" href="#rankmf">RankMf Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#samcomf">SamcoMf Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#aum">AUM Data</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#principal">Principal Decision Maker</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#amcwise">AMC wise Project focus Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#meetinglog">Meeting Log</a>
                </li> --}}

                @if($flag_have_commission_structure_permission)
                    <li class="nav-item">
                        <a class="nav-link" href="#commission" id="commission_data_load">Commission Structure</a>
                    </li>
                @endif
                
                @if($flag_have_aum_transaction_analytics_permission)
                <li class="nav-item">
                    <a class="nav-link" href="#aum_transaction_analytics">AUM & Transaction Analytics</a>
                </li>
                @endif
                
				@if($flag_have_sip_analytics_permission)
					<li class="nav-item">
						<a class="nav-link" href="#sip_analytics">SIP Analytics</a>
					</li>
                @endif 

				@if($flag_have_client_analytics_permission)
                <li class="nav-item">
                    <a class="nav-link" href="#client_analytics">Client Analytics</a>
                </li>
                @endif
			
            </ul>
            <div class="tab-content  data-tabs">
                <div class="tab-pane show active tab-list" id="amfi" style="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-top-align no-wrap">
                                    <tbody>
                                        <tr>
                                            <th width="25%">AMFI- ARN</th>
                                            <td>{{$partner_data->ARN}}</td>
                                        </tr>
                                        <tr>
                                            <th>AMFI ARN Holder's Name</th>
                                            <td>{{$partner_data->arn_holders_name}}</td>
                                        </tr>
                                        <tr>
                                            <th>AMFI Address</th>
                                            <td>{{$partner_data->arn_address}}</td>
                                        </tr>
                                        <tr>
                                            <th>AMFI Pin</th>
                                            <td>{{$partner_data->arn_pincode}}</td>
                                        </tr>
                                        <tr>
                                            <th>AMFI Email</th>
                                            <td>{{$partner_data->arn_email}}</td>
                                        </tr>
                                        <tr>
                                            <th>AMFI City</th>
                                            <td>{{$partner_data->arn_city}}</td>
                                        </tr>
                                        <!-- tr>
                                            <th>AMFI State</th>
                                            <td>{{$partner_data->arn_state}}</td>
                                        </tr>
                                        <tr>
                                            <th>Zone</th>
                                            <td>{{$partner_data->arn_zone}}</td>
                                        </tr -->
                                        <tr>
                                            <th>AMFI Telephone(R)</th>
                                            <td>{{$partner_data->arn_telephone_r}}</td>
                                        </tr>
                                        <tr>
                                            <th>AMFI Telephone(O)</th>
                                            <td>{{$partner_data->arn_telephone_o}}</td>
                                        </tr>
                                        <tr>
                                            <th>AMFI- ARN Valid From</th>
                                            <td>{{show_date_in_display_format('d/m/Y', $partner_data->arn_valid_from)}}</td>
                                        </tr>
                                        <tr>
                                            <th>AMFI- ARN Valid Till</th>
                                            <td>{{show_date_in_display_format('d/m/Y', $partner_data->arn_valid_till)}}</td>
                                        </tr>
                                        <tr>
                                            <th>AMFI- KYD Compliant</th>
                                            <td>{{$partner_data->arn_kyd_compliant}}</td>
                                        </tr>
                                        <tr>
                                            <th>AMFI- EUIN</th>
                                            <td>{{$partner_data->arn_euin}}</td>
                                        </tr>
                                        <!-- tr>
                                            <th>
                                                <span class="display-flex"> Quality of Relationship with ARN 
                                                    @if(isset($arr_relationship_quality) && is_array($arr_relationship_quality) && count($arr_relationship_quality) > 0)
                                                    <a class="ml-1" href="#" data-toggle="modal" data-target="#view_quality_relationship_arn_modal" title='View Quality Relationship ARN'><i class='icons information-icon' title='View Quality Relationship ARN'></i></a>
                                                    @endif
                                                </span>
                                            </th>
                                            <td>
                                                <span>{{ucfirst($partner_data->relationship_quality_with_arn)}}</span>
                                                <select class="form-control mb-1" id="relationship_quality_with_arn" style="display:none;">
                                                    <option value="">Select</option>
                                                    @foreach($arr_relationship_quality as $relationship)
                                                    <option value="{{$relationship['key']}}" {{(!empty($relationship['key']) && ($relationship['key'] == $partner_data->relationship_quality_with_arn))?'selected':''}}>{{$relationship['value']}}</option>
                                                    @endforeach
                                                </select>
                                                <button id="submit_relationship_quality_with_arn" type="button" class="btn btn-outline-primary" style="display:none;" onclick="submit_field_data('relationship_quality_with_arn', 'relationship quality with ARN');">Submit</button>
                                                <button id="cancel_relationship_quality_with_arn" onclick="cancel_field_edit('relationship_quality_with_arn')" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:edit_field_data('relationship_quality_with_arn');" id="edit_relationship_quality_with_arn" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr -->
                                        <tr>
                                            <th>Distributor Category</th>
                                            <td>
                                                <input type="hidden" id="arn_number" value="{{$partner_data->ARN}}" />
                                                <span>{{$partner_data->distributor_category}}</span>
                                                <select class="form-control" id="dcategory" name="dcategory" style="display:none;">
                                                    <option value="">Select</option>
                                                    @foreach($arr_distributor_category_records as $category)
                                                    <option value="{{$category['value']}}" {{(!empty($partner_data->distributor_category) && ($partner_data->distributor_category == $category['value']))?'selected':''}}>{{$category['value']}}</option>
                                                    @endforeach
                                                </select>
                                                <button id="submit" type="button" class="btn btn-outline-primary" style="display:none;">Submit</button>
                                                <button id="cancelDcategory" onclick="cancelDcategory()" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:editFunc();" id="editbutton" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <!-- tr>
                                            <th>Project Focus</th>
                                            <td>
                                                <span class="display-flex">
                                                <span><span>{{ucfirst($partner_data->project_focus)}}</span>
                                                <select class="form-control mb-1 min-w-250" id="pfocus" style="display:none;">
                                                    @foreach($arr_project_focus_options as $value)
                                                    <option value="{{$value['key']}}" {{(!empty($partner_data->project_focus) && ($partner_data->project_focus == $value['key']))?'selected':''}}>{{$value['value']}}</option>
                                                    @endforeach
                                                </select>
                                                <button id="psubmit" type="button" class="btn btn-outline-primary" style="display:none;">Submit</button>
                                                <button id="cancelPfocus" onclick="cancelPfocus()" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                </span>  
                                                <span>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:editFocusFunc();" id="editFocusbutton" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                </span>                                                
                                                @endif
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Project Emerging Stars</th>
                                            <td>
                                                <span>{{ucfirst($partner_data->project_emerging_stars)}}</span>
                                                <select class="form-control" id="project_emerging_stars" style="display:none;">
                                                    @foreach($arr_project_focus_options as $value)
                                                    <option value="{{$value['key']}}" {{(!empty($partner_data->project_emerging_stars) && ($partner_data->project_emerging_stars == $value['key']))?'selected':''}}>{{$value['value']}}</option>
                                                    @endforeach
                                                </select>
                                                <button id="submit_project_emerging_stars" type="button" class="btn btn-outline-primary" style="display:none;" onclick="submit_field_data('project_emerging_stars', 'project emerging stars')">Submit</button>
                                                <button id="cancel_project_emerging_stars" onclick="cancel_field_edit('project_emerging_stars')" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:edit_field_data('project_emerging_stars');" id="edit_project_emerging_stars" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Project Green Shoots</th>
                                            <td>
                                                <span>{{ucfirst($partner_data->project_green_shoots)}}</span>
                                                <select class="form-control" id="project_green_shoots" style="display:none;">
                                                    @foreach($arr_project_focus_options as $value)
                                                    <option value="{{$value['key']}}" {{(!empty($partner_data->project_green_shoots) && ($partner_data->project_green_shoots == $value['key']))?'selected':''}}>{{$value['value']}}</option>
                                                    @endforeach
                                                </select>
                                                <button id="submit_project_green_shoots" type="button" class="btn btn-outline-primary" style="display:none;" onclick="submit_field_data('project_green_shoots', 'project green shoots')">Submit</button>
                                                <button id="cancel_project_green_shoots" onclick="cancel_field_edit('project_green_shoots')" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:edit_field_data('project_green_shoots');" id="edit_project_green_shoots" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr-->
                                        <tr>
                                            <th>Direct Relationship Assigned to</th>
                                            <td>
                                                <span>{{$partner_data->bdm_name}}</span>
                                                @if(strtolower($partner_data->rm_relationship) != 'final')
                                                <select class="form-control" id="direct_relationship_user_id" style="display:none;">
                                                    <option value="">Select</option>
                                                    @foreach($list_of_users as $role => $users)
                                                        <optgroup label="{{$role}}">
                                                            @foreach($users as $record)
                                                            <option value="{{$record['key']}}" data-reporting_to_name="{{$record['reporting_to_name']}}">{{$record['value']}}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                                <button id="submit_direct_relationship_user_id" type="button" class="btn btn-outline-primary" style="display:none;" onclick="submit_field_data('direct_relationship_user_id', 'BDM');">Submit</button>
                                                <button id="cancel_direct_relationship_user_id" onclick="cancel_field_edit('direct_relationship_user_id')" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission && $flag_have_auto_assign_bdm_permission)
                                                <a href="javascript:edit_field_data('direct_relationship_user_id');" id="edit_direct_relationship_user_id" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                                @if($flag_have_auto_assign_bdm_permission && empty($partner_data->bdm_name))
                                                <a href="javascript:auto_assign_bdm_to_arn();" title="Auto assign BDM" style="vertical-align:top;">Auto assign BDM</a>
                                                @endif
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Reporting Manager of Direct Assignee</th>
                                            <td>
                                                <span>{{$partner_data->reporting_to_name}}</span>
                                                <input type="hidden" id="reporting_manager_of_direct_assignee">
                                            </td>
                                        </tr>
                                        <!-- tr>
                                            <th>RM Relationship Flag</th>
                                            <td>
                                                <span>{{ucfirst($partner_data->rm_relationship)}}</span>
                                                @if(strtolower($partner_data->rm_relationship) != 'final')
                                                <select class="form-control" id="rm_relationship" style="display:none;">
                                                    @foreach($arr_rm_relationship_options as $value)
                                                    <option value="{{$value['key']}}" {{(!empty($partner_data->rm_relationship) && ($partner_data->rm_relationship == $value['key']))?'selected':''}}>{{$value['value']}}</option>
                                                    @endforeach
                                                </select>
                                                <button id="submit_rm_relationship" type="button" class="btn btn-outline-primary" style="display:none;" onclick="submit_field_data('rm_relationship', 'RM relationship');">Submit</button>
                                                <button id="cancel_rm_relationship" onclick="cancel_field_edit('rm_relationship')" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:edit_field_data('rm_relationship');" id="edit_rm_relationship" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Front Visiting Card Image</th>
                                            <td>
                                                <span>
                                                    @if(!empty($partner_data->front_visiting_card_image) && file_exists(storage_path() .'/'. $arn_visiting_card_images_folder . $partner_data->front_visiting_card_image))
                                                    <a class="anchor_uploaded_img_file" href="{{asset(get_storage_folder_url($arn_visiting_card_images_folder) . $partner_data->front_visiting_card_image)}}" target="_blank"><img class="uploaded_img_file" src="{{asset('images/loader-small.gif')}}" data-src="{{asset(get_storage_folder_url($arn_visiting_card_images_folder) . $partner_data->front_visiting_card_image)}}"></a>
                                                    @endif
                                                </span>
                                                <input type="file" class="form-control" id="front_visiting_card_image" name="front_visiting_card_image" style="display:none;"/>
                                                <button id="submit_front_visiting_card_image" type="button" class="btn btn-outline-primary" style="display:none;" onclick="submit_field_data('front_visiting_card_image', 'front visiting card image');">Submit</button>
                                                <button id="cancel_front_visiting_card_image" onclick="cancel_field_edit('front_visiting_card_image')" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:edit_field_data('front_visiting_card_image');" id="edit_front_visiting_card_image" title="Edit Field" style="vertical-align:top;"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Back Visiting Card Image</th>
                                            <td>
                                                <span>
                                                    @if(!empty($partner_data->back_visiting_card_image) && file_exists(storage_path() .'/'. $arn_visiting_card_images_folder . $partner_data->back_visiting_card_image))
                                                    <a class="anchor_uploaded_img_file" href="{{asset(get_storage_folder_url($arn_visiting_card_images_folder) . $partner_data->back_visiting_card_image)}}" target="_blank"><img class="uploaded_img_file" src="{{asset('images/loader-small.gif')}}" data-src="{{asset(get_storage_folder_url($arn_visiting_card_images_folder) . $partner_data->back_visiting_card_image)}}"></a>
                                                    @endif
                                                </span>
                                                <input type="file" class="form-control" id="back_visiting_card_image" name="back_visiting_card_image" style="display:none;"/>
                                                <button id="submit_back_visiting_card_image" type="button" class="btn btn-outline-primary" style="display:none;" onclick="submit_field_data('back_visiting_card_image', 'front visiting card image');">Submit</button>
                                                <button id="cancel_back_visiting_card_image" onclick="cancel_field_edit('back_visiting_card_image')" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:edit_field_data('back_visiting_card_image');" id="edit_front_visiting_card_image" title="Edit Field" style="vertical-align:top;"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr -->
                                            <td colspan="2">
                                                <table class="table table-striped table-top-align no-wrap">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="4">Alternate contact details</th>
                                                        </tr>
                                                        <tr>
                                                            <th width="10%">Sr. No.</th>
                                                            <th width="30%">Name</th>
                                                            <th width="30%">Mobile</th>
                                                            <th width="30%">Email</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @for($cntr = 1; $cntr <= 5; $cntr++)
                                                            @php
                                                                $alternate_name = 'alternate_name_'. $cntr;
                                                                $alternate_mobile = 'alternate_mobile_'. $cntr;
                                                                $alternate_email = 'alternate_email_'. $cntr;
                                                            @endphp
                                                        <tr>
                                                            <td>{{$cntr}}</td>
                                                            <td>
                                                                <div class="row" style="width:100%">
                                                                    <div class="col-md-9">
                                                                        <span>{{$partner_data->$alternate_name}}</span>
                                                                        <input type="text" class="form-control" id="{{$alternate_name}}" name="{{$alternate_name}}" style="display:none;" value="{{$partner_data->$alternate_name}}" maxlength="255" />
                                                                        <button id="submit_{{$alternate_name}}" type="button" class="btn btn-outline-primary" style="display:none;" onclick="submit_field_data('{{$alternate_name}}', '{{str_replace('_', ' ', $alternate_name)}}');">Submit</button>
                                                                        <button id="cancel_{{$alternate_name}}" onclick="cancel_field_edit('{{$alternate_name}}')" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        @if($flag_have_edit_permission)
                                                                        <a href="javascript:edit_field_data('{{$alternate_name}}');" id="edit_{{$alternate_name}}" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="row" style="width:100%;">
                                                                    <div class="col-md-9">
                                                                        <span>{{$partner_data->$alternate_mobile}}</span>
                                                                        <input type="text" class="form-control" id="{{$alternate_mobile}}" name="{{$alternate_mobile}}" style="display:none;" value="{{$partner_data->$alternate_mobile}}" maxlength="10" />
                                                                        <button id="submit_{{$alternate_mobile}}" type="button" class="btn btn-outline-primary" style="display:none;" onclick="submit_field_data('{{$alternate_mobile}}', '{{str_replace('_', ' ', $alternate_mobile)}}');">Submit</button>
                                                                        <button id="cancel_{{$alternate_mobile}}" onclick="cancel_field_edit('{{$alternate_mobile}}')" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        @if($flag_have_edit_permission)
                                                                        <a href="javascript:edit_field_data('{{$alternate_mobile}}');" id="edit_{{$alternate_mobile}}" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="row" style="width: 100%;">
                                                                    <div class="col-md-9">
                                                                        <span>{{$partner_data->$alternate_email}}</span>
                                                                        <input type="text" class="form-control" id="{{$alternate_email}}" name="{{$alternate_email}}" style="display:none;" value="{{$partner_data->$alternate_email}}" maxlength="255" />
                                                                        <button id="submit_{{$alternate_email}}" type="button" class="btn btn-outline-primary" style="display:none;" onclick="submit_field_data('{{$alternate_email}}', '{{str_replace('_', ' ', $alternate_email)}}');">Submit</button>
                                                                        <button id="cancel_{{$alternate_email}}" onclick="cancel_field_edit('{{$alternate_email}}')" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        @if($flag_have_edit_permission)
                                                                        <a href="javascript:edit_field_data('{{$alternate_email}}');" id="edit_{{$alternate_email}}" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endfor
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        @php 
										/*
                                        if(!$linked_arn->isEmpty()){
                                        @endphp
                                        <tr>
                                            <td colspan="2">
                                                <table class="table table-striped table-top-align no-wrap">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="4">Linked ARN</th>
                                                        </tr>
                                                        <tr>
                                                            <th width="30%">ARN</th>
                                                            <th width="40%">Name</th>
                                                            <th width="30%">View</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($linked_arn as $key => $value)
                                                        <tr>
                                                            <td>
                                                                {{$value->ARN}}
                                                            </td>
                                                            <td>
                                                                {{$value->arn_holders_name}}
                                                            </td>
                                                            <td>
                                                                <a target="_blank" href="{{ url('/distributor/'.$value->ARN) }}" title="View Record"><i class="icons view-icon" title="View Record" alt="View Record"></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        @php
                                        }
										*/
                                        @endphp
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane tab-list" id="rankmf" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-top-align no-wrap">
                                    <tbody>
                                        <tr>
                                            <th width="25%">Empanelled Distributor</th>
                                            <td>
                                            <span>{{!empty($partner_data->is_rankmf_partner) ? 'Yes':'No'}}</span>
                                                <select class="form-control" id="rankmf_p" style="display:none;">
                                                    @foreach($arr_yes_no_options as $value)
                                                    <option value="{{$value['key']}}" {{(is_numeric($partner_data->is_rankmf_partner) && ($partner_data->is_rankmf_partner == $value['key']))?'selected':''}}>{{$value['value']}}</option>
                                                    @endforeach
                                                </select>
                                                <button id="ranksubmit" type="button" class="btn btn-outline-primary" style="display:none;">Submit</button>
                                                <button id="cancelRankMfPartner" onclick="cancelRankMfPartner()" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <!--a href="javascript:editRankFunc();" id="editRankbutton" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a-->
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Partner Code</th>
                                            <td>{{$partner_data->rankmf_partner_code}}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{$partner_data->rankmf_email}}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile</th>
                                            <td>{{$partner_data->rankmf_mobile}}</td>
                                        </tr>
                                        <tr>
                                            <th>Active Partner</th>
                                            <td>
                                            <span>{{!empty($partner_data->is_partner_active_on_rankmf) ? 'Yes':'No'}}</span>
                                                <select class="form-control" id="rankmfa_p" style="display:none;">
                                                    @foreach($arr_yes_no_options as $value)
                                                    <option value="{{$value['key']}}" {{(is_numeric($partner_data->is_partner_active_on_rankmf) && ($partner_data->is_partner_active_on_rankmf == $value['key']))?'selected':''}}>{{$value['value']}}</option>
                                                    @endforeach
                                                </select>
                                                <button id="rankasubmit" type="button" class="btn btn-outline-primary" style="display:none;">Submit</button>
                                                <button id="cancelRankMfAPartner" onclick="cancelRankMfAPartner()" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <!--a href="javascript:editRankAFunc();" id="editRankAbutton" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a-->
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Partner AUM</th>
                                            <td>{{$partner_data->rankmf_partner_aum}}</td>
                                        </tr>
                                        <tr>
                                            <th>Stage of Relationship</th>
                                            <td>{{$partner_data->rankmf_partner_relationship_stage}}</td>
                                        </tr>
                                        <tr>
                                            <th>Stage of prospect </th>
                                            <td>{{$partner_data->rankmf_stage_of_prospect}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane tab-list" id="samcomf" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-top-align no-wrap">
                                    <tbody>
                                        <tr>
                                            <th width="25%">Empanelled Distributor</th>
                                            <td>
                                            <span>{{!empty($partner_data->is_samcomf_partner) ? 'Yes':'No'}}</span>
                                                <select class="form-control" id="samco_p" style="display:none;">
                                                    @foreach($arr_yes_no_options as $value)
                                                    <option value="{{$value['key']}}" {{(is_numeric($partner_data->is_samcomf_partner) && ($partner_data->is_samcomf_partner == $value['key']))?'selected':''}}>{{$value['value']}}</option>
                                                    @endforeach
                                                </select>
                                                <button id="samcosubmit" type="button" class="btn btn-outline-primary" style="display:none;">Submit</button>
                                                <button id="cancelSamcoMfPartner" onclick="cancelSamcoMfPartner()" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <!--a href="javascript:editSamcoFunc();" id="editSamcobutton" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a-->
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Partner Code</th>
                                            <td>{{$partner_data->samcomf_partner_code}}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{$partner_data->samcomf_email}}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile</th>
                                            <td>{{$partner_data->samcomf_mobile}}</td>
                                        </tr>
                                        <tr>
                                            <th>Active Partner</th>
                                            <td>
                                            <span>{{!empty($partner_data->is_partner_active_on_samcomf) ? 'Yes':'No'}}</span>
                                                <select class="form-control" id="samcoa_p" style="display:none;">
                                                    @foreach($arr_yes_no_options as $value)
                                                    <option value="{{$value['key']}}" {{(is_numeric($partner_data->is_partner_active_on_samcomf) && ($partner_data->is_partner_active_on_samcomf == $value['key']))?'selected':''}}>{{$value['value']}}</option>
                                                    @endforeach
                                                </select>
                                                <button id="samcoasubmit" type="button" class="btn btn-outline-primary" style="display:none;">Submit</button>
                                                <button id="cancelSamcoMfAPartner" onclick="cancelSamcoMfAPartner()" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <!--a href="javascript:editSamcoAFunc();" id="editSamcoAbutton" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a-->
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Partner AUM</th>
                                            <td>{{$partner_data->samcomf_partner_aum}}</td>
                                        </tr>
                                        <tr>
                                            <th>Stage of RankMf Relationship(Where RankMf empanelled Distributor is No)</th>
                                            <td>{{$partner_data->samcomf_relationship_stage}}</td>
                                        </tr>
                                        <tr>
                                            <th>Stage of prospect </th>
                                            <td>{{$partner_data->samcomf_stage_of_prospect}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane tab-list" id="aum" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-top-align no-wrap">
                                    <tbody>
                                        <tr>
                                            <th width="25%">ARN Average AUM - Last Reported<br>(In Lakhs)</th>
                                            <td>{{$partner_data->arn_avg_aum}}</td>
                                        </tr>
                                        <tr>
                                            <th>ARN Total Commision</th>
                                            <td>{{$partner_data->arn_total_commission}}</td>
                                        </tr>
                                        <tr>
                                            <th>ARN Yield </th>
                                            <td>{{$partner_data->arn_yield}}</td>
                                        </tr>
                                        <tr>
                                            <th>ARN Business Focus Type</th>
                                            <td>{{$partner_data->arn_business_focus_type}}</td>
                                        </tr>
                                        <tr>
                                            <th>Total AUM with SAMCO MF AND RANKMF</th>
                                            <td>{{$partner_data->total_aum}}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Hybrid & EQUITY AUM with SAMCO MF AND RANKMF</th>
                                            <td>{{$partner_data->total_equity_and_hybrid_aum}}</td>
                                        </tr>
                                        <tr>
                                            <th>Marked Share % of Hybrid & EQUITY AUM with SAMCO MF AND RANKMF</th>
                                            <td>{{$partner_data->percent_market_share_of_equity_and_hybrid_aum}}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Industry AUM<br>(In Crores)</th>
                                            <td>{{$partner_data->total_ind_aum}}</td>
                                        </tr>
                                        <tr>
                                            <th>Total industry aum as on date</th>
                                            <td>{{show_date_in_display_format('d/m/Y', $partner_data->ind_aum_as_on_date)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane tab-list" id="principal" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-top-align no-wrap">
                                    <tbody>
                                        <tr>
                                            <th width="25%">ARN For Product Approval- Name </th>
                                            <td>
                                                <span>{{$partner_data->product_approval_person_name}}</span>
                                                <input type="text" class="form-control" id="papprovename" name="papprovename" value="{{$partner_data->product_approval_person_name}}" style="display:none;"/>
                                                <button id="pnamesubmit" type="button" class="btn btn-outline-primary" style="display:none;">Submit</button>
                                                <button id="cancelPName" onclick="cancelPName()" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:editPNameFunc();" id="editPNamebutton" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>ARN For Product Approval- Mobile number</th>
                                            <td>
                                                <span>{{$partner_data->product_approval_person_mobile}}</span>
                                                <input type="text" class="form-control" id="papprovemobile" name="papprovemobile" value="{{$partner_data->product_approval_person_mobile}}" style="display:none;"/>
                                                <button id="pmobilesubmit" type="button" class="btn btn-outline-primary" style="display:none;">Submit</button>
                                                <button id="cancelPMobile" onclick="cancelPMobile()" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:editPMobileFunc();" id="editPMobilebutton" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>ARN For Product Approval- Email </th>
                                            <td>
                                                <span>{{$partner_data->product_approval_person_email}}</span>
                                                <input type="text" class="form-control" id="papproveemail" name="papproveemail" value="{{$partner_data->product_approval_person_email}}" style="display:none;"/>
                                                <button id="pemailsubmit" type="button" class="btn btn-outline-primary" style="display:none;">Submit</button>
                                                <button id="cancelPEmail" onclick="cancelPEmail()" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:editPEmailFunc();" id="editPEmailbutton" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>ARN For Sales Drive- Name </th>
                                            <td>
                                                <span>{{$partner_data->sales_drive_person_name}}</span>
                                                <input type="text" class="form-control" id="salesdriveename" name="salesdriveename" value="{{$partner_data->sales_drive_person_name}}" style="display:none;"/>
                                                <button id="salesdrivesubmit" type="button" class="btn btn-outline-primary" style="display:none;">Submit</button>
                                                <button id="cancelSalesName" onclick="cancelSalesName()" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:editSalesNameFunc();" id="editSalesNamebutton" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>ARN For Sales Drive- Mobile number</th>
                                            <td>
                                            <span>{{$partner_data->sales_drive_person_mobile}}</span>
                                                <input type="text" class="form-control" id="salesdrivemobile" name="salesdrivemobile" value="{{$partner_data->sales_drive_person_mobile}}" style="display:none;"/>
                                                <button id="salesdrivemobilesubmit" type="button" class="btn btn-outline-primary" style="display:none;">Submit</button>
                                                <button id="cancelSalesMobile" onclick="cancelSalesMobile()" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:editSalesMobileFunc();" id="editSalesMobilebutton" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>ARN For Sales Drive- Email </th>
                                            <td>
                                                <span>{{$partner_data->sales_drive_person_email}}</span>
                                                <input type="text" class="form-control" id="salesdriveemail" name="salesdriveemail" value="{{$partner_data->sales_drive_person_email}}" style="display:none;"/>
                                                <button id="salesdriveemailsubmit" type="button" class="btn btn-outline-primary" style="display:none;">Submit</button>
                                                <button id="cancelSalesEmail" onclick="cancelSalesEmail()" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:editSalesEmailFunc();" id="editSalesEmailbutton" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Quality of Relationship with Product Decision Maker</th>
                                            <td>
                                                <span>{{ucfirst($partner_data->relationship_quality_with_product_approver)}}</span>
                                                <select class="form-control" id="relationship_quality_with_product_approver" style="display:none;">
                                                    <option value="">Select</option>
                                                    @foreach($arr_relationship_quality as $relationship)
                                                    <option value="{{$relationship['key']}}" {{(!empty($relationship['key']) && ($relationship['key'] == $partner_data->relationship_quality_with_product_approver))?'selected':''}}>{{$relationship['value']}}</option>
                                                    @endforeach
                                                </select>
                                                <button id="submit_relationship_quality_with_product_approver" type="button" class="btn btn-outline-primary" style="display:none;" onclick="submit_field_data('relationship_quality_with_product_approver', 'relationship quality with product approver');">Submit</button>
                                                <button id="cancel_relationship_quality_with_product_approver" onclick="cancel_field_edit('relationship_quality_with_product_approver')" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:edit_field_data('relationship_quality_with_product_approver');" id="edit_relationship_quality_with_product_approver" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Quality of Relationship with Sales Decision Maker </th>
                                            <td>
                                                <span>{{ucfirst($partner_data->relationship_quality_with_sales_person)}}</span>
                                                <select class="form-control" id="relationship_quality_with_sales_person" style="display:none;">
                                                    <option value="">Select</option>
                                                    @foreach($arr_relationship_quality as $relationship)
                                                    <option value="{{$relationship['key']}}" {{(!empty($relationship['key']) && ($relationship['key'] == $partner_data->relationship_quality_with_sales_person))?'selected':''}}>{{$relationship['value']}}</option>
                                                    @endforeach
                                                </select>
                                                <button id="submit_relationship_quality_with_sales_person" type="button" class="btn btn-outline-primary" style="display:none;" onclick="submit_field_data('relationship_quality_with_sales_person', 'relationship quality with sales person');">Submit</button>
                                                <button id="cancel_relationship_quality_with_sales_person" onclick="cancel_field_edit('relationship_quality_with_sales_person')" type="button" class="btn btn-outline-primary" style="display:none;">Cancel</button>
                                                @if($flag_have_edit_permission)
                                                <a href="javascript:edit_field_data('relationship_quality_with_sales_person');" id="edit_relationship_quality_with_sales_person" title="Edit Field"><i class="icons edit-icon" title="Edit Record" alt="Edit Record"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane tab-list" id="amcwise" style="display: none;">
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="mt-2 table-responsive">
                                <a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data_amc('{{$partner_data->ARN}}');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>
                                <table id="panel_arn_amc_wise_list" class="display" style="width:100%;">
                                    <thead>
                                        <tr>
                                           <th>AMC Name</th>
                                           <th>Total Commision</th>
                                           <th>Gross Inflows</th>
                                           <th>Net Inflows</th>
                                           <th>Average AUM </th>
                                           <th>Closing AUM </th>
                                           <th>Effective Yield</th>
                                           <th>Nature of AUM</th>
                                           <th>Reported Year</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @foreach ($amc_data as $amc)
                                            <tr>
                                                <td>{{ $amc->amc_name }}</td>
                                                <td>{{ $amc->total_commission_expenses_paid }}</td>
                                                <td>{{ $amc->gross_inflows }}</td>
                                                <td>{{ $amc->net_inflows }}</td>
                                                <td>{{ $amc->avg_aum_for_last_reported_year }}</td>
                                                <td>{{ $amc->closing_aum_for_last_financial_year }}</td>
                                                <td>{{ $amc->effective_yield }}</td>
                                                <td>{{ $amc->nature_of_aum }}</td>
                                                <td>{{ $amc->reported_year }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane tab-list" id="meetinglog" style="display: none;">
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="mt-2 table-responsive">
                                <table id="panel_meetinglog" class="display" style="width:100%;" data-arn_number="{{$arn_number??''}}">
                                    <thead>
                                    <tr>
                                    @php
                                    echo $meeting_log_heading_html;
                                    @endphp
                                    </tr>
                                    <tr>
                                    @php
                                    echo $meeting_log_heading_html;
                                    @endphp
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                    @php
                                    echo $meeting_log_heading_html;
                                    @endphp
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane tab-list" id="commission" style="display: none;">
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="mt-2" id="scheme_rate_card_table">
                            
                            @php
                    if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('/commission_exportToCSV', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE)){
                        @endphp 
                                <a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data_commission('{{$partner_data->ARN}}');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>
                                    @php
                                        }

                                    @endphp
                                    @php
                                            if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('edit-commission-detail', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE)){
                                                @endphp 
                                                @if(isset($commission_data[0]) && !empty($commission_data[0]))
                                                <a href="javascript:void(0);" title="Edit Record"><i class="btn btn-primary" title="Edit Record" alt="Edit Record" onclick="edit_code('{{$commission_data[0]->partner_arn}}')">Change Category</i></a>
                                                @endif
                                                @php
                                                        }
                                                    @endphp
                                <!-- Filter section -->
                                <div class="m-subheader-search m-portlet tab-filter mb-1">
                                    <div id="progressbar" class="mb-3"></div>
                                    <div class="tab-content-item">
                                        <ul class="nav nav-tabs new-tab mt-0" id="myTab2" role="tablist">
                                            <li class="nav-item active scheme_tab">
                                                <a class="nav-link" style="cursor: pointer;" value="scheme">Scheme Rate Card</a>
                                            </li>
                                            <li class="nav-item scheme_tab">
                                                <a class="nav-link" style="cursor: pointer;" value="nfo">NFO Scheme Rate Card</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <input type="hidden" id="scheme_select" value="scheme">
                                    <h2 class="m-subheader-search__title">
                                        Filter Now
                                    </h2>
						            <div id="sourceList">
							            <div class="m-form">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="m-input-icon m-input-icon--fixed m-input-icon--fixed-large m-input-icon--right">
                                                        <label>Month</label>
                                                        <select name="nfromDate" id="nfromDate" class="form-control form-control-lg m-input m-input--pill">
                                                            <option value="">Month</option>
                                                            <option value="1"  <?php echo (date("m") ==  1) ? "selected" : "" ?>>Jan</option>
                                                            <option value="2"  <?php echo (date("m") ==  2) ? "selected" : "" ?>>Feb</option>
                                                            <option value="3"  <?php echo (date("m") ==  3) ? "selected" : "" ?>>Mar</option>
                                                            <option value="4"  <?php echo (date("m") ==  4) ? "selected" : "" ?>>Apr</option>
                                                            <option value="5"  <?php echo (date("m") ==  5) ? "selected" : "" ?>>May</option>
                                                            <option value="6"  <?php echo (date("m") ==  6) ? "selected" : "" ?>>Jun</option>
                                                            <option value="7"  <?php echo (date("m") ==  7) ? "selected" : "" ?>>Jul</option>
                                                            <option value="8"  <?php echo (date("m") ==  8) ? "selected" : "" ?>>Aug</option>
                                                            <option value="9"  <?php echo (date("m") ==  9) ? "selected" : "" ?>>Sept</option>
                                                            <option value="10" <?php echo (date("m") == 10) ? "selected" : "" ?>>Oct</option>
                                                            <option value="11" <?php echo (date("m") == 11) ? "selected" : "" ?>>Nov</option>
                                                            <option value="12" <?php echo (date("m") == 12) ? "selected" : "" ?>>Dec</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="m-input-icon m-input-icon--fixed m-input-icon--fixed-large m-input-icon--right mar-5">
                                                        <label>Year</label>
                                                        <select name="ntoDate" id="ntoDate" class="form-control form-control-lg m-input m-input--pill">
                                                            <option value="">Year</option>
                                                            <?php for($i=2017;$i<=date('Y');$i++) { 
                                                                $yerselected='';
                                                                if($i==date('Y')){
                                                                        $yerselected='selected';
                                                                }?>
                                                                <option value="<?php echo $i ?>" <?php echo $yerselected ?>><?php echo $i ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" onclick="nfilterByDate()" class="btn btn-primary" style="margin-top:24px;margin-left:10px; border-radius:25px;"> 
                                                        Search 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
					                </div>
				                </div>
                                <!-- Filter section -->
                                <table id="panel_commission" class="display" style="width:100%;">
                                    <thead>
                                        <tr>
                                        <th width="40%">Scheme Name</th>
                                        <th width="20%">Month</th>
                                        <th width="20%">Year</th>
                                        <th width="20%"> 1st Year Trail(p.a.)</th>
                                        <th width="20%">2nd Year Trail(p.a.)</th>
                                        <th width="20%">Additionl Trail for B30# <br>(1st Year Only) </th>
                                        <th width="20%">Special Addition for first year trail</th>
                                        <th width="20%">Special Addition for first year trail for B30</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @foreach ($commission_data as $commission)
                                            <tr>
                                                <td>{{ $commission->scheme_name }}</td>
                                                @php
                                                $monthNum  = $commission->month;
                                                $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                                                $monthName = $dateObj->format('F');
                                                @endphp
                                                <td>{{ $monthName }}</td>
                                                <td>{{ $commission->year }}</td>
                                                <td>{{ $commission->first_year_trail }}</td>
                                                <td>{{ $commission->second_year_trail }}</td>
                                                <td>{{ $commission->b30 }}</td>
                                                @if($commission->special_additional_first_year_trail == null)
                                                <td>N/A</td>
                                                @else
                                                <td>{{ $commission->special_additional_first_year_trail }}</td>
                                                @endif
                                                @if($commission->special_additional_first_year_trail_for_b30 == null)
                                                <td>N/A</td>
                                                @else
                                                <td>{{ $commission->special_additional_first_year_trail_for_b30 }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @if($flag_have_aum_transaction_analytics_permission)
                <!--- Reports of AUM and Transaction Analytics  -->
                <div class="tab-pane tab-list" id="aum_transaction_analytics" style="display: none;">
                    <div class="row mt-4">
                        <div class="col-lg-8"><h3>Showing AUM and Transaction Analytics as on Date</h3></div>
                        <div class="col-lg-2 text-right">
                            <select class="form-control" id="scheme_filter" name="scheme_filter" onchange="load_aum_year()">
                                    <option value="0" selected>All</option>
                                    <option value="1">Asset Type Wise</option>
                            </select>
                        </div>
                        <div class="col-lg-2 text-right">
                            <select class="form-control" id="aum_year" name="aum_year" onchange="load_aum_year()">
                                 @foreach($getListofYears as $year)
                                    <option value="{{$year}}">{{$year}}</option>
                                 @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="mt-2 table-responsive">
                                <table id="panel_aum_transaction" class="display" style="width:100%;" data-arn_number="{{$arn_number??''}}">
                                    <thead>
                                    <tr>
                                    @php
                                    echo $aum_transactions_heading_html;
                                    @endphp
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                    @php
                                    echo $aum_transactions_heading_html;
                                    @endphp
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- END of Reports of AUM and Transaction Analytics  -->
                @endif
                @if($flag_have_sip_analytics_permission)
                <!--- Reports of SIP Analytics  -->
                <div class="tab-pane tab-list" id="sip_analytics" style="display: none;">
                    <div class="row mt-4">
                        <div class="col-lg-8"><h3>Live SIP Registration Book as on Date</h3></div>
                        <div class="col-lg-2 text-right">
                            <select class="form-control" id="sip_scheme_filter" name="sip_scheme_filter" onchange="load_sip_year()">
								<option value="0" selected>All</option>
								{{-- @foreach($asset_type as $val) --}}
								<option value="1" >Asset Wise</option>
								{{-- <option value="{{$val->asset_type}}" >{{$val->asset_type}}</option>
									@endforeach --}}
                            </select>
                        </div>
                        <div class="col-lg-2 text-right">
                            <select class="form-control" id="sip_year" name="sip_year" onchange="load_sip_year()">
								
                                 @foreach($years as $val)
                                    <option value="{{$val->year}}">{{$val->year}}</option>
                                 @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="mt-2 table-responsive">
                                <table id="panel_sip_analytics" class="display" style="width:100%;" data-arn_number="{{$arn_number??''}}">
                                    <thead>
                                    <tr>
                                    @php
                                    echo $sip_analytics_heading_html;
                                    @endphp
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                    @php
                                    echo $sip_analytics_heading_html;
                                    @endphp
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- ENd of Reports of SIP Analytics  -->
                @endif
                @if($flag_have_client_analytics_permission)
                <!--- Reports of CLient Analytics  -->
                <div class="tab-pane tab-list" id="client_analytics" style="display: none;">
                    <div class="row mt-4">
                        <div class="col-lg-8"><h3>Showing Client Analytics as on Date</h3></div>
                        <div class="col-lg-2 text-right">
                            <select class="form-control" id="client_scheme_filter" name="client_scheme_filter" onchange="load_client_year()">
								<option value="0" selected>All</option>
								<option value="1">Asset Wise</option>
                            </select>
                        </div>
                        <div class="col-lg-2 text-right">
                            <select class="form-control" id="client_year" name="client_year" onchange="load_client_year()">
                                 @foreach($getSettingsTableYear as $year)
                                    <option value="{{$year}}">{{$year}}</option>
                                 @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="mt-2 table-responsive">
                                <table id="panel_client_analytics" class="display" style="width:100%;" data-arn_number="{{$arn_number??''}}">
                                    <thead>
                                    <tr>
                                    @php
                                    echo $client_analytics_heading_html;
                                    @endphp
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                    @php
                                    echo $client_analytics_heading_html;
                                    @endphp
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Client Analytics -->
                @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        No record found
        @endif
    </div>
</div>
<!--/.row mt-4-->

@endsection

@section('custom_after_footer_html')

<!-- View Modal -->
<div class="modal fade" id="view_meetinglog_modal" tabindex="-1" role="dialog" aria-labelledby="view_meetinglog_modal_label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" class="close closed"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="view_meetinglog_modal_label">View: Meeting Log</h4>
            </div>
            <div class="modal-body">
                <div class="mt-2">
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>ARN</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="arn"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Meeting Mode</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="meeting_mode"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Contact Name</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="contact_name"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Contact Mobile</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="contact_mobile"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Contact Email</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="contact_email"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Start Time</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="start_time"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>End Time</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="end_time"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Remarks</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="remarks"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Customer Response Received</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="customer_response"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Customer Response Source</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="customer_source"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Customer Response Received Date</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="customer_response_received_date"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Customer Given Rating</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="customer_rating"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Customer Feedback</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="customer_feedback"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<!-- End View modal -->
<!-- Modal edit-->
<div class="modal fade" id="editmyModal1" tabindex="-1" role="dialog" aria-labelledby="editmyModalLabel">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close closed"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Update Commission Structure</h4>
        </div>
        <div class="modal-body">
                <div class="mt-2">
                    <form method="POST" action="{{ route('updatecommissiondetail') }}">
                        @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row form-inline">
                            <div class="col">
                                <div class="form-group">
                                    <label >Scheme Code</label> 
                                    <select class="form-control" id="scheme_code" name="scheme_code" required onchange="getpcode()">
                                <option value="" disabled selected>Select Scheme Code</option>
                                @php $scheme_list=array('FCRG','ONRG','ELRG');
                                foreach($scheme_list as $val){
                                 @endphp   
                                 <option value="{{$val}}"{{$val== "ONRG"  ? '' : ''}} >{{$val}}</option>
                               @php } @endphp
                               
                                </select>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row form-inline">
                            <div class="col">
                                <div class="form-group">
                                    <label >Plan Type</label> 
                                    <select class="form-control" id="plan_type" name="plan_type">
                                    <option value="">Select Plan type</option>
                                    <option value="Business">Business</option>
                                    <option value="Professional">Professional</option>
                                    </select>
                                    <input type="hidden" id="commission_id" name="commission_id"/>
                                    <input type="hidden" id="arn_commission" name="arn_commission" value="" />
                                    <input type="hidden" id="arn_number" name="arn_number" value="" />
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row form-inline">
                            <div class="col">
                                <div class="form-group">
                                <label class="input-label active">Year:</label>
                                <select class="form-control"  id="year" name="year" placeholder="Select year" onchange="enab()" required>
                                    <option value="" disabled selected>Select Year</option>
                                    @for($i = $start_year; $i <= $end_year; $i++)
                                    <option value="{{ $i }}" >{{$i}}</option>
                                    @endfor
                                </select>
                                <label class="input-label active">Month:</label>
                                <select class="form-control month" id="month" name="month" required disabled>
                                    <option value="" disabled selected>Select Month</option>
                                    @for($i = 1; $i <= 12; $i++)
                                    <option value="{{date('m',strtotime(date('Y').'-'.$i.'-01'))}}" {{($checkmonth == $i) ? 'selected':''}}>{{date('F',strtotime(date('Y').'-'.$i.'-01'))}}</option>
                                    @endfor
                                    </select>
                                    <!-- <input type="number" max="12" class="form-control"  id="datepicker" name="month" step="1" required placeholder="MM" onchange="if(this.value.length < 2) this.value = '0' + this.value;"/> -->
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
    </div>
    </div>
</div>
<!-- end -->

<!-- View AUM DETAIL Modal -->
<div class="modal fade" id="view_aum_modal" tabindex="-1" role="dialog" aria-labelledby="view_aum_modal_label">
    <div class="modal-dialog modal-lg" role="document" style="width:100%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" class="close closed"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="view_meetinglog_modal_label">View: AUM & Transaction Analytics <span id="aum_text"></span></h4>
            </div>
            <div class="modal-body">
                <div class="mt-2">
                        aum_transaction_analytics
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="back_aum" translate>Back</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<!-- End View modal -->

<!-- View SIP DETAIL Modal -->
<div class="modal fade" id="view_sip_modal" tabindex="-1" role="dialog" aria-labelledby="view_sip_modal_label">
    <div class="modal-dialog modal-lg" role="document" style="width:100%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" class="close closed"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="view_meetinglog_modal_label">View: SIP Analytics <span id="sip_text"></span></h4>
            </div>
            <div class="modal-body">
                <div class="mt-2">
                        sip Analytics
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="back_sip" translate>Back</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<!-- End View modal -->
<!-- View Client DETAIL Modal -->
<div class="modal fade" id="view_client_modal" tabindex="-1" role="dialog" aria-labelledby="view_client_modal_label">
    <div class="modal-dialog modal-lg" role="document" style="width:100%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" class="close closed"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="view_meetinglog_modal_label">View: Client Analytics <span id="client_text"></span></h4>
            </div>
            <div class="modal-body">
                <div class="mt-2">
                        client Analytics
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="back_client" translate>Back</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<!-- End View modal -->

<!-- Quality relationship ARN View Modal -->
@if(isset($arr_relationship_quality) && is_array($arr_relationship_quality) && count($arr_relationship_quality) > 0)
<div class="modal fade" id="view_quality_relationship_arn_modal" tabindex="-1" role="dialog" aria-labelledby="view_quality_relationship_arn_modal_label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" class="close closed"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="view_quality_relationship_arn_modal_label">Quality Relationship ARN Info</h4>
            </div>
            <div class="modal-body">
                <div class="mt-2">
                <table class="table table-striped table-top-align">
                    <thead>
                        <tr>
                        <th>Type</th>
                        <th>Definition</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($arr_relationship_quality as $_key => $_value)
                        <tr>
                            <td>{{$_value['label']}}</td>
                            <td>{{($_value['description']??'')}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End View modal -->
@endif

@endsection

@section('custom_scripts')

<script src="{{asset('js/select2.min.js')}}"></script>
<script src="{{asset('js/meetinglog.js')}}"></script>
<script src="{{asset('js/aum_transaction.js?v=2.2')}}"></script>
<script src="{{asset('js/sip_analytics.js?v=2.3')}}"></script>
<script src="{{asset('js/client_analytics.js?v=2.3')}}"></script>
<script src="{{asset('js/client_analytics_monthwise.js?v=2.4')}}"></script>
<script type="text/javascript">
    var investor_transactions_type = JSON.parse(@json($transaction_type_json));
    var aum_export_permission = JSON.parse(@json($flag_have_export_aum_transaction_analytics_permission));
    var sip_export_permission = JSON.parse(@json($flag_have_export_sip_analytics_permission));
    var client_export_permission = JSON.parse(@json($flag_have_export_client_analytics_permission));
    $(document).ready(function() {
        window.setTimeout(function(){
            $('img.uploaded_img_file').each(function(){
                $(this).attr('src', $(this).attr('data-src'));
            });
        }, 1500);

        $(".new-tab li a").on("click", function(a) {
            a.preventDefault();
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
            var t = $(this).attr("href").split("#")[1];
            $(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id="' + t + '"]').fadeIn();
            $(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id!="' + t + '"]').hide();
            switch(t){
                case 'meetinglog':
                    // preparing datatable for showing meeting log
                    if(meetinglog_datatable == null || meetinglog_datatable == ''){
                        load_meetinglog_datatable();
                    }
                    else{
                        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
                    }
                    break;
                case 'aum_transaction_analytics':
                    // preparing datatable for showing aum transaction
                    if(aum_transaction_datatable == null || aum_transaction_datatable == ''){
                        load_aum_transaction_datatable();
                    }
                    else{
                        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
                    }
                    break;
                case 'sip_analytics':
                    // preparing datatable for showing sip analytics
                    if(sip_analytics_datatable == null || sip_analytics_datatable == ''){
                        load_sip_analytics_datatable();
                    }
                    else{
                        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
                    }
                    break;
                case 'client_analytics':
                    // preparing datatable for showing client analytics
                    if(client_analytics_datatable == null || client_analytics_datatable == ''){
                        load_client_analytics_datatable();
                    }
                    else{
                        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
                    }
                    break;
                case 'client_analytics_monthwise':
                    /*
                    // preparing datatable for showing client analytics monthwise
                    if(client_monthwise_analytics_datatable == null || client_monthwise_analytics_datatable == ''){
                        load_client_monthwise_analytics_datatable();
                    }
                    else{
                        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
                    }
                    */
                    break;
            }
        });

        table = $('#panel_arn_amc_wise_list').DataTable({
            scrollX: true,
            responsive: true,
            // dom: 't',
            language: {
                oPaginate: {
                sNext: '<i class="la la-angle-right"></i>',
                sPrevious: '<i class="la la-angle-left"></i>',
                sFirst: '<i class="fa fa-step-backward"></i>',
                sLast: '<i class="fa fa-step-forward"></i>'
                }
            },
        });

        $(".dataTables_scrollHeadInner").css({"width":"100%"});
        $("table.display:first").css({"width":"100%"});
        $('.dataTables_filter label').append('<i class="icons search-icon"></i>');

        $('#panel_commission').DataTable({
            responsive: true,
            // dom: 't',
            language: {
                oPaginate: {
                sNext: '<i class="la la-angle-right"></i>',
                sPrevious: '<i class="la la-angle-left"></i>',
                sFirst: '<i class="fa fa-step-backward"></i>',
                sLast: '<i class="fa fa-step-forward"></i>'
                }
            },
        }); 

        $('.dataTables_filter label').append('<i class="icons search-icon"></i>');

        $( "#submit" ).click(function() {
            var dcategory =   $('#dcategory').val();
            var arn =   $('#arn_number').val();

            if(dcategory ==''){
                swal("Please","Select Something!!", "warning");
            }
            else{
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        dcategory: dcategory,
                        arn_number: arn,
                        updating_field:'distributor_category'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#dcategory').hide();
                        $('#submit').hide();
                        $('#cancelDcategory').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#dcategory').prev('span').show();
                            $("#dcategory").prev("span").text($("#dcategory").val());
                            $('#editbutton').show();
                        });
                    }
                });
            }
        });

        $( "#psubmit" ).click(function() {
            var pfocus =   $('#pfocus').val();
            var arn =   $('#arn_number').val();

            if(pfocus ==''){
                swal("Please","Select Something!!", "warning");
            }
            else{
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        pfocus: pfocus,
                        arn_number: arn,
                        updating_field:'project_focus'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#pfocus').hide();
                        $('#psubmit').hide();
                        $('#cancelPfocus').hide();
                        // swal(success.msg);
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#pfocus').prev('span').show();
                            $("#pfocus").prev("span").text($("#pfocus").find("option:selected").text());
                            $('#editFocusbutton').show();
                        });
                    }
                });
            }
        });

        $( "#ranksubmit" ).click(function() {
            var rankmfp =   $('#rankmf_p').val();
            var arn     =   $('#arn_number').val();

            if(rankmfp ==''){
                    swal("Please","Select Something!!", "warning")
            }
            else{
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        rankmfp: rankmfp,
                        arn_number: arn,
                        updating_field:'is_rankmf_partner'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#rankmf_p').hide();
                        $('#ranksubmit').hide()
                        $('#cancelRankMfPartner').hide();;
                        // swal(success.msg);
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#rankmf_p').prev('span').show();
                            $("#rankmf_p").prev("span").text($("#rankmf_p").find("option:selected").text());
                            $('#editRankbutton').show();
                        });
                    }
                });
            }
        });

        $( "#rankasubmit" ).click(function() {
            var rankmfap = $('#rankmfa_p').val();
            var arn = $('#arn_number').val();

            if(rankmfap ==''){
                swal("Please","Select Something!!", "warning")
            }
            else{
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        rankmfap: rankmfap,
                        arn_number: arn,
                        updating_field:'is_partner_active_on_rankmf'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#rankmfa_p').hide();
                        $('#rankasubmit').hide();
                        $('#cancelRankMfAPartner').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#rankmfa_p').prev('span').show();
                            $("#rankmfa_p").prev("span").text($("#rankmfa_p").find("option:selected").text());
                            $('#editRankAbutton').show();
                        });
                    }
                });
            }
        });

        $( "#samcosubmit" ).click(function() {
            var samcop =   $('#samco_p').val();
            var arn     =   $('#arn_number').val();

            if(samcop ==''){
                swal("Please","Select Something!!", "warning")
            }
            else{
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        samcop: samcop,
                        arn_number: arn,
                        updating_field:'is_samcomf_partner'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#samco_p').hide();
                        $('#samcosubmit').hide();
                        $('#cancelSamcoMfPartner').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#samco_p').prev('span').show();
                            $("#samco_p").prev("span").text($("#samco_p").find("option:selected").text());
                            $('#editSamcobutton').show();
                        });
                    }
                });
            }
        });

        $( "#samcoasubmit" ).click(function() {
            var samcoap =   $('#samcoa_p').val();
            var arn     =   $('#arn_number').val();

            if(samcoap ==''){
                swal("Please","Select Something!!", "warning")
            }
            else{
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        samcoap: samcoap,
                        arn_number: arn,
                        updating_field:'is_partner_active_on_samcomf'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#samcoa_p').hide();
                        $('#samcoasubmit').hide();
                        $('#cancelSamcoMfAPartner').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#samcoa_p').prev('span').show();
                            $("#samcoa_p").prev("span").text($("#samcoa_p").find("option:selected").text());
                            $('#editSamcoAbutton').show();
                        });
                    }
                });
            }
        });

        $( "#pnamesubmit" ).click(function() {
            var papprovename =  $.trim($('#papprovename').val());
            var arn = $('#arn_number').val();
            if(papprovename ==''){
                swal("Please","Enter a Name!", "warning")
            }
            else if(papprovename.length <3){
                swal("Please","Enter a Name with minimum 3 Character!", "warning")
            }
            else{
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        papprovename: papprovename,
                        arn_number: arn,
                        updating_field:'product_approval_person_name'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#papprovename').hide();
                        $('#pnamesubmit').hide();
                        $('#cancelPName').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#papprovename').prev('span').show();
                            $("#papprovename").prev("span").text($("#papprovename").val());
                            $('#editPNamebutton').show();
                        });
                    },
                });
            }
        });

        $( "#pmobilesubmit" ).click(function() {
            var papprovemobile =  $.trim($('#papprovemobile').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateMobile(papprovemobile)){
                flag=0;
                swal("Please","Enter the valid Mobile Number!", "warning")
            }

            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        papprovemobile: papprovemobile,
                        arn_number: arn,
                        updating_field:'product_approval_person_mobile'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#papprovemobile').hide();
                        $('#pmobilesubmit').hide();
                        $('#cancelPMobile').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#papprovemobile').prev('span').show();
                            $("#papprovemobile").prev("span").text($("#papprovemobile").val());
                            $('#editPMobilebutton').show();
                        });
                    }
                });
            }
        });

        $( "#pemailsubmit" ).click(function() {
            var papproveemail =  $.trim($('#papproveemail').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateEmail(papproveemail)){
                flag=0;
                swal("Please","Enter the valid Email Id!", "warning")
            }

            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        papproveemail: papproveemail,
                        arn_number: arn,
                        updating_field:'product_approval_person_email'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#papproveemail').hide();
                        $('#pemailsubmit').hide();
                        $('#cancelPEmail').hide();
                        // swal(success.msg);
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#papproveemail').prev('span').show();
                            $("#papproveemail").prev("span").text($("#papproveemail").val());
                            $('#editPEmailbutton').show();
                        });
                    }
                });
            }
        });

        $( "#salesdrivesubmit" ).click(function() {
            var salesdriveename =  $.trim($('#salesdriveename').val());
            var arn = $('#arn_number').val();
            if(salesdriveename ==''){
                swal("Please","Enter a Name!", "warning")
            }
            else if(salesdriveename.length <3){
                swal("Please","Enter a Name with minimum 3 Character!", "warning")
            }
            else{
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        salesdriveename: salesdriveename,
                        arn_number: arn,
                        updating_field:'sales_drive_person_name'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#salesdriveename').hide();
                        $('#salesdrivesubmit').hide();
                        $('#cancelSalesName').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#salesdriveename').prev('span').show();
                            $("#salesdriveename").prev("span").text($("#salesdriveename").val());
                            $('#editSalesNamebutton').show();
                        });
                    },
                });
            }
        });

        $( "#salesdrivemobilesubmit" ).click(function() {
            var salesdrivemobile =  $.trim($('#salesdrivemobile').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateMobile(salesdrivemobile)){
                flag=0;
                swal("Please","Enter the valid Mobile Number!", "warning")
            }
            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        salesdrivemobile: salesdrivemobile,
                        arn_number: arn,
                        updating_field:'sales_drive_person_mobile'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#salesdrivemobile').hide();
                        $('#salesdrivemobilesubmit').hide();
                        $('#cancelSalesMobile').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#salesdrivemobile').prev('span').show();
                            $("#salesdrivemobile").prev("span").text($("#salesdrivemobile").val());
                            $('#editSalesMobilebutton').show();
                        });
                    },
                });
            }
        });

        $( "#salesdriveemailsubmit" ).click(function() {
            var salesdriveemail =  $.trim($('#salesdriveemail').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateEmail(salesdriveemail)){
                flag=0;
                swal("Please","Enter the valid Email Id!", "warning")
            }
            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        salesdriveemail: salesdriveemail,
                        arn_number: arn,
                        updating_field:'sales_drive_person_email'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#salesdriveemail').hide();
                        $('#salesdriveemailsubmit').hide();
                        $('#cancelSalesEmail').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#salesdriveemail').prev('span').show();
                            $("#salesdriveemail").prev("span").text($("#salesdriveemail").val());
                            $('#editSalesEmailbutton').show();
                        });
                    }
                });
            }
        });

        $( "#alternateMobileonesubmit" ).click(function() {
            var alternateMobileone =  $.trim($('#alternateMobileone').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateMobile(alternateMobileone)){
                flag=0;
                swal("Please Type","Valid Mobile Number!!", "warning")
            }
            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        alternateMobileone: alternateMobileone,
                        arn_number: arn,
                        updating_field:'alternate_mobile_1'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#alternateMobileone').hide();
                        $('#alternateMobileonesubmit').hide();
                        $('#cancelalternateMobileone').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#alternateMobileone').prev('span').show();
                            $("#alternateMobileone").prev("span").text($("#alternateMobileone").val());
                            $('#editAlternateonebutton').show();
                        });
                    }
                });
            }
        });

        $( "#alternateMobiletwosubmit" ).click(function() {
            var alternateMobiletwo =  $.trim($('#alternateMobiletwo').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateMobile(alternateMobiletwo)){
                flag=0;
                swal("Please Type","Valid Mobile Number!!", "warning")
            }
            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        alternateMobiletwo: alternateMobiletwo,
                        arn_number: arn,
                        updating_field:'alternate_mobile_2'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#alternateMobiletwo').hide();
                        $('#alternateMobiletwosubmit').hide();
                        $('#cancelalternateMobiletwo').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#alternateMobiletwo').prev('span').show();
                            $("#alternateMobiletwo").prev("span").text($("#alternateMobiletwo").val());
                            $('#editAlternatetwobutton').show();
                        });
                    }
                });
            }
        });

        $( "#alternateMobilethreesubmit" ).click(function() {
            var alternateMobilethree =  $.trim($('#alternateMobilethree').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateMobile(alternateMobilethree)){
                flag=0;
                swal("Please Type","Valid Mobile Number!!", "warning")
            }
            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        alternateMobilethree: alternateMobilethree,
                        arn_number: arn,
                        updating_field:'alternate_mobile_3'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#alternateMobilethree').hide();
                        $('#alternateMobilethreesubmit').hide();
                        $('#cancelalternateMobilethree').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#alternateMobilethree').prev('span').show();
                            $("#alternateMobilethree").prev("span").text($("#alternateMobilethree").val());
                            $('#editAlternatethreebutton').show();
                        });
                    }
                });
            }
        });

        $( "#alternateMobilefoursubmit" ).click(function() {
            var alternateMobilefour =  $.trim($('#alternateMobilefour').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateMobile(alternateMobilefour)){
                flag=0;
                swal("Please Type","Valid Mobile Number!!", "warning")
            }
            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        alternateMobilefour: alternateMobilefour,
                        arn_number: arn,
                        updating_field:'alternate_mobile_4'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#alternateMobilefour').hide();
                        $('#alternateMobilefoursubmit').hide();
                        $('#cancelalternateMobilefour').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#alternateMobilefour').prev('span').show();
                            $("#alternateMobilefour").prev("span").text($("#alternateMobilefour").val());
                            $('#editAlternatefourbutton').show();
                        });
                    }
                });
            }
        });

        $( "#alternateMobilefivesubmit" ).click(function() {
            var alternateMobilefive =  $.trim($('#alternateMobilefive').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateMobile(alternateMobilefive)){
                flag=0;
                swal("Please Type","Valid Mobile Number!!", "warning")
            }
            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        alternateMobilefive: alternateMobilefive,
                        arn_number: arn,
                        updating_field:'alternate_mobile_5'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#alternateMobilefive').hide();
                        $('#alternateMobilefivesubmit').hide();
                        $('#cancelalternateMobilefive').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#alternateMobilefive').prev('span').show();
                            $("#alternateMobilefive").prev("span").text($("#alternateMobilefive").val());
                            $('#editAlternatefivebutton').show();
                        });
                    }
                });
            }
        });

        $( "#alternateEmailonesubmit" ).click(function() {
            var alternateEmailone =  $.trim($('#alternateEmailone').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateEmail(alternateEmailone)){
                flag=0;
                swal("Please Type","Valid Email!!", "warning")
            }
            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        alternateEmailone: alternateEmailone,
                        arn_number: arn,
                        updating_field:'alternate_email_1'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#alternateEmailone').hide();
                        $('#alternateEmailonesubmit').hide();
                        $('#cancelalternateEmailone').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#alternateEmailone').prev('span').show();
                            $("#alternateEmailone").prev("span").text($("#alternateEmailone").val());
                            $('#editAlternateEmailonebutton').show();
                        });
                    }
                });
            }
        });

        $( "#alternateEmailtwosubmit" ).click(function() {
            var alternateEmailtwo =  $.trim($('#alternateEmailtwo').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateEmail(alternateEmailtwo)){
                flag=0;
                swal("Please Type","Valid Email!!", "warning")
            }
            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        alternateEmailtwo: alternateEmailtwo,
                        arn_number: arn,
                        updating_field:'alternate_email_2'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#alternateEmailtwo').hide();
                        $('#alternateEmailtwosubmit').hide();
                        $('#cancelalternateEmailtwo').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#alternateEmailtwo').prev('span').show();
                            $("#alternateEmailtwo").prev("span").text($("#alternateEmailtwo").val());
                            $('#editAlternateEmailtwobutton').show();
                        });
                    }
                });
            }
        });

        $( "#alternateEmailthreesubmit" ).click(function() {
            var alternateEmailthree =  $.trim($('#alternateEmailthree').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateEmail(alternateEmailthree)){
                flag=0;
                swal("Please Type","Valid Email!!", "warning")
            }
            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        alternateEmailthree: alternateEmailthree,
                        arn_number: arn,
                        updating_field:'alternate_email_3'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#alternateEmailthree').hide();
                        $('#alternateEmailthreesubmit').hide();
                        $('#cancelalternateEmailthree').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#alternateEmailthree').prev('span').show();
                            $("#alternateEmailthree").prev("span").text($("#alternateEmailthree").val());
                            $('#editAlternateEmailthreebutton').show();
                        });
                    }
                });
            }
        });

        $( "#alternateEmailfoursubmit" ).click(function() {
            var alternateEmailfour =  $.trim($('#alternateEmailfour').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateEmail(alternateEmailfour)){
                flag=0;
                swal("Please Type","Valid Email!!", "warning")
            }
            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        alternateEmailfour: alternateEmailfour,
                        arn_number: arn,
                        updating_field:'alternate_email_4'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#alternateEmailfour').hide();
                        $('#alternateEmailfoursubmit').hide();
                        $('#cancelalternateEmailfour').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#alternateEmailfour').prev('span').show();
                            $("#alternateEmailfour").prev("span").text($("#alternateEmailfour").val());
                            $('#editAlternateEmailfourbutton').show();
                        });
                    }
                });
            }
        });

        $( "#alternateEmailfivesubmit" ).click(function() {
            var alternateEmailfive =  $.trim($('#alternateEmailfive').val());
            var arn = $('#arn_number').val();
            var flag=1;
            if(!validateEmail(alternateEmailfive)){
                flag=0;
                swal("Please Type","Valid Email!!", "warning")
            }
            if(flag==1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ url('distributor/UpdateByArn') }}",
                    data: {
                        alternateEmailfive: alternateEmailfive,
                        arn_number: arn,
                        updating_field:'alternate_email_5'
                    },
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown){
                        if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                            prepare_error_text(jqXHR.responseJSON);
                        }
                        else{
                            swal('', unable_to_process_request_text, 'warning');
                        }
                    },
                    success:function(success){
                        $('#alternateEmailfive').hide();
                        $('#alternateEmailfivesubmit').hide();
                        $('#cancelalternateEmailfive').hide();
                        swal({
                            title: "Success",
                            text: success.msg,
                            type: "success"
                        }).then((value) => {
                            $('#alternateEmailfive').prev('span').show();
                            $("#alternateEmailfive").prev("span").text($("#alternateEmailfive").val());
                            $('#editAlternateEmailfivebutton').show();
                        });
                    }
                });
            }
        });
    });

    function export_csv_formatted_data_amc(arn_number){
        var searched_text =  $('#panel_arn_amc_wise_list_filter input[type="search"]').val();
        var formObj = $('#frm_export_data');
        formObj.append('<input type="hidden" name="searched_text" value="'+ searched_text +'">');
        formObj.append('<input type="hidden" name="arn_number" value="'+ arn_number +'">');
        formObj.attr({'action': baseurl + '/distributor_exportToCSV'});
        formObj.submit();
        formObj.attr({'action':'javascript:void(0);'});
        $('#frm_export_data input[name!="_token"]').remove();
    }

    function export_csv_formatted_data_commission(arn_number){
        var searched_text =  $('#panel_commission_filter input[type="search"]').val();
        var formObj = $('#frm_export_data');
        formObj.append('<input type="hidden" name="searched_text" value="'+ searched_text +'">');
        formObj.append('<input type="hidden" name="arn_number" value="'+ arn_number +'">');
        formObj.attr({'action': baseurl + '/commission_exportToCSV'});
        formObj.submit();
        formObj.attr({'action':'javascript:void(0);'});
        $('#frm_export_data input[name!="_token"]').remove();
    }

    function editFunc(){
        $('#editbutton').hide();
        $('#dcategory').prev('span').hide();
        $('#dcategory').show();
        $('#submit').show();
        $('#cancelDcategory').show();
    }

    function cancelDcategory(){
        $('#dcategory').prev('span').show();
        $('#editbutton').show();
        $('#dcategory').hide();
        $('#submit').hide();
        $('#cancelDcategory').hide();
    }

    function editFocusFunc(){
        $('#editFocusbutton').hide();
        $('#pfocus').prev('span').hide();
        $('#pfocus').show();
        $('#psubmit').show();
        $('#cancelPfocus').show();
    }

    function cancelPfocus(){
        $('#pfocus').prev('span').show();
        $('#editFocusbutton').show();
        $('#pfocus').hide();
        $('#psubmit').hide();
        $('#cancelPfocus').hide();
    }

    function editRankFunc(){
        $('#editRankbutton').hide();
        $('#rankmf_p').prev('span').hide();
        $('#rankmf_p').show();
        $('#ranksubmit').show();
        $('#cancelRankMfPartner').show();
    }

    function cancelRankMfPartner(){
        $('#rankmf_p').prev('span').show();
        $('#editRankbutton').show();
        $('#rankmf_p').hide();
        $('#ranksubmit').hide();
        $('#cancelRankMfPartner').hide();
    }

    function editRankAFunc(){
        $('#editRankAbutton').hide();
        $('#rankmfa_p').prev('span').hide();
        $('#rankmfa_p').show();
        $('#rankasubmit').show();
        $('#cancelRankMfAPartner').show();
    }

    function cancelRankMfAPartner(){
        $('#rankmfa_p').prev('span').show();
        $('#editRankAbutton').show();
        $('#rankmfa_p').hide();
        $('#rankasubmit').hide();
        $('#cancelRankMfAPartner').hide();
    }
    
    function editSamcoFunc(){
        $('#editSamcobutton').hide();
        $('#samco_p').prev('span').hide();
        $('#samco_p').show();
        $('#samcosubmit').show();
        $('#cancelSamcoMfPartner').show();
    }

    function cancelSamcoMfPartner(){
        $('#samco_p').prev('span').show();
        $('#editSamcobutton').show();
        $('#samco_p').hide();
        $('#samcosubmit').hide();
        $('#cancelSamcoMfPartner').hide();
    }

    function editSamcoAFunc(){
        $('#editSamcoAbutton').hide();
        $('#samcoa_p').prev('span').hide();
        $('#samcoa_p').show();
        $('#samcoasubmit').show();
        $('#cancelSamcoMfAPartner').show();
    }

    function cancelSamcoMfAPartner(){
        $('#samcoa_p').prev('span').show();
        $('#editSamcoAbutton').show();
        $('#samcoa_p').hide();
        $('#samcoasubmit').hide();
        $('#cancelSamcoMfAPartner').hide();
    }

    function editPNameFunc(){
        $('#editPNamebutton').hide();
        $('#papprovename').prev('span').hide();
        $('#papprovename').show();
        $('#pnamesubmit').show();
        $('#cancelPName').show();
    }

    function cancelPName(){
        $('#papprovename').prev('span').show();
        $('#editPNamebutton').show();
        $('#papprovename').hide();
        $('#pnamesubmit').hide();
        $('#cancelPName').hide();
    }

    function editPMobileFunc(){
        $('#editPMobilebutton').hide();
        $('#papprovemobile').prev('span').hide();
        $('#papprovemobile').show();
        $('#pmobilesubmit').show();
        $('#cancelPMobile').show();
    }

    function cancelPMobile(){
        $('#papprovemobile').prev('span').show();
        $('#editPMobilebutton').show();
        $('#papprovemobile').hide();
        $('#pmobilesubmit').hide();
        $('#cancelPMobile').hide();
    }

    function editPEmailFunc(){
        $('#editPEmailbutton').hide();
        $('#papproveemail').prev('span').hide();
        $('#papproveemail').show();
        $('#pemailsubmit').show();
        $('#cancelPEmail').show();
        }
    function cancelPEmail(){
        $('#papproveemail').prev('span').show();
        $('#editPEmailbutton').show();
        $('#papproveemail').hide();
        $('#pemailsubmit').hide();
        $('#cancelPEmail').hide();
    }

    function editSalesNameFunc(){
        $('#editSalesNamebutton').hide();
        $('#salesdriveename').prev('span').hide();
        $('#salesdriveename').show();
        $('#salesdrivesubmit').show();
        $('#cancelSalesName').show();
    }

    function cancelSalesName(){
        $('#salesdriveename').prev('span').show();
        $('#editSalesNamebutton').show();
        $('#salesdriveename').hide();
        $('#salesdrivesubmit').hide();
        $('#cancelSalesName').hide();
    }

    function editSalesMobileFunc(){
        $('#editSalesMobilebutton').hide();
        $('#salesdrivemobile').prev('span').hide();
        $('#salesdrivemobile').show();
        $('#salesdrivemobilesubmit').show();
        $('#cancelSalesMobile').show();
        }
    function cancelSalesMobile(){
        $('#salesdrivemobile').prev('span').show();
        $('#editSalesMobilebutton').show();
        $('#salesdrivemobile').hide();
        $('#salesdrivemobilesubmit').hide();
        $('#cancelSalesMobile').hide();
    }

    function editSalesEmailFunc(){
        $('#editSalesEmailbutton').hide();
        $('#salesdriveemail').prev('span').hide();
        $('#salesdriveemail').show();
        $('#salesdriveemailsubmit').show();
        $('#cancelSalesEmail').show();
    }

    function cancelSalesEmail(){
        $('#salesdriveemail').prev('span').show();
        $('#editSalesEmailbutton').show();
        $('#salesdriveemail').hide();
        $('#salesdriveemailsubmit').hide();
        $('#cancelSalesEmail').hide();
    }

    function editAlternateMobileoneFunc(){
        $('#editAlternateonebutton').hide();
        $('#alternateMobileone').prev('span').hide();
        $('#alternateMobileone').show();
        $('#alternateMobileonesubmit').show();
        $('#cancelalternateMobileone').show();
    }

    function cancelalternateMobileone(){
        $('#alternateMobileone').prev('span').show();
        $('#editAlternateonebutton').show();
        $('#alternateMobileone').hide();
        $('#alternateMobileonesubmit').hide();
        $('#cancelalternateMobileone').hide();
    }

    function editAlternateMobiletwoFunc(){
        $('#editAlternatetwobutton').hide();
        $('#alternateMobiletwo').prev('span').hide();
        $('#alternateMobiletwo').show();
        $('#alternateMobiletwosubmit').show();
        $('#cancelalternateMobiletwo').show();
    }

    function cancelalternateMobiletwo(){
        $('#alternateMobiletwo').prev('span').show();
        $('#editAlternatetwobutton').show();
        $('#alternateMobiletwo').hide();
        $('#alternateMobiletwosubmit').hide();
        $('#cancelalternateMobiletwo').hide();
    }

    function editAlternateMobilethreeFunc(){
        $('#editAlternatethreebutton').hide();
        $('#alternateMobilethree').prev('span').hide();
        $('#alternateMobilethree').show();
        $('#alternateMobilethreesubmit').show();
        $('#cancelalternateMobilethree').show();
    }

    function cancelalternateMobilethree(){
        $('#alternateMobilethree').prev('span').show();
        $('#editAlternatethreebutton').show();
        $('#alternateMobilethree').hide();
        $('#alternateMobilethreesubmit').hide();
        $('#cancelalternateMobilethree').hide();
    }

    function editAlternateMobilefourFunc(){
        $('#editAlternatefourbutton').hide();
        $('#alternateMobilefour').prev('span').hide();
        $('#alternateMobilefour').show();
        $('#alternateMobilefoursubmit').show();
        $('#cancelalternateMobilefour').show();
    }

    function cancelalternateMobilefour(){
        $('#alternateMobilefour').prev('span').show();
        $('#editAlternatefourbutton').show();
        $('#alternateMobilefour').hide();
        $('#alternateMobilefoursubmit').hide();
        $('#cancelalternateMobilefour').hide();
    }

    function editAlternateMobilefiveFunc(){
        $('#editAlternatefivebutton').hide();
        $('#alternateMobilefive').prev('span').hide();
        $('#alternateMobilefive').show();
        $('#alternateMobilefivesubmit').show();
        $('#cancelalternateMobilefive').show();
    }

    function cancelalternateMobilefive(){
        $('#alternateMobilefive').prev('span').show();
        $('#editAlternatefivebutton').show();
        $('#alternateMobilefive').hide();
        $('#alternateMobilefivesubmit').hide();
        $('#cancelalternateMobilefive').hide();
    }

    function editAlternateEmailoneFunc(){
        $('#editAlternateEmailonebutton').hide();
        $('#alternateEmailone').prev('span').hide();
        $('#alternateEmailone').show();
        $('#alternateEmailonesubmit').show();
        $('#cancelalternateEmailone').show();
    }

    function cancelalternateEmailone(){
        $('#alternateEmailone').prev('span').show();
        $('#editAlternateEmailonebutton').show();
        $('#alternateEmailone').hide();
        $('#alternateEmailonesubmit').hide();
        $('#cancelalternateEmailone').hide();
    }

    function editAlternateEmailtwoFunc(){
        $('#editAlternateEmailtwobutton').hide();
        $('#alternateEmailtwo').prev('span').hide();
        $('#alternateEmailtwo').show();
        $('#alternateEmailtwosubmit').show();
        $('#cancelalternateEmailtwo').show();
    }

    function cancelalternateEmailtwo(){
        $('#alternateEmailtwo').prev('span').show();
        $('#editAlternateEmailtwobutton').show();
        $('#alternateEmailtwo').hide();
        $('#alternateEmailtwosubmit').hide();
        $('#cancelalternateEmailtwo').hide();
    }

    function editAlternateEmailthreeFunc(){
        $('#editAlternateEmailthreebutton').hide();
        $('#alternateEmailthree').prev('span').hide();
        $('#alternateEmailthree').show();
        $('#alternateEmailthreesubmit').show();
        $('#cancelalternateEmailthree').show();
    }

    function cancelalternateEmailthree(){
        $('#alternateEmailthree').prev('span').show();
        $('#editAlternateEmailthreebutton').show();
        $('#alternateEmailthree').hide();
        $('#alternateEmailthreesubmit').hide();
        $('#cancelalternateEmailthree').hide();
    }

    function editAlternateEmailfourFunc(){
        $('#editAlternateEmailfourbutton').hide();
        $('#alternateEmailfour').prev('span').hide();
        $('#alternateEmailfour').show();
        $('#alternateEmailfoursubmit').show();
        $('#cancelalternateEmailfour').show();
    }

    function cancelalternateEmailfour(){
        $('#alternateEmailfour').prev('span').show();
        $('#editAlternateEmailfourbutton').show();
        $('#alternateEmailfour').hide();
        $('#alternateEmailfoursubmit').hide();
        $('#cancelalternateEmailfour').hide();
    }

    function editAlternateEmailfiveFunc(){
        $('#editAlternateEmailfivebutton').hide();
        $('#alternateEmailfive').prev('span').hide();
        $('#alternateEmailfive').show();
        $('#alternateEmailfivesubmit').show();
        $('#cancelalternateEmailfive').show();
    }

    function cancelalternateEmailfive(){
        $('#alternateEmailfive').prev('span').show();
        $('#editAlternateEmailfivebutton').show();
        $('#alternateEmailfive').hide();
        $('#alternateEmailfivesubmit').hide();
        $('#cancelalternateEmailfive').hide();
    }

    function auto_assign_bdm_to_arn(){
        var arn = '';
        if($('#arn_number').length > 0 && $('#arn_number').val() != null && $('#arn_number').val() != ''){
            arn = $('#arn_number').val();
        }
        else{
            return false;
        }

        var inputData = {'save_data': 1};
        $.ajax({
            type: 'POST',
            url: "{{ url('distributor/auto-assign-bdm/')}}/"+ arn,
            data: inputData,
            dataType: 'json',
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                    prepare_error_text(jqXHR.responseJSON);
                }
                else{
                    swal('', unable_to_process_request_text, 'warning');
                }
            },
            success:function(response){
                if(response.status == 'failed'){
                    err_msg = '';
                    $.each(response.msg, function(key, value){
                        err_msg += value +'\n'
                    });

                    if(err_msg == ''){
                        err_msg = unable_to_process_request_text;
                    }
                    swal('', err_msg, 'warning');
                }
                else if(response.status == 'success'){
                    swal({
                        title: "Success",
                        text: response.msg,
                        type: "success"
                    }).then((value) => {
                        window.location.reload();
                        return false;
                    });
                }
            }
        });
    }

    function edit_field_data(input_field){
        $('#edit_'+ input_field).hide();
        $('#'+ input_field).prev('span').hide();
        $('#'+ input_field).show();
        if(input_field == 'direct_relationship_user_id' && $('#'+ input_field).hasClass('select2-hidden-accessible')){
            $('#'+ input_field).next('span.select2').show();
        }
        $('#submit_'+ input_field).show();
        $('#cancel_'+ input_field).show();

        if(input_field == 'direct_relationship_user_id' && !$('#'+ input_field).hasClass('select2-hidden-accessible')){
            $('#'+ input_field).select2();
        }
    }

    function cancel_field_edit(input_field){
        $('#'+ input_field).prev('span').show();
        $('#edit_'+ input_field).show();
        $('#'+ input_field).hide();
        if(input_field == 'direct_relationship_user_id' && $('#'+ input_field).hasClass('select2-hidden-accessible')){
            $('#'+ input_field).next('span.select2').hide();
        }
        $('#submit_'+ input_field).hide();
        $('#cancel_'+ input_field).hide();
    }

    function submit_field_data(input_field, input_field_label){
        var inputElementObj = $('#'+ input_field), err_flag = false, err_msg = '', elementToFocus = '';
        var input_field_value =  $.trim(inputElementObj.val());
        var arn = $('#arn_number').val();
        var max_size_in_kb = (1024 * 1024 * 5);
        var allowed_image_file_types = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'];
        var flag_file_upload = false, uploaded_file;

        if(input_field_value == null || input_field_value == ''){
            err_flag =  true;
            err_msg = capitalizeFirstLetter(input_field_label) +' value is required';
            if(elementToFocus == null || elementToFocus == ''){
                elementToFocus = inputElementObj;
            }
        }
        else{
            if(input_field == 'rm_relationship' && input_field_value == 'final'){
                // checking whether BDM mapping is present or not. If user is trying to mark RM relationship as FINAL
                var known_bdm_name = $('#direct_relationship_user_id').prev('span').text();
                if(known_bdm_name == null || typeof known_bdm_name == 'undefined' || known_bdm_name == ''){
                    err_flag = true;
                    err_msg = capitalizeFirstLetter(input_field_label) +' can not be marked as '+ input_field_value +', because it\'s not mapped against any BDM';
                    if(elementToFocus == null || elementToFocus == ''){
                        elementToFocus = inputElementObj;
                    }
                }
            }
            if(input_field == 'front_visiting_card_image' || input_field == 'back_visiting_card_image'){
                flag_file_upload = true;
                var uploaded_file = document.getElementById(input_field);
                if(uploaded_file.files.length == 0){
                    err_flag = true;
                    err_msg = "Please choose a "+ input_field_label;
                }
                else if($.inArray(uploaded_file.files[0].type, allowed_image_file_types) == -1){
                    err_flag = true;
                    err_msg = "Only PNG/JPG/GIF file is allowed";
                }
                else if(uploaded_file.files[0].size > max_size_in_kb){
                    err_flag = true;
                    err_msg = "Maximum file size should not exceed 5 MB";
                }
            }
        }

        if(!err_flag){
            var inputData;
            if(!flag_file_upload){
                // if request is not of FILE UPLOAD
                inputData = {'arn_number': arn, 'updating_field': input_field, 'updating_field_label': input_field_label};
                inputData[input_field] = input_field_value;
            }
            else{
                // preparing FORM DATA for sending it across request
                inputData = new FormData();
                inputData.append('arn_number', arn);
                inputData.append('updating_field', input_field);
                inputData.append('updating_field_label', input_field_label);
                inputData.append(input_field, uploaded_file.files[0]);
            }

            var confirm_response, confirmation_text = 'Are you sure?';
            if(input_field == 'direct_relationship_user_id'){
                confirmation_text = 'Is this RM relationship final?';
            }

            confirm_response = confirm(confirmation_text);
            if(input_field != 'direct_relationship_user_id'){
                if(!confirm_response){
                    // if user did not gave confirmation then stopping further processing of a request
                    return false;
                }
            }
            else{
                var rm_relationship_flag_value = 'provisional';
                if(confirm_response){
                    rm_relationship_flag_value = 'final';
                }
                $.extend(inputData, {'rm_relationship': rm_relationship_flag_value});
            }

            // saving data into the DB
            $.ajax({
                type: 'POST',
                url: "{{ url('distributor/UpdateByArn') }}",
                data: inputData,
                dataType: 'json',
                processData: !flag_file_upload,
                contentType: (flag_file_upload?false:'application/x-www-form-urlencoded'),
                error: function(jqXHR, textStatus, errorThrown){
                    if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                        prepare_error_text(jqXHR.responseJSON);
                    }
                    else{
                        swal('', unable_to_process_request_text, 'warning');
                    }
                },
                success:function(response){
                    if(response.status == 'failed'){
                        err_msg = '';
                        $.each(response.msg, function(key, value){
                            err_msg += value +'\n'
                        });

                        if(err_msg == ''){
                            err_msg = unable_to_process_request_text;
                        }
                        swal('', err_msg, 'warning');
                    }
                    else if(response.status == 'success'){
                        inputElementObj.hide();
                        $('#submit_'+ input_field).hide();
                        $('#cancel_'+ input_field).hide();
                        swal({
                            title: "Success",
                            text: response.msg,
                            type: "success"
                        }).then((value) => {
                            if(flag_file_upload){
                                // in case of file uploads, just reloading the page to show an updated data
                                window.location.reload();
                                return false;
                            }

                            var trigger_change_event = false;
                            inputElementObj.prev('span').show();
                            if( $.inArray(input_field, ['rm_relationship', 'project_emerging_stars', 'project_green_shoots', 'project_focus']) != -1){
                                // if updating field is rm relationship flag then while showing data just capitalizing the first letter
                                input_field_value = capitalizeFirstLetter(input_field_value);
                            }
                            else if(input_field == 'direct_relationship_user_id'){
                                // if updating field is bdm mapping then updating both bdm mapping field and rm relationship flag field data
                                if(rm_relationship_flag_value != null && typeof rm_relationship_flag_value != 'undefined'){
                                    $('#rm_relationship').prev("span").text(capitalizeFirstLetter(rm_relationship_flag_value));
                                    $('#cancel_rm_relationship').trigger('click');
                                }

                                input_field_value = inputElementObj.find('option:selected').text();
                                if(inputElementObj.hasClass('select2-hidden-accessible')){
                                    inputElementObj.next('span.select2').hide();
                                }
                                trigger_change_event = true;

                                // checking if reporting to name is present then showing it
                                if(inputElementObj.find('option:selected').attr('data-reporting_to_name') != null && typeof inputElementObj.find('option:selected').attr('data-reporting_to_name') != 'undefined'){
                                    $('#reporting_manager_of_direct_assignee').prev("span").text(inputElementObj.find('option:selected').attr('data-reporting_to_name'));
                                }
                            }
                            inputElementObj.prev("span").text(input_field_value);
                            $('#edit_'+ input_field).show();

                            // checking whether change event needs to be triggered or not
                            if(trigger_change_event){
                                inputElementObj.val('');
                                inputElementObj.trigger('change');
                            }

                            if(input_field == 'rm_relationship' && input_field_value == 'Final'){
                                inputElementObj.parent().find(':not(span)').remove();
                                $('#direct_relationship_user_id').parent().find(':not(span)').remove();
                            }
                            else if(input_field == 'direct_relationship_user_id' && (rm_relationship_flag_value != null && rm_relationship_flag_value == 'final')){
                                $('#rm_relationship').parent().find(':not(span)').remove();
                                inputElementObj.parent().find(':not(span)').remove();
                            }
                        });
                    }
                }
            });
        }
        else{
            // showing an error
            swal(err_msg, '', 'warning');
            swal({
                title: '',
                text: err_msg,
                type: 'warning'
            }).then((value) => {
                if(elementToFocus != null && elementToFocus != '' && elementToFocus.length > 0){
                    elementToFocus.focus();
                }
            });
        }
    }

    function edit_code(id){
      $.ajax({
         type:'POST',
         url:"{{ route('edit.commissiondetail') }}",
         data:{id:id},
         error: function(jqXHR, textStatus, errorThrown){
          if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
            prepare_error_text(jqXHR.responseJSON);
          }
          else{
            swal('', unable_to_process_request_text, 'warning');
          }
         },
         success:function(data){

            var resultdata=JSON.parse(data);
            //alert(resultdata[0].ARN);
            
            $("#commission_id").val(id);
            $("#arn_commission").val(resultdata[0].ARN);
            $("body").addClass("modal-open");            
            $(".modal-ovelay").fadeIn();
            $('#editmyModal1').addClass('in');

            $('#editmyModal1').show();
               }
            });
    }

    function validateMobile(e) {
        e = $.trim(e);
        var t = /^[1-9][0-9]{9}$/;
        return t.test(e) ? 1 : 0;
    }

    function validateEmail(e) {
        e = $.trim(e);
        var t = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        return t.test(e) ? 1 : 0;
    }
    function enab(){
    $("#month").prop('disabled', false);
    }

    $(".modal").on('click', function(e) { 
            //Check whether click on modal-content    
            if (e.target !== this)      
              return;
            $(".modal-ovelay").fadeOut();
            $(this).removeClass('in');
            $(this).hide();   
            
            $("body").removeClass("modal-open");          
          });
         
          $(".closed").click(function() {
          $(".modal").css({
              display: "none"
          });             
          $(".modal-ovelay").fadeOut();                      
          $(this).removeClass('in');            
          $("body").removeClass("modal-open"); 
          
         });

function getpcode(){
    let scode=$('#scheme_code').val();
     let arn=$('#arn_commission').val();
    $.ajax({
        type : "POST",
        url  : "{{asset('get-category-name')}}",
        data: {scode:scode,_token:"{{ csrf_token() }}",arn:arn},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data)
        {
            let json = JSON.parse(data);
            $("#plan_type").val(json.category);
            $('#arn_number').val(json.arn);
        
        }
    });

}
$(document).on("click", '.scheme_tab', function(event){
    let li_index = $(this).index();
    let li_tags  = document.getElementsByClassName('scheme_tab');
    if(li_index == 0){
        li_tags[li_index].classList.add('active');
        li_tags[li_index+1].classList.remove('active');
    }
    else{
        li_tags[li_index].classList.add('active');
        li_tags[li_index-1].classList.remove('active');
    }
    let scheme = event.target.getAttribute('value');
    $('#scheme_select').val(scheme);
    nfilterByDate();
});
$(document).on("click", '#commission_data_load', function(event){
    nfilterByDate();
});

function getProgressBar(){
    let scheme_type = $('#scheme_select').val();
    scheme_type='nfo';
    let arn = "{{$partner_data->ARN}}";
    $.ajax({
        url     : "{{asset('nfo-scheme-progressbar')}}",
        type    : "POST",
        data    : {scheme_type: scheme_type, arn: arn, _token:"{{ csrf_token() }}"},
        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success : function(data){
            let progress = document.getElementById('progressbar');
            progress.innerHTML = data;
        }
    });
}
function nfilterByDate(){
    let from_date = $("#nfromDate").val();
    let to_date = $("#ntoDate").val();
    if(!from_date){
        alert("Please select month");
        return false;
    }
    if(!to_date){
        alert("Please select year");
        return false;
    }
    let scheme_type = $('#scheme_select').val();
    let arn = "{{$partner_data->ARN}}";
    let url = "";
    if(scheme_type == 'nfo')
        url = "{{asset('nfo-scheme-rate-card')}}";
    else
        url = "{{asset('scheme-rate-card')}}"
    getProgressBar();
    $.ajax({
        url     : url,
        type    : "POST",
        data    : {month: from_date, year: to_date, scheme_type: scheme_type, arn: arn, _token:"{{ csrf_token() }}"},
        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success : function(data){
            tableRecreate('panel_commission', data);
        }
    });
}
function tableRecreate(id, data){
    $('#'+id).DataTable().destroy();
    $("#"+id).remove();
    $("#main_div_panel_commision").remove();
    div = document.createElement('div');
    div.innerHTML = data;
    div.classList.add('table-responsive');
    div.setAttribute("id", "main_div_panel_commision");
    $("#scheme_rate_card_table").append(div);
    let columns = document.getElementById('panel_commission').rows[0].cells.length
    let columnDefsArr = [];
    if(columns == 5){
        columnDefsArr= [
            { "width": "5%", "targets": 0 },
            { "width": "50%", "targets": 1 },
            { "width": "12%", "targets": 2 },
            { "width": "21%", "targets": 3 },
            { "width": "12%", "targets": 4 },
        ]
    }else{
        columnDefsArr= [
            { "width": "5%", "targets": 0 },
            { "width": "30%", "targets": 1 },
            { "width": "6%", "targets": 2 },
            { "width": "6%", "targets": 4 },
            { "width": "12%", "targets": 6 },
        ]
    }
    $('#'+id).DataTable({
        responsive: true,
        language: {
            "oPaginate": {
                "sNext": '<i class="icons angle-right"></i>',
                "sPrevious": '<i class="icons angle-left"></i>',
                "sFirst": '<i class="icons step-backward"></i>',
                "sLast": '<i class="icons step-forward"></i>'
            }
        },
        "pageLength":100,
        columnDefs: columnDefsArr
    }); 
}
</script>

@endsection
