<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorMpanelKfinSaveApiLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_mpanel_kfin_save_api_log', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('arn', 10)->nullable()->comment('arn code');
            $table->string('type', 20)->nullable()->comment('distributor category type');
            $table->string('api_name', 255)->nullable()->comment('API used');
            $table->text('request')->nullable()->comment('request send to api');
            $table->text('response')->nullable()->comment('response get from api');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_mpanel_kfin_save_api_log');
    }
}
