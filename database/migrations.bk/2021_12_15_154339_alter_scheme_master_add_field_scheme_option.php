<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterAddFieldSchemeOption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding field Scheme_Option
        Schema::table('scheme_master', function (Blueprint $table) {
            $table->string('Scheme_Option')->nullable()->after('Scheme_Plan');
        });

        DB::update("UPDATE `scheme_master` SET `created` = `created`, `Scheme_Option` = 'G' WHERE `RTA_Scheme_Code` = 'FCRG';");
        DB::update("UPDATE `scheme_master` SET `created` = `created`, `Scheme_Option` = 'D' WHERE `RTA_Scheme_Code` = 'FCDG';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added field Scheme_Option
        Schema::table('scheme_master', function (Blueprint $table) {
            $table->dropColumn(['Scheme_Option']);
        });
    }
}
