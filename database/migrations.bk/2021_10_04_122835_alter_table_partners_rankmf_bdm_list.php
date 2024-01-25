<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePartnersRankmfBdmList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding columns available from partners rankmf DB
        DB::statement("ALTER TABLE `partners_rankmf_bdm_list` ADD COLUMN `login_master_sr_id` INT NOT NULL COMMENT 'mfp_partner_login_master table >> id field' AFTER `id`, ADD COLUMN `role_name` VARCHAR(12) DEFAULT NULL COMMENT 'mfp_partner_login_master table >> role_name field' AFTER `login_master_sr_id`, ADD COLUMN `pan` VARCHAR(150) DEFAULT NULL COMMENT 'PAN Number' AFTER `mobile`, ADD COLUMN `password` VARCHAR(400) DEFAULT NULL COMMENT 'mfp_partner_login_master table >> password field' AFTER `pan`, ADD COLUMN `unit_code` VARCHAR(50) DEFAULT NULL COMMENT 'UNIT Code' AFTER `address`, ADD COLUMN `branch_code` VARCHAR(50) DEFAULT NULL COMMENT 'Branch Code' AFTER `unit_code`, ADD COLUMN `title` VARCHAR(50) DEFAULT NULL COMMENT 'Title' AFTER `branch_code`, ADD COLUMN `marital_status` VARCHAR(50) DEFAULT NULL COMMENT 'Marital Status' AFTER `title`, ADD COLUMN `city` VARCHAR(50) DEFAULT NULL COMMENT 'City' AFTER `pincode`, ADD COLUMN `dob` DATE DEFAULT NULL COMMENT 'DOB' AFTER `name`;");
        // updating the column data types
        DB::statement("ALTER TABLE `partners_rankmf_bdm_list` CHANGE `branch_manager` `branch_manager` INT NULL DEFAULT NULL COMMENT 'Branch Manager', CHANGE `area_manager` `area_manager` INT NULL DEFAULT NULL COMMENT 'Area Manager', CHANGE `circle_manager` `circle_manager` INT NULL DEFAULT NULL COMMENT 'Circle Manager', CHANGE `national_manager` `national_manager` INT NULL DEFAULT NULL COMMENT 'National Manager';");
        // adding an index
        DB::statement("ALTER TABLE `partners_rankmf_bdm_list` ADD INDEX `idx_login_master_sr_id` (`login_master_sr_id`);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing columns which were added earlier
        DB::statement("ALTER TABLE `partners_rankmf_bdm_list` DROP COLUMN `login_master_sr_id`, DROP COLUMN `role_name`, DROP COLUMN `pan`, DROP COLUMN `password`, DROP COLUMN `unit_code`, DROP COLUMN `branch_code`, DROP COLUMN `title`, DROP COLUMN `marital_status`, DROP COLUMN `city`, DROP COLUMN `dob`;");
        // updating the column data types
        DB::statement("ALTER TABLE `partners_rankmf_bdm_list` CHANGE `branch_manager` `branch_manager` VARCHAR(191) DEFAULT NULL COMMENT 'Branch Manager', CHANGE `area_manager` `area_manager` VARCHAR(191) DEFAULT NULL COMMENT 'Area Manager', CHANGE `circle_manager` `circle_manager` VARCHAR(191) DEFAULT NULL COMMENT 'Circle Manager', CHANGE `national_manager` `national_manager` VARCHAR(191) DEFAULT NULL COMMENT 'National Manager';");
        // removing an index
        DB::statement("ALTER TABLE `partners_rankmf_bdm_list` DROP INDEX `idx_login_master_sr_id`;");
    }
}
