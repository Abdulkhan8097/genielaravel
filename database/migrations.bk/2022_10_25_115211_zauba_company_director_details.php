<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ZaubaCompanyDirectorDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zauba_company_director_details', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('company_CIN', 255)->nullable()->comment('Compaany CIN');
            $table->string('company_directors_name', 255)->nullable()->comment('Compaany Directors Name');
            $table->string('din', 100)->nullable()->comment('Compaany Directors Name');
            $table->string('designation', 100)->nullable()->comment('Designation');
            $table->string('appointment_date', 100)->nullable()->comment('Appointment Date');
            $table->tinyInteger('status')->nullable()->default(1)->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('company_CIN');
            $table->index('created_at');
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
        Schema::dropIfExists('zauba_company_director_details');
    }
}
