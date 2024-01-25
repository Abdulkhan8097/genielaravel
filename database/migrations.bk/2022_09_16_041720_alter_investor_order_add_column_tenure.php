<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorOrderAddColumnTenure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding column tenure in MySQL table: investor_order
        Schema::table('investor_order', function (Blueprint $table) {
            $table->string('tenure_period', 20)->nullable()->comment('Tenure period, used for STP order')->after('frequency_type');
            $table->string('frequency_type', 20)->nullable()->comment('SIP/STP/SWP order frequency')->change();
            $table->date('start_date')->nullable()->comment('Used for SIP/STP/SWP orders and redemption order')->change();
            $table->date('sip_end_date')->nullable()->comment('SIP/STP/SWP end date')->change();
            $table->integer('installments')->nullable()->comment('Number of SIP/STP/SWP installments')->change();
            $table->decimal('installment_amount', 25, 4)->nullable()->comment('SIP/STP/SWP installment amount')->change();
            $table->string('selected_day', 20)->nullable()->comment('It is used for storing stp order day')->after('tenure_period');
            $table->index('tenure_period');
        });

        // updating existing SIP orders tenure_period as DATE WISE because all SIP are having START DATE & END DATE compulsory
        DB::statement("UPDATE investor_order SET created_at = created_at, updated_at = updated_at, tenure_period = 'dateWise' WHERE order_type = 'sip' AND tenure_period IS NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added column tenure in MySQL table: investor_order
        Schema::table('investor_order', function (Blueprint $table) {
            $table->dropColumn('tenure_period');
            // $table->dropColumn('selected_day');
            $table->string('frequency_type', 10)->nullable()->comment('SIP order frequency')->change();
            $table->date('start_date')->nullable()->comment('Used for SIP orders and redemption order')->change();
            $table->date('sip_end_date')->nullable()->comment('SIP end date')->change();
            $table->integer('installments')->nullable()->comment('Number of SIP installments')->change();
            $table->decimal('installment_amount', 25, 4)->nullable()->comment('SIP installment amount')->change();
        });
    }
}
