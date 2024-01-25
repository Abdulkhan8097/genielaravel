<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDrmUsersArnRelationshipQualityScore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_users_arn_relationship_quality_score', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->unsignedBigInteger('user_id')->comment('User ID belongs to users table');
            $table->date('score_of_date')->comment('Helps to Identify for which date the score was calculated');
            $table->integer('no_of_assigned_arn')->default(0)->comment('How many ARN(s) were assigned to user on the score calculation date');
            $table->decimal('maximum_score',25,2)->default(0)->comment('maximum score for the calculation date,formula:no of ARN(s) * 100');
            $table->decimal('calculated_score',25,2)->default(0)->comment('Score calculated for a user based on relationship quality of ARN');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->unique(['user_id', 'score_of_date'], 'unique_user_id_score_of_date');
            $table->index('score_of_date', 'idx_score_of_date');
            $table->index('user_id', 'idx_user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drm_users_arn_relationship_quality_score');
    }
}
