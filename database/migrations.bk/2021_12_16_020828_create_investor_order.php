<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE TABLE `investor_order` (
                        `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
                        `investor_account_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Foreign key references investor_id field from investor_account table',
                        `order_type` varchar(10) NULL DEFAULT 'lumpsum' COMMENT 'Order type: Lumpsum/SIP etc.',
                        `order_id` int(11) DEFAULT NULL COMMENT 'RTA order id',
                        `sip_reg_id` varchar(20) DEFAULT NULL COMMENT 'SIP registration id',
                        `amc_code` int(11) DEFAULT NULL COMMENT 'AMC code',
                        `scheme_code` varchar(40) DEFAULT NULL COMMENT 'RTA scheme code',
                        `folio_number` varchar(20) DEFAULT NULL COMMENT 'Folio number',
                        `buy_sell` varchar(1) DEFAULT NULL COMMENT 'Buy/Sell: P=purchase, R=redemption/sell',
                        `buy_sell_type` varchar(10) DEFAULT NULL COMMENT 'FRESH/ADDITIONAL',
                        `amount` DECIMAL(25, 4) DEFAULT NULL COMMENT 'Purchase order amount',
                        `quantity` DECIMAL(25, 4) DEFAULT NULL COMMENT 'Redemption/Sell order quantity',
                        `allottedunit` decimal(10,4) DEFAULT NULL COMMENT 'Successful purchase order alloted units',
                        `all_redeem` varchar(5) DEFAULT NULL COMMENT 'All units redeemed. Y = yes for other Y keep it blank',
                        `min_redeem` varchar(5) DEFAULT NULL COMMENT 'Minimum redemption flag, when all_redeem is other than yes, then keeping its value Y = yes',
                        `mandate_id` varchar(100) DEFAULT NULL COMMENT 'Mandate id used for SIP order',
                        `start_date` date DEFAULT NULL COMMENT 'Used for SIP orders and redemption order',
                        `frequency_type` varchar(10) DEFAULT NULL COMMENT 'SIP order frequency',
                        `installments` int(5) DEFAULT NULL COMMENT 'Number of SIP installments',
                        `installment_amount` varchar(15) DEFAULT NULL COMMENT 'SIP installment amount',
                        `first_order_flag` char(1) DEFAULT NULL COMMENT 'Same as lumpsum order, but it is getting created while placing SIP order and investor want to place the first order on the same date only',
                        `order_status` tinyint(1) DEFAULT NULL COMMENT 'Order Status: 0 = pending, 1 = failed, 2 = success, 3 = cancelled',
                        `order_status_response` varchar(200) DEFAULT NULL COMMENT '',
                        `order_response` varchar(200) DEFAULT NULL COMMENT '',
                        `order_link` varchar(200) DEFAULT NULL COMMENT '',
                        `order_payment_status` varchar(200) DEFAULT NULL COMMENT '',
                        `settno` int(11) NULL DEFAULT '0' COMMENT 'Settlement number, generally it will be same for orders placed on the same date',
                        `settlement_date` date DEFAULT NULL COMMENT 'date redemtion hits the pool',
                        `order_mode` char(1) NULL DEFAULT 'D' COMMENT 'Order mode: D = DEMAT, P = Physical',
                        `created_by` varchar(20) DEFAULT NULL COMMENT 'Order placed by',
                        `source` varchar(20) DEFAULT NULL COMMENT 'Source',
                        `broker_id` varchar(20) DEFAULT NULL COMMENT 'ARN code of a broker/partner/distributor',
                        `broker_euin` varchar(20) DEFAULT NULL COMMENT 'EUIN of a broker/partner/distributor',
                        `sub_broker_arn` varchar(20) DEFAULT NULL COMMENT 'Sub broker ARN code',
                        `sub_broker_internal_code` varchar(20) DEFAULT NULL COMMENT 'Branch or sub broker internal code',
                        `ria_code` varchar(20) DEFAULT NULL COMMENT 'RIA code',
                        `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Created date',
                        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Modified date',
                        PRIMARY KEY (`id`),
                        KEY `investor_order_order_type` (`order_type`),
                        KEY `investor_order_investor_order_order_id` (`order_id`),
                        KEY `investor_order_sip_reg_id` (`sip_reg_id`),
                        KEY `investor_order_start_date` (`start_date`),
                        KEY `investor_order_order_status` (`order_status`),
                        KEY `investor_order_buy_sell` (`buy_sell`),
                        KEY `investor_order_mandate_id` (`mandate_id`),
                        KEY `investor_order_broker_id` (`broker_id`),
                        KEY `investor_order_scheme_code` (`scheme_code`),
                        KEY `investor_order_created_at_index` (`created_at`),
                        KEY `investor_order_investor_account_id_index` (`investor_account_id`),
                        CONSTRAINT `investor_order_investor_account_id_foreign` FOREIGN KEY (`investor_account_id`) REFERENCES `investor_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('investor_order');
    }
}
