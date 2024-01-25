var sip_analytics_datatable;
function load_sip_analytics_datatable(view_to_be_loaded='year_wise_data', scheme_code='', selected_month = '', selected_day = ''){
    switch(view_to_be_loaded) {
        case 'month_wise_data':
            load_sip_month_wise_data(view_to_be_loaded,scheme_code);
            break;
        case 'day_wise_data':
            load_sip_day_wise_data(view_to_be_loaded,scheme_code,selected_month);
            break;
        case 'date_wise_data':
            load_aum_date_wise_data(view_to_be_loaded,selected_month,selected_day);
            break;
        default:
            load_sip_year_wise_data(view_to_be_loaded);
    }
}

function load_sip_year_wise_data(view_to_be_loaded){
    var arn_number = $('#panel_sip_analytics').attr('data-arn_number');
    var sip_year = $('#sip_year').val();

    sip_analytics_datatable = $('#panel_sip_analytics').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "ajax": {
            "url": baseurl +"/report-of-sip-analytics",
            "dataType": "json",
            "type": "POST",
            "data": function(d){
                if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                    d.columns[1]['search']['value'] = arn_number;
                    d.exact_arn_match = 1;
                }
                d.sip_year = $('#sip_year').val();
                d.asset_filter = $('#sip_scheme_filter').val();
                d.columns[3]['search']['value'] = 'active';
                d.view_to_be_loaded = view_to_be_loaded;
            }
        },
        "columns": [{"data": "action", "visible":true,"orderable":false},
                    {"data": "ARN", "visible":false},
                    {"data": "asset_type","visible":(($('#sip_scheme_filter').val() == 1)?true:false)},
                    {"data": "order_status"},
                    {"data": "installment_amount", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "no_of_sip"},
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
    $('#panel_sip_analytics_filter').empty();
    if(sip_export_permission === true){
    $('#panel_sip_analytics_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data_sip_analytics(\'' + view_to_be_loaded + '\');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
    }
}

function load_sip_month_wise_data(view_to_be_loaded,scheme_code){
    var arn_number = $('#panel_sip_analytics').attr('data-arn_number');
    var sip_year = $('#sip_year').val();

    var modalObj = $("#view_sip_modal"), modalBodyObj = modalObj.find(".modal-body");
    if(!modalObj.is(":visible")){
        modalObj.modal("show");
    }
    $('#sip_text').text('(Month wise for Year '+sip_year+')');

    var monthwise_table_html = '<table id="panel_sip_analytics_monthwise" class="display" style="width:100%;" data-arn_number="'+ arn_number +'">';
        monthwise_table_html += '<thead>';
        monthwise_table_html += '<tr>';
        monthwise_table_html += '<td>Action</td><td>ARN</td><td>Scheme Name</td><td>SIP Registration Month</td><td>SIP Registration Amount</td><td>No of SIP</td><td>SIP Live Amount</td><td>No of Live SIP</td><td>SIP Closure Amount</td><td>No of Closed SIP</td>';
        monthwise_table_html += '</tr>';
        monthwise_table_html += '</thead>';
    monthwise_table_html += '</table>';
    modalBodyObj.empty();
    modalBodyObj.html(monthwise_table_html);
    $("#back_sip").hide();

    $('#panel_sip_analytics_monthwise').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
		"pageLength": '25',
        "ajax": {
            "url": baseurl +"/report-of-sip-analytics",
            "dataType": "json",
            "type": "POST",
            "data": function(d){
                if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                    d.columns[1]['search']['value'] = arn_number;
                    d.exact_arn_match = 1;
                }
                d.sip_year = $('#sip_year').val();
                d.scheme_filter = $('#sip_scheme_filter').val();
                d.asset_type = scheme_code;
                d.view_to_be_loaded = view_to_be_loaded;
            }
        },
        "columns": [{"data": "action", "visible":true,"orderable":false},
                    {"data": "ARN", "visible":false},
                    {"data": "scheme_name","visible":(($('#sip_scheme_filter').val() == 1)?true:false)},
                    {"data": "sip_registration_month"},
                    {"data": "sip_registration_amount", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "no_of_sip"},
                    {"data": "sip_live_amount", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "no_of_live_sip"},
                    // {"data": "sip_pending_registration_amount", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    // {"data": "no_of_pending_registration_sip"},
                    {"data": "sip_closures_amount", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "no_of_closed_sip"},
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
    $('#panel_sip_analytics_monthwise_filter').empty();
    window.setTimeout(function(){
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    }, 200);
    if(sip_export_permission === true){
    $('#panel_sip_analytics_monthwise_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data_sip_analytics(\'' + view_to_be_loaded + '\',\'' + scheme_code + '\');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
    }
}

function load_sip_day_wise_data(view_to_be_loaded,scheme_code,selected_month){
    var arn_number = $('#panel_sip_analytics').attr('data-arn_number');
    var sip_year = $('#sip_year').val();

    var modalObj = $("#view_sip_modal"), modalBodyObj = modalObj.find(".modal-body");
    if(!modalObj.is(":visible")){
        modalObj.modal("show");
    }
    month_text = '';
    if(selected_month != null && typeof selected_month != 'undefined' && selected_month != ''){
        month_text = selected_month.toString().padStart(2, '0');
        month_text = GetMonthName(month_text) + ' ';

        $("#back_sip").show();
        $('#back_sip').attr('onclick',"load_sip_month_wise_data('month_wise_data','"+scheme_code+"')");
    }
    else{
        $("#back_sip").hide();
    }

    $('#sip_text').html('');
    $('#sip_text').text('(Day wise for Year '+month_text+sip_year+')');


    var daywise_table_html = '';
    daywise_table_html += '<div class="row">';
        daywise_table_html += '<div class="col-lg-4">';
            daywise_table_html += '<input type="text" placeholder="Investor Search by Name" id="search_name" onchange="load_sip_datewise()" class="form-control">';
        daywise_table_html += '</div>';
        daywise_table_html += '<div class="col-lg-4">';
            daywise_table_html += '<input type="text" placeholder="Investor Search by PAN" id="search_pan" onchange="load_sip_datewise()" class="form-control">';
        daywise_table_html += '</div>';
        daywise_table_html += '<div class="col-lg-4">';
            daywise_table_html += '<select id="search_status" onchange="load_sip_datewise()" class="form-control">';
            daywise_table_html += '<option value="">Select Status</option><option value="active">Active SIP</option><option value="rejected">Rejected Sip</option><option value="cancelled">Cancelled Sip</option></select>';
        daywise_table_html += '</div>';
    daywise_table_html += '</div>';
    daywise_table_html += '<div class="row"><div class="col-lg-12">&nbsp;</div></div>';
    daywise_table_html += '<div class="row">';
        daywise_table_html += '<div class="col-lg-12">';
            daywise_table_html += '<table id="panel_sip_analytics_daywise" class="display" style="width:100%;" data-arn_number="'+ arn_number +'">';
            daywise_table_html += '<thead>';
            daywise_table_html += '<tr>';
            daywise_table_html += '<td>Action</td><td>ARN</td><td>Scheme Name</td><td>Client Name</td><td>PAN</td><td>SIP Registered Since</td><td>UMRN Code</td><td>Current AUM</td><td>SIP Amount</td><td>Status</td>';
            daywise_table_html += '</tr>';
            daywise_table_html += '</thead>';
        daywise_table_html += '</table>';
        daywise_table_html += '</div>';
    daywise_table_html += '</div>';
    modalBodyObj.empty();
    modalBodyObj.html(daywise_table_html);

    sip_daywise_transaction_datatable = $('#panel_sip_analytics_daywise').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "ajax": {
            "url": baseurl +"/report-of-sip-analytics",
            "dataType": "json",
            "type": "POST",
            "data": function(d){
                if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                    d.columns[1]['search']['value'] = arn_number;
                    d.exact_arn_match = 1;
                }
                d.sip_year = $('#sip_year').val();
                d.scheme_filter = $('#sip_scheme_filter').val();
                d.asset_type = scheme_code;
                d.sip_month = selected_month;
                d.view_to_be_loaded = view_to_be_loaded;
                d.search_pan = $('#search_pan').val();
                d.search_name = $('#search_name').val();
                d.search_status = $('#search_status').val();
            }
        },
        "columns": [{"data": "action", "visible":false,"orderable":false},
                    {"data": "ARN", "visible":false},
                    {"data": "scheme_name","visible":true},
                    {"data": "client_name"},
                    {"data": "pan"},
                    {"data": "sip_registered_since"},
                    {"data": "umrncode"},
                    {"data": "client_aum", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "sip_amount", "render": $.fn.dataTable.render.number(',', '.', 2, '')},
                    {"data": "sip_status"},
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
            [5, 'desc']
        ]
    });

    // removing common "Search Box" which generally getting seen above DataTable.
    $('#panel_sip_analytics_daywise_filter').empty();

    window.setTimeout(function(){
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    }, 200);
    if(sip_export_permission === true){
    $('#panel_sip_analytics_daywise_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data_sip_analytics(\'' + view_to_be_loaded + '\',\'' + scheme_code + '\',\'' + selected_month + '\');"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
    }
}

//loading datatable onchanging a year
function load_sip_year(){
    sip_analytics_datatable.draw();
    sip_analytics_datatable.column(2).visible((($('#sip_scheme_filter').val() == 1)?true:false));
}

function load_sip_datewise(){
    sip_daywise_transaction_datatable.draw();
}

function GetMonthName(monthNumber) {

    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
    return months[monthNumber - 1];

}

function export_csv_formatted_data_sip_analytics(view_to_be_loaded,scheme_code,selected_month,selected_day){
    var columns = [];
    var formObj = $('#frm_export_data');
    var arn_number = $('#panel_sip_analytics').attr('data-arn_number');
    var search_pan = $('#search_pan').val();
    var search_status = $('#search_status').val();
    var search_name = $('#search_name').val();
    columns.push({'data':'ARN', 'search':{'value':arn_number}});
    if(view_to_be_loaded == 'year_wise_data'){
        columns.push({'data':'order_status', 'search':{'value':'active'}});
    }
    formObj.append('<input type="hidden" name="columns" value=\''+ JSON.stringify(columns) +'\'>');
    if(scheme_code != null && typeof scheme_code != 'undefined' && scheme_code != ''){
        formObj.append('<input type="hidden" name="asset_type" value="'+scheme_code+'">');
    }
    if(selected_month != null && typeof selected_month != 'undefined' && selected_month != ''){
        formObj.append('<input type="hidden" name="sip_month" value="'+selected_month+'">');
    }
    if(selected_day != null && typeof selected_day != 'undefined' && selected_day != ''){
        formObj.append('<input type="hidden" name="selected_date" value="'+$('#sip_year').val() +'-'+ selected_month +'-'+ selected_day+'">');
    }
    if(search_pan != null && typeof search_pan != 'undefined' && search_pan != ''){
        formObj.append('<input type="hidden" name="search_pan" value="'+search_pan+'">');
    }
    if(search_status != null && typeof search_status != 'undefined' && search_status != ''){
        formObj.append('<input type="hidden" name="search_status" value="'+search_status+'">');
    }
    if(search_name != null && typeof search_name != 'undefined' && search_name != ''){
        formObj.append('<input type="hidden" name="search_name" value="'+search_name+'">');
    }
    formObj.append('<input type="hidden" name="asset_filter" value="'+$('#sip_scheme_filter').val()+'">');
    formObj.append('<input type="hidden" name="sip_year" value="'+$('#sip_year').val()+'">');
    formObj.append('<input type="hidden" name="exact_arn_match" value="1">');
    formObj.append('<input type="hidden" name="view_to_be_loaded" value="'+view_to_be_loaded+'">');
    formObj.append('<input type="hidden" name="export_data" value="1">');
    formObj.append('<input type="hidden" name="load_datatable" value="1">');
    formObj.attr({'action': baseurl + '/report-of-sip-analytics'});
    formObj.submit();
    formObj.attr({'action':'javascript:void(0);'});
    $('#frm_export_data input[name!="_token"]').remove();
}

