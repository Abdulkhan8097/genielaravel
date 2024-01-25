<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrokerageAnnexureApiLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brokerage_annexure_api_logs', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('fund', 5)->nullable()->comment('Fund code');
            $table->string('brok_code', 20)->nullable()->comment('Broker ARN');
            $table->date('cycle_date')->nullable()->comment('Brokerage cycle date');
            $table->string('expiry_time', 20)->nullable()->comment('Link to be expired in how many minutes');
            $table->string('request_from', 20)->nullable()->comment('Request source');
            $table->string('file_password', 20)->nullable()->comment('Password used for opening the downloaded report file');
            $table->string('return_code', 20)->nullable()->comment('API return code');
            $table->string('return_msg')->nullable()->comment('API return message');
            $table->string('request_no')->nullable()->comment('API response request number');
            $table->text('annexure_url')->nullable()->comment('Annexure file downloading url');
            $table->text('response')->nullable()->comment('API response');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('brok_code');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brokerage_annexure_api_logs');
    }
}
