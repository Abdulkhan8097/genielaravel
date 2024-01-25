<?php 
/*
    |--------------------------------------------------------------------------
    | Custom file created for constant BY- Dharmesh
    |--------------------------------------------------------------------------
    |
    */
    return [
        'cdn_url' => env('CDN_URL'), // using partner live  https://partners.rankmf.com
        'SALT' => 'SamCo@ZonSoft',
        'FORM_STATUS' => array('1'=>'pan','2'=>'personal','3'=>'communication','4'=>'mobile-verification', '5'=>'email-verification','6'=>'bank','7'=>'arn','8'=>'upload','9'=>'thankyou'),
        'SAMCOMF' => env('APP_URL'),
        'SAMCO_CORE_API' => 'https://core-api.samco.in/',
        'SAMCO_CORE_API_UAT' => 'https://uat-core-api.samco.in/',
        'ADDRESS_TYPE' => array('Passport' => 'Passport', 'Aadhar card' => 'Aadhar card', 'Driving Licence' => 'Driving Licence', 'Voter Id' => 'Voter Id', 'Latest Bank Account Statement' => 'Latest Bank Account Statement', 'Electricity / Gas / Telephone Bill' => 'Electricity / Gas / Telephone Bill'),
        // 'TEXTLOCAL_API_KEY_OTP' => 'NTk0ZDQ4NDI2ZTQzNjgzNTZkNzg0ZjYxNzk2OTQ1NGU=',
        'TEXTLOCAL_API_KEY' => 'xznOy/ClCPE-yOAWzhlnQfocITXZPS1shpeX3hQixS',
        'POOLVALUE' => '&poolName=API',
        'EMPANEL_FORM_STATUS' => array('1' => array('id' => '1', 'url' => 'verify', 'order' => 1, 'label' => 'Verification'),
                                       '9' => array('id' => '9', 'url' => 'upload-arn', 'order' => 2, 'label' => 'Upload ARN'),
                                       '7' => array('id' => '7', 'url' => 'bank-details', 'order' => 3, 'label' => 'Add Bank Details'),
                                       '8' => array('id' => '8', 'url' => 'nominee-details', 'order' => 4, 'label' => 'Nominee Details'),
                                       '2' => array('id' => '2', 'url' => 'upload-docs', 'order' => 5, 'label' => 'Upload Documents'),
                                       '5' => array('id' => '5', 'url' => 'add-authorised-signatories', 'order' => 6, 'label' => 'Add Signatories'),
                                       '6' => array('id' => '6', 'url' => 'authorised-signatories-esign', 'order' => 7, 'label' => 'E-sign & Verify'),
                                       '3' => array('id' => '3', 'url' => 'consent', 'order' => 8, 'label' => 'Consent'),
                                       '4' => array('id' => '4', 'url' => 'thankyou', 'order' => 9, 'label' => 'Thank You')),
        'ARN_RECORD_STATUS' => array('Created', 'Approved', 'Activated', 'Deactivated'),
        'BR_FILE_UPLOAD_FOLDER' => 'app/public/partner-documents/br/',
        'ASL_FILE_UPLOAD_FOLDER' => 'app/public/partner-documents/asl/',
        'ARN_VISITING_CARD_IMAGES' => 'app/public/arn_visiting_card_images/',
        'SIGNED_DOCUMENTS_FILE_UPLOAD_FOLDER' => 'app/public/partner-documents/signed-documents/',
        'UPLOAD_DOC_TYPE_ARRAY' => array('1' => 'Cheque Copy', '2' => 'Board Resolution (BR)', '3' => 'Authorzied Signatory (ASL)'),
        'Video_Language' => array(
			'Hindi' => 'hindi',
			'English' => 'english',
			'Bengali' => 'bengali',
			'Gujarati' => 'gujarati',
			'Kannada' => 'kannada',
			'Malayalam' => 'malayalam',
			'Tamil' => 'tamil',
			'Telugu' => 'telugu'
		),
		'MEETING_MODE' => array(
			'Phone Call' => 'Phone Call',
			'In Person Meeting' => 'In Person Meeting',
			'Virtual Meeting' => 'Virtual Meeting',
			'Other' => 'Other',
		),
		'MEETING_PURPOSE' => array(
			'Product Pitch' => 'Product Pitch',
			'Market Info Sharing' => 'Market Info Sharing',
			'Pricing Negotiation' => 'Pricing Negotiation',
			'Distributor Training Event' => 'Distributor Training Event',
			'Relationship Building' => 'Relationship Building',
			'Group Training Event' => 'Group Training Event',
			'Business Planning' => 'Business Planning',
			'Business Review' => 'Business Review',
			'Product Training' => 'Product Training',
			'Servicing' => 'Servicing',
			'Training Workshop' => 'Training Workshop',
			'Sales Review' => 'Sales Review',
			'Other' => 'Other',
		),
        'FREQUENCY' => array(
                              array('key' => 'M', 'value' => 'Monthly'),
                              array('key' => 'Q', 'value' => 'Quaterly'),
                              array('key' => 'D', 'value' => 'Daily'),
                              array('key' => 'H', 'value' => 'Half Yearly'),
                              array('key' => 'W', 'value' => 'Weekly'),
                              array('key' => 'F', 'value' => 'Fortnightly'),
                            ),
        'ENCRYPT_METHOD' => 'AES-256-CBC',
        'ENCRYPT_SECRET_KEY' => '$SAMCO-ITL$',
        'ENCRYPT_SECRET_IV' => '$ITL-SAMCO$',
    ];
