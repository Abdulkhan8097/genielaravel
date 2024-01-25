<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersAddFieldStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column for field status
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('status')->nullable()->default(1)->comment('Status: 0=Inactive, 1=Active')->after('is_drm_user');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column for field status
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }
}
