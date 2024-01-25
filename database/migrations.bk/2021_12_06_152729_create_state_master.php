<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStateMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('state_master', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('country_code', 5)->comment('Country Code');
            $table->string('state_name')->comment('State Name');
            $table->string('state_code', 5)->comment('State Code');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('country_code');
            $table->index('state_code');
            $table->index('status');
        });

        // inserting few records
        $insert_data = array(
            array('country_code'=>'IN', 'state_name' => 'Andhra Pradesh ', 'state_code' => 'AP', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Arunachal Pradesh ', 'state_code' => 'AR', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Assam ', 'state_code' => 'AS', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Bihar ', 'state_code' => 'BR', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Chandigarh', 'state_code' => 'CD', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Chattisgarh ', 'state_code' => 'CG', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Dadra and Nagar Haveli ', 'state_code' => 'DN', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Daman and Diu ', 'state_code' => 'DD', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Delhi ', 'state_code' => 'DL', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Goa ', 'state_code' => 'GA', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Gujarat ', 'state_code' => 'GJ', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Haryana ', 'state_code' => 'HR', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Himachal Pradesh ', 'state_code' => 'HP', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Jammu and Kashmir ', 'state_code' => 'JK', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Jharkhand ', 'state_code' => 'JH', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Karnataka ', 'state_code' => 'KA', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Kerala ', 'state_code' => 'KL', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Madhya Pradesh ', 'state_code' => 'MP', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Maharashtra ', 'state_code' => 'MH', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Manipur ', 'state_code' => 'MN', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Meghalaya ', 'state_code' => 'ML', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Mizoram ', 'state_code' => 'MZ', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Nagaland ', 'state_code' => 'NL', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Orissa ', 'state_code' => 'OR', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Pondicherry ', 'state_code' => 'PD', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Punjab ', 'state_code' => 'PB', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Rajasthan ', 'state_code' => 'RJ', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Sikkim ', 'state_code' => 'SK', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Tamil Nadu ', 'state_code' => 'TN', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Tripura ', 'state_code' => 'TR', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Uttar Pradesh ', 'state_code' => 'UP', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Uttarakhand ', 'state_code' => 'UK', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'West Bengal ', 'state_code' => 'WB', 'status' => 1),
            array('country_code'=>'IN', 'state_name' => 'Telangana', 'state_code' => 'TS', 'status' => 1),
        );
        DB::table('state_master')->insert($insert_data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('state_master');
    }
}
