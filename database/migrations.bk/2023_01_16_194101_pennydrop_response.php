<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PennydropResponse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('pennydrop_api_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investor_account_id')->nullable()->comment('Foreign key references id field from investor_account table');
            $table->unsignedBigInteger('investor_order_id')->nullable()->comment('Foreign key references id field from investor_order table');
            $table->unsignedBigInteger('vendor_id')->nullable()->comment('Foreign key references id field from pennydrop_vendor_master table');
            $table->string('pan', 20)->nullable()->comment('Investor pan');
            $table->string('name', 100)->nullable()->comment('BC customer\'s Name. This will be used in the message send to NFS, Max 20 char.');
            $table->string('penny_complete_by',20)->nullable()->comment('who penny create Investor ya partner');
            $table->string('localTxnDtTime',50)->nullable()->comment('Transaction Date & Time (YYYYMMDDHHmmss)');
            $table->string('beneAccNo', 30)->nullable()->comment('Beneficiary Account Number');
            $table->string('beneIFSC',20)->nullable()->comment('Beneficiary bank\'s IFSC Code- 11 digit');
            $table->decimal('amount', 25, 4)->nullable()->comment('Transaction amount to be transferred');
            $table->string('tranRefNo',50)->nullable()->comment('Transaction Reference Number that uniquely identifies the transaction at BC end');
            $table->string('paymentRef', 100)->nullable()->comment('Transaction Info, will be send in NFS message- max 50 char');
            $table->integer('mobile')->nullable()->comment('Remittance Mobile number- Max 10 char');
            $table->string('retailerCode', 100)->nullable()->comment('Retailer/CSP code (BC Specific Retailer/CSP Code).value to be passed as rcode');
            $table->string('passCode', 100)->nullable()->comment('PassCode been generated by IMPS');
            $table->string('bcID', 100)->nullable()->comment('Merchant\'s BCID');
            $table->string('crpId', 100)->nullable()->comment('Corporate ID (mandatory only for Nodal account)');
            $table->string('crpUsr', 100)->nullable()->comment('SMS Corporate User (mandatory only for Nodal account)');
            $table->integer('ActCode')->nullable()->comment('ActCode Value it is error code');
            $table->string('Response', 255)->nullable()->comment('ActCode Description');
            $table->string('BankRRN', 50)->nullable()->comment('Bank Transaction Reference Number');
            $table->string('BeneName', 255)->nullable()->comment('Beneficiary Name');
            $table->tinyInteger('api_status')->nullable()->comment('API status');
            $table->longText('api_request')->nullable()->comment('api request');
            $table->longText('api_response')->nullable()->comment('api request');
            $table->string('request_IP', 100)->nullable()->comment('Request ip');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('pan');
            $table->index('tranRefNo');
            $table->index('created_at');
            $table->foreign('investor_account_id')->references('id')->on('investor_account');
            $table->foreign('investor_order_id')->references('id')->on('investor_order');
        });
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
