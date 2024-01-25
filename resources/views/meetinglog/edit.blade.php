<style>.select2-container {
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
	.angle-left {
		top: 6px !important;
	}
	.angle-right {
		top: 6px !important;
	}
	</style>
@php
   $arr_meeting_mode = config('constants.MEETING_MODE');
   $arr_meeting_purpose = config('constants.MEETING_PURPOSE');
   $arr_alternate_contact_options = array();
   if(!empty($partner_data->alternate_name_1) || !empty($partner_data->alternate_email_1) || !empty($partner_data->alternate_mobile_1)){
      $str_value  = $partner_data->alternate_name_1??'';
      $str_value .= (!empty($str_value)?"\\":"") . ($partner_data->alternate_email_1??'');
      $str_value .= (!empty($str_value)?"\\":"") . ($partner_data->alternate_mobile_1??'');
      $arr_alternate_contact_options[] = array('key' => 'alternate_contact_1', 'value' => $str_value);
   }
   if(!empty($partner_data->alternate_name_2) || !empty($partner_data->alternate_email_2) || !empty($partner_data->alternate_mobile_2)){
      $str_value  = $partner_data->alternate_name_2??'';
      $str_value .= (!empty($str_value)?"\\":"") . ($partner_data->alternate_email_2??'');
      $str_value .= (!empty($str_value)?"\\":"") . ($partner_data->alternate_mobile_2??'');
      $arr_alternate_contact_options[] = array('key' => 'alternate_contact_2', 'value' => $str_value);
   }
   if(!empty($partner_data->alternate_name_3) || !empty($partner_data->alternate_email_3) || !empty($partner_data->alternate_mobile_3)){
      $str_value  = $partner_data->alternate_name_3??'';
      $str_value .= (!empty($str_value)?"\\":"") . ($partner_data->alternate_email_3??'');
      $str_value .= (!empty($str_value)?"\\":"") . ($partner_data->alternate_mobile_3??'');      
      $arr_alternate_contact_options[] = array('key' => 'alternate_contact_3', 'value' => $str_value);
   }
   if(!empty($partner_data->alternate_name_4) || !empty($partner_data->alternate_email_4) || !empty($partner_data->alternate_mobile_4)){
      $str_value  = $partner_data->alternate_name_4??'';
      $str_value .= (!empty($str_value)?"\\":"") . ($partner_data->alternate_email_4??'');
      $str_value .= (!empty($str_value)?"\\":"") . ($partner_data->alternate_mobile_4??'');
      $arr_alternate_contact_options[] = array('key' => 'alternate_contact_4', 'value' => $str_value);
   }
   if(!empty($partner_data->alternate_name_5) || !empty($partner_data->alternate_email_5) || !empty($partner_data->alternate_mobile_5)){
      $str_value  = $partner_data->alternate_name_5??'';
      $str_value .= (!empty($str_value)?"\\":"") . ($partner_data->alternate_email_5??'');
      $str_value .= (!empty($str_value)?"\\":"") . ($partner_data->alternate_mobile_5??'');
      $arr_alternate_contact_options[] = array('key' => 'alternate_contact_5', 'value' => $str_value);
   }
@endphp
@extends('../layout')
@section('title', 'Edit a meeting log')
@section('breadcrumb_heading', 'Meeting Log >> Edit')

@section('content')
<div class="row">
  <!--div class="col-md-12">
    <div class="border-bottom display-flex">
      <h2 class="">Meeting Log : Create </span></h2>
    </div>
  </div-->
  <div class="col-lg-12">
    <div class="mt-2">
      <form action="{{url('update_meeting_data')}}" method="post" onsubmit="return ValidateForm();">
        @csrf
        <div class="row">
          <div class="col-lg-12">
            <div class="row form-inline">
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>ARN</label>
                  <input type="text" class="form-control" id="arn_text" value="{{ $partner_data->ARN }}" readonly/>
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Meeting Mode</label>
                  <select class="form-control" id="meeting_mode" name="meeting_mode">
                    <option value="">Select Mode</option>
                    @foreach($arr_meeting_mode as $key => $value)
                    <option value="{{$key}}"{{($partner_data->meeting_mode == $key)?'selected':''}}>{{$value}}</option>
                    @endforeach
                  </select>
                  <span class="error">{{ $errors->first('meeting_mode') }}</span>
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Contact Person</label>
                  <select class="form-control" name="contact_person" id="contact_person">
                    <optgroup label="ARN">
                      <option value="{{$partner_data->contact_person_name}}"{{($partner_data->contact_person_name == $partner_data->contact_person_name)?'selected':''}}>{{$partner_data->contact_person_name}}\{{$partner_data->contact_person_email}}\{{$partner_data->contact_person_mobile}}</option>
                    </optgroup>
                    <optgroup label="Product Approval">
                      @if(isset($partner_data->product_approval_person_email) && !empty($partner_data->product_approval_person_email) && isset($partner_data->product_approval_person_mobile) && !empty($partner_data->product_approval_person_mobile))
                        <option value="product_apporver_present"{{ old('contact_person') == "product_apporver_present" ? 'selected' : '' }}>{{$partner_data->product_approval_person_name}}\{{$partner_data->product_approval_person_email}}\{{$partner_data->product_approval_person_mobile}}</option>
                      @else
                        <option value="product_approver_other"{{ old('contact_person') == "product_approver_other" ? 'selected' : '' }}>Add detail</option>
                      @endif
                    </optgroup>
                    <optgroup label="Sales Drive Person">
                      @if(isset($partner_data->sales_drive_person_email) && !empty($partner_data->sales_drive_person_email) && isset($partner_data->sales_drive_person_mobile) && !empty($partner_data->sales_drive_person_mobile))
                        <option value="sales_provide_present"{{ old('contact_person') == "sales_provide_present" ? 'selected' : '' }}>{{$partner_data->sales_drive_person_name}}\{{$partner_data->sales_drive_person_email}}\{{$partner_data->sales_drive_person_mobile}}</option>
                      @else
                        <option value="sales_provide_other"{{old('contact_person') == "sales_provide_other" ? 'selected' : '' }}>Add detail</option>
                      @endif
                    </optgroup>
                    <optgroup label="Alternate Contacts">
                      @foreach($arr_alternate_contact_options as $value)
                        <option value="{{$value['key']}}"{{old('contact_person')== $value['key'] ? 'selected' : '' }}>{{$value['value']}}</option>
                      @endforeach
                    </optgroup>
                    @if(count($arr_alternate_contact_options) < 5)
                      <optgroup label="None of the Above">
                        <option value="other"{{old('contact_person') == "other" ? 'selected' : '' }}>Add Detail</option>
                      </optgroup>
                    @endif
                  </select>
                </div><!--/.form-group-->
              </div><!--/.col-->
            </div><!--/.row form-inline-->
          </div><!--/.col-lg-12-->
        </div><!--/.row-->
        <div class="row arn_detail" style="display:none;">
          <div class="col-lg-12">
            <div class="row form-line">
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Name:</label>
                  <input type="hidden" class="form-control" id="ARN" name="ARN" value="{{$partner_data->ARN}}"/>
                  <input type="hidden" class="form-control" name="logid" value="{{$partner_data->id}}"/>
                  <input type="hidden" class="form-control" id="arn_name" name="arn_name" value="{{$partner_data->contact_person_name}}"/>
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Name:</label>
                  <input type="hidden" class="form-control" id="arn_mobile" name="arn_mobile" value="{{$partner_data->contact_person_mobile}}"/>
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Name:</label>
                  <input type="hidden" class="form-control" id="arn_email" name="arn_email" value="{{$partner_data->contact_person_email}}"/>
                </div><!--/.form-group-->
              </div><!--/.col-->
            </div><!--/.row form-inline-->
          </div><!--/.col-lg-12-->
        </div><!--/.row arn_detail-->
        <div class="row product_approval" style="display:none;">
          <div class="col-lg-12">
            <div class="row form-line">
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Name:</label>
                  <input type="hidden" class="form-control" id="approval_name" name="approval_name" value="{{$partner_data->product_approval_person_name}}"/>
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Name:</label>
                  <input type="hidden" class="form-control" id="approval_mobile" name="approval_mobile" value="{{$partner_data->product_approval_person_mobile}}"/>
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Name:</label>
                  <input type="hidden" class="form-control" id="approval_email" name="approval_email" value="{{$partner_data->product_approval_person_email}}"/>
                </div><!--/.form-group-->
              </div><!--/.col-->
            </div><!--/.row form-inline-->
          </div><!--/.col-lg-12-->
        </div><!--/.row product_approval-->
        <div class="row sales_drive" style="display:none;">
          <div class="col-lg-12">
            <div class="row form-line">
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Name:</label>
                  <input type="hidden" class="form-control" id="sales_name" name="sales_name" value="{{$partner_data->sales_drive_person_name}}"/>
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Name:</label>
                  <input type="hidden" class="form-control" id="sales_mobile" name="sales_mobile" value="{{$partner_data->sales_drive_person_mobile}}"/>
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Name:</label>
                  <input type="hidden" class="form-control" id="sales_email" name="sales_email" value="{{$partner_data->sales_drive_person_email}}"/>
                </div><!--/.form-group-->
              </div><!--/.col-->
            </div><!--/.row form-inline-->
          </div><!--/.col-lg-12-->
        </div><!--/.row sales_drive-->
        <div class="row alernate" style="display:none;">
          <div class="col-lg-12">
            <div class="row form-line">
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Name:</label>
                  <input type="hidden" class="form-control" id="alternate_name_1" name="alternate_name_1" value="{{$partner_data->alternate_name_1}}"/>
                  <input type="hidden" class="form-control" id="alternate_name_2" name="alternate_name_2" value="{{$partner_data->alternate_name_2}}"/>
                  <input type="hidden" class="form-control" id="alternate_name_3" name="alternate_name_3" value="{{$partner_data->alternate_name_3}}"/>
                  <input type="hidden" class="form-control" id="alternate_name_4" name="alternate_name_4" value="{{$partner_data->alternate_name_4}}"/>
                  <input type="hidden" class="form-control" id="alternate_name_5" name="alternate_name_5" value="{{$partner_data->alternate_name_5}}"/>
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Mobile:</label>
                  <input type="hidden" class="form-control" id="alternate_mobile_1" name="alternate_mobile_1" value="{{$partner_data->alternate_mobile_1}}"/>
                  <input type="hidden" class="form-control" id="alternate_mobile_2" name="alternate_mobile_2" value="{{$partner_data->alternate_mobile_2}}"/>
                  <input type="hidden" class="form-control" id="alternate_mobile_3" name="alternate_mobile_3" value="{{$partner_data->alternate_mobile_3}}"/>
                  <input type="hidden" class="form-control" id="alternate_mobile_4" name="alternate_mobile_4" value="{{$partner_data->alternate_mobile_4}}"/>
                  <input type="hidden" class="form-control" id="alternate_mobile_5" name="alternate_mobile_5" value="{{$partner_data->alternate_mobile_5}}"/>
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                <label>Email:</label>
                  <input type="hidden" class="form-control" id="alternate_email_1" name="alternate_email_1" value="{{$partner_data->alternate_email_1}}"/>
                  <input type="hidden" class="form-control" id="alternate_email_2" name="alternate_email_2" value="{{$partner_data->alternate_email_2}}"/>
                  <input type="hidden" class="form-control" id="alternate_email_3" name="alternate_email_3" value="{{$partner_data->alternate_email_3}}"/>
                  <input type="hidden" class="form-control" id="alternate_email_4" name="alternate_email_4" value="{{$partner_data->alternate_email_4}}"/>
                  <input type="hidden" class="form-control" id="alternate_email_5" name="alternate_email_5" value="{{$partner_data->alternate_email_5}}"/>
                </div><!--/.form-group-->
              </div><!--/.col-->
            </div><!--/.row form-inline-->
          </div><!--/.col-lg-12-->
        </div><!--/.row alternate-->
        <div class="row other">
          <div class="col-lg-12">
            <div class="row form-line">
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Contact name:</label>
                  <input type="text" class="form-control" id="contact_name" name="contact_name" minlength="3" value="{{old('contact_name', $partner_data->contact_person_name)}}"/>
                  <span class="error">{{ $errors->first('contact_name') }}</span>
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Contact Mobile:</label>
                  <input type="text" class="form-control" id="contact_mobile" maxlength="10" name="contact_mobile" value="{{old('contact_mobile', $partner_data->contact_person_mobile)}}"/>
                  <span class="error">{{ $errors->first('contact_mobile') }}</span>
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Contact Email:</label>
                  <input type="text" class="form-control" id="contact_email" name="contact_email" value="{{old('contact_email', $partner_data->contact_person_email)}}"/>
                  <span class="error">{{ $errors->first('contact_email') }}</span>
                </div><!--/.form-group-->
              </div><!--/.col-->
            </div><!--/.row form-inline-->
          </div><!--/.col-lg-12-->
        </div><!--/.row other-->
        <div class="row">
          <div class="col-lg-12">
            <div class="row form-inline">
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Start Time</label>
                  <div class='input-group date'>
                    @php
                      $max=date("Y-m-d")."T".date("H:i:s");
                      $endTime = strtotime("+14 days", strtotime(date('Y-m-d H:i')));
                      $endTime = date('Y-m-d',$endTime)."T".date("H:i:s",$endTime);
                    @endphp
                    <input type='datetime-local' class="form-control" id='start_time' name="start_time" value="{{$partner_data->start_datetime}}" max="{{$endTime}}" />
                    <span class="error">{{ $errors->first('start_time') }}</span>
                  </div><!--/.input-group date-->
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>End Time</label>
                  <div class='input-group date'>
                    <input type='datetime-local' class="form-control" id='end_time' name="end_time" max="{{$endTime}}" value="{{$partner_data->end_datetime}}" />
                    <span class="error">{{ $errors->first('end_time') }}</span>
                  </div><!--/.input-group date-->
                </div><!--/.form-group-->
              </div><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label >Meeting Remarks</label>
                  <textarea id="remarks" name="remarks"  class="form-control" placeholder="Remarks">{{$partner_data->meeting_remarks}}</textarea>
                </div><!--/.form-group-->
              </div><!--/.col-->
            </div>

          </div>
        </div><!--/.row-->
		<div class="row">
			<div class="col-lg-12">
			  <div class="row form-inline">
				<div class="col-md-8 col-sm-4">
				  <div class="form-group">
					<label>Meeting Purpose (Optional)</label>
					<select class="form-control select2_data" id="meeting_purpose" multiple="multiple" name="meeting_purpose[]">
					  {{-- <option value="">Select Meeting Purpose</option> --}}
					  @foreach($arr_meeting_purpose as $key => $value)
					     @php
                    // Check if the current value is in the exploded array
                    $selected = in_array($key, explode(',', $partner_data->meeting_purpose)) ? 'selected' : '';
					// print_r($partner_data->meeting_purpose);exit;
                    @endphp
					 <option value="{{$key}}" {{ $selected }}>{{$value}}</option>
					  {{-- <option value="{{$key}}"{{(old('meeting_purpose') == $key)?'selected':''}}>{{$value}}</option> --}}
					  @endforeach
					</select>
					<span class="error">{{ $errors->first('meeting_purpose') }}</span>
				  </div><!--/.form-group-->
				</div>

				<div class="col-md-8 col-sm-4">
					<div class="form-group">
					  <label>Select Meeting Tags User (Optional)</label>
					  <select class="form-control select2_data_bdm" id="bdm_data" multiple="multiple" name="bdm_data[]">
						{{-- <option value="">Select Meeting Purpose</option> --}}
						@foreach($bdmlist as $val)
						@php
						// Check if the current value is in the exploded array
						$selected = in_array($val['id'], explode(',', $partner_data->bdm_data)) ? 'selected' : '';
					@endphp
					<option value="{{$val['id']}}" {{ $selected }}>{{$val['name']}}</option>
						{{-- <option value="{{$val['id']}}">{{$val['name']}}</option> --}}
						@endforeach
					  </select>
					  <span class="error">{{ $errors->first('bdm_data') }}</span>
					</div><!--/.form-group-->
				  </div>
			  </div>
			</div>
		  </div><!--/.row-->
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <button type="submit" class="btn btn-primary">Update</button>
          </div><!--/.col-lg-12-->
        </div><!--/.row-->
      </form><!--/.form-->
    </div><!--/.mt-2-->
  </div><!--/.col-lg-12-->
</div><!--/.row mt-4-->
<h2>Distributors List Near to {{$partner_data->arn_holders_name}}</h2>
<table class="table table-bordered yajra-datatable">
	<thead>
		<tr>
			<th>Action</th>
			<th>AMFI - ARN</th>
			<th>RankMF Partner(Yes/No)</th>
			<th>AMFI - ARN Holder's Name</th>
			<th>AMFI - Address</th>
			<th>AMFI - Pin</th>
			<th>AMFI - Email</th>
			<th>AMFI - City</th>
			<th>AMFI - Telephone (R)</th>
			<th>AMFI - Telephone (O)</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

@endsection

@section('custom_scripts')
<script type="text/javascript">
	$(function () {
	  
	  var table = $('.yajra-datatable').DataTable({
		  processing: true,
		  serverSide: true,
		  ajax: {
				url: "{{ url()->current() }}",
				type:'post'
			},
		    columns: [
	
			  {data: 'action', name: 'action'},
			  {data: 'ARN', name: 'ARN'},
			  {data: 'is_rankmf_partner', name: 'is_rankmf_partner'},
			  {data: 'arn_holders_name', name: 'arn_holders_name'},
			  {data: 'arn_address', name: 'arn_address'},
			  {data: 'arn_pincode', name: 'arn_pincode'},
			  {data: 'arn_email', name: 'arn_email'},
			  {data: 'arn_city', name: 'arn_city'},
			  {data: 'arn_telephone_r', name: 'arn_telephone_r'},
			  {data: 'arn_telephone_o', name: 'arn_telephone_o'},
		
		  ],
			language: {
			paginate: {
			next: '<i class="icons angle-right"></i>',
			previous: '<i class="icons angle-left"></i>'  
			}
			},
	  });
	  
	});
  </script>

<script>
	 $(".select2_data").select2({
			closeOnSelect : false,
			placeholder : "Select Meeting Purpose",
			allowHtml: true,
			allowClear: true,
			tags: true // создает новые опции на лету
		});
		$(".select2_data_bdm").select2({
			closeOnSelect : false,
			placeholder : "Choose Meeting Tags User",
			allowHtml: true,
			allowClear: true,
			tags: true // создает новые опции на лету
		});

$(document).ready(function() {
   $( "#contact_person" ).change(function() {
      var group = $(this)
               .find('option:selected') // get selected option
               .parent()   // get that option's optgroup
               .attr("label");
      var option_value = $(this).val();
      // alert(option_value);
      if (group == 'ARN')
      {
         // alert($('#arn_name').val());
         $('#contact_name').val($('#arn_name').val());
         $('#contact_mobile').val($('#arn_mobile').val());
         $('#contact_email').val($('#arn_email').val());
      }
      else if(group == 'Product Approval')
      {
         if(option_value == 'product_apporver_present')
         {
         $('#contact_name').val($('#approval_name').val());
         $('#contact_mobile').val($('#approval_mobile').val());
         $('#contact_email').val($('#approval_email').val());
         }
         else if(option_value == 'product_approver_other')
         {
         $('#contact_name').val('');
         $('#contact_mobile').val('');
         $('#contact_email').val('');
         }
      }
      else if(group == 'Sales Drive Person')
      {
         if(option_value == 'sales_provide_present')
         {
         $('#contact_name').val($('#sales_name').val());
         $('#contact_mobile').val($('#sales_mobile').val());
         $('#contact_email').val($('#sales_email').val());
         }
         else if(option_value == 'sales_provide_other')
         {
         $('#contact_name').val('');
         $('#contact_mobile').val('');
         $('#contact_email').val('');
         }
      }
      else if(group == 'Alternate Contacts')
      {
         if(option_value == 'alternate_contact_1')
         {
            $('#contact_name').val($('#alternate_name_1').val());
            $('#contact_mobile').val($('#alternate_mobile_1').val());
            $('#contact_email').val($('#alternate_email_1').val());
         }
         if(option_value == 'alternate_contact_2')
         {
            $('#contact_name').val($('#alternate_name_2').val());
            $('#contact_mobile').val($('#alternate_mobile_2').val());
            $('#contact_email').val($('#alternate_email_2').val());
         }
         if(option_value == 'alternate_contact_3')
         {
            $('#contact_name').val($('#alternate_name_3').val());
            $('#contact_mobile').val($('#alternate_mobile_3').val());
            $('#contact_email').val($('#alternate_email_3').val());
         }
         if(option_value == 'alternate_contact_4')
         {
            $('#contact_name').val($('#alternate_name_4').val());
            $('#contact_mobile').val($('#alternate_mobile_4').val());
            $('#contact_email').val($('#alternate_email_4').val());
         }
         if(option_value == 'alternate_contact_5')
         {
            $('#contact_name').val($('#alternate_name_5').val());
            $('#contact_mobile').val($('#alternate_mobile_5').val());
            $('#contact_email').val($('#alternate_email_5').val());
         }
      }
      else if(group == 'None of the Above')
      {
         $('#contact_name').val('');
         $('#contact_mobile').val('');
         $('#contact_email').val('');
      }

   });
});

function ValidateForm(){
   var meeting_mode = $('#meeting_mode').val();
   var contact_person = $('#contact_person').val();
   var contact_name = $('#contact_name').val();
   var contact_mobile = $('#contact_mobile').val();
   var contact_email = $('#contact_email').val();
   var start_datetime = $('#start_time').val();
   var end_datetime = $('#end_time').val();
   var err_flag = false;

   if(meeting_mode==''){
      err_flag = true;
      $("#meeting_mode").next("span").text("Select Mode of a meeting!!");
   }
   else{
      $("#meeting_mode").next("span").html('');
   }

   if(contact_person == ''){
      err_flag = true;
      $("#meeting_mode").next("span").text("Select a Contact Person!!");
   }
   else{
      $("#contact_person").next("span").html('');
   }

   if(contact_name == ''){
      err_flag = true;
      $("#contact_name").next("span").text("Enter a contact a name!");
   }
   else{
      $("#contact_name").next("span").html('');
   }

   if(!validateMobile(contact_mobile)){
      err_flag = true;
      $("#contact_mobile").next("span").text("Enter a Valid Mobile number!!");
   }
   else{
      $("#contact_mobile").next("span").html('');
   }

   if(!validateEmail(contact_email)){
      err_flag = true;
      $("#contact_email").next("span").text("Enter a Valid Email address!!");
   }
   else{
      $("#contact_email").next("span").html('');
   }

   if(start_datetime ==''){
      err_flag = true;
      $("#start_time").next("span").text("Select Start DateTime of a Meeting!!");
   }
   else{
      $("#start_time").next("span").html('');
   }

   if(end_datetime ==''){
      err_flag = true;
      $("#end_time").next("span").text("Select End DateTime of a Meeting!!");
   }
   else{
      $("#end_time").next("span").html('');
   }

   if(!err_flag){
		var mode=$("#meeting_mode").val();
		if(mode == 'In Person Meeting'){
		return confirm('Notification will be sent to meeting person, please check all the details before confirming?');
		}
   }
   else{
      return false;
   }
}

function validateMobile(e) {
        e = $.trim(e);
        var t = /^[1-9][0-9]{9}$/;
        return t.test(e) ? 1 : 0;
    }

function validateEmail(e) {
        e = $.trim(e);
        var t = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        return t.test(e) ? 1 : 0;
    }
</script>
@endsection
