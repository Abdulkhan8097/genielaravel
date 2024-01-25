<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmReimbursementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drm_reimbursement', function (Blueprint $table) {
			$table->string('em_code',50)->nullable()->commenmt('Employee code');
			$table->string('travel_type',50)->nullable();
			$table->string('TransportType',50)->nullable();
			$table->string('tolocation',50)->nullable();
			$table->date('todate')->nullable();
			$table->double('approx_km',10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('drm_reimbursement', function(Blueprint $table){
			$table->dropColumn('em_code');
			$table->dropColumn('travel_type');
			$table->dropColumn('TransportType');
			$table->dropColumn('tolocation');
			$table->dropColumn('todate');
			$table->dropColumn('approx_km');
		});
    }
}
