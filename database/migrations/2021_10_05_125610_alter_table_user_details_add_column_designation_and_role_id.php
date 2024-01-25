<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUserDetailsAddColumnDesignationAndRoleId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding designation & role_id field
        Schema::table('users_details', function (Blueprint $table) {
            $table->string('designation', 255)->comment('Employee designation')->after('employee_code');
            $table->unsignedBigInteger('role_id')->nullable()->comment('Role id: references role_master table')->after('user_id');
            $table->index('role_id');
            $table->foreign('role_id')->references('id')->on('role_master')->onUpdate('set null')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added designation & role_id field
        Schema::table('users_details', function (Blueprint $table) {
            $table->dropColumn(['designation', 'role_id']);
        });
    }
}
