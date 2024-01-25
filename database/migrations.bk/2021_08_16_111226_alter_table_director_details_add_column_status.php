<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDirectorDetailsAddColumnStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding a new column "status"
        Schema::table('director_details', function (Blueprint $table) {
            if(!Schema::hasColumn('director_details', 'status')){
                $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active')->after('esign_status');
                $table->index('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing already added column "status"
        Schema::table('director_details', function (Blueprint $table) {
            if(Schema::hasColumn('director_details', 'status')){
                $table->dropColumn('status');
            }
        });
    }
}
