<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUsersVideo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding mobile, email, address & language field
        Schema::table('users_video', function (Blueprint $table) {
            $table->string('mobile')->nullable()->comment('Mobile Number')->after('video');
            $table->string('email')->nullable()->comment('Email Id')->after('mobile');
            $table->string('address1')->nullable()->comment('address1')->after('email');
            $table->string('address2')->nullable()->comment('address2')->after('address1');
            $table->string('address3')->nullable()->comment('address3')->after('address2');
            $table->string('language')->nullable()->comment('Audio Language')->after('address3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing already added fields of mobile, email, address & language field
        Schema::table('users_video', function (Blueprint $table) {
            $table->dropColumn('mobile');
            $table->dropColumn('email');
            $table->dropColumn('address1');
            $table->dropColumn('address2');
            $table->dropColumn('address3');
            $table->dropColumn('language');
        });
    }
}
