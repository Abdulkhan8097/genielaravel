<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `investore_oder_log`  ADD `sub_broker_arn` VARCHAR(244) NULL DEFAULT NULL  AFTER `new_folio`,  ADD `sub_broker_internal_code` VARCHAR(244) NULL DEFAULT NULL  AFTER `sub_broker_arn`,  ADD `euin_declaration_terms` VARCHAR(244) NULL DEFAULT NULL  AFTER `sub_broker_internal_code`;");
        DB::statement("ALTER TABLE `investore_oder_log` ADD `broker_euin` VARCHAR(244) NULL DEFAULT NULL AFTER `euin_declaration_terms`;");
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
