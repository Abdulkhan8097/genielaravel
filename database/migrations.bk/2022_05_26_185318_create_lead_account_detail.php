<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadAccountDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('invdb')
        ->statement("CREATE TABLE `lead_account_detail` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Serial ID',
 `investor_id` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Investor ID',
 `ekyc` tinyint(1) DEFAULT NULL COMMENT 'KYC done from: 0 = Physical form submission, 1 = esign',
 `sign` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = No, 1 = e-sign, 2=Aadhar e-sign',
 `sign_time` datetime DEFAULT NULL COMMENT 'sign upload time',
 `video` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = No, 1 = yes',
 `video_time` datetime DEFAULT NULL COMMENT 'video upload time',
 `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FULLNAME',
 `pan_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Title as per PAN',
 `pan_firstname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'First name as per PAN',
 `pan_middlename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Middle name as per PAN',
 `pan_lastname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Last name as per PAN',
 `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email ID',
 `email_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Email verified: 0 = No, 1 = Yes',
 `mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mobile',
 `mobile_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Mobile verified: 0 = No, 1 = Yes',
 `pan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'PAN card number',
 `dob` date DEFAULT NULL COMMENT 'Date of birth',
 `gender` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Gender',
 `marital_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Marital Status',
 `birth_place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Place of birth',
 `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Country of birth',
 `nationality` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nationality of an investor',
 `residential_status` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Residential Status',
 `tax_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tax category',
 `tax_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tax status',
 `politically_exposed_person` tinyint(1) DEFAULT NULL COMMENT 'Is investor a politically exposed person (PEP)?0=no, 1=yes',
 `related_to_politically_exposed_person` tinyint(1) DEFAULT NULL COMMENT 'Is related to a politically exposed person?0=no, 1=yes',
 `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Occupation',
 `income_range` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Income range. E.G: Below 1 Lakh, 1 Lakh - 3 Lakh etc.',
 `net_worth_amount` decimal(25,2) DEFAULT NULL COMMENT 'net_worth_amount as on date',
 `pay_tax_in_other_country` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Pay tax in country other than India: 0 = No, 1 = Yes',
 `login_pin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'login pin',
 `terms_accepted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Terms and conditions accepted by user: 0 = No, 1 = Yes',
 `whatsapp_optin` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whatsapp message opt in: 0 = No, 1 = Yes',
 `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Status: 0 = Inactive, 1 = Active',
 `active_date` datetime DEFAULT NULL,
 `is_pan_upload` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Pan card upload required: 0 = No, 1 = Yes',
 `is_address_proof_upload` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Address proof upload required: 0 = No, 1 = Yes',
 `is_photo_upload` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Investor photograph required: 0 = No, 1 = Yes',
 `is_signature_upload` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Investor signature required: 0 = No, 1 = Yes',
 `is_ipv_upload` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'In person verification(IPV) required: 0 = No, 1 = Yes',
 `ipv_code` int(11) DEFAULT NULL COMMENT 'ipv generated code',
 `kra_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'KRA - Name',
 `kra_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'KRA - Email',
 `kra_email_verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'KRA - Email verified or not: 0 = Not Verified, 1 = Verified',
 `kra_fetch_datetime` datetime DEFAULT NULL COMMENT 'When did record got fetched from KRA API',
 `kra_mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'KRA - Mobile',
 `kra_document_fetch` tinyint(1) DEFAULT NULL COMMENT 'KRA document fetch: 0 = No, 1 = Yes',
 `kra_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'KRA Status: 0 = Non KRA, 1 = Yes',
 `kra_status_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'KRA Status Code retrieved from an API',
 `final_kra_status` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 `xml_file_status` int(11) DEFAULT NULL COMMENT '1=>''New Entry'',2=>''Modify'',3=>''Error'',4=>''Pending''',
 `xml_file_message` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'XML API Status message',
 `sftp_doc_status` int(11) DEFAULT NULL COMMENT '1=>''Done'',2=>''Pending''',
 `sftp_doc_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SFTP Doc response message',
 `kra_api_status` int(11) DEFAULT NULL COMMENT '1=>''Complete'',2=>''Pending'',3=>''Hold''',
 `father_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Father Name',
 `mother_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mother Name',
 `spouse_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Spouse Name',
 `address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address Line 1',
 `address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address Line 2',
 `address_line_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address Line 3',
 `address_pincode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Pincode',
 `address_city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address City',
 `address_district` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address District',
 `address_state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address State',
 `address_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address Type',
 `address_proof_submitted` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type of address proof submitted: Aadhar Card/ Voter ID etc.',
 `permanent_address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address Line 1',
 `permanent_address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address Line 2',
 `permanent_address_line_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address Line 3',
 `permanent_address_pincode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Pincode',
 `permanent_address_city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address City',
 `permanent_address_district` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address District',
 `permanent_address_state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address State',
 `permanent_address_proof_submitted` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type of address proof submitted: Aadhar Card/ Voter ID etc.',
 `permanent_address_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Address Type',
 `permanent_address_country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'permanent address country',
 `is_joint_holder` enum('Yes','No') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'No' COMMENT 'Joint Holder',
 `nominee_available` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Nominee details available: 0 = No, 1 = Yes',
 `broker_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Distributor ID',
 `from_site` text COLLATE utf8mb4_unicode_ci COMMENT 'from_site',
 `form_status` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Form Status',
 `is_form_completed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'IS Form Completed : 0 => No, 1 => Yes',
 `form_completed_date` datetime DEFAULT NULL COMMENT 'Form Completed datetime',
 `is_exported` enum('Yes','No') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'No' COMMENT 'Record exported to 3rd party like Tech Excel/KARVY etc.',
 `is_deficiency` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is_Deficiency: 0=> No deficiency, 1=> deficiency',
 `is_scrutiny_pending` tinyint(1) DEFAULT NULL COMMENT 'IS_Scrutiny_Pending: 1 => Scrutiny Pending , 2 => Scrutiny Done',
 `deficiency_date` datetime DEFAULT NULL COMMENT 'deficieny_date: Deficiency marked date',
 `kra_deficient` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'KRA-Deficient: 0 => Not having any KRA deficiency, 1 => Having KRA deficiency',
 `reg_ip_address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'registration local ip address',
 `ipv_ip_address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IPV local ip address',
 `ip_address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ip address of user',
 `fatca_terms_accepted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'FATCA terms accepted 1=YES',
 `bank_terms_accepted` tinyint(1) DEFAULT '0' COMMENT 'BANK terms accepted 1=YES',
 `nominee_later_terms_accepted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Nominee later terms accepted 1=YES',
 `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Created Date',
 `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Modified Date',
 `cvlkra_processed_date` date DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `investor_account_investor_id_index` (`investor_id`),
 KEY `investor_account_email_index` (`email`),
 KEY `investor_account_mobile_index` (`mobile`),
 KEY `investor_account_pan_index` (`pan`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
"); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_account_detail');
    }
}
