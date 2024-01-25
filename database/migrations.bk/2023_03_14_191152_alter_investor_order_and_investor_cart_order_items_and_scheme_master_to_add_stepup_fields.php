<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorOrderAndInvestorCartOrderItemsAndSchemeMasterToAddStepupFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investor_order', function (Blueprint $table) {
            $table->enum('stepup_flag', ['Y', 'N'])->nullable()->comment('Step UP Flag: Y/N')->after('installment_amount');
            $table->string('stepup_frequency', 20)->nullable()->comment('Step UP Frequency: Half Yearly/Yearly etc.')->after('stepup_flag');
            $table->decimal('stepup_amount', 25, 4)->nullable()->comment('SIP amount needs to be increased by fixed amount as per step up frequency')->after('stepup_frequency');
            $table->integer('stepup_numbers')->nullable()->comment('Stop doing step up after reaching this many steps. This field work if stepup_maxtopup value is not available')->after('stepup_amount');
            $table->decimal('stepup_maxtopup', 25, 4)->nullable()->comment('Stop doing step up after reaching this amount. This field work if stepup_numbers value is not available')->after('stepup_numbers');
            $table->tinyInteger('stepup_percentage')->nullable()->comment('SIP amount needs to be increased by given percentage as per step up frequency')->after('stepup_maxtopup');   
        });

        Schema::table('investor_cart_order_items', function (Blueprint $table) {
            $table->enum('stepup_flag', ['Y', 'N'])->nullable()->comment('Step UP Flag: Y/N')->after('installment_amount');
            $table->string('stepup_frequency', 20)->nullable()->comment('Step UP Frequency: Half Yearly/Yearly etc.')->after('stepup_flag');
            $table->decimal('stepup_amount', 25, 4)->nullable()->comment('SIP amount needs to be increased by fixed amount as per step up frequency')->after('stepup_frequency');
            $table->integer('stepup_numbers')->nullable()->comment('Stop doing step up after reaching this many steps. This field work if stepup_maxtopup value is not available')->after('stepup_amount');
            $table->decimal('stepup_maxtopup', 25, 4)->nullable()->comment('Stop doing step up after reaching this amount. This field work if stepup_numbers value is not available')->after('stepup_numbers');
            $table->tinyInteger('stepup_percentage')->nullable()->comment('SIP amount needs to be increased by given percentage as per step up frequency')->after('stepup_maxtopup');   
            
        });

        Schema::table('scheme_master', function (Blueprint $table) {
            $table->string('STEPUP_SIP_FLAG', 5)->default('N')->nullable()->after('SIP_FLAG');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investor_order', function (Blueprint $table) {
            $table->dropColumn(['stepup_flag']);
            $table->dropColumn(['stepup_frequency']);
            $table->dropColumn(['stepup_amount']);
            $table->dropColumn(['stepup_numbers']);
            $table->dropColumn(['stepup_maxtopup']);
            $table->dropColumn(['stepup_percentage']);
        });

        Schema::table('investor_cart_order_items', function (Blueprint $table) {
            $table->dropColumn(['stepup_flag']);
            $table->dropColumn(['stepup_frequency']);
            $table->dropColumn(['stepup_amount']);
            $table->dropColumn(['stepup_numbers']);
            $table->dropColumn(['stepup_maxtopup']);
            $table->dropColumn(['stepup_percentage']);
        });

        Schema::table('scheme_master', function (Blueprint $table) {
            $table->dropColumn(['STEPUP_SIP_FLAG']);
        });
    }
}
