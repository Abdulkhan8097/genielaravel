<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrmUploadedAmfiCityZoneMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drm_uploaded_amfi_city_zone_mapping', function (Blueprint $table) {
            $table->id()->comment('Serial ID');
            $table->string('amfi_city', 100)->nullable()->comment('AMFI: City');
            $table->string('mapped_zone', 50)->nullable()->comment('Zone mapped for given city');
            $table->tinyInteger('status')->default(1)->nullable()->comment('Status: 0=Inactive, 1=Active');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('Created Date');
            $table->dateTime('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Modified Date');
            $table->index('status');
            $table->index('amfi_city');
        });

        DB::statement("CREATE TABLE drm_uploaded_amfi_city_zone_mapping_backup LIKE drm_uploaded_amfi_city_zone_mapping;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drm_uploaded_amfi_city_zone_mapping');
        Schema::dropIfExists('drm_uploaded_amfi_city_zone_mapping_backup');
    }
}
