<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserAccountUploadChangeColumnUploadType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Changing column upload_type comment
        Schema::table('user_account_upload', function (Blueprint $table) {
            // $table->tinyInteger('upload_type')->default(0)->nullable()->comment('1=Cheque, 2=BR, 3=ASL')->change();
            $table->index('upload_type');
        });
        DB::statement("ALTER TABLE user_account_upload CHANGE COLUMN upload_type upload_type TINYINT NULL DEFAULT 0 COMMENT '1=Cheque, 2=BR, 3=ASL';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Changing column upload_type comment as earlier one
        Schema::table('user_account_upload', function (Blueprint $table) {
            // $table->tinyInteger('upload_type')->comment('1=Cheque')->change();
            $table->dropIndex('upload_type');
        });
        DB::statement("ALTER TABLE user_account_upload CHANGE COLUMN upload_type upload_type TINYINT COMMENT '1=Cheque';");
    }
}
