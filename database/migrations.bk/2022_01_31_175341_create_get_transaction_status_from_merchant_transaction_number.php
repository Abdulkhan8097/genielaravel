<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGetTransactionStatusFromMerchantTransactionNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('get_transaction_status_from_merchant_transaction_number', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->unsignedBigInteger('investor_order_id')->nullable()->comment('Foreign key references id field from investor_order table');
            $table->string('api_name', 255)->nullable()->comment('Decides which payment mode customer tried doing the transaction');
            $table->string('unique_transaction_number', 100)->nullable()->comment('Unique transaction number sent by us to either Billdesk/UPI gateway');
            $table->string('transaction_reference_number', 100)->nullable()->comment('transaction reference number sent by Billdesk after redirection from payment gateway');
            $table->string('bank_reference_number', 100)->nullable()->comment('Payment reference number');
            $table->text('request')->nullable()->comment('Request parameters');
            $table->text('response')->nullable()->comment('Response parameters');
            $table->string('payment_status', 20)->nullable()->comment('Payment Status: SUCCESS/FAILED');
            $table->string('payment_status_response', 200)->nullable()->comment('Payment status response, stores the failure reason, if any');
            $table->tinyInteger('processed')->default(0)->nullable()->comment('Processed: 0=No, 1=Yes');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
        });
        DB::statement("ALTER TABLE get_transaction_status_from_merchant_transaction_number ADD INDEX idx_investor_order_id(investor_order_id);");
        DB::statement("ALTER TABLE get_transaction_status_from_merchant_transaction_number ADD INDEX idx_record_status(status);");
        DB::statement("ALTER TABLE get_transaction_status_from_merchant_transaction_number ADD INDEX idx_record_processed(processed);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('get_transaction_status_from_merchant_transaction_number');
    }
}
