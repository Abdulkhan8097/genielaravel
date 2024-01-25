<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmUploadedArnIndAumData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_uploaded_arn_ind_aum_data', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('ARN', 100)->comment('ARN number');
            $table->decimal('total_ind_aum', 25, 4)->nullable()->comment('Total industry aum');
            $table->date('ind_aum_as_on_date')->nullable()->comment('Total industry aum as on date');
            $table->tinyInteger('status')->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('ARN');
            $table->index('status');
        });

        // creating replica of an existing table drm_uploaded_arn_ind_aum_data_backup for importing CSV records into backup table
        DB::statement('CREATE TABLE `drm_uploaded_arn_ind_aum_data_backup` LIKE `drm_uploaded_arn_ind_aum_data`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drm_uploaded_arn_ind_aum_data');
        Schema::dropIfExists('drm_uploaded_arn_ind_aum_data_backup');
    }
}
