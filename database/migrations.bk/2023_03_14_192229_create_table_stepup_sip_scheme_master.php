<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableStepupSipSchemeMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
       DB::statement("CREATE TABLE stepup_sip_scheme_master(
        id INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'Serial ID' PRIMARY KEY,
        rta_scheme_code VARCHAR(50) NOT NULL COMMENT 'RTA Scheme Code, foreign key reference to scheme_master >> RTA_Scheme_Code',
        sip_frequency VARCHAR(20) NOT NULL COMMENT 'Frequency for which the configuration needs to be considered',
        increase_by_amount TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Is it allowed to use amount option while doing the step up? 1 = Yes, 0 = No',
        sip_min_installment_amt INTEGER(20) NOT NULL DEFAULT '500' COMMENT 'Minimum amount to be considered if field increase_by_amount have value as 1',
        sip_multiplier_amt INTEGER(20) NOT NULL DEFAULT '1' COMMENT 'Multiplier amount to be considered if field increase_by_amount have value as 1',
        increase_by_percentage TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Is it allowed to use percentage option while doing the step up? 1 = Yes, 0 = No',
        sip_min_installment_percentage INTEGER(20) NOT NULL DEFAULT '10' COMMENT 'Minimum percentage to be considered if field increase_by_percentage have value as 1',
        sip_multiplier_percentage INTEGER(20) NOT NULL DEFAULT '5' COMMENT 'Multiplier percentage to be considered if field increase_by_percentage have value as 1',
        sip_maximum_topup_amount INTEGER(20) NOT NULL DEFAULT '1000000' COMMENT 'After reaching this amount SIP will stopped from being stepped up',
        created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created at',
        updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated at',
        KEY idx_rta_scheme_code(rta_scheme_code));");

        DB::statement("INSERT INTO stepup_sip_scheme_master(rta_scheme_code, sip_frequency, increase_by_amount, sip_min_installment_amt, sip_multiplier_amt, increase_by_percentage, sip_min_installment_percentage, sip_multiplier_percentage, sip_maximum_topup_amount, created_at) VALUES('FCRG', 'HALF YEARLY', 1, 500, 1, 1, 10, 5, 1000000, NOW()), ('FCRG', 'YEARLY', 1, 500, 1, 1, 10, 5, 1000000, NOW()), ('FCDG', 'HALF YEARLY', 1, 500, 1, 1, 10, 5, 1000000, NOW()), ('FCDG', 'YEARLY', 1, 500, 1, 1, 10, 5, 1000000, NOW()), ('ONRG', 'HALF YEARLY', 1, 500, 1, 1, 10, 5, 1000000, NOW()), ('ONRG', 'YEARLY', 1, 500, 1, 1, 10, 5, 1000000, NOW()), ('ONDG', 'HALF YEARLY', 1, 500, 1, 1, 10, 5, 1000000, NOW()), ('ONDG', 'YEARLY', 1, 500, 1, 1, 10, 5, 1000000, NOW()), ('ELRG', 'HALF YEARLY', 1, 500, 1, 0, 10, 5, 1000000, NOW()), ('ELRG', 'YEARLY', 1, 500, 1, 0, 10, 5, 1000000, NOW()), ('ELDG', 'HALF YEARLY', 1, 500, 1, 0, 10, 5, 1000000, NOW()), ('ELDG', 'YEARLY', 1, 500, 1, 0, 10, 5, 1000000, NOW());");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stepup_sip_scheme_master');
    }
}
