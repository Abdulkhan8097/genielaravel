<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePartnersRankmfCurrentAumBackup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creating replica of an existing table partners_rankmf_current_aum for importing CSV records into backup table
        DB::statement('CREATE TABLE `partners_rankmf_current_aum_backup` LIKE `partners_rankmf_current_aum`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partners_rankmf_current_aum_backup');
    }
}
