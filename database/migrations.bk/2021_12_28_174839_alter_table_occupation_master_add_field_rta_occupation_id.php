<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOccupationMasterAddFieldRtaOccupationId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('occupation_master', function (Blueprint $table) {
            $table->string('rta_occupation_id')->nullable()->comment('RTA occupation master primary id')->after('description');
        });

        DB::statement('TRUNCATE TABLE occupation_master;');
        DB::table('occupation_master')->insert(array(
                    array('code' => 'SALR', 'description' => 'SERVICE', 'rta_occupation_id' => 1, 'status' => 1),
                    array('code' => 'BUSI', 'description' => 'BUSINESS', 'rta_occupation_id' => 2, 'status' => 1),
                    array('code' => 'HSWF', 'description' => 'HOUSEWIFE', 'rta_occupation_id' => 4, 'status' => 1),
                    array('code' => 'STUD', 'description' => 'STUDENT', 'rta_occupation_id' => 7, 'status' => 1),
                    array('code' => 'RETD', 'description' => 'RETIRED', 'rta_occupation_id' => 8, 'status' => 1),
                    array('code' => 'OTHR', 'description' => 'OTHERS', 'rta_occupation_id' => 9, 'status' => 1)
                )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('occupation_master', function (Blueprint $table) {
            $table->dropColumn(['rta_occupation_id']);
        });
    }
}
