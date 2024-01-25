<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSchemeMasterRecordDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("UPDATE scheme_master SET created = created, Scheme_Code = 'FC', Scheme_Plan = 'Regular' WHERE RTA_Scheme_Code = 'FCRG';");
        DB::statement("UPDATE scheme_master SET created = created, Scheme_Code = 'FC', Scheme_Plan = 'DIRECT', Scheme_Option = 'G' WHERE RTA_Scheme_Code = 'FCDG';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
