var aum_transaction_datatable;
function load_aum_transaction_datatable(view_to_be_loaded='year_wise_data', scheme_code='', selected_month = '', selected_day = ''){
    switch(view_to_be_loaded) {
        case 'month_wise_data':
            load_aum_month_wise_data(view_to_be_loaded, scheme_code);
            break;
        case 'day_wise_data':
            load_aum_day_wise_data(view_to_be_loaded, scheme_code,selected_month);
            break;
        case 'date_wise_data':
            load_aum_date_wise_data(view_to_be_loaded, scheme_code,selected_month,selected_day);
            break;
        default:
            load_aum_year_wise_data(view_to_be_loaded);
    }
}

function load_aum_year_wise_data(view_to_be_loaded){
    var arn_number = $('#panel_aum_transaction').attr('data-arn_number');
    var aum_year = $('#aum_year').val();

    aum_transaction_datatable = $('#panel_aum_transaction').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "ajax": {
            "url": baseurl +"/report-of-aum-transaction-analytics",
            "dataType": "json",
            "type": "POST",
            "data": function(d){
                if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                    d.columns[1]['search']['value'] = arn_number;
                    d.exact_arn_match = 1;
                }
                d.aum_year = $('#aum_year').val();
                d.scheme_filter = $('#scheme_filter').val();
                d.view_to_be_loaded = view_to_be_loaded;
            }
        },
        "columns": [{"data": "action", "visible":true,"orderable":false},
                    {"data": "ARN", "visible":false},
                    {"data": "asset_type", "visible":(($('#scheme_filter').val() == 1)?true:false)},
                    {"data": "total_gross_inflow", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_redemptions", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_netflow", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_aum", "orderable":false, "render": $.fn.dataTable.render.number(',', '.', 2, '')},
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
            [2, 'desc']
        ]
    });

    // removing common "Search Box" which generally getting seen above DataTable.
    $('#panel_aum_transaction_filter').empty();
    if(aum_export_permission === true){
        $('#panel_aum_transaction_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data_aum_transaction(\'' + view_to_be_loaded + '\',\'\');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
    }
}

function load_aum_month_wise_data(view_to_be_loaded, scheme_code){
    var arn_number = $('#panel_aum_transaction').attr('data-arn_number');
    var aum_year = $('#aum_year').val();

    var modalObj = $("#view_aum_modal"), modalBodyObj = modalObj.find(".modal-body");
    if(!modalObj.is(":visible")){
        modalObj.modal("show");
    }
    $('#aum_text').text('(Month wise for Year '+aum_year+')');

    var monthwise_table_html = '<table id="panel_aum_transaction_monthwise" class="display" style="width:100%;" data-arn_number="'+ arn_number +'">';
        monthwise_table_html += '<thead>';
        monthwise_table_html += '<tr>';
        monthwise_table_html += '<td>Action</td><td>ARN</td><td>Asset Type</td><td>Month</td><td>Total Gross Inflow</td><td>Total Redemptions</td><td>Total Netflow</td><td>Total AUM</td>';
        monthwise_table_html += '</tr>';
        monthwise_table_html += '</thead>';
    monthwise_table_html += '</table>';
    modalBodyObj.empty();
    modalBodyObj.html(monthwise_table_html);

    $("#back_aum").hide();

    $('#panel_aum_transaction_monthwise').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "ajax": {
            "url": baseurl +"/report-of-aum-transaction-analytics",
            "dataType": "json",
            "type": "POST",
            "data": function(d){
                if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                    d.columns[1]['search']['value'] = arn_number;
                    d.exact_arn_match = 1;
                }
                d.aum_year = $('#aum_year').val();
                d.asset_type = scheme_code;
                d.scheme_filter = $('#scheme_filter').val();
                d.view_to_be_loaded = view_to_be_loaded;
            }
        },
        "columns": [{"data": "action", "visible":true,"orderable":false},
                    {"data": "ARN", "visible":false},
                    {"data": "asset_type","visible":(($('#scheme_filter').val() == 1)?true:false)},
                    {"data": "trdt_month"},
                    {"data": "total_gross_inflow", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_redemptions", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_netflow", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_aum", "orderable":false, "render": $.fn.dataTable.render.number(',', '.', 2, '')},
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
            [3, 'asc']
        ]
    });

    // removing common "Search Box" which generally getting seen above DataTable.
    $('#panel_aum_transaction_monthwise_filter').empty();
    window.setTimeout(function(){
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    }, 200);
    if(aum_export_permission === true){
    $('#panel_aum_transaction_monthwise_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data_aum_transaction(\'' + view_to_be_loaded + '\',\'' + scheme_code + '\');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
    }
}

function load_aum_day_wise_data(view_to_be_loaded, scheme_code,selected_month){
    var arn_number = $('#panel_aum_transaction').attr('data-arn_number');
    var aum_year = $('#aum_year').val();

    var modalObj = $("#view_aum_modal"), modalBodyObj = modalObj.find(".modal-body");
    if(!modalObj.is(":visible")){
        modalObj.modal("show");
    }
    month_text = '';
    if(selected_month != null && typeof selected_month != 'undefined' && selected_month != ''){
        month_text = selected_month.toString().padStart(2, '0');
        month_text = GetMonthName(month_text) + ' ';
    }

    $('#aum_text').html('');
    $('#aum_text').text('(Day wise for Year '+month_text+aum_year+')');

    var daywise_table_html = '<table id="panel_aum_transaction_daywise" class="display" style="width:100%;" data-arn_number="'+ arn_number +'">';
        daywise_table_html += '<thead>';
        daywise_table_html += '<tr>';
        daywise_table_html += '<td>Action</td><td>ARN</td><td>Asset Type</td><td>Date</td><td>Total Gross Inflow</td><td>Total Redemptions</td><td>Total Netflow</td><td>Total AUM</td>';
        daywise_table_html += '</tr>';
        daywise_table_html += '</thead>';
    daywise_table_html += '</table>';
    modalBodyObj.empty();
    modalBodyObj.html(daywise_table_html);
    $("#back_aum").show();
    $('#back_aum').attr('onclick',"load_aum_month_wise_data('month_wise_data','"+scheme_code+"')");

    $('#panel_aum_transaction_daywise').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "ajax": {
            "url": baseurl +"/report-of-aum-transaction-analytics",
            "dataType": "json",
            "type": "POST",
            "data": function(d){
                if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                    d.columns[1]['search']['value'] = arn_number;
                    d.exact_arn_match = 1;
                }
                d.aum_year = $('#aum_year').val();
                d.asset_type = scheme_code;
                d.aum_month = selected_month;
                d.scheme_filter = $('#scheme_filter').val();
                d.view_to_be_loaded = view_to_be_loaded;
            }
        },
        "columns": [{"data": "action", "visible":true,"orderable":false},
                    {"data": "ARN", "visible":false},
                    {"data": "asset_type",visible:(($('#scheme_filter').val() == 1)?true:false)},
                    {"data": "trdt_date"},
                    {"data": "total_gross_inflow", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_redemptions", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_netflow", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "total_aum", "orderable":false, "render": $.fn.dataTable.render.number(',', '.', 2, '')},
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
    $('#panel_aum_transaction_daywise_filter').empty();
    window.setTimeout(function(){
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    }, 200);
    if(aum_export_permission === true){
    $('#panel_aum_transaction_daywise_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data_aum_transaction(\'' + view_to_be_loaded + '\',\'' + scheme_code + '\',\'' + selected_month + '\');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
    }
}

function load_aum_date_wise_data(view_to_be_loaded, scheme_code,selected_month,selected_day){
    selected_month = selected_month.toString().padStart(2, '0');
    selected_day = selected_day.toString().padStart(2, '0');
    var arn_number = $('#panel_aum_transaction').attr('data-arn_number');
    var aum_year = $('#aum_year').val();

    var modalObj = $("#view_aum_modal"), modalBodyObj = modalObj.find(".modal-body");
    if(!modalObj.is(":visible")){
        modalObj.modal("show");
    }

    month_text = selected_month.toString().padStart(2, '0');
    day_text = selected_day.toString().padStart(2, '0');
    $('#aum_text').html('');
    $('#aum_text').text('(Date wise for Year '+aum_year+'-'+month_text+'-'+day_text+')');
    var datewise_table_html = '';
    datewise_table_html += '<div class="row">';
        datewise_table_html += '<div class="col-lg-6">';
            datewise_table_html += '<input type="text" placeholder="Investor Search by Name,folio" id="global_search" onchange="load_aum_datewise()" class="form-control">';
        datewise_table_html += '</div>';
        datewise_table_html += '<div class="col-lg-6">';
            datewise_table_html += '<select id="order_type" class="form-control" onchange="load_aum_datewise()">';
                datewise_table_html += '<option value="">Select Transaction Type</option>';
                $.each(investor_transactions_type, function(key, value){
                    datewise_table_html += '<option value="'+ key +'">'+ value +'</option>';
                });
            datewise_table_html += '</select>';
        datewise_table_html += '</div>';
    datewise_table_html += '</div>';
    datewise_table_html += '<div class="row"><div class="col-lg-12">&nbsp;</div></div>';
    datewise_table_html += '<div class="row">';
        datewise_table_html += '<div class="col-lg-12">';
            datewise_table_html += '<table id="panel_aum_transaction_datewise" class="display" style="width:100%;" data-arn_number="'+ arn_number +'">';
                datewise_table_html += '<thead>';
                datewise_table_html += '<tr>';
                datewise_table_html += '<td>Action</td><td>Scheme</td><td>Folio</td><td>Investor</td><td>Trx Type</td><td>Order Type</td><td>Units</td><td>Amount</td><td>NAV</td><td>Created</td>';
                //<td>Status</td>
                datewise_table_html += '</tr>';
                datewise_table_html += '</thead>';
            datewise_table_html += '</table>';
        datewise_table_html += '</div>';
    datewise_table_html += '</div>';
    modalBodyObj.empty();
    modalBodyObj.html(datewise_table_html);

    $('#back_aum').attr('onclick',"load_aum_day_wise_data('day_wise_data','"+scheme_code+"','"+selected_month+"')");

    aum_datewise_transaction_datatable = $('#panel_aum_transaction_datewise').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "ajax": {
            "url": baseurl +"/report-of-aum-transaction-analytics",
            "dataType": "json",
            "type": "POST",
            "data": function(d){
                if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                    d.arn_number = arn_number;
                    d.exact_arn_match = 1;
                }
                d.asset_type = scheme_code;
                d.selected_date = $('#aum_year').val() +'-'+ selected_month +'-'+ selected_day;
                d.scheme_filter = $('#scheme_filter').val();
                d.view_to_be_loaded = view_to_be_loaded;
                d.global_search = $('#global_search').val();
                d.order_type = $('#order_type').val();
            }
        },
        "columns": [{"data": "action", "visible":false,"orderable":false},
                    {"data": "schemename"},
                    {"data": "folio_number"},
                    {"data": "clientname"},
                    {"data": "trxn_type_name"},
                    {"data": "sub_trxntype_name"},
                    {"data": "units"},
                    {"data": "amount"},
                    {"data": "nav"},
                    {"data": "trdt_date"},
                    //{"data": "order_response"},
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
            [1, 'desc']
        ]
    });

    // removing common "Search Box" which generally getting seen above DataTable.
    $('#panel_aum_transaction_datewise_filter').empty();
    window.setTimeout(function(){
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    }, 200);
    if(aum_export_permission === true){
    $('#panel_aum_transaction_datewise_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data_aum_transaction(\'' + view_to_be_loaded + '\',\'' + scheme_code + '\',\'' + selected_month + '\',\'' + selected_day + '\');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
    }
}

function GetMonthName(monthNumber) {

    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
    return months[monthNumber - 1];

}

//loading datatable onchanging a year
function load_aum_year(){
    aum_transaction_datatable.draw();
    aum_transaction_datatable.column(2).visible((($('#scheme_filter').val() == 1)?true:false));
}

function load_aum_datewise(){
    aum_datewise_transaction_datatable.draw();
}

function export_csv_formatted_data_aum_transaction(view_to_be_loaded,scheme_code,selected_month,selected_day){
    var formObj = $('#frm_export_data');
    var tableThObj = $('table.dataTable thead tr:first th');
    var arn_number = $('#panel_aum_transaction').attr('data-arn_number');
    var global_search = $('#global_search').val();
    var order_type = $('#order_type').val();
    formObj.append('<input type="hidden" name="ARN" value="'+arn_number+'">');
    formObj.append('<input type="hidden" name="arn_number" value="'+arn_number+'">');
    formObj.append('<input type="hidden" name="asset_type" value="'+scheme_code+'">');
    if(selected_month != null && typeof selected_month != 'undefined' && selected_month != ''){
        formObj.append('<input type="hidden" name="aum_month" value="'+selected_month+'">');
    }
    if(selected_day != null && typeof selected_day != 'undefined' && selected_day != ''){
        formObj.append('<input type="hidden" name="selected_date" value="'+$('#aum_year').val() +'-'+ selected_month +'-'+ selected_day+'">');
    }
    if(global_search != null && typeof global_search != 'undefined' && global_search != ''){
        formObj.append('<input type="hidden" name="global_search" value="'+global_search+'">');
    }
    if(order_type != null && typeof order_type != 'undefined' && order_type != ''){
        formObj.append('<input type="hidden" name="order_type" value="'+order_type+'">');
    }
    formObj.append('<input type="hidden" name="scheme_filter" value="'+$('#scheme_filter').val()+'">');
    formObj.append('<input type="hidden" name="aum_year" value="'+$('#aum_year').val()+'">');
    formObj.append('<input type="hidden" name="exact_arn_match" value="1">');
    formObj.append('<input type="hidden" name="view_to_be_loaded" value="'+view_to_be_loaded+'">');
    formObj.append('<input type="hidden" name="export_data" value="1">');
    formObj.append('<input type="hidden" name="load_datatable" value="1">');
    formObj.append('<input type="hidden" name="pagination_required" value="0">');
    formObj.append('<input type="hidden" name="get_all_arn_data" value="1">');
    formObj.attr({'action': baseurl + '/report-of-aum-transaction-analytics'});
    formObj.submit();
    formObj.attr({'action':'javascript:void(0);'});
    $('#frm_export_data input[name!="_token"]').remove();
}