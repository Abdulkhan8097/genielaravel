<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investor_account', function (Blueprint $table) {
            $table->id();
            $table->integer('pincode');
            //$table->string('address_type', 50)->nullable();
            //$table->string('permanent_address_line_1')->nullable();
            //$table->string('permanent_address_line_2')->nullable();
            //$table->string('permanent_address_line_3')->nullable();
            //$table->string('permanent_address_pincode', 20)->nullable();
            //$table->string('permanent_address_city', 100)->nullable();
            //$table->string('permanent_address_district', 100)->nullable();
            //$table->string('permanent_address_state', 100)->nullable();
            //$table->string('permanent_address_proof_submitted')->nullable();
            $table->string('address_proof_submitted')->nullable();
            $table->string('address_state', 100)->nullable();
            $table->string('income_range')->nullable();
            $table->bigInteger('investor_account_id')->unsigned();
            $table->bigInteger('investor_id')->unsigned();
            $table->bigInteger('is_ipv_upload')->unsigned();
            $table->bigInteger('kra_deficient')->unsigned();
            //$table->string('permanent_address_type', 50)->nullable();
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
        Schema::dropIfExists('investor_account');
    }
}
