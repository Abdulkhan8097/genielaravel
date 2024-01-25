<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNseIndexPePbDivyield extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emosi_nse_index_pe_pb_divyield', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('symbol', 20)->comment('Symbol');
            $table->date('record_date')->comment('Date for which data got fetched');
            $table->decimal('pe', 25, 4)->nullable()->comment('P/E: Price to earning');
            $table->decimal('pb', 25, 4)->nullable()->comment('P/B: Price to book');
            $table->decimal('div_yield', 25, 4)->nullable()->comment('P/B: Price to book');
            $table->tinyInteger('status')->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('symbol');
            $table->index('record_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emosi_nse_index_pe_pb_divyield');
    }
}
