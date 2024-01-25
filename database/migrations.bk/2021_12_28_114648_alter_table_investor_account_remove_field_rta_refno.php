<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInvestorAccountRemoveFieldRtaRefno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // removing rta_refno field from MySQL table: investor_account
        Schema::table('investor_account', function (Blueprint $table) {
            $table->dropColumn(['rta_refno']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // adding earlier removed rta_refno field from MySQL table: investor_account
        DB::statement("ALTER TABLE `investor_account` ADD `rta_refno` BIGINT(20) NULL DEFAULT NULL COMMENT 'RTA referance number' AFTER `investor_id`;");
    }
}
