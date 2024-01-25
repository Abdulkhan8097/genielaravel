<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlertPeenyDefaultIpAdd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penny_default_account_numbers', function (Blueprint $table) {
       $table->dropColumn('pan');
       $table->dropColumn('beneAccNo');
      });
    DB::statement("ALTER TABLE `penny_default_account_numbers` ADD `block_value` VARCHAR(255) NULL DEFAULT NULL COMMENT 'key of of block value' AFTER `id`");
    DB::statement("ALTER TABLE `penny_default_account_numbers` ADD `block_filed` VARCHAR(255) NULL DEFAULT NULL COMMENT 'key of of block list like beneAccNo,ip_address_block,ip_address_allow' AFTER `id`");
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
