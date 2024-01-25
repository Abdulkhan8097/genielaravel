<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class calculate_median_deviation_from_ma1750 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'median_deviation_from_ma1750:calculate {--index_symbol= : NSE index symbol} {--from_date= : date FROM which DEVIATION value needs to be calculated} {--to_date= : date TO which DEVIATION value needs to be calculated} {--calculate_for_all_dates= : want to calculate values for available dates then send this parameter as 1 in that case --from_date & --to_date value will be ignored} {--enable_query_log= : want to enable query log then send this parameter as 1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate MEDIAN DEVIATION FROM MOVING AVERAGE 1750 for the input NSE index symbol & date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $flag_enable_query_log = false;
        if($this->option('enable_query_log') !== null && !empty($this->option('enable_query_log')) && ($this->option('enable_query_log') == 1)){
            $flag_enable_query_log = 1;
        }
        $flag_calculate_for_all_dates = 0;
        if($this->option('calculate_for_all_dates') !== null && !empty($this->option('calculate_for_all_dates')) && ($this->option('calculate_for_all_dates') == 1)){
            $flag_calculate_for_all_dates = 1;
        }
        $retrieved_data = \App\Models\EmosiMovingAverage1750CalculationModel::calculate_median_deviation_from_ma1750(array(
                                                                'index_symbol' => $this->option('index_symbol'),
                                                                'from_date' => $this->option('from_date'),
                                                                'to_date' => $this->option('to_date'),
                                                                'calculate_for_all_dates' => $flag_calculate_for_all_dates,
                                                                'enable_query_log' => $flag_enable_query_log
                                                                )
                                                            );
        $this->line(print_r($retrieved_data, true));
        if(isset($retrieved_data['display_messages']) && is_array($retrieved_data['display_messages']) && count($retrieved_data['display_messages']) > 0){
            foreach($retrieved_data['display_messages'] as $log_message){
                $this->line($log_message);
                $this->newLine();
            }
            unset($log_message);
        }
        else{
            $this->line('The command execution is successful!');
        }
        unset($retrieved_data);
    }
}
