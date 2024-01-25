<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddingIndexOnRateCardPartnerwise extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `rate_card_partnerwise` ADD INDEX `idx_partner_arn`(`partner_arn`);');
        DB::statement('ALTER TABLE `rate_card_partnerwise` ADD INDEX `idx_scheme_code`(`scheme_code`);');
        DB::statement('ALTER TABLE `rate_card_partnerwise` ADD INDEX `idx_com_category`(`com_category`);');
        DB::statement('ALTER TABLE `rate_card_partnerwise` ADD INDEX `idx_month`(`month`);');
        DB::statement('ALTER TABLE `rate_card_partnerwise` ADD INDEX `idx_year`(`year`);');
        DB::statement('ALTER TABLE `rate_card_partnerwise` ADD INDEX `idx_status`(`status`);');
        DB::statement('ALTER TABLE `rate_card_schemewise` ADD INDEX `idx_scheme_code`(`scheme_code`);');
        DB::statement('ALTER TABLE `rate_card_schemewise` ADD INDEX `idx_com_category`(`com_category`);');
        DB::statement('ALTER TABLE `rate_card_schemewise` ADD INDEX `idx_month`(`month`);');
        DB::statement('ALTER TABLE `rate_card_schemewise` ADD INDEX `idx_year`(`year`);');
        DB::statement('ALTER TABLE `rate_card_schemewise` ADD INDEX `idx_status`(`status`);');
        DB::statement('ALTER TABLE `user_account` ADD INDEX `idx_commission_category`(`commission_category`);');
        DB::statement('ALTER TABLE `rate_card_additional` ADD INDEX `idx_partner_arn`(`partner_arn`);');
        DB::statement('ALTER TABLE `rate_card_additional` ADD INDEX `idx_scheme_code`(`scheme_code`);');
        DB::statement('ALTER TABLE `rate_card_additional` ADD INDEX `idx_month`(`month`);');
        DB::statement('ALTER TABLE `rate_card_additional` ADD INDEX `idx_year`(`year`);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP INDEX idx_partner_arn ON rate_card_partnerwise;');
        DB::statement('DROP INDEX idx_scheme_code ON rate_card_partnerwise;');
        DB::statement('DROP INDEX idx_com_category ON rate_card_partnerwise;');
        DB::statement('DROP INDEX idx_month ON rate_card_partnerwise;');
        DB::statement('DROP INDEX idx_year ON rate_card_partnerwise;');
        DB::statement('DROP INDEX idx_status ON rate_card_partnerwise;');
        DB::statement('DROP INDEX idx_scheme_code ON rate_card_schemewise;');
        DB::statement('DROP INDEX idx_com_category ON rate_card_schemewise;');
        DB::statement('DROP INDEX idx_month ON rate_card_schemewise;');
        DB::statement('DROP INDEX idx_year ON rate_card_schemewise;');
        DB::statement('DROP INDEX idx_status ON rate_card_schemewise;');
        DB::statement('DROP INDEX idx_commission_category ON user_account;');
        DB::statement('DROP INDEX idx_partner_arn ON rate_card_additional;');
        DB::statement('DROP INDEX idx_scheme_code ON rate_card_additional;');
        DB::statement('DROP INDEX idx_month ON rate_card_additional;');
        DB::statement('DROP INDEX idx_year ON rate_card_additional;');
    }
}
