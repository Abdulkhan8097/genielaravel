<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableTaxPayingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::statement("ALTER TABLE `investor_tax_paying_countries` ADD FOREIGN KEY `fk_investor_account_id`(`investor_account_id`) references `investor_account`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;");
         DB::statement("ALTER TABLE `investor_account` ADD `net_worth_amount` decimal(25,2) NULL DEFAULT NULL COMMENT 'net_worth_amount as on date' AFTER `income_range`;");
         DB::statement("ALTER TABLE `investor_account_bank_details` CHANGE `dp_id` `nsdl_dp_id` varchar(50) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Broker demat id nsdl' AFTER `beneficiary_name`, CHANGE `bo_id` `nsdl_bo_id` varchar(50) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Investor demat id nsdl ' AFTER `nsdl_dp_id`, ADD `cdsl_bo_id` varchar(50) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Investor demat id cdsl' AFTER `nsdl_bo_id`;");
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
