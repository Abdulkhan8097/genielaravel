<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappOptinApiLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp_optin_api_log', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->text('url')->nullable()->comment('api url');
            $table->string('mobile',20)->nullable()->comment('mobile number');
            $table->text('request')->nullable()->comment('Request data');
            $table->text('response')->nullable()->comment('Response data');
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
        Schema::dropIfExists('whatsapp_optin_api_log');
    }
}
