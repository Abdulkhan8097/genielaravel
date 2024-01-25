<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUserAccountBankDetailsAddColumnNameAndHashKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding new columns "name" & "hash_key"
        Schema::table('user_account_bank_details', function (Blueprint $table) {

            if(!Schema::hasColumn('user_account_bank_details', 'name')){
                $table->string('name', 100)->nullable()->comment('Name/Business Name/Proprietor Name')->after('arn');
            }

            if(!Schema::hasColumn('user_account_bank_details', 'hash_key')){
                $table->string('hash_key')->nullable()->comment('Hash Key:ACC_NO|IFSC_CODE|ACCOUNT_TYPE')->after('name');
            }
            
        });

        DB::statement("ALTER TABLE `user_account_bank_details` ADD INDEX `user_account_bank_details_hash_key_index` (`hash_key`(191));");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing already added columns "name" & "hash_key"
        Schema::table('user_account_bank_details', function (Blueprint $table) {
            if(Schema::hasColumn('user_account_bank_details', 'name')){
                $table->dropColumn('name');
            }
            if(Schema::hasColumn('user_account_bank_details', 'hash_key')){
                $table->dropColumn('hash_key');
            }
        });
    }
}
