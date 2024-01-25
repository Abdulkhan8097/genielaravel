@php
$copyright_date = date('Y');
$logged_in_user_name = Auth::user()->name??'';
$user_short_name_arr = explode(" ", $logged_in_user_name);
$logged_in_user_short_name = '';
if(count($user_short_name_arr) == 1){
    $logged_in_user_short_name .= strtoupper(substr($user_short_name_arr[0], 0, 2));
}
else{
    array_walk($user_short_name_arr, function($value, $key, $_user_data){
        $_user_data[0] .= strtoupper(substr($value, 0, 1));
    }, [&$logged_in_user_short_name]);
}
unset($user_short_name_arr);

$logged_in_user_email = Auth::user()->email??'';
$logged_in_user_id = intval(Auth::user()->id)??0;

$current_page_route = Route::getCurrentRoute()->uri();
$current_page_route_url = URL::to($current_page_route);
$retrieved_data = \App\Models\MeetinglogModel::getnotification_data($logged_in_user_id);
$todayMeeting_data = \App\Models\MeetinglogModel::getTodayMeetingNotificationData($logged_in_user_id);
$tagtodayMeeting = \App\Models\MeetinglogModel::getMeetingDataByTag($logged_in_user_id);
$todayPartnersBirthDayNotificationData = \App\Models\MeetinglogModel::getTodayPartnersBirthDayNotificationData();
$UpcomingPartnersBirthDayNotificationData = \App\Models\MeetinglogModel::getUpcomingPartnersBirthDayNotificationData();
//$count=count($retrieved_data)  + count($todayMeeting_data) + count($tagtodayMeeting) + count($todayPartnersBirthDayNotificationData);
// retrieving logged in user role and permission details, below variable available only view which are added using @include method. For @section, we need to recall the same statement again.
$logged_in_user_roles_and_permissions=session('logged_in_user_roles_and_permissions');
@endphp
<!doctype html>
<html lang="en">

<head>
    <title>@yield('title', 'RankMF DRM Panel')</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="baseurl" content="{{env('APP_URL')}}">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" href="/favicon.ico">

    @section('custom_meta_tags')
    @show
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@100;300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('css/style.css?v=1.10')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/datepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dataTables.min.css')}}">
    <!--link rel="stylesheet" type="text/css" href="{{asset('css/sweetalert.css')}}"-->

    @section('custom_head_tags')
    @show

    @section('style')
    @show

	<style>
		.notification-inner-group-link {
			width: 500px;
		}
		#tab_content {
			overflow: auto;
			height: 70vh;
		}
	</style>
</head>

<body class="dashboard m-page--fluid">
    <div class="policy-header-ctnr">
        <div class="display-flex-ctnr">
            <div class="display-flex-ctnr-lft">
                <div class="policy_logo">
                    <a class="logo" href="{{env('APP_URL')}}"><img src="{{asset('images/RankMF-Final-Logo192X192.png')}}"></a>
                    <a href="javascript:void(0);" class="toggler--left"><span></span></a>
                    <a href="javascript:void(0);" class="m-brand__toggler--left"><label>Menu</label><span></span></a>
                    <a class="header-toggler"><span></span><span></span><span></span></a>
                </div>
            </div>
        </div>
    </div>
    <!-- begin:: Main Container -->
    <div class="index-mainbox">
        <div class="">
            <!-- begin:: Aside Menu -->
            <div class="add-sender-domain-modal-main"></div>
            <div class="index-mainbox-left-ctnr">
                <button class="add-sender-domain-modal-box-heading-close" id="close-add-sender-domain">x</button>
                <div class="index-mainbox-left">
                    @include('left-menu')
                </div>
            </div>
            <!-- end:: Aside Menu -->
            <!-- begin:: Header -->
            <div class="index-mainbox-right">
                <div class="add-sender-domain-modal-main1"></div>
                <div class="policy-header mb-3">
                    <button class="close-right-panel" id="close-add-sender-domain">x</button>
                    <div class="display-flex-ctnr">
                        <div class="policy-header-ctnr-lft">
                            <div class="header-left"></div>
                        </div>
                        <div class="display-flex-ctnr-rgt">
                            <div class="policy_logo_toolbar">
                                <div class="header-right text-right">
                                    <ul>
										<li>
											<a href="{{url('meetinglog')}}" class="notification-box" style="width:30px">
											  <img src="{{url('/images/bell.png')}}" alt="" width="30">
											  <span id="notification_count" class="badge"></span>
											</a>
										  </li>
                                        <li>
                                            <a href="javascript:void(0);" class="m-dropdown__toggle">{{ $logged_in_user_name }}<span class="user-img">{{ $logged_in_user_short_name }}</span> </a>
                                            <div class="signout-pop">
                                                <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                                <div class="m-card-user border-bottom">
                                                    <div class="m-card-user__details">
                                                        <span class="m-card-user__name m--font-weight-500">{{ $logged_in_user_name }}</span>
                                                        <a href="javascript:void(0);" class="m-card-user__email m--font-weight-300 m-link">{{ $logged_in_user_email }}</a>
                                                    </div>
                                                </div>
                                                <div class="m-card-user border-bottom logout-cntr">
                                                    <ul>
                                                        <li>
                                                            <a href="{{url('update-profile')}}"><i class="icons profile-icon" aria-hidden="true"></i> My profile</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="m-card-user border-bottom logout-cntr">
                                                    <ul>
                                                        <li>
                                                        <a class="dropdown-item" onclick="document.getElementById('logout-form').submit();"><i class="icons logout-icon" aria-hidden="true"></i>{{ __('Logout') }}</a>

                                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                            @csrf
                                                        </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('flash-message')

                <div class="row mt-4 xsmt-0">
                    <div class="col-md-12">
                        <nav class="breacrumbs" aria-label="breadcrumb">
                            <div style="align-items: flex-end;">
                                <h3 class="breadcream-text">@yield('breadcrumb_heading')</h3>
                               
								{{-- @if (!empty($rating))
								<div>
									<span style="margin-bottom: 3px;display:block">Your Partner Rating</span>
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
								
									
								@endif --}}
                                <!--<ol class="breadcrumb breadcream">
                                    <li class="breadcrumb-item"><a href="{{URL::to('/')}}">Home</a></li>
                                </ol>-->
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                    @section('content')
                    @show
                  </div>
               </div>
			   <div class="notification-detail">
				<button class="close-btn">
				  <img src="{{url('/images/close.png')}}" alt="">
				</button>
				<div class="tabs">
					<ul>
					@if(count($general_alerts_group) > 0)
						<li><a href="tab-one" ><span>General</span></a></li>
					@endif
					  <li><a href="tab-two" class="active"><span>Meeting</span></a></li>
					  <li><a href="tab-three"><span>Upcoming Birthday</span></a></li>
					</ul>
				  </div>
				  <div id="tab_content" >
					@if(count($general_alerts_group) > 0)
						<div class="tabs-group" id="tab-one">
							@foreach($general_alerts_group as $title => $general_alerts)
							<div class="notification-group">
								<h3 class="group-title">{{ $title }}</h3>
								@if(is_array($general_alerts))
								@foreach($general_alerts as $general_alert)
								<a href="{{ $general_alert['href'] }}" class="notification-inner-group-link">
									<div class="notification-inner-group">
										<h4 class="inner-title">{{ $general_alert['title'] }}</h4>
										<p class="content" style="font-size: initial;">{{ $general_alert['desc'] }}</p>
									</div>
								</a>
								@endforeach
								@endif
							</div>
							@endforeach
						</div>
					@endif
					<div class="tabs-group active" id="tab-two">
						@if(!empty($retrieved_data) && count($retrieved_data) > 0)
						<div class="notification-group">
						  <h3 class="group-title">Meeting Remark Alert</h3>
						  @foreach($retrieved_data as $val)
						  <a href="{{url('meetinglog/edit/'.$val->id)}}" class="notification-inner-group-link">
							<div class="notification-inner-group">
							  <h4 class="inner-title">{{ $val->arn_holders_name }} (ARN -{{ $val->ARN }})  </h4>
							  <p class="content"  style="font-size: initial;">Add a remark to close the meeting on date
								{{ explode(' ', date("d-M-y", strtotime($val->start_datetime)))[0] }} {{ substr(explode(' ', $val->start_datetime)[1], 0, 5) }} to {{ substr(explode(' ', $val->end_datetime)[1], 0, 5) }}
							  </p>
							  {{-- <p class="time">3 hours ago</p> --}}
							</div>
						  </a>
						  @endforeach
						</div>
						@endif
						@if(!empty($todayMeeting_data) && count($todayMeeting_data) > 0 || !empty($tagtodayMeeting) && count($tagtodayMeeting) > 0)
						<div class="notification-group">
						  <h3 class="group-title">Today Meeting Alert</h3>
						  @if(!empty($todayMeeting_data) && count($todayMeeting_data) > 0)
						  @foreach($todayMeeting_data as $val)
						  <a href="{{url('meetinglog/edit/'.$val->id)}}" class="notification-inner-group-link">
							<div class="notification-inner-group">
							  <h4 class="inner-title">{{ $val->arn_holders_name }} (ARN -{{ $val->ARN }})  </h4>
							  <p class="content"  style="font-size: initial;">Meeting schedule today at time
								 {{ substr(explode(' ', $val->start_datetime)[1], 0, 5) }} to {{ substr(explode(' ', $val->end_datetime)[1], 0, 5) }}
							  </p>
							  {{-- <p class="time">3 hours ago</p> --}}
							</div>
						  </a>
						  @endforeach
						  @endif
						</div>
					
						{{-- // tag meeting --}}
						@if(!empty($tagtodayMeeting) && count($tagtodayMeeting) > 0)
						<div class="notification-group">
						  {{-- <h3 class="group-title">Today Meeting Alert</h3> --}}
						  @foreach($tagtodayMeeting as $val)
						  <a href="javascript:void(0)" class="notification-inner-group-link">
							<div class="notification-inner-group">
							  <h4 class="inner-title"> {{ $val->arn_holders_name }} (ARN -{{ $val->ARN }})  </h4>
							  <p class="content"  style="font-size: initial;">Meeting Create BY: {{ $val->bdm_name }}
								
							 </p>
							  <p class="content"  style="font-size: initial;">Meeting schedule today at time
								 {{ substr(explode(' ', $val->start_datetime)[1], 0, 5) }} to {{ substr(explode(' ', $val->end_datetime)[1], 0, 5) }}
							  </p>
							  {{-- <p class="time">3 hours ago</p> --}}
							</div>
						  </a>
						  @endforeach
						</div>
						@endif
						@endif
						{{-- end tag meeting --}}
					</div>
					<div class="tabs-group" id="tab-three">
						@if(!empty($todayPartnersBirthDayNotificationData) && count($todayPartnersBirthDayNotificationData) > 0)
						<div class="notification-group">
						   {{-- 
						   <h3 class="group-title"></h3>
						   --}}
						   @foreach($todayPartnersBirthDayNotificationData as $val)
						   <a href="javascript:void(0)" class="notification-inner-group-link">
							  <div class="notification-inner-group">
								 <h4 class="inner-title">{{ $val->arn_name }} (ARN -{{ $val->ARN }})   </h4>
								 <p class="content"  style="font-size: initial;">𝖳𝗈𝖽𝖺𝗒  🎉 🎂 
									{{-- {{ explode(' ', date("d-M-y", strtotime($val->dob)))[0] }} --}}
								 </p>
								 {{-- 
								 <p class="time">3 hours ago</p>
								 --}}
							  </div>
						   </a>
						   @endforeach
						</div>
						@endif
						@if(!empty($UpcomingPartnersBirthDayNotificationData) && count($UpcomingPartnersBirthDayNotificationData) > 0)
						<div class="notification-group">
						   <h3 class="group-title"></h3>
						   @foreach($UpcomingPartnersBirthDayNotificationData as $val)
						   <a href="javascript:void(0)" class="notification-inner-group-link">
							  <div class="notification-inner-group">
								 <h4 class="inner-title">{{ $val->arn_name }} (ARN -{{ $val->ARN }}) </h4>
								 <p class="content"  style="font-size: initial;">
									{{ date("jS F", strtotime($val->dob)) }}
								 </p>
								 {{-- 
								 <p class="time">3 hours ago</p>
								 --}}
							  </div>
						   </a>
						   @endforeach
						</div>
						@endif
					 </div>
				  </div>
				  </div>
			  </div>
            </div>
            <!-- end:: Header-->

        </div>
    </div>
    <!-- end:: Main Container -->

    <!-- begin:: Footer -->
    <div class="footer mt-9">
        <div class="">
            <div class="index-mainbox-right">
                <div class="footer-text"> Copyright &copy; {{$copyright_date}} RankMF All rights reserved. </div>
            </div>
        </div>
    </div>
    <!-- end:: Footer-->
    <div id="m_scroll_top" class="m-scroll-top">
        <i class="icons arrow-up"></i>
    </div>
    <div class="loader">
        <div class="loaders-design">
            Processing...
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    @section('custom_after_footer_html')
    @show

    <div class="modal-ovelay"></div>
    <form action="javascript:void(0);" method="post" target="_blank" id="frm_export_data">@csrf</form>
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('js/custom.js?v=1.7')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <!--script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script-->
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <!--script src="{{asset('js/sweetalert.min.js')}}"></script-->
    <script src="{{asset('js/sweetalert_2.1.2/sweetalert.min.js')}}"></script>
    <!--script src="{{asset('js/jquery.slimscroll.min.js')}}"></script-->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
  
    @section('custom_scripts')
    @show
	<script>
		
		$(".notification-box").on("click", function(e) {
			notification_count = $('.notification-inner-group-link').length;
			if(notification_count == 0){
				return false;
			}
			e.preventDefault()
			$(".notification-detail").toggle();

			var position = $('.notification-box').offset();
			//notificationwidth = $('.notification-detail').width()
			$('.notification-detail').offset({
				top: position.top + $('.notification-box').height(),
				left: position.left - $('.notification-detail').width(),
			});
			$('.notification-detail').width(500);
		});
		
		$(".close-btn").on("click", function() {
			$(".notification-detail").hide();
		});

		$(".tabs li a").on("click", function(e) {
			e.preventDefault()
			$(".tabs li a").removeClass("active")
			$(this).addClass("active")
			$(".tabs-group").removeClass("active")
			$('#'+$(this).attr("href")).addClass("active")
		})

		$.ajaxSetup({
			complete: function(jqXHR, textStatus) {
				if(typeof jqXHR.responseText !== 'undefined'){
					if(jqXHR.responseText.indexOf('page.refresh') != -1){
						location.reload();
					}
				}
			}
		});

	  </script>
<button type="button" id="main_drm_model" style="display:none;" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#flipFlop"></button>
<!-- The modal -->
<div class="modal fade" id="flipFlop" tabindex="-1" role="dialog" aria-labelledby="main_drm_model_header" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
<h4 class="modal-title" id="main_drm_model_header">Alert</h4>
</div>
<div class="modal-body" id="main_drm_model_body"></div>
<div class="modal-footer">
<button type="button" class="btn btn-primary" id="main_drm_model_ok" data-dismiss="modal">Ok</button>
<button type="button" class="btn btn-primary" id="main_drm_model_submit" data-dismiss="modal">Submit</button>
<button type="button" class="btn btn-primary" id="main_drm_model_close" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>
<script>

	function drm_alert(json){

		$( "#main_drm_model" ).trigger( "click" );
		$('#main_drm_model_submit').hide();
		$('#main_drm_model_ok').hide();

		if(typeof json.head !== 'undefined'){
			$('#main_drm_model_header').html(json.head);
		}

		if(typeof json.body !== 'undefined'){
			$('#main_drm_model_body').html(json.body);
		}

		if(typeof json.submit !== 'undefined'){
			$("#main_drm_model_submit").unbind("click");
			$('#main_drm_model_submit').show();
			$("#main_drm_model_submit").click(function(){
				json.submit();
				$('#main_drm_model_submit').hide();
			});
		}

		if(typeof json.ok !== 'undefined'){
			$("#main_drm_model_ok").unbind("click");
			$('#main_drm_model_ok').show();
			$("#main_drm_model_ok").click(function(){
				json.ok();
				$('#main_drm_model_ok').hide();
			});
		}

		if(typeof json.close !== 'undefined'){
			$("#main_drm_model_close").click(function(){
				json.close();
			});
		}

	}

	$( document ).ready(function() {
		notification_count = $('.notification-inner-group-link').length;
		if(notification_count == 0){
			$('#notification_count').hide();
		}else{
			if(notification_count > 99){
				notification_count = '99+'
			}
			$('#notification_count').html(notification_count);
		}
	});

</script>
</body>
</html>
