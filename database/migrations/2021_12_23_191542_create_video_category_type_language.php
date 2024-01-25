<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoCategoryTypeLanguage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_category_type_language', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('video_category_type_id')->comment('Video Category Type ID');
            $table->string('language',255)->nullable()->comment('Video language');
            $table->string('audio',1000)->nullable()->comment('video audio name, audio name should be subCategory_language.extension');
            $table->string('video',1000)->nullable()->comment('video video name, video name should be subCategory_language.extension');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->foreign('video_category_type_id')->references('id')->on('video_category_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_category_type_language');
    }
}
