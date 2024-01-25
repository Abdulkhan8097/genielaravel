<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDrmDistributorMasterAddColumnRelationshipQualityWithArnAndVisitingCardImageUpload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding columns relationship_quality_with_arn, front_visiting_card_image & back_visiting_card_image
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->string('relationship_quality_with_arn', 191)->nullable()->comment('How is the relationship with ARN holder')->after('arn_euin');
            $table->string('front_visiting_card_image', 255)->nullable()->comment('Visiting card front image')->after('relationship_quality_with_arn');
            $table->string('back_visiting_card_image', 255)->nullable()->comment('Visiting card back image')->after('front_visiting_card_image');
        });

        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->string('relationship_quality_with_arn', 191)->nullable()->comment('How is the relationship with ARN holder')->after('arn_euin');
            $table->string('front_visiting_card_image', 255)->nullable()->comment('Visiting card front image')->after('relationship_quality_with_arn');
            $table->string('back_visiting_card_image', 255)->nullable()->comment('Visiting card back image')->after('front_visiting_card_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added columns relationship_quality_with_arn, front_visiting_card_image & back_visiting_card_image
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->dropColumn(['relationship_quality_with_arn', 'front_visiting_card_image', 'back_visiting_card_image']);
        });

        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->dropColumn(['relationship_quality_with_arn', 'front_visiting_card_image', 'back_visiting_card_image']); 
        });
    }
}
