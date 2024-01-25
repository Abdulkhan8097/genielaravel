<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRateCardPartnerwise extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_card_partnerwise', function (Blueprint $table) {
            $table->id();
            $table->string('partner_arn')->nullable()->comment('Partner ARN');
            $table->string('scheme_code')->nullable()->comment('Scheme code');
            $table->string('scheme_name')->nullable()->comment('Scheme name');
            $table->string('com_category')->nullable()->comment('Commission category P -> Professional B -> Business');
            $table->string('first_year_trail')->nullable()->comment('First year Trail');
            $table->string('second_year_trail')->nullable()->comment('Second year Trail');
            $table->string('b30')->nullable()->comment('Additional trail for b30');
            $table->string('month')->nullable()->comment('month');
            $table->string('year')->nullable()->comment('year');
            $table->string('status')->nullable()->comment('Status')->default('1');
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
        Schema::dropIfExists('rate_card_partnerwise');
    }
}
