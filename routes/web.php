<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureUserHasPermission;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Auth::routes(['register' => false]);
Route::get('login', 'App\Http\Controllers\LoginController@index')->name('login');
Route::get('logout', 'App\Http\Controllers\LoginController@index')->name('logout');
Route::post('custom-login', 'App\Http\Controllers\LoginController@customLogin')->name('login.custom');
Route::match(['get', 'post'], '/', 'App\Http\Controllers\HomeController@index');
Route::get('active-share-contribution', 'App\Http\Controllers\ActiveShareController@index');
Route::post('get_scheme_list', array('as' => 'get_scheme_list', 'uses' => 'App\Http\Controllers\ActiveShareController@get_scheme_list'));
Route::post('get_active_share', array('as' => 'get_active_share', 'uses' => 'App\Http\Controllers\ActiveShareController@get_active_share'));
Route::post('get_active_share_csv', array('as' => 'get_active_share_csv', 'uses' => 'App\Http\Controllers\ActiveShareController@get_active_share_csv'));
Route::match(['get', 'post'], '/update-profile', 'App\Http\Controllers\LoginController@profile');

// adding routes under middleware group "user.permission" for verifying whether an acessing page have permission or not for that page
Route::middleware(['user.permission'])->group(function () {
    Route::match(['get', 'post'], '/distributorslist', 'App\Http\Controllers\Distributors@index');
    Route::match(['get', 'post'], '/aumdatalist', 'App\Http\Controllers\Aumdata@index');
    Route::match(['get', 'post'], '/arnamcwisedata', 'App\Http\Controllers\Arnamcwisedata@index');
    Route::match(['get', 'post'], '/arndistributorcategorydata', 'App\Http\Controllers\Arndistributorcategorydata@index');
    Route::match(['get', 'post'], '/arnprojectfocus', 'App\Http\Controllers\Arnprojectfocus@index');
    Route::match(['get', 'post'], '/pincodelist', 'App\Http\Controllers\Pincodemaster@index');
    Route::match(['get', 'post'], '/arnindaumdata', 'App\Http\Controllers\Arnindaummaster@index');
    Route::match(['get', 'post'], '/arnbdmmapping', 'App\Http\Controllers\Arnbdmmapping@index');
    Route::match(['get', 'post'], '/arnprojectemergingdata', 'App\Http\Controllers\Arnprojectemergingstars@index');
    Route::match(['get', 'post'], '/arnprojectgreenshoots', 'App\Http\Controllers\Arnprojectgreenshoots@index');
    Route::match(['get', 'post'], '/amficityzone', 'App\Http\Controllers\Amficityzone@index');
    Route::match(['get', 'post'], '/arnalternatedata', 'App\Http\Controllers\Arnalternatedata@index');
    Route::get('upload', array('as' => 'upload', 'uses' => 'App\Http\Controllers\ImportFileController@index'));
    Route::post('save_uploaded_data', array('as' => 'save_uploaded_data', 'uses' => 'App\Http\Controllers\ImportFileController@saveUploadData'));
    Route::get('/distributor/{arn_number}', 'App\Http\Controllers\Distributors@view');
    Route::post('distributor/UpdateByArn', 'App\Http\Controllers\Distributors@UpdateByArn');
    Route::post('/distributor_exportToCSV', 'App\Http\Controllers\Distributors@exporttoCSV');
    Route::post('/distributor/auto-assign-bdm/{arn_number}', 'App\Http\Controllers\Distributors@autoAssignBDM');
    Route::match(['get', 'post'], '/usermasterlist', 'App\Http\Controllers\Usermaster@index');
    Route::post('add-user', 'App\Http\Controllers\Usermaster@store')->name('adduser');
    Route::post('check-email', 'App\Http\Controllers\Usermaster@duplicateemail')->name('checkemail')->withoutMiddleware('user.permission');
    Route::post('edit-detail', 'App\Http\Controllers\Usermaster@get_edit_detail')->name('edit.usermasterdetail');
    Route::post('usermaster-update', 'App\Http\Controllers\Usermaster@updateusermaster')->name('updateusermasterdetail');
    Route::post('get-reporting', 'App\Http\Controllers\Usermaster@get_reporting')->name('get_report_detail')->withoutMiddleware('user.permission');
    Route::post('services-pincode', 'App\Http\Controllers\Usermaster@get_services_pincode')->name('get.servicespincode');
    Route::match(['get', 'post'],'meetinglog', 'App\Http\Controllers\MeetingLogController@index');
    Route::match(['get', 'post'],'meetinglog/create/{arn_number}', 'App\Http\Controllers\MeetingLogController@create');
    Route::match(['get', 'post'],'meetinglog/edit/{logID}', 'App\Http\Controllers\MeetingLogController@edit');
    Route::post('save_meeting_data', 'App\Http\Controllers\MeetingLogController@save_data');
    Route::post('update_meeting_data', 'App\Http\Controllers\MeetingLogController@update_meeting_data');
    Route::post('view-detail', 'App\Http\Controllers\MeetingLogController@get_view_detail')->name('view.meetinglogdetail');
    Route::post('meeting-feedback-notification', 'App\Http\Controllers\MeetingLogController@meeting_feedback_notification');
    Route::match(['get', 'post'],'appointment', 'App\Http\Controllers\AppointmentController@index');
    Route::post('generate_appointment_list', array('as' => 'generate_appointment_list', 'uses' => 'App\Http\Controllers\AppointmentController@generate_appointment_list'))->withoutMiddleware('user.permission');
    Route::match(['get', 'post'], '/roles', 'App\Http\Controllers\Rolemaster@index');
    Route::match(['get', 'post'], '/roles/addedit', 'App\Http\Controllers\Rolemaster@addedit');
    Route::match(['get', 'post'], '/roles/addedit/{role_id?}', 'App\Http\Controllers\Rolemaster@addedit');
    Route::post('/commission_exportToCSV', 'App\Http\Controllers\Distributors@exportCommissionStructuretoCSV');
    Route::post('edit-commission-detail', 'App\Http\Controllers\Distributors@get_edit_commission_detail')->name('edit.commissiondetail');
    Route::post('commission-update', 'App\Http\Controllers\Distributors@updatecommissionstructure')->name('updatecommissiondetail');
    Route::match(['get', 'post'], '/report-of-project-focus-partner', 'App\Http\Controllers\ReportsController@getReportofProjectFocusPartner');
    Route::match(['get', 'post'], '/report-of-project-emerge-partner', 'App\Http\Controllers\ReportsController@getReportofProjectEmergePartner');
    Route::match(['get', 'post'], '/report-of-partner-aum-no-transactions', 'App\Http\Controllers\ReportsController@getPartnerwithAumButNoTransactions');
    Route::match(['get', 'post'], '/report-of-partner-aum-no-active-sip', 'App\Http\Controllers\ReportsController@getPartnerwithAumButNoActiveSIP');
    Route::match(['get', 'post'], '/report-of-partner-aum-unique-client', 'App\Http\Controllers\ReportsController@getPartnerwithAumUniqueClient');
    Route::get('user-hierarchy', 'App\Http\Controllers\UserHierarchy@getUserDetailHierarchy');
    Route::get('user-hierarchy-json', 'App\Http\Controllers\UserHierarchy@userHierarchyDetailJson');
    Route::get('user-hierarchy-tree-json', 'App\Http\Controllers\UserHierarchy@userHierarchyDetailJsonNew');
    Route::match(['get', 'post'], '/report-of-aum-transaction-analytics', 'App\Http\Controllers\ReportsController@getAumTransactionAnalyitcs');
    Route::match(['get', 'post'], '/report-of-sip-analytics', 'App\Http\Controllers\ReportsController@getSipAnalyitcs');
    Route::match(['get', 'post'], '/report-of-client-analytics', 'App\Http\Controllers\ReportsController@getClientAnalyitcs');
    Route::match(['get', 'post'], '/report-of-daywise-transaction-analytics', 'App\Http\Controllers\ReportsController@getDaywiseTransactionAnalyitcs');
    Route::match(['get', 'post'], '/report-of-client-monthwise-analytics', 'App\Http\Controllers\ReportsController@getClientMonthwiseAnalytics');
    Route::match(['get', 'post'], '/report-of-monthwise-bdmwise-inflows', 'App\Http\Controllers\ReportsController@getBDMMonthwiseInflows');
    Route::match(['get', 'post'], '/download-nse-details', 'App\Http\Controllers\DownloadController@index');
    Route::match(['get', 'post'], '/booster-stp-sip', 'App\Http\Controllers\BoosterStpSipController@index')->name('booster-stp-sip');
    Route::get('nse-historical-indices/{symbol?}/{start_date?}/{end_date?}', 'App\Http\Controllers\DownloadController@nse_historical_indices');
    Route::get('investing-bonding-details/{symbol?}/{start_date?}/{end_date?}', 'App\Http\Controllers\DownloadController@investiong_bonding_details');
    Route::get('nse-historical-index/{symbol?}/{start_date?}/{end_date?}', 'App\Http\Controllers\DownloadController@nse_historical_index');
    Route::get('nse-stock-equity-index/{symbol?}', 'App\Http\Controllers\DownloadController@get_equity_stock_indices');
	Route::match(['get', 'post'],'/mis', 'App\Http\Controllers\InterAMCSwitchMISController@index');
	Route::match(['get', 'post'],'/get-mis-data', 'App\Http\Controllers\InterAMCSwitchMISController@getMisData');
	Route::match(['get', 'post'],'/ajax-unlink-file', 'App\Http\Controllers\InterAMCSwitchMISController@ajax_unlink_file');
	Route::match(['get', 'post'],'/auto-switch-orders', 'App\Http\Controllers\InterAMCSwitchMISController@autoswitch');
	Route::match(['get', 'post'],'/pending-interswitch-mis', 'App\Http\Controllers\InterAMCSwitchMISController@provisional_interswitch_orders');
	// Route::post('/mis', 'App\Http\Controllers\InterAMCSwitchMISController@index')->middleware(['getAccessToken']);
    // retrieve & sending email of DIRECT INVESTORS data
    Route::get('retrieve-daily-direct-orders/{send_email?}', function($send_email=0){
        $txt_artisan_command = "daily_direct_orders_update:retrieve";
        if(!empty($send_email) && is_numeric($send_email)){
            $txt_artisan_command .= " --send_email=". $send_email;
        }
        exec("nohup php ". base_path() ."/artisan ". $txt_artisan_command ." > /dev/null 2>&1 &", $return_var, $exit_code);
        $output_arr = array('artisan_command' => $txt_artisan_command, 'exit_code' => $exit_code, 'return_var' => $return_var);
        return $output_arr;
    });
    // retrieve & sending email of Distributor Empanelment data
    Route::get('retrieve-daily-distributor-empanelment-and-activation-update/{send_email?}', function($send_email=0){
        $txt_artisan_command = "daily_distributor_empanelment_and_activation_update:retrieve";
        if(!empty($send_email) && is_numeric($send_email)){
            $txt_artisan_command .= " --send_email=". $send_email;
        }
        exec("nohup php ". base_path() ."/artisan ". $txt_artisan_command ." > /dev/null 2>&1 &", $return_var, $exit_code);
        $output_arr = array('artisan_command' => $txt_artisan_command, 'exit_code' => $exit_code, 'return_var' => $return_var);
        return $output_arr;
    });
    // calculating ACTIVE SHARE
    Route::get('calc-active-share/{enable_query_log?}/{rta_scheme_code?}/{active_share_date?}', function($enable_query_log=0, $rta_scheme_code='', $active_share_date=''){
        $txt_artisan_command = "activeshare:calculate ";
        if(!empty($rta_scheme_code)){
            $txt_artisan_command .= "--rta_scheme_code=". $rta_scheme_code;
        }
        if(!empty($rta_scheme_code)){
            $txt_artisan_command .= " --active_share_date=". $active_share_date;
        }
        if(!empty($enable_query_log) && is_numeric($enable_query_log)){
            $txt_artisan_command .= " --enable_query_log=". $enable_query_log;
        }
        exec("nohup php ". base_path() ."/artisan ". $txt_artisan_command ." > /dev/null 2>&1 &", $return_var, $exit_code);
        $output_arr = array('artisan_command' => $txt_artisan_command, 'exit_code' => $exit_code, 'return_var' => $return_var);
        return $output_arr;
    });
    // calculating MEDIAN BEER
    Route::get('calc-median-beer/{bond_symbol}/{index_symbol}/{calculate_for_all_dates?}/{from_date?}/{to_date?}/{enable_query_log?}', function($bond_symbol, $index_symbol, $calculate_for_all_dates=0, $from_date='', $to_date='', $enable_query_log=0){
        $txt_artisan_command = "median_beer:calculate --bond_symbol=". $bond_symbol ." --index_symbol=". $index_symbol ." --calculate_for_all_dates=". $calculate_for_all_dates ." --from_date=". $from_date ." --to_date=". $to_date ." --enable_query_log=". $enable_query_log;
        exec("nohup php ". base_path() ."/artisan ". $txt_artisan_command ." > /dev/null 2>&1 &", $return_var, $exit_code);
        $output_arr = array('artisan_command' => $txt_artisan_command, 'exit_code' => $exit_code, 'return_var' => $return_var);
        return $output_arr;
    });
    // calculating MEDIAN DEVIATION
    Route::get('calc-median-deviation/{index_symbol}/{calculate_for_all_dates?}/{from_date?}/{to_date?}/{enable_query_log?}', function($index_symbol, $calculate_for_all_dates=0, $from_date='', $to_date='', $enable_query_log=0){
        $txt_artisan_command = "median_deviation_from_ma1750:calculate --index_symbol=". $index_symbol ." --calculate_for_all_dates=". $calculate_for_all_dates ." --from_date=". $from_date ." --to_date=". $to_date ." --enable_query_log=". $enable_query_log;
        exec("nohup php ". base_path() ."/artisan ". $txt_artisan_command ." > /dev/null 2>&1 &", $return_var, $exit_code);
        $output_arr = array('artisan_command' => $txt_artisan_command, 'exit_code' => $exit_code, 'return_var' => $return_var);
        return $output_arr;
    });
    // calculating EMOSI History
    Route::get('calc-emosi/{bond_symbol}/{index_symbol}/{calculate_for_all_dates?}/{from_date?}/{to_date?}/{enable_query_log?}', function($bond_symbol, $index_symbol, $calculate_for_all_dates=0, $from_date='', $to_date='', $enable_query_log=0){
        $txt_artisan_command = "emosi:calculate --bond_symbol=". $bond_symbol ." --index_symbol=". $index_symbol ." --calculate_for_all_dates=". $calculate_for_all_dates ." --from_date=". $from_date ." --to_date=". $to_date ." --enable_query_log=". $enable_query_log;
        exec("nohup php ". base_path() ."/artisan ". $txt_artisan_command ." > /dev/null 2>&1 &", $return_var, $exit_code);
        $output_arr = array('artisan_command' => $txt_artisan_command, 'exit_code' => $exit_code, 'return_var' => $return_var);
        return $output_arr;
    });
    Route::match(['get', 'post'], 'emosi-data', 'App\Http\Controllers\EmosiData@index')->name('emosi-data');
    Route::match(['get', 'post'], 'emosi-data_create', 'App\Http\Controllers\EmosiData@create')->name('emosi-data_create');
    Route::match(['get', 'post'], 'ajax_show_data_emosi', 'App\Http\Controllers\EmosiData@Ajax_create')->name('ajax_show_data_emosi');
    Route::match(['get', 'post'], '/GetSTPEmosiDetailsSave/{symbol?}/{start_date?}/{end_date?}', 'App\Http\Controllers\DownloadController@save_emosi_value_details_to_kfin');
    Route::get('bond-yied-historical', 'App\Http\Controllers\DownloadController@Bond_Yield_History_data');
    Route::match(['get', 'post'], '/SaveEmosiValueToKfin/{symbol?}/{emosi_value?}', 'App\Http\Controllers\EmosiData@GetSTPEmosiSave');
    Route::match(['get', 'post'], '/get-kfin-emosi-values', 'App\Http\Controllers\EmosiData@GetKfinEmosiValueDetails');
    Route::get('inflow-outflow-order', 'App\Http\Controllers\MasterSipStpTransactionDetailsController@inflow_outflow_order');
    Route::get('empanelment-alert', 'App\Http\Controllers\MasterSipStpTransactionDetailsController@empanelment_alert');
    Route::get('bdm-meeting-dashboard', 'App\Http\Controllers\BDM_Meeting_Dashboard@index');
    Route::get('download-detail-BDM/{bdm_id}/{type}', 'App\Http\Controllers\BDM_Meeting_Dashboard@download_data');
    Route::post('view-detail-BDM', 'App\Http\Controllers\BDM_Meeting_Dashboard@get_view_detail_bdm');
    Route::get('goal', 'App\Http\Controllers\UsresGoalController@index');
    Route::post('goal/set', 'App\Http\Controllers\UsresGoalController@set');
    Route::get('command', 'App\Http\Controllers\RunCommandController@command');
	Route::get('autologin/{user_id}', 'App\Http\Controllers\AutoLoginController@index');
    Route::get('reimbursement/{logid?}', 'App\Http\Controllers\ReimbursementController@index');
    Route::post('reimbursement/add', 'App\Http\Controllers\ReimbursementController@add');
    Route::post('reimbursement/list', 'App\Http\Controllers\ReimbursementController@list');
    Route::post('reimbursement/expense_list', 'App\Http\Controllers\ReimbursementController@expense_list');
    Route::post('reimbursement/addRemark', 'App\Http\Controllers\ReimbursementController@addRemark');
    Route::post('reimbursement/status', 'App\Http\Controllers\ReimbursementController@status');
    Route::post('reimbursement/getstatus', 'App\Http\Controllers\ReimbursementController@getStatus');
    Route::get('arntransfer', 'App\Http\Controllers\ArnTransferController@index');
    Route::post('arntransfer/getarn', 'App\Http\Controllers\ArnTransferController@getARN');
    Route::post('arntransfer/transferarn', 'App\Http\Controllers\ArnTransferController@TransferARN');
    Route::match(['get', 'post'], '/nfo-scheme-rate-card','App\Http\Controllers\Distributors@getNFOSchemeRateCard');
    Route::match(['get', 'post'], '/nfo-scheme-progressbar','App\Http\Controllers\Distributors@getNFOProgress');
    Route::match(['get', 'post'], '/scheme-rate-card','App\Http\Controllers\Distributors@getSchemeRateCard');
    Route::get('InterAMCSwitch','App\Http\Controllers\AMCInterSwitchSchemeController@index');
    Route::match(['get', 'post'],'InterAMCSwitch/api/{api_url}','App\Http\Controllers\AMCInterSwitchSchemeController@api')->middleware(['getAccessToken','getAMCBearerAuthHeader']);
	Route::get('deletedusers','App\Http\Controllers\DeletedUsersController@index');
	Route::post('deletedusers','App\Http\Controllers\DeletedUsersController@deleted');
	Route::get('oldusers','App\Http\Controllers\OldUsersController@index');
	Route::post('oldusers','App\Http\Controllers\OldUsersController@old');
});

Route::get('smf/{short_code}', 'App\Http\Controllers\Smf@index')->name('smf');
Route::match(['get', 'post'], '/search-arn', 'App\Http\Controllers\Distributors@arnSearch');

Route::match(['get', 'post'], '/mos_multiplier_data', 'App\Http\Controllers\MosMultiplierDataController@index')->name('mos_multiplier_data');
Route::match(['get', 'post'], '/mos_multiplier_data_add', 'App\Http\Controllers\MosMultiplierDataController@create')->name('mos_multiplier_data_add');

Route::match(['get', 'post'], '/mos_multiplier_data_edit/{role_id?}', 'App\Http\Controllers\MosMultiplierDataController@edit')->name('mos_multiplier_data_edit');

Route::match(['get', 'post'], '/mos_multiplier_data_delete/{role_id?}', 'App\Http\Controllers\MosMultiplierDataController@delete')->name('mos_multiplier_data_delete');
Route::match(['get', 'post'], '/MasterSipStpTransactionReport', 'App\Http\Controllers\MasterSipStpTransactionDetailsController@index')->name('MasterSipStpTransactionReport');
// clearing cache by the URL
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('optimize:clear');
    $seed = Artisan::call('db:seed');
    $migration = Artisan::call('migrate');
    echo "<br/>Cache is cleared.<br/>Execute code : ".$exitCode;
    echo "<br/>DB seed.<br/>Execute code : ".$seed;
    echo "<br/>Migration.<br/>Execute code : ".$migration;
});
Route::match(['get', 'post'], '/getPredefinedSipStpReport', 'App\Http\Controllers\MasterSipStpTransactionDetailsController@getPredefinedSipStpReport')->name('getPredefinedSipStpReport');
Route::match(['get', 'post'], '/get-category-name','App\Http\Controllers\Distributors@get_category_name');
