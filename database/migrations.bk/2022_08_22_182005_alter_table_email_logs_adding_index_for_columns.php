<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableEmailLogsAddingIndexForColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //adding index for column
        Schema::table('email_logs', function(Blueprint $table){
            $table->index('email');
            $table->index('status');
            $table->index('for');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop index
        Schema::table('email_logs', function(Blueprint $table){
            $table->dropIndex('email_logs_email_index');
            $table->dropIndex('email_logs_status_index');
            $table->dropIndex('email_logs_for_index');
        });
    }
}
