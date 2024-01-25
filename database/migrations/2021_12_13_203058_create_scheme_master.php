<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemeMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE TABLE `scheme_master` (
                        `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
                        `Unique_No` varchar(20) DEFAULT NULL,
                        `Scheme_Code` varchar(50) DEFAULT NULL,
                        `RTA_Scheme_Code` varchar(50) DEFAULT NULL,
                        `AMC_Scheme_Code` varchar(50) DEFAULT NULL,
                        `ISIN` varchar(50) DEFAULT NULL,
                        `AMC_Code` varchar(255) DEFAULT NULL,
                        `Scheme_Type` varchar(50) DEFAULT NULL,
                        `Scheme_Plan` varchar(20) DEFAULT NULL,
                        `Scheme_Name` varchar(255) DEFAULT NULL,
                        `Purchase_Allowed` varchar(10) DEFAULT NULL,
                        `Purchase_Transaction_mode` varchar(10) DEFAULT NULL,
                        `Minimum_Purchase_Amount` varchar(20) DEFAULT NULL,
                        `Additional_Purchase_Amount` varchar(20) DEFAULT NULL,
                        `Maximum_Purchase_Amount` varchar(50) DEFAULT NULL,
                        `Purchase_Amount_Multiplier` varchar(20) DEFAULT NULL,
                        `Purchase_Cutoff_Time` varchar(10) DEFAULT NULL,
                        `Redemption_Allowed` varchar(20) DEFAULT NULL,
                        `Redemption_Transaction_Mode` varchar(20) DEFAULT NULL,
                        `Minimum_Redemption_Qty` varchar(20) DEFAULT NULL,
                        `Redemption_Qty_Multiplier` varchar(20) DEFAULT NULL,
                        `Maximum_Redemption_Qty` varchar(20) DEFAULT NULL,
                        `Redemption_Amount_Minimum` varchar(20) DEFAULT NULL,
                        `Redemption_Amount_Maximum` varchar(20) DEFAULT NULL,
                        `Redemption_Amount_Multiple` varchar(20) DEFAULT NULL,
                        `Redemption_Cut_off_Time` varchar(20) DEFAULT NULL,
                        `RTA_Agent_Code` varchar(30) DEFAULT NULL,
                        `AMC_Active_Flag` varchar(5) DEFAULT NULL,
                        `Dividend_Reinvestment_Flag` varchar(5) DEFAULT NULL,
                        `SIP_FLAG` varchar(5) DEFAULT NULL,
                        `STP_FLAG` varchar(5) DEFAULT NULL,
                        `SWP_Flag` varchar(5) DEFAULT NULL,
                        `Switch_FLAG` varchar(5) DEFAULT NULL,
                        `SETTLEMENT_TYPE` varchar(10) DEFAULT NULL,
                        `AMC_IND` varchar(10) DEFAULT NULL,
                        `Face_Value` varchar(10) DEFAULT NULL,
                        `Start_Date` varchar(30) DEFAULT NULL,
                        `End_Date` varchar(30) DEFAULT NULL,
                        `Exit_Load_Flag` varchar(5) DEFAULT NULL,
                        `Exit_Load` varchar(255) DEFAULT NULL,
                        `Lock_in_Period_Flag` varchar(10) DEFAULT NULL,
                        `Lock_in_Period` varchar(255) DEFAULT NULL,
                        `Channel_Partner_Code` varchar(50) DEFAULT NULL,
                        `scheme_flag` tinyint(1) NULL DEFAULT '0' COMMENT '0 - not investable,1 - investable,2 investable but schemecode not found',
                        `for_date` date DEFAULT NULL,
                        `tbl_soft_delete_status` tinyint(1) NULL DEFAULT '2',
                        `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`),
                        KEY `Scheme_Code` (`Scheme_Code`),
                        KEY `ISIN` (`ISIN`),
                        KEY `Purchase_Allowed` (`Purchase_Allowed`),
                        KEY `Purchase_Transaction_mode` (`Purchase_Transaction_mode`),
                        KEY `SIP_FLAG` (`SIP_FLAG`),
                        KEY `Scheme_Plan` (`Scheme_Plan`),
                        KEY `AMC_Active_Flag` (`AMC_Active_Flag`),
                        KEY `scheme_flag` (`scheme_flag`),
                        KEY `soft_delete_status` (`tbl_soft_delete_status`),
                        KEY `channel_partner_code` (`Channel_Partner_Code`(20)),
                        KEY `idx_Unique_No` (`Unique_No`),
                        FULLTEXT KEY `Scheme_Name` (`Scheme_Name`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        $insert_data = array(array('RTA_Scheme_Code' => 'FCRG', 'AMC_Scheme_Code' => 'FCRG', 'Scheme_Type' => 'Equity',
                                'Scheme_Plan' => 'Normal', 'Scheme_Name' => 'SAMCO FLEXI CAP FUND - REGULAR GROWTH',
                                'Purchase_Allowed' => 'Y', 'Purchase_Transaction_mode' => 'DP', 'Minimum_Purchase_Amount' => '5000',
                                'Additional_Purchase_Amount' => '500', 'Maximum_Purchase_Amount' => '0',
                                'Purchase_Amount_Multiplier' => '1', 'Purchase_Cutoff_Time' => '15:00:00', 'Redemption_Allowed' => 'N',
                                'Redemption_Transaction_Mode' => 'DP', 'Minimum_Redemption_Qty' => '0.001',
                                'Redemption_Qty_Multiplier' => '0.001', 'Maximum_Redemption_Qty' => '0',
                                'Redemption_Amount_Minimum' => '0.01', 'Redemption_Amount_Maximum' => '0',
                                'Redemption_Amount_Multiple' => '0.01', 'Redemption_Cut_off_Time' => '15:00:00',
                                'RTA_Agent_Code' => 'KFIN', 'AMC_Active_Flag' => '1', 'Dividend_Reinvestment_Flag' => 'N',
                                'SIP_FLAG' => 'Y', 'STP_FLAG' => 'N', 'SWP_Flag' => 'N', 'Switch_FLAG' => 'N', 'SETTLEMENT_TYPE' => 'MF',
                                'AMC_IND' => '', 'Face_Value' => 'MF', 'Start_Date' => 'JAN 17 2022', 'End_Date' => 'JAN 31 2022',
                                'Exit_Load_Flag' => 'Y', 'Exit_Load' => '0', 'Lock_in_Period_Flag' => 'N', 'Lock_in_Period' => '0',
                                'Channel_Partner_Code' => 'FCRG', 'scheme_flag' => '1', 'for_date' => '2021-12-13',
                                'tbl_soft_delete_status' => 1, 'created' => '2021-12-13 21:00:00'),
                            array('RTA_Scheme_Code' => 'FCDG', 'AMC_Scheme_Code' => 'FCDG', 'Scheme_Type' => 'Equity',
                                'Scheme_Plan' => 'Direct', 'Scheme_Name' => 'SAMCO FLEXI CAP FUND - DIRECT GROWTH',
                                'Purchase_Allowed' => 'Y', 'Purchase_Transaction_mode' => 'DP', 'Minimum_Purchase_Amount' => '5000',
                                'Additional_Purchase_Amount' => '500', 'Maximum_Purchase_Amount' => '0',
                                'Purchase_Amount_Multiplier' => '1', 'Purchase_Cutoff_Time' => '15:00:00', 'Redemption_Allowed' => 'N',
                                'Redemption_Transaction_Mode' => 'DP', 'Minimum_Redemption_Qty' => '0.001',
                                'Redemption_Qty_Multiplier' => '0.001', 'Maximum_Redemption_Qty' => '0',
                                'Redemption_Amount_Minimum' => '0', 'Redemption_Amount_Maximum' => '0',
                                'Redemption_Amount_Multiple' => '0.01', 'Redemption_Cut_off_Time' => '15:00:00',
                                'RTA_Agent_Code' => 'KFIN', 'AMC_Active_Flag' => '1', 'Dividend_Reinvestment_Flag' => 'N',
                                'SIP_FLAG' => 'Y', 'STP_FLAG' => 'N', 'SWP_Flag' => 'N', 'Switch_FLAG' => 'N', 'SETTLEMENT_TYPE' => 'MF',
                                'AMC_IND' => '', 'Face_Value' => 'MF', 'Start_Date' => 'JAN 17 2022', 'End_Date' => 'JAN 31 2022',
                                'Exit_Load_Flag' => 'Y', 'Exit_Load' => '0', 'Lock_in_Period_Flag' => 'N', 'Lock_in_Period' => '0',
                                'Channel_Partner_Code' => 'FCRG', 'scheme_flag' => '1', 'for_date' => '2021-12-13',
                                'tbl_soft_delete_status' => 1, 'created' => '2021-12-13 21:00:00')
                    );
        DB::table('scheme_master')->insert($insert_data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheme_master');
    }
}
