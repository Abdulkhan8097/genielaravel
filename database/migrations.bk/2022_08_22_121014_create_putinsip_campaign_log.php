<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePutinsipCampaignLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('putinsip_campaign_log', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('ARN',100)->nullable()->comment('ARN code');
            $table->string('campaign',100)->nullable()->comment('Campaign Name. E.G: PutInSIP etc.');
            $table->string('category',100)->nullable()->comment('Email Category');
            $table->string('email_day',10)->nullable()->comment('Email execution day: E.G: T, T+3, T+5 etc.');
            $table->string('email_description',50)->nullable()->comment('Email description');
             $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=inactive, 1=active');
            $table->date('created_at')->nullable()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('ARN');
            $table->index('category');
            $table->index('email_day');
            $table->index('status');
            //$table->index('abm_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('putinsip_campaign_log');
    }
}
