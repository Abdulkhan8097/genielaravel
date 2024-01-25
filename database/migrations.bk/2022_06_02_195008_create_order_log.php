<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `investore_oder_log` ADD `investor_personal_order` TEXT NULL AFTER `sub_broker_arn`;"); 
        DB::statement("ALTER TABLE `investore_oder_log` ADD `investor_nominee_details` TEXT NULL AFTER `sub_broker_arn`;"); 
        DB::statement("ALTER TABLE `investore_oder_log` ADD `investor_bank_details` TEXT NULL AFTER `sub_broker_arn`;");
        DB::statement("ALTER TABLE `investore_oder_log` ADD `investor_tax_pay_details` TEXT NULL AFTER `sub_broker_arn`;"); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('');
    }
}
