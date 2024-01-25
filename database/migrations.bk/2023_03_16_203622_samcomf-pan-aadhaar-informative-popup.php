<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SamcomfPanAadhaarInformativePopup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       DB::statement("ALTER TABLE investor_account ADD pan_aadhaar_linked TINYINT(1) NULL DEFAULT '0' COMMENT 'Does PAN & AADHAAR linked with each other? 1 = Yes, 0 = No' AFTER pan"); 
       DB::statement("ALTER TABLE folio_investor_detail ADD pan_aadhaar_linked TINYINT(1) NULL DEFAULT '0' COMMENT 'Does PAN & AADHAAR linked with each other? 1 = Yes, 0 = No' AFTER pan");
       DB::statement("ALTER TABLE investor_folio_details ADD pan_aadhaar_linked TINYINT(1) NULL DEFAULT '0' COMMENT 'Does PAN & AADHAAR linked with each other? 1 = Yes, 0 = No' AFTER pan"); 
       DB::statement("ALTER TABLE lead_account_detail ADD pan_aadhaar_linked TINYINT(1) NULL DEFAULT '0' COMMENT 'Does PAN & AADHAAR linked with each other? 1 = Yes, 0 = No' AFTER pan"); 
       DB::table('settings')->insert(array(array('key' => 'PAN_AADHAAR_MANDATORY_LINKING',
                                                  'value' => '0'
                                                ),array('key' => 'PAN_AADHAAR_LINKED_SUCCESS_MSG',
                                                        'value' => 'Existing and Valid. Aadhaar Seeding is Successful.'),
                                                array('key' => 'SHOW_PAN_AADHAAR_LINKING_POPUP_MSG',
                                                        'value' => '1')
                                            ),

                                    );
       // updating field pan_aadhaar_linked = 1 in MySQL table: investor_account
       DB::statement("UPDATE (SELECT pan, LOWER(REPLACE(TRIM(CASE WHEN(IFNULL(response, '') != '' AND JSON_CONTAINS_PATH(response, 'one', '$.data.status')) THEN JSON_EXTRACT(response, \"$.data.status\") ELSE '' END), '\"', '')) AS pan_status FROM kra_api_logs WHERE api_name = 'IT PAN API' AND response LIKE '%rawResponse%' GROUP BY pan, pan_status HAVING pan_status = 'existing and valid. aadhaar seeding is successful.') AS a INNER JOIN investor_account ON (a.pan = investor_account.pan) SET investor_account.created_at = investor_account.created_at, investor_account.updated_at = investor_account.updated_at, investor_account.pan_aadhaar_linked = 1 WHERE investor_account.pan_aadhaar_linked = 0;");

       // updating field pan_aadhaar_linked = 1 in MySQL table: folio_investor_detail
       DB::statement("UPDATE (SELECT pan, LOWER(REPLACE(TRIM(CASE WHEN(IFNULL(response, '') != '' AND JSON_CONTAINS_PATH(response, 'one', '$.data.status')) THEN JSON_EXTRACT(response, \"$.data.status\") ELSE '' END), '\"', '')) AS pan_status FROM kra_api_logs WHERE api_name = 'IT PAN API' AND response LIKE '%rawResponse%' GROUP BY pan, pan_status HAVING pan_status = 'existing and valid. aadhaar seeding is successful.') AS a INNER JOIN folio_investor_detail ON (a.pan = folio_investor_detail.pan) SET folio_investor_detail.created_at = folio_investor_detail.created_at, folio_investor_detail.updated_at = folio_investor_detail.updated_at, folio_investor_detail.pan_aadhaar_linked = 1 WHERE folio_investor_detail.pan_aadhaar_linked = 0;");

       // updating field pan_aadhaar_linked = 1 in MySQL table: investor_folio_details
       DB::statement("UPDATE (SELECT pan, LOWER(REPLACE(TRIM(CASE WHEN(IFNULL(response, '') != '' AND JSON_CONTAINS_PATH(response, 'one', '$.data.status')) THEN JSON_EXTRACT(response, \"$.data.status\") ELSE '' END), '\"', '')) AS pan_status FROM kra_api_logs WHERE api_name = 'IT PAN API' AND response LIKE '%rawResponse%' GROUP BY pan, pan_status HAVING pan_status = 'existing and valid. aadhaar seeding is successful.') AS a INNER JOIN investor_folio_details ON (a.pan = investor_folio_details.pan) SET investor_folio_details.created_at = investor_folio_details.created_at, investor_folio_details.updated_at = investor_folio_details.updated_at, investor_folio_details.pan_aadhaar_linked = 1 WHERE investor_folio_details.pan_aadhaar_linked = 0;");

       // updating field pan_aadhaar_linked = 1 in MySQL table: lead_account_detail
       DB::statement("UPDATE (SELECT pan, LOWER(REPLACE(TRIM(CASE WHEN(IFNULL(response, '') != '' AND JSON_CONTAINS_PATH(response, 'one', '$.data.status')) THEN JSON_EXTRACT(response, \"$.data.status\") ELSE '' END), '\"', '')) AS pan_status FROM kra_api_logs WHERE api_name = 'IT PAN API' AND response LIKE '%rawResponse%' GROUP BY pan, pan_status HAVING pan_status = 'existing and valid. aadhaar seeding is successful.') AS a INNER JOIN lead_account_detail ON (a.pan = lead_account_detail.pan) SET lead_account_detail.created_at = lead_account_detail.created_at, lead_account_detail.updated_at = lead_account_detail.updated_at, lead_account_detail.pan_aadhaar_linked = 1 WHERE lead_account_detail.pan_aadhaar_linked = 0;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
