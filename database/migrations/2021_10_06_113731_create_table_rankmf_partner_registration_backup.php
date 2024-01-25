<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRankmfPartnerRegistrationBackup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creating replica of an existing table rankmf_partner_registration for importing CSV records into backup table
        DB::statement('CREATE TABLE `rankmf_partner_registration_backup` LIKE `rankmf_partner_registration`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rankmf_partner_registration_backup');
    }
}
