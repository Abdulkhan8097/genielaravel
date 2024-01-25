<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMfHolidays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('invdb')->statement("CREATE TABLE `mf_holidays` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `date` date NOT NULL COMMENT 'holiday date',
            `is_holiday` tinyint(1) NOT NULL COMMENT '0=>Not Holiday, 1=>holiday',
            `equity` tinyint(1) NOT NULL COMMENT '0=No, 1=Yes',
            `debt` tinyint(1) NOT NULL COMMENT '0=No, 1=Yes',
            `liquid` tinyint(1) NOT NULL COMMENT '0=No, 1=Yes',
            `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

        DB::connection('invdb')->statement("INSERT INTO `mf_holidays` (`date`, `is_holiday`, `equity`, `debt`, `liquid`, `date_created`) VALUES
                                                                        ('2022-01-26',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-03-01',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-03-18',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-04-14',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-04-10',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-04-15',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-05-01',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-05-03',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-07-10',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-08-09',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-08-15',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-08-31',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-10-02',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-10-05',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-10-24',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-10-26',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-11-08',	1,	1,	1,	1,	'2021-12-17 15:00:00'),
                                                                        ('2022-12-25',	1,	1,	1,	1,	'2021-12-17 15:00:00');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mf_holidays');
    }
}
