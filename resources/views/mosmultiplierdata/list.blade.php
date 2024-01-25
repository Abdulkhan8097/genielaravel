@php
$data_table_headings_html = '';
if(isset($data_table_headings) && is_array($data_table_headings) && count($data_table_headings) > 0){
    foreach($data_table_headings as $key => $value){
        $data_table_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}
// preparing JSON values which are getting used in JAVASCRIPT for creating dropdown options. ENDS
@endphp

@extends('../layout')
@section('title', 'Mos Multiplier Data List')
@section('breadcrumb_heading', 'MOS Multiplier Data List')

@section('content')

<style type="text/css">
    .display-flexdv{display: flex; align-items: center;}
    .display-flexdv label{margin-right: 10px;}
</style>

<div class="row mt-4">

<div class="col-lg-9">
@if($flag_have_all_permissions || (isset($logged_in_user_roles_and_permissions['role_permissions']) && is_array($logged_in_user_roles_and_permissions['role_permissions']) && in_array('mos_multiplier_data_add', $logged_in_user_roles_and_permissions['role_permissions']) !== FALSE))   
<a type="button" class="btn btn-primary btn-lg btn-open-modal" href="{{url('mos_multiplier_data_add')}}"><i class="icons plus-icon"></i>Add</a>
@endif
</div>

<div class="col-lg-3">
   <div class="display-flexdv form-group">
    
      <label>Multiplier</label> 
      <select name="multiplier" id="multiplier" class="form-control">
        <option selected value="all">All</option>
        @foreach($arr_multiplier_type as $value)
        <option value="{{$value}}">{{$value}}</option>
        @endforeach
      </select>
   </div>
</div>

  
        <div class="col-lg-12 mt-2">
            <table id="mos_id" class="display" style="width:100%">
                <thead>
                    <tr>
                        @php
                            echo $data_table_headings_html;
                        @endphp
                    </tr>
                    <!-- <tr>
                        @php
                            echo $data_table_headings_html;
                        @endphp
                    </tr> -->
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
@section('custom_scripts')
<script>
var data_table;
    $( document ).ready(function() {
        $('#multiplier').on('change',function(){
            data_table.draw();
        });
       var data_table_columns = [];
       $('#mos_id thead tr:nth-child(1) th').each(function(idx) {
        var data_column = $(this).attr("data-column"),
            title = $.trim($(this).text()),
            columnDefJSON = {
                "data": data_column
            };

         data_table_columns.push(columnDefJSON);
      });
       data_table = $('#mos_id').DataTable({
        // "ordering": false,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "scrollX": true,
        "ajax": {
            "url": baseurl + "/mos_multiplier_data",
            "type": "POST",
            "data": function(d) {
                d.load_datatable = 1;
                d.multiplier =$('#multiplier').val();
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
    });

    function Deletemyfunction(id){
        // alert();
      if(confirm("Are You Sure to delete this?")){
        myfunction(id);
      }
    }

     function myfunction(id) {
        $.ajax({
            url:  baseurl + "/mos_multiplier_data_delete",
            type: 'POST',
            dataType: 'json',
            data:{"id":id},
            success: function (data) {
                console.log(data);
                if(data.status == 'success'){
                     alert(data.messages);
                    data_table.draw();
                }else{
                    alert('somethimg went wrong');
                }
                
            }
        });
    }
    
    $(".alert").delay(3000).slideUp(200, function(){
    $(this).alert('close');
   });

</script>
@endsection






