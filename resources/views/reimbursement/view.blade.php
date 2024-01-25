@section('style')
<style>
	.select2-container {
	min-width: 400px;
	}
	.select2-results__option {
	padding-right: 20px;
	vertical-align: middle;
	}
	.select2-results__option:before {
	content: "";
	display: inline-block;
	position: relative;
	height: 20px;
	width: 20px;
	border: 2px solid #e9e9e9;
	border-radius: 4px;
	background-color: #fff;
	margin-right: 20px;
	vertical-align: middle;
	}
	.select2-results__option[aria-selected=true]:before {
	font-family:fontAwesome;
	content: "\f00c";
	color: #fff;
	background-color: #f77750;
	border: 0;
	display: inline-block;
	padding-left: 3px;
	padding-top: 3px;
	}
	.select2-container--default .select2-results__option[aria-selected=true] {
	background-color: #fff;
	}
	.select2-container--default .select2-results__option--highlighted[aria-selected] {
	background-color: #eaeaeb;
	color: #272727;
	}
	.select2-container--default .select2-selection--multiple {
	margin-bottom: 10px;
	}
	.select2-container--default.select2-container--open.select2-container--below .select2-selection--multiple {
	border-radius: 4px;
	}
	.select2-container--default.select2-container--focus .select2-selection--multiple {
	border-color: #f77750;
	border-width: 2px;
	}
	.select2-container--default .select2-selection--multiple {
	border-width: 2px;
	}
	.select2-container--open .select2-dropdown--below {
	
	border-radius: 6px;
	box-shadow: 0 0 10px rgba(0,0,0,0.5);
	
	}
	.select2-selection .select2-selection--multiple:after {
	content: 'hhghgh';
	}
	/* select with icons badges single*/
	.select-icon .select2-selection__placeholder .badge {
	display: none;
	}
	.select-icon .placeholder {
	display: none;
	}
	.select-icon .select2-results__option:before,
	.select-icon .select2-results__option[aria-selected=true]:before {
	display: none !important;
	/* content: "" !important; */
	}
	.select-icon  .select2-search--dropdown {
	display: none;
	}
	.select2-container {
		min-width: auto;
	}
	.select2-container .select2-selection--multiple {
		min-height: 38px;
	}
	#expense_claim_tab{
		display:none;
	}
	.required{
		color: #FF0000;
	}
	table.data-table th {
		white-space: nowrap;
	}
	</style>
@endsection
@extends('../layout')
@section('title', 'Reimbursement')
@section('breadcrumb_heading', 'Reimbursement')
@section('content')
@php

$types = array('Mobile Internet Expenses','Courier charges','Food reimbursement','Nism fee reimbursement','Travelling reimbursement','Stay Expense','Halting Charges','Other');

@endphp
<div class="row">
	<div class="col-lg-12">
	<div class="mt-2">
		@csrf
		<div class="row">
			<div class="col-lg-12">
			<div class="row form-inline">
				@if(empty($log_id))
				<div class="col-md-4 col-sm-4">
					<div class="form-group">
						<label>Select Meeting<span class="required reimbus reimbus_or">*</span></label>
						<div class='input-group'>	
							<select class="form-control" id="meetingid" name="ReimbursementType">
							<option value="">Select Meeting</option>
							@foreach($logs as $log)
								<option value="{{ $log->id }}">{{ $log->start_datetime }} | {{ str_pad($log->ARN, 12) }} | {{ $log->contact_person_name }}</option>
							@endforeach								
							</select>
						</div>
					</div>
				</div>
				@else
				<input type="hidden" id="meetingid" value="{{ $log_id }}" />
				@endif
				<div class="col-md-4 col-sm-4">
					<div class="form-group">
						<label>Reimbursement Type<span class="required">*</span></label>
						<select class="form-control" id="ReimbursementType" name="ReimbursementType">
							<option value="">Please Select</option>
							@foreach($types as $_type)
							<option value="{{ $_type }}"
							@if($type == $_type)
							selected 
							@endif>{{ $_type }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-md-4 col-sm-4 reimbus reimbus_tr">
					<div class="form-group">
						<label>Travel Type<span class="required">*</span></label>
						<select class="form-control" id="travelType" name="travelType">
							<option value="">Please Select</option>
							<option value="Local Travel">Local Travel</option>
							<option value="Tour Travel">Tour Travel</option>
						</select>
					</div>
				</div>
				<div class="col-md-4 col-sm-4 reimbus reimbus_tr">
					<div class="form-group">
						<label>Transport Type<span class="required">*</span></label>
						<select class="form-control" id="TransportType" name="TransportType">
							<option value="">Please Select</option>
							<option value="2 Wheeler">2 Wheeler</option>
							<option value="4 Wheeler">4 Wheeler</option>
							<option value="Railway">Railway</option>
							<option value="Flight">Flight</option>
							<option value="Auto">Auto</option>
							<option value="Cab">Cab</option>
							<option value="Bus">Bus</option>
						</select>
					</div>
				</div>
				<div class="col-md-4 col-sm-4 reimbus reimbus_cc reimbus_tr reimbus_fr reimbus_se reimbus_nf reimbus_mi reimbus_hc">
					<div class="form-group">
						<label>Amount<span class="required">*</span></label>
						<input type="text" class="form-control" id="amount" placeholder="Amount" value="{{ $amount }}"/>
					</div>
				</div>
				<div class="col-md-4 col-sm-4 reimbus reimbus_cc reimbus_tr reimbus_fr reimbus_se reimbus_nf reimbus_hc">
					<div class="form-group">
						<label><span class=" reimbus reimbus_tr">From</span> Location<span class="required">*</span></label>
						<input type="text" class="form-control" id="location" placeholder="Location"  value="{{ $location }}" />
					</div>
				</div>
				<div class="col-md-4 col-sm-4 reimbus reimbus_tr">
					<div class="form-group">
						<label>To Location<span class="required">*</span></label>
						<input type="text" class="form-control" id="tolocation" placeholder="Location To"  value="{{ $tolocation }}" />
					</div>
				</div>
				<div class="col-md-4 col-sm-4 reimbus reimbus_tr">
					<div class="form-group">
						<label>Approx KM<span class="required">*</span></label>
						<input type="text" class="form-control" id="approx_km" placeholder="Approx KM"  value="{{ $approx_km }}" />
					</div>
				</div>
				<div class="col-md-4 col-sm-4 reimbus reimbus_cc reimbus_tr reimbus_fr reimbus_se reimbus_nf reimbus_mi reimbus_hc">
					<div class="form-group">
						<label><span class="reimbus reimbus_se reimbus_hc">From</span> Date<span class="required">*</span></label>
						<div class='input-group date'>
						<input type='date' class="form-control" id='date' value="{{ $date }}" name="date" max="{{date('Y-m-d')}}" value=" " />
						</div>
					</div>
				</div>
				<div class="col-md-4 col-sm-4 reimbus reimbus_se reimbus_hc">
					<div class="form-group">
						<label>To Date<span class="required">*</span></label>
						<div class='input-group date'>
						<input type='date' class="form-control" id='todate' value="{{ $todate }}" name="todate" max="{{date('Y-m-d')}}" />
						</div>
					</div>
				</div>
				<div class="col-md-4 col-sm-4">
					<div class="form-group">
						<label>Upload file<span class="required">*</span></label>
						<div class='input-group date'>
							<input type='file' class="form-control" id='file' name="file" max=" " value=" " />
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8 col-sm-8">
				<label>Description<span class="required">*</span></label>
				<div class='input-group date'>
					<textarea id="description" maxlength="1000" class="form-control">{{ $description }}</textarea>
					<span style="float:right" id="charcount"></span>
				</div> 
			</div> 
		</div> 
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<div class='input-group date'>
					<button type="submit" class="btn btn-primary" id="reimb_submit">Submit</button>
					<button type="submit" class="btn btn-primary" id="reimb_reset">Reset</button>
				</div> 
			</div> 
		</div> 
		<div class="row">
			<div class="tab-content-item">
				<ul class="nav nav-tabs new-tab mt-0" id="myTab" role="tablist">
					<li class="nav-item active">
						<a class="nav-link" href="#Reimbursement_details">Reimbursement Details</a>
					</li>
					<li class="nav-item" id="expense_claim_tab">
						<a class="nav-link" href="#expense_claim">Expense Claims</a>
					</li>
				</ul>
				<div class="tab-content  data-tabs">
					<div class="tab-pane show active tab-list" id="Reimbursement_details" style="">
						<div class="col-md-12 col-sm-12">
							<table id="Reimbursement" class="table-bordered data-table">
								<thead>
									<tr>
										<th>Emplyee Code</th>
										<th>Type</th>
										<th>Date</th>
										<th>To Date</th>
										<th>Location</th>
										<th>To Location</th>
										<th>Distance</th>
										<th>Transport Type</th>
										<th>Travel Type</th>
										<th>Uploads</th>
										<th>Description</th>
										<th>Amount</th>
										<th>Remark</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div> 
					</div>
					<div class="tab-pane show tab-list" id="expense_claim" style="display: none;">
						<div class="col-md-12 col-sm-12">
							<table id="ReimbursementClaims" class="table-bordered data-table">
								<thead>
									<tr>
										<th style="width:200px;">Name</th>
										<th>Emplyee Code</th>
										<th>Type</th>
										<th>Date</th>
										<th>To Date</th>
										<th>Location</th>
										<th>To Location</th>
										<th>Distance</th>
										<th>Transport Type</th>
										<th>Travel Type</th>
										<th>Uploads</th>
										<th>Description</th>
										<th>Amount</th>
										<th>Remark</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div> 
					</div>
				</div>
			</div>
		</div> 
	</div>
</div>
<!-- Trigger the modal with a button -->
<button type="button" id="reimbursementmdl" class="btn btn-info btn-lg" data-toggle="modal" data-target="#reimb_model" style="display: none;"></button>
<!-- Modal -->
<div class="modal fade" id="reimb_model" role="dialog">
  <div class="modal-dialog">
	<!-- Modal content-->
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title" id="model_title">Alert</h4>
	  </div>
	  <div class="modal-body" id="reimbursementbody">
		<p></p>
	  </div>
	  <div class="modal-footer">
		<button type="button" id="model_submit" style="display:none;" class="btn btn-primary" data-dismiss="modal">Submit</button>
		<button type="button" id="rembus_close" class="btn btn-default" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>
<input type="hidden" id="resubmit" value="false" />
@endsection

@section('custom_scripts')
<script>

var table2 = table1 = null;

function load_expense_list_table(){
	$(function () {
		$("#ReimbursementClaims").dataTable().fnDestroy();
		table1 = $('#ReimbursementClaims').DataTable({
			"language": {
				"paginate": {
							"previous": "<",
							"next": ">"
							}
			},
			scrollX: true,
			processing: true,
			serverSide: true,
			ajax: {
				url: baseurl + '/reimbursement/expense_list',
				type:'post'
			},
			columns: [
				{data: 'name', name: 'name', width: '80px'},
				{data: 'em_code', name: 'em_code', width: '80px'},
				{data: 'type', name: 'type', width: '120px'},
				{data: 'date', name: 'date', width: '80px'},
				{data: 'todate', name: 'todate', width: '80px'},
				{data: 'location', name: 'location', width: '120px'},
				{data: 'tolocation', name: 'tolocation', width: '120px'},
				{data: 'approx_km', name: 'approx_km', width: '80px'},
				{data: 'TransportType', name: 'TransportType', width: '80px'},
				{data: 'travel_type', name: 'travel_type', width: '80px'},
				{data: 'file', name: 'file', width: '80px'},
				{data: 'description', name: 'description', width: '120px'},
				{data: 'amount', name: 'amount', width: '80px'},
				{data: 'remark', name: 'remark', width: '80px'},
				{data: 'status', name: 'status', width: '80px'},
			],
			"order": [[3, 'desc']],
		});
		table1.on('draw', function (data) {
			if(table1.page.info().recordsDisplay > 0){
				$("#expense_claim_tab").show();
			}
		});
	});
	$(".modal-backdrop").hide();
}

function isAsciiOnly(str) {
    for (var i = 0; i < str.length; i++)
        if (str.charCodeAt(i) > 127)
            return false;
    return true;
}

function utf8ByteCount(str) {
	if(!isAsciiOnly(str)){
		return str.length + 4;
	}
	return str.length;
}

$('#description').keyup(function(e) {
	max = $(this).attr('maxlength');
	if (e.which < 0x20) {
		return;
	}
	if (utf8ByteCount(this.value) == max) {
		e.preventDefault();
	} else if (utf8ByteCount(this.value) > max) {
		if(isAsciiOnly(this.value)){
			this.value = this.value.substring(0, max);
		}else{
			this.value = this.value.substring(0, max - 4);
		}
	}
});

$('#description').keypress(function(e) {
	max = $(this).attr('maxlength');
	if (e.which < 0x20) {
		return;
	}
	if (utf8ByteCount(this.value) == max) {
		e.preventDefault();
	} else if (utf8ByteCount(this.value) > max) {
		if(isAsciiOnly(this.value)){
			this.value = this.value.substring(0, max);
		}else{
			this.value = this.value.substring(0, max - 4);
		}
	}
});

function showimage(file)
{
	$( "#model_submit" ).hide();
	$( "#model_title" ).html('Image');
	$( "#reimbursementbody" ).html('<img src="'+file+'" style="max-width:100%;"/>');
	$( "#reimbursementmdl" ).trigger( "click" );
}

function showtext(txt)
{
	$( "#model_submit" ).hide();
	$( "#model_title" ).html("Alert");
	$( "#reimbursementbody" ).text(txt);
	$( "#reimbursementmdl" ).trigger( "click" );
}

function update_status(elm){
	id = elm.getAttribute("data-id");
	userid = elm.getAttribute("data-uid");
	status = elm.value;
	data = {
		status : status,
		id : id,
		userid : userid
	};
	$.ajax
		({ 
			url: baseurl + '/reimbursement/status',
			type: 'post',
			data: data,
			success: function(data)
			{
				$(".loader").hide();
				$( "#model_title" ).html("Status Updated");
				$( "#reimbursementbody" ).html( data.msg );
				$( "#reimbursementmdl" ).trigger( "click" );
				load_expense_list_table();
			},
			error: function(jqXHR, textStatus, errorThrown){
				var txt = '';
				$.each(jqXHR.responseJSON.errors, function(key,val) { 
					txt += val+"<br/>";     
				});
				$( "#model_title" ).html("Error");
				$( "#reimbursementbody" ).html(txt);
				$( "#reimbursementmdl" ).trigger( "click" );
			},
			complete: function(){
				$(".loader").hide();
			}
		});
	load_expense_list_table();
}

function addRemark(elm,editable = false)
{
	txt = elm.getAttribute("data-text");
	id = elm.getAttribute("data-id");
	userid = elm.getAttribute("data-uid");
	$("#model_submit").hide();
	if(editable){
		html = '<textarea  maxlength="1000" id="add_remark">'+ txt +'</textarea>';
		$( "#model_title" ).html("Add Remark");
		$("#model_submit").show();
	}else{
		html = '<span>'+ txt +'</span>';
		$( "#model_title" ).html("View Remark");
	}
	$( "#reimbursementbody" ).html(html);
	if(editable){
		model_submit = document.getElementById("model_submit");
		model_submit.setAttribute("data-id",id);
		model_submit.onclick = function(){
			data = {
				remark : $("#add_remark").val(),
				id : model_submit.getAttribute("data-id"),
				userid : userid
			};
			$.ajax
			({ 
				url: baseurl + '/reimbursement/addRemark',
				type: 'post',
				data: data,
				success: function(data)
				{
					load_expense_list_table();
					// $(".loader").hide();
					// $(".modal-backdrop").hide();
					// $("#model_submit").hide();
					// $( "#model_title" ).html("Remark Added");
					// $( "#reimbursementbody" ).html( data.msg );
					// $( "#reimbursementmdl" ).trigger( "click" );
					// $(".modal-backdrop").hide();
				},
				error: function(jqXHR, textStatus, errorThrown){
					// var txt = '';
					// $.each(jqXHR.responseJSON.errors, function(key,val) { 
					// 	txt += val+"<br/>";     
					// });
					// $(".loader").hide();
					// $(".modal-backdrop").hide();
					// $("#model_submit").hide();
					// $( "#model_title" ).html("Error");
					// $( "#reimbursementbody" ).html(txt);
					// $( "#reimbursementmdl" ).trigger( "click" );
				},
				complete: function(){
					$(".loader").hide();
					$(".modal-backdrop").hide();
				}
			});
		}
	}
	$( "#reimbursementmdl" ).trigger( "click" );
}

function get_status(elm)
{
	hrms_id = elm.getAttribute("data-hrms_id");
	id = elm.getAttribute("data-id");

	data = {
		hrms_id : hrms_id,
		id : id
	};

	$.ajax
	({ 
		url: baseurl + '/reimbursement/getstatus',
		type: 'post',
		data: data,
		success: function(data)
		{
			$(".loader").hide();
			$("#model_submit").hide();
			$( "#model_title" ).html("Refresh");
			$( "#reimbursementbody" ).html(data.msg);
			$( "#reimbursementmdl" ).trigger( "click" );
			load_expense_list_table();
		},
		error: function(jqXHR, textStatus, errorThrown){
			var txt = '';
			$.each(jqXHR.responseJSON.errors, function(key,val) { 
				txt += val+"<br/>";     
			});
			$( "#model_title" ).html("Error");
			$( "#reimbursementbody" ).html(txt);
			$( "#reimbursementmdl" ).trigger( "click" );
		},
		complete: function(){
			$(".loader").hide();
		}
	});
}

$(document).ready(function(){
	
	$('.reimbus').hide();
	$('.reimbus_cc').show();

	$('#ReimbursementType').change(function() {

		$('#resubmit').val('false');

		$type = $('#ReimbursementType').val().toLowerCase();
		
		$('.reimbus').hide();
		$('.reimbus_or').show();

		if($type == 'courier charges'.toLowerCase()){
			$('.reimbus_cc').show();
		}else if($type == 'travelling reimbursement'.toLowerCase()){
			$('.reimbus_tr').show();
		}else if($type == 'food reimbursement'.toLowerCase()){
			$('.reimbus_fr').show();
		}else if($type == 'stay expense'.toLowerCase()){
			$('.reimbus_se').show();
		}else if($type == 'nism fee reimbursement'.toLowerCase()){
			$('.reimbus_nf').show();
		}else if($type == 'mobile internet expenses'.toLowerCase()){
			$('.reimbus_mi').show();
		}else if($type == 'halting charges'.toLowerCase()){
			$('.reimbus_hc').show();
		}else if($type == 'other'.toLowerCase()){
			$('.reimbus_cc').show();
			$('.reimbus_or').hide();
		}else{
			$('.reimbus_cc').show();
		}
	});

	tab = '';
	$(".new-tab li a").on("click", function(a) {
		a.preventDefault();
		$(this).parent().addClass("active");
		$(this).parent().siblings().removeClass("active");
		var t = $(this).attr("href").split("#")[1];
		if(tab != t){
			if(t == 'Reimbursement_details'){
				load_reimbursement_table();
			}
			if(t == 'expense_claim'){
				load_expense_list_table();
			}
			tab = t;
		}
		$(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id="' + t + '"]').fadeIn();
		$(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id!="' + t + '"]').hide();
	});

	$('#meetingid').change(function() {
		$('#resubmit').val('false');
		//window.location = baseurl + '/reimbursement/' + $(this).val();
	});

	$('#description').keyup(function() {
		remainingchars = utf8ByteCount($(this).val());
		$('#charcount').html(remainingchars+'/1000');
	});

	$('#reimb_submit').click(function() 
	{
		if($('#meetingid').val() == '' && $('#ReimbursementType').val() != 'Other'){
			$( "#model_title" ).html("Select");
			$( "#model_title" ).html("Select Meeting");
			$( "#reimbursementbody" ).html( "Select Meeting to proceed." );
			$( "#reimbursementmdl" ).trigger( "click" );
			return false;
		}

		fd = new FormData();
		
		files = $('#file')[0].files[0];
		
		fd.append('file',files);
		fd.append('amount',$('#amount').val());
		fd.append('description',$('#description').val());
		fd.append('meeting_id', $('#meetingid').val());
		fd.append('resubmit', $('#resubmit').val());

		if (typeof $('#ReimbursementType:not(:hidden)').val() != "undefined") {
			fd.append('type',$('#ReimbursementType').val());
		}else{
			fd.append('type','');
		}
		if (typeof $('#travelType:not(:hidden)').val() != "undefined") {
			fd.append('travel_type',$('#travelType').val());
		}else{
			$("#travelType option:first").attr('selected','selected');
			$("#travelType").val($("#travelType option:first").val()); 
			fd.append('travel_type','');
		}
		if (typeof $('#TransportType:not(:hidden)').val() != "undefined") {
			fd.append('TransportType',$('#TransportType').val());
		}else{
			$("#TransportType option:first").attr('selected','selected');
			$("#TransportType").val($("#TransportType option:first").val()); 
			fd.append('TransportType','');
		}
		if (typeof $('#location:not(:hidden)').val() != "undefined") {
			fd.append('location',$('#location').val());
		}else{
			$('#location').val('');
			fd.append('location','');
		}
		if (typeof $('#tolocation:not(:hidden)').val() != "undefined") {
			fd.append('tolocation',$('#tolocation').val());
		}else{
			$('#tolocation').val('');
			fd.append('tolocation','');
		}
		if (typeof $('#approx_km:not(:hidden)').val() != "undefined") {
			fd.append('approx_km',$('#approx_km').val());
		}else{
			$('#approx_km').val('');
			fd.append('approx_km','');
		}
		if (typeof $('#date:not(:hidden)').val() != "undefined") {
			fd.append('date',$('#date').val());
		}else{
			$('#date').val('');
			fd.append('date','');
		}
		if (typeof $('#todate:not(:hidden)').val() != "undefined") {
			fd.append('todate',$('#todate').val());
		}else{
			$('#todate').val('');
			fd.append('todate','');
		}

		$.ajax
		({ 
			url: baseurl + '/reimbursement/add',
			type: 'post',
			data: fd,
			contentType: false,
			processData: false,
			success: function(data)
			{
				$('#rembus_close').click(function() 
				{
					make_empty();
					document.location = baseurl + '/reimbursement';
				});
				if(data.msg == 'exist' && $('#resubmit').val() == 'false'){
					data.msg = 'Attempting to resubmit a reimbursement request results in updating the existing request since the meeting has already been recorded in the system.';
					$('#rembus_close').unbind('click');
					$('#resubmit').val('true');
				}
				$(".loader").hide();
				$( "#model_title" ).html("Information");
				$( "#reimbursementbody" ).html( data.msg );
				$( "#reimbursementmdl" ).trigger( "click" );
			},
			error: function(jqXHR, textStatus, errorThrown){
				var txt = '';
				$.each(jqXHR.responseJSON.errors, function(key,val) { 
					txt += val+"<br/>";     
				});
				$( "#model_title" ).html("Please fill mandatory fields");
				$( "#reimbursementbody" ).html(txt);
				$( "#reimbursementmdl" ).trigger( "click" );
				load_reimbursement_table();
			},
			complete: function(){
				$(".loader").hide();
			}
		});

		load_reimbursement_table();

	});
	
	function load_reimbursement_table(){
		$(function () {
			$("#Reimbursement").dataTable().fnDestroy();
			table2 = $('#Reimbursement').DataTable({
				"language": {
					"paginate": {
								"previous": "<",
								"next": ">"
								}
				},
				scrollX: true,
				processing: true,
				serverSide: true,
				ajax: {
					url: baseurl + '/reimbursement/list',
					type:'post'
				},
				columns: [
					{data: 'em_code', name: 'em_code', width: '80px'},
					{data: 'type', name: 'type', width: '120px'},
					{data: 'date', name: 'date', width: '80px'},
					{data: 'todate', name: 'todate', width: '80px'},
					{data: 'location', name: 'location', width: '120px'},
					{data: 'tolocation', name: 'tolocation', width: '120px'},
					{data: 'approx_km', name: 'approx_km', width: '80px'},
					{data: 'TransportType', name: 'TransportType', width: '80px'},
					{data: 'travel_type', name: 'travel_type', width: '80px'},
					{data: 'file', name: 'file', width: '80px'},
					{data: 'description', name: 'description', width: '120px'},
					{data: 'amount', name: 'amount', width: '80px'},
					{data: 'remark', name: 'remark', width: '120px'},
					{data: 'status', name: 'status', width: '80px'},
				]
			});
		});
		$(".modal-backdrop").hide();
	}

	$type = $('#ReimbursementType').val().toLowerCase();
		
	$('.reimbus').hide();
	$('.reimbus_or').show();

	if($type == 'courier charges'.toLowerCase()){
		$('.reimbus_cc').show();
	}else if($type == 'travelling reimbursement'.toLowerCase()){
		$('.reimbus_tr').show();
	}else if($type == 'food reimbursement'.toLowerCase()){
		$('.reimbus_fr').show();
	}else if($type == 'stay expense'.toLowerCase()){
		$('.reimbus_se').show();
	}else if($type == 'nism fee reimbursement'.toLowerCase()){
		$('.reimbus_nf').show();
	}else if($type == 'mobile internet expenses'.toLowerCase()){
		$('.reimbus_mi').show();
	}else if($type == 'halting charges'.toLowerCase()){
		$('.reimbus_hc').show();
	}else if($type == 'other'.toLowerCase()){
		$('.reimbus_cc').show();
		$('.reimbus_or').hide();
	}else{
		$('.reimbus_cc').show();
	}

	load_reimbursement_table();
	load_expense_list_table();

	function make_empty(){
		$('#amount').val('');
		$('#description').val('');
		$('#meetingid').val('');
		$("#ReimbursementType").val(''); 
		$("#travelType").val(''); 
		$("#TransportType").val(''); 
		$('#location').val(''); 
		$('#tolocation').val('');
		$('#approx_km').val('');
		$('#date').val('');
		$('#todate').val('');
		$('#file').val('');
		$( "#ReimbursementType" ).trigger( "change" );
	}

	$("#reimb_reset").click(function(){
		make_empty();
		window.location = baseurl + '/reimbursement';
	});

	remainingchars = utf8ByteCount($('#description').val());
	$('#charcount').html(remainingchars+'/1000');

	$("#travelType").val('{{ $travel_type }}');
	$("#TransportType").val('{{ $TransportType }}');

});

</script>
@endsection
