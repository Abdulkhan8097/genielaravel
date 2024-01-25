<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDrmRelationshipMasterAddingColDesc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //adding columns description in drm_relationship_quality_master
        Schema::table('drm_relationship_quality_master', function (Blueprint $table) {
            $table->string('description', 255)->nullable()->comment('description')->after('label');
        });

        DB::statement("UPDATE drm_relationship_quality_master SET `description` =  'No Contact', `created_at` = `created_at`, `updated_at` = `updated_at` WHERE label = 'Database Record';");
        DB::statement("UPDATE drm_relationship_quality_master SET `description` =  'Met but not yet empanelled', `created_at` = `created_at`, `updated_at` = `updated_at` WHERE label = 'Contact';");
        DB::statement("UPDATE drm_relationship_quality_master SET `description` =  'Relationship but not yet empanelled', `created_at` = `created_at`, `updated_at` = `updated_at` WHERE label = 'Prospect';");
        DB::statement("UPDATE drm_relationship_quality_master SET `description` =  'Relationship & empanelled', `created_at` = `created_at`, `updated_at` = `updated_at` WHERE label = 'Acquaintance';");
        DB::statement("UPDATE drm_relationship_quality_master SET `description` =  'Empanelled & AUM > 0', `created_at` = `created_at`, `updated_at` = `updated_at` WHERE label = 'Customer';");
        DB::statement("UPDATE drm_relationship_quality_master SET `description` =  'Empanelled & AUM > 25 Lakhs', `created_at` = `created_at`, `updated_at` = `updated_at` WHERE label = 'Good Customer';");
        DB::statement("UPDATE drm_relationship_quality_master SET `description` =  'Empanelled & AUM > 1 Crore', `created_at` = `created_at`, `updated_at` = `updated_at` WHERE label = 'Friend';");
        DB::statement("UPDATE drm_relationship_quality_master SET `description` =  'Empanelled & AUM > 5 Crore', `created_at` = `created_at`, `updated_at` = `updated_at` WHERE label = 'Loyal Friend';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        // removing earlier added column scheme_slug in MySQL table: scheme_master
        Schema::table('drm_relationship_quality_master', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
