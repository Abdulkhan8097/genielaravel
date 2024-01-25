<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TextlocalSmsLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('textlocal_sms_log', function (Blueprint $table){
            $table->id();
            $table->string('for',100)->comment('for');
            $table->string('mobile',10)->comment('mobile');
            $table->string('status',20)->comment('status');
            $table->text('response_details')->nullable()->comment('response');
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
        Schema::dropIfExists('textlocal_sms_log');
    }
}
