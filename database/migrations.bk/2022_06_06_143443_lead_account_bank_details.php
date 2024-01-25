<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadAccountBankDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('invdb')
        ->statement("CREATE TABLE `folio_account_bank_details` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Serial id',
 `investor_account_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Foreign key references investor_id field from investor_account table',
 `hash_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hash key:ACC_NO|IFSC_CODE|ACCOUNT_TYPE',
 `ifsc_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank IFSC code',
 `micr` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'MICR code',
 `account_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Account type, possible values are: Savings, Current',
 `account_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank account number',
 `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank name',
 `branch_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank address',
 `bank_city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank city',
 `bank_state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank state',
 `bank_pincode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank pincode',
 `bank_contact` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank contact number',
 `depository_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Depository name',
 `depo_part_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Depository partner name',
 `beneficiary_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Beneficiery name',
 `nsdl_dp_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Broker demat id nsdl',
 `nsdl_bo_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Investor demat id nsdl ',
 `cdsl_bo_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Investor demat id cdsl',
 `bank_details_saved` tinyint(4) DEFAULT '0' COMMENT 'Bank details saved api status: 0=pending, 1=failed, 2=success',
 `status` tinyint(4) DEFAULT '1' COMMENT 'Status: 0=inactive, 1=active',
 `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Created date',
 `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Modified date',
 PRIMARY KEY (`id`),
 KEY `folio_account_bank_details_investor_account_id_index` (`investor_account_id`),
 KEY `hash_key_index` (`hash_key`),
 KEY `folio_account_bank_details_ifsc_code_index` (`ifsc_code`),
 KEY `folio_account_bank_details_account_type_index` (`account_type`),
 KEY `folio_account_bank_details_account_number_index` (`account_number`),
 KEY `folio_account_bank_details_status_index` (`status`),
 KEY `folio_account_bank_details_created_at_index` (`created_at`),
 CONSTRAINT `folio_account_bank_details_investor_account_id_foreign` FOREIGN KEY (`investor_account_id`) REFERENCES `folio_investor_detail` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
