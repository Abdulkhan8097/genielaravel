<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmSchemesForAutoSwitchTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('drm_schemes_for_auto_switch', function (Blueprint $table) {
			$table->id();
			$table->string('Unique_No');
			$table->string('isin');
			$table->string('AMC_Scheme_Code');
			$table->string('To_AMC_Scheme_Code');
			$table->integer('autoswitch')->default(1);
			$table->date('start_date');
			$table->date('end_date');
			$table->timestamps();
			$table->index('Unique_No');
			$table->index('isin');
			$table->index('AMC_Scheme_Code');
			$table->index('autoswitch');
			$table->index('start_date');
			$table->index('end_date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('drm_schemes_for_auto_switch');
	}
}
