<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePennytable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('folio_account_bank_details', function (Blueprint $table) {
            $table->tinyInteger('bank_verified')->default(0)->nullable()->comment('Possible values: 0 = pending, 1 = In progress, 2 = success, 3 = failed')->after('penny_verified');
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
