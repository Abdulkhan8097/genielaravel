<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePartnerRoleAndPartnerRoleMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE TABLE `partner_role_master` LIKE `role_master`;");
        DB::statement("CREATE TABLE `partner_role_permissions` LIKE `role_permissions`;");
        DB::statement("ALTER TABLE `users_details` DROP FOREIGN KEY users_details_role_id_foreign;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_role_master');
        Schema::dropIfExists('partner_role_permissions');
        DB::statement("ALTER TABLE `users_details` ADD FOREIGN KEY `users_details_role_id_foreign`(`role_id`) REFERENCES `role_master`(`id`);");
    }
}
