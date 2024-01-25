<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexOnKfintecTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //adding migrations for KFINTEC tables
        DB::statement('ALTER TABLE `kfintec_Postendorsement_TransactionDetails` ADD KEY `idx_agent_code`(`agent_code`(100));');
        DB::statement('ALTER TABLE `kfintec_Postendorsement_TransactionDetails_final` ADD KEY `idx_agent_code`(`agent_code`(100));');
        DB::statement('ALTER TABLE `kfintec_Postendorsement_TransactionDetails` ADD KEY `idx_trdt`(`trdt`);');
        DB::statement('ALTER TABLE `kfintec_Postendorsement_TransactionDetails_final` ADD KEY `idx_final_trdt`(`trdt`);');
        DB::statement('ALTER TABLE `kfintec_MasterSipStp_TransactionDetails` ADD KEY `idx_sip_agent_code`(`agent_code`);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop
        DB::statement('ALTER TABLE `kfintec_Postendorsement_TransactionDetails` DROP KEY `idx_agent_code`;');
        DB::statement('ALTER TABLE `kfintec_Postendorsement_TransactionDetails_final` DROP KEY `idx_agent_code`;');
        DB::statement('ALTER TABLE `kfintec_Postendorsement_TransactionDetails` DROP KEY `idx_trdt`;');
        DB::statement('ALTER TABLE `kfintec_Postendorsement_TransactionDetails_final` DROP KEY `idx_final_trdt`;');
        DB::statement('ALTER TABLE `kfintec_MasterSipStp_TransactionDetails` DROP KEY `idx_sip_agent_code`;');
    }
}
