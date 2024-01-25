<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmReimbursement2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drm_reimbursement', function (Blueprint $table) {
			$table->integer('hrms_id')->unsigned()->nullable()->commenmt('HRMS Response id');
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
			$table->dropColumn('hrms_id');
        });
    }
}
