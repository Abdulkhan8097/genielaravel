<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmDistributorMasterAddFieldSamcomfLiveSipAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding columns samcomf_live_sip_amount in MySQL table: drm_distributor_master
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->decimal('samcomf_live_sip_amount', 25, 4)->nullable()->comment('SamcoMF partner Live SIP Registration Amount')->after('is_partner_active_on_samcomf');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column samcomf_live_sip_amount in MySQL table: drm_distributor_master
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->dropColumn('samcomf_live_sip_amount');
        });
    }
}
