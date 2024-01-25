<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nav_history', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('AMC', 50)->nullable()->comment('AMC Name');
            $table->string('Scheme', 255)->nullable()->comment('Scheme Name');
            $table->string('Plan', 255)->nullable()->comment('Scheme Plan');
            $table->string('Scheme_Code', 50)->nullable()->comment('Scheme Code');
            $table->string('Plan_Code', 50)->nullable()->comment('Plan Code');
            $table->decimal('NAV',25,4)->nullable()->default(0)->comment('Current NAV');
            $table->date('NAV_Date')->nullable()->comment('NAV Date');
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
        Schema::dropIfExists('nav_history');
    }
}
