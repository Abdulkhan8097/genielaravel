<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorsInvestorsApiLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributors_investors_api_logs', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->unsignedBigInteger('investor_account_id')->nullable()->comment('Foreign key references id field from investor_account table');
            $table->string('api_name', 191)->nullable()->comment('API used');
            $table->text('request')->nullable()->comment('Request parameters');
            $table->text('response')->nullable()->comment('Response parameters');
            $table->string('requested_from', 20)->nullable()->default(1)->comment('Request came in from');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('investor_account_id', 'investor_account_id_index');
            $table->index('api_name', 'api_name_index');
            $table->index('status');
            $table->index('created_at');
            $table->foreign('investor_account_id', 'investor_account_id_foreign')->references('id')->on('investor_account');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributors_investors_api_logs');
    }
}
