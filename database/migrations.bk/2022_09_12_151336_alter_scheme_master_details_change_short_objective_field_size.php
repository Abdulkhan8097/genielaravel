<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterDetailsChangeShortObjectiveFieldSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // making short_objective field length to 512 characters
        Schema::table('scheme_master_details', function (Blueprint $table) {
            $table->string('short_objective', 512)->nullable()->comment('Short Objective/About scheme description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // reverting short_objective field length back to 255 characters
        Schema::table('scheme_master_details', function (Blueprint $table) {
            $table->string('short_objective', 255)->nullable()->comment('Short Objective/About scheme description')->change();
        });
    }
}
