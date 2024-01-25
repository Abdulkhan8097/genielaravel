@php
$data_table_headings_html = '';
@endphp
@extends('../layout')
@section('title', 'BDM Goal Dashboard')
@section('breadcrumb_heading', 'BDM Goal Dashboard')

@section('custom_head_tags')
<link rel="stylesheet" type="text/css" href="{{asset('css/meetinglog.css')}}">
@endsection

@section('content')

<section class="mb-4 mt-4">
   <div class="col-lg-12">
      <div class="row">
         <div class="col-lg-12">
            <h2 class="border-bottom">Goal</h2>            
				<div class="row">
					<div class="col-lg-12">
					<div class="row form-inline">
						<div class="col-lg-4">
							<div class="form-group">
								<label>Target Meeting</label>
								<input id="target_meetings" name="target_meetings" type="text" class="form-control" value="{{$target_meetings}}" placeholder="Set Meeting Goal">
								<div class="error">&nbsp;</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group">
								<label>Target Call</label>
								<input id="target_calls" name="target_calls" type="text" class="form-control" value="{{$target_calls}}" placeholder="Set Call Goal">
								<div class="error">&nbsp;</div>
							</div>
						</div>
						<div class="">
							<div class="col-lg-12">
								<div class="">
								<button type="button" class="btn btn-primary" id="submit_goals">Submit</button>
								</div>
							</div>
						</div>
					</div>
					</div>
				</div>
            </div>
         </div>
      </div>
   </div>
</section>


@endsection

@section('custom_after_footer_html')

<!-- End View modal -->

@endsection

@section('custom_scripts')
    <script src="{{asset('js/select2.min.js')}}"></script>
    <script>
		$("#submit_goals").click(function(){
			target_meetings = $("#target_meetings").val();
			target_calls = $("#target_calls").val();
			_token = $("meta[name='csrf-token']").attr("content");
			$.ajax({
				type: 'POST',
				url: baseurl +'/goal/set',
				data: {
					target_meetings: target_meetings,
					target_calls: target_calls,
					_token: _token,
				},
				dataType: 'json',
				error: function(jqXHR, textStatus, errorThrown){
					$('.loader').hide();
					if(jqXHR.responseJSON != null && typeof jqXHR.responseJSON != 'undefined' && jqXHR.status == 401){
						prepare_error_text(jqXHR.responseJSON);
					}
					else{
						swal('', unable_to_process_request_text, 'warning');
					}
				},
				success: function(response) {
					$('.loader').hide();
					var msg ="something went wrong.";
					var swalType = 'warning';
					var swalTitle = 'Error';
					if(response.status == 'success'){
						swalTitle = 'Updated';
						swalType = 'success';
						msg =  response.msg;
					}
					swal(swalTitle, msg, swalType);
				}
			});

		}); 
	</script>
@endsection
