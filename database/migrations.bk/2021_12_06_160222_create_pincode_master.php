<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePincodeMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pincode_master', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('country_code', 5)->comment('Country Code');
            $table->string('state_code', 5)->comment('State Code');
            $table->string('circle_name')->nullable()->comment('Circle Name');
            $table->string('region_name')->nullable()->comment('Region Name');
            $table->string('division_name')->nullable()->comment('Division Name');
            $table->string('office_name')->nullable()->comment('Office Name');
            $table->string('pincode', 20)->nullable()->comment('Pincode');
            $table->string('office_type')->nullable()->comment('Office Type');
            $table->string('district')->nullable()->comment('District');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('country_code');
            $table->index('state_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pincode_master');
    }
}
