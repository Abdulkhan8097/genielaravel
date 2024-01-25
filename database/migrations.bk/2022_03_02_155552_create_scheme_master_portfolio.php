<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemeMasterPortfolio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheme_master_portfolio', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('RTA_Scheme_Code', 50)->nullable()->comment('RTA scheme code');
            $table->dateTime('Invdate')->nullable()->comment('Investment date');
            $table->dateTime('Invenddate')->nullable()->comment('Investment end date');
            $table->integer('Srno')->default(0)->nullable()->comment('Serial no');
            $table->integer('Asect_code')->nullable()->comment('Asectcode which takes value from asect_mst');
            $table->integer('Sect_code')->nullable()->comment('Sectorcode which takes value from sect_mst');
            $table->string('Noshares', 18)->nullable()->comment('No of shares');
            $table->string('Mktval', 100)->nullable()->comment('Market value');
            $table->string('Aum', 100)->nullable()->comment('Aum (total market volume)');
            $table->string('Holdpercentage', 100)->nullable()->comment('Holding percentage');
            $table->string('Compname', 255)->nullable()->comment('Company name');
            $table->string('Sect_name', 50)->nullable()->comment('Sector name');
            $table->string('Asect_name', 50)->nullable()->comment('Asect name');
            $table->string('Rating', 50)->nullable()->comment('Rating');
            $table->integer('ratecode')->nullable()->comment('Ratecode');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('RTA_Scheme_Code');
            $table->index('status');
            $table->index('Invdate');
            $table->foreign('RTA_Scheme_Code')->references('RTA_Scheme_Code')->on('scheme_master');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheme_master_portfolio');
    }
}
