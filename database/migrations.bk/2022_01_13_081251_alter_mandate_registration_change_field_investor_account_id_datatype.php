<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMandateRegistrationChangeFieldInvestorAccountIdDatatype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // chaging datatype of field investor_account_id
        DB::statement("ALTER TABLE `mandate_registrations` CHANGE `investor_account_id` `investor_account_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT 'Foreign key references id field from investor_account table';");
        Schema::table('mandate_registrations', function (Blueprint $table) {
            $table->index('investor_account_id');
            $table->index('umrn');
            $table->foreign('investor_account_id')->references('id')->on('investor_account');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // reverting table structure the way it was before modification
        Schema::table('mandate_registrations', function (Blueprint $table) {
            $table->dropForeign('mandate_registrations_investor_account_id_foreign');
        });
        DB::statement("ALTER TABLE `mandate_registrations` CHANGE `investor_account_id` `investor_account_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Investor Id';");
    }
}
