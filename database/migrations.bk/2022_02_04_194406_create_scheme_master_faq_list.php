<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemeMasterFaqList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creating a scheme_master_faq_list table
        Schema::create('scheme_master_faq_list', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('RTA_Scheme_Code', 50)->nullable()->comment('RTA scheme code');
            $table->string('question')->comment('Question');
            $table->text('answer')->comment('Answer can contain HTML');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('RTA_Scheme_Code');
            $table->index('status');
            $table->foreign('RTA_Scheme_Code')->references('RTA_Scheme_Code')->on('scheme_master');
        });

        DB::table('scheme_master_faq_list')->insert(
            array(
                array('RTA_Scheme_Code' => 'FCRG',
                      'question' => 'Is this scheme suitable for me?',
                      'answer' => "<p>Samco Flexi Cap Fund is suitable for all investors who want to invest in equity markets for a minimum period of 3 years and are looking to own efficient businesses across the globe.</p>"),
                array('RTA_Scheme_Code' => 'FCRG',
                      'question' => 'What is the asset allocation pattern for Samco Flexi Cap Fund?',
                      'answer' => '<table class="quick-table"><tbody><tr><td><a><strong>Type of Security</strong></a></td><td><a><strong>Minimum Allocation <br>(% of net assets)</strong></a></td><td><a><strong>Maximum Allocation <br>(% of net assets)</strong></a></td><td><a><strong>Risk Profile</strong></a></td></tr><tr><td><a><span>Indian Equity and Equity Related Instruments</span></a></td><td><a><span>65</span></a></td><td><a><span>100</span></a></td><td><a><span>High Risk</span></a></td></tr><tr><td><a><span>Foreign Equity and equity related instruments</span></a></td><td><a><span>0</span></a></td><td><a><span>35</span></a></td><td><a><span>High Risk</span></a></td></tr><tr><td><a><span>Tri-party Repo (TREPS) through CCIL</span></a></td><td><a><span>0</span></a></td><td><a><span>35</span></a></td><td><a><span>Low to Medium Risk</span></a></td></tr></tbody></table>'),
                array('RTA_Scheme_Code' => 'FCRG',
                      'question' => 'What is the benchmark of the Samco Flexi Cap Fund?',
                      'answer' => "<p>Samco Flexi Cap Fund's performance would be benchmarked against NIFTY500 TRI. Please understand that the performance of the benchmark is a broad measurement of the changes in the stock markets. It is to be used only for comparative purposes only and in no way indicates the potential performance of the Samco Flexi Cap Fund.</p>"),
                array('RTA_Scheme_Code' => 'FCRG',
                      'question' => 'Who can invest in the Samco Flexi Cap Fund?',
                      'answer' => '<p>Resident adult individuals either singly or jointly (not exceeding three) or on an Anyone or Survivor basis; 2. Hindu Undivided Family (HUF) through Karta; 3. Minor (as the first and the sole holder only) through a natural guardian (i.e. father or mother, as the case may be) or a court appointed legal guardian. There shall not be any joint holding with minor investments; 4. Partnership Firms including limited liability partnership firms; 5. Proprietorship in the name of the sole proprietor; 6. Companies, Bodies Corporate, Public Sector Undertakings (PSUs), Association of Persons (AOP) or Bodies of Individuals (BOI) and societies registered under the Societies Registration Act, 1860(so long as the purchase of Units is permitted under the respective constitutions); 7. Banks (including Co-operative Banks and Regional Rural Banks) and Financial Institutions; 8. Religious and Charitable Trusts, Wakfs or endowments of private trusts (subject to receipt of necessary approvals as "Public Securities" as required) and Private trusts authorised to invest in mutual fund schemes under their trust deeds; 9. Non-Resident Indians (NRIs) / Persons of Indian origin (PIOs)/ Overseas Citizen of India (OCI) residing abroad on repatriation basis or on non repatriation basis; 10 Foreign Institutional Investors (FIIs) and their sub-accounts registered with SEBI on repatriation basis; 11. Army, Air Force, Navy and other paramilitary units and bodies created by such institutions; 12. Scientific and Industrial Research Organizations; 13. Multilateral Funding Agencies / Bodies Corporate incorporated outside India with the permission of Government of India / RBI; 14. Provident/ Pension/ Gratuity Fund to the extent they are permitted; 15. Other schemes of Samco Mutual Fund or any other mutual fund subject to the conditions and limits prescribed by the SEBI (MF) Regulations; 16. Schemes of Alternative Investment Funds; 17. Trustee, AMC or Sponsor or their associates may subscribe to Units under the Scheme; 18. Qualified Foreign Investor (QFI) 19. Such other person as maybe decided by the AMC from time to time. The list given above is indicative and the applicable laws, if any, as amended from time to time shall supersede the list.</p>'),
                array('RTA_Scheme_Code' => 'FCRG',
                      'question' => 'How can Samco Flexi Cap keep a minimum turnover?',
                      'answer' => "<p>We are only investing in the best stress tested efficient businesses and we try to invest in them at an efficient price and hence it is assumed that we will not have to make too many changes in the portfolio</p>"),
                array('RTA_Scheme_Code' => 'FCRG',
                      'question' => "What will the Samco Flexi Cap's constituents be like?",
                      'answer' => "<p>It will have 25 of the best businesses across the globe with at least 65% of businesses from India and 35% from across the globe.</p>"),
                array('RTA_Scheme_Code' => 'FCRG',
                      'question' => "What is the E3 Strategy that is used in the Samco Flexi Cap Fund?",
                      'answer' => '<p>It is simple 3-step strategy that we follow at the fund level -</p><ul class="mt-20"><li>Step 1 - Invest in stress tested EFFICIENT companies.</li><li>Step 2 - Invest in these companies at an EFFICIENT price.</li><li>Step 3 - Maintain an EFFICIENT turnover ratio.</li></ul>'),
                array('RTA_Scheme_Code' => 'FCRG',
                      'question' => "What is Voluntary Dealing cost?",
                      'answer' => '<p>Voluntary dealing cost is something that is deducted from the NAV and occurs due to excessive turnover or changes in the portfolio of the fund. Samco Flexi Cap aims at keeping this cost to a minimum by reducing the change in the portfolio</p>'),
                array('RTA_Scheme_Code' => 'FCRG',
                      'question' => "What is FATCA?",
                      'answer' => '<p>The Foreign Account Tax Compliance Act (FATCA) is a United States Federal Law, aimed at prevention of tax evasion by United States taxpayers through use of offshore accounts. The provisions of FATCA essentially provide for 30% withholding tax on US source payments made to Foreign Financial Institutions unless they enter into an agreement with the Internal Revenue Service (US IRS) to provide information about accounts held with them by USA persons or entities (firms/companies/trusts) controlled by USA persons.</p>'),
                array('RTA_Scheme_Code' => 'FCDG',
                      'question' => 'Is this scheme suitable for me?',
                      'answer' => "<p>Samco Flexi Cap Fund is suitable for all investors who want to invest in equity markets for a minimum period of 3 years and are looking to own efficient businesses across the globe.</p>"),
                array('RTA_Scheme_Code' => 'FCDG',
                      'question' => 'What is the asset allocation pattern for Samco Flexi Cap Fund?',
                      'answer' => '<table class="quick-table"><tbody><tr><td><a><strong>Type of Security</strong></a></td><td><a><strong>Minimum Allocation <br>(% of net assets)</strong></a></td><td><a><strong>Maximum Allocation <br>(% of net assets)</strong></a></td><td><a><strong>Risk Profile</strong></a></td></tr><tr><td><a><span>Indian Equity and Equity Related Instruments</span></a></td><td><a><span>65</span></a></td><td><a><span>100</span></a></td><td><a><span>High Risk</span></a></td></tr><tr><td><a><span>Foreign Equity and equity related instruments</span></a></td><td><a><span>0</span></a></td><td><a><span>35</span></a></td><td><a><span>High Risk</span></a></td></tr><tr><td><a><span>Tri-party Repo (TREPS) through CCIL</span></a></td><td><a><span>0</span></a></td><td><a><span>35</span></a></td><td><a><span>Low to Medium Risk</span></a></td></tr></tbody></table>'),
                array('RTA_Scheme_Code' => 'FCDG',
                      'question' => 'What is the benchmark of the Samco Flexi Cap Fund?',
                      'answer' => "<p>Samco Flexi Cap Fund's performance would be benchmarked against NIFTY500 TRI. Please understand that the performance of the benchmark is a broad measurement of the changes in the stock markets. It is to be used only for comparative purposes only and in no way indicates the potential performance of the Samco Flexi Cap Fund.</p>"),
                array('RTA_Scheme_Code' => 'FCDG',
                      'question' => 'Who can invest in the Samco Flexi Cap Fund?',
                      'answer' => '<p>Resident adult individuals either singly or jointly (not exceeding three) or on an Anyone or Survivor basis; 2. Hindu Undivided Family (HUF) through Karta; 3. Minor (as the first and the sole holder only) through a natural guardian (i.e. father or mother, as the case may be) or a court appointed legal guardian. There shall not be any joint holding with minor investments; 4. Partnership Firms including limited liability partnership firms; 5. Proprietorship in the name of the sole proprietor; 6. Companies, Bodies Corporate, Public Sector Undertakings (PSUs), Association of Persons (AOP) or Bodies of Individuals (BOI) and societies registered under the Societies Registration Act, 1860(so long as the purchase of Units is permitted under the respective constitutions); 7. Banks (including Co-operative Banks and Regional Rural Banks) and Financial Institutions; 8. Religious and Charitable Trusts, Wakfs or endowments of private trusts (subject to receipt of necessary approvals as "Public Securities" as required) and Private trusts authorised to invest in mutual fund schemes under their trust deeds; 9. Non-Resident Indians (NRIs) / Persons of Indian origin (PIOs)/ Overseas Citizen of India (OCI) residing abroad on repatriation basis or on non repatriation basis; 10 Foreign Institutional Investors (FIIs) and their sub-accounts registered with SEBI on repatriation basis; 11. Army, Air Force, Navy and other paramilitary units and bodies created by such institutions; 12. Scientific and Industrial Research Organizations; 13. Multilateral Funding Agencies / Bodies Corporate incorporated outside India with the permission of Government of India / RBI; 14. Provident/ Pension/ Gratuity Fund to the extent they are permitted; 15. Other schemes of Samco Mutual Fund or any other mutual fund subject to the conditions and limits prescribed by the SEBI (MF) Regulations; 16. Schemes of Alternative Investment Funds; 17. Trustee, AMC or Sponsor or their associates may subscribe to Units under the Scheme; 18. Qualified Foreign Investor (QFI) 19. Such other person as maybe decided by the AMC from time to time. The list given above is indicative and the applicable laws, if any, as amended from time to time shall supersede the list.</p>'),
                array('RTA_Scheme_Code' => 'FCDG',
                      'question' => 'How can Samco Flexi Cap keep a minimum turnover?',
                      'answer' => "<p>We are only investing in the best stress tested efficient businesses and we try to invest in them at an efficient price and hence it is assumed that we will not have to make too many changes in the portfolio</p>"),
                array('RTA_Scheme_Code' => 'FCDG',
                      'question' => "What will the Samco Flexi Cap's constituents be like?",
                      'answer' => "<p>It will have 25 of the best businesses across the globe with at least 65% of businesses from India and 35% from across the globe.</p>"),
                array('RTA_Scheme_Code' => 'FCDG',
                      'question' => "What is the E3 Strategy that is used in the Samco Flexi Cap Fund?",
                      'answer' => '<p>It is simple 3-step strategy that we follow at the fund level -</p><ul class="mt-20"><li>Step 1 - Invest in stress tested EFFICIENT companies.</li><li>Step 2 - Invest in these companies at an EFFICIENT price.</li><li>Step 3 - Maintain an EFFICIENT turnover ratio.</li></ul>'),
                array('RTA_Scheme_Code' => 'FCDG',
                      'question' => "What is Voluntary Dealing cost?",
                      'answer' => '<p>Voluntary dealing cost is something that is deducted from the NAV and occurs due to excessive turnover or changes in the portfolio of the fund. Samco Flexi Cap aims at keeping this cost to a minimum by reducing the change in the portfolio</p>'),
                array('RTA_Scheme_Code' => 'FCDG',
                      'question' => "What is FATCA?",
                      'answer' => '<p>The Foreign Account Tax Compliance Act (FATCA) is a United States Federal Law, aimed at prevention of tax evasion by United States taxpayers through use of offshore accounts. The provisions of FATCA essentially provide for 30% withholding tax on US source payments made to Foreign Financial Institutions unless they enter into an agreement with the Internal Revenue Service (US IRS) to provide information about accounts held with them by USA persons or entities (firms/companies/trusts) controlled by USA persons.</p>'),
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
        Schema::dropIfExists('scheme_master_faq_list');
    }
}
