<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmArnTransferLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_arn_transfer_log', function (Blueprint $table) {
            $table->id();
			$table->text('arn');
			$table->integer('arn_no');
			$table->integer('from');
			$table->integer('to');
			$table->integer('updated_by');
			$table->text('pincode_moved');
			$table->string('ip',40);
			$table->string('device');
			$table->string('remark',2000);
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
        Schema::dropIfExists('drm_arn_transfer_log');
    }
}
