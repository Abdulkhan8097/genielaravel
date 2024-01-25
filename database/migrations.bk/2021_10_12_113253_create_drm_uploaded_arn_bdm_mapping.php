<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmUploadedArnBdmMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_uploaded_arn_bdm_mapping', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('ARN', 100)->comment('ARN number');
            $table->string('bdm_email', 191)->comment('BDM email id used for assigning against an ARN number');
            $table->bigInteger('bdm_user_id')->nullable()->comment('User id will be retrieved based on email id available in users table');
            $table->string('rm_relationship', 20)->nullable()->comment('Flag RM relationship: Possible values can be provisional/final');
            $table->tinyInteger('status')->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('ARN');
            $table->index('status');
        });

        // creating replica of an existing table drm_uploaded_arn_bdm_mapping_backup for importing CSV records into backup table
        DB::statement('CREATE TABLE `drm_uploaded_arn_bdm_mapping_backup` LIKE `drm_uploaded_arn_bdm_mapping`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drm_uploaded_arn_bdm_mapping');
        Schema::dropIfExists('drm_uploaded_arn_bdm_mapping_backup');
    }
}
