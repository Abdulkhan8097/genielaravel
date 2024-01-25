<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlertInvestorAccountAddAddressFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding new columns related to permanent address
        Schema::table('investor_account', function (Blueprint $table) {
            $table->renameColumn('pincode', 'address_pincode');
            $table->string('address_type', 50)->nullable()->comment('Address Type')->after('address_state');
            $table->string('permanent_address_line_1')->nullable()->comment('Address Line 1')->after('address_proof_submitted');
            $table->string('permanent_address_line_2')->nullable()->comment('Address Line 2')->after('permanent_address_line_1');
            $table->string('permanent_address_line_3')->nullable()->comment('Address Line 3')->after('permanent_address_line_2');
            $table->string('permanent_address_pincode', 20)->nullable()->comment('Pincode')->after('permanent_address_line_3');
            $table->string('permanent_address_city', 100)->nullable()->comment('Address City')->after('permanent_address_pincode');
            $table->string('permanent_address_district', 100)->nullable()->comment('Address District')->after('permanent_address_city');
            $table->string('permanent_address_state', 100)->nullable()->comment('Address State')->after('permanent_address_district');
            $table->string('permanent_address_proof_submitted')->nullable()->comment('Type of address proof submitted: Aadhar Card/ Voter ID etc.')->after('permanent_address_state');
            $table->string('permanent_address_type', 50)->nullable()->comment('Address Type')->after('permanent_address_proof_submitted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added field like permanent address
        Schema::table('investor_account', function (Blueprint $table) {
            $table->renameColumn('address_pincode', 'pincode');
            $table->dropColumn(['address_type', 'permanent_address_line_1', 'permanent_address_line_2', 'permanent_address_line_3', 'permanent_address_pincode', 'permanent_address_city', 'permanent_address_district', 'permanent_address_state', 'permanent_address_proof_submitted', 'permanent_address_type']);
        });
    }
}
