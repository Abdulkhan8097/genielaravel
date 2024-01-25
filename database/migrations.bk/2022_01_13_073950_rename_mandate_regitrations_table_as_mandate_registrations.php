<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameMandateRegitrationsTableAsMandateRegistrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // correcting spelling of table mandate registrations
        DB::statement("RENAME TABLE `mandate_regitrations` TO `mandate_registrations`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // keeping old table name as it is
        DB::statement("RENAME TABLE `mandate_registrations` TO `mandate_regitrations`;");
    }
}
