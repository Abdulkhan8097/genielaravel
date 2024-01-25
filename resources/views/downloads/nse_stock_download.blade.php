@extends('../layout')
@section('title', 'NSE Stock Free Float Market Cap Data')
@section('breadcrumb_heading', 'NSE Stock Free Float Market Cap Data')

@section('custom_head_tags')
<style type="text/css">
.progress-bar {
width: 100%;
background-color: #e0e0e0;
padding: 3px;
border-radius: 3px;
box-shadow: inset 0 1px 3px rgba(0, 0, 0, .2);
}

.progress-bar-fill {
display: block;
height: 22px;
background-color: #659cef;
border-radius: 3px;

transition: width 500ms ease-in-out;
}
    </style>
@endsection

@section('content')

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="mt-2">
            <form class="" name="NSEForm"  method="post" action="javascript:void(0)">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div clasNSEForms="row form-inline">
                            <div class="col-lg-2">
                                <div class="col-lg-12">
                                    <div class="">
                                        <input type="hidden" class="form-control" name="download_nse" id="download_nse" value="1"/>
                                        <button type="button" class="btn btn-primary" id="submit_nse">DOWNLOAD NSE Stock Free Float Market Cap Data</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- progress bar -->
<div class="row mt-4">
    <div class="col-lg-12">
<div class="progress-bar" style="display:none;">
    <span class="progress-bar-fill" style="width: 0%;text-align:center">0%</span>
</div>
</div>
</div>
<!--end progress bar -->
@endsection

@section('custom_scripts')
<script type="text/javascript">
   var log_response = "{{$log_response??''}}";
   $("#submit_nse").on("click", function(){
    var err_flag = false;

    if(!err_flag){
        //code executed if all validation done
        var form = $('[name="NSEForm"]')[0]; // You need to use standard javascript object here
        var formData = new FormData(form);
        var jsonResponse = '', lastResponseLen = false, download_filename = '';
        $.ajax({
            url: baseurl + "/download-nse-details",
            dataType : "html",
            type:"post",
            data:formData,
            processData:false,
            contentType:false,
            xhrFields: {
                onprogress: function(e) {
                    var thisResponse, response = e.currentTarget.response;
                    if(lastResponseLen === false) {
                        thisResponse = response;
                        lastResponseLen = response.length;
                    } else {
                        thisResponse = response.substring(lastResponseLen);
                        lastResponseLen = response.length;
                    }
                    if(log_response == '1'){
                        console.log("thisResponse=", thisResponse);
                    }

                    try{
                        jsonResponse = JSON.parse(thisResponse);
                        showPercentage = jsonResponse.count/jsonResponse.total*100;
                        // console.log("showPercentage=", showPercentage);
                        showPercentage =  Math.round(showPercentage);
                        // console.log("showPercentage=", showPercentage);
                        if(log_response == '1'){
                            console.log('Processed '+jsonResponse.count+' of '+jsonResponse.total);
                            console.log("showPercentage=", showPercentage);
                            console.log("progress-bar=", $('.progress-bar'), $('.progress-bar').length);
                        }
                        $('.progress-bar').show();
                        $('.progress-bar-fill').attr('style','width: '+showPercentage+'%;text-align:center');
                        $('.progress-bar-fill').text(showPercentage+'%');
                    }
                    catch(e){
                        if(log_response == '1'){
                            console.log("error=",e);
                        }
                    }

                    if(response.download_filename != null && typeof response.download_filename != 'undefined'){
                        download_filename = response.filename;
                    }
                }
            },
            beforeSend: function() {
                $("#submit_nse").attr("disabled", true);
                $('.loader').hide();
                $('.progress-bar').show();
            },
            error: function(){
                swal("", unable_to_process_request_text, "warning");
            },
            success: function(data){
                // console.log(data);
                data = data.substr(data.indexOf('"filename":') + 12).replace('"}','');
                window.open(baseurl+'/storage/NSEdata/'+data, '_blank');
            },
            complete: function(){
                $("#submit_nse").attr("disabled", false);
                $('.progress-bar').hide();
                // alert(download_filename);
            }
        });

    }
});
</script>
@endsection
