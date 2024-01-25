<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUploadedArnAverageAumTotalCommissionData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploaded_arn_average_aum_total_commission_data', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('ARN', 100)->comment('ARN number');
            $table->decimal('arn_avg_aum', 25, 4)->nullable()->comment('Average AUM for this ARN, this data will be available via uploaded files');
            $table->decimal('arn_total_commission', 25, 4)->nullable()->comment('Total commission earned by ARN, this data will be available via uploaded files');
            $table->decimal('arn_yield', 25, 4)->nullable()->comment('Total AUM earned by this ARN, this is calculated field');
            $table->string('arn_business_focus_type', 100)->nullable()->comment('Dealing majorly in which segment: Possible values are like EQUITY, DEBT, EQUITY & DEBT etc.');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('status');
            $table->index('ARN');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploaded_arn_average_aum_total_commission_data');
    }
}
