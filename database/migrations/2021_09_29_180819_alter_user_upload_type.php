<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserUploadType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `user_account_upload`
        CHANGE `upload_type` `upload_type` tinyint NULL DEFAULT '0' COMMENT '1=Cheque, 2=BR, 3=ASL, 4=ARN' AFTER `uploaded_image_original`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
