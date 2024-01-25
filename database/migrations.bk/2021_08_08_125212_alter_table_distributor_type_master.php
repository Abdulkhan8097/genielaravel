<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDistributorTypeMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Adding column status in distributor type master
        Schema::table('distributer_type_master', function (Blueprint $table) {
            $table->tinyInteger('status')->nullable()->default(0)->comment('Status: 0=No, 1=Yes')->after('doc_asl');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Removing added field status
        Schema::table('distributer_type_master', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
