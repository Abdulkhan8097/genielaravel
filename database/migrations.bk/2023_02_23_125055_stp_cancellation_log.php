<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StpCancellationLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stp_cancellation_log', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->unsignedBigInteger('investor_account_id')->nullable()->comment('Investor account id');
            $table->string('folio_number', 20)->nullable()->comment('Folio number');
            $table->string('ihno', 20)->nullable()->comment('In House Number');
            $table->string('entry_by', 100)->nullable()->comment('Entry by name');
            $table->string('reason', 100)->nullable()->comment('Reason');
            $table->string('cancellation_ihno', 100)->nullable()->comment('Cancellation In House Number');
            $table->text('response')->nullable()->comment('Response');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stp_cancellation_log');
    }
}
