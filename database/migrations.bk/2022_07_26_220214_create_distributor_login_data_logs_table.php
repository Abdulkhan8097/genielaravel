<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorLoginDataLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_login_data_logs', function (Blueprint $table) {
            $table->id();
            $table->text('login_details')->nullable()->comment('json response to store distributor login details');
            $table->string('created_by',100)->nullable()->comment('distributor login by user ');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_login_data_logs');
    }
}
