<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemeMasterDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding an index against RTA_Scheme_Code field available in MySQL table: scheme_master
        DB::statement("ALTER TABLE `scheme_master` ADD INDEX (`RTA_Scheme_Code`);");

        // creating a scheme_master_details table
        Schema::create('scheme_master_details', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('RTA_Scheme_Code', 50)->nullable()->comment('RTA scheme code');
            $table->text('meta_description')->nullable()->comment('Meta Description');
            $table->string('meta_keywords')->nullable()->comment('Meta Keywords');
            $table->string('meta_title')->nullable()->comment('Page title');
            $table->string('slug')->nullable()->comment('Scheme details page url');
            $table->string('short_objective')->nullable()->comment('Short Objective/About scheme description');
            $table->text('objective')->nullable()->comment('Objective/About scheme description');
            $table->string('scheme_type_text')->nullable()->comment('Type of scheme description');
            $table->string('scheme_plan_text')->nullable()->comment('Scheme plan description');
            $table->decimal('aum', 25, 4)->nullable()->comment('AUM of a scheme');
            $table->date('aum_date')->nullable()->comment('AUM details available on this date');
            $table->decimal('nav', 25, 4)->nullable()->comment('NAV of a scheme');
            $table->date('nav_date')->nullable()->comment('NAV available on this date');
            $table->decimal('expense_ratio', 25, 4)->nullable()->comment('Expense Ratio');
            $table->decimal('turnover_ratio', 25, 4)->nullable()->comment('Turnover Ratio');
            $table->decimal('1dayret', 25, 4)->nullable()->comment('1 day return percentage');
            $table->decimal('1weekret', 25, 4)->nullable()->comment('1 week return percentage');
            $table->decimal('1monthret', 25, 4)->nullable()->comment('1 month return percentage');
            $table->decimal('3monthret', 25, 4)->nullable()->comment('3 month return percentage');
            $table->decimal('6monthret', 25, 4)->nullable()->comment('6 month return percentage');
            $table->decimal('9monthret', 25, 4)->nullable()->comment('9 month return percentage');
            $table->decimal('1yearret', 25, 4)->nullable()->comment('1 year return percentage');
            $table->decimal('2yearret', 25, 4)->nullable()->comment('2 year return percentage');
            $table->decimal('3yearret', 25, 4)->nullable()->comment('3 year return percentage');
            $table->decimal('4yearret', 25, 4)->nullable()->comment('4 year return percentage');
            $table->decimal('5yearret', 25, 4)->nullable()->comment('5 year return percentage');
            $table->decimal('incret', 25, 4)->nullable()->comment('since inception return percentage');
            $table->string('risk_meter', 50)->nullable()->comment('Risk meter. Possible values like: Low, High, Very High, Moderate etc.');
            $table->decimal('active_share', 25, 4)->nullable()->comment('Active share percentage');
            $table->decimal('vdc', 25, 4)->nullable()->comment('VDC percentage');
            $table->unsignedBigInteger('fund_mgr1_id')->nullable()->comment('Fund manager 1 id, foreign key references to table: scheme_fund_manager_master');
            $table->unsignedBigInteger('fund_mgr2_id')->nullable()->comment('Fund manager 2 id, foreign key references to table: scheme_fund_manager_master');
            $table->unsignedBigInteger('fund_mgr3_id')->nullable()->comment('Fund manager 3 id, foreign key references to table: scheme_fund_manager_master');
            $table->unsignedBigInteger('fund_mgr4_id')->nullable()->comment('Fund manager 4 id, foreign key references to table: scheme_fund_manager_master');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('RTA_Scheme_Code');
            $table->index('status');
            $table->foreign('RTA_Scheme_Code')->references('RTA_Scheme_Code')->on('scheme_master');
        });

        DB::table('scheme_master_details')->insert(
            array(
                array('RTA_Scheme_Code' => 'FCRG',
                      'meta_title' => 'Samco Flexi Cap Fund - Regular Growth',
                      'slug' => 'samco-flexi-cap-fund-regular-growth',
                      'short_objective' => ' Flexi cap fund Investing in efficient stress tested Indian & global companies at efficient prices.',
                      'objective' => 'Samco Flexi Cap Fund will invest in 25 stress tested efficient companies from India & across the globe at an efficient price, maintaining an efficient portfolio turnover & cost to generate superior risk-adjusted return for investors over long term.',
                      'scheme_type_text' => 'An open-ended dynamic equity scheme investing across large cap, mid cap, small cap stocks',
                      'scheme_plan_text' => 'Regular Plan - Growth',
                      'risk_meter' => 'Very High',
                      'fund_mgr1_id' => 1,
                      'fund_mgr2_id' => 2),
                array('RTA_Scheme_Code' => 'FCDG',
                      'meta_title' => 'Samco Flexi Cap Fund - Direct Growth',
                      'slug' => 'samco-flexi-cap-fund-direct-growth',
                      'short_objective' => ' Flexi cap fund Investing in efficient stress tested Indian & global companies at efficient prices.',
                      'objective' => 'Samco Flexi Cap Fund will invest in 25 stress tested efficient companies from India & across the globe at an efficient price, maintaining an efficient portfolio turnover & cost to generate superior risk-adjusted return for investors over long term.',
                      'scheme_type_text' => 'An open-ended dynamic equity scheme investing across large cap, mid cap, small cap stocks',
                      'scheme_plan_text' => 'Direct Plan - Growth',
                      'risk_meter' => 'Very High',
                      'fund_mgr1_id' => 1,
                      'fund_mgr2_id' => 2)
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
        Schema::dropIfExists('scheme_master_details');
    }
}
