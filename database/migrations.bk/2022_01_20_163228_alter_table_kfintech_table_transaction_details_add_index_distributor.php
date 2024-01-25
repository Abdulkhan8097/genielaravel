<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableKfintechTableTransactionDetailsAddIndexDistributor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("ALTER TABLE `samcomf_investor_db`.`kfintechTableTransactionDetails` ADD INDEX (`distributor`);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement("ALTER TABLE `kfintechTableTransactionDetails` DROP INDEX `distributor`;");
    }
}
