<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrokaragePartnerList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brokarage_partner_list', function (Blueprint $table) {
            $table->id();
            $table->string('scheme_code', 100)->nullable()->comment('scheme code');
            $table->string('partner_arn', 265)->nullable()->comment('partner arn');
            $table->string('plan_type', 50)->nullable()->comment('Business or Professional');
            $table->string('first_year', 245)->nullable()->comment('1st year commission');
            $table->string('second_year', 250)->nullable()->comment('2st year commission');
            $table->string('b30', 254)->nullable()->comment('b30 commission');
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
        Schema::dropIfExists('brokarage_partner_list');
    }
}
