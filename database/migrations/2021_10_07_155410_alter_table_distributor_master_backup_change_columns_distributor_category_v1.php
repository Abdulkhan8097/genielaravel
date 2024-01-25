<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDistributorMasterBackupChangeColumnsDistributorCategoryV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // converting distributor category & relationship_quality fields from Master Entry to Open Text Entry
        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `distributor_category` `distributor_category` VARCHAR(191) NULL DEFAULT NULL COMMENT 'Distributor category';");
            DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `relationship_quality_with_product_approver` `relationship_quality_with_product_approver` VARCHAR(191) NULL DEFAULT NULL COMMENT 'How is the relationship with product approver person';");
            DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `relationship_quality_with_sales_person` `relationship_quality_with_sales_person` VARCHAR(191) NULL DEFAULT NULL COMMENT 'How is the relationship with sales driving person';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `distributor_category` `distributor_category` INT(11) NULL DEFAULT NULL COMMENT 'Distributor category';");
            DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `relationship_quality_with_product_approver` `relationship_quality_with_product_approver` INT(11) NULL DEFAULT NULL COMMENT 'How is the relationship with product approver person';");
            DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `relationship_quality_with_sales_person` `relationship_quality_with_sales_person` INT(11) NULL DEFAULT NULL COMMENT 'How is the relationship with sales driving person';");
        });
    }
}
