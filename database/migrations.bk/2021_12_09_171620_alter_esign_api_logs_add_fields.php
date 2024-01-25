<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEsignApiLogsAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding field(s) like investor_account_id, created_by and source
        Schema::table('esign_api_logs', function (Blueprint $table) {
            $table->dropColumn('arn');
            $table->unsignedBigInteger('investor_account_id')->nullable()->comment('Foreign key references investor_id field from investor_account table')->after('id');
            $table->string('source', 100)->nullable()->comment('table name eg: users,invertor_account')->after('status');
            $table->unsignedBigInteger('created_by')->nullable()->comment('user id')->after('source');
            $table->index('investor_account_id');
            $table->index('created_at');
            $table->index(['source', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // removing earlier added field(s) like investor_account_id, created_by and source
        Schema::table('esign_api_logs', function (Blueprint $table) {
            $table->string('arn', 100)->nullable()->comment('ARN number')->after('id');
            $table->dropColumn(['investor_account_id', 'created_by', 'source']);
        });
    }
}
