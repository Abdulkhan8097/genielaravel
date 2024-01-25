<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserAccountLogs extends Migration
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
        CHANGE `created_by` `created_by` int NULL COMMENT 'user id' AFTER `new_records`,
        ADD `source` varchar(100) NULL COMMENT 'table name eg: user_account' AFTER `created_by`;
        ");
        

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
