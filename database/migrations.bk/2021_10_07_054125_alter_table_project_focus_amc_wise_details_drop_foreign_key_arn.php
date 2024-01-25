<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProjectFocusAmcWiseDetailsDropForeignKeyArn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // removing a foreign key ARN
        Schema::table('project_focus_amc_wise_details', function (Blueprint $table) {
            $table->dropForeign('project_focus_amc_wise_details_arn_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
