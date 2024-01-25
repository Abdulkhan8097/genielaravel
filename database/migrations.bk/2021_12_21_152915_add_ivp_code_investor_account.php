<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIvpCodeInvestorAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `investor_account` ADD `ipv_code` INT(11) NULL DEFAULT NULL COMMENT 'ipv generated code' AFTER `is_ipv_upload`;");
        DB::statement("ALTER TABLE `investor_account` ADD `rta_refno` BIGINT(20) NULL DEFAULT NULL COMMENT 'RTA referance number' AFTER `investor_id`;");
        DB::statement("ALTER TABLE `investor_account` ADD `reg_ip_address` VARCHAR(100) NULL DEFAULT NULL COMMENT 'registration local ip address' AFTER `kra_deficient`;");
        DB::statement("ALTER TABLE `investor_account` ADD `ipv_ip_address` VARCHAR(100) NULL DEFAULT NULL COMMENT 'IPV local ip address' AFTER `reg_ip_address`;");
        DB::statement("ALTER TABLE `investor_account` ADD `ip_address` VARCHAR(100) NULL DEFAULT NULL COMMENT 'ip address of user' AFTER `ipv_ip_address`, ADD `fatca_terms_accepted` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'FATCA terms accepted 1=YES' AFTER `ip_address`, ADD `bank_terms_accepted` TINYINT(1) NULL DEFAULT '0' COMMENT 'BANK terms accepted 1=YES' AFTER `fatca_terms_accepted`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investor_account', function (Blueprint $table) {
            $table->dropColumn(['ivp_code', 'rta_refno', 'reg_ip_address','ipv_ip_address','ip_address','fatca_terms_accepted','bank_terms_accepted']);
        });
    }
}
