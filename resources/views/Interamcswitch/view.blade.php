@section('style')
<style>

.required, .text-error{
	color:#ED3237;
}

#client_id, #missngEntryTable{
	width:100%;
}

#addNew, #btn_submit_switched_schemes{
	margin: 16px auto;
}

#addNew{
	float: right;
}

.close-item {
	color: black;
	font-weight: 600;
	font-size: 12px;
	cursor: pointer;
	position: absolute;
	top: -8px;
	right: -8px;
	background-color: #ED3237;
	width: 16px;
	height: 16px;
	margin: 0px;
	padding: 10px;
	align-items: center;
	justify-content: center;
	display: flex;
	border-radius: 10px;
}

#missngEntryTable tr{
	position: relative;
}

.t-contoler{
	width: 100px;
	height: 33px;
	border-radius: 5px;
	background: none;
	font-size: 12px;
	font-weight: 600;
	padding: 0 7px;
	border: solid 1px #e7e7e7;
}
.has-error{
	border: solid 1px #ED3237!important;
}

.table td, .table th {
	border: 1px solid #68686817;
}

.table thead {
	background-color: #e7e7e7;
	/* border-radius: 26px; */
}
#frm_inter_switch_schemes{
	width: 100%;
}

.auto_switch {
	margin: 5px 5px 0px 0px;
}

.folio_num_to {
	margin: 5px 5px 0px 5px;
}

.auto_switch_options {
	display : none;
}

#missngEntryTable tr>th:nth-of-type(4),   table tr>td:nth-of-type(4){
	display:none;
}

</style>

@endsection
@extends('../layout')
@section('title', 'Inter Switch Schemes')
@section('breadcrumb_heading', 'Inter Switch Schemes ')

@section('content')
<div class="row">
	<form class="kt-form kt-form--label-right" id="frm_inter_switch_schemes" name="frm_inter_switch_schemes">
	<div class="col-lg-12">
		<div class="mt-2">
			@csrf
			<div class="row">
				<div class="col-lg-12">
					<div class="row form-inline">
						<div class="col-md-4 col-sm-4">
							<div class="form-group">
								<label>Client Code/Client Name<span class="required">*</span></label>
								<div class='input-group'>	
									<select name="client_id" id="client_id" ></select>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-sm-12">
							<div class="form-group">
								<div class="alert alert-warning" id="is_alert" role="alert">
								<b>Important Note</b> : If OTM of higher amount is already approved for this client, then only order will be placed successfully for this client! IF not, then you need to first ask client to create OTM and once it is approved, then only orders will be processed for this client!
								</div>
							</div>
						</div>
						<div class="col-md-12 col-sm-12" style="overflow-x: auto;">
							<table class="table" id="missngEntryTable">
								<thead>
									<tr>
										<th>Sr no</th>
										<th>From Scheme (Enter Scheme Name / ISIN / Channel Partner Code)</th>
										<th>Folio no</th>
										<th>All Units</th>
										<th>Units</th>
										<th>TO Scheme (Select recommended Scheme Or Enter Scheme Name / ISIN / Channel Partner Code)</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>1</td>
										<td>
											<select name="unique_no[]" data-curnav="0" data-minredeem="0"></select>
											<div class="text-error">&nbsp;</div>
										</td>
										<td>
											<input class="t-contoler" type="text" name="folio_num[]" placeholder="Eg: 123456789" required="required"><div class="text-error">&nbsp;</div>
										</td>
										<td>
											<input type="checkbox" name="all_units[]" disabled><div class="text-error">&nbsp;</div>
										</td>
										<td>
											<input class="t-contoler units" type="text" name="units[]" placeholder="Eg: 234.54" required="required"><div class="text-error">&nbsp;</div>
										</td>
										<td>
											<select name="unique_no_to[]" data-minpurchase="0"></select><br/><input type="checkbox" class="auto_switch_options auto_switch" name="auto_switch[]" ><label class="auto_switch_options">Auto Switch to DAAF</label><br/><label class="auto_switch_options label_folio_num_to">Folio : </label><select class="auto_switch_options folio_num_to" name="folio_num_to[]" data-minpurchase="0"></select>
											<div class="text-error">&nbsp;</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-md-12 col-sm-12">
							<button class="submitbtn btn btn-primary" type="button" id="btn_submit_switched_schemes">Send OTP &amp; Submit Details</button>
							<button type="button" class="btn btn-primary float-right" id="addNew">+ Add new</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</form>
</div>

<div class="modal fade" id="ClientOTPModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content" style="width: 100%;">
			<div class="modal-header">
				<h5 class="modal-title" id="view_image_modal_title"><span id="typeAddEdit"></span>OTP Confirmation for InterSwitch <div class="CurCatName"></div> <span id="view_image_modal_span"></span></h5>
			</div>
			<!-- <form method="post" id="addProductCatSubmit" action="" enctype="multipart/form-data"> -->
				<div class="modal-body" style="">
					<div id="list_display">
						<div class="form-group row">
							<label class="col-xl-3 col-lg-3 col-form-label"> Enter OTP Received By Client: </label>
							<div class="col-lg-9" id="textArea">
								<div id="sample">          
									<input type="text" class="form-control" name="submitOtp" id="submitOtp" maxlength="4" required>
									<div id="submitOtpAlert" style="color: red;font-style: italic;display: none;">Please enter a valid 4-digit OTP*</div>
									<label id="got_otp_msg" style="color: green;font-style: italic;"></label>
									<label id="invalid_otp_msg" style="color: red;font-style: italic;"></label>                                        
								</div>            
							</div>
						</div> 
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id='submitOtpBtn' class="btn btn-success">Submit</button>
					<button type="submit" id='resendOtpBtn' class="btn btn-success">Resend OTP</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			<!--  </form> -->
		</div>
	</div>
</div>
<input type="hidden" id="otm_amount" value="0" />

@endsection

@section('custom_scripts')
<script>

function formatRepo(repo){
	if (repo.loading) {
		return repo.label;
	}

	var $container = $(
	"<div class='select2-result-repository clearfix'>" +
	"<div class='select2-result-repository__meta'>" +
		"<div class='select2-result-repository__title'></div>" +
	"</div>" +
	"</div>"
	);

	$container.find(".select2-result-repository__title").text(repo.label);
	return $container;
}

function formatRepoSelection(repo){
	return repo.label;
}

// adding up an autocomplete functionality based on Client Code/ Client Name
function bindSelectClientData(inputObj){
	inputObj.select2({
		ajax: {
			url: "{{ url('InterAMCSwitch/api/get_client_details') }}",
			dataType: "json",
			method: "POST",
			delay: 250,
			data: function(params){
				return {
					search_term:params.term
				}
			},
			processResults: function(data, params){
				params.page = params.page || 1;
				return {
					results: data.items,
					/*pagination: {
						more: (params.page * 30) < data.total_count
					}*/
				}
			}
		},
		placeholder: 'Client Code',
		minimumInputLength: 3,
		templateResult: formatRepo,
		templateSelection: formatRepoSelection
	});
	inputObj.next('span:first').css({"width":"300px"});
}

// adding up an autocomplet functionality based on ISIN/Scheme Name
function bindSelect2(inputObj){
	inputObj.select2({
		ajax: {
			url: "{{ url('InterAMCSwitch/api/get_scheme_details') }}",
			dataType: "json",
			method: "POST",
			delay: 250,
			data: function(params){
				return {
					api:8.3,
					search_term:params.term,
					show_schemes_for:((inputObj.attr('name') == 'unique_no[]')?'redeem':'purchase'),
					view_nfo_schemes:((inputObj.attr('name') == 'unique_no[]')?'0':'1'),
					redeeming_scheme_uniqueno:((inputObj.attr('name') == 'unique_no_to[]')?$(inputObj).closest('tr').find('[name="unique_no[]"]').val():''),
					api_requested_from_source:'RankMF-DRM'
				}
			},
			processResults: function(data, params){
				params.page = params.page || 1;
				return {
					results: data.items,
					/*pagination: {
						more: (params.page * 30) < data.total_count
					}*/
				}
			}
		},
		placeholder: 'ISIN',
		minimumInputLength: 3,
		templateResult: formatRepo,
		templateSelection: formatRepoSelection
	});

	inputObj.next('span:first').css({"width":"300px"});
}

$(document).on('select2:select', '[name="client_id"]', function(e){

	client_id_obj = document.getElementById("client_id");

	client_id = $.trim($(client_id_obj).val());

	$.ajax({
		url:"{{ url('InterAMCSwitch/api/checkMandate') }}",
		type: "POST",
		data: {client_id : client_id},
		dataType: "json",
		error: function(){
			swal('Error','Error! Unable to process request.Please try again!');
		},
		beforeSend : function(){
			$('.p-rmf-loader').show();
		},
		success: function(response){
			if(response.status=="success"){
				if(response.amount != 0){
					$('#is_alert').html('The OTM mandate for this client is registered for amount - '+response.amount);
					$('#is_alert').show();
					$('#otm_amount').val(response.amount)
				}else{
					$('#is_alert').html('Your client doesnt have an OTM registered yet with us ; you can place an order however the same will be saved at our end as provisional and will be executed once the OTM is registered.');
					$('#is_alert').show();
				}
			}
		},
		complete: function(){
			$('.p-rmf-loader').hide();
		}
	});

});

$(document).on('select2:select', '[name="unique_no_to[]"]', function(e){

	var data = e.params.data;
	var elementToFocus = [];
	var schemecode_to_objt = $(this);
	
	client_id_obj = document.getElementById("client_id");

	client_id = $.trim($(client_id_obj).val());

	if(client_id == ''){
		swal('Alert','Please select client.');
		return false;
	}

	$.ajax({
			url:"{{ url('InterAMCSwitch/api/check_for_autoswitch') }}",
			type: "POST",
			data: {
				ISIN : data.label,
				id : data.id,
				client_id : client_id
			},
			dataType: "json",
			error: function(){
				swal('Error','Error! Unable to process request.Please try again!');
			},
			beforeSend : function(){
				$('.p-rmf-loader').show();
			},
			success: function(response){
				//enable checkbox for autoswitch
				if(response.autoswitch == 'true'){
					$(schemecode_to_objt).parent().parent().find('.auto_switch_options').show();
					$(schemecode_to_objt).parent().parent().find('input.auto_switch').prop("checked", true).prop("disabled", false);
				}else{
					$(schemecode_to_objt).parent().parent().find('.auto_switch_options').hide();
					$(schemecode_to_objt).parent().parent().find('input.auto_switch').prop("checked", false).prop("disabled", true);
				}
				// set folio of client
				$select = $(schemecode_to_objt).parent().parent().find('select.folio_num_to');
				$select.html('');
				$select.append('<option value="">New Folio</option>');
				if(response.investor_folios.length > 0){
					$.each(response.investor_folios,function(key, value)
					{
						$select.append('<option value=' + value + '>' + value + '</option>');
					});

				}
			},
			complete: function(){
				$('.p-rmf-loader').hide();
			}
	});
	
	if(data.scheme_rating != null && data.scheme_rating != 'undefined' && data.scheme_rating != '-1'){
		if(data.scheme_rating<3){
			//$(schemecode_to_objt).next('.select2-container').addClass('has-error');
			$(schemecode_to_objt).parent().find('div.text-error').html('Since this is a low rated equity scheme, RANK MF research recommends that Investors should look for alternative better Equity schemes');
			if(elementToFocus == null || typeof elementToFocus == 'undefined'){
				elementToFocus = $(schemecode_to_objt);
			}
		}
		else{
			$(schemecode_to_objt).parent().find('.select2-container').removeClass('has-error');
			$(schemecode_to_objt).parent().find('div.text-error').html('&nbsp;');
		}
	}
	var purchase_amt = ((data.min_purchase_amt != null && $.isNumeric(data.min_purchase_amt))?data.min_purchase_amt:0);
	$(this).attr('data-minpurchase',purchase_amt);
});

$(document).on('select2:select', '[name="unique_no[]"]', function(e){
	var data = e.params.data;
	var nav = ((data.cur_nav_accord != null && $.isNumeric(data.cur_nav_accord))?data.cur_nav_accord:0);
	$(this).attr('data-curnav',nav);
	var minredeem = ((data.min_redemption_qty != null && $.isNumeric(data.min_redemption_qty))?data.min_redemption_qty:0);
	$(this).attr('data-minredeem',minredeem);
	// reseting the selected To Scheme value
	var schemecode_to_obj = $(this).closest('tr').find('[name="unique_no_to[]"]');
	$(schemecode_to_obj).val('').trigger('change');   
	$(schemecode_to_obj).parent().find('.select2-container').removeClass('has-error');
	$(schemecode_to_obj).parent().find('div.text-error').html('&nbsp;');
});

$(document).on('change','[name="all_units[]"]', function(e){
	var units_obj = $(this).closest('tr').find('[name="units[]"]');
	units_obj.val('');
	if($(this).is(':checked')) {
		units_obj.prop("readonly", true);
	}
	else{
		units_obj.prop("readonly", false);
	}
	$(units_obj).removeClass('has-error');
	$(units_obj).parent().find('div.text-error').html('&nbsp;');
	$(this).removeClass('has-error');
	$(this).next('div.text-error').html('&nbsp;');
});

$("#addNew").on("click", function(){
	var removeAnchorText = '<div class="close-item" title="Remove">&#10005;</div>';
	$("#missngEntryTable tbody tr").find("td:first").each(function(index){
		$(this).text(index + 1);

		if($("#missngEntryTable tbody tr").length > 0){
			// adding "Close Button" to the every row if it's not already being present.
			if($(this).parent().find('td:last > div.close-item').length == 0){
				$(this).parent().find('td:last > [name="unique_no_to[]"]').after(removeAnchorText);
				$(this).parent().addClass('entryItem');
			}
		}
	});

	var entryNUmber = $("#missngEntryTable tbody tr").length;
	if(entryNUmber == 0){
		removeAnchorText = '';
	}
	$("#missngEntryTable tbody").append('<tr class="entryItem"><td>'+(entryNUmber + 1)+'</td><td><select name="unique_no[]" data-curnav="0" data-minredeem="0"></select><div class="text-error">&nbsp;</div></td><td><input class="t-contoler" type="text" name="folio_num[]" placeholder="Eg: 123456789" required="required"><div class="text-error">&nbsp;</div></td><td><input type="checkbox" name="all_units[]" disabled><div class="text-error">&nbsp;</div></td><td><input class="t-contoler units" type="text" name="units[]" placeholder="Eg: 234.54" required="required"><div class="text-error">&nbsp;</div></td><td><select name="unique_no_to[]" data-minpurchase="0"></select><br/><input type="checkbox" class="auto_switch_options auto_switch" name="auto_switch[]" ><label class="auto_switch_options">Auto Switch to DAAF</label><br/><label class="auto_switch_options label_folio_num_to">Folio : </label><select class="auto_switch_options folio_num_to" name="folio_num_to[]" data-minpurchase="0"></select>'+ removeAnchorText +'<div class="text-error">&nbsp;</div></td></tr>');

	bindSelect2($('[name="unique_no[]"]:last'));
	bindSelect2($('[name="unique_no_to[]"]:last'));
	//bindSelect2($('[name="folio_num_to[]"]:last'));
});

$(document).on("click", ".close-item", function(){
	$(this).parents(".entryItem").remove();
	if($("#missngEntryTable tbody tr").length == 1){
		$("#missngEntryTable tbody tr:first").find('td:last > div.close-item').remove();
		$("#missngEntryTable tbody tr:first").removeClass('entryItem');
	}
	$("#missngEntryTable tbody tr").find("td:first").each(function(index){
		$(this).text(index + 1);
	});
});

var confirmed = false;

$('#btn_submit_switched_schemes').on('click', function(){

	var err_flag = 0,
	elementToFocus,
	err_msg = [],
	schemecode = '',
	folionum_obj,
	folionum = '',
	units_obj, units = '',
	client_id_obj,
	client_id = '',
	schemecode_to_obj,
	schemecode_to = '',
	all_units_obj = '',
	auto_switch_obj = '';

	client_id_obj = document.getElementById("client_id");

	client_id = $.trim($(client_id_obj).val());

	//this is to show error when client is not selected
	if(client_id == null || typeof client_id == 'undefined' || client_id == ''){
		err_flag = 1;
		$(client_id_obj).addClass('has-error');
		$(client_id_obj).parent().find('div.text-error').html('Client Code is required');
		if(elementToFocus == null || typeof elementToFocus == 'undefined'){
			elementToFocus = $(client_id_obj);
		}
	}
	else{
		$(client_id_obj).removeClass('has-error');
		$(client_id_obj).parent().find('div.text-error').html('&nbsp;');
	}

	var known_fund_details = [];
	var only_from_scheme_and_foliono = {};
	if($('[name="unique_no[]"]').length == 0){
		return false;
	}

	$('[name="unique_no[]"]').each(function(index){
		schemecode = $.trim($(this).val());
		folionum_obj = document.getElementsByName('folio_num[]')[index];
		folionum = $.trim($(folionum_obj).val());
		units_obj = document.getElementsByName('units[]')[index];
		units = $.trim($(units_obj).val());
		all_units_obj = document.getElementsByName('all_units[]')[index];
		auto_switch_obj = document.getElementsByName('auto_switch[]')[index];
		schemecode_to_obj = document.getElementsByName('unique_no_to[]')[index];
		schemecode_to = $.trim($(schemecode_to_obj).val());

		if(schemecode == null || typeof schemecode == 'undefined' || schemecode == ''){
			err_flag = 1;
			$(this).next('.select2-container').addClass('has-error');
			$(this).parent().find('div.text-error').html('From ISIN is required');
			if(elementToFocus == null || typeof elementToFocus == 'undefined'){
				elementToFocus = $(this);
			}
		}
		else{
			$(this).next('.select2-container').removeClass('has-error');
			$(this).parent().find('div.text-error').html('&nbsp;');
		}

		if(schemecode_to == null || typeof schemecode_to == 'undefined' || schemecode_to == ''){
			err_flag = 1;
			$(schemecode_to_obj).parent().find('.select2-container').addClass('has-error');
			$(schemecode_to_obj).parent().find('div.text-error').html('To ISIN is required');
			if(elementToFocus == null || typeof elementToFocus == 'undefined'){
				elementToFocus = $(schemecode_to_obj);
			}
		}
		else if(schemecode == schemecode_to){
		  err_flag = 1;
		  $(schemecode_to_obj).parent().find('.select2-container').addClass('has-error');
		  $(schemecode_to_obj).parent().find('div.text-error').html('TO Scheme can not be same as From Scheme i.e. you can not perform inter switch between same schemes!');
		  if(elementToFocus == null || typeof elementToFocus == 'undefined'){
				elementToFocus = $(schemecode_to_obj);
			}
		}
		else{
			$(schemecode_to_obj).parent().find('.select2-container').removeClass('has-error');
			$(schemecode_to_obj).parent().find('div.text-error').html('&nbsp;');
		}

		if(folionum == null || typeof folionum == 'undefined' || folionum == ''){
			err_flag = 1;
			$(folionum_obj).addClass('has-error');
			$(folionum_obj).next('div.text-error').html('Folio number is required');
			if(elementToFocus == null || typeof elementToFocus == 'undefined'){
				elementToFocus = $(folionum_obj);
			}
		}
		else if(schemecode != ''){
			if(only_from_scheme_and_foliono[schemecode +'||'+ folionum] == null || typeof only_from_scheme_and_foliono[schemecode +'||'+ folionum] == 'undefined'){
				only_from_scheme_and_foliono[schemecode +'||'+ folionum] = {'all_checked':0, 'not_checked':0, 'row_index':index};
			}
			// adding an extra check, for FOLIO NUMBERS ending with "/0" should give an error, if combination of FROM SCHEME & FOLIO NUMBER without ending "/0" should also give an error
			if(folionum.substr(-2) == '/0'){
				var folionum_without_ending_zero = folionum.substr(0, (folionum.length - 2));
				if(only_from_scheme_and_foliono[schemecode +'||'+ folionum_without_ending_zero] == null || typeof only_from_scheme_and_foliono[schemecode +'||'+ folionum_without_ending_zero] == 'undefined'){
					only_from_scheme_and_foliono[schemecode +'||'+ folionum_without_ending_zero] = {'all_checked':0, 'not_checked':0, 'row_index':index};
				}
			}
			// Validation for auto_switch_obj
			/*
			.....
			*/
			if(!$(all_units_obj).is(':checked')){
				only_from_scheme_and_foliono[schemecode +'||'+ folionum]['not_checked'] += 1;
				// check added for FOLIO NUMBERS ending with "/0"
				if(folionum.substr(-2) == '/0'){
					folionum_without_ending_zero = folionum.substr(0, (folionum.length - 2));
					only_from_scheme_and_foliono[schemecode +'||'+ folionum_without_ending_zero]['not_checked'] += 1;
				}
			}
			else{
				only_from_scheme_and_foliono[schemecode +'||'+ folionum]['all_checked'] += 1;
				// check added for FOLIO NUMBERS ending with "/0"
				if(folionum.substr(-2) == '/0'){
					folionum_without_ending_zero = folionum.substr(0, (folionum.length - 2));
					only_from_scheme_and_foliono[schemecode +'||'+ folionum_without_ending_zero]['all_checked'] += 1;
				}
			}

			/*
			if(schemecode_to != '' && !$(auto_switch_obj).is(':checked')){
			  if($.inArray(schemecode +'||'+ folionum + '||' + schemecode_to, known_fund_details) == -1){
				  $(folionum_obj).removeClass('has-error');
				  $(folionum_obj).next('div.text-error').html('&nbsp;');
				  known_fund_details.push(schemecode +'||'+ folionum + '||' + schemecode_to);
				  // checking whether FOLIO NUMBER ending with "/0" and same FOLIO NUMBER without "/0" is present or not
				  if(folionum.substr(-2) == '/0' && $.inArray(schemecode +'||'+ folionum.substr(0, (folionum.length - 2)) + '||' + schemecode_to, known_fund_details) != -1){
					  err_flag = 1;
					  $(folionum_obj).addClass('has-error');
					  $(folionum_obj).next('div.text-error').html('From ISIN, Folio number & To ISIN combination details already present');
					  if(elementToFocus == null || typeof elementToFocus == 'undefined'){
						  elementToFocus = $(folionum_obj);
					  }
				  }
			  }
			  else{
				  err_flag = 1;
				  $(folionum_obj).addClass('has-error');
				  $(folionum_obj).next('div.text-error').html('From ISIN, Folio number & To ISIN combination details already present');
				  if(elementToFocus == null || typeof elementToFocus == 'undefined'){
					  elementToFocus = $(folionum_obj);
				  }
			  }
			}
			*/
		}

		if(!$(all_units_obj).is(':checked')) { // If All Units are not selected (i.e. All units checkbox is not checked) then only validate conditions for units txtbox
			if(units == null || typeof units == 'undefined' || units == ''){
				err_flag = 1;
				$(units_obj).addClass('has-error');
				$(units_obj).parent().find('div.text-error').html('Units are required');
				if(elementToFocus == null || typeof elementToFocus == 'undefined'){
					elementToFocus = $(units_obj);
				}
			}
			else if(!$.isNumeric(units)){
				err_flag = 1;
				$(units_obj).addClass('has-error');
				$(units_obj).parent().find('div.text-error').html('Units must have numbers only');
				if(elementToFocus == null || typeof elementToFocus == 'undefined'){
					elementToFocus = $(units_obj);
				}
			}
			else{
				// step 1: Checking units entered are more than minium redemption quantity or not

				var from_scheme_min_redeem_units = (($(this).attr('data-minredeem') != null && $.isNumeric($(this).attr('data-minredeem')))?$(this).attr('data-minredeem'):0);
				from_scheme_min_redeem_units = parseFloat(from_scheme_min_redeem_units).toFixed(3);
				if(parseFloat(units) < parseFloat(from_scheme_min_redeem_units)){
					err_flag = 1;
					$(units_obj).addClass('has-error');
					$(units_obj).parent().find('div.text-error').html('Units must be greater than from scheme minimum redemption units i.e. '+ from_scheme_min_redeem_units);
					if(elementToFocus == null || typeof elementToFocus == 'undefined'){
						elementToFocus = $(units_obj);
					}
				}
				else{
					$(units_obj).removeClass('has-error');
					$(units_obj).parent().find('div.text-error').html('&nbsp;');
				}

				// step 2: Checking From scheme current valuation must be greater than To scheme minimum purchase amount
				var from_scheme_curnav = (($(this).attr('data-curnav') != null && $.isNumeric($(this).attr('data-curnav')))?$(this).attr('data-curnav'):0)
				var from_scheme_valuation = parseFloat(units) * parseFloat(from_scheme_curnav);
				from_scheme_valuation = parseFloat(from_scheme_valuation.toFixed(3));
				// console.log('From Scheme Nav=', from_scheme_curnav, ' :: Units=', units, ' :: valuation=', from_scheme_valuation);
				
				var to_scheme_min_purchase_amt = (($(schemecode_to_obj).attr('data-minpurchase') != null && $.isNumeric($(schemecode_to_obj).attr('data-minpurchase')))?$(schemecode_to_obj).attr('data-minpurchase'):0);
				to_scheme_min_purchase_amt = parseFloat(to_scheme_min_purchase_amt);
				//console.log('To scheme minimum purchase amount=', to_scheme_min_purchase_amt);

				otm_amount = $('#otm_amount').val();

				if(from_scheme_valuation > otm_amount && otm_amount > 0){
					err_flag = 1;
					$(this).next('.select2-container').addClass('has-error');
					$(this).parent().find('div.text-error').html('Your order value exceeds the OTM value, kindly split the amount accordingly into separate orders.');
					if(elementToFocus == null || typeof elementToFocus == 'undefined'){
						elementToFocus = $(this);
					}
					//confirmed = confirm("Your order value exceeds the OTM value, kindly split the amount accordingly into separate orders.");
				}else
				if(from_scheme_valuation < to_scheme_min_purchase_amt){
					err_flag = 1;
					$(this).next('.select2-container').addClass('has-error');
					$(this).parent().find('div.text-error').html('From scheme valuation amount which is '+ from_scheme_valuation +' must be greater than minimum purchase amount of To scheme i.e. '+ to_scheme_min_purchase_amt);
					if(elementToFocus == null || typeof elementToFocus == 'undefined'){
						elementToFocus = $(this);
					}
				}
				else{
					$(this).next('.select2-container').removeClass('has-error');
					$(this).parent().find('div.text-error').html('&nbsp;');
				}
			}
		}
		else{
			$(units_obj).removeClass('has-error');
			$(units_obj).parent().find('div.text-error').html('&nbsp;');
		}
	});

	$.each(only_from_scheme_and_foliono, function(key, value){
		var all_units_obj = document.getElementsByName('all_units[]')[value.row_index];
		if(parseInt(value.all_checked) > 0 && parseInt(value.not_checked) > 0){
			// when ALL UNITS is checked & other rows with same Schemecode/Folio Number
			err_flag = 1;
			$(all_units_obj).addClass('has-error');
			$(all_units_obj).parent().find('div.text-error').html('From ISIN, Folio number & All Units combination details already present');
		}
		else if(parseInt(value.all_checked) > 1){
			// when ALL UNITS checkboxes checked more than 1
			err_flag = 1;
			$(all_units_obj).addClass('has-error');
			$(all_units_obj).parent().find('div.text-error').html('From ISIN, Folio number & All Units combination details already present');
		}
		else{
			$(all_units_obj).removeClass('has-error');
			$(all_units_obj).next('div.text-error').html('&nbsp;');
		}
	});

	if(err_flag == 0 /* && confirmed */){
		
		var autoswitch = $('input.auto_switch').is(':checked'); 

		$.ajax({
			url:"{{ url('InterAMCSwitch/api/sendOtpToClient') }}",
			type: "POST",
			data: {
				client_id : client_id,
				autoswitch : autoswitch
			},
			dataType: "json",
			error: function(){
				swal('Error','Error! Unable to process request.Please try again!');
			},
			beforeSend : function(){
				$('.p-rmf-loader').show();
			},
			success: function(response){
			   if(response.status=="success"){
					$("#submitOtp").val('');
					$("#ClientOTPModal").modal("show");
					$("#got_otp_msg").text("OTP has been sent on your SMS, Whatsapp & Email. Please check.");
			   }
			},
			complete: function(){
				$('.p-rmf-loader').hide();
			}
		});       
	}
	
});

$("#submitOtpBtn").on("click", function(){
	var otp =     $.trim($("#submitOtp").val()); 
	var client_id = $.trim($("#client_id").val());

	if(otp=="" || otp.length < 4){
		$("#got_otp_msg").text("");
		$("#invalid_otp_msg").text("");
		$("#submitOtpAlert").show();
		$("#submitOtp").focus();
	}else if(!$.isNumeric(otp)){
		$("#got_otp_msg").text("");
		$("#submitOtpAlert").hide();
		$("#invalid_otp_msg").text("OTP must have numbers only");
		$("#submitOtp").focus();
	}else{
		$("#submitOtpAlert").hide(); 
		$("#invalid_otp_msg").text("");

		var otp = $("#submitOtp").val();

		$.ajax({
			url:"{{ url('InterAMCSwitch/api/verifyOTPClient') }}",
			type:"POST",
			data:{
				client_id : client_id,
				otp : otp
			},
			dataType: "json",
			beforeSend : function(){
				$('.p-rmf-loader').show();       
			},
			error: function(){
				swal('Alert','something went wrong');
			},
			success: function(response){
				$('.p-rmf-loader').hide();
				if(response.status == 'success'){
					
					$('[name="auto_switch[]"]').prop("disabled", true);

					var inputdata = $('#frm_inter_switch_schemes').serializeArray();$

					$('[name="auto_switch[]"]').prop("disabled", false);
					
					$.each($('[name="all_units[]"]'), function(index, value){
						var selected_val = 0;
						if($(this).is(':checked')){
							selected_val = 1;
						}
						inputdata.push({"name":"select_all_units[]", "value":selected_val});
					});

					$.each($('[name="auto_switch[]"]'), function(index, value){
						var selected_val = 0;
						if($(this).is(':checked')){
							selected_val = 1;
						}
						inputdata.push({"name":"select_auto_switch[]", "value":selected_val});
					});

					$.ajax({
						url:"{{ url('InterAMCSwitch/api/saveInterSwitchSchemes') }}",
						type: "POST",
						data: inputdata,
						dataType: "json",
						error: function(){
							swal('Error','Error! Unable to process request.Please try again!');
						},
						beforeSend : function(){
							$('.p-rmf-loader').show(); 
						},
						success: function(response){
							$('.p-rmf-loader').hide();
							if(response.err_flag == 1){
								var err_msg = '';
								if(response.err_msg != null){
									$.each(response.err_msg, function(key, value){
										err_msg += value +'\n';
									});
								}
								if(err_msg == ''){
									err_msg = 'Unable to process your request, please try again later';
								}
								swal('Error',err_msg);
							}
							else{

								$txtMsg = '';

								if(response.result == 1){
									$("#ClientOTPModal").modal("hide");

									autoswitch_text = ' ';

									if(response.auto_switch_response != null  && typeof response.auto_switch_response !== 'undefined'){
										$.each(response.auto_switch_response, function(key, value){
											if(value.err_flag == 1){
												if(value.err_msg.length == 0){
													if(typeof value.order_message !== 'undefined'){
														$txtMsg += value.order_message+'\n';
														autoswitch_text = ' and AutoSwitch '
													}
												}
												if(typeof value.error !== 'undefined'){
													$txtMsg += value.error+'\n';
												}
											}else{
												autoswitch_text = ' and AutoSwitch '
											}
										});
									}

									var txtMsg = "OTP submitted and InterSwitch" + autoswitch_text + "details saved successfully!";
									if(response.no_of_processed_orders != null && typeof response.no_of_processed_orders != 'undefined'){
										txtMsg += "\nNumber of orders processed are "+ response.no_of_processed_orders+"\n";
										txtMsg += $txtMsg;
									}

									swal({title: "", text: txtMsg, type: "success"}).then(function(){
											location.reload();
											}                                           
									);
									
								}
								else{
									swal('Alert','Fund details not saved, please try again later');
								}
							}
						},
						complete: function(){
							$('.p-rmf-loader').hide();
						}
					});
				} 
				else{
					$("#submitOtpAlert").show();
					$("#submitOtp").focus();
				}
			},
			complete: function(jqXHR){
			
			}
		});        
	}
});

$("#resendOtpBtn").on("click", function(){

	var client_id  =  $.trim($('#client_id').val());
	var autoswitch = $('input.auto_switch').is(':checked');

	$.ajax({
		url:"{{ url('InterAMCSwitch/api/sendOtpToClient') }}",
		type: "POST",
		data: {
			client_id : client_id,
			autoswitch : autoswitch
		},
		dataType: "json",
		error: function(){
			swal('Error','Error! Unable to process request.Please try again!');
		},
		beforeSend : function(){
			$('.p-rmf-loader').show();
		},
		success: function(response){
			if(response.status=="success"){
				$("#got_otp_msg").text("OTP has been resent on your SMS,Whatsapp & Email. Please check.");
				$("#submitOtpAlert").hide();
				$("#invalid_otp_msg").text("");
			}else{
				swal('Error','Sorry! Something went wrong!Try again!');
			}
		},
		complete: function(){
			$('.p-rmf-loader').hide();
		}
	});     
});

$(document).ready(function() {

    bindSelectClientData($('[name="client_id"]:first'));

	bindSelect2($('[name="unique_no[]"]:first'));

	bindSelect2($('[name="unique_no_to[]"]:first'));

	//bindSelect2($('[name="folio_num_to[]"]:first'));

	$('input.auto_switch').prop("checked", false).prop("disabled", true);

	$('#is_alert').hide();

	$('input').val('');
});

</script>
@endsection

