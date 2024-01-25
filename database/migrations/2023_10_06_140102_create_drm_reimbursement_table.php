<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmReimbursementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_reimbursement', function (Blueprint $table) {
            $table->id();
			$table->integer('user_id')->unsigned()->commenmt('User id');
			$table->integer('meeting_id')->unsigned()->commenmt('Meeting id');
			$table->string('type', 50)->comment('Reimbursement Type');
			$table->double('amount')->comment('Amount');
			$table->string('location', 255)->comment('Location');
			$table->date('date')->comment('Date of expense occurred');
			$table->string('file', 255)->comment('Uploaded file');
			$table->string('description', 1000)->comment('Description about meeting');
			$table->string('remark', 1000)->comment('Reporting managers remark')->default('');
			$table->integer('status')->unsigned()->comment('0 - not approved,1 - approved,2 - rejected')->default(0);
            $table->timestamps();
			
			$table->index('user_id');
			$table->index('meeting_id');
			$table->index('type');
			$table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drm_reimbursement');
    }
}
