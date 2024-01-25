<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableChangePartnerCategoryLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_partner_category_log', function (Blueprint $table) {
            DB::statement("ALTER TABLE `change_partner_category_log` ADD `month` VARCHAR(250) NULL DEFAULT '0' COMMENT 'Month' AFTER `changed_to`;");
            DB::statement("ALTER TABLE `change_partner_category_log` ADD `year` VARCHAR(250) NULL DEFAULT '0' COMMENT 'Year' AFTER `month`;");
            // DB::statement("ALTER TABLE `change_partner_category_log` ADD `updated_at` datetime NULL DEFAULT (DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')) COMMENT 'updated at' AFTER `created_at`;");
            $table->datetime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_partner_category_log', function (Blueprint $table) {
            DB::statement("ALTER TABLE `change_partner_category_log` DROP `month`;");
            DB::statement("ALTER TABLE `change_partner_category_log` DROP `year`;");
            DB::statement("ALTER TABLE `change_partner_category_log` DROP `updated_at`;");
        });    
    }
}
