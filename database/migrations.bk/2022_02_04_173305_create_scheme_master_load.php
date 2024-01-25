<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemeMasterLoad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creating a scheme_master_load table
        Schema::create('scheme_master_load', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('RTA_Scheme_Code', 50)->nullable()->comment('RTA scheme code');
            $table->datetime('load_date')->nullable()->comment('Load date');
            $table->tinyInteger('load_typecode')->nullable()->comment('Load type code, possible values: 1 = Entry Load, 2 = Exit Load');
            $table->integer('load_srno')->nullable()->comment('Load entry serial number');
            $table->decimal('from_amount', 25, 4)->nullable()->comment('Load is applicable based on from amount');
            $table->decimal('upto_amount', 25, 4)->nullable()->comment('Load is applicable based on upto amount');
            $table->integer('min_period')->nullable()->comment('Load is applicable based on starting time period');
            $table->integer('max_period')->nullable()->comment('Load is applicable based on maximum time period');
            $table->decimal('entryload', 25, 4)->nullable()->comment('Entry load of scheme. 0 = Not applicable, other than 0 means that much load is applicable while purchasing');
            $table->decimal('exitload', 25, 4)->nullable()->comment('Exit load of scheme. 0 = Not applicable, other than 0 means that much load is applicable while redeeming');
            $table->text('remarks')->nullable()->comment('Remarks, even though multiple entries are present for entry/exit load. This will have the same value for each record');
            $table->string('period_condition', 10)->nullable()->comment('Period Condition, possible values like =,>,<,>=,<=,between etc.');
            $table->string('period_type', 10)->nullable()->comment('Period Type, possible values like DAY, YEAR etc.');
            $table->string('percentage_condition', 10)->nullable()->comment('Percentage condition, if entry/exit load is based on percentage of units getting purchased/redeemed');
            $table->decimal('percentage_from', 25, 4)->nullable()->comment('Percentage from');
            $table->decimal('percentage_to', 25, 4)->nullable()->comment('Percentage to');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('RTA_Scheme_Code');
            $table->index('load_date');
            $table->index('status');
            $table->foreign('RTA_Scheme_Code')->references('RTA_Scheme_Code')->on('scheme_master');
        });

        DB::table('scheme_master_load')->insert(
            array(
                array('RTA_Scheme_Code' => 'FCRG',
                      'load_typecode' => 1,
                      'load_srno' => 1,
                      'from_amount' => 0,
                      'upto_amount' => 0,
                      'min_period' => 0,
                      'max_period' => 0,
                      'entryload' => 0,
                      'exitload' => 0,
                      'remarks' => 'Not applicable',
                      'period_condition' => null,
                      'period_type' => null),
                array('RTA_Scheme_Code' => 'FCRG',
                      'load_typecode' => 2,
                      'load_srno' => 1,
                      'from_amount' => 0,
                      'upto_amount' => 0,
                      'min_period' => 365,
                      'max_period' => 0,
                      'entryload' => 0,
                      'exitload' => 2,
                      'remarks' => '<li>2.00% if the investment is redeemed or switched out on or before 365 days from the date of allotment of units</li><li>1.00% if the investment is redeemed or switched out after 365 days but on or before 730 days from date of allotment of units.</li><li>No Exit Load will be charged if investment is redeemed or switched out after 730 days from the date of allotment of units.</li>',
                      'period_condition' => '<=',
                      'period_type' => 'DAY'),
                array('RTA_Scheme_Code' => 'FCRG',
                      'load_typecode' => 2,
                      'load_srno' => 2,
                      'from_amount' => 0,
                      'upto_amount' => 0,
                      'min_period' => 366,
                      'max_period' => 730,
                      'entryload' => 0,
                      'exitload' => 1,
                      'remarks' => '<li>2.00% if the investment is redeemed or switched out on or before 365 days from the date of allotment of units</li><li>1.00% if the investment is redeemed or switched out after 365 days but on or before 730 days from date of allotment of units.</li><li>No Exit Load will be charged if investment is redeemed or switched out after 730 days from the date of allotment of units.</li>',
                      'period_condition' => 'between',
                      'period_type' => 'DAY'),
                array('RTA_Scheme_Code' => 'FCRG',
                      'load_typecode' => 2,
                      'load_srno' => 3,
                      'from_amount' => 0,
                      'upto_amount' => 0,
                      'min_period' => 730,
                      'max_period' => 0,
                      'entryload' => 0,
                      'exitload' => 0,
                      'remarks' => '<li>2.00% if the investment is redeemed or switched out on or before 365 days from the date of allotment of units</li><li>1.00% if the investment is redeemed or switched out after 365 days but on or before 730 days from date of allotment of units.</li><li>No Exit Load will be charged if investment is redeemed or switched out after 730 days from the date of allotment of units.</li>',
                      'period_condition' => '>',
                      'period_type' => 'DAY'),
                array('RTA_Scheme_Code' => 'FCDG',
                      'load_typecode' => 1,
                      'load_srno' => 1,
                      'from_amount' => 0,
                      'upto_amount' => 0,
                      'min_period' => 0,
                      'max_period' => 0,
                      'entryload' => 0,
                      'exitload' => 0,
                      'remarks' => 'Not applicable',
                      'period_condition' => null,
                      'period_type' => null),
                array('RTA_Scheme_Code' => 'FCDG',
                      'load_typecode' => 2,
                      'load_srno' => 1,
                      'from_amount' => 0,
                      'upto_amount' => 0,
                      'min_period' => 365,
                      'max_period' => 0,
                      'entryload' => 0,
                      'exitload' => 2,
                      'remarks' => '<li>2.00% if the investment is redeemed or switched out on or before 365 days from the date of allotment of units</li><li>1.00% if the investment is redeemed or switched out after 365 days but on or before 730 days from date of allotment of units.</li><li>No Exit Load will be charged if investment is redeemed or switched out after 730 days from the date of allotment of units.</li>',
                      'period_condition' => '<=',
                      'period_type' => 'DAY'),
                array('RTA_Scheme_Code' => 'FCDG',
                      'load_typecode' => 2,
                      'load_srno' => 2,
                      'from_amount' => 0,
                      'upto_amount' => 0,
                      'min_period' => 366,
                      'max_period' => 730,
                      'entryload' => 0,
                      'exitload' => 1,
                      'remarks' => '<li>2.00% if the investment is redeemed or switched out on or before 365 days from the date of allotment of units</li><li>1.00% if the investment is redeemed or switched out after 365 days but on or before 730 days from date of allotment of units.</li><li>No Exit Load will be charged if investment is redeemed or switched out after 730 days from the date of allotment of units.</li>',
                      'period_condition' => 'between',
                      'period_type' => 'DAY'),
                array('RTA_Scheme_Code' => 'FCDG',
                      'load_typecode' => 2,
                      'load_srno' => 3,
                      'from_amount' => 0,
                      'upto_amount' => 0,
                      'min_period' => 730,
                      'max_period' => 0,
                      'entryload' => 0,
                      'exitload' => 0,
                      'remarks' => '<li>2.00% if the investment is redeemed or switched out on or before 365 days from the date of allotment of units</li><li>1.00% if the investment is redeemed or switched out after 365 days but on or before 730 days from date of allotment of units.</li><li>No Exit Load will be charged if investment is redeemed or switched out after 730 days from the date of allotment of units.</li>',
                      'period_condition' => '>',
                      'period_type' => 'DAY'),
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
        Schema::dropIfExists('scheme_master_load');
    }
}
