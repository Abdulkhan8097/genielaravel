<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePennyVendorMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penny_vendor_master', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('name')->nullable()->comment(' vendor name');
            $table->tinyInteger('status')->default(0)->nullable()->comment('0 => inactive , 1 => active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');

            $table->index('status');
            
        });
        Schema::table('pennydrop_api_logs', function (Blueprint $table) {
            $table->foreign('vendor_id')->references('id')->on('penny_vendor_master')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pennydrop_api_logs', function (Blueprint $table) {
            $table->dropForeign('pennydrop_api_logs_vendor_id_foreign');
        });
        Schema::dropIfExists('penny_vendor_master');
    }
}
