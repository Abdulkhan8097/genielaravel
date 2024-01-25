<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePartnersRankmfCurrentAum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners_rankmf_current_aum', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('client_id', 100)->comment('Client ID');
            $table->string('broker_id', 100)->comment('Broker ID');
            $table->decimal('total_aum', 25, 4)->nullable()->comment('Total aum');
            $table->decimal('equity_aum', 25, 4)->nullable()->comment('Equity aum');
            $table->decimal('debt_aum', 25, 4)->nullable()->comment('Debt aum');
            $table->decimal('hybrid_aum', 25, 4)->nullable()->comment('Hybrid aum');
            $table->decimal('others_aum', 25, 4)->nullable()->comment('Others aum');
            $table->decimal('commodity_aum', 25, 4)->nullable()->comment('Commodity aum');
            $table->tinyInteger('status')->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('broker_id');
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
        Schema::dropIfExists('partners_rankmf_current_aum');
    }
}
