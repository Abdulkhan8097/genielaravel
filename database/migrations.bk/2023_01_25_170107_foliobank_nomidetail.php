<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FoliobankNomidetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE investor_nominee_details ADD COLUMN nominee_guardian_relationship VARCHAR(100) NULL comment 'Guardian Relationship' AFTER nominee_guardian_pan, ADD COLUMN nominee_pan VARCHAR(20) NULL comment 'Nominee PAN' AFTER nominee_name");
        DB::statement("ALTER TABLE folio_nominee_details ADD COLUMN nominee_guardian_relationship VARCHAR(100) NULL comment 'Guardian Relationship' AFTER nominee_guardian_pan, ADD COLUMN nominee_pan VARCHAR(20) NULL comment 'Nominee PAN' AFTER nominee_name");

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
