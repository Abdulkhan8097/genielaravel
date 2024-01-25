<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableArnAlternateDetailsBackup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creating replica of an existing table arn_alternate_details for importing CSV records into backup table
        DB::statement('CREATE TABLE `arn_alternate_details_backup` LIKE `arn_alternate_details`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arn_alternate_details_backup');
    }
}
