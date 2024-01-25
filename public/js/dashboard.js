var calendar_arn_relationship_quality_score, datatable_panel_userwise_arn_quality_score;
function load_arn_empanelment_count_details(datatable_id){
  var action_name = 'load_arn_empanelment_count_details_statewise';
  var first_column_name = 'arn_state';
  if(datatable_id == 'panel_arnuserwise_count'){
    action_name = 'load_arn_empanelment_count_details_userwise';
    first_column_name = 'user_name';
  }
  $('#'+ datatable_id).DataTable({
    "ajax": {
      "url": baseurl,
      "type": "POST",
      "data": function(d){
        d.action = action_name;
      },
      "complete": function(){
        window.setTimeout(function(){
          $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
        }, 1000);
      }
    },
    "columns": [
      {
        "data":first_column_name,
      },
      {
        "data":"not_empanelled",
        "sortable": false
      },
      {
        "data":"empanelled",
        "sortable": false
      },
      {
        "data":"total",
      },
    ],
    "language": {
      "oPaginate": {
        "sNext": '<i class="icons angle-right"></i>',
        "sPrevious": '<i class="icons angle-left"></i>',
        "sFirst": '<i class="icons step-backward"></i>',
        "sLast": '<i class="icons step-forward"></i>'
      }
    },
    "order": [3, 'desc'],
    "scrollX": true,
    "footerCallback": function ( row, data, start, end, display ) {
      var api = this.api();
      // Remove the formatting to get integer data for summation
      var intVal = function ( i ) {
        return typeof i === 'string' ?
          i.replace(/[\$,]/g, '')*1 :
          typeof i === 'number' ?
            i : 0;
      };

      // Total over all pages for empanelled users
      var totalEmpanelled = api
        .column( 1 )
        .data()
        .reduce( function (a, b) {
          if(b.indexOf('(') != -1){
            b = b.substr(0, b.indexOf('('));
            b = $.trim(b);
          }
          return intVal(a) + intVal(b);
        }, 0);

      // Total over this page for empanelled users
      var pageTotalEmpanelled = api
        .column( 1, { page: 'current'} )
        .data()
        .reduce( function (a, b) {
          if(b.indexOf('(') != -1){
            b = b.substr(0, b.indexOf('('));
            b = $.trim(b);
          }
          return intVal(a) + intVal(b);
        }, 0);

      // Total over all pages for non empanelled users
      var totalNonEmpanelled = api
        .column( 2 )
        .data()
        .reduce( function (a, b) {
          if(b.indexOf('(') != -1){
            b = b.substr(0, b.indexOf('('));
            b = $.trim(b);
          }
          return intVal(a) + intVal(b);
        }, 0);

      // Total over this page for non empanelled users
      var pageTotalNonEmpanelled = api
        .column( 2, { page: 'current'} )
        .data()
        .reduce( function (a, b) {
          if(b.indexOf('(') != -1){
            b = b.substr(0, b.indexOf('('));
            b = $.trim(b);
          }
          return intVal(a) + intVal(b);
        }, 0);

      // Total over all pages
      var total = api
        .column( 3 )
        .data()
        .reduce( function (a, b) {
          return intVal(a) + intVal(b);
        }, 0);

      // Total over this page
      var pageTotal = api
        .column( 3, { page: 'current'} )
        .data()
        .reduce( function (a, b) {
          return intVal(a) + intVal(b);
        }, 0);

      // Update footer
      $( api.column( 1 ).footer() ).html(
        pageTotalEmpanelled +' ('+ totalEmpanelled +' total)'
      );
      $( api.column( 2 ).footer() ).html(
        pageTotalNonEmpanelled +' ('+ totalNonEmpanelled +' total)'
      );
      $( api.column( 3 ).footer() ).html(
        pageTotal +' ('+ total +' total)'
      );
    }
  });
}

function load_goal_userwise(data_shown_format='tabular', inputObj){

	var selectElement = $("#user_select");
	var optionCount = selectElement.find("option").length;
	if (optionCount <= 1) {
		selectElement.hide();
	}

	var period = $('#select_period').val();
	var select_user = $('#select_user').val();

	var column_name = '';

	if(select_user == 0){
		$('#useranddate').html('User');
		column_name = 'user_name';
		$('#gday').html('Daily');
		$('#gweek').html('Weekly');
		$('#gmonth').html('Monthly');
	}else{
		$('#useranddate').html('Date');
		column_name = 'date';
		$('#gday').html('Today');
		$('#gweek').html('This Week');
		$('#gmonth').html('This Month');
	}

	panel_goal_userwise = $('#panel_goal_userwise').DataTable({
		"ajax": {
		  "url": baseurl,
		  "type": "POST",
		  "data": function(d){
			d.action = 'load_goal_userwise';
			d.period = period;
			d.select_user = select_user;
		  },
		  "complete": function(jqXHR){
			if(jqXHR.responseJSON != null && jqXHR.responseJSON.max_score_of_date != null && typeof jqXHR.responseJSON.max_score_of_date != 'undefined' && jqXHR.responseJSON.max_score_of_date != ''){
			  $('#user_arn_relationship_quality_score_of_date_text').html('Showing data for date <b>'+ jqXHR.responseJSON.max_score_of_date +'</b>');
			}
			else{
			  $('#user_arn_relationship_quality_score_of_date_text').html('');
			}
			window.setTimeout(function(){
			  $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
			}, 1000);
		  }
		},
		"columns": [
		  {
			"data":column_name,
		  },
		  {
			"data":"target_calls",
		  },
		  {
			"data":"achieved_calls",
		  },
		  {
			"data":"target_meetings",
		  },
		  {
			"data":"achieved_meetings",
		  },
		  {
			"data":"achieved_percentage",
		  }
		],
		"language": {
		  "oPaginate": {
			"sNext": '<i class="icons angle-right"></i>',
			"sPrevious": '<i class="icons angle-left"></i>',
			"sFirst": '<i class="icons step-backward"></i>',
			"sLast": '<i class="icons step-forward"></i>'
		  }
		},
		"order": [0, 'asc'],
		"scrollX": true
	  });
}

function load_arn_relationship_quality_score(data_shown_format='tabular', inputObj){
	var calendarEl = document.getElementById('panel_userwise_arn_quality_score_calendar_view');
	var selected_user_id = '';
	if($('#select_user_id').length > 0 && $('#select_user_id').val() != null && typeof $('#select_user_id').val() != 'undefined'){
	  selected_user_id = $('#select_user_id').val();
	}
  
	$('#'+ inputObj).siblings('a').removeClass('active-anchor').css('opacity', '0.5');
	$('#'+ inputObj).addClass('active-anchor').css('opacity', '1');
  
	if(data_shown_format == 'tabular'){
	  $(calendarEl).siblings('.table-responsive').removeClass('hidden-element');
	  $(calendarEl).addClass('hidden-element');
  
	  if($('#panel_userwise_arn_quality_score').hasClass('dataTable') && datatable_panel_userwise_arn_quality_score != null){
		datatable_panel_userwise_arn_quality_score.destroy();
	  }
  
	  datatable_panel_userwise_arn_quality_score = $('#panel_userwise_arn_quality_score').DataTable({
		"ajax": {
		  "url": baseurl,
		  "type": "POST",
		  "data": function(d){
			d.action = 'load_arn_relationship_quality_score';
			d.data_shown_format = data_shown_format;
			d.user_id = selected_user_id;
		  },
		  "complete": function(jqXHR){
			if(jqXHR.responseJSON != null && jqXHR.responseJSON.max_score_of_date != null && typeof jqXHR.responseJSON.max_score_of_date != 'undefined' && jqXHR.responseJSON.max_score_of_date != ''){
			  $('#user_arn_relationship_quality_score_of_date_text').html('Showing data for date <b>'+ jqXHR.responseJSON.max_score_of_date +'</b>');
			}
			else{
			  $('#user_arn_relationship_quality_score_of_date_text').html('');
			}
			window.setTimeout(function(){
			  $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
			}, 1000);
		  }
		},
		"columns": [
		  {
			"data":"user_name",
		  },
		  {
			"data":"no_of_assigned_arn",
		  },
		  {
			"data":"maximum_score",
		  },
		  {
			"data":"calculated_score",
		  },
		  {
			"data":"achieved_percentage",
		  }
		],
		"language": {
		  "oPaginate": {
			"sNext": '<i class="icons angle-right"></i>',
			"sPrevious": '<i class="icons angle-left"></i>',
			"sFirst": '<i class="icons step-backward"></i>',
			"sLast": '<i class="icons step-forward"></i>'
		  }
		},
		"order": [4, 'desc'],
		"scrollX": true
	  });
	}
	else if(data_shown_format == 'calendar'){
	  if(selected_user_id == ''){
		swal('', 'Please select an user', 'warning');
		return false;
	  }
  
	  $('#user_arn_relationship_quality_score_of_date_text').html('');
	  $(calendarEl).siblings('.table-responsive').addClass('hidden-element');
	  $(calendarEl).removeClass('hidden-element');
	  if($(calendarEl).attr('calendar-loaded') != null || typeof $(calendarEl).attr('calendar-loaded') == '1'){
		$(calendarEl).attr('calendar-loaded', '');
		calendar_arn_relationship_quality_score.destroy();
	  }
  
	  calendar_arn_relationship_quality_score = new FullCalendar.Calendar(calendarEl, {
		headerToolbar: {
		  left: 'prev,next today',
		  center: 'title',
		  right: 'dayGridMonth,timeGridWeek'
		},
		displayEventTime: false,
		dayMaxEvents: true, // allow "more" link when too many events
		events: {
		  "url": baseurl,
		  "dataType": "json",
		  "extraParams": {"action": "load_arn_relationship_quality_score", "data_shown_format": data_shown_format, "user_id": selected_user_id, "_token": $('meta[name="csrf-token"]').attr('content')},
		  "method": "POST",
		  failure: function(){
			swal('', 'Unable to get data for selected period', 'warning');
		  }
		},
		eventDidMount: function(calEvent) {
		  var tooltip = new Tooltip(calEvent.el, {
			title: 'Maximum Score: '+ calEvent.event.extendedProps.maximum_score + '<br>Achieved Score: '+ calEvent.event.extendedProps.calculated_score + '<br>Achieved Percentage: '+ calEvent.event.extendedProps.achieved_percentage,
			placement: 'top',
			trigger: 'hover',
			container: 'body',
			html: true
		  });
		},
		eventDisplay: 'block'
	  });
  
	  calendar_arn_relationship_quality_score.render();
	  $(calendarEl).attr('calendar-loaded', 1);
	}
  }

$(document).ready(function() {
  // user id dropdown change event
  $('#select_user_id').on('change', function(e){
    var faq_answer_obj = $(".faq-box.active .faq-question").siblings(".faq-answer");
    // if ARN relationship quality score accordian is open/active then only triggering on change event of an anchor tag who have class as "anchor-active"
    if($(".faq-box.active .faq-question").attr('id') == 'accordian_user_arn_relationship_quality_score'){
      if(faq_answer_obj.find('a.active-anchor').length > 0){
        faq_answer_obj.find('a.active-anchor').trigger('click');
      }
    }
    else{
      e.preventDefault();
    }
  });

  // tab click event
  $(".new-tab li a").on("click", function(a) {
    a.preventDefault();
    $(this).parent().addClass("active");
    $(this).parent().siblings().removeClass("active");
    var t = $(this).attr("href").split("#")[1];
    if(!$('#'+ t).find('table[id^="panel_"]').hasClass('dataTable')){
      // populating datatable records, if it's not already available
      load_arn_empanelment_count_details($('#'+ t).find('table[id^="panel_"]').attr('id'));
    }
    $(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id="' + t + '"]').fadeIn();
    $(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id!="' + t + '"]').hide();
    $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
  });

  	$("#select_period").on("change", function() {
		$("#panel_goal_userwise").dataTable().fnDestroy();
		load_goal_userwise('tabular', 'panel_goal_userwise');
	});
	$("#select_user").on("change", function() {
		$("#panel_goal_userwise").dataTable().fnDestroy();
		load_goal_userwise('tabular', 'panel_goal_userwise');
	});

  // accordian click event
  $(".faq-question").on("click", function() {
    var faq_answer_obj = $(this).siblings(".faq-answer");
    if(!faq_answer_obj.find('table[id^="panel_"]').hasClass('dataTable')){
      if(faq_answer_obj.find('table[id^="panel_"]').attr('id') == 'panel_userwise_arn_quality_score'){
        load_arn_relationship_quality_score('tabular', 'anchor_arn_relation_quality_tabular_view');
      }
      if(faq_answer_obj.find('table[id^="panel_"]').attr('id') == 'panel_goal_userwise'){
        load_goal_userwise('tabular', 'panel_goal_userwise');
      }
      else if($(this).attr('id') == 'accordian_arn_empanelled'){
        faq_answer_obj.find(".new-tab li.active a").trigger('click');
      }
    }
    faq_answer_obj.slideToggle();
    $(this).parent().toggleClass("active");
    $(this).parent().siblings().removeClass("active");
    $(this).parent().siblings().children(".faq-answer").slideUp();
    $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
  });

  $(".faq-box:first .faq-question").trigger('click');
});
