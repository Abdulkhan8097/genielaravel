<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOccupationMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('occupation_master', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('code', 10)->comment('Occupation Code');
            $table->string('description')->comment('Occupation Description');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('code');
            $table->index('status');
        });

        // inserting few records
        $insert_data = array(
            array('code'=>'SALR', 'description' => 'PRIVATE SECTOR SERVICE', 'status' => 1),
            array('code'=>'SALG', 'description' => 'PUBLIC SECTOR / GOVERNMENT SERVICE', 'status' => 1),
            array('code'=>'BUSI', 'description' => 'BUSINESS', 'status' => 1),
            array('code'=>'STUD', 'description' => 'STUDENT', 'status' => 1),
            array('code'=>'HSWF', 'description' => 'HOUSEWIFE', 'status' => 1),
            array('code'=>'PROF', 'description' => 'PROFESSIONAL', 'status' => 1),
            array('code'=>'AGRI', 'description' => 'AGRICULTURIST', 'status' => 1),
            array('code'=>'RETD', 'description' => 'RETIRED', 'status' => 1),
            array('code'=>'OTHR', 'description' => 'OTHERS', 'status' => 1),
            array('code'=>'LABO', 'description' => 'LABOUR', 'status' => 1),
            array('code'=>'SALR', 'description' => 'SALARIED', 'status' => 1),
            array('code'=>'SELF', 'description' => 'SELF EMPLOYED', 'status' => 1),
            array('code'=>'UNEM', 'description' => 'UNEMPLOYED', 'status' => 1),
            array('code'=>'NULL', 'description' => 'FOREX DEALER', 'status' => 1),
        );
        DB::table('occupation_master')->insert($insert_data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('occupation_master');
    }
}
