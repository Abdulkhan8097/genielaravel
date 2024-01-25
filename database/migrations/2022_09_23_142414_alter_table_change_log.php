<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableChangeLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_partner_category_log', function (Blueprint $table) {
            DB::statement("ALTER TABLE `change_partner_category_log` ADD `scheme_code` VARCHAR(20) NULL DEFAULT NULL AFTER `year`");
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
