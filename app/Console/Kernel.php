<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Calculate user(s) relationship quality score available against an ARN, daily around 11:00 PM
        $schedule->command('users_arn_relationship_quality_score:calculate')->withoutOverlapping()->runInBackground()->dailyAt('23:00');

        // Update MySQL table: drm_distributor_master with data coming from Partners RankMF MongoDB table: mf_arn_data
        $schedule->exec('sh vendor/shell_scripts/read_arn_data_from_partners_mongodb.sh')->withoutOverlapping()->runInBackground()->everyTenMinutes()->sendOutputTo('public/storage/logs/read_arn_data_from_partners_mongodb.txt');

        // Calculate active share for SAMCOMF SCHEMES, daily around 13:30 PM
        $schedule->command('activeshare:calculate')->withoutOverlapping()->runInBackground()->days([Schedule::TUESDAY, Schedule::WEDNESDAY, Schedule::THURSDAY, Schedule::FRIDAY, Schedule::SATURDAY])->at('13:30');

        //Calculating EMOSI data using current date and sending EMAIL to Research team at 20:30
        $schedule->exec('sh vendor/shell_scripts/download_emosi_data.sh "India 10Y" "NIFTY 50" "'.date('Y-m-d').'" "'.date('Y-m-d').'" 1')->withoutOverlapping()->runInBackground()->days([Schedule::MONDAY,Schedule::TUESDAY, Schedule::WEDNESDAY, Schedule::THURSDAY, Schedule::FRIDAY, Schedule::SATURDAY, Schedule::SUNDAY])->at('17:00');

        $schedule->exec('sh vendor/shell_scripts/download_emosi_data.sh "India 10Y" "NIFTY 50" "'.date('Y-m-d').'" "'.date('Y-m-d').'" 1')->withoutOverlapping()->runInBackground()->days([Schedule::MONDAY,Schedule::TUESDAY, Schedule::WEDNESDAY, Schedule::THURSDAY, Schedule::FRIDAY, Schedule::SATURDAY, Schedule::SUNDAY])->at('18:00');

        $schedule->exec('sh vendor/shell_scripts/download_emosi_data.sh "India 10Y" "NIFTY 50" "'.date('Y-m-d').'" "'.date('Y-m-d').'" 1')->withoutOverlapping()->runInBackground()->days([Schedule::MONDAY,Schedule::TUESDAY, Schedule::WEDNESDAY, Schedule::THURSDAY, Schedule::FRIDAY, Schedule::SATURDAY, Schedule::SUNDAY])->at('19:00');

        $schedule->exec('sh vendor/shell_scripts/download_emosi_data.sh "India 10Y" "NIFTY 50" "'.date('Y-m-d').'" "'.date('Y-m-d').'" 1')->withoutOverlapping()->runInBackground()->days([Schedule::MONDAY,Schedule::TUESDAY, Schedule::WEDNESDAY, Schedule::THURSDAY, Schedule::FRIDAY, Schedule::SATURDAY, Schedule::SUNDAY])->at('20:00');

        $schedule->exec('sh vendor/shell_scripts/download_emosi_data.sh "India 10Y" "NIFTY 50" "'.date('Y-m-d').'" "'.date('Y-m-d').'" 1')->withoutOverlapping()->runInBackground()->days([Schedule::MONDAY,Schedule::TUESDAY, Schedule::WEDNESDAY, Schedule::THURSDAY, Schedule::FRIDAY, Schedule::SATURDAY, Schedule::SUNDAY])->at('20:30');

        //Saving emosi value into KFIN API
        $schedule->command('emosi:save --index_symbol="nifty_50" --from_date="'.date('Y-m-d').'" --to_date="'.date('Y-m-d').'"')->withoutOverlapping()->runInBackground()->days([Schedule::MONDAY,Schedule::TUESDAY, Schedule::WEDNESDAY, Schedule::THURSDAY, Schedule::FRIDAY, Schedule::SATURDAY, Schedule::SUNDAY])->at('20:45');

        // Calculate user(s) relationship quality score available against an ARN, daily around 10:00 AM to Midnight 12 PM
        $schedule->command('inoutflow:email')->withoutOverlapping()->runInBackground()->hourly();
        $schedule->command('inoutflow:email')->withoutOverlapping()->runInBackground()->dailyAt('23:59');

        // send daily MIS emails regarding DIRECT INVESTOR ORDERS, daily around 11:30 AM
        $schedule->command('daily_direct_orders_update:retrieve --send_email=1')->withoutOverlapping()->runInBackground()->dailyAt('11:30');

        // send daily MIS emails regarding EMPANELMENT & ACTIVATION REPORT of PARTNERS, daily around 11:30 AM
        $schedule->command('daily_distributor_empanelment_and_activation_update:retrieve --send_email=1')->withoutOverlapping()->runInBackground()->dailyAt('11:30');
        $schedule->command('empanelment:email')->withoutOverlapping()->runInBackground()->dailyAt('10:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
