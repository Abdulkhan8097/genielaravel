<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DirectorDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('director_details', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('reference user id user_account');
            $table->string('name',100)->comment('Directors Name');
            $table->string('email',100)->comment('Directors email');
            $table->string('mobile',20)->comment('Directors mobile');
            $table->string('aadhar_number',50)->comment('Directors Aadhar Number');
            $table->tinyInteger('esign_status')->comment('0=Pending, 1=Done');            
            $table->dateTime('created_at')->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('director_details');
    }
}
