<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorOrderAddFieldEmailSent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding field email_sent in MySQL table: investor_order
        Schema::table('investor_order', function (Blueprint $table) {
            $table->tinyInteger('email_sent')->default(0)->nullable()->comment('Email sent: 0=pending, 1=success')->after('first_order_ref_id');
        });
        DB::statement("UPDATE `investor_order` SET `email_sent` = 1, `created_at` = `created_at`, `updated_at` = `updated_at` WHERE `email_sent` = 0 AND `order_status` = 2;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column email_sent
        Schema::table('investor_order', function (Blueprint $table) {
            $table->dropColumn(['email_sent']);
        });
    }
}
