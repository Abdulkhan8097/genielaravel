<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmRelationshipQualityMasterAddNewRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding new column "score"
        Schema::table('drm_relationship_quality_master', function (Blueprint $table) {
            $table->integer('score')->default(0)->nullable()->comment('Score')->after('label');
        });

        // truncating table
        DB::table('drm_relationship_quality_master')->truncate();

        // add new records into table
        DB::table('drm_relationship_quality_master')->insert(
            array(array('label' => 'Database Record', 'status' => 1, 'score' => 0),
                  array('label' => 'Contact', 'status' => 1, 'score' => 10),
                  array('label' => 'Prospect', 'status' => 1, 'score' => 20),
                  array('label' => 'Acquaintance', 'status' => 1, 'score' => 30),
                  array('label' => 'Customer', 'status' => 1, 'score' => 50),
                  array('label' => 'Good Customer', 'status' => 1, 'score' => 70),
                  array('label' => 'Friend', 'status' => 1, 'score' => 90),
                  array('label' => 'Loyal Friend', 'status' => 1, 'score' => 100)
                )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // deleting earlier added column "score"
        Schema::table('drm_relationship_quality_master', function (Blueprint $table) {
            $table->dropColumn(['score']);
        });
    }
}
