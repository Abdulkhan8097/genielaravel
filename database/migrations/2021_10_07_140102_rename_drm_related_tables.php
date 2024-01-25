<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameDrmRelatedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding prefix against DRM related tables to identify them easily
        Schema::rename('distributor_category_master', 'drm_distributor_category_master');
        Schema::rename('distributor_master', 'drm_distributor_master');
        Schema::rename('distributor_master_backup', 'drm_distributor_master_backup');
        Schema::rename('partners_rankmf_bdm_list', 'drm_partners_rankmf_bdm_list');
        Schema::rename('partners_rankmf_bdm_list_backup', 'drm_partners_rankmf_bdm_list_backup');
        Schema::rename('partners_rankmf_current_aum', 'drm_partners_rankmf_current_aum');
        Schema::rename('partners_rankmf_current_aum_backup', 'drm_partners_rankmf_current_aum_backup');
        Schema::rename('project_focus_amc_wise_details', 'drm_project_focus_amc_wise_details');
        Schema::rename('project_focus_amc_wise_details_backup', 'drm_project_focus_amc_wise_details_backup');
        Schema::rename('rankmf_partner_registration', 'drm_rankmf_partner_registration');
        Schema::rename('rankmf_partner_registration_backup', 'drm_rankmf_partner_registration_backup');
        Schema::rename('relationship_quality_master', 'drm_relationship_quality_master');
        // Schema::rename('relationship_quality_master_backup', 'drm_relationship_quality_master_backup');
        Schema::rename('uploaded_arn_average_aum_total_commission_data', 'drm_uploaded_arn_average_aum_total_commission_data');
        Schema::rename('uploaded_arn_average_aum_total_commission_data_backup', 'drm_uploaded_arn_average_aum_total_commission_data_backup');
        Schema::rename('uploaded_arn_distributor_category', 'drm_uploaded_arn_distributor_category');
        Schema::rename('uploaded_arn_distributor_category_backup', 'drm_uploaded_arn_distributor_category_backup');
        //Schema::rename('uploaded_arn_project_focus_yes_no', 'drm_uploaded_arn_project_focus_yes_no');
        //Schema::rename('uploaded_arn_project_focus_yes_no_backup', 'drm_uploaded_arn_project_focus_yes_no_backup');
        Schema::rename('uploaded_pincode_city_state', 'drm_uploaded_pincode_city_state');
        Schema::rename('uploaded_pincode_city_state_backup', 'drm_uploaded_pincode_city_state_backup');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added prefix against tables
        Schema::rename('drm_distributor_category_master', 'distributor_category_master');
        Schema::rename('drm_distributor_master', 'distributor_master');
        Schema::rename('drm_distributor_master_backup', 'distributor_master_backup');
        Schema::rename('drm_partners_rankmf_bdm_list', 'partners_rankmf_bdm_list');
        Schema::rename('drm_partners_rankmf_bdm_list_backup', 'partners_rankmf_bdm_list_backup');
        Schema::rename('drm_partners_rankmf_current_aum', 'partners_rankmf_current_aum');
        Schema::rename('drm_partners_rankmf_current_aum_backup', 'partners_rankmf_current_aum_backup');
        Schema::rename('drm_project_focus_amc_wise_details', 'project_focus_amc_wise_details');
        Schema::rename('drm_project_focus_amc_wise_details_backup', 'project_focus_amc_wise_details_backup');
        Schema::rename('drm_rankmf_partner_registration', 'rankmf_partner_registration');
        Schema::rename('drm_rankmf_partner_registration_backup', 'rankmf_partner_registration_backup');
        Schema::rename('drm_relationship_quality_master', 'relationship_quality_master');
        // Schema::rename('drm_relationship_quality_master_backup', 'relationship_quality_master_backup');
        Schema::rename('drm_uploaded_arn_average_aum_total_commission_data', 'uploaded_arn_average_aum_total_commission_data');
        Schema::rename('drm_uploaded_arn_average_aum_total_commission_data_backup', 'uploaded_arn_average_aum_total_commission_data_backup');
        Schema::rename('drm_uploaded_arn_distributor_category', 'uploaded_arn_distributor_category');
        Schema::rename('drm_uploaded_arn_distributor_category_backup', 'uploaded_arn_distributor_category_backup');
        //Schema::rename('drm_uploaded_arn_project_focus_yes_no', 'uploaded_arn_project_focus_yes_no');
        //Schema::rename('drm_uploaded_arn_project_focus_yes_no_backup', 'uploaded_arn_project_focus_yes_no_backup');
        Schema::rename('drm_uploaded_pincode_city_state', 'uploaded_pincode_city_state');
        Schema::rename('drm_uploaded_pincode_city_state_backup', 'uploaded_pincode_city_state_backup');
    }
}
