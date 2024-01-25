var meetinglog_datatable;
function load_meetinglog_datatable(){
    var arn_number = $('#panel_meetinglog').attr('data-arn_number');
    $('#panel_meetinglog thead tr:first th').each( function (idx) {
        var data_column = $(this).attr("data-column"),title = $(this).text();
        if($.inArray(idx, [1]) != -1){
            if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                $(this).html('&nbsp;');
            }
            else{
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            }
        }
        else if($.inArray(idx, [2,4, 17]) != -1){
            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
        }
        else if($.inArray(idx, [5, 6,19,20]) != -1){
            $(this).html( '<div class="row"><div class="col-lg-10"><input type="date" data-from_date="1" id="from_'+ data_column +'" placeholder="From Date"></div><div class="col-lg-2"><a href="javascript:void(0);" onclick="clear_date(this, '+ idx +', meetinglog_datatable);">X</a></div></div><div class="row"><div class="col-lg-12"> - </div></div><div class="row"><div class="col-lg-10"><input type="date" data-to_date="1" id="to_'+ data_column +'" placeholder="To Date"></div><div class="col-lg-2"><a href="javascript:void(0);" onclick="clear_date(this, ' +idx +', meetinglog_datatable);">X</a></div></div>' );
        }
        else if($.inArray(idx, [ 8,9,11,14]) != -1){
            $(this).html( '<select><option value="">All</option><option value="0">No</option><option value="1">Yes</option></select>' );
        }
        else{
            $(this).html('&nbsp;');
        }
    });

    meetinglog_datatable = $('#panel_meetinglog').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "ajax": {
            "url": baseurl +"/meetinglog",
            "dataType": "json",
            "type": "POST",
            "data": function(d){
                if(arn_number != null && typeof arn_number != 'undefined' && arn_number != ''){
                    d.columns[1]['search']['value'] = arn_number;
                    d.exact_arn_match = 1;
                }
            }
        },
        "columns": [{"data": "action", "orderable":false},
					{"data": "bdm_name"},
                    {"data": "ARN"},
                    {"data": "meeting_mode"},
                    {"data": "contact_person_name"},
                    {"data": "start_datetime",
                     "render": function (data, type, row, meta) {
                            var inputColumn = 5, inputValue;
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
                    {"data": "end_datetime",
                     "render": function (data, type, row, meta) {
                            var inputColumn = 6, inputValue;
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
                    {"data": "meeting_hour", "orderable":false},
                    {"data": "email_sent_to_customer"},
                    {"data": "sms_sent_to_customer"},
                    {"data": "customer_response_received_datetime"},
                    {"data": "customer_response_received"},
                    {"data": "customer_given_rating"},
                    {"data": "customer_remarks"},
                    // {"data": "product_information_received"},
                    {"data": "is_rankmf_partner"},
                    {"data": "total_ind_aum"},
                    {"data": "last_meeting_date",
                    "render": function (data, type, row, meta) {
                           var inputColumn = 16, inputValue;
                           var inputColumnName = meta['settings']['aoColumns'][inputColumn].data;
                           var inputDateFormat = 'DD/MM/YYYY hh:mm A';
                           if(row[inputColumnName] != null && row[inputColumnName] != 'NA' && row[inputColumnName] != '0000-00-00'){
                               return moment(row[inputColumnName]).format(inputDateFormat);
                           }
                           else{
                               return 'NA';
                           }
                       },
                     "orderable":false
                   },
                    
                    {"data": "meeting_purpose"},
                    {"data": "tags"},
                    {"data": "created_at"},
                    {"data": "updated_at"},
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
            [4, 'desc']
        ]
    });

    // removing common "Search Box" which generally getting seen above DataTable.
    $('#panel_meetinglog_filter').empty();

    meetinglog_datatable.columns().indexes().each(function(idx){
        $('#panel_meetinglog_wrapper table.dataTable thead tr:first th').eq(idx).find('input, select').on('change', function(){
            var data_column = $(this).closest('th').attr('data-column'), txtSearchedValue = $.trim(this.value);
            switch(data_column){
                case 'start_datetime':
                case 'end_datetime':
                case 'created_at':
                case 'updated_at':
                    txtSearchedValue = $.trim($('#from_'+ data_column).val()) +';'+ $.trim($('#to_'+ data_column).val());
                    break;
            }
            meetinglog_datatable.column(idx).search(txtSearchedValue).draw();
        });
    });
    $('.dataTables_filter').append('<a href="javascript:void(0);" title="Export Data" onclick="export_csv_formatted_data(this);"><i class="icons excel-icon" title="Export Data" alt="Export Data"></i></a>');
}

function export_csv_formatted_data(inputObj){
    var columns = [], known_data_columns = [], formObj = $('#frm_export_data');
    var tableThObj = $('#panel_meetinglog_wrapper table.dataTable thead tr:first th');
    var arn_number = $('#panel_meetinglog').attr('data-arn_number');
    meetinglog_datatable.columns().indexes().each(function(idx){
        if(tableThObj.eq(idx).find('input, select').length > 0){
            tableThObj.eq(idx).find('input, select').each(function(){
                var data_column = $(this).closest('th').attr('data-column'), txtSearchedValue = $.trim(this.value);
                switch(data_column){
                    case 'start_datetime':
                    case 'end_datetime':
                    case 'created_at':
                    case 'updated_at':
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
        else if(tableThObj.eq(idx).attr('data-column').toString().toLowerCase() == 'arn'){
            columns.push({'data':tableThObj.eq(idx).attr('data-column'), 'search':{'value':arn_number}});
            known_data_columns.push(tableThObj.eq(idx).attr('data-column'));
        }
        else{
            columns.push({'data':tableThObj.eq(idx).attr('data-column'), 'search':{'value':''}});
            known_data_columns.push(tableThObj.eq(idx).attr('data-column'));
        }
    });
    formObj.append('<input type="hidden" name="columns" value=\''+ JSON.stringify(columns) +'\'>');
    formObj.append('<input type="hidden" name="export_data" value="1">');
    formObj.append('<input type="hidden" name="load_datatable" value="1">');
    formObj.attr({'action': baseurl + '/meetinglog'});
    formObj.submit();
    formObj.attr({'action':'javascript:void(0);'});
    $('#frm_export_data input[name!="_token"]').remove();
}

function clear_date(inputObj, objectIndex, data_table_obj){
    var closestDateObj = $(inputObj).parents().eq(1).find('[type="date"]'), dateObjectID = closestDateObj.attr("id").substr(closestDateObj.attr("id").indexOf("_")+1);
    closestDateObj.val('');
    closestDateObj.trigger('change');
}

function view_code(id) {
    $.ajax({
        type: 'POST',
        url: baseurl +'/view-detail',
        data: {
            id: id
        },
        error: function(jqXHR, textStatus, errorThrown){
            if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                prepare_error_text(jqXHR.responseJSON);
            }
            else{
                swal('', unable_to_process_request_text, 'warning');
            }
        },
        success: function(data) {
            var resultdata = JSON.parse(data);
            // console.log(resultdata[0].meeting_mode);
            $("#arn").text(resultdata[0].ARN);
            $("#meeting_mode").text(resultdata[0].meeting_mode);
            $("#contact_name").text(resultdata[0].contact_person_name);
            $("#contact_mobile").text(resultdata[0].contact_person_mobile);
            $("#contact_email").text(resultdata[0].contact_person_email);
            $("#start_time").text(resultdata[0].start_datetime);
            $("#end_time").text(resultdata[0].end_datetime);
            $("#remarks").text(resultdata[0].meeting_remarks);
            var customer_response = resultdata[0].customer_response_received;
            if(customer_response === 0) {
                $("#customer_response").text('No');
                $("#customer_source").closest('.row.mt-1').hide();
                $("#customer_rating").closest('.row.mt-1').hide();
                $("#customer_feedback").closest('.row.mt-1').hide();
                $("#customer_response_received_date").closest('.row.mt-1').hide();
                $("#product_information").closest('.row.mt-1').hide();
            }
            else{
                $("#customer_response").text('Yes');
                $("#customer_source").closest('.row.mt-1').show();
                $("#customer_rating").closest('.row.mt-1').show();
                $("#customer_feedback").closest('.row.mt-1').show();
                $("#product_information").closest('.row.mt-1').show();
                $("#customer_response_received_date").closest('.row.mt-1').show();
            }
            var customer_source = resultdata[0].customer_response_source;
            if(customer_source === 1) {
                $("#customer_source").text('Email');
            }
            else if(customer_source === 2){
                $("#customer_source").text('SMS');
            }
            else{
                $("#customer_source").text('NA');
            }
            var customer_rating = resultdata[0].customer_given_rating;
            $("#customer_rating").text(customer_rating);
            $("#customer_feedback").text(resultdata[0].customer_remarks);
            $("#customer_response_received_date").text(resultdata[0].customer_response_received_datetime);
            var product_information = resultdata[0].product_information_received;
            if(product_information === 0){
                $("#product_information").text('No');
            }
            else
            {
                $("#product_information").text('Yes');
            }
        }
    }).then(function() {
        $('#view_meetinglog_modal').modal('show');
    });
}

function send_feedback_notification(id){
    $.ajax({
        type: 'POST',
        url: baseurl +'/meeting-feedback-notification',
        data: {
            id: id
        },
        dataType: 'json',
        error: function(jqXHR, textStatus, errorThrown){
            if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
                prepare_error_text(jqXHR.responseJSON);
            }
            else{
                swal('', unable_to_process_request_text, 'warning');
            }
        },
        success: function(response) {
            var msg ="";
            $.each(response.msg, function(key, value){
                msg += value +'\n'
            });
            if(msg == ''){
                msg = unable_to_process_request_text;
            }
            var swalType = 'warning', swalTitle = 'Error';
            if(response.status == 'success'){
                swalTitle = 'Good';
                swalType = 'success';
            }
            swal(swalTitle, msg, swalType);
        },
        complete:function(){
            meetinglog_datatable.draw();
        }
    });
}
