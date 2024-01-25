<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class sip_analytics_view extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::statement('DROP VIEW IF EXISTS sip_analytics_view');
		DB::statement("
		CREATE view sip_analytics_view as
		SELECT 
		mfd.broker_id, 
		mpr.ARN, 
		mfd.client_id, 
		cm.client_name, 
		cm.client_pan, 
		mfd.order_id, 
		mfd.sip_reg_id, 
		mfd.order_type, 
		mfd.buy_sell, 
		sma.asset_code, 
		sma.asset_type,
		sa.Schemecode as accord_scheme_code,
		mfd.folio_number,
		mfd.scheme_code, 
		sm.Scheme_Name as scheme_name, 
		mfd.start_date, 
		mfd.installment_amount, 
		mfd.order_status, 
		IFNULL(mfd.utr_no, '') AS utr_no, 
		ca.Navdate as nav_date, 
		ca.Navrs as nav, 
		'0' as current_aum, 
		mfd.source, 
		mfd.created_by, 
		mfd.date_created, 
		mfd.date_modified 
	  FROM 
		mutual_funds.mf_client_order mfd 
		INNER JOIN mutual_funds.mf_client_master cm ON cm.client_id = mfd.client_id
		INNER JOIN mutual_funds.mf_scheme_master sm ON sm.Scheme_Code = mfd.scheme_code
		INNER JOIN mutual_funds.mf_schemeisinmaster_accord sa ON sa.ISIN = sm.ISIN
		INNER JOIN mutual_funds.mf_scheme_details_accord sda ON sda.schemecode = sa.Schemecode
		INNER JOIN mutual_funds.mf_sclass_mst_accord sma ON sma.classcode = sda.classcode
		INNER JOIN mutual_funds.mf_currentnav_accord ca ON ca.Schemecode = sa.Schemecode
		INNER JOIN mutual_fund_partners.mfp_partner_registration mpr ON mpr.partner_code = mfd.broker_id
	  WHERE 
		mfd.`broker_id` IS NOT NULL 
		AND mfd.`broker_id` != '' 
		AND cm.`broker_id` IS NOT NULL 
		AND cm.`broker_id` != '' 
		AND mpr.ARN != '' 
		AND mfd.order_type IN ('xsip', 'XSIP', 'SIP') 
	  UNION ALL 
	  SELECT 
		mfi.broker_id, 
		mpr.ARN as sub_broker_arn, 
		mfi.client_id, 
		cm.client_name, 
		cm.client_pan, 
		mfi.order_id, 
		mfi.sip_reg_id, 
		mfi.order_type, 
		mfi.buy_sell, 
		sma.asset_code, 
		sma.asset_type,
		sa.Schemecode as accord_scheme_code,
		mfi.folio_number,
		mfi.scheme_code, 
		sm.Scheme_Name as scheme_name, 
		mfi.start_date, 
		mfi.installment_amount, 
		mfi.order_status, 
		'' as utr_no, 
		ca.Navdate as nav_date, 
		ca.Navrs as nav, 
		'0' as current_aum, 
		mfi.source, 
		mfi.created_by, 
		mfi.date_created, 
		mfi.date_modified 
	  FROM 
		mutual_funds.mf_client_order_mfi mfi 
		INNER JOIN mutual_funds.mf_client_master cm ON cm.client_id = mfi.client_id 
		AND cm.`broker_id` != '' 
		INNER JOIN mutual_funds.mf_scheme_master sm ON sm.Scheme_Code = mfi.scheme_code
		INNER JOIN mutual_funds.mf_schemeisinmaster_accord sa ON sa.ISIN = sm.ISIN
		INNER JOIN mutual_funds.mf_scheme_details_accord sda ON sda.schemecode = sa.Schemecode
		INNER JOIN mutual_funds.mf_sclass_mst_accord sma ON sma.classcode = sda.classcode
		INNER JOIN mutual_funds.mf_currentnav_accord ca ON ca.Schemecode = sa.Schemecode
		INNER JOIN mutual_fund_partners.mfp_partner_registration mpr ON mpr.partner_code = mfi.broker_id 
	  WHERE 
		mfi.`broker_id` IS NOT NULL 
		AND mfi.`broker_id` != '' 
		AND cm.`broker_id` IS NOT NULL 
		AND cm.`broker_id` != '' 
		AND mpr.ARN != '' 
		AND mfi.order_type IN ('xsip', 'XSIP', 'SIP');
		");
    }
}
