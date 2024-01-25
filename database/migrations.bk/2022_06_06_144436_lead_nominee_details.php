<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadNomineeDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('invdb')
        ->statement("CREATE TABLE `folio_nominee_details` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Serial id',
 `investor_account_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Foreign key references investor_id field from investor_account table',
 `nominee_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nominee name',
 `nominee_relation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nominee relation',
 `nominee_allocation_percentage` int(11) DEFAULT NULL COMMENT 'Nominee allocation percentage share',
 `is_nominee_minor` tinyint(4) DEFAULT '0' COMMENT 'Is nominee minor: 0=no, 1=yes',
 `nominee_dob` date DEFAULT NULL COMMENT 'Nominee date of birth',
 `nominee_guardian_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nominee guardian name',
 `nominee_guardian_pan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nominee guardian PAN',
 `status` tinyint(4) DEFAULT '1' COMMENT 'Status: 0=inactive, 1=active',
 `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Created date',
 `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Modified date',
 PRIMARY KEY (`id`),
 KEY `folio_nominee_details_investor_account_id_index` (`investor_account_id`),
 KEY `folio_nominee_details_status_index` (`status`),
 KEY `folio_nominee_details_created_at_index` (`created_at`),
 CONSTRAINT `lead_nominee_details_investor_account_id_foreign` FOREIGN KEY (`investor_account_id`) REFERENCES `folio_investor_detail` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"); 
    }

    /**
     * Reverse the migrations.folio
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
