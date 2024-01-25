@php
$page_title = '';
// page url where user will gets redirected when he/she clicks on CANCEL button
$back_page_url = URL::to('/mos_multiplier_data');

@endphp
@extends('../layout')
@section('title', $page_title)
@section('breadcrumb_heading', $page_title)
@section('custom_head_tags')

@endsection

@section('content')
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="mt-2">
            <form id="frm_sms_vendors" class="" action="{{url('mos_multiplier_data_add')}}" method="post" onsubmit="" autocomplete="off">       
            @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row form-inline">
                                                     
                            <div class="col-lg-6 col-sm-auto">
                                <div class="form-group">
                                    <label><b>Multiplier Type</b></label> 
                                    <input type="text"  class="form-control" id="select_multiplier" name="select_multiplier" value="{{old('select_multiplier')}}">
                                    <!-- <p class="error">&nbsp;</p> -->
                                    @error('select_multiplier')
                                         <p class="error">{{$message}}</p>
                                    @enderror

                                </div><!--/.form-group-->
                            </div><!--/.col-->

                            <div class="col-lg-6 col-sm-auto">
                                <div class="form-group">
                                    <label><b>Margin Of Safety</b></label> 
                                    <input type="text"  class="form-control" id="select_margin" name="select_margin" value="{{old('select_margin')}}">
                                    @error('select_margin')
                                         <p class="error">{{$message}}</p>
                                    @enderror
                                </div><!--/.form-group-->
                            </div><!--/.col-->


                            <div class="col-lg-6 col-sm-auto">
                                <div class="form-group">
                                    <label><b>Multiplier Value</b></label> 
                                    <input type="text"  class="form-control" id="select_multipliervalue" name="select_multipliervalue" value="{{old('select_multipliervalue')}}">
                                    @error('select_multipliervalue')
                                         <p class="error">{{$message}}</p>
                                    @enderror
                                </div><!--/.form-group-->
                            </div><!--/.col-->

                        </div><!--/.row-->
                    </div><!--/.col-lg-12-->
                </div><!--/.row-->
                <div class="row">
                    <div class="col-lg-12">&nbsp;</div>
                </div><!--/.row-->
                <div class="row">
                    <div class="col-lg-12">&nbsp;</div>
                </div><!--/.row-->
                <div class="row">
                    <div class="col">
                        <button type="submit" class="btn btn-primary" id="btn_submit" name = "btn_submit">Submit</button>
                    </div><!--/.col-->
                    <div class="col text-right">
                        <button type="button" class="btn btn-default" id="btn_back" onclick="window.open('{{$back_page_url}}', '_parent');">Cancel</button>
                    </div><!--/.col-->
                </div><!--/.row-->
            </form><!--/form-->
        </div><!--/.mt-2-->
    </div><!--/.col-lg-12-->
</div><!--/.row-->

@endsection
@section('custom_scripts')
<script type="text/javascript">
    $(document).ready(function(){
  $("#frm_sms_vendors").on("submit", function(){
    $(".loader").fadeIn();
  });//submit
});//document ready
</script>
@endsection