<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCobrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing_collateral_list', function (Blueprint $table) {
            $table->id();
            $table->string('scheme_id', 100)->nullable()->comment('pdf download');
            $table->string('collateral_doc',100)->nullable()->comment('collatate list');
            $table->string('file_name', 265)->nullable()->comment('partner arn');
            $table->text('json_parameter')->nullable()->comment('Business or Professional');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('scheme_id');
        });
         Schema::create('marketing_collateral_scheme', function (Blueprint $table) {
            $table->id();
            $table->string('scheme_id', 100)->nullable()->comment('schme id');
            $table->string('scheme_name', 100)->nullable()->comment('pdf download');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('scheme_name');

        });
         DB::statement("ALTER TABLE `marketing_collateral_list` ADD `doc_unique_id` VARCHAR(100) NOT NULL AFTER `scheme_id`;
            ");
         DB::statement("ALTER TABLE `marketing_collateral_list` ADD `img_dispaly` TINYINT NULL DEFAULT NULL AFTER `file_name`");
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cobrand');
    }
}
