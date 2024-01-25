<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmRankmfPartnerRegistrationBackupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drm_rankmf_partner_registration_backup', function(Blueprint $table){
            $table->string('dob')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drm_rankmf_partner_registration_backup', function(Blueprint $table){
			$table->dropColumn('dob');
        });
    }
}
