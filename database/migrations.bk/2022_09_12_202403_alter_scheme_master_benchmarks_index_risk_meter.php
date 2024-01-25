<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterBenchmarksIndexRiskMeter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column risk_meter in MySQL table: scheme_master_benchmarks
        Schema::table('scheme_master_benchmarks', function (Blueprint $table) {
            $table->string('risk_meter', 50)->nullable()->comment('Risk meter. Possible values like: Low, High, Very High, Moderate etc.')->after('index_name');
        });
        DB::statement("UPDATE scheme_master_benchmarks SET created_at = created_at, updated_at = updated_at, risk_meter = 'Very High' WHERE RTA_Scheme_Code IN ('FCRG','FCDG') AND index_code = '312' AND status = '1';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column risk_meter in MySQL table: scheme_master_benchmarks
        Schema::table('scheme_master_benchmarks', function (Blueprint $table) {
            $table->dropColumn('risk_meter');
        });
    }
}
