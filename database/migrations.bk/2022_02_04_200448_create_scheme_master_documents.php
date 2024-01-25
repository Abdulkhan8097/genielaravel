<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemeMasterDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheme_master_documents', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('RTA_Scheme_Code', 50)->nullable()->comment('RTA scheme code');
            $table->string('file_label')->comment('File label which will be used for display');
            $table->string('uploaded_filename')->nullable()->comment('Uploaded filename');
            $table->string('uploaded_original_filename')->nullable()->comment('Uploaded original filename');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('RTA_Scheme_Code');
            $table->index('status');
            $table->foreign('RTA_Scheme_Code')->references('RTA_Scheme_Code')->on('scheme_master');
        });

        DB::table('scheme_master_documents')->insert(
            array(
                array('RTA_Scheme_Code' => 'FCRG',
                      'file_label' => 'Samco Flexi Cap Fund Product Leaf Let'),
                array('RTA_Scheme_Code' => 'FCRG',
                      'file_label' => 'Samco Flexi Cap Fund KIM'),
                array('RTA_Scheme_Code' => 'FCRG',
                      'file_label' => 'Samco Flexi Cap Fund SID'),
                array('RTA_Scheme_Code' => 'FCDG',
                      'file_label' => 'Samco Flexi Cap Fund Product Leaf Let'),
                array('RTA_Scheme_Code' => 'FCDG',
                      'file_label' => 'Samco Flexi Cap Fund KIM'),
                array('RTA_Scheme_Code' => 'FCDG',
                      'file_label' => 'Samco Flexi Cap Fund SID'),
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
        Schema::dropIfExists('scheme_master_documents');
    }
}
