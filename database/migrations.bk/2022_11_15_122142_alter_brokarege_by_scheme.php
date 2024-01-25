<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBrokaregeByScheme extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::table('rate_card_schemewise', function (Blueprint $table) {
            DB::statement("ALTER TABLE `rate_card_schemewise` ADD `last_active` TINYINT NOT NULL DEFAULT '1' COMMENT '0->inactive,1->active' AFTER `check`;");

        });
        ;
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
