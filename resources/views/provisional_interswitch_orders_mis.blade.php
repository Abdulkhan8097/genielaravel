@extends('../layout')
@section('title', 'Pending Interswitch MIS')
@section('breadcrumb_heading', 'Pending Interswitch MIS')
@section('custom_head_tags')
@section('content')
			@php
			$uploaded_cas_order_status_json = [];
			if(isset($uploaded_cas_order_status_arr) && is_array($uploaded_cas_order_status_arr) && count($uploaded_cas_order_status_arr) > 0){
			$uploaded_cas_order_status_json[] = ['label' => 'All', 'value' => ''];
			foreach ($uploaded_cas_order_status_arr as $key => $value) {
				$uploaded_cas_order_status_json[] = ['label' => $value, 'value' => $key];
			}
			unset($key, $value);
			}
			$uploaded_cas_order_status_json = json_encode($uploaded_cas_order_status_json);

			$data_table_headings = [
                //'action' => ['label' => 'Action'],
                'investor_name' => ['label' => 'Investor Name'],
                'investor_email' => ['label' => 'Investor Email'],
                'investor_mobile' => ['label' => 'Investor Mobile Number'],
                'investor_pan' => ['label' => 'Investor PAN'],
                'investor_dob' => ['label' => 'Investor DOB'],
                'client_id' => ['label' => 'Client ID'],
			];

			$data_table_headings = array_merge($data_table_headings, ['created' => ['label'=>'Date Created'], 'modified' => ['label'=>'Date Modified']]);
			$data_table_headings_html = '';
			$heading_field_counter = 0;

			foreach ($data_table_headings as $key => $value) {
				$data_table_headings_html .= '<th data-column="'. $key .'" data-fieldindex="'. $heading_field_counter++ .'">'. $value['label'] .'</th>';
			}
			unset($key, $value, $data_table_headings, $order_type_row, $heading_field_counter);
			@endphp
	<style>
		.btn-primary {
			float: inline-end;
		}
		.btn i {
		padding-right: 0.5rem;
		vertical-align: middle;
		line-height: 0;
		}
		table.dataTable thead th {
			white-space: nowrap;
		}
		.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>tbody>tr>td  {
			padding: 14px 18px;
		}
    
	</style>
<div class="row">
    <div class="col-lg-12">
        <div class="mt-2">
			<div>
				<a href="javascript:void(0);" class="btn btn-primary font-weight-bolder" onclick="exportCsvFormattedData(this);" accesskey="d"><i class="fa fa-download"></i>   <u>D</u>ownload CSV</a>
			</div>
			
            <table id="panel_table_sm" class="display" style="width:100%" >
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
    
    <form action="javascript:void(0);"  method="post" target="_blank" id="frm_export_data"></form>
  
@endsection
@section('custom_scripts')
<script type="text/javascript">
		var unable_to_process_request_text = 'Unable to process your request, try again later', data_table, detailed_tokens_data_table;
		var data_table_columns = [];
		function makeModalForm(modal_title, modal_body, modal_footer){
	    var modalHTML = '', modal_id = 'modal_id_'+ $.now();
		modalHTML += '<div id="'+ modal_id +'" class="modal fade in"  tabindex="-1" role="dialog"  aria-hidden="true">';
			modalHTML += '<div class="modal-dialog modal-lg" role="document" style="width:100%;">';
			modalHTML += '<div class="modal-content">';
				modalHTML += '<div class="modal-header"><h5 class="modal-title">'+ modal_title +'</h5></div>';
				modalHTML += '<div class="modal-body">'+ modal_body +'</div>';
				modalHTML += '<div class="modal-footer">'+ modal_footer +'</div>';
			modalHTML += '</div>';
			modalHTML += '</div>';
		modalHTML += '</div>';
		$('#frm_export_data').after(modalHTML);
		    return modal_id;
		}
		$(document).ready(function () {
            var data_table_columns = [];
            $('#panel_table_sm thead tr:nth-child(1) th').each(function () {
                var data_column = $(this).attr("data-column"),
                title = $.trim($(this).text()),
                txtSearchInput = '',
                columnDefJSON = { "data": data_column };

                switch(data_column){
                    case 'investor_dob':
                    case 'created':
                    case 'modified':
                        txtSearchInput  = '<input class="form-control" type="date" data-from_date="1" id="from_'+ data_column +'" placeholder="From Date"> - ';
                        txtSearchInput += '<input class="form-control" type="date" data-to_date="1" id="to_'+ data_column +'" placeholder="To Date">';
                    break;
                    default:
                    txtSearchInput = '<input class="form-control" type="text" placeholder="'+title+'" />';
                }

                if (txtSearchInput !== '') {
                    $(this).html(txtSearchInput);
                }
                data_table_columns.push(columnDefJSON);
            });

            // Call the function to initialize or update your DataTable
            prepareMisDatatable(data_table_columns);
        });

	var data_table;

	function prepareMisDatatable(data_table_columns) 
	{
		
            // Datatable
            data_table = $('#panel_table_sm').DataTable({
                "ordering": false,
                "responsive": true,
                "processing": true,
                "serverSide": true,
                "searching": true,
                // "scrollY": "50vh",
                "scrollX": true,
                "scrollCollapse": true,
                language: {
                    paginate: {
                        next: '<i class="icons angle-right"></i>',
                        previous: '<i class="icons angle-left"></i>'  
                    }
                },
                "ajax": {
                    "beforeSend": function () {
                        if (typeof KTApp !== "undefined") {
                            KTApp.blockPage();
                        }
                    },
                    "url": "{{ url()->current() }}",
                    "type": "POST",
					"dataType": "json",
                    "data": function (d) {
                        d.load_datatable = 1;
                        d.searched_order_source = 'interswitch';
                        d.partner_interswitch_mis_request = 1;
                        d.records_created_by_bdm = '{{ $records_created_by_bdm }}';
                    },
                    "complete": function () {
                        if (typeof KTApp !== "undefined") {
                            KTApp.unblockPage();
                        }
                        window.setTimeout(function () {
                            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                        }, 1000);
                    }
                },
				"columns": data_table_columns,

				 // Assuming $data_table_columns is available in your Blade template
            });

            // Removing common "Search Box" which generally gets seen above DataTable.
            $('#panel_table_sm_filter').empty();

            // Apply the search
            data_table.columns().indexes().each(function (idx) {
                $('table.dataTable thead tr:first th').eq(idx).find('input').on('change', function () {
                    var data_column = $(this).closest('th').attr('data-column'),
                        txtSearchedValue = $.trim(this.value),
                        data_fieldindex = $(this).closest('th').attr('data-fieldindex');

                    switch (data_column) {
                        case 'investor_dob':
                        case 'created':
                        case 'modified':
                            txtSearchedValue = $.trim($('#from_' + data_column).val()) + ';' + $.trim($('#to_' + data_column).val());
                            break;
                    }

                    data_table.column(data_fieldindex).search(txtSearchedValue).draw();
                });
            });
    }
	function exportCsvFormattedData(inputObj) 
	{
            var columns = [], knownDataColumns = [], formObj = $('#frm_export_data');

            data_table.columns().indexes().each(function (idx) {
                var dataFieldIndex;
                if ($('table.dataTable thead tr:first th').eq(idx).find('input, select').length > 0) {
                    $('table.dataTable thead tr:first th').eq(idx).find('input, select').each(function () {
                        var dataColumn = $(this).closest('th').attr('data-column'),
                            txtSearchedValue = $.trim(this.value);
                        dataFieldIndex = $(this).closest('th').attr('data-fieldindex');

                        switch (dataColumn) {
                            case 'investor_dob':
                            case 'created':
                            case 'modified':
                                if ($.trim($('#from_' + dataColumn).val()) !== '' || $.trim($('#to_' + dataColumn).val()) !== '') {
                                    txtSearchedValue = $.trim($('#from_' + dataColumn).val()) + ';' + $.trim($('#to_' + dataColumn).val());
                                }
                                break;
                        }

                        if ($.inArray(dataColumn, knownDataColumns) === -1) {
                            columns.push({'data': dataColumn, 'search': {'value': txtSearchedValue}});
                            knownDataColumns.push(dataColumn);
                        }
                    });
                }
            });

            var activeTabId = $('ul.tabs li.tab a.active').attr('href');
            // formObj.append('<input type="hidden" name="columns" value=\'' + JSON.stringify(columns) + '\'>');
            // formObj.append('<input type="hidden" name="searched_order_source" value="interswitch">');
            // formObj.append('<input type="hidden" name="partner_interswitch_mis_request" value="1">');
            // formObj.append('<input type="hidden" name="records_created_by_bdm" value="{{ $records_created_by_bdm }}">');
            // formObj.append('<input type="hidden" name="export_data" value="1">');
            // formObj.append('<input type="hidden" name="load_datatable" value="1">');
            // formObj.attr({'action': "{{ url()->current() }}"});
			// // formObj.attr({'action': baseurl + '/mis'});
            // formObj.submit();
            // formObj.attr({'action': 'javascript:void(0);'});
            // formObj.empty();
				var formData = {};
				formData['columns'] = JSON.stringify(columns);
				formData['searched_order_source'] = 'interswitch';
				formData['partner_interswitch_mis_request'] = '1';
				formData['records_created_by_bdm'] = '{{ $records_created_by_bdm }}';
				formData['export_data'] = '1';
				formData['load_datatable'] = '1';
				$.ajax({
					type: 'POST', // or 'GET' depending on your server-side endpoint
					url: baseurl + '/pending-interswitch-mis',
					data: formData,
					dataType:'json',
					success: function (data) {
						// window.location.href = data.file;
						if (data.file) {

							formData['path'] = data.base_path;

							var link = document.createElement('a');
							link.href = data.file;
							link.download = data.file_name;
							link.click();
							link.remove();

							$.ajax({
								url: baseurl + '/ajax-unlink-file',
								method: 'POST',
								data: formData,
								dataType:'json',
								success: function (secondData) {
									console.log(secondData);
								},
								error: function (error) {
									// Handle errors for the second AJAX call
									console.error("Error in second AJAX call", error);
								}
							});

						} else {
							// Handle the case where the result is empty
							console.log("Data is empty");
						}
					},
					error: function (error) {
						// Handle error
						console.error(error);
					}
				});

    }
	function filter_mis_data(inputObj) 
	{
        var formObj = $("#frm_mis_filters");
        var inputData = formObj.serializeArray();
        var flagExportData = false, flagShowDataInModal = false, flagCasWiseDataShown = false;

        if ($(inputObj).attr("for") !== null && typeof $(inputObj).attr("for") !== "undefined") {
            if ($(inputObj).attr("data-casid") !== null && typeof $(inputObj).attr("data-casid") !== "undefined") {
                inputData = [];
                inputData.push({"name": "filter_records_for_cas_uploaded_id", "value": $(inputObj).attr("data-casid")});
                flagCasWiseDataShown = true;
            }

            inputData.push({"name": "show_data_for", "value": $(inputObj).attr("for")});

            if ($(inputObj).attr("data-export") !== null && typeof $(inputObj).attr("data-export") !== "undefined") {
                flagExportData = true;
            } else {
                flagShowDataInModal = true;
            }
        } else {
            inputData.push({"name": "return_count", "value": "1"});
        }

        var activeTabId = $('ul.tabs li.tab a.active').attr('href');
        var searchedOrderSource = 'interswitch';
		 
		if (flagExportData) {
				// Create a data object with the form data
				var formData = {};
				$.each(inputData, function (index, value) {
					formData[value.name] = value.value;
				});
				formData['export_data'] = '1';
				formData['partner_interswitch_mis_request'] = '1';
				formData['records_created_by_bdm'] = '{{ $records_created_by_bdm }}';
				formData['searched_order_source'] = searchedOrderSource;

				// Perform an AJAX request
				$.ajax({
					type: 'POST', // or 'GET' depending on your server-side endpoint
					url: baseurl + '/get-mis-data',
					data: formData,
					dataType:'json',
					success: function (data) {
						console.log(data);
						// window.location.href = data.file;
						if (data.file) {

							formData['path'] = data.base_path;

							var link = document.createElement('a');
							link.href = data.file;
							link.download = data.file_name;
							link.click();
							link.remove();
							
							$.ajax({
								url: baseurl + '/ajax-unlink-file',
								method: 'POST',
								data: formData,
								dataType:'json',
								success: function (secondData) {
									
								},
								error: function (error) {
									// Handle errors for the second AJAX call
									console.error("Error in second AJAX call", error);
								}
							});
							
						} else {
							// Handle the case where the result is empty
							console.log("Data is empty");
						}
						
					},
					error: function (error) {
						// Handle error
						console.error(error);
					}
				});
			}
		else {
            inputData.push({"name": "searched_order_source", "value": searchedOrderSource});
            inputData.push({"name": "partner_interswitch_mis_request", "value": 1});
            inputData.push({"name": "records_created_by_bdm", "value": "{{ $records_created_by_bdm }}"});


            $.ajax({
                beforeSend: function () {
                    if (typeof KTApp !== "undefined") {
                        KTApp.blockPage();
                    }
                },
                url: "{{ url('get-mis-data') }}",
                type: "POST",
                data: inputData,
                dataType: "json",
                error: function () {
                    alert('Unable to process the request.');
                },
                success: function (response) {
                    // Handle success response

                    if (response.return_count !== null && typeof response.return_count !== 'undefined' && response.return_count === 1) {
                        // Handle the return count scenario
                        if (response.order_type_counts !== null && typeof response.order_type_counts !== 'undefined') {
                            $.each(response.order_type_counts, function (key, value) {
                                if ($('h4[for="' + key + '"]').length > 0) {
                                    $('h4[for="' + key + '"]').html(value);
                                }
                            });
                        }
                    } else if (flagShowDataInModal) {
                        // Handle showing data in modal scenario

                        var modalFooterHtml = '<div class="row" style="width:100%;">' +
                            '<div class="col-lg-6 text-left"></div>' +
                            '<div class="col-lg-6 text-right"><button type="button" id="btnClose" class="btn btn-secondary" onclick="$(this).closest(\'.modal\').modal(\'hide\');">Close</button></div>' +
                            '</div>';

                        var modalTitle = $.trim($(inputObj).parents().eq(1).find('.card-title').text());
                        if (modalTitle === "" && flagCasWiseDataShown) {
                            modalTitle = $.trim($('table.dataTable thead:first').find('th.sorting_disabled[data-column="' + $(inputObj).attr("for") + '"]').text());
                        }

                        modalTitle = modalTitle.toString().replace('Number of', 'Detailed view of');

                        var modalId = makeModalForm(modalTitle, response.modal_body, modalFooterHtml);
                        var modalObj = $('#' + modalId);
                        modalObj.modal({dismissible: false});
                        modalObj.addClass('modal_lg');

                        if (modalObj.find('table.responsive-table').length > 0) {
                            modalObj.find('table.responsive-table').DataTable({
								"order": [[0, 'asc']],
                                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
								"scrollX": true,
								"autoWidth": true,
                            });

                            // modalObj.css({'min-height': '80%'});
                            modalObj.find('.modal-content h5').wrap('<div class="col-lg-6 text-left"></div>');
                            modalObj.find('.modal-content h5').parent().wrap('<div class="row" style="width:100%;"></div>');

                            if (flagCasWiseDataShown) {
                                modalObj.find('.modal-content h5').parent().after('<div class="col-lg-6 text-right"><button type="button" class="btn btn-primary font-weight-bolder" id="btn_export_mis_data" name="btn_export_mis_data" for="' + $(inputObj).attr("for") + '" data-export="1" data-casid="' + $(inputObj).attr("data-casid") + '" onclick="filter_mis_data(this);">Export Data</button</div>');
                            } else {
                                modalObj.find('.modal-conflagCasWiseDataShowntent h5').parent().after('<div class="col-lg-6 text-right"><button type="button" class="btn btn-primary font-weight-bolder" id="btn_export_mis_data" name="btn_export_mis_data" for="' + $(inputObj).attr("for") + '" data-export="1" onclick="filter_mis_data(this);">Export Data</button</div>');
                            }

                            modalObj.find('select').show();
                        }

                        modalObj.modal();
                        modalObj.on('hidden.bs.modal', function (e) {
                            $(this).remove();
                        });

                        $('body').css({overflow: 'visible'});
                    }
                },
                complete: function (jqXHR) {
                    if (typeof KTApp !== "undefined") {
                        KTApp.unblockPage();
                    }
                }
            });
        }
    }

</script>
@endsection


