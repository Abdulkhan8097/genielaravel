<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsectMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asect_master', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('asect_type', 100)->nullable()->comment('Asset name');
            $table->string('asset', 25)->nullable()->comment('Asset type');
            $table->integer('score')->default(0)->nullable()->comment('Score');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('status');
        });

        // inserting few records into MySQL table: asect_master
        DB::table('asect_master')->insert(
                array(
                    array('asect_type'=>'Domestic Equities','asset'=>'Equity','score'=>'0'),
                    array('asect_type'=>'Corporate Debt','asset'=>'Debt','score'=>'0'),
                    array('asect_type'=>'PSU & PFI Bonds','asset'=>'Debt','score'=>'0'),
                    array('asect_type'=>'Government Securities','asset'=>'Debt','score'=>'0'),
                    array('asect_type'=>'Cash & Cash Equivalents and Net Assets','asset'=>'Others','score'=>'50'),
                    array('asect_type'=>'Treasury Bills','asset'=>'Debt','score'=>'50'),
                    array('asect_type'=>'Mibor Linked Instruments','asset'=>'Debt','score'=>'0'),
                    array('asect_type'=>'Deposits','asset'=>'Others','score'=>'50'),
                    array('asect_type'=>'PTC & Securitized Debt','asset'=>'Debt','score'=>'0'),
                    array('asect_type'=>'Certificate of Deposit','asset'=>'Debt','score'=>'0'),
                    array('asect_type'=>'Bills Rediscounting','asset'=>'Others','score'=>'0'),
                    array('asect_type'=>'Floating Rate Instruments','asset'=>'Debt','score'=>'0'),
                    array('asect_type'=>'Derivatives-Futures','asset'=>'Others','score'=>'30'),
                    array('asect_type'=>'Warrants','asset'=>'Others','score'=>'30'),
                    array('asect_type'=>'Domestic Mutual Funds Units','asset'=>'Others','score'=>'50'),
                    array('asect_type'=>'Preference Shares','asset'=>'Others','score'=>'20'),
                    array('asect_type'=>'Commercial Paper','asset'=>'Debt','score'=>'0'),
                    array('asect_type'=>'Overseas Equities','asset'=>'Equity','score'=>'50'),
                    array('asect_type'=>'Gold','asset'=>'Others','score'=>'60'),
                    array('asect_type'=>'Rights','asset'=>'Others','score'=>'30'),
                    array('asect_type'=>'ADRs & GDRs','asset'=>'Others','score'=>'50'),
                    array('asect_type'=>'Overseas Mutual Fund Units','asset'=>'Others','score'=>'50'),
                    array('asect_type'=>'Others','asset'=>'Others','score'=>'30'),
                    array('asect_type'=>'Others Equities','asset'=>'Equity','score'=>'50'),
                    array('asect_type'=>'Partly Paid-up Equity','asset'=>'Equity','score'=>'30'),
                    array('asect_type'=>'Derivatives-Call Options','asset'=>'Others','score'=>'30'),
                    array('asect_type'=>'Derivatives-Put Options','asset'=>'Others','score'=>'30'),
                    array('asect_type'=>'Derivatives-Options (Others)','asset'=>'Others','score'=>'50'),
                    array('asect_type'=>'Cash Management Bill','asset'=>'Debt','score'=>'0'),
                    array('asect_type'=>'Non-Convertible Debentures','asset'=>'Debt','score'=>'0'),
                    array('asect_type'=>'Zero Coupon Bonds','asset'=>'Debt','score'=>'0'),
                    array('asect_type'=>'Deposits (Placed as Margin)','asset'=>'Others','score'=>'50'),
                    array('asect_type'=>'Application Money','asset'=>'Others','score'=>'50'),
                    array('asect_type'=>'Gold Deposit Scheme','asset'=>'Others','score'=>'60'),
                    array('asect_type'=>'REITs & InvITs','asset'=>'Others','score'=>'60'),
                    array('asect_type'=>'Silver','asset'=>'Others','score'=>'0'),
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
        Schema::dropIfExists('asect_master');
    }
}
