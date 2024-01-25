<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterAddColumnSchemeSlug extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column scheme_slug in MySQL table: scheme_master
        Schema::connection('invdb')->table('scheme_master', function (Blueprint $table) {
            $table->string('scheme_slug', 100)->nullable()->comment('Scheme slug this will be common for schemes which have only different plan types')->after('Scheme_Name');
            $table->index('scheme_slug');
        });

        // updating default scheme_slug against existing schemes like SAMCO FLEXI CAP REGULAR & DIRECT GROWTH
        DB::connection('invdb')->statement("UPDATE scheme_master SET created = created, scheme_slug = 'samco-flexi-cap-fund' WHERE RTA_Scheme_Code IN ('FCRG', 'FCDG');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column scheme_slug in MySQL table: scheme_master
        Schema::connection('invdb')->table('scheme_master', function (Blueprint $table) {
            $table->dropColumn('scheme_slug');
        });
    }
}
