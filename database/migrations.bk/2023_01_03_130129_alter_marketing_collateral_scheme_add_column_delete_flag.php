<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMarketingCollateralSchemeAddColumnDeleteFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column delete_flag in MySQL table: marketing_collateral_scheme
        Schema::table('marketing_collateral_scheme', function (Blueprint $table) {
            $table->tinyInteger('delete_flag')->default(0)->nullable()->comment('Delete flag: 0=not deleted, 1=deleted')->after('scheme_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column delete_flag from MySQL table: marketing_collateral_scheme
        Schema::table('marketing_collateral_scheme', function (Blueprint $table) {
            $table->dropColumn('delete_flag');
        });
    }
}
