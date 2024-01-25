<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordsInApiUsersListRegardingREDVISION extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding new entry of REDVISION in MySQL table:api_users_list
        DB::table('api_users_list')->insert(array(array('name' => 'REDVISION', 'status' => 1)));
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
