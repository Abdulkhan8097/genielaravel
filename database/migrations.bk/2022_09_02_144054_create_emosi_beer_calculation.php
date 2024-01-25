<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmosiBeerCalculation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emosi_beer_calculation', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->date('record_date')->comment('Date for which data got fetched');
            $table->string('bond_symbol', 20)->comment('Bond/Government security symbol');
            $table->decimal('g_sec_yield', 25, 4)->nullable()->comment('Government security closing value. This field data taken from table: emosi_bond_data_history > close field');
            $table->string('index_symbol', 20)->comment('NSE index symbol');
            $table->decimal('pe', 25, 4)->nullable()->comment('Price to earnings ratio. This field data taken from table: emosi_nse_index_pe_pb_yield > pe field');
            $table->decimal('earnings_yield', 25, 4)->nullable()->comment('Earnings yield = ((1/g_sec_yield) * 100)');
            $table->decimal('beer', 25, 4)->nullable()->comment('Bond Yield to Equity Earnings Return. BEER = g_sec_yield/pe');
            $table->decimal('median_beer', 25, 4)->nullable()->comment('Median BEER value = ((MEDIAN(<ALL AVAILABLE BEER VALUES>)/<BEER>) * 100)');
            $table->tinyInteger('status')->nullable()->default(1)->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('record_date');
            $table->index('bond_symbol');
            $table->index('index_symbol');
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
        Schema::dropIfExists('emosi_beer_calculation');
    }
}
