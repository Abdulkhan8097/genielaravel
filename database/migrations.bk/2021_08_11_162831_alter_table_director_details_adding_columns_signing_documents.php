<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDirectorDetailsAddingColumnsSigningDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding signing document filename & signed document filename field
        Schema::table('director_details', function (Blueprint $table) {
            $table->string('esigning_document_filename')->nullable()->comment('Signing document filename')->after('aadhar_number');
            $table->string('esigned_document_filename')->nullable()->comment('Signed document filename')->after('esigning_document_filename');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing already added fields of signing document filename & signed document filename
        Schema::table('director_details', function (Blueprint $table) {
            $table->dropColumn('esigning_document_filename');
            $table->dropColumn('esigned_document_filename');
        });
    }
}
