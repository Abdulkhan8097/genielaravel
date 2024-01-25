<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActiveShare extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('active_share', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('schemecode',120)->nullable()->comment('Schemecode accord');
            $table->string('indexcode', 120)->nullable()->comment('IndexCode accord');
            $table->string('indexname', 120)->nullable()->comment('IndexCode accord');
            $table->string('isin', 120)->nullable()->comment('isin accord');
            $table->string('symbol', 120)->nullable()->comment('Symbol accord');
            $table->string('compname', 120)->nullable()->comment('Company Name');
            $table->decimal('aum',25,4)->nullable()->comment('Company AUM');
            $table->decimal('holdpercentage',25,4)->nullable()->comment('Holdpercentage Company');
            $table->string('index_name_master', 120)->nullable()->comment('index name');
            $table->decimal('index_weightage', 25,4)->nullable()->comment('index weightage');
            $table->decimal('abs_diff', 25,4)->nullable()->comment('absolute different');
            $table->decimal('active_share_contribution', 25,4)->nullable()->comment('active share contribution');
            $table->date('active_share_date')->nullable()->comment('its date of day active share');
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
        Schema::dropIfExists('active_share');
    }
}
