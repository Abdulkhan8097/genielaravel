<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableKydapiLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('kydapi_logs');
        Schema::create('kydapi_logs', function (Blueprint $table) {
            $table->id();
            $table->string('arn', 100)->nullable()->comment('ARN number');
            $table->string('api_name', 100)->nullable()->comment('API used');
            $table->text('request')->nullable()->comment('Request');
            $table->text('response')->nullable()->comment('Response');
            $table->tinyInteger('status')->nullable()->default(1)->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('arn');
            $table->index('api_name');
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
        Schema::dropIfExists('kydapi_logs');
    }
}
