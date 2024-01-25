<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Otp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp', function (Blueprint $table){
            $table->id();
            $table->string('email_id',100)->comment('email id');
            $table->string('otp',10)->comment('otp');
            $table->tinyInteger('is_verified')->default(0)->comment('1=>success');
            $table->string('used_for',100)->nullable()->comment('used for eg: mobile verification etc');
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
        Schema::dropIfExists('otp');
    }
}
