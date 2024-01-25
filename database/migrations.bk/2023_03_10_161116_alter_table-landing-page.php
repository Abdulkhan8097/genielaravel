<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableLandingPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landing_page', function (Blueprint $table) {
            $table->string('RTA_Scheme_Code', 50)->nullable()->comment('RTA Scheme Code')->after('campaign_name');
            $table->string('view_name', 50)->nullable()->comment('view name')->after('RTA_Scheme_Code');
        });
        DB::statement("UPDATE landing_page SET created_at = created_at, updated_at = updated_at, RTA_Scheme_Code = 'ELRG', view_name = from_site WHERE description LIKE '%ELSS%';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landing_page', function (Blueprint $table) {
            $table->dropColumn(['RTA_Scheme_Code']);
            $table->dropColumn(['view_name']);
        });
    }
}
