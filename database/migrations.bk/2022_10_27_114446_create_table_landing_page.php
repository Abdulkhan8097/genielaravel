<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLandingPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_page', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->tinyInteger('brand')->default(5)->nullable()->comment('0:Samco, 1:RankMF, 2:StockBasket, 4:Kya Trade , 5:SAMCOMF');
            $table->string('from_site',100)->nullable()->comment('from site landing page');
            $table->string('description',200)->nullable()->comment('description campaign');
            $table->string('campaign_type',50)->nullable()->comment('campaign type');
            $table->string('campaign_platform',50)->nullable()->comment('campaign platform');
            $table->string('campaign_name',100)->nullable()->comment('campaign name');
            $table->string('flag',100)->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('from_site');
            $table->index('flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landing_page');
    }
}
