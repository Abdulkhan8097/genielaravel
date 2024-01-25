<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_video', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->unique();
            $table->string('gif')->nullable();;
            $table->string('image')->nullable();;
            $table->string('company_name')->nullable();;
            $table->string('video')->nullable();;
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
        Schema::dropIfExists('users_video');
    }
}
