@php
$data_table_headings_html = '';
if(isset($data_table_headings) && is_array($data_table_headings) && count($data_table_headings) > 0){
    foreach($data_table_headings as $key => $value){
        $data_table_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}
@endphp

@extends('../layout')
@section('title', 'Appointments')
@section('breadcrumb_heading', 'Appointments Log')

@section('custom_head_tags')

@endsection

@section('content')

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

@section('custom_scripts')
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
                        case 'start_datetime':
                        case 'end_datetime':
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
        formObj.attr({'action': baseurl + '/appointment'});
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
        var data_table_columns = [];
        $('#panel_table_sm thead tr:nth-child(1) th').each(function(idx){
            var data_column = $(this).attr("data-column"), title = $.trim($(this).text()), txtSearchInput = '', columnDefJSON = {"data":data_column};
            switch(data_column){
                case 'start_datetime':
                case 'end_datetime':
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
                                                        // switch(inputColumnName){
                                                        //     case 'arn_valid_from':
                                                        //         inputDateFormat = 'DD/MM/YYYY';
                                                        //         break;
                                                        //     case 'arn_valid_till':
                                                        //         inputDateFormat = 'DD/MM/YYYY';
                                                        //         break;
                                                        // }

                                                        if(row[inputColumnName] != null && row[inputColumnName] != ''){
                                                            return moment(row[inputColumnName]).format(inputDateFormat);
                                                        }
                                                        else{
                                                            return '';
                                                        }
                                                    }
                                        });
                    break;
                case 'action':
                case 'notes':
                case 'customer_address':
                case 'customer_city':
                case 'customer_state':
                case 'customer_zipcode':
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
      
        // Datatable
        data_table = $('#panel_table_sm').DataTable({
            // "ordering": false,
            "processing": true,
            "serverSide": true,
            "searching": true,
            "scrollX": true,
            "ajax": {
                "url": baseurl + "/appointment",
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
            "order": [[ 3, 'desc' ]]
        });

        // removing common "Search Box" which generally getting seen above DataTable.
        $('#panel_table_sm_filter').empty();

        // Apply the search
        data_table.columns().indexes().each(function(idx){
            $('table.dataTable thead tr:first th').eq(idx).find('input, select').on('change', function(){
                var data_column = $(this).closest('th').attr('data-column'), txtSearchedValue = $.trim(this.value);
                switch(data_column){
                    case 'start_datetime':
                    case 'end_datetime':
                        txtSearchedValue = $.trim($('#from_'+ data_column).val()) +';'+ $.trim($('#to_'+ data_column).val());
                        break;
                }
                data_table.column(idx).search(txtSearchedValue).draw();
            });
        });

        $('.dataTables_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data(this);"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
    });
</script>
@endsection
