<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestoreOderLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investore_oder_log', function (Blueprint $table) {
            $table->id();
            $table->string('scheme_id', 100)->nullable()->comment('order scheme id');
            $table->string('investor_id',100)->nullable()->comment('partner investore id');
            $table->string('order_type', 265)->nullable()->comment('order type');
            $table->string('sip_date',265)->nullable()->comment('sip date');
            $table->text('payment_link')->nullable()->comment('payment link');
            $table->string('order_amt',265)->nullable()->comment('order_amt');
            $table->string('order_status')->nullable()->comment('order status');
            $table->string('folio_no')->nullable()->comment('exist folio no');
            $table->string('arn')->nullable()->comment('ARN');
            $table->dateTime('create_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('create Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');

            $table->index('scheme_id');
            $table->index('create_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('investore_oder_log');
    }
}
