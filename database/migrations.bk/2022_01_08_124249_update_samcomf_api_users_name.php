<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSamcomfApiUsersName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding prefix against SAMCOMF api users list, so that other vendors should not guess the SAMCOMF api username
        DB::statement("UPDATE `api_users_list` SET `created_at` = `created_at`, `updated_at` = `updated_at`, `name` = REPLACE(`name`, 'SAMCOMF', '1960295626_SAMCOMF') WHERE `name` LIKE 'SAMCOMF%';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement("UPDATE `api_users_list` SET `created_at` = `created_at`, `updated_at` = `updated_at`, `name` = REPLACE(`name`, '1960295626_', '') WHERE `name` LIKE '1960295626_SAMCOMF%';");
    }
}
