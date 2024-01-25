@php
$data_table_headings_html = '';
if(isset($data_table_headings) && is_array($data_table_headings) && count($data_table_headings) > 0){
    foreach($data_table_headings as $key => $value){
        $data_table_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}

// preparing JSON values which are getting used in JAVASCRIPT for creating dropdown options. STARTS
$status_code = array(array('key' => '', 'value' => 'All'),
                          array('key' => '0', 'value' => 'Inactive'),
                          array('key' => '1', 'value' => 'Active'));
$status_code_json = json_encode($status_code);
$arn_record_status_json = json_encode($role_list);
// preparing JSON values which are getting used in JAVASCRIPT for creating dropdown options. ENDS

// retrieving logged in user role and permission details
if(!isset($logged_in_user_roles_and_permissions)){
    $logged_in_user_roles_and_permissions = array();
}
if(!isset($flag_have_all_permissions)){
    $flag_have_all_permissions = false;
}
@endphp
@extends('../layout')
@section('title', 'User Master List')
@section('breadcrumb_heading', 'User Master List')
@section('custom_head_tags')

  <link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
  <style type="text/css">
   i.material-icons-extended.VfPpkd-kBDsod {
    font-family: 'Material Icons Extended';
    font-weight: normal;
    font-style: normal;
    font-size: 24px;
    line-height: 1;
    letter-spacing: normal;
    text-transform: none;
    display: inline-block;
    white-space: nowrap;
    word-wrap: normal;
    direction: ltr;
    -webkit-font-feature-settings: 'liga';
    -webkit-font-smoothing: antialiased;
   }
   .VfPpkd-Bz112c-Jh9lGc {
    height: 100%;
    left: 0;
    pointer-events: none;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: -1;
   }
   button.VfPpkd-Bz112c-LgbsSe.yHy1rc.eT1oJ {
    display: inline-block;
    position: relative;
    box-sizing: border-box;
    border: none;
    outline: none;
    background-color: transparent;
    fill: currentColor;
    color: inherit;
    text-decoration: none;
    cursor: pointer;
    -webkit-user-select: none;
    z-index: 0;
    overflow: visible;
   }
   span.PpHmke {
    color: #5f6368;
    padding-left: 10px;
   }
   .jSlHNc {
    letter-spacing: .00625em;
    font-family: Roboto,Arial,sans-serif;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5rem;
    align-items: center;
    background-color: #f1f3f4;
    border-radius: 6px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    margin: auto;
    margin-bottom: 10px;
    min-width: 376px;
    padding: 0 4px 0 16px;
    -webkit-user-select: text;
    overflow: auto;
    word-break: break-all;
   }
  </style>

@endsection

@section('content')

<div class="row mt-4">
    <div class="col-lg-12">
       <div class="mt-2">  
          @if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('add-user', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE))
            <button type="button" class="btn btn-primary btn-lg btn-open-modal" data-target="#myModal1"><i class="icons plus-icon"></i>Add</button>
          @endif
        </div>
    </div>
</div>

<div class="row mt-4">

    <div class="col-lg-12">
        <div class="mt-2">
            <table id="panel_table_sm" class="display" style="width:100%">
                <thead>
                    <tr>
                        @php
                            echo $data_table_headings_html;
                        @endphp
                    </tr>
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
</div>

@endsection

@section('custom_after_footer_html')

<!-- Modal -->
      <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
         <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close closed"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Add User</h4>
               </div>
               <div class="modal-body">
                     <div class="mt-2">
                         <form method="POST" action="{{ route('adduser') }}">
                             @csrf
                           <div class="row">
                              <div class="col-lg-12">
                                 <div class="row form-inline">
                                    <div class="col">
                                       <div class="form-group">
                                          <label >Role</label> 
                                          <select class="form-control" id="role_id" name="role_id"required>
                                            <option value="">Select Role</option>
                                            @foreach ($role_list as $list)
                                             <option value="{{ $list->id }}">{{ $list->label }}</option>
                                             
                                             @endforeach
                                          </select>
                                       </div>
                                    </div>
                                    <div class="col">
                                       <div class="form-group">
                                          <label>Name</label> 
                                          <input type="text" class="form-control"  placeholder="Name" required name="uname" id="uname_add" pattern="[A-Za-z ]{4,}">
                                       </div>
                                       <p id="uname_add_e" class=""style="color: red;"></p>
                                    </div>
                                    <div class="col">
                                       <div class="form-group">
                                          <label>Email</label> 
                                          <input type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"placeholder="Email" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,6}" required name="email" id="email">

                                       </div>
                                       <p id="email_e" class=""style="color: red;"></p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-lg-12">
                                 <div class="row form-inline">
                                    <div class="col">
                                       <div class="form-group">
                                          <label >Mobile</label> 
                                          <input type="tel" class="form-control"  placeholder="Mobile" pattern="[1-9][0-9]{9}" required name="mobile_number" id="mobile_number_add">
                                       </div>
                                       <p id="mobile_add_e" class=""style="color: red;"></p>
                                    </div>
                                    <div class="col">
                                       <div class="form-group">
                                          <label>Employee_code</label> 
                                          <input type="text" class="form-control"  placeholder="Employee_code" required name="employee_code"> 
                                       </div>
                                    </div>
                                    <div class="col">
                                       <div class="form-group">
                                          <label>Designation</label> 
                                          <input type="text" class="form-control"  placeholder="Designation" required name="designation">
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                            <div class="row">
                              <div class="col-lg-12">
                                 <div class="row form-inline">
                                    <div class="col">
                                       <span>Reporting to</span> 
                                       <div class="form-group">
                                        
                                        <select id='myselect'class="form-control" name="reporting_to">

                                            
                                        </select>
                                       </div>
                                    </div>
                                    <div class="col">
                                       <div class="form-group">
                                          <label>Cadre_of employee</label> 
                                          <input type="text" class="form-control"  placeholder="Cadre_of employee" pattern="[0-9]{1}" name="cadre_of_employee">
                                       </div>
                                    </div>
                                    <div class="col">
                                       <div class="form-group">
                                          <label>Serviceable pincode</label> 
                                          <textarea class="form-control" rows="2" cols="20" name="serviceable_pincode" placeholder="Serviceable pincode" value=''>
                                          </textarea>
                                          
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>

                           <div class="row">
                              <div class="col-lg-6">
                                 <div class="row form-inline">
                                 <div class="col">
                                       <span>Skip in ARN Mapping</span> 
                                       <div class="form-group">
                                        <select id='myselect'class="form-control" name="skip_in_arn_mapping">
                                           <option value="0">No</option>
                                           <option value="1">Yes</option>
                                        </select>
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
      <!-- Modal edit-->
      <div class="modal fade" id="editmyModal1" tabindex="-1" role="dialog" aria-labelledby="editmyModalLabel">
         <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close closed"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Update User</h4>
               </div>
               <div class="modal-body">
                     <div class="mt-2">
                         <form method="POST" action="{{ route('updateusermasterdetail') }}">
                             @csrf
                           <div class="row">
                              <div class="col-lg-12">
                                 <div class="row form-inline">
                                    <div class="col">
                                       <div class="form-group">
                                          <label >Role</label> 
                                          <select class="form-control" id="role_id_edit" name="role_id"required>
                                            <option value="">Select Role</option>
                                            @foreach ($role_list as $list)
                                             <option value="{{ $list->id }}">{{ $list->label }}</option>
                                             
                                             @endforeach
                                          </select>
                                       </div>
                                    </div>
                                    <div class="col">
                                       <div class="form-group">
                                          <label>Name</label> 
                                          <input type="text" class="form-control"  placeholder="Name" required name="uname" pattern="[A-Za-z ]{4,}" id="uname">
                                       </div>
                                       <p id="uname_edit_e" class=""style="color: red;"></p>
                                    </div>
                                    <div class="col">
                                       <div class="form-group">
                                          <label>Email</label> 
                                          <input type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"placeholder="Email" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,6}" required name="email" id="emaile">
                                         <input type="hidden" class="form-control"  name="ehidden"placeholder="Name" id="ehidden">
                                          <input type="hidden" class="form-control"  name="editid"placeholder="Name" id="editid">
                                          <input type="hidden" class="form-control" name="editidu" placeholder="Name" id="editidu">
                                       </div>
                                       <p id="email_ee" class=""style="color: red;"></p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-lg-12">
                                 <div class="row form-inline">
                                    <div class="col">
                                       <div class="form-group">
                                          <label >Mobile</label> 
                                          <input type="tel" class="form-control"  placeholder="Mobile" pattern="[1-9][0-9]{9}" required name="mobile_number"id="mobile_number">
                                       </div>
                                       <p id="mobile_edit_e" class=""style="color: red;"></p>
                                    </div>
                                    <div class="col">
                                       <div class="form-group">
                                          <label>Employee_code</label> 
                                          <input type="text" class="form-control"  placeholder="Employee_code" required name="employee_code" id="employee_code"> 
                                       </div>
                                    </div>
                                    <div class="col">
                                       <div class="form-group">
                                          <label>Designation</label> 
                                          <input type="text" class="form-control"  placeholder="Designation" required name="designation" id="designation">
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-lg-12">
                                 <div class="row form-inline">

                                    <div class="col">
                                       <span>Reporting to</span> 
                                       <div class="form-group">
                                       
                                        <select id='myselectedit'class="form-control" name="reporting_to_edit">

                                            
                                        </select>
                                       </div>
                                    </div>
                                    <div class="col">
                                       <div class="form-group">
                                          <label>Cadre_of employee</label> 
                                          <input type="text" class="form-control"  placeholder="Cadre_of employee" pattern="[0-9]{1}" name="cadre_of_employee" id="cadre_of_employee_edit">
                                       </div>
                                    </div>
                                    <div class="col">
                                       <div class="form-group">
                                          <label>Serviceable pincode</label> 
                                          <textarea class="form-control" rows="2" cols="20" name="serviceable_pincode"placeholder="Serviceable pincode" id="serviceable_pincode_edit">
                                          </textarea>
                                          
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-lg-12">
                                 <div class="row form-inline">
                                    <div class="col-3">
                                       <div class="form-group">
                                          <label>Status</label> 
                                          <select name="status" id="editstatus" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                          </select>
                                       </div>
                                    </div>
                                    <div class="col-3">
                                       <span>Skip in ARN Mapping</span> 
                                       <div class="form-group">
                                        <select class="form-control" name="skip_in_arn_mapping" id="skip_in_arn_mapping_edit">
                                           <option value="">Select</option>
                                           <option value="0">No</option>
                                           <option value="1">Yes</option>
                                        </select>
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

      <input type="hidden" class="form-control" name="role_id_by" placeholder="Name" id="role_id_by">
      <input type="hidden" class="form-control" name="" placeholder="Name" id="reporting_to_edit">
      <!-- end -->
<!-- Modal service pin-->
      <div class="modal fade" id="servicepinmyModal1" tabindex="-1" role="dialog" aria-labelledby="editmyModalLabel">
         <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close closed"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Serviceable Pincode</h4>
               </div>
               <div class="modal-body">
                     <div class="table-responsive mb-3" id="service_list">
                     </div>
                  </div>
               
            </div>
         </div>
      </div>

<!-- Modal for copy to clipboard-->
<div class="modal fade" id="copyclipboardModal1" tabindex="-1" role="dialog" aria-labelledby="copyclipboardModal1">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="bu totton" class="close closed"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="">Appointment Link</h4>
         </div>
         <div class="modal-body text-center">
               <div class="table-responsive mb-3" id="">
                  <div class="input-group-append mb-3">
                     <div class="jSlHNc">
                        <span class="oXn32">meet.google.com/kxh-pcma-dvd</span>
                        <span class="PpHmke">
                           <button class="VfPpkd-Bz112c-LgbsSe yHy1rc eT1oJ">
                              <div jsname="s3Eaab" class="VfPpkd-Bz112c-Jh9lGc"></div>
                              <i id="copy_appointment_link" class="material-icons-extended VfPpkd-kBDsod"><img src="{{asset('images/content_copy.png')}}" width="25" hight="auto" alt=""></i>
                           </button>
                        </span>
                     </div>
                     <!--input readonly type="text" class="form-control-blue" id="appointment_link_text">
                     <span class="icons search-icon"></span-->
                  </div>
                  <a href="" target="_blank" id="appointment_link" class="btn btn-outline-primary">Open Appointment Link </a>
                  <!--a href="javascript:void(0);" id="copy_appointment_link" class="btn btn-outline-primary">Copy Appointment Link </a-->
               </div>
               <p id="url_text"></p>
            </div>
         
      </div>
   </div>
</div>
@endsection

@section('custom_scripts')

    <script src="{{asset('js/select2.min.js')}}"></script>
    <script type="text/javascript">
    var data_table;
    function export_csv_formatted_data(inputObj){
        var columns = [], known_data_columns = [], formObj = $('#frm_export_data');
        var tableThObj = $('table.dataTable thead tr:first th');
        data_table.columns().indexes().each(function(idx){
            if(tableThObj.eq(idx).find('input, select').length > 0){
                tableThObj.eq(idx).find('input, select').each(function(){
                    var data_column = $(this).closest('th').attr('data-column'), txtSearchedValue = $.trim(this.value);
                    switch(data_column){
                        case 'created_at':
                            if($.trim($('#from_'+ data_column).val()) != '' || $.trim($('#to_'+ data_column).val()) != ''){
                                txtSearchedValue = $.trim($('#from_'+ data_column).val()) +';'+ $.trim($('#to_'+ data_column).val());
                            }
                            break;
                    }

                    if($.inArray(data_column, known_data_columns) == -1){
                        columns.push({'data':data_column, 'search':{'value':txtSearchedValue}});
                        known_data_columns.push(data_column);
                    }
                });
            }
            else{
                columns.push({'data':tableThObj.eq(idx).attr('data-column'), 'search':{'value':''}});
                known_data_columns.push(tableThObj.eq(idx).attr('data-column'));
            }
        });
        formObj.append('<input type="hidden" name="columns" value=\''+ JSON.stringify(columns) +'\'>');
        formObj.append('<input type="hidden" name="export_data" value="1">');
        formObj.append('<input type="hidden" name="load_datatable" value="1">');
        formObj.attr({'action': baseurl + '/usermasterlist'});
        formObj.submit();
        formObj.attr({'action':'javascript:void(0);'});
        $('#frm_export_data input[name!="_token"]').remove();
    }

    function clear_date(inputObj, objectIndex, data_table_obj){
        var closestDateObj = $(inputObj).parents().eq(1).find('[type="date"]'), dateObjectID = closestDateObj.attr("id").substr(closestDateObj.attr("id").indexOf("_")+1);
        closestDateObj.val('');
        closestDateObj.trigger('change');
    }

    $(document).ready(function() {
        
        /*$('#myModal1').on('shown.bs.modal', function () {
            $('#mySelect').select2({
                dropdownParent: $('#myModal1')
            });
            alert(1);
        })*/

        var data_table_columns = [];
        $('#panel_table_sm thead tr:nth-child(1) th').each(function(idx){
            var data_column = $(this).attr("data-column"), title = $.trim($(this).text()), txtSearchInput = '', columnDefJSON = {"data":data_column};
            switch(data_column){
                case 'created_at':
                case 'arn_valid_from':
                case 'arn_valid_till':
                    txtSearchInput  = '<div class="row">'+
                                        '<div class="col-lg-10">'+
                                            '<input type="date" data-from_date="1" id="from_'+ data_column +'" placeholder="From Date">'+
                                        '</div>'+
                                        '<div class="col-lg-2">'+
                                            '<a href="javascript:void(0);" onclick="clear_date(this, '+ idx +', data_table);">X</a>'+
                                        '</div>'+
                                      '</div>'+
                                      '<div class="row"><div class="col-lg-12"> - </div></div>'+
                                      '<div class="row">'+
                                        '<div class="col-lg-10">'+
                                            '<input type="date" data-to_date="1" id="to_'+ data_column +'" placeholder="To Date">'+
                                        '</div>'+
                                        '<div class="col-lg-2">'+
                                            '<a href="javascript:void(0);" onclick="clear_date(this, '+ idx +', data_table);">X</a>'+
                                        '</div>'+
                                      '</div>';
                    $.extend(columnDefJSON, {"render": function (data, type, row, meta) {
                                                        var inputColumn = meta.col, inputValue;
                                                        var inputColumnName = meta['settings']['aoColumns'][inputColumn].data;
                                                        var inputDateFormat = 'DD/MM/YYYY hh:mm:ss a';
                                                        switch(inputColumnName){
                                                            case 'arn_valid_from':
                                                                inputDateFormat = 'DD/MM/YYYY';
                                                                break;
                                                            case 'arn_valid_till':
                                                                inputDateFormat = 'DD/MM/YYYY';
                                                                break;
                                                        }

                                                        if(row[inputColumnName] != null && row[inputColumnName] != ''){
                                                            return moment(row[inputColumnName]).format(inputDateFormat);
                                                        }
                                                        else{
                                                            return '';
                                                        }
                                                    }
                                        });
                    break;
                case 'status':
                var dropdown_filter_options_new;
                    dropdown_filter_options_new = JSON.parse(@json($status_code_json));
                    //console.log(dropdown_filter_options);
                     txtSearchInput += '<option value="">Select status</option>';
                    $.each(dropdown_filter_options_new, function(key, value){
                        txtSearchInput += '<option value="'+ value.key +'">'+ value.value +'</option>';
                    });
                    txtSearchInput = '<select class="">'+ txtSearchInput +'</select>';
                    if($.inArray(data_column, []) == -1){
                        $.extend(columnDefJSON, {"orderable":false});
                    }
                    break;
                case 'role_id':
                    var dropdown_filter_options;
                    dropdown_filter_options = JSON.parse(@json($arn_record_status_json));
                    //console.log(dropdown_filter_options);
                     txtSearchInput += '<option value="">Select Role</option>';
                    $.each(dropdown_filter_options, function(key, value){
                        txtSearchInput += '<option value="'+ value.id +'">'+ value.label +'</option>';
                    });
                    txtSearchInput = '<select class="">'+ txtSearchInput +'</select>';
                    if($.inArray(data_column, []) == -1){
                        $.extend(columnDefJSON, {"orderable":false});
                    }
                    break;
                case 'action':
                case 'serviceable_pincode':
                case 'reporting_to':
                    txtSearchInput = '&nbsp;';
                    $.extend(columnDefJSON, {"orderable":false});
                    break;
                default:
                    txtSearchInput = '<input type="text" placeholder="'+title+'" class="" />';
            }
            if(txtSearchInput != ''){
                $(this).html(txtSearchInput);
            }
            data_table_columns.push(columnDefJSON);
        });
      //console.log(data_table_columns);
        // Datatable
        data_table = $('#panel_table_sm').DataTable({
            // "ordering": false,
            "processing": true,
            "serverSide": true,
            "searching": true,
            "scrollX": true,
            "ajax": {
                "url": baseurl + "/usermasterlist",
                "type": "POST",
                "data": function(d){
                    d.load_datatable = 1;
                },
                "complete": function(){
                    window.setTimeout(function(){
                        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
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
            "order": [[ 1, 'asc' ]]
        });

        // removing common "Search Box" which generally getting seen above DataTable.
        $('#panel_table_sm_filter').empty();

        // Apply the search
        data_table.columns().indexes().each(function(idx){
            $('table.dataTable thead tr:first th').eq(idx).find('input, select').on('change', function(){
                var data_column = $(this).closest('th').attr('data-column'), txtSearchedValue = $.trim(this.value);
                switch(data_column){
                    case 'created_at':
                    case 'arn_valid_from':
                    case 'arn_valid_till':
                        txtSearchedValue = $.trim($('#from_'+ data_column).val()) +';'+ $.trim($('#to_'+ data_column).val());
                        break;
                }
                data_table.column(idx).search(txtSearchedValue).draw();
            });
        });

        $('.dataTables_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data(this);"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
    });
    </script>
    <script>
         // Modal Design  //
         (function($) {
          $('.btn-open-modal').on('click', function() {  
          $("body").addClass("modal-open");            
            var target = $(this).data('target');                 
            $(".modal-ovelay").fadeIn(),          
            $(target).addClass('in');
            $("#myselect").select2({
                 placeholder: "Select Reporting to",
                 allowClear: true
            });
            $(target).show();
          });
         
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
          
         })(jQuery);   

    
    $("#email").focusout(function()
    {
        var email = $("input[name=email]").val();
            $('#email_e').html('');
            if(!validateEmail(email)){
                $('#email_e').html('Enter the valid Email Id');
            }
            $.ajax({
               type:'POST',
               url:"{{ route('checkemail') }}",
               data:{email:email},
               error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                  prepare_error_text(jqXHR.responseJSON);
                }
                else{
                  swal('', unable_to_process_request_text, 'warning');
                }
               },
               success:function(data){
                    if(data.status=='error')
                    {
                      $('#email_e').html('Email Id Already Exists');
                      $(":submit").attr("disabled", true);
                    }else{
                        $(":submit").removeAttr("disabled");
                    }
               }
            });
    });

    $("#uname_add").focusout(function()
    {
        var uname = $(this).val();
            $('#uname_add_e').html('');
            if( $(this).val().trim() == '' ) {
                $('#uname_add_e').html('Name should not be empty');
            }
    });

    $("#uname").focusout(function()
    {
        var uname = $(this).val();
            $('#uname_edit_e').html('');
            if( $(this).val().trim() == '' ) {
                $('#uname_edit_e').html('Name should not be empty');
            }
    });

    $("#mobile_number_add").focusout(function()
    {
        var mobile = $(this).val();
            $('#mobile_add_e').html('');
            if(!validateMobile(mobile)){
                $('#mobile_add_e').html('Enter the valid Mobile No.');
            }
    });

    $("#mobile_number").focusout(function()
    {
        var mobile = $(this).val();
            $('#mobile_edit_e').html('');
            if(!validateMobile(mobile)){
                $('#mobile_edit_e').html('Enter the valid Mobile No.');
            }
    });

    function validateEmail(e) {
        e = $.trim(e);
        var t = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        return t.test(e) ? 1 : 0;
    }

    function validateMobile(e) {
        e = $.trim(e);
        var t = /^[1-9][0-9]{9}$/;
        return t.test(e) ? 1 : 0;
    }

    function edit_code(id){
      $("#myselectedit").select2({
          placeholder: "Select Reporting to",
          allowClear: true
      });
      $.ajax({
         type:'POST',
         url:"{{ route('edit.usermasterdetail') }}",
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
            // console.log(resultdata[0].skip_in_arn_mapping);
            $("#role_id_edit").val(resultdata[0].role_id);
            $("#emaile").val(resultdata[0].email);
            $("#ehidden").val(resultdata[0].email);
            $("#uname").val(resultdata[0].name);
            $('#employee_code').val(resultdata[0].employee_code);
            $('#mobile_number').val(resultdata[0].mobile_number);
            $('#designation').val(resultdata[0].designation);
            $('#editid').val(resultdata[0].id);
            $('#editidu').val(resultdata[0].uid);
            $('#role_id_by').val(resultdata[0].role_id);
            $('#reporting_to_edit').val(resultdata[0].reporting_to);
            $('#cadre_of_employee_edit').val(resultdata[0].cadre_of_employee);
            $('#serviceable_pincode_edit').val(resultdata[0].serviceable_pincode);
            $("#editstatus").val(resultdata[0].status);
            $("#skip_in_arn_mapping_edit").val(resultdata[0].skip_in_arn_mapping);

            var role_id=$("#role_id_by").val();
            $.ajax({
               type:'POST',
               url:"{{ route('get_report_detail') }}",
               data:{role_id:role_id},
               error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                  prepare_error_text(jqXHR.responseJSON);
                }
                else{
                  swal('', unable_to_process_request_text, 'warning');
                }
               },
               success:function(data){
                  $('#myselectedit').html(data);
                  $("#myselectedit").val($("#reporting_to_edit").val());
                  $("body").addClass("modal-open");            
                  //var target = $(this).data('target');                 
                  $(".modal-ovelay").fadeIn();
                  $('#editmyModal1').addClass('in');

                  $('#editmyModal1').show();
               }
            });
         }
      });
    }

 $("#emaile").focusout(function()
    {
        var email = $("#emaile").val();
        var ehidden = $("#ehidden").val();
            $('#email_ee').html('');
            if(!validateEmail(email)){
                $('#email_ee').html('Enter the valid Email Id');
            }
            if(email!=ehidden)
            {
                $.ajax({
                   type:'POST',
                   url:"{{ route('checkemail') }}",
                   data:{email:email},
                   error: function(jqXHR, textStatus, errorThrown){
                    if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                      prepare_error_text(jqXHR.responseJSON);
                    }
                    else{
                      swal('', unable_to_process_request_text, 'warning');
                    }
                   },
                   success:function(data){
                        if(data.status=='error')
                        {
                          $('#email_ee').html('Email Id Already Exists');
                          $(":submit").attr("disabled", true);
                        }else{
                            $(":submit").removeAttr("disabled");
                        }
                   }
                });
            }
    });

$('#role_id').on('change', function() 
{ 
    var role_id=$('#role_id').val();
                $.ajax({
                   type:'POST',
                   url:"{{ route('get_report_detail') }}",
                   data:{role_id:role_id},
                   error: function(jqXHR, textStatus, errorThrown){
                    if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                      prepare_error_text(jqXHR.responseJSON);
                    }
                    else{
                      swal('', unable_to_process_request_text, 'warning');
                    }
                   },
                   success:function(data){
                       $('#myselect').html(data);
                   }
                 });
});
$('#role_id_edit').on('change', function() 
{ 
    var role_id=$('#role_id_edit').val();
                $.ajax({
                   type:'POST',
                   url:"{{ route('get_report_detail') }}",
                   data:{role_id:role_id},
                   error: function(jqXHR, textStatus, errorThrown){
                    if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                      prepare_error_text(jqXHR.responseJSON);
                    }
                    else{
                      swal('', unable_to_process_request_text, 'warning');
                    }
                   },
                   success:function(data){
                       $('#myselectedit').html(data);
                   }
                 });
});
function view_servicecode(id){
     
      $.ajax({
         type:'POST',
         url:"{{ route('get.servicespincode') }}",
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
            $('#service_list').html(data);
            $("body").addClass("modal-open");
            //var target = $(this).data('target');
            $(".modal-ovelay").fadeIn();
            $('#servicepinmyModal1').addClass('in');
            $('#servicepinmyModal1').show();
         }
      });
    }
   function generate_appointment_link(email){
      // console.log(email);
      $.ajax({
         type:'POST',
         url:"{{ route('generate_appointment_list') }}",
         data:{email:email},
         error: function(jqXHR, textStatus, errorThrown){
          if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
            prepare_error_text(jqXHR.responseJSON);
          }
          else{
            swal('', unable_to_process_request_text, 'warning');
          }
         },
         success:function(data){
               // console.log(data);
               if(data.status == 'success'){
                  // window.open(data.appointment_url, '_blank');
                  $(".modal-ovelay").fadeIn(),          
                  $('#copyclipboardModal1').addClass('in');
                  $('#copyclipboardModal1').show();
                  $("#appointment_link").prop("href", data.appointment_url);
                  // $("#appointment_link_text").val(data.appointment_url);
                  $('.oXn32').html(data.appointment_url);
               }
               else if(data.status == 'warning'){
                  swal('', data.message, 'warning');
               }
            }
         });
   }
   function get_appointment_link(){
      $(".modal-ovelay").fadeIn(),          
      $('#copyclipboardModal1').addClass('in');
      $('#copyclipboardModal1').show();
      $("body").on("click",".app_link", function(){
         var link = $(this).attr('appointment_link');
         $("#appointment_link").prop("href", link);
         // $("#appointment_link_text").val(link);
         $('.oXn32').html(link);
      });
   }

   $('body').on('click', '#copy_appointment_link', function() {
      var $temp = $("<input>");
      var $url = $("#appointment_link").prop('href');
      $("body").append($temp);
      $temp.val($url).select();
      document.execCommand("copy");
      $temp.remove();
      $("#url_text").text("URL copied!");
   })
</script>
@endsection
