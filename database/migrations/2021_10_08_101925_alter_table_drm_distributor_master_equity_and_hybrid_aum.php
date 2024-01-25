<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDrmDistributorMasterEquityAndHybridAum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding columns for equity & hybrid aum for rankmf/samcomf
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->decimal('rankmf_partner_equity_and_hybrid_aum', 25, 4)->nullable()->comment('RankMF partner Equity & Hybrid AUM')->after('rankmf_partner_aum');
            $table->decimal('samcomf_partner_equity_and_hybrid_aum', 25, 4)->nullable()->comment('SamcoMF partner Equity & Hybrid AUM')->after('samcomf_partner_aum');
        });

        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->decimal('rankmf_partner_equity_and_hybrid_aum', 25, 4)->nullable()->comment('RankMF partner Equity & Hybrid AUM')->after('rankmf_partner_aum');
            $table->decimal('samcomf_partner_equity_and_hybrid_aum', 25, 4)->nullable()->comment('SamcoMF partner Equity & Hybrid AUM')->after('samcomf_partner_aum');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added columns for equity & hybrid aum for rankmf/samcomf
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->dropColumn(['rankmf_partner_equity_and_hybrid_aum', 'samcomf_partner_equity_and_hybrid_aum']);
        });

        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->dropColumn(['rankmf_partner_equity_and_hybrid_aum', 'samcomf_partner_equity_and_hybrid_aum']);
        });
    }
}
