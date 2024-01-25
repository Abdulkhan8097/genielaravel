<?php
namespace App\Libraries;

class PhpMailer{
    private $apiKey   = "8475390EFDD4C1E781B61E4AA6F99A478AA6F65FEBC80D2D1FDB9ACA36DBFBBAA8DEBCE28BAF4F64CF3AFBF874F5D60D";
    private $userName = "";
    public function __construct($userName=NULL,$apiKey=NULL)
    {
        if($userName != NULL) $this->userName = $userName;
        if($apiKey != NULL)   $this->apiKey = $apiKey;
    }

    public function getParams($params){
        echo $this->apiKey;
        return $params;
    }

    function uploadAttachment($filepath, $filename)
    {
        $data = http_build_query(array('username' => urlencode($this->userName),'api_key' => urlencode($this->apiKey),'file' => urlencode($filename)));
        $file = file_get_contents($filepath);
        $result = ''; 

        $fp = fsockopen('ssl://api.elasticemail.com', 443, $errno, $errstr, 30);   

        if ($fp){
            fputs($fp, "PUT /attachments/upload?".$data." HTTP/1.1\r\n");
            fputs($fp, "Host: api.elasticemail.com\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: ". strlen($file) ."\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $file);
            while(!feof($fp)) {
                $result .= fgets($fp, 128);
            }
        } else { 
            return array(
                'status'=>false,
                'error'=>$errstr.'('.$errno.')',
                'result'=>$result);
        }
        fclose($fp);
        $result = explode("\r\n\r\n", $result, 2); 
        // return array(
        //     'status' => true,
        //     'attachId' => isset($result[1]) ? $result[1] : ''
        // );
        return ((isset($result[1])) ? $result[1] : '');
    }

    function sendElasticEmail($to, $subject, $body_text, $body_html, $from, $fromName, $attachments)
    {

        global $username, $apikey;

        $res = "";

        $data = "username=".$username;
        $data .= "&poolName=Default&api_key=".$apikey;
        $data .= "&from=".urlencode($from);
        $data .= "&from_name=".urlencode($fromName);
        $data .= "&to=".urlencode($to);
        $data .= "&subject=".urlencode($subject);
        if($body_html)
          $data .= "&body_html=".urlencode($body_html);
        if($body_text)
          $data .= "&body_text=".urlencode($body_text);

        if($attachments)
          $data .= "&attachments=".urlencode($attachments);

        $header = "POST /mailer/send HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($data) . "\r\n\r\n";
        $fp = fsockopen('ssl://api.elasticemail.com', 443, $errno, $errstr, 30);

        if(!$fp)
          return "ERROR. Could not open connection";
        else {
          fputs ($fp, $header.$data);
          while (!feof($fp)) {
            $res .= fread ($fp, 1024);
          }
          fclose($fp);
        }
        return $res;                  
    }

    /**
	 * Sending simple email
	 * @param string $from
	 * @param string $fromName
	 * @param string $to
	 * @param string $subject
	 * @param string $bodyText
	 * @param string $bodyHTML
	 */
	function mandrill_send($params,$email_type='',$email_from_id='')
	{
        if(empty($email_from_id)){
            $email_from_id = 'alerts@samcomf.com';
        }

        $res = "";
        $data = "username=".urlencode($this->userName);
        if(isset($params['pool_name']) && $params['pool_name'] == 'transactional'){
            $data = "&poolName=transactional&api_key=".urlencode($this->apiKey);
        }else{
            $data .= "&poolName=Default&api_key=".urlencode($this->apiKey);
        }
        

	    $data .= "&from=".urlencode($email_from_id);
        $data .= "&isTransactional=".TRUE;
        //$data .= "&from=".urlencode($params['from'][0]);
        if(isset($params['from_name']) && !empty($params['from_name'])){
            $data .= "&from_name=".urlencode($params['from_name']);
        }else{
            $data .= "&from_name=".urlencode("Samco Mutual Fund");
        }
        if(isset($params['reply_to']) && !empty($params['reply_to'])){
            $data .= "&reply_to=".urlencode($params['reply_to']);
        }
        foreach($params['to'] as $to_arr)
        {
            $email_ids[] = $to_arr[0];
        }
            
        $semi_colon_seperated_to_emails = implode(';',$email_ids); 
        $data .= "&to=".urlencode($semi_colon_seperated_to_emails);
        $data .= "&subject=".urlencode($params['subject'] ?? '');
            

        if(!empty($params['templateName'])) 
        {
            $data .= "&template=".$params['templateName'];
        }
        else
        {
            if($params['message']) $data .= "&body_html=".urlencode($params['message']);
            if($params['bodyText']) $data .= "&body_text=".urlencode($params['bodyText']);

        }

        if(!empty($params['channel'])) 
        {
            $data .= "&channel=".urlencode($params['channel']);
        }

        if(!empty($params['merge_vars'])) 
        {
            foreach ($params['merge_vars'] as $key => $value) {
                $data .= "&merge_".$key."=".urlencode($value);
            }
        }

        if(!empty($params['attachment']))
        {
            $attachIDs = [];
            foreach($params['attachment'] as $attachment)
            {
                $attachment_explode = explode('/',$attachment[0]);
                $filename = $attachment_explode[count($attachment_explode)-1];
                $attachIDs[] = $this->uploadAttachment($attachment[0], $filename);
            }
            $attachIDList = implode(';', $attachIDs);
            $data .= "&attachments=".urlencode($attachIDList);
        }
            
        $header = "POST /mailer/send HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($data) . "\r\n\r\n";
        $fp = @fsockopen('ssl://api.elasticemail.com', 443, $errno, $errstr, 30);
        if(!$fp)
        {
            return "ERROR. Could not open connection";
        }
        else
        {
            fputs ($fp, $header.$data);
            while (!feof($fp))
            {
                    $res .= fread ($fp, 1024);
            }
            fclose($fp);
        }
        return $res;
	}

    // send otp mail
    function mandrill_send_otp($params,$email_type='')
    {
        $res = "";
        $this->apiKey = '8475390EFDD4C1E781B61E4AA6F99A478AA6F65FEBC80D2D1FDB9ACA36DBFBBAA8DEBCE28BAF4F64CF3AFBF874F5D60D';

        // $data = "username=".urlencode($this->userName);
        if(isset($params['pool_name']) && $params['pool_name'] == 'transactional'){
            $data = "&poolName=transactional&api_key=".urlencode($this->apiKey);
        }else{
            $data = "&poolName=Default&api_key=".urlencode($this->apiKey);
        }
        //$data .= "&reply_to=".urlencode($params['from'][0]);
        
        if(isset($params['from_email']) && !empty($params['from_email'])){
            $data .= "&from=".urlencode($params['from_email']);
        }else{
            $data .= "&from=".urlencode("alerts@samcomf.com");
        }
        
        if(isset($params['from_name']) && !empty($params['from_name'])){
            $data .= "&from=".urlencode($params['from_name']);
        }else{
            $data .= "&from_name=".urlencode("Samco Mutual Fund");
        }
        $data .= "&isTransactional=".TRUE;
        
        foreach($params['to'] as $to_arr)
        {
            $email_ids[] = $to_arr[0];
        }
        
        $semi_colon_seperated_to_emails = implode(';',$email_ids); 
        $data .= "&to=".urlencode($semi_colon_seperated_to_emails);
        $data .= "&subject=".urlencode($params['subject'] ?? '');
        

        if(!empty($params['templateName'])) 
        {
            $data .= "&template=".$params['templateName'];
        }
        else
        {
            if($params['message']) $data .= "&body_html=".urlencode($params['message']);
            if($params['bodyText']) $data .= "&body_text=".urlencode($params['bodyText']);

        }

        if(!empty($params['channel'])) 
        {
            $data .= "&channel=".urlencode($params['channel']);
        }

        if(!empty($params['merge_vars'])) 
        {
            foreach ($params['merge_vars'] as $key => $value) {
                $data .= "&merge_".$key."=".urlencode($value);
            }
        }

        if(!empty($params['attachment']))
        {
            $attachIDs = [];
            foreach($params['attachment'] as $attachment)
            {
                $attachment_explode = explode('/',$attachment[0]);
                $filename = $attachment_explode[count($attachment_explode)-1];
                $attachIDs[] = $this->uploadAttachment($attachment[0], $filename);
            }
            
            $attachIDList = implode(';', $attachIDs);
            $data .= "&attachments=".urlencode($attachIDList);
        }
        // x($data);
        $header = "POST /mailer/send HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($data) . "\r\n\r\n";
        $fp = @fsockopen('ssl://api.elasticemail.com', 443, $errno, $errstr, 30);
        if(!$fp)
        {
                return "ERROR. Could not open connection";
        }
        else
        {
                fputs ($fp, $header.$data);
                while (!feof($fp))
                {
                        $res .= fread ($fp, 1024);
                }
                fclose($fp);
        }
        return $res;
    }

    /**
	 * Sending emails with Elastic MailMerge
	 * @param string $csv Content of the CSV File to send
	 * @param string $from
	 * @param string $fromName
	 * @param string $subject
	 * @param string $bodyText
	 * @param string $bodyHTML
	 */
	function mailMerge($params,$email_type='')
	{
        //$csvName = $params['csvName']; //mailmerge.csv
        $attachID = $this->uploadAttachment($params['csvFilePath'], $params['csvFileName']);
        
        $res = "";
        $data = "username=".urlencode($this->userName);
        $data .= "&poolName=Default&api_key=".urlencode($this->apiKey);

        $data .= "&from=".urlencode("alerts@samcomf.com");
        $data .= "&from_name=".urlencode("Samco Mutual Fund");
        $data .= "&isTransactional=".TRUE;

        $data .= "&subject=".urlencode($params['subject']);
        $data .= "&data_source=".urlencode($attachID);
        if(!empty($params['bodyHTML'])) 
            $data .= "&body_html=".urlencode($params['bodyHTML']);
        if(!empty($params['bodyText'])) 
            $data .= "&body_text=".urlencode($params['bodyText']);
        if(!empty($params['channel'])) 
            $data .= "&channel=".urlencode($params['channel']);            

        $data .= "&template=".$params['templateName'];

        if(!empty($params['attachments']))
        {
            $attachIDs = [];
            foreach($params['attachments'] as $attachment)
            {
                $attachIDs[] = $this->uploadAttachment($attachment['filePath'], $attachment['fileName']);
            }
            
            $attachIDList = implode(';', $attachIDs);
            $data .= "&attachments=".urlencode($attachIDList);
        }
        
        $header = "POST /mailer/send HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($data) . "\r\n\r\n";
        $fp = @fsockopen('ssl://api.elasticemail.com', 443, $errno, $errstr, 30);
        if(!$fp)
        {
            return "ERROR. Could not open connection";
        }
        else
        {
            fputs ($fp, $header.$data);
            while (!feof($fp))
            {
                    $res .= fread ($fp, 1024);
            }
            fclose($fp);
        }
        
        return $res;
	}

    function get_array_column($array, $field)
    {
        $arr = array();
        if (!empty($array)) {
            foreach ($array as $k => $v) {
                $arr[] = $v[$field];
            }
            return $arr;
        } else {
            return $arr;
        }
    }
    /**
     * Sending simple email
     * @param string $from
     * @param string $fromName
     * @param string $to
     * @param string $subject
     * @param string $bodyText
     * @param string $bodyHTML
     */
    function mandrill_send_admin($params,$email_type='')
    {
        $res = "";
        $data = "username=".urlencode($this->userName);
        $data .= config('constants.POOLVALUE')."&api_key=".urlencode($this->apiKey);

        if(stripos($params['from'][0], 'yahoo') !== FALSE){
            $data .= "&reply_to=".urlencode($params['from'][0]);
            $data .= "&from=".urlencode('mfoperation@samco.in');
            $data .= "&isTransactional=".TRUE; 
        }
        else
        {
            $data .= "&reply_to=".urlencode($params['from'][0]);
            $data .= "&from=".urlencode('mfoperation@samco.in');
            $data .= "&isTransactional=".TRUE;

        }
        //$data .= "&from=".urlencode($params['from'][0]);
        $data .= "&from_name=".urlencode($params['from'][1]);
        foreach($params['to'] as $to_arr)
        {
            $email_ids[] = $to_arr[0];
        }
        
        $semi_colon_seperated_to_emails = implode(';',$email_ids); 
        $data .= "&to=".urlencode($semi_colon_seperated_to_emails);
        $data .= "&subject=".urlencode($params['subject']);
        
        if(!empty($params['templateName'])) 
        {
            $data .= "&template=".$params['templateName'];
        }
        else
        {
            if($params['message']) $data .= "&body_html=".urlencode($params['message']);
            if($params['bodyText']) $data .= "&body_text=".urlencode($params['bodyText']);

        }

        if(!empty($params['channel'])) 
        {
            $data .= "&channel=".urlencode($params['channel']);
        }

        if(!empty($params['merge_vars'])) 
        {
            foreach ($params['merge_vars'] as $key => $value) {
                $data .= "&merge_".$key."=".urlencode($value);
            }
        }

        if(!empty($params['attachment']))
        {
            $attachIDs = [];
            foreach($params['attachment'] as $attachment)
            {
                $attachment_explode = explode('/',$attachment[0]);
                $filename = $attachment_explode[count($attachment_explode)-1];
                $attachIDs[] = $this->uploadAttachment($attachment[0], $filename);
            }
            
            $attachIDList = implode(';', $attachIDs);
            $data .= "&attachments=".urlencode($attachIDList);
        }
        
        $header = "POST /mailer/send HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($data) . "\r\n\r\n";
        $fp = @fsockopen('ssl://api.elasticemail.com', 443, $errno, $errstr, 30);
        if(!$fp)
        {
                return "ERROR. Could not open connection";
        }
        else
        {
            fputs ($fp, $header.$data);
            while (!feof($fp))
            {
                    $res .= fread ($fp, 1024);
            }
            fclose($fp);
        }
        return $res;
    }

}