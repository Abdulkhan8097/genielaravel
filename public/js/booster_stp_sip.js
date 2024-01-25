$(document).ready(function(){
  // tab click event
  $(".new-tab li a").on("click", function(a) {
    a.preventDefault();
    $(this).parent().addClass("active");
    $(this).parent().siblings().removeClass("active");
    var t = $(this).attr("href").split("#")[1];

    $(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id="' + t + '"]').fadeIn();
    $(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id!="' + t + '"]').hide();
    $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
  });

  // accordian click event
  $(".faq-question").on("click", function() {
    var faq_answer_obj = $(this).siblings(".faq-answer");
    faq_answer_obj.slideToggle();
    if($(this).parent().hasClass("active")){
        $(this).html('Show Filter<i class="arrow"></i>');
    }
    else{
        $(this).html('Hide Filter<i class="arrow"></i>');
    }
    $(this).parent().toggleClass("active");
    $(this).parent().siblings().removeClass("active");
    $(this).parent().siblings().children(".faq-answer").slideUp();
  });

  // pre selecting souce scheme if it's single value
  if($('[name="select_stp_source_scheme"], [name="select_sip_source_scheme"]').length > 0){
    $('[name="select_stp_source_scheme"], [name="select_sip_source_scheme"]').each(function(){
      var selectObj = $(this);
      if(selectObj.find('option[value!=""]').length == 1){
        selectObj.val(selectObj.find('option[value!=""]:first').val());
      }
    });
  }

  // if from an ACTIVE TAB filters are not getting shown by default then showing them at page load
  if($('.nav .nav-item.active').find('a.nav-link').length == 1){
    var selected_anchor_id = $('.nav .nav-item.active').find('a.nav-link').attr('href');
    if(!$(selected_anchor_id).find('.faq-answer').is(':visible')){
      $(selected_anchor_id).find('.faq-question').trigger('click');
    }
  }

  $('.nav-item').on('click', function(){
    var selected_anchor_id = $('.nav .nav-item.active').find('a.nav-link').attr('href');
    if(!$(selected_anchor_id).find('.faq-answer').is(':visible')){
      $(selected_anchor_id).find('.faq-question').trigger('click');
    }
  });

  // click event to download BOOSTER STP data
  $('[name="btn_stp_submit"]').on('click', function(){
    var formObj = $('[name="frm_stp_search_filters"]');
    var err_flag = false, elementToFocus = null, err_msg = '';

    var select_stp_source_scheme_obj = $('[name="select_stp_source_scheme"]');
    if(select_stp_source_scheme_obj.val() == null || typeof select_stp_source_scheme_obj.val() == 'undefined' || select_stp_source_scheme_obj.val() == ''){
      err_flag = true;
      err_msg += 'Select source scheme\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = select_stp_source_scheme_obj;
      }
    }
    else{
      var select_stp_source_scheme_label = select_stp_source_scheme_obj.find('option:selected').text();
      $('[name="select_stp_source_scheme_label"]').val(select_stp_source_scheme_label);
    }

    /*var stp_start_date_obj = $('[name="stp_start_date"]');
    if(stp_start_date_obj.val() == null || typeof stp_start_date_obj.val() == 'undefined' || stp_start_date_obj.val() == ''){
      err_flag = true;
      err_msg += 'Select start date\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = stp_start_date_obj;
      }
    }*/

    var stp_end_date_obj = $('[name="stp_end_date"]');
    if(stp_end_date_obj.val() == null || typeof stp_end_date_obj.val() == 'undefined' || stp_end_date_obj.val() == ''){
      err_flag = true;
      err_msg += 'Select end date\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = stp_end_date_obj;
      }
    }

    var stp_multiplier_type_obj = $('[name="stp_multiplier_type"]');
    if(stp_multiplier_type_obj.val() == null || typeof stp_multiplier_type_obj.val() == 'undefined' || stp_multiplier_type_obj.val() == ''){
      err_flag = true;
      err_msg += 'Select multiplier\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = stp_multiplier_type_obj;
      }
    }

    var select_stp_target_scheme_obj = $('[name="select_stp_target_scheme"]');
    if(select_stp_target_scheme_obj.val() == null || typeof select_stp_target_scheme_obj.val() == 'undefined' || select_stp_target_scheme_obj.val() == ''){
      err_flag = true;
      err_msg += 'Select target scheme\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = select_stp_target_scheme_obj;
      }
    }
    else{
      var select_stp_target_scheme_label = select_stp_target_scheme_obj.find('option:selected').text();
      $('[name="select_stp_target_scheme_label"]').val(select_stp_target_scheme_label);
    }

    var stp_opening_balance_obj = $('[name="stp_opening_balance"]');
    if(stp_opening_balance_obj.val() == null || typeof stp_opening_balance_obj.val() == 'undefined' || stp_opening_balance_obj.val() == ''){
      err_flag = true;
      err_msg += 'Please enter opening balance\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = stp_opening_balance_obj;
      }
    }
    else if(!$.isNumeric(stp_opening_balance_obj.val())){
      err_flag = true;
      err_msg += 'Opening balance should be numeric\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = stp_opening_balance_obj;
      }
    }

    var stp_base_amount_obj = $('[name="stp_base_amount"]');
    if(stp_base_amount_obj.val() == null || typeof stp_base_amount_obj.val() == 'undefined' || stp_base_amount_obj.val() == ''){
      err_flag = true;
      err_msg += 'Please enter base amount required Booster STP\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = stp_base_amount_obj;
      }
    }
    else if(!$.isNumeric(stp_base_amount_obj.val())){
      err_flag = true;
      err_msg += 'Base amount should be numeric\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = stp_base_amount_obj;
      }
    }

    if(err_flag){
      alert(err_msg);
      if(elementToFocus != null && elementToFocus.length > 0){
        elementToFocus.focus();
      }
      return false;
    }
  });

  // click event to download BOOSTER SIP data
  $('[name="btn_sip_submit"]').on('click', function(){
    var formObj = $('[name="frm_sip_search_filters"]');
    var err_flag = false, elementToFocus = null, err_msg = '';

    var select_sip_source_scheme_obj = $('[name="select_sip_source_scheme"]');
    if(select_sip_source_scheme_obj.val() == null || typeof select_sip_source_scheme_obj.val() == 'undefined' || select_sip_source_scheme_obj.val() == ''){
      err_flag = true;
      err_msg += 'Select source scheme\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = select_sip_source_scheme_obj;
      }
    }
    else{
      var select_sip_source_scheme_label = select_sip_source_scheme_obj.find('option:selected').text();
      $('[name="select_sip_source_scheme_label"]').val(select_sip_source_scheme_label);
    }

    /*var sip_start_date_obj = $('[name="sip_start_date"]');
    if(sip_start_date_obj.val() == null || typeof sip_start_date_obj.val() == 'undefined' || sip_start_date_obj.val() == ''){
      err_flag = true;
      err_msg += 'Select start date\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = sip_start_date_obj;
      }
    }*/

    var sip_end_date_obj = $('[name="sip_end_date"]');
    if(sip_end_date_obj.val() == null || typeof sip_end_date_obj.val() == 'undefined' || sip_end_date_obj.val() == ''){
      err_flag = true;
      err_msg += 'Select end date\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = sip_end_date_obj;
      }
    }

    var sip_multiplier_type_obj = $('[name="sip_multiplier_type"]');
    if(sip_multiplier_type_obj.val() == null || typeof sip_multiplier_type_obj.val() == 'undefined' || sip_multiplier_type_obj.val() == ''){
      err_flag = true;
      err_msg += 'Select multiplier\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = sip_multiplier_type_obj;
      }
    }

    var select_sip_target_scheme_obj = $('[name="select_sip_target_scheme"]');
    if(select_sip_target_scheme_obj.val() == null || typeof select_sip_target_scheme_obj.val() == 'undefined' || select_sip_target_scheme_obj.val() == ''){
      err_flag = true;
      err_msg += 'Select target scheme\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = select_sip_target_scheme_obj;
      }
    }
    else{
      $('[name="select_sip_target_scheme_label"]').val(select_sip_target_scheme_obj.find('option:selected').text());
    }

    var sip_opening_balance_obj = $('[name="sip_opening_balance"]');
    if(sip_opening_balance_obj.val() == null || typeof sip_opening_balance_obj.val() == 'undefined' || sip_opening_balance_obj.val() == ''){
      err_flag = true;
      err_msg += 'Please enter opening balance\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = sip_opening_balance_obj;
      }
    }
    else if(!$.isNumeric(sip_opening_balance_obj.val())){
      err_flag = true;
      err_msg += 'Opening balance should be numeric\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = sip_opening_balance_obj;
      }
    }

    var sip_base_amount_obj = $('[name="sip_base_amount"]');
    if(sip_base_amount_obj.val() == null || typeof sip_base_amount_obj.val() == 'undefined' || sip_base_amount_obj.val() == ''){
      err_flag = true;
      err_msg += 'Please enter base amount required Booster STP\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = sip_base_amount_obj;
      }
    }
    else if(!$.isNumeric(sip_base_amount_obj.val())){
      err_flag = true;
      err_msg += 'Base amount should be numeric\n';
      if(elementToFocus == null || typeof elementToFocus == 'undefined'){
        elementToFocus = sip_base_amount_obj;
      }
    }

    if(err_flag){
      alert(err_msg);
      if(elementToFocus != null && elementToFocus.length > 0){
        elementToFocus.focus();
      }
      return false;
    }
  });
});