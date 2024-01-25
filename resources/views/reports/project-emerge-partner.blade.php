@php
$data_table_headings_html = '';
if(isset($data_table_headings_project_emerge) && is_array($data_table_headings_project_emerge) && count($data_table_headings_project_emerge) > 0){
    foreach($data_table_headings_project_emerge as $key => $value){
        $data_table_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}
@endphp
@extends('../layout')
@section('title', 'Emerging star partners that we have not met in a quarter')
@section('breadcrumb_heading', 'Emerging star partners that we have not met in a quarter')

@section('custom_head_tags')

    <link href="{{asset('css/select2.min.css')}}" rel="stylesheet"/>

@endsection

@section('content')

<div class="row">
    <!--div class="col-md-12">
        <div class="border-bottom display-flex">
            <h2 class="">Meeting Log : View </span></h2>
        </div>
    </div-->
    <div class="col-lg-12">
        <div class="mt-2">
            <table id="panel_meetinglog" class="display" style="width:100%" data-arn_number="{{$arn_number??''}}">
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

    <script src="{{asset('js/select2.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            load_project_focus_datatable();
            // converting HTML dropdown to searchable dropdown
            $(".dropdown-select2").select2();
        });

        var project_emerger_datatable;
        function load_project_focus_datatable(){
            var arn_number = $('#panel_meetinglog').attr('data-arn_number');
            $('#panel_meetinglog thead tr:first th').each( function (idx) {
                var data_column = $(this).attr("data-column"),title = $(this).text();
                if($.inArray(idx, [0]) != -1){
                    if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                        $(this).html('&nbsp;');
                    }
                    else{
                        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                    }
                }
                else if($.inArray(idx, [1,2,3,4,5]) != -1){
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
                else if($.inArray(idx, [9]) != -1){
                    $(this).html( '<select><option value="">All</option><option value="0">No</option><option value="1">Yes</option></select>' );
                }
                else if($.inArray(idx, [6]) != -1){
                    $(this).html( '<select class="dropdown-select2"><option value="">All</option>@foreach($UserDetails as $key => $value)<option value="{{$value['name']}}">{{$value['name']}}</option>@endforeach</select>' );
                }
                else{
                    $(this).html('&nbsp;');
                }
            });

            project_emerger_datatable = $('#panel_meetinglog').DataTable({
                "processing": true,
                "serverSide": true,
                "scrollX": true,
                "ajax": {
                    "url": baseurl +"/report-of-project-emerge-partner",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d){
                        if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                            d.columns[0]['search']['value'] = arn_number;
                            d.exact_arn_match = 1;
                        }
                    }
                },
                "columns": [
                            {"data": "ARN"},
                            {"data": "arn_holders_name"},
                            {"data": "arn_email"},
                            {"data": "arn_telephone_r"},
                            {"data": "arn_telephone_o"},
                            {"data": "arn_city"},
                            {"data": "relationship_mapped_to"},
                            {"data": "last_meeting_date",
                             "render": function (data, type, row, meta) {
                                    var inputColumn = 7, inputValue;
                                    var inputColumnName = meta['settings']['aoColumns'][inputColumn].data;
                                    var inputDateFormat = 'DD/MM/YYYY hh:mm A';
                                    if(row[inputColumnName] != null && row[inputColumnName] != '' && row[inputColumnName] != '0000-00-00'){
                                        return moment(row[inputColumnName]).format(inputDateFormat);
                                    }
                                    else{
                                        return '';
                                    }
                                },
                            },
                            {"data": "total_ind_aum"},
                            {"data": "is_samcomf_partner"},
                            {"data": "samcomf_partner_aum"},
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
            $('#panel_meetinglog_filter').empty();

            project_emerger_datatable.columns().indexes().each(function(idx){
                $('#panel_meetinglog_wrapper table.dataTable thead tr:first th').eq(idx).find('input, select').on('change', function(){
                    var data_column = $(this).closest('th').attr('data-column'), txtSearchedValue = $.trim(this.value);
                    switch(data_column){
                        case 'start_datetime':
                        case 'end_datetime':
                            txtSearchedValue = $.trim($('#from_'+ data_column).val()) +';'+ $.trim($('#to_'+ data_column).val());
                            break;
                    }
                    project_emerger_datatable.column(idx).search(txtSearchedValue).draw();
                });
            });
            $('.dataTables_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data(this);"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
        }

        function export_csv_formatted_data(inputObj){
            var columns = [], known_data_columns = [], formObj = $('#frm_export_data');
            var tableThObj = $('table.dataTable thead tr:first th');
            project_emerger_datatable.columns().indexes().each(function(idx){
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
            formObj.attr({'action': baseurl + '/report-of-project-emerge-partner'});
            formObj.submit();
            formObj.attr({'action':'javascript:void(0);'});
            $('#frm_export_data input[name!="_token"]').remove();
        }

        function clear_date(inputObj, objectIndex, data_table_obj){
            var closestDateObj = $(inputObj).parents().eq(1).find('[type="date"]'), dateObjectID = closestDateObj.attr("id").substr(closestDateObj.attr("id").indexOf("_")+1);
            closestDateObj.val('');
            closestDateObj.trigger('change');
        }
    </script>

@endsection
