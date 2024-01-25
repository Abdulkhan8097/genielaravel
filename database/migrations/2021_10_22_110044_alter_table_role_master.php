<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRoleMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding have_all_permission column
        Schema::table('role_master', function (Blueprint $table) {
            $table->tinyInteger('have_all_permissions')->default(0)->nullable()->comment('Is this role have all permissions: 0=No, 1=Yes')->after('label');
            $table->index('have_all_permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added have_all_permission column
        Schema::table('role_master', function (Blueprint $table) {
            $table->dropColumn(['have_all_permissions']);
        });
    }
}
