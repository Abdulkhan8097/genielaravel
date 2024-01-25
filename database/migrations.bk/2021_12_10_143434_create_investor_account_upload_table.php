<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorAccountUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investor_account_upload', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('investor_account_id')->unsigned();
            $table->tinyInteger('upload_type')->nullable();
            $table->string('uploaded_original_filename');
            $table->tinyInteger('upload_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('investor_account_upload');
    }
}
