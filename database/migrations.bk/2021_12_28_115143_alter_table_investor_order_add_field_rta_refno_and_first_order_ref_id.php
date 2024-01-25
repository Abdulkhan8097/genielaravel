<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInvestorOrderAddFieldRtaRefnoAndFirstOrderRefId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding rta_refno, first_order_ref_id field(s) from MySQL table: investor_order
        Schema::table('investor_order', function (Blueprint $table) {
            $table->string('rta_refno', 20)->nullable()->comment('RTA referance number')->after('order_type');
            $table->unsignedBigInteger('first_order_ref_id')->nullable()->comment('id field from invstor_order table, this id will be of LUMPSUM order')->after('first_order_flag');
            $table->index('rta_refno');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added rta_refno, first_order_ref_id field(s) from MySQL table: investor_order
        Schema::table('investor_order', function (Blueprint $table) {
            $table->dropColumn(['rta_refno', 'first_order_ref_id']);
        });
    }
}
