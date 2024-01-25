<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRtaApiExecutionStatusAddRedemptionDetailsSaved extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding a column redemption_details_saved in MySQL table: rta_api_execution_status
        Schema::table('rta_api_execution_status', function (Blueprint $table) {
            $table->tinyInteger('redemption_details_saved')->default('0')->nullable()->comment('Redemption save details status: 0=pending, 1=failed, 2=success')->after('purchase_save_confirmation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column redemption_details_saved from MySQL table: rta_api_execution_status
        Schema::table('rta_api_execution_status', function (Blueprint $table) {
            $table->dropColumn(['redemption_details_saved']);
        });
    }
}
