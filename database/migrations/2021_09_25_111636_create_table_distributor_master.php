<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDistributorMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_master', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('ARN', 100)->comment('ARN number');
            $table->string('arn_holders_name', 100)->nullable()->comment("AMFI: ARN holder's name");
            $table->string('arn_address', 255)->nullable()->comment('AMFI: Address retrieved');
            $table->string('arn_pincode', 50)->nullable()->comment('AMFI: Pincode');
            $table->string('arn_email', 255)->nullable()->comment('AMFI: Email id, can have multiple value separated by COMMA');
            $table->string('arn_city', 100)->nullable()->comment('AMFI: City');
            $table->string('arn_telephone_r', 255)->nullable()->comment('AMFI: Telephone residence, can have multiple value separated by COMMA');
            $table->string('arn_telephone_o', 255)->nullable()->comment('AMFI: Telephone office, can have multiple value separated by COMMA');
            $table->dateTime('arn_valid_from')->nullable()->comment('AMFI: ARN valid from date');
            $table->dateTime('arn_valid_till')->nullable()->comment('AMFI: ARN valid till date');
            $table->string('arn_kyd_compliant', 20)->nullable()->comment('AMFI: Compliant or not, possible values are Yes or No');
            $table->string('arn_euin')->nullable()->comment('AMFI: EUIN');
            $table->integer('distributor_category')->nullable()->comment('Distributor category id');
            $table->string('project_focus', 20)->nullable()->comment('Possible Values: Yes or No');
            $table->unsignedBigInteger('direct_relationship_user_id')->nullable()->comment('SAMCO contact person for this ARN record: references users table');
            $table->string('alternate_mobile_1', 20)->nullable()->comment('Alternate mobile number 1');
            $table->string('alternate_mobile_2', 20)->nullable()->comment('Alternate mobile number 2');
            $table->string('alternate_mobile_3', 20)->nullable()->comment('Alternate mobile number 3');
            $table->string('alternate_mobile_4', 20)->nullable()->comment('Alternate mobile number 4');
            $table->string('alternate_mobile_5', 20)->nullable()->comment('Alternate mobile number 5');
            $table->string('alternate_email_1', 20)->nullable()->comment('Alternate email id 1');
            $table->string('alternate_email_2', 20)->nullable()->comment('Alternate email id 2');
            $table->string('alternate_email_3', 20)->nullable()->comment('Alternate email id 3');
            $table->string('alternate_email_4', 20)->nullable()->comment('Alternate email id 4');
            $table->string('alternate_email_5', 20)->nullable()->comment('Alternate email id 5');
            $table->decimal('arn_avg_aum', 25, 4)->nullable()->comment('Average AUM for this ARN, this data will be available via uploaded files');
            $table->decimal('arn_total_commission', 25, 4)->nullable()->comment('Total commission earned by ARN, this data will be available via uploaded files');
            $table->decimal('arn_yield', 25, 4)->nullable()->comment('Total AUM earned by this ARN, this is calculated field');
            $table->string('arn_business_focus_type', 100)->nullable()->comment('Dealing majorly in which segment: Possible values are like EQUITY, DEBT, EQUITY & DEBT etc.');
            $table->tinyInteger('is_rankmf_partner')->default(0)->nullable()->comment('Is ARN a RankMF partner? 0 = No, 1 = Yes');
            $table->string('rankmf_partner_code', 50)->nullable()->comment('RankMF partner code, value will be present only when is_rankmf_partner = 1');
            $table->tinyInteger('is_partner_active_on_rankmf')->default(0)->nullable()->comment('Is partner active on RankMF means having some AUM available');
            $table->decimal('rankmf_partner_aum', 25, 4)->nullable()->comment('RankMF partner AUM');
            $table->enum('rankmf_partner_relationship_stage', ['prospect', 'not a prospect'])->nullable()->comment('RankMF relationship stage: Possible values are prospect/not a prospect');
            $table->tinyInteger('rankmf_stage_of_prospect')->nullable()->comment('RankMF partner form status');
            $table->tinyInteger('is_samcomf_partner')->default(0)->nullable()->comment('Is ARN a SamcoMF partner? 0 = No, 1 = Yes');
            $table->string('samcomf_partner_code', 50)->nullable()->comment('SamcoMF partner code, value will be present only when is_samcomf_partner = 1');
            $table->tinyInteger('is_partner_active_on_samcomf')->default(0)->nullable()->comment('Is partner active on SamcoMF means having some AUM available');
            $table->decimal('samcomf_partner_aum', 25, 4)->nullable()->comment('SamcoMF partner AUM');
            $table->enum('samcomf_relationship_stage', ['prospect', 'not a prospect'])->nullable()->comment('SamcoMF relationship stage: Possible values are prospect/not a prospect');
            $table->tinyInteger('samcomf_stage_of_prospect')->nullable()->comment('SamcoMF partner form status');
            $table->string('product_approval_person_name', 255)->nullable()->comment('Principal Decision Maker of ARN for Product Approval - Name');
            $table->string('product_approval_person_mobile', 50)->nullable()->comment('Principal Decision Maker for Product Approval - Mobile Number');
            $table->string('product_approval_person_email', 255)->nullable()->comment('Principal Decision Maker for Product Approval - Email ID');
            $table->string('sales_drive_person_name', 255)->nullable()->comment('Principal Decision Maker of ARN for Sales Drive - Name');
            $table->string('sales_drive_person_mobile', 50)->nullable()->comment('Principal Decision Maker for for Sales Drive - Mobile Number');
            $table->string('sales_drive_person_email', 255)->nullable()->comment('Principal Decision Maker for Sales Drive - Email ID');
            $table->integer('relationship_quality_with_product_approver')->nullable()->comment('How is the relationship with product approver person');
            $table->integer('relationship_quality_with_sales_person')->nullable()->comment('How is the relationship with sales driving person');
            $table->decimal('total_aum', 25, 4)->nullable()->comment('Total AUM with SAMCO MF and RankMF');
            $table->decimal('total_equity_and_hybrid_aum', 25, 4)->nullable()->comment('Total Equity & Hybrid AUM with SAMCO MF and RankMF');
            $table->decimal('percent_market_share_of_equity_and_hybrid_aum')->nullable()->comment('Market Share % of Equity & Hybrid AUM with SAMCO MF and RankMF');
            $table->tinyInteger('status')->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->unique('ARN');
            $table->index('status');
            $table->index('arn_valid_from');
            $table->index('arn_valid_till');
            $table->index('arn_kyd_compliant');
            $table->index('distributor_category');
            $table->index('direct_relationship_user_id');
            $table->foreign('direct_relationship_user_id')->references('id')->on('users')->onUpdate('set null')->onDelete('set null');
            $table->index('is_rankmf_partner');
            $table->index('is_samcomf_partner');
            // $table->index('relationship_quality_with_product_approver');
            // $table->index('relationship_quality_with_sales_person');
        });
        DB::statement("ALTER TABLE `distributor_master` ADD INDEX `idx_relationship_quality_with_product_approver` (`relationship_quality_with_product_approver`);");
        DB::statement("ALTER TABLE `distributor_master` ADD INDEX `idx_relationship_quality_with_sales_person` (`relationship_quality_with_sales_person`);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_master');
    }
}
