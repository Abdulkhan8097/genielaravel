<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableMfExpenseRatioSebi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `mf_expense_ratio_sebi` CHANGE `expe_date` `expe_date` date NULL COMMENT 'Expense Ratio Date' AFTER `scheme_type`;");

        DB::statement("ALTER TABLE `mf_expense_ratio_sebi` ADD `file_path` varchar(255) NULL COMMENT 'Uploaded File Path' AFTER `status`;");

        DB::statement("ALTER TABLE `nav_history` ADD `file_path` varchar(255) NULL COMMENT 'Uploaded File Path' AFTER `status`;");

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
