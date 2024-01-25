<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRateCardPartnerwiseStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rate_card_partnerwise', function (Blueprint $table) {
            //DB::statement("ALTER TABLE `rate_card_partnerwise` MODIFY `status` TINYINT(2) DEFAULT(1);");
            DB::statement("ALTER TABLE `rate_card_partnerwise` CHANGE `status` `status` tinyint(2) DEFAULT 1;");
        });
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
