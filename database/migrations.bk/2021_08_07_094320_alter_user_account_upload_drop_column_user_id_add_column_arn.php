<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserAccountUploadDropColumnUserIdAddColumnArn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Adding column ARN as reference key
        Schema::table('user_account_upload', function (Blueprint $table) {
            $table->string('ARN', 100)->comment('ARN number refers to user_account table')->after('id');
            $table->index('ARN');
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Removing added column ARN as reference key
        Schema::table('user_account_upload', function (Blueprint $table) {
            $table->dropIndex('ARN');
            $table->dropColumn('ARN');
            $table->integer('user_id')->comment('reference user id user_account');
        });
    }
}
