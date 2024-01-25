<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArnToUserAccountLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //ALTER TABLE `user_account_logs`
        DB::statement("ALTER TABLE `user_account_logs` ADD `arn` varchar(50) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `type`");
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
