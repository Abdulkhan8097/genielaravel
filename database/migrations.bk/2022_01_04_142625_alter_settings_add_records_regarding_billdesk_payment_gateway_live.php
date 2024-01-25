<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSettingsAddRecordsRegardingBilldeskPaymentGatewayLive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("INSERT INTO `settings`(`id`, `key`, `value`, `status`, `created_at`, `updated_at`) VALUES(NULL, 'BILLDESK_PAYMENT_GATEWAY_CHECKSUM_KEY_LIVE', 'J3uGaevLBO5bco9IUUW87iziU7Tf8ywW', 1, '2021-12-22 22:46:52', '2021-12-22 22:46:52'), (NULL, 'BILLDESK_PAYMENT_GATEWAY_CHECKSUM_KEY_LIVE', 'J3uGaevLBO5bco9IUUW87iziU7Tf8ywW', 1, '2021-12-22 22:46:52', '2021-12-22 22:46:52');");
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
