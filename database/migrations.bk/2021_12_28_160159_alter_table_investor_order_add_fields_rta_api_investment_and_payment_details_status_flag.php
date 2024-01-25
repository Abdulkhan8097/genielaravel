<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInvestorOrderAddFieldsRtaApiInvestmentAndPaymentDetailsStatusFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('investor_order', function (Blueprint $table) {
            //$table->tinyInteger('investment_details_saved')->default(0)->nullable()->comment('Investment details saved api status: 0=pending, 1=failed, 2=success')->after('order_response');
            //$table->tinyInteger('payment_details_saved')->default(0)->nullable()->comment('Payment details saved api status: 0=pending, 1=failed, 2=success')->after('investment_details_saved');
        });

        // DB::statement("ALTER TABLE `investor_order` ADD CONSTRAINT `investor_order_rta_refno_foreign` FOREIGN KEY (`rta_refno`) REFERENCES `rta_api_execution_status`(`rta_refno`) ON DELETE CASCADE ON UPDATE CASCADE;");
        DB::statement("ALTER TABLE `investor_order` CHANGE `rta_refno` `rta_refno` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'RTA reference number';");
        //DB::statement("ALTER TABLE `investor_order` DROP FOREIGN KEY `investor_order_payment_bank_id_foreign`;");
        DB::statement("ALTER TABLE `investor_order` ADD CONSTRAINT `investor_order_payment_bank_id_foreign` FOREIGN KEY (`payment_bank_id`) REFERENCES `investor_account_bank_details`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('investor_order', function (Blueprint $table) {
            $table->dropColumn(['investment_details_saved', 'payment_details_saved']);
            // $table->dropForeign('investor_order_rta_refno_foreign');
        });
    }
}
