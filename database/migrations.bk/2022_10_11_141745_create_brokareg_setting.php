<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrokaregSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brokerage_category_partner_schemewise', function (Blueprint $table) {
            $table->id()->comment('Serial Id');
            $table->string('scheme_code',100)->nullable()->comment('Scheme Code');
            $table->string('scheme_name',100)->nullable()->comment('Scheme Name');
            $table->string('arn',100)->nullable()->comment('Partner ARN');;
            $table->string('category')->nullable()->comment('Partner Category');
             $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('scheme_code');
            $table->index('arn');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brokerage_category_partner_schemewise');
    }
}
