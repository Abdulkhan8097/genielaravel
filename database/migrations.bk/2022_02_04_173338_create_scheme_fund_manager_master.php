<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemeFundManagerMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creating a scheme_master_fund_managers table
        Schema::create('scheme_master_fund_managers', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('initial', 10)->nullable()->comment('Initial - Mr. Mrs. Etc.');
            $table->string('fundmanager')->nullable()->comment('Name of Fund Manager');
            $table->string('qualification')->nullable()->comment('Qualification of Fund Manager');
            $table->string('experience')->nullable()->comment('Experience of Fund Manager');
            $table->text('basicdetails')->nullable()->comment('Past Experience of Fund Manager');
            $table->string('designation', 100)->nullable()->comment('Designation of Fund Manager');
            $table->string('profile_image')->nullable()->comment('Profile image of Fund Manager');
            $table->integer('age')->nullable()->comment('Age of Fund Manager');
            $table->date('reporteddate')->nullable()->comment('Date of appointment as Fund Manager');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('status');
        });

        DB::table('scheme_master_fund_managers')->insert(
            array(
                array('initial' => 'Ms.',
                      'fundmanager' => 'Nirali Bhansali',
                      'designation' => 'Fund Manager - Equity',
                      'profile_image' => 'nirali-bhansali.png'),
                array('initial' => 'Mr.',
                      'fundmanager' => 'Dhawal Dhanani',
                      'designation' => 'Dedicated Fund Manager for overseas investments',
                      'profile_image' => 'dhawal-dhanani.png')
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
        Schema::dropIfExists('scheme_master_fund_managers');
    }
}
