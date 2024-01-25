<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorOrderChangeCommentOfAllottedunitField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("ALTER TABLE `samcomf_investor_db`.`investor_order` CHANGE `allottedunit` `allottedunit` DECIMAL(10,4) NULL DEFAULT NULL COMMENT 'Successful purchase order alloted/redeemed units';");
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
