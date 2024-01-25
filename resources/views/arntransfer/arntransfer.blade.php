@extends('../layout')
@section('title', 'ARN Transfer')
@section('breadcrumb_heading', 'ARN Transfer')
@section('custom_head_tags')

@endsection

@section('style')
<style>
	.full-width{
		display:block;
	}
	*[disabled="disabled"] {
		cursor: not-allowed;
	}
</style>
@endsection

@section('content')
<style>
	.full-width{
		display:block;
	}
	.arn_alert, .pincode_alert{
		display:none;
	}
	.px-0{
		padding-left: 0px;
  		padding-right: 0px;
	}
	.require{
		color:#FF0000;
	}
	.full-width{
		display:block;
	}
	*[disabled="disabled"] {
		cursor: not-allowed;
	}
</style>
<div class="row mt-4">
	<div class="col-lg-12 mb-3 arn_alert">	
		<div class="alert alert-info" id="arn_alert" role="alert"></div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<span class="label label-default full-width"><b>Select Users</b></span>
		</div>
		<label><b>From</b><span class="require">*</span></label>
		<select id="oldbdm"  class="form-control">
			<option value="">Select User</option>
			@foreach($bdmlist as $bdm)
				<option value="{{ $bdm->id }}">{{ $bdm->name }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<span class="label label-default full-width">&nbsp;</span>
		</div>
		<label><b>To</b><span class="require">*</span></label>
		<select id="newbdm" class="form-control">
			<option value="">Select User</option>
			@foreach($bdmlist as $bdm)
				<option value="{{ $bdm->id }}">{{ $bdm->name }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-lg-12 mt-3">
		<div class="form-group">
			<span class="label label-default full-width"><b>Select ARN</b></span>
		</div>
		<div class="form-group">
			<select id="arns" class="form-control col-lg-3">
				<option id="arns" value="0">Select ARN</option>
			</select>
		</div>
		<div class="form-group">
			<span class="label label-default full-width"><b>Select Empanelled or Non Empanelled Type.</b></span>
		</div>
		<div class="form-group">
			<label class="radio-inline"><input name="arn" id="all" type="radio" value="0" checked>All</label>
			<label class="radio-inline"><input name="arn" id="emp_n" type="radio" value="1">Non Empanelled</label>
			<label class="radio-inline"><input name="arn" id="emp_y" type="radio" value="2">Empanelled</label>
		</div>
		<div class="col-lg-12 px-0 mb-2 pincode-info pincode_alert">	
			<div class="alert alert-warning" id="pincode_alert" role="alert"></div>
		</div>
		<div class="form-group">
			<span class="label label-default full-width"><b>Pincode</b></span>
		</div>
		<div class="form-group">
			<textarea id="pincode" class="form-control col-lg-12"></textarea>
		</div>
		<div class="form-group">
			<span class="label label-default full-width"><b>Remark</b><span class="require">*</span></span>
		</div>
		<div class="form-group">
			<textarea id="remark" class="form-control col-lg-6"></textarea>
		</div>
		<div class="form-group">
			<button type="button" class="btn btn-primary" id="submit_arn_tr">Transfer ARN</button>
		</div>
	</div>
</div>
@endsection

@section('custom_scripts')
    <script type="text/javascript">

		function getARNs(update,changepin){

			$("#newbdm option").show();

			if($('#oldbdm').val() != ''){
				$("#newbdm option[value=" + $('#oldbdm').val() + "]").hide();
			}

			if($('#newbdm').val() == $('#oldbdm').val()){
				$("#newbdm").val($("#newbdm option:first").val());
			}

			data = {
				from_direct_relationship_user_id : $('#oldbdm').val(),
				to_direct_relationship_user_id : $('#newbdm').val(),
				serviceable_pincode : $('#pincode').val().trim(),
				arns : $('#arns').val(),
				is_rankmf_partner : $("input[type='radio'][name='arn']:checked").val(),
				remark : $('#remark').val().trim(),
				use_serviceable_pincode : changepin
			};

			$.ajax
			({
				url: baseurl + '/arntransfer/getarn',
				type: 'post',
				data: data,
				success: function(data)
				{
					$('.arn_alert').show();
					if(!$('#arn_alert').hasClass('alert-info')){
						$('#arn_alert').addClass('alert-info');
					}
					if($('#arn_alert').hasClass('alert-danger')){
						$('#arn_alert').removeClass('alert-danger');
					}
					$('#pincode_alert').html("");
					if(typeof data.alert !== 'undefined'){
						$('.pincode_alert').show();
						$.each(data.alert, function(key, value) { 
							$('#pincode_alert').append(value + "<br/>");
						});
					}else{
						$('.pincode_alert').hide();
					}
					if(update){
						$('#arns').html('');
						$('#arns').append($("<option></option>").attr("value",0).text("Select ARN"));
						$.each(data.arns, function(key, value) { 
							$('#arns').append($("<option></option>").attr("value", value.ARN).text(value.ARN)); 
						});
						$("#arns").select2(); 
						$('#pincode_alert').html("");
						if(typeof data.alert !== 'undefined'){
							$('.pincode_alert').show();
							$.each(data.alert, function(key, value) { 
								$('#pincode_alert').append(value + "<br/>");
							});
						}else{
							$('.pincode_alert').hide();
						}
					}
					if(changepin){
						if(typeof data.pincodes !== 'undefined'){
							$("#pincode").val(data.pincodes.serviceable_pincode);
						}
					}
					if ($("#arns").val() == 0) {
						$("input[type='radio'][name='arn']").removeAttr("disabled");
						$("#pincode").removeAttr("disabled");
					} else {
						$("input[type='radio'][name='arn']").attr("disabled", true);
						$("#pincode").attr("disabled", true);
					}
				},
				error: function(jqXHR, textStatus, errorThrown){

				},
				complete: function(){
					$(".loader").hide();
				}
			});

		}

		$(document).ready(function(){

			$('#emp_y').change(function(){
				getARNs(true,false);
			});

			$('#emp_n').change(function(){
				getARNs(true,false);
			});

			$('#pincode').blur(function(){
				getARNs(true,false);
			});

			$('#pincode').on("change keyup paste", function() {
				let n = $(this).val().split("").reverse().join("").search(/([0-9]{6})/is);
				if(n == 0){
					getARNs(true,false);
				}
			});

			$('#all').change(function(){
				getARNs(true,false);
			});

			$('#oldbdm').change(function(){
				$("#arns").val($("#arns option:first").val());
				$("#pincode").val('');
				getARNs(true,true);
			});
			
			$('#arns').change(function(){
				if ($(this).val() == 0) {
					$("input[type='radio'][name='arn']").removeAttr("disabled");
					$("#pincode").removeAttr("disabled");
				} else {
					$("#all").prop("checked",true);
					$("input[type='radio'][name='arn']").attr("disabled", true);
					$("#pincode").val('');
					$("#pincode").attr("disabled", true);
				}
				getARNs(false,false);
			});

			transfer = (function(){

				data = {
					from_direct_relationship_user_id : $('#oldbdm').val(),
					to_direct_relationship_user_id : $('#newbdm').val(),
					serviceable_pincode : $('#pincode').val().trim(),
					arns : $('#arns').val(),
					is_rankmf_partner : $("input[type='radio'][name='arn']:checked").val(),
					remark : $('#remark').val().trim()
				};

				$.ajax
				({ 
					url: baseurl + '/arntransfer/transferarn',
					type: 'post',
					data: data,
					success: function(data)
					{
						$('.arn_alert').show();
						if(data.message.match("Transferred")){
							drm_alert({
								'head':'Alert',
								'body':data.message,
								'close':function(){
									$("#oldbdm").val($("#oldbdm option:first").val());
									$("#newbdm").val($("#newbdm option:first").val());
									$("#remark").val('');
									$("#pincode").val('');
									$('#arns').html('');
									$("#arns").select2();
									$(".pincode_alert").hide();
									location.reload();
								}
							});
						}else{
							drm_alert({
								'head':'Alert',
								'body':data.message
							});
						}
						$('#arn_alert').html(data.message);
					},
					error: function(jqXHR, textStatus, errorThrown){

					},
					complete: function(){
						$(".loader").hide();
					}
				});
			});

			$('#submit_arn_tr').click(function(){
				error = '';
				if($('#oldbdm').val().trim() == ''){
					error = 'Please select target user.<br/>';
				}
				if($('#newbdm').val().trim() == ''){
					error += 'Please select transferor user.<br/>';
				}
				if($('#remark').val().trim() == ''){
					error += 'Please fill remark.<br/>';
				}
				if(error != ''){
					drm_alert({
						'head':'Required fields',
						'body': error
					});
					return true;
				}
				drm_alert({
					'head':'Transfer Alert',
					'body':'Are you sure, you want to Transfer ARNs',
					'ok':transfer
				});
			});

			getARNs(true,true);
			$('.loader').hide();
			$('#arn_alert').hide();
		});
    </script>
@endsection