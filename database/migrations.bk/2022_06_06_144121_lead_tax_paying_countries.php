<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadTaxPayingCountries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('invdb')
        ->statement("CREATE TABLE `folio_tax_paying_countries` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Serial id',
 `investor_account_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Foreign key references investor_id field from investor_account table',
 `tax_country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tax assessed country',
 `tax_reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tax reference number',
 `tax_identification_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tax identification type',
 `status` tinyint(4) DEFAULT '1' COMMENT 'Status: 0=inactive, 1=active',
 `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Created date',
 `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Modified date',
 PRIMARY KEY (`id`),
 KEY `filio_tax_paying_countries_investor_account_id_index` (`investor_account_id`),
 KEY `folio_tax_paying_countries_status_index` (`status`),
 KEY `folio_tax_paying_countries_created_at_index` (`created_at`),
 CONSTRAINT `filio_tax_paying_countries_ibfk_1` FOREIGN KEY (`investor_account_id`) REFERENCES `folio_investor_detail` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"); 
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
