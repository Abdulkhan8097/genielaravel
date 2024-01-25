<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BrokarageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("INSERT INTO `brokarge_master_plan` (`id`, `scheme_code`, `sheme_Name`, `plan_type`, `first_year`, `second_year`, `b30`, `created_at`, `updated_at`) VALUES
(1, 'FCDG', 'SAMCO FLEXI CAP FUND - DIRECT GROWTH', 'Business', 1.40, '1.40', '1.50', '2021-12-15 11:40:18', '2021-12-15 18:49:20'),
(2, 'FCRG', 'SAMCO FLEXI CAP FUND - REGULAR GROWTH', 'Professional', 1.15, '1.15', '1.50', '2021-12-15 11:41:12', '2021-12-15 18:49:42')");
        DB::statement("ALTER TABLE `brokarge_master_plan` CHANGE `first_year` `first_year` VARCHAR(200) NULL DEFAULT NULL COMMENT '1st year commission'");
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
