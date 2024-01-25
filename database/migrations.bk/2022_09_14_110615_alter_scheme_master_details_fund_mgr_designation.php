<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterDetailsFundMgrDesignation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column fund_mgr_designation in MySQL table: scheme_master_details
        Schema::table('scheme_master_details', function (Blueprint $table) {
            $table->string('fund_mgr1_designation', 255)->nullable()->comment('Fund manager 1 designation')->after('fund_mgr1_id');
            $table->string('fund_mgr2_designation', 255)->nullable()->comment('Fund manager 2 designation')->after('fund_mgr2_id');
            $table->string('fund_mgr3_designation', 255)->nullable()->comment('Fund manager 3 designation')->after('fund_mgr3_id');
            $table->string('fund_mgr4_designation', 255)->nullable()->comment('Fund manager 4 designation')->after('fund_mgr4_id');
        });

        DB::statement("UPDATE scheme_master_details SET created_at = created_at, updated_at = updated_at, fund_mgr1_designation = 'Fund Manager - Equity' WHERE fund_mgr1_id = '1' AND RTA_Scheme_Code IN ('FCRG','FCDG');");
        DB::statement("UPDATE scheme_master_details SET created_at = created_at, updated_at = updated_at, fund_mgr2_designation = 'Dedicated Fund Manager for overseas investments' WHERE fund_mgr2_id = '2' AND RTA_Scheme_Code IN ('FCRG','FCDG');");
        DB::statement("UPDATE scheme_master_details SET created_at = created_at, updated_at = updated_at, scheme_plan_text = '<ul><li>Direct Growth</li><li>Regular Growth</li></ul>' WHERE RTA_Scheme_Code IN ('FCRG','FCDG');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column fund_mgr_designation in MySQL table: scheme_master_details
        Schema::table('scheme_master_details', function (Blueprint $table) {
            $table->dropColumn('fund_mgr1_designation');
            $table->dropColumn('fund_mgr2_designation');
            $table->dropColumn('fund_mgr3_designation');
            $table->dropColumn('fund_mgr4_designation');
        });
    }
}
