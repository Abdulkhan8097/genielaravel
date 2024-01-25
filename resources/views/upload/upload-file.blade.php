@php
$file_type_array = array(
    'aum_data' => 'AUM Data',
    'arn_amc_project_focus' => 'ARN & AMC Wise Project Focus',
    'arn_distributor_category' => 'ARN Wise Distributor Category',
    'arn_project_focus_yesno' => 'ARN Project Focus Yes/No',
    'arn_project_emerging_stars' => 'ARN Project Emerging Stars Yes/No',
    'alternate_mobile_email_data' => 'Alternate Mobile/Email Data',
    'pincode_data' => 'Pincode Data',
    'arn_ind_aum_data' => 'ARN Wise Industry AUM Data',
    'arn_bdm_mapping' => 'ARN Wise BDM Mapping',
    'amficity_mapped_zone' => 'AMFI City Zones',
    'arn_project_green_shoots' => 'ARN Project Green Shoots Yes/No',
    );
    //x($file_type_array);
$amc_master_list = $data['amc_list'];
@endphp
@extends('../layout')
@section('title', 'Upload File')
@section('breadcrumb_heading', 'Upload File')
@section('custom_head_tags')

    <link href="{{asset('css/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css" />

@endsection

@section('content')
<div class="container mt-4">
    <div class="col-lg-12">
    <div class="row">
    <div class="col-lg-12">
        <h2 class="border-bottom">Form</h2>
        <div class="mt-2">
            <div class="row">
                <div class="col-lg-12">
                    <div>
                        <div class="row form-inline">
                            <div class="col-lg-9">
                                <div class="d-none" id="template_download_btn">
                                    <button type="button" class="btn btn-primary" onclick="download_template()">Download CSV Template</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-2">
            <form class="" name="uploadFileForm"  method="post" enctype="multipart/form-data" action="javascript:void(0)">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row form-inline">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>File Type</label>
                                    <select class="form-control" id="file_type" name="file_type">
                                        <option value="">Select File Type</option>
                                        @foreach($file_type_array as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                    <div class="error">&nbsp;</div>
                                </div>
                            </div>
                            <div class="col-lg-4 d-none" id="yearDiv">
                                <div class="form-group">
                                    <label>Reporting Year</label>
                                    <div class="input-group-append">
                                        <input id="reporting_year" name="reporting_year" type="text" class="form-control" placeholder="Reporting Year">
                                        <div class="error">&nbsp;</div>                             
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-4 d-none" id="amcDiv">
                                <div class="form-group">
                                    <label>AMC Name</label>
                                    <select class="form-control" id="amc_name" name="amc_name">
                                        <option value="">Select AMC Name</option>
                                        @foreach($amc_master_list as $key => $value)
                                            <option value="{{$value->amc_name}}">{{$value->amc_name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="error">&nbsp;</div>
                                </div>
                            </div>
                            <div class="col-lg-4 d-none" id="multiplierTypeDiv">
                                <div class="form-group">
                                    <label>Multiplier Type</label>
                                    <div class="input-group-append">
                                        <input id="multiplier_type" name="multiplier_type" type="text" class="form-control" placeholder="Multiplier Type">
                                        <div class="error">&nbsp;</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Choose CSV File</label> 
                                    <input class="form-control" type="file" name="uploadfile" id="uploadfile" accept=".csv,text/csv">
                                    <div class="error">&nbsp;</div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="col-lg-12">
                                    <div class="">
                                        <button type="button" class="btn btn-primary" id="submit_file_form">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-2">
            <div class="row">
                <div class="col-lg-12">
                    <div>
                        <div class="row form-inline">
                            <div class="col-lg-9">
                                <div class="left-align">
                                    <p><b><u>NOTE:</u></b></p>
                                    <p>01) Please download the CSV template and put your data in the file to and upload it.</p>
                                    <p>02) Date should formatted as YYYY-MM-DD. E.G. Today's date is {{date('d/m/Y')}}, it should be written as '{{date('Y-m-d')}}</p>
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
</div>


@endsection

@section('custom_scripts')
<script type="text/javascript">
$('body').on('change', '#file_type', function(){
    // console.log($(this).val());
    if($(this).val().length > 0){
        $("#template_download_btn").show();
        document.getElementById('uploadfile').value= null;
    }else{
        $("#template_download_btn").hide();
    }

    if($(this).val() == 'arn_amc_project_focus'){
        $("#yearDiv").show();
        $("#amcDiv").show();
    }else{
        $("#yearDiv").hide();
        $("#amcDiv").hide();
    }

    if($(this).val() == 'mos_multiplier_data'){
        $("#multiplierTypeDiv").show();
    }
    else{
        $("#multiplierTypeDiv").hide();
    }
});

function download_template(){
    var file_type = $('#file_type').val();
    if(file_type != ''){
        window.open(baseurl+'/storage/'+ file_type +'/template/'+file_type+'.csv', '_blank');
    }
}

function save_uploaded_file(process_request=false){
    var err_flag = false, elementToFocus = '', formObj = $('[name="uploadFileForm"]');
    var file = document.getElementsByName('uploadfile')[0];
    var max_size_in_kb = (1024 * 1024 * 10);
    var fileType, fileType_obj, upload_file, upload_file_obj, reporting_year, reporting_year_obj, amc_name, amc_name_obj, multiplier_type, multiplier_type_obj;

    formObj.find('div.error').html('&nbsp;');
    fileType_obj = formObj.find('#file_type');
    fileType = $.trim(fileType_obj.val());
    var spacevar = "";
    if(fileType == '' || fileType == null){
        err_flag = true;
        fileType_obj.closest('div').find('.error').text('Please select file type');
        // alert('Please select file type');
        if(elementToFocus == '' || elementToFocus == null){
            elementToFocus = fileType_obj;
        }
    }
    else{
        fileType_obj.siblings('.error').html('&nbsp;');
    }

    if(fileType == 'arn_amc_project_focus'){
        reporting_year_obj = formObj.find('#reporting_year');
        reporting_year = $.trim(reporting_year_obj.val());
        if(reporting_year == '' || reporting_year == null){
            err_flag = true;
            reporting_year_obj.closest('div').find('.error').text('Reporting year is required');
            // alert('Reporting year is required');
            if(elementToFocus == '' || elementToFocus == null){
                elementToFocus = reporting_year_obj;
            }
        }
        else{
            reporting_year_obj.closest('div').find('.error').html('&nbsp;');
        }

        amc_name_obj = formObj.find('#amc_name');
        amc_name = $.trim(amc_name_obj.val());
        if(amc_name == '' || amc_name == null){
            err_flag = true;
            amc_name_obj.closest('div').find('.error').text('Please select amc name');
            // alert('Reporting year is required');
            if(elementToFocus == '' || elementToFocus == null){
                elementToFocus = amc_name_obj;
            }
        }
        else{
            amc_name_obj.closest('div').find('.error').html('&nbsp;');
        }
    }

    if(fileType == 'mos_multiplier_data'){
        multiplier_type_obj = formObj.find('#multiplier_type');
        multiplier_type = $.trim(multiplier_type_obj.val());
        if(multiplier_type == '' || multiplier_type == null){
            err_flag = true;
            multiplier_type_obj.closest('div').find('.error').text('Multiplier type is required');
            if(elementToFocus == '' || elementToFocus == null){
                elementToFocus = multiplier_type_obj;
            }
        }
        else{
            multiplier_type_obj.closest('div').find('.error').html('&nbsp;');
        }
    }

    upload_file_obj = formObj.find('#uploadfile');
    upload_file = document.getElementsByName('uploadfile')[0];
    if(upload_file.files.length == 0){
        err_flag = true;
        // alert('Upload file is required');
        upload_file_obj.closest('div').find('.error').show().text('Upload file is required');
        if(elementToFocus == '' || elementToFocus == null){
            elementToFocus = upload_file_obj;
        }
    }
    else if(upload_file.files.length == 1 && ($.inArray(upload_file.files[0]['type'], ['text/csv','application/vnd.ms-excel']) == -1) ){
        //checking whether Image uploaded or not
        err_flag = true;
        // alert('Please select an csv file');
        upload_file_obj.closest('div').find('.error').show().text('Please select an csv file');
        if(elementToFocus == '' || elementToFocus == null){
            elementToFocus = upload_file_obj;
        }
    }
    else if(upload_file.files[0].size > max_size_in_kb){
    // checking whether Input File size is of greater than 5MB or not
        err_flag = true;
        // alert('Maximum file upload size should not exceed 10MB');
        upload_file_obj.closest('div').find('.error').show().text('Maximum file upload size should not exceed 10MB');
        if(elementToFocus == '' || elementToFocus == null){
            elementToFocus = upload_file_obj;
        }
    }
    else{
        upload_file_obj.closest('div').find('.error').hide().html('&nbsp;');
    }

    if(!err_flag){
        //code executed if all validation done
        var form = $('[name="uploadFileForm"]')[0]; // You need to use standard javascript object here
        var formData = new FormData(form);
        if($("#file_type").val() == 'arn_bdm_mapping'){
            if(process_request){
                formData.append('overwrite', 'yes');
            }
            else{
                process_request = true;
                formData.append('overwrite', 'no');
            }
        }

        if(process_request == true){
            $.ajax({
                url:"{{ route('save_uploaded_data') }}",
                dataType : "JSON",
                type:"post",
                data:formData,
                processData:false,
                contentType:false,
                // cache:false,
                // async:false,
                beforeSend: function() {

                },
                error: function(){
                    swal("", unable_to_process_request_text, "warning");
                },
                success: function(data){
                    if(data.status=="error")
                    {
                        var err_msg_text = '';
                        if(data.data != null && typeof data.data != 'undefined' && data.data != ''){
                            err_msg_text = data.data;
                        }
                        if(err_msg_text == ''){
                            err_msg_text = unable_to_process_request_text;
                        }
                        swal("", err_msg_text, "error");
                    }
                    else
                    {
                        swal({
                            title: "Success",
                            text: data.message,
                            type: "success"
                        })
                        .then((value) => {
                            window.open("{{ route('upload') }}", "_parent");
                            return false;
                        });
                    }
                },
                complete: function(){

                }
            });
        }
    }else{
        // adding focus to an element having an error
        if(elementToFocus != null && elementToFocus != '' && elementToFocus.length > 0){
            elementToFocus.focus();
        }
    }
}

$("#submit_file_form").on("click", function(){
    var err_flag = false, elementToFocus = '', formObj = $('[name="uploadFileForm"]');
    var file = document.getElementsByName('uploadfile')[0];
    var max_size_in_kb = (1024 * 1024 * 10);
    var fileType, fileType_obj, upload_file, upload_file_obj, reporting_year, reporting_year_obj, amc_name, amc_name_obj;

    fileType_obj = formObj.find('#file_type');
    fileType = $.trim(fileType_obj.val());
    var spacevar = "";
    if(fileType == '' || fileType == null){
        err_flag = true;
        fileType_obj.closest('div').find('.error').text('Please select file type');
        // alert('Please select file type');
        if(elementToFocus == '' || elementToFocus == null){
            elementToFocus = fileType_obj;
        }
    }
    else{
        fileType_obj.siblings('.error').html('&nbsp;');
    }

    if(fileType == 'arn_amc_project_focus'){
        reporting_year_obj = formObj.find('#reporting_year');
        reporting_year = $.trim(reporting_year_obj.val());
        if(reporting_year == '' || reporting_year == null){
            err_flag = true;
            reporting_year_obj.closest('div').find('.error').text('Reporting year is required');
            // alert('Reporting year is required');
            if(elementToFocus == '' || elementToFocus == null){
                elementToFocus = reporting_year_obj;
            }
        }
        else{
            reporting_year_obj.closest('div').find('.error').html('&nbsp;');
        }

        amc_name_obj = formObj.find('#amc_name');
        amc_name = $.trim(amc_name_obj.val());
        if(amc_name == '' || amc_name == null){
            err_flag = true;
            amc_name_obj.closest('div').find('.error').text('Please select amc name');
            // alert('Reporting year is required');
            if(elementToFocus == '' || elementToFocus == null){
                elementToFocus = amc_name_obj;
            }
        }
        else{
            amc_name_obj.closest('div').find('.error').html('&nbsp;');
        }
    }

    if(fileType == 'mos_multiplier_data'){
        multiplier_type_obj = formObj.find('#multiplier_type');
        multiplier_type = $.trim(multiplier_type_obj.val());
        if(multiplier_type == '' || multiplier_type == null){
            err_flag = true;
            multiplier_type_obj.closest('div').find('.error').text('Multiplier type is required');
            if(elementToFocus == '' || elementToFocus == null){
                elementToFocus = multiplier_type_obj;
            }
        }
        else{
            multiplier_type_obj.closest('div').find('.error').html('&nbsp;');
        }
    }

    upload_file_obj = formObj.find('#uploadfile');
    upload_file = document.getElementsByName('uploadfile')[0];
    if(upload_file.files.length == 0){
        err_flag = true;
        // alert('Upload file is required');
        upload_file_obj.closest('div').find('.error').show().text('Upload file is required');
        if(elementToFocus == '' || elementToFocus == null){
            elementToFocus = upload_file_obj;
        }
    }
    else if(upload_file.files.length == 1 && ($.inArray(upload_file.files[0]['type'], ['text/csv','application/vnd.ms-excel']) == -1) ){
        //checking whether Image uploaded or not
        err_flag = true;
        // alert('Please select an csv file');
        upload_file_obj.closest('div').find('.error').show().text('Please select an csv file');
        if(elementToFocus == '' || elementToFocus == null){
            elementToFocus = upload_file_obj;
        }
    }
    else if(upload_file.files[0].size > max_size_in_kb){
    // checking whether Input File size is of greater than 5MB or not
        err_flag = true;
        // alert('Maximum file upload size should not exceed 10MB');
        upload_file_obj.closest('div').find('.error').show().text('Maximum file upload size should not exceed 10MB');
        if(elementToFocus == '' || elementToFocus == null){
            elementToFocus = upload_file_obj;
        }
    }
    else{
        upload_file_obj.closest('div').find('.error').hide().html('&nbsp;');
    }
    if(!err_flag){
        //code executed if all validation done
        if($("#file_type").val() == 'arn_bdm_mapping'){
            swal({
                title: "Are you sure?",
                text: "Do you want to map ARN even though their RM relationship flag is FINAL?",
                icon: "warning",
                buttons: ["No", "Yes"],
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    save_uploaded_file(true);
                } else {
                    save_uploaded_file(false);
                }
            });
        }
        else{
            save_uploaded_file(true);
        }
    }
    else{
        // adding focus to an element having an error
        if(elementToFocus != null && elementToFocus != '' && elementToFocus.length > 0){
            elementToFocus.focus();
        }
    }
});
</script>
@endsection
