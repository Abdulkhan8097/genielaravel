@php
$page_title = 'Add a role';
if(isset($flag_add_edit_record) && ($flag_add_edit_record == 'edit')){
    $page_title = 'Edit a role';
}

// page url where user will gets redirected when he/she clicks on CANCEL button
$back_page_url = URL::to('/roles');

@endphp
@extends('../layout')
@section('title', $page_title)
@section('breadcrumb_heading', $page_title)
@section('custom_head_tags')


@endsection

@section('content')

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="mt-2">
            <form id="frm_roles" class="" action="javascript:void(0);" method="post" autocomplete="off">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row form-inline">
                            <div class="col">
                                <div class="form-group">
                                    <label><b>Label</b></label> 
                                    <input type="text" class="form-control" id="txt_label" name="txt_label" value="{{$known_record_data['label']??''}}" maxlength="255">
                                    <p class="error">&nbsp;</p>
                                </div><!--/.form-group-->
                            </div><!--/.col-->
                            <div class="col-3">
                                <div class="form-group">
                                    <p><b>Show all ARN/User data</b> <a href="javascript:void(0);" title="If not checked then if logged in user is not a reporting person of any other person, then not showing any ARN data. If checked (E.G. Admin Role etc.) then it means even though user is not a reporting person of any other person still show them all ARN data." flow="up" style="vertical-align:text-top;"><i class="icons information-icon"></i> <span></span></a></p>
                                    <label class="switch">
                                        <input type="checkbox" id="txt_show_all_arn_data" name="txt_show_all_arn_data" value="1" {{(isset($known_record_data['show_all_arn_data']) && (strtolower($known_record_data['show_all_arn_data']) == 1))?'checked':''}}>
                                        <span class="slider round"></span>
                                    </label><!--/.switch-->
                                    <p class="error">&nbsp;</p>
                                </div><!--/.form-group-->
                            </div><!--/.col-->
                            <div class="col-3">
                                <div class="form-group">
                                    <p><b>Status</b></p>
                                    <label class="switch">
                                        <input type="checkbox" id="txt_status" name="txt_status" value="1" {{(isset($known_record_data['status']) && (strtolower($known_record_data['status']) == 'active'))?'checked':''}}>
                                        <span class="slider round"></span>
                                    </label><!--/.switch-->
                                    <p class="error">&nbsp;</p>
                                </div><!--/.form-group-->
                            </div><!--/.col-->
                        </div><!--/.row-->
                    </div><!--/.col-lg-12-->
                </div><!--/.row-->
                <div class="row">
                    <div class="col-lg-12 border-bottom">
                        <label><b>Permissions</b></label>
                    </div><!--/.col-lg-12-->
                </div><!--/.row-->
                <div class="row">
                    <div class="col-lg-12">
                        <p><input type="checkbox" id="chk_sel_all" name="chk_sel_all" value="1" onchange="check_uncheck_all('chk_sel_all', 'chk_permission[]');" {{(isset($known_record_data['have_all_permissions']) && ($known_record_data['have_all_permissions'] == 1))?'checked':''}}>&nbsp;<label for="chk_sel_all" style="vertical-align:text-bottom;">Select All</label></p>
                        @foreach($arr_permissions_list as $menu_cntr => $menu)
                            @if(isset($menu['shown_in_permission']) && ($menu['shown_in_permission'] == 1))
                                <p>
                                    <input type="checkbox" id="chk_permission[{{$menu_cntr}}]" name="chk_permission[]" value="{{trim($menu['link'], '/')}}" onchange="check_uncheck_all('chk_permission[]', 'chk_sel_all');" {{(isset($known_record_data['permissions']['active']) && array_search(trim($menu['link'], '/'), $known_record_data['permissions']['active']) !== FALSE)?'checked':''}}>&nbsp;<label for="chk_permission[{{$menu_cntr}}]" style="vertical-align:text-bottom;">{{$menu['text']}}</label>
                                </p>
                                @if(isset($menu['extra_permissions']) && is_array($menu['extra_permissions']) && count($menu['extra_permissions']) > 0)
                                    @foreach($menu['extra_permissions'] as $extra_menu_cntr => $extra_menu)
                                        <p>
                                            <input type="checkbox" id="chk_permission[{{$menu_cntr .'_'. $extra_menu_cntr}}]" name="chk_permission[]" value="{{trim($extra_menu['link'], '/')}}" onchange="check_uncheck_all('chk_permission[]', 'chk_sel_all');" {{(isset($known_record_data['permissions']['active']) && array_search(trim($extra_menu['link'], '/'), $known_record_data['permissions']['active']) !== FALSE)?'checked':''}}>&nbsp;<label for="chk_permission[{{$menu_cntr .'_'. $extra_menu_cntr}}]" style="vertical-align:text-bottom;">{{$extra_menu['text']}}</label>
                                        </p>
                                    @endforeach
                                @endif
                            @endif
                        @endforeach
                    </div><!--/.col-lg-12-->
                </div><!--/.row-->
                <div class="row">
                    <div class="col-lg-12">&nbsp;</div><!--/.col-lg-12-->
                </div><!--/.row-->
                <div class="row">
                    <div class="col-lg-12">&nbsp;</div><!--/.col-lg-12-->
                </div><!--/.row-->
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-primary" id="btn_submit" onclick="add_edit_form(this);">Submit</button>
                    </div><!--/.col-->
                    <div class="col text-right">
                        <button type="button" class="btn btn-default" id="btn_back" onclick="window.open('{{$back_page_url}}', '_parent');">Cancel</button>
                    </div><!--/.col-->
                </div><!--/.row-->
            </form><!--/form-->
        </div><!--/.mt-2-->
    </div><!--/.col-lg-12-->
</div><!--/.row-->

@endsection

@section('custom_scripts')

    <script type="text/javascript">
        function add_edit_form(inputObj){
            var formObj = $('#frm_roles');
            var err_flag = false, elementToFocus = null;

            var roleLabelObj = $('[name="txt_label"]');
            if(roleLabelObj.val() == null || roleLabelObj.val() == ''){
                err_flag = true;
                roleLabelObj.next('.error').html('Please enter label for a role');
                if(elementToFocus == '' || elementToFocus == null){
                    elementToFocus = roleLabelObj;
                }
            }
            else if(roleLabelObj.val().toString().length < 3){
                err_flag = true;
                roleLabelObj.next('.error').html('Label should not be less than 3 characters');
                if(elementToFocus == '' || elementToFocus == null){
                    elementToFocus = roleLabelObj;
                }
            }
            else{
                roleLabelObj.next('.error').html('&nbsp;');
            }

            if(!err_flag){
                var inputData = formObj.serializeArray();
                $.ajax({
                    url: baseurl +'/roles/addedit/{{$role_id}}',
                    type: 'POST',
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
                    success: function(response){
                        if(response.err_flag != null && response.err_flag == 1){
                            // coming here in case of error message
                            prepare_error_text(response);
                        }
                        else{
                            // showing a success message
                            swal({
                                title: '',
                                text: response.msg,
                                type: 'success'
                            }).then((value) => {
                                window.location.href = baseurl +'/roles';
                            });
                        }
                    }
                });
            }
            else{
                if(elementToFocus != null && elementToFocus.length > 0){
                    elementToFocus.focus();
                }
            }
        }

        function check_uncheck_all(input_checkbox, output_checkbox){
            if($('[name="'+ input_checkbox +'"]').length == $('[name="'+ input_checkbox +'"]:checked').length){
                $('[name="'+ output_checkbox +'"]').prop('checked', true);
            }
            else{
                $('[name="'+ output_checkbox +'"]').prop('checked', false);
            }
        }

        $(document).ready(function(){
            // checking at page load if Select All checkbox is checked then triggering it's related on change event so that, if any permission checkboxses which were not marked as checked, will get marked as checked
            if($('[name="chk_sel_all"]').is(':checked')){
                check_uncheck_all('chk_sel_all', 'chk_permission[]');
            }
        });
    </script>

@endsection
