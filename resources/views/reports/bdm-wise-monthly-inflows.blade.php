@php
$data_table_headings_html = '';
if(isset($data_table_headings_bdmwise_inflows) && is_array($data_table_headings_bdmwise_inflows) && count($data_table_headings_bdmwise_inflows) > 0){
    foreach($data_table_headings_bdmwise_inflows as $key => $value){
        $data_table_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}
@endphp
@extends('../layout')
@section('title', 'Month-wise BDM wise Flows')
@section('breadcrumb_heading', 'Month-wise BDM wise Flows')

@section('content')
<div class="row mt-4">
    <div class="col-lg-7"></div>
    <div class="col-lg-2">
        <label><b>Select Scheme</b></label>
        <select id="selected_scheme" class="form-control" onchange="load_datatable()">
            <option value="" data-is_nfo_scheme="0">All Schemes</option>
            @if(isset($arr_schemes) && is_array($arr_schemes) && count($arr_schemes) > 0)
            @foreach($arr_schemes as $scheme_record)
            <option value="{{$scheme_record['Scheme_Code']}}" data-is_nfo_scheme="{{(($scheme_record['SETTLEMENT_TYPE']??'') == 'MF')?1:0}}">{{$scheme_record['scheme']}}</option>
            @endforeach
            @endif
        </select>
    </div>
    <div class="col-lg-2">
        <label><b>Month Selection</b></label>
        <input type="month" class="form-control" id="month_wise" value="{{date("Y-m")}}" onchange="load_datatable()"/> 
    </div>
    <div class="col-lg-1 text-right" id="div_export_button"></div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="mt-2">
            <table id="panel_monthiwse_bdm_inflows" class="display" style="width:100%">
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
<!--/.row mt-4-->

@endsection

@section('custom_after_footer_html')

@endsection

@section('custom_scripts')

    <script type="text/javascript">
        $(document).ready(function() {
            load_monthwise_bdmwise_inflows();
        });

        var panel_monthwise_bdm_inflows_datatable;
        function load_monthwise_bdmwise_inflows(){
            var data_table_columns = [];
            var arn_number = $('#panel_monthiwse_bdm_inflows').attr('data-arn_number');
            $('#panel_monthiwse_bdm_inflows thead tr:first th').each( function (idx) {
                var data_column = $(this).attr("data-column"), title = $.trim($(this).text()), txtSearchInput = '', columnDefJSON = {"data":data_column};
                switch(data_column){
                    case 'bdm_name':
                    case 'reporting_manager':
                        txtSearchInput = '<input type="text" placeholder="'+title+'" />';
                        break;
                    default:
                        txtSearchInput = '&nbsp;';
                        if($.inArray(data_column, ['number_of_arn_mapped', 'number_of_arn_empanelled']) != -1){
                            $.extend(columnDefJSON, {"render": $.fn.dataTable.render.number(',', '.', 0, '')});
                        }
                        else if($.inArray(data_column, ['sip_gross_inflow_till_date', 'otherthan_sip_gross_inflow_till_date', 'total_gross_inflow_till_date', 'gross_redemptions_till_date', 'net_inflow_till_date', 'net_inflow_financial_year_till_date', 'net_inflow_current_quarter_till_date']) != -1){
                            $.extend(columnDefJSON, {"render": $.fn.dataTable.render.number(',', '.', 2, '')});
                        }
                        else{
                            $.extend(columnDefJSON, {"orderable":false});
                        }
                }
                if(txtSearchInput != ''){
                    $(this).html(txtSearchInput);
                }
                data_table_columns.push(columnDefJSON);
            });

            panel_monthwise_bdm_inflows_datatable = $('#panel_monthiwse_bdm_inflows').DataTable({
                "processing": true,
                "serverSide": true,
                "scrollX": true,
                "ajax": {
                    "url": baseurl +"/report-of-monthwise-bdmwise-inflows",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d){
                        d.month_wise = $("#month_wise").val() +'-01';
                        d.selected_scheme = $("#selected_scheme").val();
                        d.is_nfo_scheme = $("#selected_scheme").find("option:selected").attr("data-is_nfo_scheme");
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
                    [0, 'asc']
                ]
            });

            // removing common "Search Box" which generally getting seen above DataTable.
            $('#panel_monthiwse_bdm_inflows_filter').empty();

            panel_monthwise_bdm_inflows_datatable.columns().indexes().each(function(idx){
                $('#panel_monthiwse_bdm_inflows_wrapper table.dataTable thead tr:first th').eq(idx).find('input, select').on('change', function(){
                    var data_column = $(this).closest('th').attr('data-column'), txtSearchedValue = $.trim(this.value);
                    switch(data_column){
                        case 'start_datetime':
                        case 'end_datetime':
                            txtSearchedValue = $.trim($('#from_'+ data_column).val()) +';'+ $.trim($('#to_'+ data_column).val());
                            break;
                    }
                    panel_monthwise_bdm_inflows_datatable.column(idx).search(txtSearchedValue).draw();
                });
            });
            $('#div_export_button').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data(this);"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
        }

        function export_csv_formatted_data(inputObj){
            var columns = [], known_data_columns = [], formObj = $('#frm_export_data');
            var tableThObj = $('table.dataTable thead tr:first th');
            var arn_number = '';
            panel_monthwise_bdm_inflows_datatable.columns().indexes().each(function(idx){
                if(tableThObj.eq(idx).find('input, select').length > 0){
                    tableThObj.eq(idx).find('input, select').each(function(){
                        var data_column = $(this).closest('th').attr('data-column'), txtSearchedValue = $.trim(this.value);
                        switch(data_column){
                            case 'start_datetime':
                            case 'end_datetime':
                                if($.trim($('#from_'+ data_column).val()) != '' || $.trim($('#to_'+ data_column).val()) != ''){
                                    txtSearchedValue = $.trim($('#from_'+ data_column).val()) +';'+ $.trim($('#to_'+ data_column).val());
                                }
                                break;
                            case 'ARN':
                                arn_number = txtSearchedValue;
                                break;
                        }

                        if(data_column != null && data_column != '' && $.inArray(data_column, known_data_columns) == -1){
                            columns.push({'data':data_column, 'search':{'value':txtSearchedValue}});
                            known_data_columns.push(data_column);
                        }
                    });
                }
                else{
                    if(tableThObj.eq(idx).attr('data-column') != null && typeof tableThObj.eq(idx).attr('data-column') != 'undefined' && tableThObj.eq(idx).attr('data-column') != ''){
                        columns.push({'data':tableThObj.eq(idx).attr('data-column'), 'search':{'value':''}});
                        known_data_columns.push(tableThObj.eq(idx).attr('data-column'));
                    }
                }
            });
            formObj.append('<input type="hidden" name="columns" value=\''+ JSON.stringify(columns) +'\'>');
            formObj.append('<input type="hidden" name="month_wise" value="'+ $.trim($("#month_wise").val() +'-01') +'">');
            formObj.append('<input type="hidden" name="selected_scheme" value="'+ $.trim($("#selected_scheme").val()) +'">');
            formObj.append('<input type="hidden" name="is_nfo_scheme" value="'+ $("#selected_scheme").find("option:selected").attr("data-is_nfo_scheme") +'">');
            formObj.append('<input type="hidden" name="export_data" value="1">');
            formObj.append('<input type="hidden" name="load_datatable" value="1">');
            formObj.attr({'action': baseurl + '/report-of-monthwise-bdmwise-inflows'});
            formObj.submit();
            formObj.attr({'action':'javascript:void(0);'});
            $('#frm_export_data input[name!="_token"]').remove();
        }

        function clear_date(inputObj, objectIndex, data_table_obj){
            var closestDateObj = $(inputObj).parents().eq(1).find('[type="date"]'), dateObjectID = closestDateObj.attr("id").substr(closestDateObj.attr("id").indexOf("_")+1);
            closestDateObj.val('');
            closestDateObj.trigger('change');
        }

        //loading datatable onchanging a scheme and year
        function load_datatable(){
           panel_monthwise_bdm_inflows_datatable.draw();
        }
    </script>

@endsection
