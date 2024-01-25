<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmUploadedArnProjectEmergingStarsYesNo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_uploaded_arn_project_emerging_stars_yes_no', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('ARN', 100)->comment('ARN number');
            $table->string('project_emerging_stars', 20)->nullable()->comment('Possible Values: Yes or No');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('ARN');
            $table->index('status', 'idx_status');
        });

        Schema::create('drm_uploaded_arn_project_emerging_stars_yes_no_backup', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('ARN', 100)->comment('ARN number');
            $table->string('project_emerging_stars', 20)->nullable()->comment('Possible Values: Yes or No');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('ARN');
            $table->index('status', 'idx_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drm_uploaded_arn_project_emerging_stars_yes_no');
        Schema::dropIfExists('drm_uploaded_arn_project_emerging_stars_yes_no_backup');
    }
}
