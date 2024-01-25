<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterAddColumnTstpFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column like TSTP_FLAG in MySQL table: scheme_master
        Schema::table('scheme_master', function (Blueprint $table) {
            $table->string('TSTP_FLAG', 5)->nullable()->default('N')->comment('')->after('STP_FLAG');
            $table->index('TSTP_FLAG');
            $table->index('STP_FLAG');
            $table->index('SWP_Flag');
            $table->index('Switch_FLAG');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column like TSTP_FLAG from MySQL table: scheme_master
        Schema::table('scheme_master', function (Blueprint $table) {
            $table->dropColumn('TSTP_FLAG');
            $table->dropIndex('scheme_master_stp_flag_index');
            $table->dropIndex('scheme_master_swp_flag_index');
            $table->dropIndex('scheme_master_switch_flag_index');
        });
    }
}
