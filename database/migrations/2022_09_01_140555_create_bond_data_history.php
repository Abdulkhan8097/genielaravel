<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBondDataHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emosi_bond_data_history', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('symbol', 20)->comment('Symbol');
            $table->date('record_date')->comment('Date for which data got fetched');
            $table->decimal('open', 25, 4)->nullable()->comment('Market open value for that day');
            $table->decimal('high', 25, 4)->nullable()->comment('Market day high value for that day');
            $table->decimal('low', 25, 4)->nullable()->comment('Market day low value for that day');
            $table->decimal('close', 25, 4)->nullable()->comment('Market day close value/index value for that day');
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
        Schema::dropIfExists('emosi_bond_data_history');
    }
}
