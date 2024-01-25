<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangePartnerCategoryLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_partner_category_log', function (Blueprint $table) {
            $table->id();
            $table->string('ARN')->comment('ARN');
            $table->string('changed_from')->comment('Changed category from');
            $table->string('changed_to')->comment('Changed category to');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('change_partner_category_log');
    }
}
