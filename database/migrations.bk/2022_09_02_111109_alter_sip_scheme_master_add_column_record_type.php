<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSipSchemeMasterAddColumnRecordType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column like record_type
        Schema::table('sip_scheme_master', function (Blueprint $table) {
            $table->string('record_type', 5)->nullable()->default('sip')->comment('Record type: Possible values are sip/stp etc.')->after('scheme_type');
            $table->index('record_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column like record_type
        Schema::table('sip_scheme_master', function (Blueprint $table) {
            $table->dropColumn('record_type');
        });
    }
}
