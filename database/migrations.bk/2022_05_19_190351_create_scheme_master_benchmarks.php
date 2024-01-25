<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemeMasterBenchmarks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('invdb')->create('scheme_master_benchmarks', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('RTA_Scheme_Code', 50)->nullable()->comment('RTA scheme code');
            $table->integer('index_code')->nullable()->comment('Scheme benchmark index');
            $table->string('index_name', 100)->nullable()->comment('Scheme benchmark index name');
            $table->tinyInteger('status')->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('RTA_Scheme_Code');
            $table->foreign('RTA_Scheme_Code')->references('RTA_Scheme_Code')->on('scheme_master')->onUpdate('cascade')->onDelete('cascade');
            $table->index('status');
        });

        DB::connection('invdb')->table('scheme_master_benchmarks')->insert(
            array(
                array('RTA_Scheme_Code' => 'FCRG',
                      'index_code' => 312,
                      'index_name' => 'Nifty 500 Index TRI',
                      'status' => 1
                    ),
                array('RTA_Scheme_Code' => 'FCDG',
                      'index_code' => 312,
                      'index_name' => 'Nifty 500 Index TRI',
                      'status' => 1
                    ),
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheme_master_benchmarks');
    }
}
