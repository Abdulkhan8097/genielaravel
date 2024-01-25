<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDistributorMasterAddColumnStateRankmfSamcomfEmailAndMobile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // adding ARN state, rankmf and samcomf email, mobile field in MySQL table: drm_distributor_master
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->string('arn_state', 100)->nullable()->comment('Yes')->after('arn_city');
            $table->string('rankmf_email', 255)->nullable()->comment('RankMF Partner Email')->after('rankmf_partner_code');
            $table->string('rankmf_mobile', 20)->nullable()->comment('RankMF Partner Mobile')->after('rankmf_email');
            $table->string('samcomf_email', 255)->nullable()->comment('SamcoMF Partner Email')->after('samcomf_partner_code');
            $table->string('samcomf_mobile', 20)->nullable()->comment('SamcoMF Partner Mobile')->after('samcomf_email');

            DB::statement("ALTER TABLE `drm_distributor_master` CHANGE `alternate_email_1` `alternate_email_1` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Alternate email id 1';");
            DB::statement("ALTER TABLE `drm_distributor_master` CHANGE `alternate_email_2` `alternate_email_2` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Alternate email id 2';");
            DB::statement("ALTER TABLE `drm_distributor_master` CHANGE `alternate_email_3` `alternate_email_3` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Alternate email id 3';");
            DB::statement("ALTER TABLE `drm_distributor_master` CHANGE `alternate_email_4` `alternate_email_4` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Alternate email id 4';");
            DB::statement("ALTER TABLE `drm_distributor_master` CHANGE `alternate_email_5` `alternate_email_5` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Alternate email id 5';");
        });

        // adding ARN state, rankmf and samcomf email, mobile field in MySQL table: drm_distributor_master_backup
        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->string('arn_state', 100)->nullable()->comment('Yes')->after('arn_city');
            $table->string('rankmf_email', 255)->nullable()->comment('RankMF Partner Email')->after('rankmf_partner_code');
            $table->string('rankmf_mobile', 20)->nullable()->comment('RankMF Partner Mobile')->after('rankmf_email');
            $table->string('samcomf_email', 255)->nullable()->comment('SamcoMF Partner Email')->after('samcomf_partner_code');
            $table->string('samcomf_mobile', 20)->nullable()->comment('SamcoMF Partner Mobile')->after('samcomf_email');

            DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `alternate_email_1` `alternate_email_1` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Alternate email id 1';");
            DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `alternate_email_2` `alternate_email_2` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Alternate email id 2';");
            DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `alternate_email_3` `alternate_email_3` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Alternate email id 3';");
            DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `alternate_email_4` `alternate_email_4` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Alternate email id 4';");
            DB::statement("ALTER TABLE `drm_distributor_master_backup` CHANGE `alternate_email_5` `alternate_email_5` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Alternate email id 5';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added columns like arn_state, rankmf_email, rankmf_mobile from MySQL table: drm_distributor_master
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->dropColumn(['arn_state', 'rankmf_email', 'rankmf_mobile', 'samcomf_email', 'samcomf_mobile']);
        });

        // removing earlier added columns like arn_state, rankmf_email, rankmf_mobile from MySQL table: drm_distributor_master_backup
        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->dropColumn(['arn_state', 'rankmf_email', 'rankmf_mobile', 'samcomf_email', 'samcomf_mobile']);
        });
    }
}
