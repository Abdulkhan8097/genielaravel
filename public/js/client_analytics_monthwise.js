var client_monthwise_analytics_datatable;
function load_client_monthwise_analytics_datatable(view_to_be_loaded='year_wise_data', scheme_code='', selected_month = '', selected_day = ''){
    switch(view_to_be_loaded) {
        case 'month_wise_data':
            load_client_month_wise_data(view_to_be_loaded,scheme_code);
            break;
        case 'day_wise_data':
            load_sip_day_wise_data(view_to_be_loaded,selected_month);
            break;
        case 'date_wise_data':
            load_aum_date_wise_data(view_to_be_loaded,selected_month,selected_day);
            break;
        default:
            load_client_monthwise_year_wise_data(view_to_be_loaded,scheme_code);
    }
}

function load_client_monthwise_year_wise_data(view_to_be_loaded,scheme_code){
    var arn_number = $('#panel_client_analytics').attr('data-arn_number');
    var client_year = $('#client_year').val();

    var modalObj = $("#view_client_modal"), modalBodyObj = modalObj.find(".modal-body");
    if(!modalObj.is(":visible")){
        modalObj.modal("show");
    }
    $("#back_client").hide();
    $('#client_text').text('(Month wise for Year '+client_year+')');

    var monthwise_table_html = '<table id="panel_client_monthwise_analytics" class="display" style="width:100%;" data-arn_number="'+ arn_number +'">';
        monthwise_table_html += '<thead>';
        monthwise_table_html += '<tr>';
        monthwise_table_html += '<td>Action</td><td>ARN</td><td>Month</td><td>Asset Type</td><td>Active Clients with AUM<br>(as on month)</td><td>New Clients with AUM<br>(in that month)</td><td>Clients without AUM<br>(as on month)</td>';
        monthwise_table_html += '</tr>';
        monthwise_table_html += '</thead>';
    monthwise_table_html += '</table>';
    modalBodyObj.empty();
    modalBodyObj.html(monthwise_table_html);
	// debugger;
    client_monthwise_analytics_datatable = $('#panel_client_monthwise_analytics').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        lengthMenu: [
            [12],
            [12],
        ],
        "ajax": {
            "url": baseurl +"/report-of-client-monthwise-analytics",
            "dataType": "json",
            "type": "POST",
            "data": function(d){
                if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                    d.columns[1]['search']['value'] = arn_number;
                    d.exact_arn_match = 1;
                }
                // d.asset_type_filter = scheme_code;
                d.asset_type_filter = $('#client_scheme_filter').val();
				d.asset_type = scheme_code;
                d.client_year = $('#client_year').val();
                d.view_to_be_loaded = view_to_be_loaded;
            }
        },
        "columns": [{"data": "action", "visible":false,"orderable":false},
                    {"data": "ARN", "visible":false,"orderable":false},
                    {"data": "m1"},
					{"data": "asset_type","visible":(($('#client_scheme_filter').val() == 1)?true:false)},
                    {"data": "active_clients_with_aum"},
                    {"data": "new_clients_with_aum"},
                    {"data": "clients_without_aum"},
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
            [2, 'asc']
        ]
    });

    // removing common "Search Box" which generally getting seen above DataTable.
    $('#panel_client_monthwise_analytics_filter').empty();
    window.setTimeout(function(){
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    }, 200);
    if(client_export_permission === true){
    $('#panel_client_monthwise_analytics_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data_client_monthwise_analytics(\'' + view_to_be_loaded + '\',\'' + scheme_code + '\');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
    }

}

//loading datatable onchanging a year
function load_client_monthwise_year(){
   client_monthwise_analytics_datatable.draw();
}

function export_csv_formatted_data_client_monthwise_analytics(view_to_be_loaded,scheme_code){
    var columns = [];
    var formObj = $('#frm_export_data');
    var arn_number = $('#panel_client_analytics').attr('data-arn_number');
    columns.push({'data':'ARN', 'search':{'value':arn_number}});

    formObj.append('<input type="hidden" name="columns" value=\''+ JSON.stringify(columns) +'\'>');
    formObj.append('<input type="hidden" name="asset_type" value="'+scheme_code+'">');
    formObj.append('<input type="hidden" name="asset_type_filter" value="'+$('#client_scheme_filter').val()+'">');
    formObj.append('<input type="hidden" name="client_year" value="'+$('#client_year').val()+'">');
    formObj.append('<input type="hidden" name="exact_arn_match" value="1">');
    formObj.append('<input type="hidden" name="view_to_be_loaded" value="'+view_to_be_loaded+'">');
    formObj.append('<input type="hidden" name="export_data" value="1">');
    formObj.append('<input type="hidden" name="load_datatable" value="1">');
    formObj.attr({'action': baseurl + '/report-of-client-monthwise-analytics'});
    formObj.submit();
    formObj.attr({'action':'javascript:void(0);'});
    $('#frm_export_data input[name!="_token"]').remove();
}