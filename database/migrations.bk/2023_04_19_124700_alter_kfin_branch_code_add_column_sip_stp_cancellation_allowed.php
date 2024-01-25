<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterKfinBranchCodeAddColumnSipStpCancellationAllowed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding columns like sip_cancellation_allowed & stp_cancellation_allowed in MySQL table: kfin_branch_code
        Schema::table('kfin_branch_code', function (Blueprint $table) {
            $table->string('distributor_name')->nullable()->comment('Distributor Name')->after('branch_name');
            $table->tinyInteger('sip_cancellation_allowed')->nullable()->default(0)->comment('Is SIP Cancellation Allowed? 1 = Yes, 0 = No')->after('distributor_name');
            $table->tinyInteger('stp_cancellation_allowed')->nullable()->default(0)->comment('Is STP Cancellation Allowed? 1 = Yes, 0 = No')->after('sip_cancellation_allowed');
        });

        // Updating list of given set of branches as SIP/STP allowed for cancellation
        DB::statement("UPDATE kfin_branch_code SET created_at = created_at, updated_at = updated_at, sip_cancellation_allowed = 1, stp_cancellation_allowed = 1 WHERE branch_code IN ('AB40','AG01','AG26','AH08','AJ44','AK23','AL01','AL17','AL28','AM23','AM41','AN65','AP89','AS64','AU59','AZ19','BA01','BA15','BA38','BB04','BE40','BG40','BH01','BH24','BH40','BI01','BI69','BK01','BK72','BL40','BN04','BP66','BR82','BS78','BT01','BU01','BV55','BY21','CA01','CB06','CH21','CK09','CL62','CO08','CS83','DA94','DA99','DB42','DH22','DH40','DL01','DO16','DU52','DV15','EL03','ER54','FA56','FE20','GA01','GA92','GB62','GD16','GL68','GN43','GO22','GP71','GR74','GU56','GW43','GY81','GZ57','HA16','HD09','HI40','HO01','HS62','HU27','HY34','IN23','JA07','JB02','JD88','JG37','JH73','JK10','JL32','JM42','JN12','JO97','JP79','KA03','KA21','KA71','KC56','KC62','KC92','KH40','KM12','KN14','KO56','KR96','KR99','KU03','KU29','KW99','KY63','LK13','LU20','MA47','MD05','MD10','ME35','MH60','MI34','ML97','MN53','MO20','MO29','MO36','MR75','MU96','MY57','MZ84','NA22','ND67','NG26','NK25','NN22','NO13','NV40','PA31','PD46','PG35','PK32','PP93','PT11','PU07','RA25','RJ33','RJ61','RK70','RN29','RO16','RO40','RT39','RW79','SA45','SA66','SB80','SG01','SH71','SH91','SI01','SI48','SL02','SL04','SM59','SN09','SO49','SP06','SR76','ST49','SU19','SU29','SV29','TC40','TD58','TI75','TP89','TR34','TU51','TV50','UD98','UJ23','VA39','VD40','VJ26','VL88','VP95','VZ76','WB99','WR89','YA21','KH03','KC75');");
        DB::statement("UPDATE kfin_branch_code SET created_at = created_at, updated_at = updated_at, distributor_name = 'AMC Web Site' WHERE branch_code = 'WB99';");
        DB::statement("UPDATE kfin_branch_code SET created_at = created_at, updated_at = updated_at, distributor_name = 'Channel Partner' WHERE branch_code IN ('HD99','IW99','P999','PC99','RR99','WI99');");
        DB::statement("UPDATE kfin_branch_code SET created_at = created_at, updated_at = updated_at, distributor_name = 'Exchange' WHERE branch_code IN ('BS77','BS88','NS77','NS88');");
        DB::statement("UPDATE kfin_branch_code SET created_at = created_at, updated_at = updated_at, distributor_name = 'Kfintech Digital' WHERE branch_code IN ('KR99','KW99');");
        DB::statement("UPDATE kfin_branch_code SET created_at = created_at, updated_at = updated_at, distributor_name = 'Kfintech Digital Distributor App' WHERE branch_code = 'DA99';");
        DB::statement("UPDATE kfin_branch_code SET created_at = created_at, updated_at = updated_at, distributor_name = 'MFU' WHERE branch_code = 'MU99';");
        DB::statement("UPDATE kfin_branch_code SET created_at = created_at, updated_at = updated_at, distributor_name = 'RIA' WHERE branch_code = 'R999';");
        DB::statement("UPDATE kfin_branch_code SET created_at = created_at, updated_at = updated_at, distributor_name = 'Offline' WHERE branch_code IN ('AB40','AG01','AG26','AH08','AJ44','AK23','AL01','AL17','AL28','AM23','AM41','AN65','AP89','AS64','AU59','AZ19','BA01','BA15','BA38','BB04','BE40','BG40','BH01','BH24','BH40','BI01','BI69','BK01','BK72','BL40','BN04','BP66','BR82','BS78','BT01','BU01','BV55','BY21','CA01','CB06','CH21','CK09','CL62','CO08','CS83','DA94','DB42','DH22','DH40','DL01','DO16','DU52','DV15','EL03','ER54','FA56','FE20','GA01','GA92','GB62','GD16','GL68','GN43','GO22','GP71','GR74','GU56','GW43','GY81','GZ57','HA16','HD09','HI40','HO01','HS62','HU27','HY34','IN23','JA07','JB02','JD88','JG37','JH73','JK10','JL32','JM42','JN12','JO97','JP79','KA03','KA21','KA71','KC56','KC62','KC92','KH40','KM12','KN14','KO56','KR96','KU03','KU29','KY63','LK13','LU20','MA47','MD05','MD10','ME35','MH60','MI34','ML97','MN53','MO20','MO29','MO36','MR75','MU96','MY57','MZ84','NA22','ND67','NG26','NK25','NN22','NO13','NV40','PA31','PD46','PG35','PK32','PP93','PT11','PU07','RA25','RJ33','RJ61','RK70','RN29','RO16','RO40','RT39','RW79','SA45','SA66','SB80','SG01','SH71','SH91','SI01','SI48','SL02','SL04','SM59','SN09','SO49','SP06','SR76','ST49','SU19','SU29','SV29','TC40','TD58','TI75','TP89','TR34','TU51','TV50','UD98','UJ23','VA39','VD40','VJ26','VL88','VP95','VZ76','WR89','YA21','KH03','KC75');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added columns like sip_cancellation_allowed & stp_cancellation_allowed from MySQL table: kfin_branch_code
        Schema::table('kfin_branch_code', function (Blueprint $table) {
            $table->dropColumn(['sip_cancellation_allowed']);
            $table->dropColumn(['stp_cancellation_allowed']);
        });
    }
}
