<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersDetailsAddColumnSkipInArnMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding skip_in_arn_mapping column
        Schema::table('users_details', function (Blueprint $table) {
            $table->tinyInteger('skip_in_arn_mapping')->default(0)->nullable()->comment('skip this user in ARN mapping: 0 = No, 1 = Yes')->after('reporting_to');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added skip_in_arn_mapping column
        Schema::table('users_details', function (Blueprint $table) {
            $table->dropColumn(['skip_in_arn_mapping']);
        });
    }
}
