@section('style')
<style>
	table.data-table th {
		white-space: nowrap;
	}
</style>
@endsection
@extends('../layout')
@section('title', 'Old Users')
@section('breadcrumb_heading', 'Old Users')
@section('content')
		<style>
			#dataTableBuilder th {
				padding: 0px !important;
			}
		</style>
		<div class="row">
			<div class="col-md-12 col-sm-12">
				{{ $html->table() }}
			</div> 
		</div> 
@endsection
@section('custom_scripts')
	{{ $html->scripts() }}
@endsection
