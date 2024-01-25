<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterAddFieldSchemePlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('scheme_master', function (Blueprint $table) {
            $table->string('Scheme_Plan_Code')->nullable()->comment('Scheme Plan Code')->after('Scheme_Plan');
        });
        DB::statement("UPDATE scheme_master SET created = created, Scheme_Code = 'FC', Scheme_Plan = 'Regular', Scheme_Plan_Code = 'RG' WHERE RTA_Scheme_Code = 'FCRG';");
        DB::statement("UPDATE scheme_master SET created = created, Scheme_Code = 'FC', Scheme_Plan = 'Direct', Scheme_Plan_Code = 'DG' WHERE RTA_Scheme_Code = 'FCDG';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('scheme_master', function (Blueprint $table) {
            $table->dropColumn(['Scheme_Plan_Code']);
        });
    }
}
