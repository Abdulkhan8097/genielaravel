<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiUsersList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_users_list', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('name', 191)->comment('User name for whom access tokens will be generated');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('name');
            $table->index('status');
        });

        DB::table('api_users_list')->insert(array(array('name' => 'SAMCOMF_PARTNERS', 'status' => 1),
                                                  array('name' => 'SAMCOMF_ADMIN', 'status' => 1),
                                                  array('name' => 'SAMCOMF_DRM', 'status' => 1),
                                                  array('name' => 'SAMCOMF', 'status' => 1),
                                                  array('name' => 'SAMCOMF_MFADMIN', 'status' => 1)
                                                )
                                        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_users_list');
    }
}
