<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrmAumDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drm_aum_data', function (Blueprint $table) {
            $table->index('trans_date');
            $table->index('agentcode');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drm_aum_data', function (Blueprint $table) {
            $table->dropIndex('trans_date');
            $table->dropIndex('agentcode');
            $table->dropIndex('created_at');
            $table->dropIndex('updated_at');
        });
    }
}
