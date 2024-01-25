<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Libraries\PhpMailer;

if(!function_exists('x')){
    function x($data,$title ='')
    {
        echo "<BR><strong> ".$title." :</strong><BR>";
        echo "<pre>";print_r($data);exit;
    }
}

if(!function_exists('y')){
    function y($data,$title ='')
    {
        echo "<BR><strong> ".$title." :</strong><BR>";
        echo "<pre>";print_r($data);
    }
}

//for sms - textlocal api || copied from Rankmf Partners

function sendSms($bulk_data)
{
    // x($bulk_data,145);
    //$apiKey = urlencode(TEXTLOCAL_API_KEY);

    if(isset($bulk_data['newOtpKey']) && ($bulk_data['newOtpKey']=="SMSOTP"))
    {
        $apiKey = urlencode(config('constants.TEXTLOCAL_API_KEY_OTP'));
    }
    else
    {
        $apiKey = urlencode(config('constants.TEXTLOCAL_API_KEY'));
    }

    $input_data=$bulk_data;
    
    // $sender = empty($input_data['sender'])?'Samcmf':$input_data['sender'];
	$sender = empty($input_data['sender'])?'RankMF':$input_data['sender'];
    if(!empty($bulk_data['bulk_sms']))
    {
        
        $sms_data=array();
        foreach ($bulk_data['merg_var'] as $row)
        {
            $message=$bulk_data['message'];
            foreach ($row as $key => $value)
            {
                $number=$row['number'];
                $message=str_replace("{".$key."}",$value ,$message);
            }
            $sms_data []=array(
                'number' => $number,
                'text' => $message
            );
            
        }
        $result=array();
        $url='https://api.textlocal.in/bulk_json/';
        $sms_data=array_chunk($sms_data, 400);
        foreach ($sms_data as $row)
        {
            $result = [];
            foreach ($row as $sms)
            {
                if($sms['number'])
                {
                    $result [] = [
                        'number' =>$sms['number'],
                        'text' => rawurlencode($sms['text'])
                    ];
                }
            }

            $data_sms=['sender' => $sender,'messages' =>$result];
            $data = array(
                'apikey' => TEXTLOCAL_API_KEY,
                'data' => json_encode($data_sms)
            );

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $insert_data=insertSmsLog($input_data,$response);
            sleep(15);
            
            
        }
        
        
    }
    else
    {   
        $numbers = $bulk_data['numbers'];
        $sender  = urlencode($sender);
        $message = rawurlencode($bulk_data['message']);
        $numbers = implode(',', $numbers);
        
        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
        $url='https://api.textlocal.in/send/';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $insert_data=insertSmsLog($input_data,$response);
        
        //return $response;
    }
    
    return $response;
    
}

function insertSmsLog($input_data,$response){
    $fetch_method =explode('@', Route::getCurrentRoute()->getActionName())[1];
    $fetch_class = class_basename(Route::current()->controller);
    $status = json_decode($response);
    // x($status);
    $for=$fetch_class." - ".$fetch_method;
    $insert_arr=array();
    $insparam =[];
    $insparam['env']        = 'db';
    $insparam['table_name'] = 'textlocal_sms_log';
    $insparam['batch']      = TRUE;
    if(!empty($input_data['bulk_sms'])){
      // print_r($status);exit;
      

      foreach($status as $msg_status=>$msg_status_arr){
        if($msg_status == 'messages'){
            foreach($msg_status_arr  as $statusk=>$statusv){
                $statusv = (array)$statusv;
                
                $insert_arr[]=array(
                    'mobile'=>substr($statusv['messages'][0]->recipient, -10),
                    'status'=> 'success',
                    'for' => $for,
                    'response_details'=>json_encode(['balance' => $statusv['balance'],'batch_id' => $statusv['batch_id'], 'num_parts'=> $statusv['message']->num_parts,'message' =>  $statusv['message']->content])
                );
                
            }
        }

        if($msg_status == 'messages_not_sent'){
            foreach($msg_status_arr  as $statusk=>$statusv){
                $statusv = (array)$statusv;
                
                $insert_arr[]=array(
                    'mobile'=>$statusv['number'],//substr($statusv['messages'][0]->recipient, -10),
                    'status'=> 'failure',
                    'for' => $for,
                    'response_details'=>json_encode(['unique_id' => $statusv['unique_id'],'message' => $statusv['message'],'error_code' => $statusv['error']->code, 'error_msg'=> $statusv['error']->message])
                );
                
            }
        }

        // x($insert_arr,1);
        if(count($insert_arr) == 1000){
            $insparam['data']       = $insert_arr;
            $CI->Partners_model->insert_table_data($insparam);
            $insert_arr = [];
        }
      }

    }else{
      if(isset($status->errors) && !empty($status->errors)){
        foreach ($input_data['numbers'] as $row) {
          $insert_arr[]=array(
              'mobile'=>$row,
              'status'=> $status->status,
              'for' => $for,
              'response_details'=>json_encode(array('error_code'=>$status->errors[0]->code,'error_msg'=>$status->errors[0]->message,'message'=> $input_data['message']))
          );
        }
      }

      if ($status->status == 'success') {
        foreach ($status->messages as $key => $value) {
          $insert_arr[]=array(
                'mobile'=>substr($value->recipient, -10),
                'status'=> $status->status,
                'for' => $for,
                'response_details'=>json_encode(array('balance'=>$status->balance,'batch_id'=>$status->batch_id,'message'=> $status->message->content))
                
            );
        }
      }
    }  
    
    if(!empty($insert_arr)){
        DB::table('textlocal_sms_log')->insert($insert_arr);
    }
    return true;
      
      // return $insert_arr;
  }

  function get_content_by_curl($url, $data, $headers = array()) {
    $userAgent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:33.0) Gecko/20100101 Firefox/33.0';
    $process = curl_init($url);
    curl_setopt($process, CURLOPT_TIMEOUT, 0);
    curl_setopt($process, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($process, CURLOPT_SAFE_UPLOAD, true);
    curl_setopt($process, CURLOPT_REFERER, env('APP_URL'));

    if (isset($headers) && is_array($headers) && count($headers) > 0) {
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
    }

    if (!empty($data)) {
        curl_setopt($process, CURLOPT_POST, true);
        curl_setopt($process, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($process, CURLOPT_USERAGENT, $userAgent);
    $contents = curl_exec($process);

    // Check if any error occurred
    if(curl_errno($process)) {
        $log_file_path = getcwd().'/storage/logs/get_content_by_curl_'. date('Y-m-d') .'.txt';
        @file_put_contents($log_file_path, PHP_EOL ."[". date('Y-m-d H:i:s') ."]\t: CURL URL: ". $url . PHP_EOL, FILE_APPEND);
        @file_put_contents($log_file_path, PHP_EOL ."[". date('Y-m-d H:i:s') ."]\t: CURL INPUT DATA: ". print_r($data, true) . PHP_EOL, FILE_APPEND);
        @file_put_contents($log_file_path, PHP_EOL ."[". date('Y-m-d H:i:s') ."]\t: CURL ERROR NUMBER: ". curl_errno($process) . PHP_EOL, FILE_APPEND);
        @file_put_contents($log_file_path, PHP_EOL ."[". date('Y-m-d H:i:s') ."]\t: CURL ERROR: ". curl_error($process) . PHP_EOL, FILE_APPEND);
        unset($log_file_path);
        $contents = json_encode(array('err_number' => curl_errno($process), 'err_text' => curl_error($process)));
    }

    // var_dump(curl_error($process));die;
    curl_close($process);

    return $contents;
}

function insertEmailLogs($insert_arr){
    if(!empty($insert_arr)){
        DB::table('email_logs')->insert($insert_arr);
    }

}

// function to get details of upcoming form status.
if(!function_exists('get_next_empanel_form_status')){
    function get_next_empanel_form_status($current_form_status = 0, $arn_individual_or_not = 1, $user_details = array()){
        // arn_individual_or_not: Possible Values are: 1 = Individual, 2 = Non Individual
        $output_arr = array('current_form_stage' => array(), 'previous_form_stage' => array(), 'next_form_stage' => array(), 'first_form_stage' => array(), 'last_form_stage' => array(), 'total_stages' => 0);

        // checking whether BR document & ASL document required or not
        $br_doc = (isset($user_details->doc_br)?$user_details->doc_br:1);
        $asl_doc = (isset($user_details->doc_asl)?$user_details->doc_asl:1);

        // retrieving list of available FORM STATUS from constant file
        $arr_empanel_form_status = config('constants.EMPANEL_FORM_STATUS');
        // checking whether ARN is of INDIVIDUAL TYPE or not
        if($arn_individual_or_not == 1){
            // if ARN is of INDIVIDUAL TYPE then skipping the steps of UPLOAD DOCUS, ADD BOARD MEMBERS & ESIGN
            $skip_form_ids = config('constants.SKIP_EMPANEL_FORM_STATUS_FOR_INDIVIDUAL_FLOW');//array(2, 5, 6);
            foreach($skip_form_ids as $skip_id){
                unset($arr_empanel_form_status[$skip_id]);
            }
            unset($skip_id, $skip_form_ids);
        }
        elseif($arn_individual_or_not == 2){
            // if ARN is of NON INDIVIDUAL TYPE then skipping the steps of NOMINEE DETAILS
            $skip_form_ids = config('constants.SKIP_EMPANEL_FORM_STATUS_FOR_NON_INDIVIDUAL_FLOW');//array(8);
            // if both BR & ASL document are not required then skipping UPLOAD DOCS form status
            if($br_doc == 0 && $asl_doc == 0){
                $skip_form_ids = array_merge($skip_form_ids, array(array_column($arr_empanel_form_status, NULL, 'url')['upload-docs']['id']));
            }
            foreach($skip_form_ids as $skip_id){
                unset($arr_empanel_form_status[$skip_id]);
            }
            unset($skip_id, $skip_form_ids);
        }

        $flag_current_stage_details_not_found = false;
        if(isset($arr_empanel_form_status[$current_form_status])){
            // if input parameter FORM STATUS is present in defined array of EMPANEL_FORM_STATUS
            // Step 01) Retrieving SEQUENCE for the input form status id
            $current_form_sequence = $arr_empanel_form_status[$current_form_status]['order'];
            $output_arr['current_form_stage'] = $arr_empanel_form_status[$current_form_status];

            // Step 02) Preparing array of FORM STATUS keeping field "order" as it's KEY
            $arr_empanel_form_status = array_column($arr_empanel_form_status, NULL, 'order');

            // Step 03) Retrieving only keys from array returned from Step (02)
            $arr_ordered_keys = array_keys($arr_empanel_form_status);

            // Step 04) Sorting the array containing only SEQUENCE of forms in ASCENDING order
            sort($arr_ordered_keys, SORT_NUMERIC);

            // Step 05) Retrieving next sequence key from a sorted array of SEQUENCE(s)
            $current_form_sequence_key = array_search($current_form_sequence, $arr_ordered_keys);

            // Step 06) Checking whether details available for the next subsequent key of "current_form_sequence_key"
            if(isset($arr_ordered_keys[($current_form_sequence_key + 1)])){
                // Step 07) as details are available then storing that SEQUENCE as "next_form_sequence"
                $next_form_sequence = $arr_ordered_keys[($current_form_sequence_key + 1)];

                // Step 08) Retrieving the details of an array based on the key "next_form_sequence"
                $output_arr['next_form_stage'] = $arr_empanel_form_status[$next_form_sequence];
            }
            else{
                // Step 09) As details were not available for next subsequent key of "current_form_sequence_key". So retrieving details of that key only and sending it across
                $output_arr['next_form_stage'] = $arr_empanel_form_status[$current_form_sequence];
            }

            // Step 10) Checking whether details available for the previous key of "current_form_sequence_key"
            if(isset($arr_ordered_keys[($current_form_sequence_key - 1)])){
                // Step 11) as details are available then storing that SEQUENCE as "previous_form_sequence"
                $previous_form_sequence = $arr_ordered_keys[($current_form_sequence_key - 1)];

                // Step 12) Retrieving the details of an array based on the key "previous_form_sequence"
                $output_arr['previous_form_stage'] = $arr_empanel_form_status[$previous_form_sequence];
            }
        }
        else{
            $flag_current_stage_details_not_found = true;
        }
        $output_arr['total_stages'] = count($arr_empanel_form_status) + 1;

        // retrieving last form stage just for reference
        // Step 01) Preparing array of FORM STATUS keeping field "order" as it's KEY
        $arr_empanel_form_status = array_column(config('constants.EMPANEL_FORM_STATUS'), NULL, 'order');

        // Step 02) Retrieving only keys from array returned from Step (01)
        $arr_ordered_keys = array_keys($arr_empanel_form_status);

        // Step 03) Sorting the array containing only SEQUENCE of forms in ASCENDING order
        sort($arr_ordered_keys, SORT_NUMERIC);

        // Step 04) Retrieving last SEQUENCE from a sorted array of SEQUENCE(s)
        $last_form_sequence = array_pop($arr_ordered_keys);

        // Step 05) Retrieving the details of an array based on the key "last_form_sequence"
        $output_arr['last_form_stage'] = $arr_empanel_form_status[$last_form_sequence];
        if($flag_current_stage_details_not_found){
            // as input parameter FORM STATUS is not present in defined array of EMPANEL_FORM_STATUS
            $output_arr['next_form_stage'] = $output_arr['last_form_stage'];
        }

        // Step 06) Retrieving first SEQUENCE from a sorted array of SEQUENCE(s)
        $first_form_sequence = $arr_ordered_keys[0];

        // Step 07) Retrieving the details of an array based on the "first_form_sequence"
        $output_arr['first_form_stage'] = $arr_empanel_form_status[$first_form_sequence];
        if($flag_current_stage_details_not_found){
            // as input parameter FORM STATUS is not present in defined array of EMPANEL_FORM_STATUS
            $output_arr['previous_form_stage'] = $output_arr['first_form_stage'];
        }
        return $output_arr;
    }
}
// function to get storage folder URL.
if(!function_exists('get_storage_folder_url')){
    function get_storage_folder_url($input_folder_path){
        return str_replace(array('app/public/'), array('/storage/'), $input_folder_path);
    }
}
// function to get document root from web browser.
if(!function_exists('get_server_document_root')){
    function get_server_document_root($remove_public_folder=false){
        if($remove_public_folder){
            // retrieving only SERVER document root and replacing the public folder from path
            return str_replace('/public', '', $_SERVER['DOCUMENT_ROOT']);
        }
        else{
            // retrieving only SERVER document root
            return $_SERVER['DOCUMENT_ROOT'];
        }
    }
}
// function to show date in proper display format.
if(!function_exists('show_date_in_display_format')){
    function show_date_in_display_format($date_format, $input_date){
        if(!empty($input_date) && strtotime($input_date) !== FALSE){
            return date($date_format, strtotime($input_date));
        }
        else{
            return $input_date;
        }
    }
}
// function to send an email and enter it's details in DB table.
if(!function_exists('sendEmailNotification')){
    function sendEmailNotification($input_arr = array()){
        $err_flag = 0;              // err_flag is 0 means no error
        $err_msg = array();         // err_msg stores list of errors found during execution
        $response_message = '';
        extract($input_arr);

        if(!isset($email_template_id) || empty($email_template_id)){
            $err_flag = 1;
            $err_msg[] = 'Email template details not found';
        }

        if(!isset($email_id)){
            $err_flag = 1;
            $err_msg[] = 'Email id is required';
        }
        elseif(!is_array($email_id)){
            $email_id = (array) $email_id;
        }

        if(!isset($email_for) || empty($email_for)){
            $err_flag = 1;
            $err_msg[] = 'Parameter email for is not found';
        }

        if(!isset($email_from_id)){
            $email_from_id = '';
        }

        if($err_flag == 0){
            $mailer = new PhpMailer();
            $mail_data = array();
            $params['templateName'] = $email_template_id;
            $params['channel'] = $email_template_id;
            $params['to'] = array($email_id);

            if(isset($from_name) && !empty($from_name)){
                $params['from_name'] = trim(strip_tags($from_name));
            }

            if(isset($subject) && !empty($subject)){
                $params['subject'] = trim(strip_tags($subject));
            }

            if(isset($reply_to) && !empty($reply_to) && filter_var($reply_to, FILTER_VALIDATE_EMAIL) !== FALSE){
                $params['reply_to'] = trim(strip_tags($reply_to));
            }

            if(isset($parameters) && is_array($parameters) && count($parameters) > 0){
                $params['merge_vars'] = $parameters;
            }
            // $mailer->mandrill_send($params);
            $mailer->mandrill_send($params, '', $email_from_id);
            // save email logs
            $insert_arr = [];
            $insert_arr[] = array(
                'email' => $input_arr['email_id'],
                'data' => json_encode($params),
                'for' => $email_for,
                'created_at' => date('Y-m-d H:i:s')
            );
            insertEmailLogs($insert_arr);
            $response_message = 'email sent';
        }
        return array('err_flag' => $err_flag, 'err_msg' => $err_msg, 'response' => $response_message);
    }
}


// function to call encrypt and decrypt
if(!function_exists('encrypt_decrypt')){
    function encrypt_decrypt($action, $string) {
        $output = false;

        // hash
        $key = hash('sha256', config('constants.ENCRYPT_SECRET_KEY'));

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', config('constants.ENCRYPT_SECRET_IV')), 0, 16);

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, config('constants.ENCRYPT_METHOD'), $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), config('constants.ENCRYPT_METHOD'), $key, 0, $iv);
        }

        return $output;
    }
}

// function to get the short url
if(!function_exists('GetShortUrl')){
    function GetShortUrl($url,$domain,$description){
       // Prepare data for POST request
	   if(is_null($domain)){
			$domain = '';
	   }
       $domain = rtrim($domain,"/");
       $posted_data = array('url_long' => $url, 'short_url_domain' => $domain,'description' => $description);
       $url=env('SAMCOMF_ADMIN_URL')."/api/create-short-url";

       $retrieved_data = get_content_by_curl($url, $posted_data);  
       return $retrieved_data;     
    }
}


// function to get samcomf fund record
if(!function_exists('getSettingsTableValue')){
    function getSettingsTableValue($inputKeyString, $conn = false){

		$setting_tables = [
			'rankmf' => 'mf_settings',
			'partner-rankmf' => 'mfp_partner_setting',
		];

        $record_value = '';
		$con_obj = DB::table('settings');
		if(is_string($conn) && !empty($conn)){
			$con_obj = DB::connection($conn)->table($setting_tables[$conn]);
		}
        $settings_records = $con_obj->where('key', $inputKeyString)->get();
        if(!$settings_records->isEmpty() && isset($settings_records[0]) && is_object($settings_records) && get_object_vars($settings_records) > 0){
          $record_value = $settings_records[0]->value;
        }
        unset($settings_records);
        return $record_value;
    }
}

// function to get years 
if(!function_exists('getListofYears')){
    function getListofYears(){
        $arr_list_of_years = array();
        $start_year = 2018;
        $current_year = date("Y");
        for($looping_year = $start_year;$looping_year <= $current_year;$looping_year++){
            $arr_list_of_years[] = $looping_year;
        }
        return $arr_list_of_years;
    }
}

// function to create SEO friendly URL
if(!function_exists('create_slug')){
    function create_slug($string, $replacing_character='-'){
       $slug = preg_replace('/[^A-Za-z0-9-]+/', $replacing_character, $string);
       return $slug;
    }
}

/**
 * A PHP function that will calculate the median value
 * of an array
 *
 * @param array $arr The array that you want to get the median value of.
 * @return boolean|float|int
 * @throws Exception If it's not an array
 */
if(!function_exists('getMedian')){
    function getMedian($arr) {
        // Make sure it's an array.
        if(!is_array($arr)){
            throw new Exception('$arr must be an array!');
        }
        // If it's an empty array, return FALSE.
        if(empty($arr)){
            return false;
        }
        // Sorting array elements into an ASCENDING order
        sort($arr);
        // Count how many elements are in the array.
        $num = count($arr);
        // Determine the middle value of the array.
        $middleVal = floor(($num - 1) / 2);
        // If the size of the array is an odd number,
        // then the middle value is the median.
        if($num % 2) {
            return $arr[$middleVal];
        }
        // If the size of the array is an even number, then we
        // have to get the two middle values and get their
        // average
        else {
            // The $middleVal var will be the low
            // end of the middle
            $lowMid = $arr[$middleVal];
            $highMid = $arr[$middleVal + 1];
            // Return the average of the low and high.
            return (($lowMid + $highMid) / 2);
        }
    }
}

// function to check whether input string is a valid JSON or not.
if(!function_exists('isJson')){
    function isJson($string) {
       json_decode($string);
       return json_last_error() === JSON_ERROR_NONE;
    }
}

if(!function_exists('get_financial_year_range')){
    function get_financial_year_range($year = '', $month = '') {
        if(empty($year)){
           $year = date('Y');
        }
        if(empty($month)){
           $month = date('m');
        }
        if($month<4){
            $year = $year-1;
        }
        $start_date = date('Y-m-d',strtotime(($year).'-04-01'));
        $end_date = date('Y-m-d',strtotime(($year+1).'-03-31'));
        $quarter_start_date = '';
        if($month <= 3){
            $quarter_start_date = ($year+1) .'-01-01';
        }
        elseif($month > 3 && $month <= 6){
            $quarter_start_date = $year .'-04-01';
        }
        elseif($month > 6 && $month <= 9){
            $quarter_start_date = $year .'-07-01';
        }
        elseif($month > 9 && $month <= 12){
            $quarter_start_date = $year .'-10-01';
        }
        $response = array('start_date' => $start_date, 'end_date' => $end_date, 'quarter_start_date' => $quarter_start_date);
        return $response;
    }
}

if(!function_exists('get_rankmf_data_by_curl_query')){
	function get_rankmf_data_by_curl_query($data) {

		$url      = env('RANKMF_URL').'/Mf_datafetch_api/execute_query';
		$userAgent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:33.0) Gecko/20100101 Firefox/33.0';
		$process = curl_init($url);
		curl_setopt($process, CURLOPT_TIMEOUT, 0);
		curl_setopt($process, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);

		if (!empty($data)) {
			curl_setopt($process, CURLOPT_POST, true);
			$fields_string = json_encode($data);
			curl_setopt($process, CURLOPT_POSTFIELDS, array('query_data' => $fields_string));
		}
		curl_setopt($process, CURLOPT_USERAGENT, $userAgent);
		$contents = curl_exec($process);
		
		curl_close($process);

		return $contents;
	}
}

if(!function_exists('get_curl_call')){

	function get_curl_call($url, $post = [],$headers = [],$ignoressl = false) {
		
		//$cert = base_path('storage/cacert.pem');

		// if(!file_exists($cert)){
		// 	$f = file_get_contents("https://curl.haxx.se/ca/cacert.pem");
		// 	file_put_contents($cert, $f);
		// }

		//$segments = parse_url($url);

		//$host = preg_replace("/[^a-zA-Z0-9]+/", "", $segments['host']);

		//$coockie_file = session('curl_cookies.'.$host);

		// if(empty($coockie_file) || !file_exists($coockie_file)){
		// 	$coockie_file = tempnam(sys_get_temp_dir(),'drm_');
		// 	session(['curl_cookies.'.$host => $coockie_file]);
		// }
		if(preg_match('/application\/json/is', serialize($headers))){
			$post = json_encode($post);
		}else{
			$post = http_build_query($post);
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:33.0) Gecko/20100101 Firefox/33.0',
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			//CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $post,
			//Loading SSL Certificate
			//CURLOPT_CAINFO => $cert,
			//CURLOPT_CAPATH => $cert,
			//Fething header details as well
			//CURLOPT_HEADER => true,
			CURLOPT_COOKIESESSION => true,
			//CURLOPT_COOKIEJAR => $coockie_file,
			//CURLOPT_COOKIEFILE => $coockie_file,
			//CURLOPT_VERBOSE => true,
		));
		
		//curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

		//to ignore ssl check
		if($ignoressl){
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		}

		if(count($headers) > 0){
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}

		$response = curl_exec($curl);
		
		if($response == false){
			$response = json_encode(curl_error($curl));
		}

		return $response;

		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

		$header = substr($response, 0, $header_size);

		curl_close($curl);

		return trim(substr($response, $header_size));
	}
}

function replaceArray($search, array $replace, $subject)
{
    $segments = explode($search, $subject);

    $result = array_shift($segments);

    foreach ($segments as $segment) {
        $result .= (array_shift($replace) ?? $search).$segment;
    }

    return $result;
}

Illuminate\Database\Query\Builder::macro(
	'getRankMFCurl',
	function () {
		$params = [];
		$params["env"] = "db";
		$sql_str = $this->toSQL();
        $sql_str = str_replace('?', "'?'", $sql_str);
        $bindings = $this->getBindings();
        $params["query"] = replaceArray('?', $bindings, $sql_str);
		return json_decode(get_rankmf_data_by_curl_query($params));
	}
);

function encrypt_decrypt($action, $string) {
	$output = false;

// hash
	$key = hash('sha256', env('ENCRYPT_SECRET_KEY'));

// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	$iv = substr(hash('sha256', env('ENCRYPT_SECRET_IV')), 0, 16);

	if ($action == 'encrypt') {
		$output = openssl_encrypt($string, env('ENCRYPT_METHOD'), $key, 0, $iv);
		$output = base64_encode($output);
	} else if ($action == 'decrypt') {
		$output = openssl_decrypt(base64_decode($string), env('ENCRYPT_METHOD'), $key, 0, $iv);
	}

	return $output;
};

function pinDistance($latlong1, $latlong2) {

	$lat1 = $latlong1->lat;
	$lon1 = $latlong1->lng;

	$lat2 = $latlong2->lat;
	$lon2 = $latlong2->lng;

    // The radius of the Earth in kilometers
    $radius = 6371;

    // Convert latitude and longitude from degrees to radians
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    // Haversine formula
    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;
    $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
    $c = 2 * asin(sqrt($a));

    // Calculate the distance
    $distance = $radius * $c;

    return $distance;
}

function strrep($str){
	$replace = [
		"\r" => ' ',
		"\n" => ' ',
		'\\' => '\\\'',
	];
	return strtr($str,$replace);
}

Illuminate\Database\Query\Builder::macro(
	'toSql2',
	function () {
		$sql_str = $this->toSQL();
        $sql_str = str_replace('?', "'?'", $sql_str);
        $bindings = $this->getBindings();
        return replaceArray('?', $bindings, $sql_str);
	}
);

if (!function_exists('getSettingsTableYear')) {
    function getSettingsTableYear()
    {
        $arr_list_of_years = [];
        $settings_record = DB::table('settings')->where('key', 'MIN_YEAR')->first();

        if (!empty($settings_record) && is_object($settings_record) && property_exists($settings_record, 'value')) {
            $start_year = $settings_record->value;
            $current_year = date("Y");

            for ($looping_year = $start_year; $looping_year <= $current_year; $looping_year++) {
                $arr_list_of_years[] = $looping_year;
            }
        }

        return $arr_list_of_years;
    }
}

if (!function_exists('RelativeTime')) {
	function RelativeTime($ts)
	{
		if(!ctype_digit($ts)){
			$ts = strtotime($ts);
		}

		$time = time();

		if(intval(date('His',$ts)) == 0){
			$time = strtotime(date('Y-m-d',$time));
		}

		$diff = $time - $ts;
		if($diff == 0){
			return 'now';
		}
		
		elseif($diff > 0)
		{
			$day_diff = floor($diff / 86400);
			if($day_diff == 0)
			{
				if($diff < 60) return 'just now';
				if($diff < 120) return '1 minute ago';
				if($diff < 3600) return floor($diff / 60) . ' minutes ago';
				if($diff < 7200) return '1 hour ago';
				if($diff < 86400) return floor($diff / 3600) . ' hours ago';
			}
			if($day_diff == 1) return 'Yesterday';
			if($day_diff < 7) return $day_diff . ' days ago';
			if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
			if($day_diff < 60) return 'last month';
			return date('F Y', $ts);
		}
		else
		{
			$diff = abs($diff);
			$day_diff = floor($diff / 86400);
			if($day_diff == 0)
			{
				if($diff < 120) return 'in a minute';
				if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
				if($diff < 7200) return 'in an hour';
				if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
			}
			if($day_diff == 1) return 'Tomorrow';
			if($day_diff < 4) return date('l', $ts);
			if($day_diff < 7 + (7 - date('w'))) return 'next week';
			if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
			if(date('n', $ts) == date('n') + 1) return 'next month';
			return date('F Y', $ts);
		}
	}
}

