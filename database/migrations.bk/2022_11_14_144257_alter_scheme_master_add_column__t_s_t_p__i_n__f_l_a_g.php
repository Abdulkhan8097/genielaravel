<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterAddColumnTSTPINFLAG extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column TSTP_IN_FLAG after field Switch_FLAG
        Schema::table('scheme_master', function (Blueprint $table) {
            $table->string('TSTP_IN_FLAG', 5)->default('N')->nullable()->comment('')->after('STP_FLAG');
        });

        // For now only Timer STP IN Flag is allowed in FLEXI CAP FUND
        DB::statement("UPDATE scheme_master SET created = created, TSTP_IN_FLAG = 'Y' WHERE RTA_Scheme_Code IN ('FCRG', 'FCDG');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column TSTP_IN_FLAG
        Schema::table('scheme_master', function (Blueprint $table) {
            $table->dropColumn('TSTP_IN_FLAG');
        });
    }
}
