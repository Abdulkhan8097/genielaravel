<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersVideoGenerationScriptRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_video_generation_script_records', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->unsignedBigInteger('video_category_type_language_id')->nullable()->comment('Foreign key refers to video_category_type_language >> id field');
            $table->string('script_name', 100)->nullable()->comment('script name from which video got generated');
            $table->integer('total_number_of_records')->default(0)->nullable()->comment('total number of records given for processing');
            $table->integer('records_processed')->default(0)->nullable()->comment('number of records got processed');
            $table->dateTime('script_start_time')->nullable()->comment('script start time');
            $table->dateTime('script_end_time')->nullable()->comment('script end time');
            $table->tinyInteger('status')->nullable()->default(1)->comment('Status: 0=Inactive, 1=Execution pending, 2=Execution inprogress, 3=Execution done');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('script_name');
            $table->index('video_category_type_language_id', 'idx_video_category_type_language_id');
            $table->index('status');
        });

        // Addinga a foreign key
        DB::statement("ALTER TABLE users_video_generation_script_records ADD CONSTRAINT idx_video_category_type_language_id_foreign FOREIGN KEY(video_category_type_language_id) REFERENCES video_category_type_language(id) ON DELETE CASCADE ON UPDATE CASCADE;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_video_generation_script_records');
    }
}
