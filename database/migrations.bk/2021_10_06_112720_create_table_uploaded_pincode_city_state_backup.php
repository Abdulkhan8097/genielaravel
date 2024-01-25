<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUploadedPincodeCityStateBackup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creating replica of an existing table uploaded_pincode_city_state for importing CSV records into backup table
        DB::statement('CREATE TABLE `uploaded_pincode_city_state_backup` LIKE `uploaded_pincode_city_state`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploaded_pincode_city_state_backup');
    }
}
