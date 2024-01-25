var client_analytics_datatable, client_register_datatable;
function load_client_analytics_datatable(view_to_be_loaded='year_wise_data', scheme_code='', selected_month = '', selected_type = ''){
    switch(view_to_be_loaded) {
        case 'month_wise_data':
            load_client_month_wise_data(view_to_be_loaded,scheme_code,selected_month,selected_type);
            break;
        case 'day_wise_data':
            load_sip_day_wise_data(view_to_be_loaded,selected_month);
            break;
        case 'date_wise_data':
            load_aum_date_wise_data(view_to_be_loaded,selected_month,selected_day);
            break;
        default:
            load_client_year_wise_data(view_to_be_loaded);
    }
}

function load_client_year_wise_data(view_to_be_loaded){
    var arn_number = $('#panel_client_analytics').attr('data-arn_number');
    var client_year = $('#client_year').val();

    client_analytics_datatable = $('#panel_client_analytics').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "ajax": {
            "url": baseurl +"/report-of-client-analytics",
            "dataType": "json",
            "type": "POST",
            "data": function(d){
                if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                    d.columns[1]['search']['value'] = arn_number;
                    d.exact_arn_match = 1;
                }
                d.asset_type_filter = $('#client_scheme_filter').val();
                d.client_year = $('#client_year').val();
                d.view_to_be_loaded = view_to_be_loaded;
            }
        },
        "columns": [{"data": "action", "visible":true,"orderable":false},
                    {"data": "ARN", "visible":false},
                    {"data": "asset_type","visible":(($('#client_scheme_filter').val() == 1)?true:false)},
                    // {"data": "no_of_clients"},
                    {"data": "total_gross_inflow", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_redemptions", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_netflow", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_aum", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
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
            [3, 'desc']
        ]
    });

    // removing common "Search Box" which generally getting seen above DataTable.
    $('#panel_client_analytics_filter').empty();
    if(client_export_permission === true){
    $('#panel_client_analytics_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data_client_analytics(\'' + view_to_be_loaded + '\');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
    }
}

function load_client_month_wise_data(view_to_be_loaded,scheme_code,selected_month,selected_type){
    var arn_number = $('#panel_client_analytics').attr('data-arn_number');
    var client_year = $('#client_year').val();

    var modalObj = $("#view_client_modal"), modalBodyObj = modalObj.find(".modal-body");
    if(!modalObj.is(":visible")){
        modalObj.modal("show");
    }

    $("#back_client").show();
    $('#back_client').attr('onclick',"load_client_monthwise_analytics_datatable('year_wise_data','"+scheme_code+"')");
    
    if(selected_month != null && typeof selected_month != 'undefined' && selected_month != ''){
        month_text = 'till '+GetMonthName(selected_month)+'-';
    }
    else{
        month_text = 'for year';
    }

    $('#client_text').text('(Client Register  '+month_text+' '+client_year+')');

    var monthwise_table_html = '';
    monthwise_table_html += '<div class="row">';
        monthwise_table_html += '<div class="col-lg-4">';
            monthwise_table_html += '<input type="text" placeholder="Investor Search by Name" id="search_name" onchange="load_client_register_data(3, this)" class="form-control">';
        monthwise_table_html += '</div>';
        monthwise_table_html += '<div class="col-lg-4">';
            monthwise_table_html += '<input type="text" placeholder="Investor Search by PAN" id="search_pan" onchange="load_client_register_data(4, this)" class="form-control">';
        monthwise_table_html += '</div>';
        monthwise_table_html += '<div class="col-lg-4">';
        monthwise_table_html += '<select id="search_type" onchange="load_client_register_type()" class="form-control">';
        monthwise_table_html += '<option value="">Select Type</option><option value="active_clients_with_aum">Active Client with AUM</option><option value="new_clients_with_aum">New Clients with AUM</option><option value="clients_without_aum">Clients Without AUM</option></select>';
        monthwise_table_html += '</div>';
    monthwise_table_html += '</div>';
    monthwise_table_html += '<div class="row"><div class="col-lg-12">&nbsp;</div></div>';
    monthwise_table_html += '<div class="row">';
        monthwise_table_html += '<div class="col-lg-12">';
            monthwise_table_html += '<table id="panel_client_analytics_monthwise" class="display" style="width:100%;" data-arn_number="'+ arn_number +'">';
                monthwise_table_html += '<thead>';
                monthwise_table_html += '<tr>';
                monthwise_table_html += '<td>Action</td><td>ARN</td><td>Asset Type</td><td>Client</td><td>PAN</td><td>Active SIP Registration Amount</td><td>Total Gross Inflow</td><td>Total Redemptions</td><td>Total Net Inflow</td><td>Total AUM</td><td>Last Transaction Date</td>';
                monthwise_table_html += '</tr>';
                monthwise_table_html += '</thead>';
            monthwise_table_html += '</table>';
        monthwise_table_html += '</div>';
    monthwise_table_html += '</div>';
    modalBodyObj.empty();
    modalBodyObj.html(monthwise_table_html);

    modalBodyObj.find("#search_type").val(selected_type);

    client_register_datatable = $('#panel_client_analytics_monthwise').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "ajax": {
            "url": baseurl +"/report-of-client-analytics",
            "dataType": "json",
            "type": "POST",
            "data": function(d){
                if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                    d.columns[1]['search']['value'] = arn_number;
                    d.exact_arn_match = 1;
                }
                d.arn_month_wise = arn_number;
                d.asset_type_filter = $('#client_scheme_filter').val();
                d.client_register_year = $('#client_year').val();
                d.asset_type = scheme_code;
                d.client_register_month = selected_month;
                d.types_of_client = modalBodyObj.find("#search_type").val();
                d.view_to_be_loaded = view_to_be_loaded;
            }
        },
        "columns": [{"data": "action", "visible":false,"orderable":false},
                    {"data": "ARN", "visible":false},
                    {"data": "asset_type","visible":(($('#client_scheme_filter').val() == 1)?true:false)},
                    {"data": "clientname"},
                    {"data": "pan"},
                    {"data": "active_sip_registration_amount", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_gross_inflow", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_redemptions", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_netflow", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_aum", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "last_transaction_date"},
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
            [3, 'desc']
        ]
    });

    // removing common "Search Box" which generally getting seen above DataTable.
    $('#panel_client_analytics_monthwise_filter').empty();
    window.setTimeout(function(){
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    }, 200);
    if(client_export_permission === true){
    $('#panel_client_analytics_monthwise_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data_client_analytics(\'' + view_to_be_loaded + '\',\'' + scheme_code + '\',\'' + selected_month + '\',\'' + selected_type + '\');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
    }
}

//loading datatable onchanging a year
function load_client_year(){
   client_analytics_datatable.draw();
   client_analytics_datatable.column(2).visible((($('#client_scheme_filter').val() == 1)?true:false));
}

function load_client_register_data(col_index, txtInput){
    txtSearchedValue = $.trim($(txtInput).val());
    if(client_register_datatable != null && typeof client_register_datatable != 'undefined' && txtSearchedValue != null){
        client_register_datatable.column(col_index).search(txtSearchedValue).draw();
    }
}

function load_client_register_type(){
    if(client_register_datatable != null && typeof client_register_datatable != 'undefined'){
        client_register_datatable.draw();
    }
}

function GetMonthName(monthNumber) {

    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
    return months[monthNumber - 1];

}

function export_csv_formatted_data_client_analytics(view_to_be_loaded,scheme_code,selected_month,selected_type){
    var columns = [];
    var formObj = $('#frm_export_data');
    var arn_number = $('#panel_client_analytics').attr('data-arn_number');
    var search_pan = $('#search_pan').val();
    var search_name = $('#search_name').val();
    var selected_type = $('#search_type').val();
    columns.push({'data':'ARN', 'search':{'value':arn_number}});
    if(search_pan != null && typeof search_pan != 'undefined' && search_pan != ''){
        columns.push({'data':'pan', 'search':{'value':search_pan}});
    }
    if(search_name != null && typeof search_name != 'undefined' && search_name != ''){
        columns.push({'data':'clientname', 'search':{'value':search_name}});
    }
    formObj.append('<input type="hidden" name="columns" value=\''+ JSON.stringify(columns) +'\'>');
    if(scheme_code != null && typeof scheme_code != 'undefined' && scheme_code != ''){
        formObj.append('<input type="hidden" name="asset_type" value="'+scheme_code+'">');
    }
    if(selected_month != null && typeof selected_month != 'undefined' && selected_month != ''){
        formObj.append('<input type="hidden" name="client_register_month" value="'+selected_month+'">');
    }
	if(selected_month != null && typeof selected_month != 'undefined' && selected_month != ''){
        formObj.append('<input type="hidden" name="client_year" value="'+selected_month+'">');
    }
    if(selected_type != null && typeof selected_type != 'undefined' && selected_type != ''){
        formObj.append('<input type="hidden" name="types_of_client" value="'+selected_type+'">');
    }
    formObj.append('<input type="hidden" name="ARN" value="'+arn_number+'">');
    formObj.append('<input type="hidden" name="asset_type_filter" value="'+$('#client_scheme_filter').val()+'">');
	formObj.append('<input type="hidden" name="asset_type" value="'+scheme_code+'">');
    formObj.append('<input type="hidden" name="client_year" value="'+$('#client_year').val()+'">');
	formObj.append('<input type="hidden" name="client_register_month" value="'+selected_month+'">');
	formObj.append('<input type="hidden" name="types_of_client" value="'+selected_type+'">');
    formObj.append('<input type="hidden" name="exact_arn_match" value="1">');
    formObj.append('<input type="hidden" name="view_to_be_loaded" value="'+view_to_be_loaded+'">');
    formObj.append('<input type="hidden" name="export_data" value="1">');
    formObj.append('<input type="hidden" name="load_datatable" value="1">');
    formObj.attr({'action': baseurl + '/report-of-client-analytics'});
    formObj.submit();
    formObj.attr({'action':'javascript:void(0);'});
    $('#frm_export_data input[name!="_token"]').remove();
}
