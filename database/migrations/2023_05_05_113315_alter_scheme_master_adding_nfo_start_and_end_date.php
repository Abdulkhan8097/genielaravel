<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterAddingNfoStartAndEndDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding nfo_start_date and nfo_end_date just for reference, so that once scheme gets re-opened, we will be able to know what's the date for NFO from MySQL table: scheme_master
        Schema::table('scheme_master', function (Blueprint $table) {
            $table->date('nfo_start_date')->nullable()->comment('NFO start date')->after('scheme_flag');
            $table->date('nfo_end_date')->nullable()->comment('NFO end date')->after('nfo_start_date');
            $table->string('scheme',256)->nullable()->comment('Name of Scheme');
        });
        // updating NFO start and end date for already known schemes
        DB::statement("UPDATE `scheme_master` SET `nfo_start_date` = '2022-01-17', `nfo_end_date` = '2022-01-31' WHERE `RTA_Scheme_Code` IN ('FCRG', 'FCDG');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added nfo_start_date and nfo_end_date from MySQL table: scheme_master
        Schema::table('scheme_master', function (Blueprint $table) {
            $table->dropColumn(['nfo_start_date', 'nfo_end_date']);
        });
    }
}
