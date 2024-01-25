<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrokargeMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brokarge_master_plan', function (Blueprint $table) {
            $table->id();
             $table->string('scheme_code', 100)->nullable()->comment('scheme code');
             $table->string('sheme_Name', 265)->nullable()->comment('Scheme Name');
            $table->string('plan_type', 50)->nullable()->comment('Business or Professional');
            $table->double('first_year', 10,2)->nullable()->comment('1st year commission');
            $table->string('second_year', 250)->nullable()->comment('2st year commission');
            $table->string('b30', 50)->nullable()->comment('b30 commission');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('scheme_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brokarge_master');
    }
}
