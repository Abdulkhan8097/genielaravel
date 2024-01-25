<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmDistributorMasterAddAlternateNameColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding alternate contact person names column
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->string('alternate_name_1')->nullable()->comment('Alternate name 1')->after('rm_relationship');
            $table->string('alternate_name_2')->nullable()->comment('Alternate name 2')->after('alternate_name_1');
            $table->string('alternate_name_3')->nullable()->comment('Alternate name 3')->after('alternate_name_2');
            $table->string('alternate_name_4')->nullable()->comment('Alternate name 4')->after('alternate_name_3');
            $table->string('alternate_name_5')->nullable()->comment('Alternate name 5')->after('alternate_name_4');
        });

        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->string('alternate_name_1')->nullable()->comment('Alternate name 1')->after('rm_relationship');
            $table->string('alternate_name_2')->nullable()->comment('Alternate name 2')->after('alternate_name_1');
            $table->string('alternate_name_3')->nullable()->comment('Alternate name 3')->after('alternate_name_2');
            $table->string('alternate_name_4')->nullable()->comment('Alternate name 4')->after('alternate_name_3');
            $table->string('alternate_name_5')->nullable()->comment('Alternate name 5')->after('alternate_name_4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added contact person names columns
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->dropColumn(['alternate_name_1', 'alternate_name_2', 'alternate_name_3', 'alternate_name_4', 'alternate_name_5']);
        });

        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->dropColumn(['alternate_name_1', 'alternate_name_2', 'alternate_name_3', 'alternate_name_4', 'alternate_name_5']);
        });
    }
}
