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
@section('title', 'Meeting Log List')
@section('breadcrumb_heading', 'Meeting Log List')

@section('content')

<div class="row">
    <!--div class="col-md-12">
        <div class="border-bottom display-flex">
            <h2 class="">Meeting Log : View </span></h2>
        </div>
    </div-->
    <div class="col-lg-12">
        <div class="mt-2">
            <table id="panel_meetinglog" class="display" style="width:100%" data-arn_number="{{$arn_number??''}}">
            <thead>
                    <tr>
                        @php
                            echo $data_table_headings_html;
                        @endphp
                    </tr>
                    <tr>
                        @php
                            echo $data_table_headings_html;
                        @endphp
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        @php
                            echo $data_table_headings_html;
                        @endphp
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!--/.row mt-4-->

@endsection

@section('custom_after_footer_html')

<!-- View Modal -->
<div class="modal fade" id="view_meetinglog_modal" tabindex="-1" role="dialog" aria-labelledby="view_meetinglog_modal_label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" class="close closed"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="view_meetinglog_modal_label">View:Meeting Log</h4>
            </div>
            <div class="modal-body">
                <div class="mt-2">
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>ARN</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="arn"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Meeting Mode</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="meeting_mode"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Contact Name</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="contact_name"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Contact Mobile</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="contact_mobile"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Contact Email</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="contact_email"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Start Time</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="start_time"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>End Time</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="end_time"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Remarks</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="remarks"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Customer Response Received</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="customer_response"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Customer Response Source</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="customer_source"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Customer Response Received Date</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="customer_response_received_date"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Customer Given Rating</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="customer_rating"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Customer Feedback</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="customer_feedback"></span>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <strong>Product Information Received</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="product_information"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<!-- End View modal -->

@endsection

@section('custom_scripts')

    <script src="{{asset('js/meetinglog.js')}}"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        load_meetinglog_datatable();
    });
    </script>

@endsection
