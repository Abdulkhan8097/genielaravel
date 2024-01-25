<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRoleMasterAddColumnHierarchy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add column hierarchy
        Schema::table('role_master', function (Blueprint $table) {
            $table->integer('hierarchy')->default(0)->nullable()->comment('Hierarchy in the organization')->after('label');
            $table->index('hierarchy');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added hierarchy field
        Schema::table('role_master', function (Blueprint $table) {
            $table->dropColumn(['hierarchy']);
        });
    }
}
