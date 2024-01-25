<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMarketingCollateralListAddingFiletype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `marketing_collateral_list`
        ADD `file_type` varchar(50) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT 'pdf' COMMENT 'For convert pdf to jpg' AFTER `json_parameter`;");
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
