<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserAccountLog2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        ALTER TABLE `user_account_logs`
            CHANGE `old_records` `old_records` text COLLATE 'utf8mb4_unicode_ci' NULL AFTER `type`,
            CHANGE `new_records` `new_records` text COLLATE 'utf8mb4_unicode_ci' NULL AFTER `old_records`; 
        ");
        
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
