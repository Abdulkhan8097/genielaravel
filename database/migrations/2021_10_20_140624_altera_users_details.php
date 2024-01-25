<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlteraUsersDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column appointment_link
        Schema::table('users_details', function (Blueprint $table) {
            $table->string('appointment_link', 255)->nullable()->comment('user appointment link')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column appointment_link
        Schema::table('users_details', function (Blueprint $table) {
            $table->dropColumn(['appointment_link']);
        });
    }
}
