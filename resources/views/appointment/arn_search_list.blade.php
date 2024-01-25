@php
$data_table_headings_html = '';
if(isset($data_table_headings) && is_array($data_table_headings) && count($data_table_headings) > 0){
    foreach($data_table_headings as $key => $value){
        $data_table_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}
@endphp

@extends('../layout')
@section('title', 'Search ARN')
@section('breadcrumb_heading', 'Search ARN List')

@section('custom_head_tags')

@endsection

@section('content')

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="mt-2">
            <form class="" name="arnSearchForm"  method="post" action="javascript:void(0)">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row form-inline">
                            
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label >ARN Number</label> 
                                    <div class="input-group-append">
                                        <input id="search_arn" name="search_arn" type="text" class="form-control" placeholder="ARN Number">
                                        <div class="error">&nbsp;</div>                             
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="col-lg-12">
                                    <div class="">
                                        <button type="button" class="btn btn-primary" id="submit_arn_search">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-2">
        <table class="table table-striped d-none" id="search_arn_tbl">
            <thead>
                <tr>
                    <th>ARN</th>
                    <th>Name</th>
                    <th>RankMF Partner (yes/no)</th>
                    <th>RankMF Stage of Prospect</th>
                    {{-- <th>SamcoMF Partner (yes/no)</th>
                    <th>SamcoMF Stage of Prospect</th> --}}
                </tr>
            </thead>
            <tbody id="showdata">
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection

@section('custom_scripts')
<script type="text/javascript">
    $(document).on('keyup', function(event){
      if(event.which == 13){
        if($('#submit_arn_search').is(':visible')){
          $('#submit_arn_search').trigger('click');
        }
        else{
          event.preventDefault();
        }
      }
    });
   $("#submit_arn_search").on("click", function(){
    var err_flag = false, elementToFocus = '', formObj = $('[name="arnSearchForm"]');
    var arnNumber, arnNumber_obj;

    arnNumber_obj = formObj.find('#search_arn');
    arnNumber = $.trim(arnNumber_obj.val());
    var spacevar = "";
    if(arnNumber == '' || arnNumber == null){
        err_flag = true;
        arnNumber_obj.closest('div').find('.error').text('Please enter arn number');
        // alert('Please select file type');
        if(elementToFocus == '' || elementToFocus == null){
            elementToFocus = arnNumber_obj;
        }
    }
    else if(arnNumber == 0){
        err_flag = true;
        arnNumber_obj.closest('div').find('.error').text('Please enter a valid arn number');
        // alert('Please select file type');
        if(elementToFocus == '' || elementToFocus == null){
            elementToFocus = arnNumber_obj;
        }
    }
    else{
        arnNumber_obj.siblings('.error').html('&nbsp;');
    }

    if(!err_flag){
        //code executed if all validation done
        var form = $('[name="arnSearchForm"]')[0]; // You need to use standard javascript object here
        var formData = new FormData(form);
        $.ajax({
            url: baseurl + "/search-arn",
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
                    $('#search_arn_tbl').hide();
                    swal("", data.data, "error");                 
                }
                else
                {
                    var html = '';
                    $('#search_arn_tbl').show();
                    var result = JSON.parse(JSON.stringify(data.data).replace(/\:null/gi, "\:\"\"")); 
                    // console.log('result',result);
                    // console.log('data.data',data.data);
                    $.each(result,function(key,value){
                        html +='<tr>';
                        html +='<td>'+ value.ARN + '</td>';
                        html +='<td>'+ value.arn_holders_name + '</td>';
                        html +='<td>'+ value.is_rankmf_partner + '</td>';
                        html +='<td>'+ value.rankmf_stage_of_prospect + '</td>';
                        // html +='<td>'+ value.is_samcomf_partner + '</td>';
                        // html +='<td>'+ value.samcomf_stage_of_prospectARN + '</td>';
                        html +='</tr>';
                    });
                    $('#showdata').html(html);
                }
            },
            complete: function(){
                
            }
        });

    }else{
        // adding focus to an element having an error
        if(elementToFocus != null && elementToFocus != '' && elementToFocus.length > 0){
            elementToFocus.focus();
        }
    }
});
</script>
@endsection
