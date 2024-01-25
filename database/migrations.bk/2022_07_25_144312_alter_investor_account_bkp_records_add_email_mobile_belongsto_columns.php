<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorAccountBkpRecordsAddEmailMobileBelongstoColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding fields like email_belongsto & mobile_belongsto into MySQL table: investor_account_bkp_records
        Schema::table('investor_account_bkp_records', function (Blueprint $table) {
            $table->string('email_belongsto', 100)->nullable()->comment('email belongs to - declaration')->after('email');
            $table->string('mobile_belongsto', 100)->nullable()->comment('mobile belongs to - declaration')->after('mobile');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added fields like email_belongsto & mobile_belongsto from MySQL table: investor_account_bkp_records
        Schema::table('investor_account_bkp_records', function (Blueprint $table) {
            $table->dropColumn(['email_belongsto', 'mobile_belongsto']);
        });
    }
}
