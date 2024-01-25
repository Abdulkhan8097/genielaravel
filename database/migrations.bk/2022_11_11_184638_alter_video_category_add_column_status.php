<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVideoCategoryAddColumnStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column as status in MySQL table: video_category
        Schema::table('video_category', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status:0=Inactive, 1=Active')->after('category');
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
        // removing earlier added column as status in MySQL table: video_category
        Schema::table('video_category', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
