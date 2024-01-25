<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDistributorMasterAddFieldsTotalIndustryAumRmRelationshipEtc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding rm_relationship, total_industry_aum and aum_as_on_date
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->string('rm_relationship', 20)->nullable()->comment('Flag RM relationship: Possible values can be provisional/final')->after('direct_relationship_user_id');
            $table->decimal('total_ind_aum', 25, 4)->nullable()->comment('Total industry aum')->after('percent_market_share_of_equity_and_hybrid_aum');
            $table->date('ind_aum_as_on_date')->nullable()->comment('Total industry aum as on date')->after('total_ind_aum');
        });

        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->string('rm_relationship', 20)->nullable()->comment('Flag RM relationship: Possible values can be provisional/final')->after('direct_relationship_user_id');
            $table->decimal('total_ind_aum', 25, 4)->nullable()->comment('Total industry aum')->after('percent_market_share_of_equity_and_hybrid_aum');
            $table->date('ind_aum_as_on_date')->nullable()->comment('Total industry aum as on date')->after('total_ind_aum');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added fields rm_relationship, total_industry_aum and aum_as_on_date
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->dropColumn(['rm_relationship', 'total_ind_aum', 'ind_aum_as_on_date']);
        });

        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->dropColumn(['rm_relationship', 'total_ind_aum', 'ind_aum_as_on_date']);
        });
    }
}
