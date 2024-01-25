<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCompanyDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zauba_company_details', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('company_CIN', 255)->nullable()->comment('Compaany CIN');
            $table->string('company_Name', 255)->nullable()->comment('Compaany Name');
            $table->string('class_of_company', 255)->nullable()->comment('Class of Company');
            $table->string('date_of_incorporation', 255)->nullable()->comment('Date of Incorporation');
            $table->string('company_sub_category', 255)->nullable()->comment('Company Sub Category');
            $table->string('roc', 100)->nullable()->comment('RoC');
            $table->string('company_register_number', 150)->nullable()->comment('Register Number');
            $table->string('company_category', 150)->nullable()->comment('Comapany Category');
            $table->string('authorised_capital', 150)->nullable()->comment('Authorised Capital');
            $table->string('paidup_capital', 150)->nullable()->comment('PaidUp Capital');
            $table->string('company_status', 150)->nullable()->comment('Company Status');
            $table->string('company_email', 150)->nullable()->comment('Company Email');
            $table->string('company_address', 255)->nullable()->comment('Company Address');
            $table->tinyInteger('status')->nullable()->default(1)->comment('Status: 0=Inactive, 1=Active');
            $table->integer('script_no')->nullable()->comment('Script_no');
            $table->string('letter',5)->nullable()->comment('Letter');
            $table->integer('page_no')->nullable()->comment('Page No.');
            $table->integer('record_no')->nullable()->comment('Record No.');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('company_CIN');
            $table->index('company_Name');
            $table->index('class_of_company');
            $table->index('company_sub_category');
            $table->index('roc');
            $table->index('company_register_number');
            $table->index('company_category');
            $table->index('company_email');
            $table->index('created_at');
            $table->index('status');
            $table->index('script_no');
            $table->index('letter');
            $table->index('page_no');
            $table->index('record_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zauba_company_details');
    }
}
