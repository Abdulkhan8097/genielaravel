<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteDataIndexHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_data_index_history', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('symbol', 20)->default(0)->comment('Index symbol');
            $table->decimal('open', 25, 4)->nullable()->comment('Market open value for that day');
            $table->decimal('high', 25, 4)->nullable()->comment('Market day high value for that day');
            $table->decimal('low', 25, 4)->nullable()->comment('Market day low value for that day');
            $table->decimal('close', 25, 4)->nullable()->comment('Market day close value/Index value for that day');
            $table->decimal('ltp', 25, 4)->nullable()->comment('LTP for that day');
            $table->unsignedBigInteger('volume')->nullable()->comment('Volume for that day');
            $table->decimal('margin_of_safety', 25, 4)->nullable()->comment('Margin of safety for that day');
            $table->date('index_date')->comment('Index date');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->unique(['symbol', 'index_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quote_data_index_history');
    }
}
