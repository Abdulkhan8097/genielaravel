<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmDistributorMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column project_emerging_stars
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->string('project_emerging_stars', 20)->nullable()->comment('Possible Values: Yes or No')->after('project_focus');
        });

        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->string('project_emerging_stars', 20)->nullable()->comment('Possible Values: Yes or No')->after('project_focus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column project_emerging_stars
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->dropColumn(['project_emerging_stars']);
        });

        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->dropColumn(['project_emerging_stars']);
        });
    }
}
