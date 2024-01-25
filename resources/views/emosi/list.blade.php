@php
$data_table_median_beer_headings_html = '';
if(isset($data_table_median_beer_headings) && is_array($data_table_median_beer_headings) && count($data_table_median_beer_headings) > 0){
    foreach($data_table_median_beer_headings as $key => $value){
        $data_table_median_beer_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}

$data_table_median_deviation_headings_html = '';
if(isset($data_table_median_deviation_headings) && is_array($data_table_median_deviation_headings) && count($data_table_median_deviation_headings) > 0){
    foreach($data_table_median_deviation_headings as $key => $value){
        $data_table_median_deviation_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}

$data_table_emosi_headings_html = '';
if(isset($data_table_emosi_headings) && is_array($data_table_emosi_headings) && count($data_table_emosi_headings) > 0){
    foreach($data_table_emosi_headings as $key => $value){
        $data_table_emosi_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}

$data_table_kfin_emosi_headings_html = '';
if(isset($data_table_kfin_emosi_headings) && is_array($data_table_kfin_emosi_headings) && count($data_table_kfin_emosi_headings) > 0){
    foreach($data_table_kfin_emosi_headings as $key => $value){
        $data_table_kfin_emosi_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}

@endphp

@extends('../layout')
@section('title', 'EMOSI Data')
@section('breadcrumb_heading', 'EMOSI Data')

@section('custom_head_tags')

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
        .btn-lg {
        font-size: 14px;
        padding: 10px 13px;
        border-radius: 3px;
        float: right;
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
                                                <li class="nav-item active">
                                                    <a class="nav-link" href="#beer_records">BEER Records</a>
                                                </li><!--/.nav-item-->
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#moving_average_records">Moving Average Records</a>
                                                </li><!--/.nav-item-->
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#calculated_emosi_records">EMOSI Records</a>
                                                </li><!--/.nav-item-->
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#kfin_emosi_records">KFIN EMOSI Records</a>
                                                </li><!--/.nav-item-->
                                            </ul><!--/.nav nav-tabs-->
                                            <div class="tab-content data-tabs">
                                                <div class="tab-pane show active tab-list" id="beer_records">
                                                    <div class="row mt-4">
                                                        <div class="col-lg-12">
                                                            <div class="mt-2">
                                                                <table id="panel_table_sm_median_beer" class="display" style="width:100%">
                                                                    <thead>
                                                                        <tr>
                                                                            @php
                                                                                echo $data_table_median_beer_headings_html;
                                                                            @endphp
                                                                        </tr>
                                                                        <tr>
                                                                            @php
                                                                                echo $data_table_median_beer_headings_html;
                                                                            @endphp
                                                                        </tr>
                                                                    </thead>
                                                                    <tfoot>
                                                                        <tr>
                                                                            @php
                                                                                echo $data_table_median_beer_headings_html;
                                                                            @endphp
                                                                        </tr>
                                                                    </tfoot>
                                                                </table><!--#panel_table_sm_median_beer-->
                                                            </div><!--/.mt-2-->
                                                        </div><!--/.col-lg-12-->
                                                    </div><!--/.row mt-4-->
                                                </div><!--#beer_records-->
                                                <div class="tab-pane show tab-list" id="moving_average_records" style="display:none;">
                                                    <div class="row mt-4">
                                                        <div class="col-lg-12">
                                                            <div class="mt-2">
                                                                <table id="panel_table_sm_median_deviation" class="display" style="width:100%">
                                                                    <thead>
                                                                        <tr>
                                                                            @php
                                                                                echo $data_table_median_deviation_headings_html;
                                                                            @endphp
                                                                        </tr>
                                                                        <tr>
                                                                            @php
                                                                                echo $data_table_median_deviation_headings_html;
                                                                            @endphp
                                                                        </tr>
                                                                    </thead>
                                                                    <tfoot>
                                                                        <tr>
                                                                            @php
                                                                                echo $data_table_median_deviation_headings_html;
                                                                            @endphp
                                                                        </tr>
                                                                    </tfoot>
                                                                </table><!--#panel_table_sm_median_deviation-->
                                                            </div><!--/.mt-2-->
                                                        </div><!--/.col-lg-12-->
                                                    </div><!--/.row mt-4-->
                                                </div><!--#moving_average_records-->
                                                <div class="tab-pane show tab-list" id="calculated_emosi_records" style="display:none;">
                                                    <div class="row mt-4">
                                                        <div class="col-lg-12">
                                                            <div class="mt-2">
                                                            <button type="button" id="id_button"class="btn btn-primary btn-lg btn-open-modal" data-toggle="modal" data-target="#exampleModal" data-whatever=" ">Add</button>

                                                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="exampleModalLabel">Emosi Record Insert</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        
                                                                        <div class="modal-body">
                                                                            <form id="emosi_frm" name ="emosi_frm" method = "post"> 
                                                                                 <!-- @php $errors->all(); @endphp; -->
                                                                                <div class="form-group">
                                                                                    <label for="selected_date_value" class="col-form-label">Record Date:</label>
                                                                                    <input type="text" class="form-control" id="record_date" name="record_date" readonly>
                                                                                    <p class="error" id="select_record_date_err"></p>
                                                                                </div>
                                                    
                                                                                <div class="form-group">
                                                                                    <label for="select_bond_value" class="col-form-label">India 10 year Bond G-Sec:</label>
                                                                                    <input type="number" min="0" onkeypress="return event.charCode != 45" class="form-control" id="select_Bond" name="select_Bond" value="{{old('select_Bond')}}">
                                                                                    <p class="error" id="select_Bond_err"></p>
                                                                                </div>

                                                                                <div class="form-group">
                                                                                    <label for="select_nifty_fifty" class="col-form-label">Nifty 50 P/E:</label>
                                                                                    <input type="number" min="0" onkeypress="return event.charCode != 45" class="form-control" id="nifty_fifty" name="nifty_fifty" value="{{old('nifty_fifty')}}">
                                                                                    <p class="error" id="select_Nifty_fifty_err"></p>  
                                                                                </div>

                                                                                <div class="form-group">
                                                                                    <label for="select_nifty_fifty_day" class="col-form-label">Nifty 50 Day Closing Value:</label>
                                                                                    <input type="number" min="0" onkeypress="return event.charCode != 45" class="form-control" id="nifty_fifty_day" name="nifty_fifty_day" value="{{old('nifty_fifty_day')}}">
                                                                                    <p class="error" id="select_Nifty_fifty_day_err"></p>  
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                        
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-primary" id="btn_submit" name = "btn_submit" onclick="myfunction()">Submit</button>
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                                                        </div>
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>

                                                                <table id="panel_table_sm_emosi" class="display" style="width:100%">
                                                                    <thead>
                                                                        <tr>
                                                                            @php
                                                                                echo $data_table_emosi_headings_html;
                                                                            @endphp
                                                                        </tr>
                                                                        <tr>
                                                                            @php
                                                                                echo $data_table_emosi_headings_html;
                                                                            @endphp
                                                                        </tr>
                                                                    </thead>
                                                                    <tfoot>
                                                                        <tr>
                                                                            @php
                                                                                echo $data_table_emosi_headings_html;
                                                                            @endphp
                                                                        </tr>
                                                                    </tfoot>
                                                                </table><!--#panel_table_sm_emosi-->
                                                            </div><!--/.mt-2-->
                                                        </div><!--/.col-lg-12-->
                                                    </div><!--/.row mt-4-->
                                                </div><!--#calculated_emosi_records-->
                                                <div class="tab-pane show tab-list" id="kfin_emosi_records" style="display:none;">
                                                <div class="row mt-4">
                                                    <div class="col-lg-6"></div>
                                                    <div class="col-lg-2">
                                                        <label>From Date</label>
                                                        <input type="date" class="form-control" id="from_date" value="{{date("Y-m-d",(strtotime ( '-30 day' , strtotime ( date("Y-m-d")) ) ))}}" onchange="load_data_table_kfin()"/>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <label>End Date</label>
                                                        <input type="date" class="form-control" id="to_date" value="{{date("Y-m-d")}}" onchange="load_data_table_kfin()"/>
                                                    </div>
                                                    <div class="col-lg-1 text-right" id="div_export_button_kfin_emosi"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="mt-2">
                                                            <table id="panel_table_kfin_emosi" class="display" style="width:100%">
                                                            <thead>
                                                                    <tr>
                                                                        @php
                                                                            echo $data_table_kfin_emosi_headings_html;
                                                                        @endphp
                                                                    </tr>
                                                                </thead>
                                                                <tfoot>
                                                                    <tr>
                                                                        @php
                                                                            echo $data_table_kfin_emosi_headings_html;
                                                                        @endphp
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div><!--#kfin_emosi_records-->
                                            </div><!--/.tab-content data-tabs-->
                                        </div><!--/.tab-content-item-->
                                    </div><!--/.col-md-12-->
                                </div><!--/.row-->
                            </div><!--/.col-md-12-->
                        </div><!--/.row mt-4-->

@endsection

@section('custom_scripts')

    <script type="text/javascript">
        var data_table = {'data_table_median_beer':null, 'data_table_median_deviation':null, 'data_table_emosi':null};
        
        function export_csv_formatted_data(inputObj){
            var columns = [], panel_table_sm = 'panel_table_sm_' + inputObj.toString().replace('data_table_', ''), known_data_columns = [], formObj = $('#frm_export_data');
            var tableThObj = $('#'+ panel_table_sm +'_wrapper').find('table.dataTable thead tr:first th');
            data_table[inputObj].columns().indexes().each(function(idx){
                if(tableThObj.eq(idx).find('input, select').length > 0){
                    tableThObj.eq(idx).find('input, select').each(function(){
                        var data_column = $(this).closest('th').attr('data-column'), txtSearchedValue = $.trim(this.value);
                        switch(data_column){
                            case 'created_at':
                            case 'record_date':
                                if($.trim($('#from_'+ data_column).val()) != '' || $.trim($('#to_'+ data_column).val()) != ''){
                                    txtSearchedValue = $.trim($('#'+ panel_table_sm +'_wrapper').find('#from_'+ data_column).val()) +';'+ $.trim($('#'+ panel_table_sm +'_wrapper').find('#to_'+ data_column).val());
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
            formObj.append('<input type="hidden" name="tab_type" value="'+ inputObj +'">');
            formObj.attr({'action': baseurl + '/emosi-data'});
            formObj.submit();
            formObj.attr({'action':'javascript:void(0);'});
            $('#frm_export_data input[name!="_token"]').remove();
        }

        function clear_date(inputObj, objectIndex, data_table_obj){
            var closestDateObj = $(inputObj).parents().eq(1).find('[type="date"]'), dateObjectID = closestDateObj.attr("id").substr(closestDateObj.attr("id").indexOf("_")+1);
            closestDateObj.val('');
            closestDateObj.trigger('change');
        }

        function load_data_table(inputObj){
            if(data_table[inputObj] == null || typeof data_table[inputObj] == 'undefined'){
                var data_table_columns = [], panel_table_sm = 'panel_table_sm_' + inputObj.toString().replace('data_table_', '');
                $('#'+ panel_table_sm +' thead tr:nth-child(1) th').each(function(idx){
                    var data_column = $(this).attr("data-column"), title = $.trim($(this).text()), txtSearchInput = '', columnDefJSON = {"data":data_column};
                    switch(data_column){
                        case 'created_at':
                        case 'record_date':
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
                                                                    case 'record_date':
                                                                        inputDateFormat = 'DD/MM/YYYY';
                                                                        break;
                                                                }

                                                                if(row[inputColumnName] != null && row[inputColumnName] != '' && row[inputColumnName] != '0000-00-00'){
                                                                    return moment(row[inputColumnName]).format(inputDateFormat);
                                                                }
                                                                else{
                                                                    return '';
                                                                }
                                                            }
                                                });
                            break;
                        case 'action':
                        case 'g_sec_yield':
                        case 'pe':
                        case 'earnings_yield':
                        case 'beer':
                        case 'median_beer':
                        case 'ma_1750':
                        case 'deviation_1750':
                        case 'emosi_median_deviation_from_ma_1750':
                        case 'emosi_value':
                        case 'rounded_emosi':
                        case 'index_value':
                            txtSearchInput = '&nbsp;';
                            if($.inArray(data_column, ['g_sec_yield', 'index_value', 'pe', 'earnings_yield', 'beer', 'median_beer', 'ma_1750', 'deviation_1750', 'emosi_median_deviation_from_ma_1750', 'emosi_value', 'rounded_emosi']) == -1){
                                $.extend(columnDefJSON, {"orderable":false});
                            }
                            if($.inArray(data_column, ['g_sec_yield', 'index_value', 'pe', 'earnings_yield', 'beer', 'median_beer', 'ma_1750', 'deviation_1750', 'emosi_median_deviation_from_ma_1750', 'emosi_value', 'rounded_emosi']) != -1){
                                $.extend(columnDefJSON, {"render": $.fn.dataTable.render.number(',', '.', 2, '')});
                            }
                            break;
                        default:
                            txtSearchInput = '<input type="text" placeholder="'+title+'" />';
                    }
                    if(txtSearchInput != ''){
                        $(this).html(txtSearchInput);
                    }
                    data_table_columns.push(columnDefJSON);
                });

                // Datatable
                data_table[inputObj] = $('#'+ panel_table_sm).DataTable({
                    // "ordering": false,
                    "processing": true,
                    "serverSide": true,
                    "searching": true,
                    "scrollX": true,
                    "ajax": {
                        "url": "{{route('emosi-data')}}",
                        "type": "POST",
                        "data": function(d){
                            d.load_datatable = 1;
                            d.tab_type = inputObj;
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
                    "order": [[ 0, 'desc' ]]
                });

                // removing common "Search Box" which generally getting seen above DataTable.
                $('#'+ panel_table_sm +'_filter').empty();

                // Apply the search
                data_table[inputObj].columns().indexes().each(function(idx){
                    $('#'+ panel_table_sm +'_wrapper').find('table.dataTable thead tr:first th').eq(idx).find('input, select').on('change', function(){
                        var data_column = $(this).closest('th').attr('data-column'), txtSearchedValue = $.trim(this.value);
                        switch(data_column){
                            case 'created_at':
                            case 'record_date':
                                txtSearchedValue = $.trim($('#'+ panel_table_sm +'_wrapper').find('#from_'+ data_column).val()) +';'+ $.trim($('#'+ panel_table_sm +'_wrapper').find('#to_'+ data_column).val());
                                break;
                        }
                        data_table[inputObj].column(idx).search(txtSearchedValue).draw();
                    });
                });

                $('#'+ panel_table_sm +'_wrapper').find('.dataTables_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data(\''+inputObj+'\');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
            }
            else{
                data_table[inputObj].draw();
            }
        }

        $( document ).ready(function() {
            load_data_table('data_table_median_beer');
            // tab click event
            $(".new-tab li a").on("click", function(a) {
                a.preventDefault();
                $(this).parent().addClass("active");
                $(this).parent().siblings().removeClass("active");
                var t = $(this).attr("href").split("#")[1];
                switch(t){
                    case 'beer_records':
                        load_data_table('data_table_median_beer');
                        break;
                    case 'moving_average_records':
                        load_data_table('data_table_median_deviation');
                        break;
                    case 'calculated_emosi_records':
                        load_data_table('data_table_emosi');
                        
                        break;
                    case 'kfin_emosi_records':
                        load_data_table_kfin();
                        break;
                }
                $(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id="' + t + '"]').fadeIn();
                $(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id!="' + t + '"]').hide();
                $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
            });
        });


        $('#exampleModal').on('show.bs.modal', function (event) {
          var button = $(event.relatedTarget) // Button that triggered the modal
          var recipient = button.data('whatever') // Extract info from data-* attributes
          // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
          // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
          var modal = $(this)
          // modal.find('.modal-title').text('Emosi Record Insert '+ recipient )
          modal.find('.modal-body input').val(recipient)
        })


        function myfunction() {
             var frm_data = $("#emosi_frm").serialize();
             let flag=1;
             var selected_date_value = $.trim($("#record_date").val());
             if(selected_date_value == ''){
                $("#select_record_date_err").text("Please select date");
                flag=0;//return false;
             }
             else{
                $("#select_record_date_err").text("");
             }

             var select_bond_value = $.trim($("#select_Bond").val());
             if(select_bond_value == ''){
                $("#select_Bond_err").text("Please Enter bond value");
                flag=0;
             }
             else{
                if(!validateOnlyNumber(select_bond_value)){
                 $("#select_Bond_err").text("Please Enter the valid number"); 
                 flag=0;  
                }else{
                 $("#select_Bond_err").text("");
                }
             }

             var select_nifty_fifty = $.trim($("#nifty_fifty").val());
             if(select_nifty_fifty == ''){
                $("#select_Nifty_fifty_err").text("Please Enter bond nifty fifty");
                flag=0;
             }
             else{
                if(!validateOnlyNumber(select_nifty_fifty)){
                 $("#select_Nifty_fifty_err").text("Please Enter the valid number"); 
                 flag=0; 
                }else{
                 $("#select_Nifty_fifty_err").text("");
                }     
             }

             var select_nifty_fifty_day = $.trim($("#nifty_fifty_day").val());
             if(select_nifty_fifty_day == ''){
                $("#select_Nifty_fifty_day_err").text("Please Enter bond nifty fifty day");
               flag=0;
             }
             else{
                if(!validateOnlyNumber(select_nifty_fifty_day)){
                 $("#select_Nifty_fifty_day_err").text("Please Enter the valid number"); 
                 flag=0;
                 }else{
                  $("#select_Nifty_fifty_day_err").text("");  
                 }
             }


            if(flag)
            {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: baseurl +"/emosi-data_create",
                    data: frm_data, // serializes the form's elements.
                    success: function(result)
                    { 
                       alert(result.msg); // show response from the php script.
                       $('#exampleModal').modal('hide')
                    }
                  
                }); 
            } 
        } 

        function Ajaxfunction(){
            let bt=0;
            let pt=1; 
               var record_date = $('#record_date').val();
               $.ajax({
                type: "POST",
                dataType: 'json',
                url: baseurl +"/ajax_show_data_emosi",
                data: {"record_date" :record_date}, // serializes the form's elements.
                success: function(result)
                {   

                    if(result.select_bond_value==null||result.select_bond_value==undefined)
                    {
                      $('#select_Bond').attr('disabled',false);
                       bt=1; //value of bt
                    }else{
                        $('#select_Bond').val(result.select_bond_value.close);
                        $('#select_Bond').attr('disabled',true);
                       pt=0;
                    }

                    if(result.select_nifty_fifty==null||result.select_nifty_fifty==undefined)
                    {
                      $('#nifty_fifty').attr('disabled',false);                
                       bt=1;
                    }else{
                        $('#nifty_fifty').val(result.select_nifty_fifty.pe);
                        $('#nifty_fifty').attr('disabled',true);
                      pt=0;
                    }

                    if(result.select_nifty_fifty_day==null||result.select_nifty_fifty_day==undefined)
                    {
                      $('#nifty_fifty_day').attr('disabled',false);
                       bt=1;
                    }else{
                        $('#nifty_fifty_day').val(result.select_nifty_fifty_day.close);
                        $('#nifty_fifty_day').attr('disabled',true);
                      pt=0;

                    }

                        if(bt==1){
                            $('#btn_submit').attr('disabled',false);
                         }
                          else if(pt==0 && bt==0){
                            $('#btn_submit').attr('disabled',true);
                         }
                         else{
                            $('#btn_submit').attr('disabled',true);
                        }


                        if(result.status=='error'){
                             //alert(result.msg);
                             $('#btn_submit').attr('disabled',true);
                             $('#select_Bond').attr('disabled',true);
                             $('#nifty_fifty').attr('disabled',true);
                             $('#nifty_fifty_day').attr('disabled',true);
                        }

                    }  
                
                }); 

              }
                

            $("#record_date").datepicker( {
                    format: "yyyy-mm-dd",
                    daysOfWeekDisabled: [0,6],
                    autoclose:true,
                   }).on('show', function() {
                        var already_known_date = $("#record_date").val();
                        window.setTimeout(function(){
                            $("#record_date").val(already_known_date); //update this instance with the current value
                        });
                    }).on('changeDate', function(e) {
                    var dateEntered =  $("#record_date").val();
                    if(!isValidDate(dateEntered)){
                        $('#select_record_date_err').text('Select a Valid Date.')
                    }
                    else{
                        $('#select_record_date_err').html('')
                        Ajaxfunction(); 

                    }
                });

                function isValidDate(dateString) {
                  var regEx = /^\d{4}-\d{2}-\d{2}$/;
                  if(!dateString.match(regEx)) return false;  // Invalid format
                  var d = new Date(dateString);
                  var dNum = d.getTime();
                  if(!dNum && dNum !== 0) return false; // NaN value, Invalid date
                  return d.toISOString().slice(0,10) === dateString;
                }


        var panel_table_kfin_emosi_datatable;
        function load_data_table_kfin(){
            $('#panel_table_kfin_emosi').DataTable().destroy();
            panel_table_kfin_emosi_datatable = $('#panel_table_kfin_emosi').DataTable({
                "processing": true,
                "serverSide": false,
                "scrollX": true,
                "ajax": {
                    "url": baseurl +"/get-kfin-emosi-values",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d){
                        d.start_date = $('#from_date').val();
                        d.end_date = $('#to_date').val();
                    }
                },
                "columns": [
                            {"data": "record_date",
                            "render": function (data, type, row, meta) {
                                    var inputColumn = 0, inputValue;
                                    var inputColumnName = meta['settings']['aoColumns'][inputColumn].data;
                                    var inputDateFormat = 'DD/MM/YYYY';
                                    if(row[inputColumnName] != null && row[inputColumnName] != 'NA' && row[inputColumnName] != '0000-00-00'){
                                        return moment(row[inputColumnName]).format(inputDateFormat);
                                    }
                                    else{
                                        return 'NA';
                                    }
                                },
                            "width": "40%"
                            },
                            {"data": "emosi_value","width": "20%"},
                            {"data": "entdt","width": "20%",
                                "render": function (data, type, row, meta) {
                                    var inputColumn = 2, inputValue;
                                    var inputColumnName = meta['settings']['aoColumns'][inputColumn].data;
                                    var inputDateFormat = 'DD/MM/YYYY hh:mm A';
                                    if(row[inputColumnName] != null && row[inputColumnName] != 'NA' && row[inputColumnName] != '0000-00-00'){
                                        return moment(row[inputColumnName]).format(inputDateFormat);
                                    }
                                    else{
                                        return 'NA';
                                    }
                                },
                            },
                            {"data": "upddt","width": "20%",
                                "render": function (data, type, row, meta) {
                                    var inputColumn = 3, inputValue;
                                    var inputColumnName = meta['settings']['aoColumns'][inputColumn].data;
                                    var inputDateFormat = 'DD/MM/YYYY hh:mm A';
                                    if(row[inputColumnName] != null && row[inputColumnName] != 'NA' && row[inputColumnName] != '0000-00-00'){
                                        return moment(row[inputColumnName]).format(inputDateFormat);
                                    }
                                    else{
                                        return 'NA';
                                    }
                                },
                            },
                        ],
                "language": {
                    "oPaginate": {
                        "sNext": '<i class="icons angle-right"></i>',
                        "sPrevious": '<i class="icons angle-left"></i>',
                        "sFirst": '<i class="icons step-backward"></i>',
                        "sLast": '<i class="icons step-forward"></i>'
                    }
                },
                "order": [
                    [0, 'desc']
                ]
            });

            // removing common "Search Box" which generally getting seen above DataTable.
            $('#panel_table_kfin_emosi_filter').empty();
            $('#div_export_button_kfin_emosi').empty();
            $('#div_export_button_kfin_emosi').append('<a href="javascript:void(0);" title="Export Data" onclick="export_kfin_emosi_csv_formatted_data(this);"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
        }

        function export_kfin_emosi_csv_formatted_data(inputObj){
            var formObj = $('#frm_export_data');
            formObj.append('<input type="hidden" name="start_date" value="'+  $('#from_date').val() +'">');
            formObj.append('<input type="hidden" name="end_date" value="'+  $('#to_date').val() +'">');
            formObj.append('<input type="hidden" name="export_data" value="1">');
            formObj.attr({'action': baseurl + '/get-kfin-emosi-values'});
            formObj.submit();
            formObj.attr({'action':'javascript:void(0);'});
            $('#frm_export_data input[name!="_token"]').remove();
        }
        function validateOnlyNumber(e){
        e = $.trim(e);
        var t = /^[0-9.]+$/;
        var flag = t.test(e) ? 1 : 0;
        return flag;
        }
        // $('#id_button').hide();
    </script>
@endsection