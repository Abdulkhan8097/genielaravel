<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePartnersRankmfBdmListBackup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creating replica of an existing table partners_rankmf_bdm_list for importing CSV records into backup table
        DB::statement('CREATE TABLE `partners_rankmf_bdm_list_backup` LIKE `partners_rankmf_bdm_list`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partners_rankmf_bdm_list_backup');
    }
}
