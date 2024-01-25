<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRtaApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rta_api_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('investor_account_id')->unsigned();
            //$table->string('rta_refno', 20)->nullable()->comment('RTA referance number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rta_api_logs');
    }
}
