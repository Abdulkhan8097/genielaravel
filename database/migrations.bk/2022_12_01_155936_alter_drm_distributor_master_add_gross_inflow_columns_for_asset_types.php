<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmDistributorMasterAddGrossInflowColumnsForAssetTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding columns for gross inflows related to asset types in MySQL table: drm_distributor_master
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->decimal('samcomf_partner_gross_inflow_sip', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of SIP orders')->after('samcomf_live_sip_amount');
            $table->decimal('samcomf_partner_gross_inflow_other', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Other than SIP orders')->after('samcomf_partner_gross_inflow_sip');

            $table->decimal('samcomf_partner_gross_inflow_sip_equity', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Equity Scheme SIP orders')->after('samcomf_partner_gross_inflow_other');
            $table->decimal('samcomf_partner_gross_inflow_other_equity', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Equity Scheme Other than SIP orders')->after('samcomf_partner_gross_inflow_sip_equity');

            $table->decimal('samcomf_partner_gross_inflow_sip_debt', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Debt Scheme SIP orders')->after('samcomf_partner_gross_inflow_other_equity');
            $table->decimal('samcomf_partner_gross_inflow_other_debt', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Debt Scheme Other than SIP orders')->after('samcomf_partner_gross_inflow_sip_debt');

            $table->decimal('samcomf_partner_gross_inflow_sip_hybrid', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Hybrid Scheme SIP orders')->after('samcomf_partner_gross_inflow_other_debt');
            $table->decimal('samcomf_partner_gross_inflow_other_hybrid', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Hybrid Scheme Other than SIP orders')->after('samcomf_partner_gross_inflow_sip_hybrid');
        });

        // adding columns for gross inflows related to asset types in MySQL table: drm_distributor_master_backup
        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->decimal('samcomf_live_sip_amount', 25, 4)->nullable()->comment('SamcoMF partner Live SIP Registration Amount')->after('is_partner_active_on_samcomf');

            $table->decimal('samcomf_partner_gross_inflow_sip', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of SIP orders')->after('samcomf_live_sip_amount');
            $table->decimal('samcomf_partner_gross_inflow_other', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Other than SIP orders')->after('samcomf_partner_gross_inflow_sip');

            $table->decimal('samcomf_partner_gross_inflow_sip_equity', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Equity Scheme SIP orders')->after('samcomf_partner_gross_inflow_other');
            $table->decimal('samcomf_partner_gross_inflow_other_equity', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Equity Scheme Other than SIP orders')->after('samcomf_partner_gross_inflow_sip_equity');

            $table->decimal('samcomf_partner_gross_inflow_sip_debt', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Debt Scheme SIP orders')->after('samcomf_partner_gross_inflow_other_equity');
            $table->decimal('samcomf_partner_gross_inflow_other_debt', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Debt Scheme Other than SIP orders')->after('samcomf_partner_gross_inflow_sip_debt');

            $table->decimal('samcomf_partner_gross_inflow_sip_hybrid', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Hybrid Scheme SIP orders')->after('samcomf_partner_gross_inflow_other_debt');
            $table->decimal('samcomf_partner_gross_inflow_other_hybrid', 25, 4)->nullable()->comment('SamcoMF partner Gross Inflow of Hybrid Scheme Other than SIP orders')->after('samcomf_partner_gross_inflow_sip_hybrid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added columns of gross inflows related to asset types from MySQL table: drm_distributor_master
        Schema::table('drm_distributor_master', function (Blueprint $table) {
            $table->dropColumn('samcomf_partner_gross_inflow_sip');
            $table->dropColumn('samcomf_partner_gross_inflow_other');
            $table->dropColumn('samcomf_partner_gross_inflow_sip_equity');
            $table->dropColumn('samcomf_partner_gross_inflow_other_equity');
            $table->dropColumn('samcomf_partner_gross_inflow_sip_debt');
            $table->dropColumn('samcomf_partner_gross_inflow_other_debt');
            $table->dropColumn('samcomf_partner_gross_inflow_sip_hybrid');
            $table->dropColumn('samcomf_partner_gross_inflow_other_hybrid');
        });

        // removing earlier added columns of gross inflows related to asset types from MySQL table: drm_distributor_master_backup
        Schema::table('drm_distributor_master_backup', function (Blueprint $table) {
            $table->dropColumn('samcomf_live_sip_amount');
            $table->dropColumn('samcomf_partner_gross_inflow_sip');
            $table->dropColumn('samcomf_partner_gross_inflow_other');
            $table->dropColumn('samcomf_partner_gross_inflow_sip_equity');
            $table->dropColumn('samcomf_partner_gross_inflow_other_equity');
            $table->dropColumn('samcomf_partner_gross_inflow_sip_debt');
            $table->dropColumn('samcomf_partner_gross_inflow_other_debt');
            $table->dropColumn('samcomf_partner_gross_inflow_sip_hybrid');
            $table->dropColumn('samcomf_partner_gross_inflow_other_hybrid');
        });
    }
}
