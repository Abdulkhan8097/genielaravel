<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInvestorNomineeDetailsAddForeignKeyInvestorAccountId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding foreign key for field investor_account_id
        DB::statement("ALTER TABLE `investor_nominee_details` ADD CONSTRAINT `investor_nominee_details_investor_account_id_foreign` FOREIGN KEY (`investor_account_id`) REFERENCES `investor_account`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added foreign key for field investor_account_id
        Schema::table('investor_nominee_details', function (Blueprint $table) {
            $table->dropForeign('investor_nominee_details_investor_account_id_foreign');
        });
    }
}
