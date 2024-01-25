<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordsIntoDrmDistributorCategoryMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // adding data into MySQL table: drm_distributor_category_master
        DB::table('drm_distributor_category_master')->insert(
            array(array('label' => 'National Distributor', 'status' => 1),
                  array('label' => 'Bank', 'status' => 1),
                  array('label' => 'Corporate Captive Distributor', 'status' => 1),
                  array('label' => 'Corporate Distributor', 'status' => 1),
                  array('label' => 'Individual MFD', 'status' => 1),
                  array('label' => 'Online Aggregator', 'status' => 1),
                  array('label' => 'Regional Distributor', 'status' => 1),
                  array('label' => 'Wealth Manager', 'status' => 1),
                  array('label' => 'Stock Broker', 'status' => 1),
                  array('label' => 'Corporate MFD', 'status' => 1),
                )
        );

        // adding data into MySQL table: drm_relationship_quality_master
        DB::table('drm_relationship_quality_master')->insert(
            array(array('label' => 'Contact', 'status' => 1),
                  array('label' => 'Prospect', 'status' => 1),
                  array('label' => 'Acquaintance', 'status' => 1),
                  array('label' => 'Customer', 'status' => 1),
                  array('label' => 'Good Customer', 'status' => 1),
                  array('label' => 'Friend', 'status' => 1),
                  array('label' => 'Loyal Friend', 'status' => 1)
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
        //
    }
}
