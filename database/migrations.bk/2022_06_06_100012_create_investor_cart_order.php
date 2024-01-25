<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorCartOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('invdb')->create('investor_cart_order', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->unsignedBigInteger('folio_investor_detail_id')->nullable()->comment('Foreign key references id field from folio_investor_detail table');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('folio_investor_detail_id');
            $table->index('status');
            $table->index('created_at');
            $table->foreign('folio_investor_detail_id')->references('id')->on('folio_investor_detail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('invdb')->dropIfExists('investor_cart_order');
    }
}
