<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemeMasterAdditionalInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheme_master_additional_information', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('RTA_Scheme_Code', 50)->nullable()->comment('RTA scheme code');
            $table->string('key', 255)->nullable()->comment('Key to be used for display/identification');
            $table->text('value')->nullable()->comment('Value for the key assigned, which can be either PLAIN TEXT/HTML');
            $table->integer('ordering_value')->nullable()->default(1)->comment('Used for ordering records as per requirement');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('status');
            $table->foreign('RTA_Scheme_Code')->references('RTA_Scheme_Code')->on('scheme_master')->onUpdate('cascade')->onDelete('cascade');
        });

        // inserting default records for an existing SAMCO FLEXI CAP SCHEME
        DB::table('scheme_master_additional_information')->insert(
            array(
                array('RTA_Scheme_Code' => 'FCRG', 'key' => 'Key Highlights', 'value' => 'Investing in portfolio of 25 efficient HexaShield tested businesses'),
                array('RTA_Scheme_Code' => 'FCRG', 'key' => 'Key Highlights', 'value' => 'Investing in high growth Indian and global businesses'),
                array('RTA_Scheme_Code' => 'FCRG', 'key' => 'Key Highlights', 'value' => 'Fund portfolio with high active share (>80%)'),
                array('RTA_Scheme_Code' => 'FCRG', 'key' => 'Key Highlights', 'value' => 'Equity fund, no derivatives & no hedging'),
                array('RTA_Scheme_Code' => 'FCRG', 'key' => 'Key Highlights', 'value' => 'Well-defined exit framework'),
                array('RTA_Scheme_Code' => 'FCRG', 'key' => 'Key Highlights', 'value' => '65% Indian equities (min)<br>35% Global equities (max)'),
                array('RTA_Scheme_Code' => 'FCRG', 'key' => 'Riskometer Popup Text1', 'value' => '<div class="col-lg-12 mb-20"><div class="sch-gr-details"><p><strong>This product is suitable for investors who are seeking* :</strong></p><ul class="scheem-list-desc"><li>To generate long-term capital growth;</li><li>Investment in Indian &amp; foreign equity instruments across market capitalization;</li></ul></div></div><div class="col-lg-12 mb-20"><p class="m-0 font12">*Investors should consult their financial advisers if in doubt about whether the product is suitable for them.</p></div>'),
                array('RTA_Scheme_Code' => 'FCDG', 'key' => 'Key Highlights', 'value' => 'Investing in portfolio of 25 efficient HexaShield tested businesses'),
                array('RTA_Scheme_Code' => 'FCDG', 'key' => 'Key Highlights', 'value' => 'Investing in high growth Indian and global businesses'),
                array('RTA_Scheme_Code' => 'FCDG', 'key' => 'Key Highlights', 'value' => 'Fund portfolio with high active share (>80%)'),
                array('RTA_Scheme_Code' => 'FCDG', 'key' => 'Key Highlights', 'value' => 'Equity fund, no derivatives & no hedging'),
                array('RTA_Scheme_Code' => 'FCDG', 'key' => 'Key Highlights', 'value' => 'Well-defined exit framework'),
                array('RTA_Scheme_Code' => 'FCDG', 'key' => 'Key Highlights', 'value' => '65% Indian equities (min)<br>35% Global equities (max)'),
                array('RTA_Scheme_Code' => 'FCDG', 'key' => 'Riskometer Popup Text1', 'value' => '<div class="col-lg-12 mb-20"><div class="sch-gr-details"><p><strong>This product is suitable for investors who are seeking* :</strong></p><ul class="scheem-list-desc"><li>To generate long-term capital growth;</li><li>Investment in Indian &amp; foreign equity instruments across market capitalization;</li></ul></div></div><div class="col-lg-12 mb-20"><p class="m-0 font12">*Investors should consult their financial advisers if in doubt about whether the product is suitable for them.</p></div>'),
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
        Schema::dropIfExists('scheme_master_additional_information');
    }
}
