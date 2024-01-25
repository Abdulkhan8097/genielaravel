<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMosMultiplierDataRenameColumnMarginOfSafeyToMarginOfSafety extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // rename column from margin_of_safey to margin_of_safety
        Schema::table('mos_multiplier_data', function (Blueprint $table) {
            $table->renameColumn('margin_of_safey', 'margin_of_safety');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // rename back column to margin_of_safey from margin_of_safety
        Schema::table('mos_multiplier_data', function (Blueprint $table) {
            $table->renameColumn('margin_of_safety', 'margin_of_safey');
        });
    }
}
