<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRoleMasterAddColumnShowAllArn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding show_all_arn_data column
        Schema::table('role_master', function (Blueprint $table) {
            $table->tinyInteger('show_all_arn_data')->default(0)->nullable()->comment('Show all ARN data: 0=No, 1=Yes. Here value 0 means if logged in user is not a reporting person of any other person, then not showing any ARN data. If value is 1 (E.G. Admin Role etc.) means even though user is not a reporting person of any other person still show them all ARN data.')->after('have_all_permissions');
            $table->index('show_all_arn_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added show_all_arn_data column
        Schema::table('role_master', function (Blueprint $table) {
            $table->dropColumn(['show_all_arn_data']);
        });
    }
}
