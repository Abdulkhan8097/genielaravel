<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterAddColumnAutoSwitchFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column Auto_Switch_OUT_FLAG & Auto_Switch_IN_FLAG after field Switch_FLAG
        Schema::table('scheme_master', function (Blueprint $table) {
            $table->string('Auto_Switch_IN_FLAG', 5)->default('N')->nullable()->comment('')->after('Switch_FLAG');
            $table->string('Auto_Switch_OUT_FLAG', 5)->default('N')->nullable()->comment('')->after('Auto_Switch_IN_FLAG');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column Auto_Switch_OUT_FLAG & Auto_Switch_IN_FLAG
        Schema::table('scheme_master', function (Blueprint $table) {
            $table->dropColumn('Auto_Switch_IN_FLAG');
            $table->dropColumn('Auto_Switch_OUT_FLAG');
        });
    }
}
