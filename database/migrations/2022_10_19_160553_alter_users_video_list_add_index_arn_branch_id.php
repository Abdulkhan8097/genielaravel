<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersVideoListAddIndexArnBranchId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding an index over fields like arn, branch_id & video_category_type_language_id
        Schema::table('users_video_list', function (Blueprint $table) {
            $table->unique(['arn', 'branch_id', 'video_category_type_language_id'], 'idx_arn_branch_id_video_language');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added indexes over fields like arn, branch_id & video_category_type_language_id
        Schema::table('users_video_list', function (Blueprint $table) {
            $table->dropIndex('idx_arn_branch_id_video_language');
        });
    }
}
