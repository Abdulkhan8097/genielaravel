<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProjectFocusAmcWiseDetailsBackup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creating replica of an existing table project_focus_amc_wise_details_backup for importing CSV records into backup table
        DB::statement('CREATE TABLE `project_focus_amc_wise_details_backup` LIKE `project_focus_amc_wise_details`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_focus_amc_wise_details_backup');
    }
}
