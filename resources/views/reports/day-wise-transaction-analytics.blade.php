@php
$data_table_headings_html = '';
if(isset($data_table_headings_daywise_transactions_analytics) && is_array($data_table_headings_daywise_transactions_analytics) && count($data_table_headings_daywise_transactions_analytics) > 0){
    foreach($data_table_headings_daywise_transactions_analytics as $key => $value){
        $data_table_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}
@endphp
@extends('../layout')
@section('title', 'Daywise Transactions Analytics')
@section('breadcrumb_heading', 'Daywise Transactions Analytics')

@section('content')
<div class="row mt-4">
    <div class="col-lg-7"></div>
    <div class="col-lg-2 text-right">
        <select class="form-control" id="scheme_filter" name="scheme_filter" onchange="load_datatable()">
            <option value="0">All</option>
            <option value="1">Scheme Wise</option>
        </select>
    </div>
    <div class="col-lg-2 text-right">
        <input type="date" class="form-control" id="date_wise" value="{{date("Y-m-d",(strtotime ( '-1 day' , strtotime ( date("Y-m-d")) ) ))}}" onchange="load_datatable()"/> 
    </div>
    <div class="col-lg-1 text-right" id="div_export_button"></div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="mt-2">
            <table id="panel_daywise_transaction" class="display" style="width:100%">
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
            load_project_focus_datatable();
        });

        var panel_daywise_transaction_datatable;
        function load_project_focus_datatable(){
            var arn_number = $('#panel_daywise_transaction').attr('data-arn_number');
            $('#panel_daywise_transaction thead tr:first th').each( function (idx) {
                var data_column = $(this).attr("data-column"),title = $(this).text();
                if($.inArray(idx, [0]) != -1){
                    if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                        $(this).html('&nbsp;');
                    }
                    else{
                        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                    }
                }
                else{
                    $(this).html('&nbsp;');
                }
            });

            panel_daywise_transaction_datatable = $('#panel_daywise_transaction').DataTable({
                "processing": true,
                "serverSide": true,
                "scrollX": true,
                "ajax": {
                    "url": baseurl +"/report-of-daywise-transaction-analytics",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d){
                        d.scheme_filter = $('#scheme_filter').val();
                        d.date_filter = $('#date_wise').val();
                    }
                },
                "columns": [
                            {"data": "agent_code"},
                            {"data": "arn_holder_name"},
                            {"data": "scheme_name", "visible":(($('#scheme_filter').val() == 1)?true:false)},
                            {"data": "total_netflow", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                            {"data": "total_gross_inflow", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                            {"data": "total_redemptions", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                            {"data": "total_aum", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                            {"data": "lumpsum_purchases", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                            {"data": "sip_purchases", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                            {"data": "total_purchases"},
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
                    [0, 'asc']
                ]
            });

            // removing common "Search Box" which generally getting seen above DataTable.
            $('#panel_daywise_transaction_filter').empty();

            panel_daywise_transaction_datatable.columns().indexes().each(function(idx){
                $('#panel_daywise_transaction_wrapper table.dataTable thead tr:first th').eq(idx).find('input, select').on('change', function(){
                    var data_column = $(this).closest('th').attr('data-column'), txtSearchedValue = $.trim(this.value);
                    switch(data_column){
                        case 'start_datetime':
                        case 'end_datetime':
                            txtSearchedValue = $.trim($('#from_'+ data_column).val()) +';'+ $.trim($('#to_'+ data_column).val());
                            break;
                    }
                    panel_daywise_transaction_datatable.column(idx).search(txtSearchedValue).draw();
                });
            });
            $('#div_export_button').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data(this);"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
        }

        function export_csv_formatted_data(inputObj){
            var columns = [], known_data_columns = [], formObj = $('#frm_export_data');
            var tableThObj = $('table.dataTable thead tr:first th');
            var arn_number = '';
            panel_daywise_transaction_datatable.columns().indexes().each(function(idx){
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
            formObj.append('<input type="hidden" name="arn_number" value="'+ arn_number +'">');
            formObj.append('<input type="hidden" name="exact_arn_match" value="1">');
            formObj.append('<input type="hidden" name="selected_date" value="'+ $.trim($('#date_wise').val()) +'">');
            formObj.append('<input type="hidden" name="scheme_filter" value="0">');
            formObj.append('<input type="hidden" name="view_to_be_loaded" value="date_wise_data">');
            formObj.append('<input type="hidden" name="pagination_required" value="0">');
            formObj.append('<input type="hidden" name="get_all_arn_data" value="1">');
            formObj.append('<input type="hidden" name="export_data" value="1">');
            formObj.append('<input type="hidden" name="load_datatable" value="1">');
            formObj.attr({'action': baseurl + '/report-of-aum-transaction-analytics'});
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
           panel_daywise_transaction_datatable.draw();
           panel_daywise_transaction_datatable.column(2).visible((($('#scheme_filter').val() == 1)?true:false));
        }
    </script>

@endsection
