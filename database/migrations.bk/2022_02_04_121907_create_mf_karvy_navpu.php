<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMfKarvyNavpu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mf_karvy_navpu', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('karvy_amc',256)->nullable()->comment('Name of amc');
            $table->string('karvy_scheme',256)->nullable()->comment('scheme name');
            $table->string('karvy_schmen_plan',256)->nullable()->comment('Scheme Plan');
            $table->string('karvy_schmen_code',256)->nullable()->comment('Scheme Code');
            $table->string('karvy_plan_code',256)->nullable()->comment('Plan Code');
            $table->decimal('karvy_nav',25,4)->nullable()->comment('NAV');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('karvy_date')->nullable()->useCurrent()->comment('Karvy Date');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('karvy_amc');
            $table->index('karvy_scheme');
            $table->index('karvy_nav');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mf_karvy_navpu');
    }
}
