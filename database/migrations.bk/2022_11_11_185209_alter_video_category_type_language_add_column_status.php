<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVideoCategoryTypeLanguageAddColumnStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column as status in MySQL table: video_category_type_language
        Schema::table('video_category_type_language', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status:0=Inactive, 1=Active')->after('video');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column as status in MySQL table: video_category_type_language
        Schema::table('video_category_type_language', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
