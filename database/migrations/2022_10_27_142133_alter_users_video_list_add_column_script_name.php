<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersVideoListAddColumnScriptName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column as script_name
        Schema::table('users_video_list', function (Blueprint $table) {
            $table->string('script_name', 100)->nullable()->comment('script name from which video got generated')->after('video');
            $table->index('script_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column as script_name
        Schema::table('users_video_list', function (Blueprint $table) {
            $table->dropColumn('script_name');
        });
    }
}
