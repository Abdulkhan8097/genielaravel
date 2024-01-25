<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('otp', function($table) {
        //     $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
        //     $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->change();
        // });

        DB::statement('ALTER TABLE `otp`
        CHANGE `created_at` `created_at` timestamp NULL ON UPDATE CURRENT_TIMESTAMP AFTER `used_for`,
        CHANGE `updated_at` `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`');
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
