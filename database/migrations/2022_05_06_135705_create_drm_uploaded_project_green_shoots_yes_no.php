<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmUploadedProjectGreenShootsYesNo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_uploaded_project_green_shoots_yes_no', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('ARN', 100)->comment('ARN number');
            $table->enum('project_green_shoots', ['yes', 'no'])->default('no')->nullable()->comment('Possible values are: yes or no');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('status');
            $table->index('ARN');
        });

        DB::statement("CREATE TABLE drm_uploaded_project_green_shoots_yes_no_backup LIKE drm_uploaded_project_green_shoots_yes_no;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drm_uploaded_project_green_shoots_yes_no');
        Schema::dropIfExists('drm_uploaded_project_green_shoots_yes_no_backup');
    }
}
