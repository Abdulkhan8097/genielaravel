<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMfRtaDividendUpload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mf_rta_dividend_upload', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('amc',256)->nullable()->comment('Mf Rta Dividend of amc');
            $table->string('scheme',256)->nullable()->comment('Mf Rta Dividend name');
            $table->string('plan',256)->nullable()->comment('Mf Rta Dividend Plan');
            $table->string('scheme_code',256)->nullable()->comment('Mf Rta Dividend Code');
            $table->string('plan_code',256)->nullable()->comment('Mf Rta Dividend Code');
            $table->decimal('navpu',25,4)->nullable()->comment('Mf Rta Dividend NAVPU');
            $table->dateTime('dividend_date')->nullable()->useCurrent()->comment('Mf Rta Dividend Date');
            $table->decimal('repurchase_price',25,4)->nullable()->comment('Mf Rta Dividend Re-Purchase Price');
            $table->decimal('sale_price',25,4)->nullable()->comment('Mf Rta Dividend sale Price');
            $table->decimal('pu_ind',25,4)->nullable()->comment('Mf Rta Dividend Div. P/U IND');
            $table->decimal('pu_corp',25,4)->nullable()->comment('Mf Rta Dividend Div. P/U CORP');

            $table->decimal('cum_nav',25,4)->nullable()->comment('Mf Rta Dividend Cum NAV');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('amc');
            $table->index('scheme');
            $table->index('navpu');
            $table->index('sale_price');
            $table->index('repurchase_price');
            $table->index('cum_nav'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mf_rta_dividend_upload');
    }
}
