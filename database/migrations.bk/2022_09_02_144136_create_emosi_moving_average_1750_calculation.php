<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmosiMovingAverage1750Calculation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emosi_moving_average_1750_calculation', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('index_symbol', 20)->comment('NSE index symbol');
            $table->date('record_date')->comment('Date for which data got fetched');
            $table->decimal('index_value', 25, 4)->nullable()->comment('Market day close value/Index value for that day');
            $table->decimal('ma_1750', 25, 4)->nullable()->comment('Moving average for 1750 days');
            $table->decimal('deviation_1750', 25, 4)->nullable()->comment('Deviation for 1750 days');
            $table->decimal('emosi_median_deviation_from_ma_1750', 25, 4)->nullable()->comment('EMOSI median deviation from moving average of 1750 days');
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
        Schema::dropIfExists('emosi_moving_average_1750_calculation');
    }
}
