<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRateCardSchemewise extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `rate_card_schemewise` ADD `check` tinyint NULL DEFAULT '0' COMMENT '0: not uploaded, 1:uploaded in rate_card_partnerwise ' AFTER `status`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `rate_card_schemewise` DROP `check`");
    }
}
