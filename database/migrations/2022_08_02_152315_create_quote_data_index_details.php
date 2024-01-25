<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteDataIndexDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_data_index_details', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('symbol', 20)->default(0)->comment('Index symbol');
            $table->string('description', 255)->nullable()->comment('Index description');
            $table->string('category', 255)->nullable()->comment('Index category');
            $table->string('exchange', 255)->nullable()->comment('Exchange in which this index is listed');
            $table->string('display_name', 255)->nullable()->comment('Index name');
            $table->text('about_name')->nullable()->comment('About this index');
            $table->tinyInteger('source_target')->default(1)->comment('Index is source/target/both. 1=source, 2=target, 3= both');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->unique('symbol');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quote_data_index_details');
    }
}
