<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FoliobankAltertable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('folio_account_bank_details', function (Blueprint $table) {
            $table->string('uploaded_filename', 255)->nullable()->comment('Uploaded filename')->after('bank_details_saved');
            $table->string('uploaded_original_filename', 255)->nullable()->comment('Uploaded original filename')->after('uploaded_filename');
            $table->string('upload_type', 255)->nullable()->comment('Possible values : Cancelled Cheque, Bank Statement')->after('uploaded_original_filename');
            $table->tinyInteger('upload_status')->default(0)->nullable()->comment('Possible values: 0 = pending, 1 = accept, 2 = rejected')->after('upload_type');
            $table->tinyInteger('penny_verified')->default(0)->nullable()->comment('Possible values: 0 = pending, 1 = failed, 2 = success')->after('upload_type');
            $table->dateTime('penny_created_at')->nullable()->comment('Penny created datetime')->after('penny_verified');
            $table->index('upload_status');
            $table->index('penny_verified');
            $table->index('penny_created_at');
        });
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
