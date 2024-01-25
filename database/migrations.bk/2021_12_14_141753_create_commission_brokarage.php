<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionBrokarage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_brokarage_survey', function (Blueprint $table) {
            $table->id();
             $table->string('distributor_id', 100)->nullable()->comment('distributor id');
            $table->string('business_segment', 50)->nullable()->comment('business segment');
            $table->double('aum', 10,2)->nullable()->comment('AUM');
            $table->string('servicing', 250)->nullable()->comment('Servicing');
            $table->string('team_member', 50)->nullable()->comment('Team members');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('distributor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commission_brokarage');
    }
}
