<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmUploadedArnProjectFocusYesNoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_uploaded_arn_project_focus_yes_no', function (Blueprint $table) {
            $table->id();
			$table->string('ARN');
			$table->string('project_focus');
			$table->integer('status')->unsigned();
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
        Schema::dropIfExists('drm_uploaded_arn_project_focus_yes_no');
    }
}
