<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRankmfPartnerRegistration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rankmf_partner_registration', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('partner_code', 50)->nullable()->comment('mfp_partner_registration table >> partner_code field');
            $table->string('email', 100)->nullable()->comment('mfp_partner_registration table >> email field');
            $table->string('mobile', 100)->nullable()->comment('mfp_partner_registration table >> mobile field');
            $table->string('ARN', 100)->comment('mfp_partner_registration table >> ARN field');
            $table->string('arn_name', 100)->nullable()->comment('mfp_partner_registration table >> arn_name field');
            $table->string('status', 50)->nullable()->comment('mfp_partner_registration table >> status field label data');
            $table->string('form_status', 100)->nullable()->comment('mfp_partner_registration table >> form_status field label data');
            $table->integer('unit_counsellor')->nullable()->comment('mfp_partner_registration table >> unit_counsellor field');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('status');
            $table->index('unit_counsellor');
            $table->index('ARN');
        });

        DB::statement("ALTER TABLE `rankmf_partner_registration` ADD UNIQUE INDEX `idx_partner_code_email`(`partner_code`, `email`);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rankmf_partner_registration');
    }
}
