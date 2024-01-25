<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDistributorMasterAddColumnLastAvailableDateInAmfi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding a column "record_last_available_in_amfi"
        Schema::table('distributor_master', function (Blueprint $table) {
            $table->dateTime('record_last_available_in_amfi')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Last record available date from amfi')->after('updated_at');
        });

        Schema::table('distributor_master_backup', function (Blueprint $table) {
            $table->dateTime('record_last_available_in_amfi')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Last record available date from amfi')->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // dropping an earlier added column "record_last_available_in_amfi"
        Schema::table('distributor_master', function (Blueprint $table) {
            $table->dropColumn(['record_last_available_in_amfi']);
        });

        Schema::table('distributor_master_backup', function (Blueprint $table) {
            $table->dropColumn(['record_last_available_in_amfi']);
        });
    }
}
