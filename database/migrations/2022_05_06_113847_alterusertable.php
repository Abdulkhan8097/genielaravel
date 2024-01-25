<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alterusertable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `user_account` ADD `kfintech_save` TINYINT NULL DEFAULT NULL COMMENT '1=>save sucessfully,0=>save fail, blink->not api hit' AFTER `terms`;");  
        DB::statement("ALTER TABLE `user_account` CHANGE `kfintech_save` `kfintech_save` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '1=>save sucessfully,0=>save fail, blink->not api hit';"); 
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
