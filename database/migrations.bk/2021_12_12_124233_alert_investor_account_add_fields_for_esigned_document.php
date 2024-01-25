<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlertInvestorAccountAddFieldsForEsignedDocument extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('investor_account_upload', function (Blueprint $table) {
            $table->string('esigned_document_filename')->nullable()->comment('Signed document filename')->after('uploaded_original_filename');
            $table->string('name_as_per_aadhar')->nullable()->comment('Name as per aadhar card retrieved from esigning vendor')->after('esigned_document_filename');
            $table->tinyInteger('esign_status')->nullable()->default(0)->comment('0=Pending, 1=Done')->after('name_as_per_aadhar');
            DB::statement("ALTER TABLE `investor_account_upload` CHANGE `upload_type` `upload_type` TINYINT(4) NULL DEFAULT '0' COMMENT '1 = PAN, 2 = Address Proof, 3 = PHOTO, 4 = Signature, 5 = IPV video/image, 6 = E-signing/signed document';");
            DB::statement("ALTER TABLE `investor_account_upload` CHANGE `upload_status` `upload_status` TINYINT(4) NULL DEFAULT '0' COMMENT 'Possible values: 0 = pending, 1 = accept, 2 = rejected';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('investor_account_upload', function (Blueprint $table) {
            $table->dropColumn(['esigned_document_filename', 'name_as_per_aadhar', 'esign_status']);
        });
    }
}
