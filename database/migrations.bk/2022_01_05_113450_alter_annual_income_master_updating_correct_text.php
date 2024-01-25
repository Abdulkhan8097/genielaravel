<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAnnualIncomeMasterUpdatingCorrectText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("UPDATE `annual_income_master` SET `description` = 'Rs 1 Lakh - Rs 5 Lakhs' WHERE `annual_income_master`.`id` = 2;");
        DB::statement("UPDATE `annual_income_master` SET `description` = 'Rs 5 Lakhs - Rs 10 Lakhs' WHERE `annual_income_master`.`id` = 3;");
        DB::statement("UPDATE `annual_income_master` SET `description` = 'Rs 10 Lakhs - Rs 25 Lakhs' WHERE `annual_income_master`.`id` = 4;");
        DB::statement("UPDATE `annual_income_master` SET `description` = 'Rs 25 Lakhs - Rs 1 crore' WHERE `annual_income_master`.`id` = 5;");
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
