<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmDistributorMasterAddColumnArnZoneAndProjectGreenShoots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column arn_zone and project_green_shoots in MySQL table: drm_distributor_master
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->string('arn_zone', 50)->nullable()->comment('ARN zone mapped based on AMFI City')->after('arn_state');
            $table->string('project_green_shoots', 20)->nullable()->comment('Possible Values: Yes or No')->after('project_emerging_stars');
            $table->index('arn_zone');
            $table->index('project_focus');
            $table->index('project_emerging_stars');
            $table->index('project_green_shoots');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column arn_zone and project_green_shoots in MySQL table: drm_distributor_master
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->dropColumn('arn_zone');
            $table->dropColumn('project_green_shoots');
            $table->dropIndex('drm_distributor_master_project_focus_index');
            $table->dropIndex('drm_distributor_master_project_emerging_stars_index');
        });
    }
}
