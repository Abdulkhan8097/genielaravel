@php
$todays_date = date('d/m/Y');
if(isset($data['date']) && !empty($data['date']) && strtotime($data['date']) !== FALSE){
  $todays_date = date('d/m/Y', strtotime($data['date']));
}

$yesterdays_date = date('d/m/Y');
if(isset($data['yesterdayDate']) && !empty($data['yesterdayDate']) && strtotime($data['yesterdayDate']) !== FALSE){
  $yesterdays_date = date('d/m/Y', strtotime($data['yesterdayDate']));
}
$rating = \App\Models\MeetinglogModel::starRating();
@endphp
@extends('../layout')
@section('title', 'Dashboard')
@section('breadcrumb_heading', 'Dashboard')

@section('custom_head_tags')

    <link rel="stylesheet" type="text/css" href="{{asset('plugins/fullcalendar-5.10.1/lib/main.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/custom-popper.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/dashboard.css')}}">

@endsection

@section('content')
	@if (!empty($rating))
	<div style="position: absolute;right: 30px;top: -73px;">
	<span style="margin-bottom: 3px;display:block">Your Partners Rating</span>
	<div class="rating-area">
	<span class="star-number">{{ $rating->rating }}</span>
	<div class="star-wrapper">
	@php
	$ratingValue = $rating->rating;
	$fullStars = floor($ratingValue); // Get the integer part of the rating
	$halfStar = ceil($ratingValue - $fullStars); // Check if there's a half star
	$emptyStars = 5 - $fullStars - $halfStar; // Calculate the remaining empty stars
	@endphp

	@for ($i = 0; $i < $fullStars; $i++)
	<i class="fa fa-star" aria-hidden="true"></i>
	@endfor

	@if ($halfStar)
	<i class="fa fa-star-half-o" aria-hidden="true"></i>
	@endif

	@for ($i = 0; $i < $emptyStars; $i++)
	<i class="fa fa-star-o" aria-hidden="true"></i>
	@endfor
	</div>
	<div class="total-ratings">
	<span>{{ number_format("$rating->count") }}</span> Response
	</div>
	</div>
	</div>								
	@endif
<div class="row mt-4">
  <div class="col-md-12 mb-2">
    <div class="row">
      <div class="col-md-12">
        <div class="faq-border-top">
          <div class="faq-box">
            <div id="accordian_arn_empanelled" class="faq-question">ARN EMPANELLED & NOT EMPANELLED <i class="arrow"></i></div><!--/.faq-question-->
            <div class="faq-answer">
              <div class="tab-content-item">
                <ul class="nav nav-tabs new-tab mt-0" id="myTab" role="tablist">
                  <li class="nav-item active">
                      <a class="nav-link" href="#userwise_details">Userwise Details</a>
                  </li>
                  @if(isset($data['flag_show_all_arn_data']) && $data['flag_show_all_arn_data'])
                  <li class="nav-item">
                      <a class="nav-link" href="#statewise_details">Statewise Details</a>
                  </li>
                  @endif
                </ul>
                <div class="tab-content  data-tabs">
                  <div class="tab-pane show active tab-list" id="userwise_details">
                    <div class="row mt-4">
                      <div class="col-lg-12">
                        <div class="mt-2 table-responsive">
                          <table id="panel_arnuserwise_count" class="display" style="width:100%">
                            <thead>
                              <tr>
                                <th>User</th>
                                <th>Not Empanelled</th>
                                <th>Empanelled</th>
                                <th>Total</th>
                              </tr>
                            </thead>
                            <tfoot>
                              <tr>
                                <th>Total</th>
                                <th></th>
                                <th></th>
                                <th></th>
                              </tr>
                            </tfoot>
                          </table><!--#panel_arnuserwise_count-->
                        </div><!--/.mt-2 table-responsive-->
                      </div><!--/.col-lg-12-->
                    </div><!--/.row mt-4-->
                  </div><!--#userwise_details-->
                  @if(isset($data['flag_show_all_arn_data']) && $data['flag_show_all_arn_data'])
                  <div class="tab-pane show tab-list" id="statewise_details" style="display:none;">
                    <div class="row mt-4">
                      <div class="col-lg-12">
                        <div class="mt-2 table-responsive">
                          <table id="panel_arnstatewise_count" class="display" style="width:100%">
                            <thead>
                              <tr>
                                <th>State</th>
                                <th>Not Empanelled</th>
                                <th>Empanelled</th>
                                <th>Total</th>
                              </tr>
                            </thead>
                            <tfoot>
                              <tr>
                                <th>Total</th>
                                <th></th>
                                <th></th>
                                <th></th>
                              </tr>
                            </tfoot>
                          </table><!--#panel_arnstatewise_count-->
                        </div><!--/.mt-2 table-responsive-->
                      </div><!--/.col-lg-12-->
                    </div><!--/.row mt-4-->
                  </div><!--#statewise_details-->
                  @endif
                </div>
              </div>
            </div><!--/.faq-answer-->
          </div><!--/.faq-box-->
		<div class="faq-box">
		  <div id="user_wise_goals" class="faq-question">Goal<i class="arrow"></i></div><!--/.faq-question-->
		  <div class="faq-answer" style="display:none;">
			<div class="row">
				<div class="col-md-4 col-sm-4">
					<div class="form-group">
						<label>Period</label>
						<select id="select_period" name="select_period_id" class="form-control">
						<option id="gday" value="Daily">Daily</option>
						<option id="gweek" value="Weekly">Weekly</option>
						<option id="gmonth" value="Monthly">Monthly</option>
						</select>
					</div>
				</div>
				@if(isset($data['bdmlist']))
				<div class="col-md-4 col-sm-4" id="user_select">
					<div class="form-group">
						<label>Choose User Name</label>
						<select id="select_user" name="select_user" class="form-control">
						@if(count($data['bdmlist']) > 1)
						<option value="0">Select User</option>
						@endif
						@foreach($data['bdmlist'] as $bdm)
						<option value="{{$bdm['id']}}">{{$bdm['name']}}</option>
						@endforeach
						</select>
					</div>
				</div>
				@endif
			</div><!--/.row-->
			<div class="row">
			  <div class="col-lg-12 col-md-12 col-sm-12">
				<div id="panel_goal_userwise_view" class="hidden-element arn_relationship_quality_score_calendar"></div>
				<div class="mt-2 table-responsive">
				  <table id="panel_goal_userwise" class="display" style="width:100%">
					<thead>
					  <tr>
						<th id="useranddate" >User</th>
						<th>Target Calls</th>
						<th>Achieved calls</th>
						<th>Target Meetings</th>
						<th>Achieved Meetings</th>
						<th>Achieved Percentage</th>
					  </tr>
					</thead>
				  </table><!--#panel_arnstatewise_count-->
				</div><!--/.mt-2 table-responsive-->
			  </div><!--/.col-lg-12 col-md-12 col-sm-12-->
			</div><!--/.row mt-4-->
		  </div><!--/.faq-answer-->
		</div><!-- end Goals tab -->
		</div><!--/.faq-border-top-->
	  </div><!--/.faq-border-top-->
      </div><!--/.col-md-12-->
    </div><!--/.row-->
  </div><!--/.col-md-12-->
</div><!--/.row mt-4-->

@endsection

@section('custom_scripts')

    <script src="{{asset('plugins/fullcalendar-5.10.1/lib/main.min.js')}}"></script>
    <script src="{{asset('plugins/fullcalendar-5.10.1/lib/popper.min.js')}}"></script>
    <script src="{{asset('plugins/fullcalendar-5.10.1/lib/tooltip.min.js')}}"></script>
    <script src="{{asset('js/dashboard.js?05102023')}}"></script>

@endsection
