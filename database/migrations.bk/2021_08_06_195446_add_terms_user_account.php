<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTermsUserAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `user_account`
        CHANGE `form_status` `form_status` tinyint NOT NULL DEFAULT '0' COMMENT '1=>verify, 2=>consent, 3=>thankyou' AFTER `is_nominee_minor`,
        ADD `terms` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = accepts our tems and conditions' AFTER `approved_date`");
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
