<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailContentLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_content_log', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('channelName')->nullable()->comment('Channel Name');
            $table->dateTime('fromDate')->nullable()->comment('From Date');
            $table->dateTime('toDate')->nullable()->comment('To Date');
            $table->longtext('logData')->nullable()->comment('Log Data');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_content_log');
    }
}
