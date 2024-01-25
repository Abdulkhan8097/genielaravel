<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmArnTransferLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::table('drm_arn_transfer_log', function (Blueprint $table) {
			$table->string('department');
		});
	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drm_arn_transfer_log', function(Blueprint $table){
			$table->dropColumn('department');
        });
    }
}
