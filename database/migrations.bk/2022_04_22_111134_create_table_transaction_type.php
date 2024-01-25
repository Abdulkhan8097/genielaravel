<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTransactionType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_type', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('tm_trtype', 50)->nullable()->comment('Transaction Type');
            $table->string('tm_desc', 255)->nullable()->comment('Transaction Description');
            $table->string('type_of_transaction', 80)->nullable()->comment('Type "LUMPSUM" OR "SIP"');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('tm_trtype');
            $table->index('status');
            $table->index('type_of_transaction');
        });

        DB::table('transaction_type')->insert(array(
                                        array('tm_trtype' => 'ADD', 'tm_desc' => 'Additional Purchase', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'ADDD', 'tm_desc' => 'Additional Purchase Delete', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'ADDR', 'tm_desc' => 'Additional Purchase Rejection', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'ADDRD', 'tm_desc' => 'Additional Purchase Rejection Delete', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'CNI', 'tm_desc' => 'Consolidation In', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'CNID', 'tm_desc' => 'Consolidation In Deletion', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'CNIR', 'tm_desc' => 'Consolidation In Rejection', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'CNIRD', 'tm_desc' => 'Consolidation In Rejection Deletion', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'CNO', 'tm_desc' => 'Consolidation Out', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'CNOD', 'tm_desc' => 'Consolidation Out Deletion', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'CNORD', 'tm_desc' => 'Consolidation Out Rejection Deletion', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'DIR', 'tm_desc' => 'Dividend Reinvestment', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DIRD', 'tm_desc' => 'Dividend Reinvestment Deletion', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DIRR', 'tm_desc' => 'Dividend Reinvestment Rejection', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DIRRD', 'tm_desc' => 'Dividend Reinvestment Rejection Delete', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DIV', 'tm_desc' => 'Dividend Pay Out', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DIVD', 'tm_desc' => 'Dividend Pay Out Deletion', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DIVR', 'tm_desc' => 'Dividend Pay Out Reject', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DIVRD', 'tm_desc' => 'Dividend Pay Out Rejection Delete', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DSPI', 'tm_desc' => 'Dividend Sweep In', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DSPID', 'tm_desc' => 'Dividend Sweep In Deletion', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DSPIR', 'tm_desc' => 'Dividend Sweep In Rejection', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DSPIRD', 'tm_desc' => 'Dividend Sweep In Rejection Deletion', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DSPO', 'tm_desc' => 'Dividend Sweep Out', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DTPA', 'tm_desc' => 'Dividend Transfer In Additional', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DTPN', 'tm_desc' => 'Dividend Transfer In New', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'DTPO', 'tm_desc' => 'Dividend Transfer Out', 'type_of_transaction' => 'Dividend', 'status' => 1),
                                        array('tm_trtype' => 'FUL', 'tm_desc' => 'Redemption', 'type_of_transaction' => 'Redemption', 'status' => 1),
                                        array('tm_trtype' => 'FULD', 'tm_desc' => 'Full Redemption Deletion', 'type_of_transaction' => 'Redemption', 'status' => 1),
                                        array('tm_trtype' => 'FULR', 'tm_desc' => 'Full Redemption Rejection', 'type_of_transaction' => 'Redemption', 'status' => 1),
                                        array('tm_trtype' => 'FULRD', 'tm_desc' => 'Full Redemption Rejection Deletion', 'type_of_transaction' => 'Redemption', 'status' => 1),
                                        array('tm_trtype' => 'IPO', 'tm_desc' => 'Initial Purchase', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'IPOD', 'tm_desc' => 'Initial Purchase Deletion', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'IPOR', 'tm_desc' => 'Initial Purchase Rejection', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'IPORD', 'tm_desc' => 'Initial Purchase Deletion', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'LTIA', 'tm_desc' => 'Lateral Shift In', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTIAD', 'tm_desc' => 'Lateral Shift In Additional Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTIAR', 'tm_desc' => 'Lateral Shift In Additional Rejection', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTIARD', 'tm_desc' => 'Lateral Shift In Additional Rejection Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTIN', 'tm_desc' => 'Lateral Shift In', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTIND', 'tm_desc' => 'Lateral Shift In New Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTINR', 'tm_desc' => 'Lateral Shift In New Rejection', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTINRD', 'tm_desc' => 'Lateral Shift In New Rejection Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTOF', 'tm_desc' => 'Lateral Shift Out', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTOFD', 'tm_desc' => 'Lateral Shift Out Full Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTOFR', 'tm_desc' => 'Lateral Shift Out Ful Rejection', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTOFRD', 'tm_desc' => 'Lateral Shift Out Ful Rejection Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTOP', 'tm_desc' => 'Lateral Shift Out', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTOPD', 'tm_desc' => 'Lateral Shift Out Partial Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTOPR', 'tm_desc' => 'Lateral Shift Out Rej.', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTOPRD', 'tm_desc' => 'Lateral Shift Out Rej. Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'LTOR', 'tm_desc' => 'Lateral Shift Out', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'NEW', 'tm_desc' => 'New Purchase', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'NEWD', 'tm_desc' => 'New Purchase Deletion', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'NEWR', 'tm_desc' => 'New Purchase Rejection', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'NEWRD', 'tm_desc' => 'New Purchase Rejection Deletion', 'type_of_transaction' => 'Lumpsum', 'status' => 1),
                                        array('tm_trtype' => 'RED', 'tm_desc' => 'Redemption', 'type_of_transaction' => 'Redemption', 'status' => 1),
                                        array('tm_trtype' => 'REDD', 'tm_desc' => 'Partial Redemption Deletion', 'type_of_transaction' => 'Redemption', 'status' => 1),
                                        array('tm_trtype' => 'REDR', 'tm_desc' => 'Partial Redemption Rejection', 'type_of_transaction' => 'Redemption', 'status' => 1),
                                        array('tm_trtype' => 'REDRD', 'tm_desc' => 'Partial Redemption Rejection Deletion', 'type_of_transaction' => 'Redemption', 'status' => 1),
                                        array('tm_trtype' => 'SIN', 'tm_desc' => 'Systematic Investment', 'type_of_transaction' => 'SIP', 'status' => 1),
                                        array('tm_trtype' => 'SIND', 'tm_desc' => 'Systematic Investment Deletion', 'type_of_transaction' => 'SIP', 'status' => 1),
                                        array('tm_trtype' => 'SINR', 'tm_desc' => 'Systematic Investment Rejection', 'type_of_transaction' => 'SIP', 'status' => 1),
                                        array('tm_trtype' => 'SINRD', 'tm_desc' => 'Systematic Investment Rejection Deletion', 'type_of_transaction' => 'SIP', 'status' => 1),
                                        array('tm_trtype' => 'STPA', 'tm_desc' => 'Systematic Transfer Plan In', 'type_of_transaction' => 'STP', 'status' => 1),
                                        array('tm_trtype' => 'STPAD', 'tm_desc' => 'S T P In Additional Deletion', 'type_of_transaction' => 'STP', 'status' => 1),
                                        array('tm_trtype' => 'STPAR', 'tm_desc' => 'S T P In Additional Rejection', 'type_of_transaction' => 'STP', 'status' => 1),
                                        array('tm_trtype' => 'STPARD', 'tm_desc' => 'S T P In Additional Rejection Deletion', 'type_of_transaction' => 'STP', 'status' => 1),
                                        array('tm_trtype' => 'STPI', 'tm_desc' => 'Systematic Transfer Plan In', 'type_of_transaction' => 'STP', 'status' => 1),
                                        array('tm_trtype' => 'STPIR', 'tm_desc' => 'Systematic Switch Plan In Rejection', 'type_of_transaction' => 'STP', 'status' => 1),
                                        array('tm_trtype' => 'STPN', 'tm_desc' => 'Systematic Transfer Plan In', 'type_of_transaction' => 'STP', 'status' => 1),
                                        array('tm_trtype' => 'STPNR', 'tm_desc' => 'S T P In Partial Rejection', 'type_of_transaction' => 'STP', 'status' => 1),
                                        array('tm_trtype' => 'STPO', 'tm_desc' => 'Systematic Transfer Plan Out', 'type_of_transaction' => 'STP', 'status' => 1),
                                        array('tm_trtype' => 'STPOD', 'tm_desc' => 'Systematic Switch Plan Out Deletion', 'type_of_transaction' => 'STP', 'status' => 1),
                                        array('tm_trtype' => 'STPOR', 'tm_desc' => 'Systematic Switch Plan Out Rejection', 'type_of_transaction' => 'STP', 'status' => 1),
                                        array('tm_trtype' => 'STPORD', 'tm_desc' => 'Systematic Switch Plan Out Rejection Deletion', 'type_of_transaction' => 'STP', 'status' => 1),
                                        array('tm_trtype' => 'SWD', 'tm_desc' => 'Systematic Withdrawal', 'type_of_transaction' => 'SWP', 'status' => 1),
                                        array('tm_trtype' => 'SWDD', 'tm_desc' => 'Systematic Withdrawal Deletion', 'type_of_transaction' => 'SWP', 'status' => 1),
                                        array('tm_trtype' => 'SWDR', 'tm_desc' => 'Systematic Withdrawal Rejection', 'type_of_transaction' => 'SWP', 'status' => 1),
                                        array('tm_trtype' => 'SWDRD', 'tm_desc' => 'Systematic Withdrawal Rejection Deletion', 'type_of_transaction' => 'SWP', 'status' => 1),
                                        array('tm_trtype' => 'SWIA', 'tm_desc' => 'Switch Over In', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWIAD', 'tm_desc' => 'Switch Over In Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWIAR', 'tm_desc' => 'Switch Over In Additional Rejection', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWIARD', 'tm_desc' => 'Switch Over In Additional Rejection Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWIN', 'tm_desc' => 'Switch Over In', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWIND', 'tm_desc' => 'Switch Over In New Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWINR', 'tm_desc' => 'Switch Over In New Rejection', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWINRD', 'tm_desc' => 'Switch Over In New Rejection Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWO', 'tm_desc' => 'Redemption', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWOF', 'tm_desc' => 'Switch over Out', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWOFD', 'tm_desc' => 'Switch Over Out Full Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWOFR', 'tm_desc' => 'Switch Over Out Full Rejection', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWOFRD', 'tm_desc' => 'Switch Over Out Full Rejection Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWOP', 'tm_desc' => 'Switch over Out', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWOPD', 'tm_desc' => 'Swithch Over Out Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWOPR', 'tm_desc' => 'Switch Over Out Partial Rejection', 'type_of_transaction' => 'Switch', 'status' => 1),
                                        array('tm_trtype' => 'SWOPRD', 'tm_desc' => 'Switch Over Out Partial Rejection Deletion', 'type_of_transaction' => 'Switch', 'status' => 1),
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
        Schema::dropIfExists('transaction_type');
    }
}
