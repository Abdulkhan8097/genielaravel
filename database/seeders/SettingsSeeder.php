<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$data = [
			["key" => "SAMCOMF_FUND_ID_AT_RTA","value" => "188","status" => "1"],
			["key" => "SAMCOMF_SIP_ENACH_BRANCH_CODE","value" => "WB99","status" => "1"],
			["key" => "UPI_PAYMENT_ENABLED","value" => "1","status" => "1"],
			["key" => "UPI_PAYMENT_WAIT_TIMEOUT","value" => "5 minutes","status" => "1"],
			["key" => "BANK_TRANSFER_ENABLED","value" => "1","status" => "1"],
			["key" => "EMANDATE_MINIMUM_AMOUNT","value" => "10000","status" => "1"],
			["key" => "EMANDATE_AMOUNT_MULTIPLIER","value" => "5000","status" => "1"],
			["key" => "RTA_API_ERROR_EMAIL_NOTIFY_TO","value" => "rankmf_dev@samco.in","status" => "1"],
			["key" => "SAMCO-PARTNER-LIVE","value" => "0","status" => "1"],
			["key" => "HIT_CIVIL_KRA_AFTER_ESIGN","value" => "0","status" => "1"],
			["key" => "NAV_EXP_CRON_NOTIFICATION_EMAIL","value" => "dharmesh.patel@samco.in,prasad.wargad@samco.in","status" => "1"],
			["key" => "NAV_EXP_DATA_READ_TO_EMAIL","value" => "dailyuploads@samcomf.com","status" => "1"],
			["key" => "NAV_EXP_DATA_READ_TO_EMAIL_PASS","value" => "4&aqAqu89F","status" => "1"],
			["key" => "NAV_EXP_DATA_READ_TO_EMAIL_HOSTNAME","value" => "mail.samcomf.com","status" => "1"],
			["key" => "NAV_EXP_DATA_READ_FROM_EMAIL","value" => "balkrushna@samco.in","status" => "1"],
			["key" => "NAV_EXP_DATA_ZIP_FILE_PASS","value" => "SA45#zc50i","status" => "1"],
			["key" => "EXP_RATIO_EMAIL_SUBJECT_NAME","value" => "FA-Web_SAMCO_EXP_RATIO","status" => "1"],
			["key" => "NAV_EMAIL_SUBJECT_NAME","value" => "FA-SAMCO_Website_SAMCO_NAV.zip","status" => "1"],
			["key" => "NAV_EMAIL_ATTACHMENT_FILE_NAME","value" => "Website_SAMCO_NAV.zip","status" => "1"],
			["key" => "EXP_RATIO_EMAIL_ATTACHMENT_FILE_NAME","value" => "Web_SAMCO_EXP_RATIO.zip","status" => "1"],
			["key" => "ENABLE_FILE_BASED_LOGS","value" => "1","status" => "1"],
			["key" => "MAXMIMUM_SIP_END_DATE","value" => "2099-12-31","status" => "1"],
			["key" => "SIP_CANCEL_GAP_DAY","value" => "10","status" => "1"],
			["key" => "SAMCO_AMC_START_DATE","value" => "2022-01-17","status" => "1"],
			["key" => "MAXIMUM_REDEMPTION_AMOUNT_PERCENTAGE","value" => "5","status" => "1"],
			["key" => "ACTIVE_SHARE_EMAIL_NOTIFY_TO","value" => "rankmf_dev@samco.in","status" => "1"],
			["key" => "B30_CITIES_DEFAULT","value" => "AHMEDABAD, ALLAHABAD, BENGALURU, BHOPAL, BHUBANESHWAR, CHANDIGARH, CHENNAI, COIMBATORE, DEHRADUN, DELHI, GUWAHATI, HYDERABAD, INDORE, JAIPUR, JAMSHEDPUR, KANPUR, KOLKATA, LUCKNOW, MUMBAI, NAGPUR, NASHIK, PATNA, PUNE, RAJKOT, RANCHI, SURAT, UDAIPUR, VADODARA, VARANASI","status" => "1"],
			["key" => "B30_CITIES_FY_2021_2022","value" => "AHMEDABAD, ALLAHABAD, BENGALURU, BHOPAL, BHUBANESHWAR, CHANDIGARH, CHENNAI, COIMBATORE, DEHRADUN, DELHI, GUWAHATI, HYDERABAD, INDORE, JAIPUR, JAMSHEDPUR, KANPUR, KOLKATA, LUCKNOW, MUMBAI, NAGPUR, NASHIK, PATNA, PUNE, RAJKOT, RANCHI, SURAT, UDAIPUR, VADODARA, VARANASI","status" => "1"],
			["key" => "B30_CITIES_FY_2022_2023","value" => "AHMEDABAD, AGRA, BENGALURU, BHOPAL, BHUBANESHWAR, CHANDIGARH, CHENNAI, COIMBATORE, DEHRADUN, DELHI, GUWAHATI, HYDERABAD, INDORE, JAIPUR, JAMSHEDPUR, KANPUR, KOLKATA, LUCKNOW, MUMBAI, NAGPUR, NASHIK, PATNA, PUNE, RAJKOT, RANCHI, SURAT, UDAIPUR, VADODARA, VARANASI","status" => "1"],
			["key" => "ALLOW_FRESH_FOLIO_CREATION_TO_OFFLINE_INVESTOR","value" => "no","status" => "1"],
			["key" => "ALLOW_FRESH_FOLIO_CREATION_TO_OTHER_THAN_OFFLINE_INVESTOR","value" => "no","status" => "1"],
			["key" => "PUTIN-SIP-START","value" => "2022-07-01","status" => "1"],
			["key" => "PUTIN-SIP-END","value" => "2022-09-30","status" => "1"],
			["key" => "PUTIN-SIP-DASHBOARD","value" => "1","status" => "1"],
			["key" => "PARTNER_SAVE_KFINTECH","value" => "1","status" => "1"],
			["key" => "EMPANELEMENT_ALERT_EMAIL","value" => "mannu.rajak@samcomf.com","status" => "1"],
			["key" => "EMPANELEMENT_ALERT_NAME","value" => "Mannu","status" => "1"],
			["key" => "EMPANELEMENT_ALERT_MOBILE","value" => "9702790831","status" => "1"],
			["key" => "PIVOT-SIP-START","value" => "2023-03-01","status" => "1"],
			["key" => "PIVOT-SIP-END","value" => "2023-06-30","status" => "1"],
			["key" => "PIVOT-SIP-DASHBOARD","value" => "1","status" => "1"],
			["key" => "PIVOT-SIP-STARTSD","value" => "2023-03-01","status" => "1"],
			["key" => "PIVOT-SIP-STARTED","value" => "2023-07-31","status" => "1"],
			["key" => "PIVOT-SIP-AMOUNT","value" => "2000","status" => "1"],
			["key" => "PIVOT-SIP-MONTH","value" => "36","status" => "1"],
			["key" => "PIVOT-SIP-SEAT-AMOUNT","value" => "250000","status" => "1"],
			["key" => "PIVOT-SIP-TERMETTED","value" => "2023-07-31","status" => "1"],
			["key" => "PAN_AADHAAR_MANDATORY_LINKING","value" => "0","status" => "1"],
			["key" => "PAN_AADHAAR_LINKED_SUCCESS_MSG","value" => "Existing and Valid. Aadhaar Seeding is Successful.","status" => "1"],
			["key" => "SHOW_PAN_AADHAAR_LINKING_POPUP_MSG","value" => "1","status" => "1"],
			["key" => "PIVOT-WEEKLY-DASHBOARD","value" => "Fri","status" => "1"],
			["key" => "BILLDESK_PAYMENT_GATEWAY_CHECKSUM_KEY_LIVE","value" => "J3uGaevLBO5bco9IUUW87iziU7Tf8ywW","status" => "1"],
			["key" => "BDM_TARGET_MEETINGS","value" => "3","status" => "1"],
			["key" => "BDM_TARGET_CALLS","value" => "15","status" => "1"],
		];

        foreach ($data as $row) {
            $condition = ['key' => $row['key']];
            DB::table('settings')->updateOrInsert(
                $condition,
                $row
            );
        }
    }
}
