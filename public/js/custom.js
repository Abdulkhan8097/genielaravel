var baseurl, unable_to_process_request_text = 'Unable to process your request, try again later';
$(document).ready(function() {
    baseurl = $('meta[name="baseurl"]').attr('content');
    $(".toggler--left").click(function(e) {
        if($(".m-page--fluid").hasClass("minimus-menu")){
            $(".m-page--fluid").removeClass("minimus-menu");
        }else {
            $(".m-page--fluid").addClass("minimus-menu");
        }
    });

    $(".m-dropdown__toggle").click(function(){
        $(".signout-pop").toggleClass("signout-pop-show");
    });

    $(".m-brand__toggler--left, .add-sender-domain-modal-box-heading-close, .add-sender-domain-modal-main").click(function(){
        if($(".index-mainbox-left-ctnr").hasClass("menu-left-0")){
            $(".add-sender-domain-modal-main").hide();
            $(".index-mainbox-left-ctnr").removeClass("menu-left-0");
        }
        else{
            $(".add-sender-domain-modal-main").show();
            $(".index-mainbox-left-ctnr").addClass("menu-left-0");
        }
    });

    $(".header-toggler, .close-right-panel, .add-sender-domain-modal-main1").click(function(){
        if($(".policy-header").hasClass("menu-right-0")){
            $(".add-sender-domain-modal-main1").hide();
            $(".policy-header").removeClass("menu-right-0");
        }
        else{
            $(".add-sender-domain-modal-main1").show();
            $(".policy-header").addClass("menu-right-0");
        }
    });

    $(window).scroll(function(){
        if($(window).scrollTop() > 300){
            $(".m-page--fluid").addClass("m-scroll-top--shown")
        }
        else{
            $(".m-page--fluid").removeClass("m-scroll-top--shown")
        }
    });

    $(".m-scroll-top").on("click", function(){
        $("html, body").animate({scrollTop:0}, 2000);
    });

    /*$(".index-mainbox-left").slimScroll({
        allowPageScroll: true,
        height: $(window).height() - 110
    });*/

    // adding click event for showing and hiding the submenus
    $("a.nav-treeview").on("click", function(){
        $(this).next("ul").toggle();
    });

    // adding submenu-items class to UL tag which have few items under it
    $('ul.submenu').parent().addClass('submenu-items');
    // adding parent UL tag as class ACTIVE, so that it too gets highlighted whenever it's one of submenu have ACTIVE class
    $('ul.submenu li.active').closest('.submenu-items').addClass('active');

    // removes the empty parent menus
    $("a.nav-treeview").next("ul").each(function(){
        if($(this).find("li").length == 0){
            $(this).closest("li.submenu-items").remove();
        }
    });

    $(document).on("click", ".submenu-items" ,function(){
        $(this).toggleClass("active");
        $(this).siblings("li").removeClass("active");
    });

    // listens to methods of informative alert strips(Refer: )
    $(".alert button.close").click(function(){
        var flash_text_span = $(this).prev("span"), data_flash = flash_text_span.attr("data-flash");
        $(this).closest(".alert").fadeOut("slow").removeClass(data_flash);
        flash_text_span.empty();
        flash_text_span.attr("data-flash", "");
    });

    // adding CSRF tokens to AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // showing a loader whenever an AJAX request starts
    $(document).ajaxStart(function(){
        if($('.loader').length > 0){
            $('.loader').show();
        }
    });

    // hiding a loader whenever an AJAX request stops
    $(document).ajaxStop(function(){
        if($('.loader').length > 0){
            $('.loader').hide();
        }
    });
});

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function prepare_error_text(response, show_it_in_sweetalert=1){
    var err_msg = '', elementToFocus = '', show_alert = 1;
    if(response.err_msg != null && response.err_msg.length > 0){
        $.each(response.err_msg, function(key, value){
            if(value.element != null && value.index != null && value.msg != null){
                // if details are available for showing an individual element level message then proceeding here
                var element_obj = document.getElementsByName(value.element)[value.index];
                $(element_obj).next('.error').html(value.msg);
                // setting up focus to a first input element having an error
                if(elementToFocus == '' || elementToFocus == null){
                    elementToFocus = $(element_obj);
                }

                if(show_alert == 1){
                    // showing individual element level messages, that's why disabling alert message
                    show_alert = 0;
                }
            }
            else{
                // coming here when only message text available for showing
                err_msg += value +'\n';
            }
        });
    }
    else{
        // if error messages not available then showing default message
        err_msg = unable_to_process_request_text;
    }

    if(show_alert == 1){
        // if sweet alert needs to be shown then below parameter should have value as 1
        if(show_it_in_sweetalert == 1){
            swal('', err_msg, 'error');
        }
        else{
            alert(err_msg);
        }
    }

    if(elementToFocus != '' && elementToFocus.length > 0){
        elementToFocus.focus();
    }
}
