<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvestorAcctAddEuinDeclarationTermsCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	DB::statement("ALTER TABLE `investor_order` ADD `euin_declaration_terms` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'euin_declaration_terms accepted 1=YES' AFTER `ria_code`");
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
