<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterActiveShareAddIndexSchemecodeAndDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding indexes over column(s) schemecode, indexcode and active_share_date in MySQL table: active_share
        Schema::table('active_share', function (Blueprint $table) {
            $table->index('schemecode');
            $table->index('indexcode');
            $table->index('symbol');
            $table->index('compname');
            $table->index('active_share_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added indexes over column(s) schemecode, indexcode and active_share_date from MySQL table: active_share
        Schema::table('active_share', function (Blueprint $table) {
            $table->dropIndex('active_share_schemecode_index');
            $table->dropIndex('active_share_indexcode_index');
            $table->dropIndex('active_share_symbol_index');
            $table->dropIndex('active_share_compname_index');
            $table->dropIndex('active_share_active_share_date_index');
        });
    }
}
