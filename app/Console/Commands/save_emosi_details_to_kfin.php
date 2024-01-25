<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\DownloadController;

class save_emosi_details_to_kfin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emosi:save {--index_symbol= : NSE index symbol} {--from_date= : date FROM which EMOSI value needs to be calculated} {--to_date= : date TO which EMOSI value needs to be calculated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching EMOSI values using from and to date and Inserting into KFIN API';

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
        $index_symbol = $this->option('index_symbol');
        $from_date = $this->option('from_date');
        $to_date = $this->option('to_date');

        $emosi_nse_controller_obj = new DownloadController;

        $save_emosi_value_details_to_kfin =  $emosi_nse_controller_obj->save_emosi_value_details_to_kfin($index_symbol, $from_date, $to_date); 
        $this->line('The command execution is successful!');
    }
}
