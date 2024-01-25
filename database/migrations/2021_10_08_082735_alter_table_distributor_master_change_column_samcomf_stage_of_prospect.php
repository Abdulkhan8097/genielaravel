<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDistributorMasterChangeColumnSamcomfStageOfProspect extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("ALTER TABLE `drm_distributor_master` CHANGE `samcomf_stage_of_prospect` `samcomf_stage_of_prospect` VARCHAR(50) NULL DEFAULT NULL COMMENT 'SamcoMF partner form status';");
        DB::statement("ALTER TABLE `drm_distributor_master` CHANGE `rankmf_stage_of_prospect` `rankmf_stage_of_prospect` VARCHAR(100) NULL DEFAULT NULL COMMENT 'RankMF partner form status';");
        DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `samcomf_stage_of_prospect` `samcomf_stage_of_prospect` VARCHAR(50) NULL DEFAULT NULL COMMENT 'SamcoMF partner form status';");
        DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `rankmf_stage_of_prospect` `rankmf_stage_of_prospect` VARCHAR(100) NULL DEFAULT NULL COMMENT 'RankMF partner form status';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement("ALTER TABLE `drm_distributor_master` CHANGE `samcomf_stage_of_prospect` `samcomf_stage_of_prospect` TINYINT(4) NULL DEFAULT NULL COMMENT 'SamcoMF partner form status';");
        DB::statement("ALTER TABLE `drm_distributor_master` CHANGE `rankmf_stage_of_prospect` `rankmf_stage_of_prospect` TINYINT(4) NULL DEFAULT NULL COMMENT 'RankMF partner form status';");
        DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `samcomf_stage_of_prospect` `samcomf_stage_of_prospect` TINYINT(4) NULL DEFAULT NULL COMMENT 'SamcoMF partner form status';");
        DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `rankmf_stage_of_prospect` `rankmf_stage_of_prospect` TINYINT(4) NULL DEFAULT NULL COMMENT 'RankMF partner form status';");
    }
}
