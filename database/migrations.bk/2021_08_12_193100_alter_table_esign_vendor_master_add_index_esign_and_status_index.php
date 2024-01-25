<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableEsignVendorMasterAddIndexEsignAndStatusIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esign_vendors_master', function (Blueprint $table) {

            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('esign_vendors_master');
        
            if(!array_key_exists("esign_vendors_master_name_index", $indexesFound)){
                DB::statement("ALTER TABLE `esign_vendors_master` ADD INDEX `esign_vendors_master_name_index` (`name`(191));");
            }


            //if(!collect(DB::select("SHOW INDEXES FROM esign_vendors_master"))->pluck('Key_name')->contains('name')){
            //}

            if(!collect(DB::select("SHOW INDEXES FROM esign_vendors_master"))->pluck('Key_name')->contains('status')){
                $table->index('status');
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
