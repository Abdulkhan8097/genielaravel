@extends('../layout')
@section('title', 'Auto Switch Orders')
@section('breadcrumb_heading', 'Auto Switch Orders')
@section('custom_head_tags')
@section('content')
			@php
			$data_table_headings = [
			'status' => ['label' => 'Status'],
			'client_id' => ['label' => 'Client ID'],
			'client_name' => ['label' => 'Investor Name'],
			'client_email' => ['label' => 'Investor Email'],
			'client_mobile' => ['label' => 'Investor Mobile Number'],
			'folio_number' => ['label' => 'Folio Number'],
			'from_scheme' => ['label' => 'From Scheme Name'],
			'to_scheme' => ['label' => 'To Scheme Name'],
			'switch_amount' => ['label' => 'Switch Amount'],
			'created_at' => ['label' => 'Created'],
			];
			$data_table_headings_html = '';
			$heading_field_counter = 0;

			foreach ($data_table_headings as $key => $value) {
				$data_table_headings_html .= '<th data-column="'. $key .'" data-fieldindex="'. $heading_field_counter++ .'">'. $value['label'] .'</th>';
			}
			@endphp
<div class="row">
    <div class="col-lg-12">
        <div class="mt-2">	
            <table id="panel_table_sm" class="display yajra-datatable" style="width:100%" >
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
		$(document).ready(function () {
        var data_table_columns = [];
        $('#panel_table_sm thead tr:nth-child(1) th').each(function () {
            var data_column = $(this).attr("data-column"),
                title = $.trim($(this).text()),
                txtSearchInput = '',
                columnDefJSON = { "data": data_column };

				switch(data_column){

    //   case 'created_at':
    //     txtSearchInput  = '<input class="form-control" type="date" data-from_date="1" id="from_'+ data_column +'" placeholder="From Date"> - ';
    //     txtSearchInput += '<input class="form-control" type="date" data-to_date="1" id="to_'+ data_column +'" placeholder="To Date">';
    //     break;
      case 'status':
		txtSearchInput += '<select><option value="">Select Status</option><option value="Pending">Pending</option><option value="Success">Success</option><option value="Failed">Failed</option></select>';
		break;
      default:
        txtSearchInput = '<input class="form-control " type="hidden" placeholder="'+title+'" />';
    }

            if (txtSearchInput !== '') {
                $(this).html(txtSearchInput);
            }

            data_table_columns.push(columnDefJSON);
        });

        // Call the function to initialize or update your DataTable
        autoSwitchMisDatatable(data_table_columns);
    });
	var data_table;

	function autoSwitchMisDatatable(data_table_columns) 
	{
		var data_table = $('.yajra-datatable').DataTable({
			    // "ordering": false,
                // "responsive": true,
                // "processing": true,
                // "serverSide": true,
                // "searching": true,
				scrollX: true,
				scrollY: false,
				destroy: true,
				columnDefs: [ { orderable: false, targets: [0,1,2,3,4,5,6,7,8] } ],
				order: [[ 9, "desc" ]],
			ajax: {
					"url": "{{ url()->current() }}",
					"type":'post',
					"data": function (d) {
                        d.load_datatable = 1;
                        d.records_created_by_bdm = '{{ $bdm_id }}';
                    },
				},
			"columns": data_table_columns,

				language: {
				paginate: {
				next: '<i class="icons angle-right"></i>',
				previous: '<i class="icons angle-left"></i>'  
				}
				},
		});
		// Removing common "Search Box" which generally gets seen above DataTable.
		// $('#panel_table_sm_filter').empty();

		// Apply the search
		data_table.columns().indexes().each(function (idx) {
			$('table.dataTable thead tr:first th').eq(idx).find('input, select').on('change', function () {
				var data_column = $(this).closest('th').attr('data-column'),
					txtSearchedValue = $.trim(this.value),
					data_fieldindex = $(this).closest('th').attr('data-fieldindex');

				switch (data_column) {
				
					case 'created_at':
						txtSearchedValue = $.trim($('#from_' + data_column).val()) + ';' + $.trim($('#to_' + data_column).val());
						break;
				}

				data_table.column(data_fieldindex).search(txtSearchedValue).draw();
			});
		});
		
	}

  </script>
  @endsection