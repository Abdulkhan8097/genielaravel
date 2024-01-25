<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePartnersRankmfBdmList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners_rankmf_bdm_list', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->text('pincode', 15)->nullable()->comment('Pincode');
            $table->string('employee_code', 191)->nullable()->comment('Employee Code');
            $table->string('branch_manager', 191)->nullable()->comment('Branch Manager');
            $table->string('area_manager', 191)->nullable()->comment('Area Manager');
            $table->string('circle_manager', 191)->nullable()->comment('Circle Manager');
            $table->string('national_manager', 191)->nullable()->comment('National Manager');
            $table->string('name', 191)->nullable()->comment('BDM name');
            $table->string('email', 191)->nullable()->comment('BDM email');
            $table->string('mobile', 15)->nullable()->comment('BDM mobile number');
            $table->text('address', 15)->nullable()->comment('BDM address');
            $table->tinyInteger('status')->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
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
        Schema::dropIfExists('partners_rankmf_bdm_list');
    }
}
