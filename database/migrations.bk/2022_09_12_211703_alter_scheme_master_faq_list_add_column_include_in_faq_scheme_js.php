<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchemeMasterFaqListAddColumnIncludeInFaqSchemeJs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column include_in_faq_scheme_js in MySQL table: scheme_master_faq_list
        Schema::table('scheme_master_faq_list', function (Blueprint $table) {
            $table->tinyInteger('include_in_faq_scheme_js')->nullable()->default(0)->comment('Include this question in SCHEMA.ORG javascript tags or not. 0=No, 1=Yes')->after('answer');
        });
        DB::statement("UPDATE scheme_master_faq_list SET created_at = created_at, updated_at = updated_at, include_in_faq_scheme_js = 1 WHERE RTA_Scheme_Code IN ('FCRG','FCDG') AND status = '1' AND question NOT IN ('What is the asset allocation pattern for Samco Flexi Cap Fund?');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column include_in_faq_scheme_js in MySQL table: scheme_master_faq_list
        Schema::table('scheme_master_faq_list', function (Blueprint $table) {
            $table->dropColumn('include_in_faq_scheme_js');
        });
    }
}
