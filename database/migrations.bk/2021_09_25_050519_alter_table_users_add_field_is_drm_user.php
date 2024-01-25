<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUsersAddFieldIsDrmUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding is_drm_user field
        Schema::table('users', function (Blueprint $table) {
            DB::statement("ALTER TABLE `users` ADD `is_drm_user` TINYINT(1) NULL DEFAULT '0' COMMENT 'Is record DRM User? 0 = No, 1 = Yes' AFTER `remember_token`, ADD INDEX `idx_is_drm_user` (`is_drm_user`);");

            DB::statement("ALTER TABLE `users` DROP INDEX `users_email_unique`, ADD UNIQUE `users_email_unique` (`email`, `is_drm_user`);");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing already added field is_drm_user
        Schema::table('users_video', function (Blueprint $table) {
            DB::statement("ALTER TABLE `users` DROP `is_drm_user`;");

            DB::statement("ALTER TABLE `users` DROP INDEX `users_email_unique`, ADD UNIQUE `users_email_unique` (`email`);");
        });
    }
}
