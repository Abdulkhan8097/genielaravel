<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmClientAumDataBackupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_client_aum_data_backup', function (Blueprint $table) {
            $table->id();
			$table->string('trans_date');
			$table->double('purchase');
			$table->double('redemption');
			$table->double('net_sales');
			$table->double('available_units');
			$table->string('client_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drm_client_aum_data_backup');
    }
}
