<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDistributorMasterArnValidDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // updating ARN Valid From & Till date as DATE only instead of DATETIME
        DB::statement("ALTER TABLE `distributor_master` CHANGE `arn_valid_from` `arn_valid_from` DATE NULL DEFAULT NULL COMMENT 'AMFI: ARN valid from date';");
        DB::statement("ALTER TABLE `distributor_master` CHANGE `arn_valid_till` `arn_valid_till` DATE NULL DEFAULT NULL COMMENT 'AMFI: ARN valid till date';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // reverting the changes did above
        DB::statement("ALTER TABLE `distributor_master` CHANGE `arn_valid_from` `arn_valid_from` datetime DEFAULT NULL COMMENT 'AMFI: ARN valid from date';");
        DB::statement("ALTER TABLE `distributor_master` CHANGE `arn_valid_till` `arn_valid_till` datetime DEFAULT NULL COMMENT 'AMFI: ARN valid till date';");
    }
}
