<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorLoginDataLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investor_login_data_logs', function (Blueprint $table) {
            $table->id();
            $table->text('login_details')->nullable()->comment('json response to store investor login details');
            $table->string('created_by',100)->nullable()->comment('investor login by user ');
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
        Schema::dropIfExists('investor_login_data_logs');
    }
}
