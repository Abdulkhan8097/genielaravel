<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmUserGoalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_user_goal', function (Blueprint $table) {
            $table->id();
			$table->integer('user_id')->default(0)->comment('Role whom target is asigned for');
			$table->integer('target_calls')->default(0)->comment('Target Calls for user');
			$table->integer('target_meetings')->default(0)->comment('Target Meetings for user');
			$table->integer('goal_level')->default(1)->comment('0 - is for global, 1 - is for perticular user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drm_user_goal');
    }
}
