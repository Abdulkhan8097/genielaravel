<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlertInvestorAccountUploadAddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding foreign key
        Schema::table('investor_account_upload', function (Blueprint $table) {
            $table->foreign('investor_account_id')->references('id')->on('investor_account')->constrained()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing foreign key
        Schema::table('investor_account_upload', function (Blueprint $table) {
            $table->dropForeign('investor_account_upload_investor_account_id_foreign');
        });
    }
}
