@php
$todays_date = date('Y-m-d');
if(isset($data['date']) && !empty($data['date']) && strtotime($data['date']) !== FALSE){
  $todays_date = date('Y-m-d', strtotime($data['date']));
}

$yesterdays_date = date('Y-m-d');
if(isset($data['yesterdayDate']) && !empty($data['yesterdayDate']) && strtotime($data['yesterdayDate']) !== FALSE){
  $yesterdays_date = date('Y-m-d', strtotime($data['yesterdayDate']));
}

if(!isset($stp_end_date) || (isset($stp_end_date) && !empty($stp_end_date) && strtotime(stp_end_date) === FALSE)) {
  $stp_end_date = $todays_date;
}

if(!isset($sip_end_date) || (isset($sip_end_date) && !empty($sip_end_date) && strtotime(sip_end_date) === FALSE)) {
  $sip_end_date = $todays_date;
}
$stp_start_date = '2005-01';
$sip_start_date = '2005-01';
@endphp
@extends('../layout')
@section('title', 'Booster STP SIP')
@section('breadcrumb_heading', 'Backtest Result')

@section('custom_head_tags')

    <style type="text/css">
        .faq-question::before{
          padding-right: 0px!important;
          content:unset!important;
        }
        .faq-answer::before{
          padding-right: 0px!important;
          content:unset!important;
        }
        .mt-20{
          margin-top: 20px!important;
        }
        div.form-group label{
          font-weight: bold;
        }
        div.form-group em{
          color: #8b8787;
        }
    </style>

@endsection

@section('content')

                      <div class="row mt-4">
                        <div class="col-md-12 mb-2">
                          <div class="tab-content-item">
                            <ul class="nav nav-tabs new-tab mt-0" id="myTab" role="tablist">
                              <li class="nav-item active">
                                <a class="nav-link" href="#booster_stp_result">STP</a>
                              </li><!--/.nav-item-->
                              <li class="nav-item">
                                <a class="nav-link" href="#booster_sip_result">SIP</a>
                              </li><!--/.nav-item-->
                            </ul><!--/.nav-->
                            <div class="tab-content data-tabs">
                              <div class="tab-pane show active tab-list" id="booster_stp_result">
                                <div class="row mt-4">
                                  <div class="col-lg-12">
                                    <div class="faq-border-top">
                                      <div class="faq-box">
                                        <div id="stp_accordian_filter" class="faq-question text-center">Show Filter<i class="arrow"></i></div><!--/.faq-question text-center-->
                                        <div class="faq-answer">
                                          <form name="frm_stp_search_filters" action="{{route('booster-stp-sip')}}" method="post" autocomplete="off" target="_blank">
                                            @csrf
                                            <div class="row">
                                              <div class="col-lg-12">
                                                <div class="row form-inline">
                                                  <div class="col-md-3 col-sm-3">
                                                    <div class="form-group">
                                                      <label>Source Scheme</label>
                                                      <input type="hidden" name="select_stp_source_scheme_label">
                                                      <select class="form-control" name="select_stp_source_scheme">
                                                        <!--option value="">Select One</option-->
                                                      @foreach($arr_index_symbol as $record)
                                                        @if($record['source_target'] == 1)
                                                        <option value="{{$record['symbol']}}">{{$record['display_name']}}</option>
                                                        @endif
                                                      @endforeach
                                                      </select><!--/.form-control-->
                                                    </div><!--/.form-group-->
                                                  </div><!--/.col-md-3 col-sm-3-->
                                                  <div class="col-md-3 col-sm-3">
                                                    <div class="form-group">
                                                      <label>Start Date</label>
                                                      <input type="month" class="form-control" name="stp_start_date" value="{{$stp_start_date}}">
                                                    </div><!--/.form-group-->
                                                  </div><!--/.col-md-3 col-sm-3-->
                                                  <div class="col-md-3 col-sm-3">
                                                    <div class="form-group">
                                                      <label>End Date</label>
                                                      <input type="date" class="form-control" name="stp_end_date" value="{{$stp_end_date}}">
                                                    </div><!--/.form-group-->
                                                  </div><!--/.col-md-3 col-sm-3-->
                                                  <div class="col-md-3 col-sm-3">
                                                    <div class="form-group">
                                                      <label>Multiplier Type</label>
                                                      <select class="form-control" name="stp_multiplier_type">
                                                        <!--option value="">Select One</option-->
                                                      @foreach($arr_multiplier_type as $record)
                                                        <option value="{{$record}}">{{$record}}</option>
                                                      @endforeach
                                                      </select><!--/.form-control-->
                                                    </div><!--/.form-group-->
                                                  </div><!--/.col-md-3 col-sm-3-->
                                                </div><!--/.row form-inline-->
                                              </div><!--/.col-lg-12-->
                                            </div><!--/.row-->
                                            <div class="row mt-20">
                                              <div class="col-md-3 col-sm-3">
                                                <div class="form-group">
                                                  <label>Target Scheme</label>
                                                  <input type="hidden" name="select_stp_target_scheme_label">
                                                  <select class="form-control" name="select_stp_target_scheme">
                                                    <!--option value="">Select One</option-->
                                                  @foreach($arr_index_symbol as $record)
                                                    @if($record['source_target'] == 2)
                                                    <option value="{{$record['symbol']}}">{{$record['display_name']}}</option>
                                                    @endif
                                                  @endforeach
                                                  </select><!--/.form-control-->
                                                </div><!--/.form-group-->
                                              </div><!--/.col-md-3 col-sm-3-->
                                              <div class="col-md-3 col-sm-3">
                                                <div class="form-group">
                                                  <label>Opening balance</label>
                                                  <input type="text" class="form-control" name="stp_opening_balance" value="1200000">
                                                </div><!--/.form-group-->
                                              </div><!--/.col-md-3 col-sm-3-->
                                              <div class="col-md-3 col-sm-3">
                                                <div class="form-group">
                                                  <label>Base amount <em><small>(Used for Booster STP)</small></em></label>
                                                  <input type="text" class="form-control" name="stp_base_amount" value="10000">
                                                </div><!--/.form-group-->
                                              </div><!--/.col-md-3 col-sm-3-->
                                              <div class="col-md-3 col-sm-3">
                                                <div class="form-group">
                                                  <label>Download Format</label>
                                                  <select class="form-control" name="stp_report_download_format">
                                                    <option value="detailed">Detailed</option>
                                                    <option value="summary">Summary</option>
                                                  </select>
                                                </div><!--/.form-group-->
                                              </div><!--/.col-md-3 col-sm-3-->
                                            </div><!--/.row-->
                                            <div class="row">
                                              <div class="col-md-3 col-sm-3">
                                                <div class="form-group">
                                                  <label>Month Date</label>
                                                  <select class="form-control" name="stp_report_month_date">
                                                    <option value="MIN">First</option>
                                                    <option value="MAX">Last</option>
                                                  </select>
                                                </div><!--/.form-group-->
                                              </div><!--/.col-md-3 col-sm-3-->
                                              <div class="col-md-9 col-sm-9 text-right mt-20">
                                                <button type="submit" class="btn btn-primary" name="btn_stp_submit" accesskey="s" value="submit"><u>S</u>ubmit</button>
                                                <button type="reset" class="btn btn-default" name="btn_stp_reset" accesskey="r"><u>R</u>eset</button>
                                              </div><!--/.col-md-9 col-sm-9-->
                                            </div><!--/.row-->
                                          </form>
                                        </div><!--/.faq-answer-->
                                      </div><!--/.faq-box-->
                                    </div><!--/.faq-border-top-->
                                  </div><!--/.col-lg-12-->
                                </div><!--/.row mt-4-->
                              </div><!--#booster_stp_result-->
                              <div class="tab-pane show tab-list" id="booster_sip_result" style="display:none;">
                                <div class="row mt-4">
                                  <div class="col-lg-12">
                                    <div class="faq-border-top">
                                      <div class="faq-box">
                                        <div id="sip_accordian_filter" class="faq-question text-center">Show Filter<i class="arrow"></i></div><!--/.faq-question text-center-->
                                        <div class="faq-answer">
                                          <form name="frm_sip_search_filters" action="{{route('booster-stp-sip')}}" method="post" autocomplete="off" target="_blank">
                                            @csrf
                                            <div class="row">
                                              <div class="col-lg-12">
                                                <div class="row form-inline">
                                                  <div class="col-md-3 col-sm-3">
                                                    <div class="form-group">
                                                      <label>Source Scheme</label>
                                                      <input type="hidden" name="select_sip_source_scheme_label">
                                                      <select class="form-control" name="select_sip_source_scheme">
                                                        <!--option value="">Select One</option-->
                                                      @foreach($arr_index_symbol as $record)
                                                        @if($record['source_target'] == 1)
                                                        <option value="{{$record['symbol']}}">{{$record['display_name']}}</option>
                                                        @endif
                                                      @endforeach
                                                      </select><!--/.form-control-->
                                                    </div><!--/.form-group-->
                                                  </div><!--/.col-md-3 col-sm-3-->
                                                  <div class="col-md-3 col-sm-3">
                                                    <div class="form-group">
                                                      <label>Start Date</label>
                                                      <input type="month" class="form-control" name="sip_start_date" value="{{$sip_start_date}}">
                                                    </div><!--/.form-group-->
                                                  </div><!--/.col-md-3 col-sm-3-->
                                                  <div class="col-md-3 col-sm-3">
                                                    <div class="form-group">
                                                      <label>End Date</label>
                                                      <input type="date" class="form-control" name="sip_end_date" value="{{$sip_end_date}}">
                                                    </div><!--/.form-group-->
                                                  </div><!--/.col-md-3 col-sm-3-->
                                                  <div class="col-md-3 col-sm-3">
                                                    <div class="form-group">
                                                      <label>Multiplier Type</label>
                                                      <select class="form-control" name="sip_multiplier_type">
                                                        <!--option value="">Select One</option-->
                                                      @foreach($arr_multiplier_type as $record)
                                                        <option value="{{$record}}">{{$record}}</option>
                                                      @endforeach
                                                      </select><!--/.form-control-->
                                                    </div><!--/.form-group-->
                                                  </div><!--/.col-md-3 col-sm-3-->
                                                </div><!--/.row form-inline-->
                                              </div><!--/.col-lg-12-->
                                            </div><!--/.row-->
                                            <div class="row mt-20">
                                              <div class="col-md-3 col-sm-3">
                                                <div class="form-group">
                                                  <label>Target Scheme</label>
                                                  <input type="hidden" name="select_sip_target_scheme_label">
                                                  <select class="form-control" name="select_sip_target_scheme">
                                                    <!--option value="">Select One</option-->
                                                  @foreach($arr_index_symbol as $record)
                                                    @if($record['source_target'] == 2)
                                                    <option value="{{$record['symbol']}}">{{$record['display_name']}}</option>
                                                    @endif
                                                  @endforeach
                                                  </select><!--/.form-control-->
                                                </div><!--/.form-group-->
                                              </div><!--/.col-md-3 col-sm-3-->
                                              <div class="col-md-3 col-sm-3">
                                                <div class="form-group">
                                                  <label>Opening balance</label>
                                                  <input type="text" class="form-control" name="sip_opening_balance" value="1200000">
                                                </div><!--/.form-group-->
                                              </div><!--/.col-md-3 col-sm-3-->
                                              <div class="col-md-3 col-sm-3">
                                                <div class="form-group">
                                                  <label>Base amount <em><small>(Used for Booster SIP)</small></em></label>
                                                  <input type="text" class="form-control" name="sip_base_amount" value="1000">
                                                </div><!--/.form-group-->
                                              </div><!--/.col-md-3 col-sm-3-->
                                              <div class="col-md-3 col-sm-3 mt-20">
                                                <button type="submit" class="btn btn-primary" name="btn_sip_submit" accesskey="s" value="submit"><u>S</u>ubmit</button>
                                                <button type="reset" class="btn btn-default" name="btn_sip_reset" accesskey="r"><u>R</u>eset</button>
                                              </div><!--/.col-md-3 col-sm-3-->
                                            </div><!--/.row-->
                                          </form>
                                        </div><!--/.faq-answer-->
                                      </div><!--/.faq-box-->
                                    </div><!--/.faq-border-top-->
                                  </div><!--/.col-lg-12-->
                                </div><!--/.row mt-4-->
                              </div><!--#booster_sip_result-->
                            </div><!--/.tab-content-->
                          </div><!--/.tab-content-item-->
                        </div><!--/.col-md-12-->
                      </div><!--/.row mt-4-->

@endsection

@section('custom_scripts')

    <script type="text/javascript" src="{{asset('js/booster_stp_sip.js?v=0.3')}}"></script>

@endsection
