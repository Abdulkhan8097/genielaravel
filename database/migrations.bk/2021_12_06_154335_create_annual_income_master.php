<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnualIncomeMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annual_income_master', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('description')->comment('Description');
            $table->tinyInteger('order')->nullable()->comment('Order');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('status');
        });

        // inserting few records
        $insert_data = array(
            array('description' => 'Below Rs 1 lakh', 'order' => '1', 'status' => 1),
            array('description' => 'Rs 1 Lakh – Rs 5 Lakhs', 'order' => '2', 'status' => 1),
            array('description' => 'Rs 5 Lakhs – Rs 10 Lakhs', 'order' => '3', 'status' => 1),
            array('description' => 'Rs 10 Lakhs – Rs 25 Lakhs', 'order' => '4', 'status' => 1),
            array('description' => 'Rs 25 Lakhs – Rs 1 crore', 'order' => '5', 'status' => 1),
            array('description' => '> Rs. 1 crore', 'order' => '6', 'status' => 1),
            array('description' => 'Not Available', 'order' => '7', 'status' => 1),
        );
        DB::table('annual_income_master')->insert($insert_data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('annual_income_master');
    }
}
