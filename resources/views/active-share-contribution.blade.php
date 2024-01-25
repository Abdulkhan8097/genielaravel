@php
$data_table_headings_html = '';
$data_table_headings = array(
    'Schemecode' => array('label' => 'Schemecode'),
    'IndexCode' => array('label' => 'Index Code'),
    'IndexName' => array('label' => 'Index Name'),
    'isin' => array('label' => 'ISIN'),
    'symbol' => array('label' => 'Symbol'),
    'COMPNAME' => array('label' => 'Company'),
    'Holdpercentage' => array('label' => 'Holding'),
    'index_weightage' => array('label' => 'Index Weightage'),
    'abs_diff' => array('label' => 'Abs Diff'),
    'active_share_contribution' => array('label' => 'Active Share Contribution'),
);
if(isset($data_table_headings) && is_array($data_table_headings) && count($data_table_headings) > 0){
    foreach($data_table_headings as $key => $value){
        $data_table_headings_html .= '<th data-column="'. $key .'">'. $value['label'] .'</th>';
    }
    unset($key, $value);
}

@endphp
@extends('../layout_open')
@section('title', 'Active Share Contribution')
@section('custom_head_tags')

    <link href="{{asset('css/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/select2.min.css')}}" rel="stylesheet"/>
    <style type="text/css">
        h2 span.red-font{font-size: 16px; color: #ff0000; font-weight: 500;}
        h2 span.green-font{font-size: 16px; color: #008000; font-weight: 500;}

        .select-input{
            width: 400px;
            margin: 0 auto;
        }

        @media(max-width:767px){
            .select-input{
                width: 100%;                
            }
        }

    </style>
@endsection

@section('content')
<div class="container mt-4">
    <div class="col-lg-12">
    <div class="row">
    <div class="col-lg-12">
        <div class="text-center">
        <h2 class="border-bottom">Active Share Contribution</h2>
        </div>
        <div class="mt-2">
            <form class="" id="schemeSearch" name="schemeSearch"  method="post"  action="javascript:void(0)">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="select-input">
                            <div class="form-group">
                                <label >Scheme list</label> 
                                <select class="form-control" id="txt_search_companyname" name="txt_search_companyname">
                                </select>
                                <div class="error">&nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-2">
            <!-- <div class="row">
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
            </div> -->
        </div>
    </div>
</div>
    </div>
</div>
<div class="container">
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="tab-content-item" id="tab_index_data">
                
            </div>
        </div>
    </div>
</div>

@endsection

@section('custom_scripts')
<script src="{{asset('js/select2.min.js')}}"></script>
<script>
    // Navigation Button  //
    $("body").on("click",".new-tab li a", function(a) {
        a.preventDefault(), 
        $(this).parent().addClass("active"), 
        $(this).parent().siblings().removeClass("active");
        var t = $(this).attr("href").split("#")[1];
        $(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id="' + t + '"]').fadeIn(), 
        $(this).parents(".tab-content-item").find(".data-tabs").children('.tab-list[id!="' + t + '"]').hide()
    });
</script>
<script type="text/javascript">

$(document).ready(function(){
    $(document).on('click', 'ul.nav li.nav-item', function(){
        //window.setTimeout(function(){
            $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
        //}, 500);
    });

    $('#txt_search_companyname').select2({
        ajax: {
            type:"post",
            dataType: "json",
            url:"{{ route('get_scheme_list') }}",
            data: function (params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function (data) {
                return {
                    results: $.map(data.items, function (item) {
                        return {
                            text: item.text,
                            id: item.id
                        }
                    })
                };
            }
        }
    });
});


$("#txt_search_companyname").on("change", function(){
    var schemecode = $(this).val();
    $.ajax({
            url:"{{ route('get_active_share') }}",
            dataType : "html",
            type:"post",
            data:{schemecode:parseInt(schemecode)},
            beforeSend: function() {
                
            },
            error: function(){
                swal("", unable_to_process_request_text, "warning");
            },
            success: function(data){
                if(data.status=="error")
                {
                    swal("", data.data, "error");
                }
                else
                {
                    $("#tab_index_data").html(data);
                    // swal("", data.message, "success"); 
                    $('[id="example"]').DataTable({
                        language: {
                                oPaginate: {
                                sNext: '<i class="icons angle-right"></i>',
                                sPrevious: '<i class="icons angle-left"></i>',
                                sFirst: '<i class="icons step-backward"></i>',
                                sLast: '<i class="icons step-forward"></i>'
                            }
                        },
                        "pageLength": 50,
                    }); 
                }
            },
            complete: function(){
                
            }
        });

        // var xhttp = new XMLHttpRequest();
        // xhttp.onreadystatechange = function() {
        //     if (this.readyState == 4 && this.status == 200) {
        //         console.log(this);
        //     //document.getElementById("demo").innerHTML = this.responseText;
        //     }
        // };
        // xhttp.open("POST", "{{ route('get_active_share') }}", true);
        // xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        // xhttp.send("shemecode="+shemecode+"&_token="+$("input[name=_token]").val());
});

$('body').on("click",".csv_download", function(){
    var indexcode = $(this).attr('indexcode');
    var schemecode = $('#txt_search_companyname').val();
    var schemename = $('#txt_search_companyname').text().trim();
    export_csv_formatted_data(indexcode,schemecode,schemename);
});


function export_csv_formatted_data(indexcode='',schemecode='',schemename=''){
        var formObj = $('#frm_export_data');
        formObj.append('<input type="hidden" name="export_data" value="1">');
        formObj.append('<input type="hidden" name="indexcode" value="'+indexcode+'">');
        formObj.append('<input type="hidden" name="schemecode" value="'+schemecode+'">');
        formObj.append('<input type="hidden" name="schemename" value="'+schemename+'">');
        // formObj.append('<input type="hidden" name="user_input_radio" value="'+$("input[name='user_input_radio']:checked").val()+'">');
        // formObj.append('<input type="hidden" name="fromDate" value="'+$("input[name='fromDate']").val()+'">');
        // formObj.append('<input type="hidden" name="toDate" value="'+$("input[name='toDate']").val()+'">');
        // formObj.append('<input type="hidden" name="searchVar" value="'+$("input[name='searchVar']").val()+'">');
        formObj.attr({'action': baseurl + '/get_active_share_csv'});
        formObj.submit();
        formObj.attr({'action':'javascript:void(0);'});
        $('#frm_export_data input[name!="_token"]').remove();
    }

</script>
@endsection
