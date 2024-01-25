<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterAddColumnMfplusScheme extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column mfplus_scheme in MySQL table: scheme_master
        Schema::connection('invdb')->table('scheme_master', function (Blueprint $table) {
            $table->string('mfplus_scheme', 50)->nullable()->comment('MFPlus Oracle DB scheme name')->after('Scheme_Name');
        });

        // updating default mfplus_scheme against existing schemes like SAMCO FLEXI CAP REGULAR & DIRECT GROWTH
        DB::connection('invdb')->statement("UPDATE scheme_master SET created = created, mfplus_scheme = 'SAMFLEX' WHERE RTA_Scheme_Code IN ('FCRG', 'FCDG');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column mfplus_scheme in MySQL table: scheme_master
        Schema::connection('invdb')->table('scheme_master', function (Blueprint $table) {
            $table->dropColumn('mfplus_scheme');
        });
    }
}
