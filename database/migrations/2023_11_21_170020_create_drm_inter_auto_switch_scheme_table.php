<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmInterAutoSwitchSchemeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_inter_auto_switch_scheme', function (Blueprint $table) {
            $table->id();
			$table->string('cas_uploaded_id');
			$table->text('json_request');
            $table->timestamps();
			$table->index('cas_uploaded_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drm_inter_auto_switch_scheme');
    }
}
