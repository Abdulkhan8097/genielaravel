<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\DownloadController;

class download_emosi_required_values extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emosi:download {--bond_symbol= : Government security bond symbol} {--index_symbol= : NSE index symbol} {--from_date= : date FROM which BEER value needs to be calculated} {--to_date= : date TO which BEER value needs to be calculated} {--calculate_for_all_dates= : want to calculate values for available dates then send this parameter as 1 in that case --from_date & --to_date value will be ignored} {--enable_query_log= : want to enable query log then send this parameter as 1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downloading Emosi Value details';

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
        $bond_symbol = $this->option('bond_symbol');
        $index_symbol = $this->option('index_symbol');
        $from_date = $this->option('from_date');
        $to_date = $this->option('to_date');
        $current_date = date('Y-m-d');
        $previous_date =  date( "Y-m-d", strtotime( $from_date . "-1 day"));

        $emosi_nse_controller_obj = new DownloadController;

        if($to_date == $current_date && $from_date == $current_date){
            $emosi_historical_data = $emosi_nse_controller_obj->get_equity_stock_indices($index_symbol);
            $this->line(print_r($emosi_historical_data, true));

            $emosi_10y_bond_details = $emosi_nse_controller_obj->Bond_Yield_History_data();
            $this->line(print_r($emosi_10y_bond_details, true));
        }
        else{
            $emosi_nse_index_pe_pb_divyield =  $emosi_nse_controller_obj->nse_historical_indices($index_symbol, $from_date, $to_date); //same date works but not current date (current date works after market closing time)

            $this->line(print_r($emosi_nse_index_pe_pb_divyield, true));
            $this->newLine();

            $emosi_10y_bond_details =  $emosi_nse_controller_obj->investiong_bonding_details($bond_symbol, $previous_date, $to_date); //same date not working. from date should be back date/previous day date

            $this->line(print_r($emosi_10y_bond_details, true));
            $this->newLine();

            $emosi_historical_data = $emosi_nse_controller_obj->nse_historical_index($index_symbol, $from_date, $to_date);
            $this->line(print_r($emosi_historical_data, true));
        }

    }
}
