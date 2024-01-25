<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorCartItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('invdb')->create('investor_cart_items', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->unsignedBigInteger('cart_id')->nullable()->comment('Foreign key references id field investor_cart_order table');
            $table->string('order_type', 10)->nullable()->comment('Order type: Lumpsum/SIP etc.');
            $table->string('scheme_code', 40)->nullable()->comment('RTA scheme code');
            $table->string('buy_sell', 1)->nullable()->comment('Buy/Sell: P=purchase, R=redemption/sell');
            $table->string('buy_sell_type', 10)->nullable()->comment('FRESH/ADDITIONAL');
            $table->decimal('amount', 25, 4)->nullable()->comment('Purchase order amount');
            $table->decimal('quantity', 25, 4)->nullable()->comment('Redemption/Sell order quantity');
            $table->string('all_redeem', 5)->nullable()->comment('All units redeemed. Y = yes for other Y keep it blank');
            $table->string('min_redeem', 5)->nullable()->comment('Minimum redemption flag, when all_redeem is other than yes, then keeping its value Y = yes');
            $table->date('start_date')->nullable()->comment('Used for SIP orders and redemption order');
            $table->date('sip_end_date')->nullable()->comment('SIP end date');
            $table->string('frequency_type', 10)->nullable()->comment('SIP order frequency');
            $table->integer('installments')->nullable()->comment('Number of SIP installments');
            $table->decimal('installment_amount', 25, 4)->nullable()->comment('SIP installment amount');
            $table->char('first_order_flag', 1)->nullable()->comment('Same as lumpsum order, but it is getting created while placing SIP order and investor want to place the first order on the same date only');
            $table->unsignedBigInteger('first_order_ref_id')->nullable()->comment('id field from invstor_cart_order table, this id will be of LUMPSUM order');
            $table->char('order_mode', 1)->default('D')->nullable()->comment('Order mode: D = DEMAT, P = Physical');
            $table->string('created_by', 20)->nullable()->comment('Order placed by');
            $table->string('source', 20)->nullable()->comment('Source');
            $table->string('broker_id', 20)->nullable()->comment('ARN code of a broker/partner/distributor');
            $table->string('broker_euin', 20)->nullable()->comment('EUIN of a broker/partner/distributor');
            $table->string('sub_broker_arn', 20)->nullable()->comment('Sub broker ARN code');
            $table->string('sub_broker_internal_code', 20)->nullable()->comment('Branch or sub broker internal code');
            $table->string('ria_code', 20)->nullable()->comment('RIA code');
            $table->tinyInteger('euin_declaration_terms')->default(1)->nullable()->comment('euin_declaration_terms accepted 1=YES');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('status');
            $table->index('created_at');
            $table->foreign('cart_id')->references('id')->on('investor_cart_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('invdb')->dropIfExists('investor_cart_items');
    }
}
