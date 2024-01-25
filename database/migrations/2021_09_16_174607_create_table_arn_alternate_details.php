<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableArnAlternateDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arn_alternate_details', function (Blueprint $table) {
            $table->id();
            $table->string('arn', 100)->nullable()->comment('ARN number');
            $table->string('email', 255)->nullable()->comment('Email ID(s) can have multiple values separated by comma');
            $table->string('mobile', 255)->nullable()->comment('Mobile Number(s) can have multiple values separated by comma');
            $table->tinyInteger('status')->nullable()->default(1)->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('arn');
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
        Schema::dropIfExists('arn_alternate_details');
    }
}
