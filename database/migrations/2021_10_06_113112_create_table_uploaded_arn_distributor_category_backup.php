<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUploadedArnDistributorCategoryBackup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creating replica of an existing table uploaded_arn_distributor_category for importing CSV records into backup table
        DB::statement('CREATE TABLE `uploaded_arn_distributor_category_backup` LIKE `uploaded_arn_distributor_category`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploaded_arn_distributor_category_backup');
    }
}
