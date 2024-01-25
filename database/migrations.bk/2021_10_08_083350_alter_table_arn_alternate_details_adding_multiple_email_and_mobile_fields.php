<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableArnAlternateDetailsAddingMultipleEmailAndMobileFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding multiple mobile and email id fields in MySQL table: arn_alternate_details
        Schema::table('arn_alternate_details', function (Blueprint $table) {
            $table->string('alternate_mobile_1', 20)->nullable()->comment('Alternate mobile number 1')->after('mobile');
            $table->string('alternate_mobile_2', 20)->nullable()->comment('Alternate mobile number 2')->after('alternate_mobile_1');
            $table->string('alternate_mobile_3', 20)->nullable()->comment('Alternate mobile number 3')->after('alternate_mobile_2');
            $table->string('alternate_mobile_4', 20)->nullable()->comment('Alternate mobile number 4')->after('alternate_mobile_3');
            $table->string('alternate_mobile_5', 20)->nullable()->comment('Alternate mobile number 5')->after('alternate_mobile_4');
            $table->string('alternate_email_1', 255)->nullable()->comment('Alternate email id 1')->after('email');
            $table->string('alternate_email_2', 255)->nullable()->comment('Alternate email id 2')->after('alternate_email_1');
            $table->string('alternate_email_3', 255)->nullable()->comment('Alternate email id 3')->after('alternate_email_2');
            $table->string('alternate_email_4', 255)->nullable()->comment('Alternate email id 4')->after('alternate_email_3');
            $table->string('alternate_email_5', 255)->nullable()->comment('Alternate email id 5')->after('alternate_email_4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added multiple mobile and email id fields in MySQL table: arn_alternate_details
        Schema::table('arn_alternate_details', function (Blueprint $table) {
            $table->dropColumn(['alternate_mobile_1','alternate_mobile_2','alternate_mobile_3','alternate_mobile_4','alternate_mobile_5', 'alternate_email_1', 'alternate_email_2', 'alternate_email_3', 'alternate_email_4', 'alternate_email_5']);
        });
    }
}
