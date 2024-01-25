<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUserAccountAddCorrespondenceAddressDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //adding correspondence address details
        DB::statement("ALTER TABLE `user_account`
        ADD `amfi_address` varchar(255) NULL  COMMENT 'AMFI Address' AFTER `ARN`,
        ADD `amfi_pin` varchar(50) NULL  COMMENT 'AMFI Pincode' AFTER `amfi_address`,
        ADD `amfi_city` varchar(100) NULL  COMMENT 'AMFI City' AFTER `amfi_pin`,
        ADD `amfi_euin` varchar(255) NULL  COMMENT 'AMFI EUIN' AFTER `amfi_city`,
        ADD `is_current_address_same_as_amfi` tinyint(1) NULL DEFAULT '0' COMMENT 'Does both amfi address and current address both are same? 0 = No, 1=Yes' AFTER `amfi_euin`,
        ADD `current_address` varchar(255) NULL  COMMENT 'Current Address' AFTER `is_current_address_same_as_amfi`,
        ADD `current_pin` varchar(50) NULL  COMMENT 'Current Pincode' AFTER `current_address`,
        ADD `current_city` varchar(100) NULL  COMMENT 'Current City' AFTER `current_pin`,
        ADD `current_landmark` varchar(255) NULL  COMMENT 'Current landmark' AFTER `current_city`");
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
