<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMosMultiplierData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mos_multiplier_data', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('multiplier_type', 10)->comment('Multiplier type/label. E.G: 5x/10x');
            $table->decimal('margin_of_safey', 25, 4)->nullable()->comment('Margin of safety');
            $table->decimal('multiplier_value', 25, 4)->default(1)->comment('Multiplier value');
            $table->tinyInteger('status')->default(1)->comment('Status: 0=inactive, 1=active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('multiplier_type');
            $table->index('margin_of_safey');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mos_multiplier_data');
    }
}
