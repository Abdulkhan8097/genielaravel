<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUploadedArnAverageAumTotalCommissionDataBackup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creating replica of an existing table uploaded_arn_average_aum_total_commission_data for importing CSV records into backup table
        DB::statement('CREATE TABLE `uploaded_arn_average_aum_total_commission_data_backup` LIKE `uploaded_arn_average_aum_total_commission_data`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploaded_arn_average_aum_total_commission_data_backup');
    }
}
