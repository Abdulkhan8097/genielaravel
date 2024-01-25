<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserAccountUpload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_account_upload', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('reference user id user_account');
            $table->string('uploaded_image',100)->comment('Uploaded image name');
            $table->string('uploaded_image_original',100)->comment('original uploaded image name');
            $table->tinyInteger('upload_type')->comment('1=Cheque');
            $table->tinyInteger('upload_status')->default(0)->comment('0=Pending, 1=Accept, 2=Reject');
            $table->string('reject_reason',200)->comment('Rejection reason');
            $table->dateTime('created_at')->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_account_upload');
    }
}
