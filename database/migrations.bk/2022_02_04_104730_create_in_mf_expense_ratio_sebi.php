<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInMfExpenseRatioSebi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mf_expense_ratio_sebi', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('scheme',256)->nullable()->comment('Name of Scheme');
            $table->string('scheme_type',256)->nullable()->comment('scheme type Regular,Direct');
            $table->dateTime('expe_date')->nullable()->useCurrent()->comment('Expense Ratio Date');
            $table->string('base_ter',256)->nullable()->comment('Base TER (%)');
            $table->decimal('additional_expense_as_per_regulation_two',25,4)->nullable()->comment('Additional expense as per Regulation 52(6A)(b) (%)2');
            $table->decimal('additional_expense_as_per_regulation_three', 25,4)->nullable()->comment('Additional expense as per Regulation 52(6A)(c) (%)3');
            $table->decimal('gst',25,4)->nullable()->comment('GST (%)4');
            $table->decimal('total_ter',25,4)->nullable()->comment('Total TER (%)');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('scheme');
            $table->index('scheme_type');
            $table->index('created_at');
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mf_expense_ratio_sebi');
    }
}
