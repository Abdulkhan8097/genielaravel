<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDirectorDetailsAddColumnEsignRequired extends Migration
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
            if(!Schema::hasColumn('director_details', 'esign_required')){
                $table->tinyInteger('esign_required')->default(0)->comment('eSign Required: 0=No, 1=Yes')->after('name_as_per_aadhar');
            }
            /*$sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('director_details');*/
            // if(!Schema::hasIndex('director_details', 'esign_required')){
            // if(array_key_exists("esign_required", $indexesFound) === FALSE){
            if(collect(DB::select("SHOW INDEXES FROM director_details"))->pluck('Key_name')->contains('esign_required')){
                $table->index('esign_required');
            }
            // if(!Schema::hasIndex('director_details', 'esign_status')){
            // if(array_key_exists("esign_status", $indexesFound) === FALSE){
            if(collect(DB::select("SHOW INDEXES FROM director_details"))->pluck('Key_name')->contains('esign_status')){
                $table->index('esign_status');
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
        //
    }
}
