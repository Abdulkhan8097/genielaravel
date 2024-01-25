<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRateCardAdditional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_card_additional', function (Blueprint $table) {
            $table->id();
            $table->string('scheme_code')->comment('Scheme Code');
            $table->string('scheme_name')->comment('Scheme Name');
            $table->string('partner_arn')->comment('Partner ARN');
            $table->string('special_additional_first_year_trail')->comment('Special Additional First Year Trail');
            $table->string('special_additional_first_year_trail_for_b30')->comment('Special Additional First Year Trail for B30');
            $table->string('month')->nullable()->comment('month');
            $table->string('year')->nullable()->comment('year');
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
        Schema::dropIfExists('rate_card_additional');
    }
}
