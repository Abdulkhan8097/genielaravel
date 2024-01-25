<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmosiHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emosi_history', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('index_symbol', 20)->comment('NSE index symbol');
            $table->date('record_date')->comment('Date for which data got fetched');
            $table->decimal('median_beer', 25, 4)->nullable()->comment('Median BEER value. This field data taken from table: emosi_beer_calculation > median_bear');
            $table->decimal('emosi_median_deviation_from_ma_1750', 25, 4)->nullable()->comment('EMOSI median deviation from moving average of 1750 days. This field data taken from table: emosi_moving_average_1750_calculation > emosi_median_deviation_from_ma_1750');
            $table->decimal('emosi_value', 25, 4)->nullable()->comment('EMOSI value = 70% of BEER & 30% of MA1750');
            $table->decimal('rounded_emosi', 25, 4)->nullable()->comment('EMOSI rounded to ZERO decimal value');
            $table->tinyInteger('status')->nullable()->default(1)->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('index_symbol');
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
        Schema::dropIfExists('emosi_history');
    }
}
