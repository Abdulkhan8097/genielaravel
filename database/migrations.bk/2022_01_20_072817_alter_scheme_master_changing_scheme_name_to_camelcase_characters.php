<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterChangingSchemeNameToCamelcaseCharacters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // updating scheme name to camel case characters
        DB::statement("UPDATE `scheme_master` SET `created` = `created`, `Scheme_Name` = 'Samco Flexi Cap Fund - Regular Growth' WHERE `RTA_Scheme_Code` = 'FCRG' AND `AMC_Scheme_Code` = 'FCRG';");
        DB::statement("UPDATE `scheme_master` SET `created` = `created`, `Scheme_Name` = 'Samco Flexi Cap Fund - Direct Growth' WHERE `RTA_Scheme_Code` = 'FCDG' AND `AMC_Scheme_Code` = 'FCDG';");
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
