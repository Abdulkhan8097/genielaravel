<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDirectorDetailsAddColumnNameAsPerAadhar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding new column name as per aadhar
        Schema::table('director_details', function (Blueprint $table) {
            $table->string('name_as_per_aadhar')->nullable()->comment('Name as per aadhar card retrieved from esigning vendor')->after('esigned_document_filename');
            $table->index('name');
            $table->index('aadhar_number');
            $table->index('esign_required');
            $table->index('esign_status');

            if(!Schema::hasColumn('director_details', 'esign_required')){
                $table->tinyInteger('esign_required')->default(0)->comment('eSign Required: 0=No, 1=Yes')->after('name_as_per_aadhar');
            }            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing added new column name as per aadhar
        Schema::table('director_details', function (Blueprint $table) {
            $table->dropColumn('name_as_per_aadhar');
            $table->dropIndex('name');
            $table->dropIndex('aadhar_number');
            $table->dropIndex('esign_required');
            $table->dropIndex('esign_status');
        });
    }
}
