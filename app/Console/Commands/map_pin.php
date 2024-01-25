<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class map_pin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:pin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command is to map pin with its nearest pin withing 2 km range.\nRun\nphp artisan map:pin";

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
        $pincodes = config('pincode');
		foreach($pincodes as $pin1 => $pincode1){
			echo "\033[32m Finding pincode near $pin1 \033[0m\n";
			foreach($pincodes as $pin2 => $pincode2){
				$dis = pinDistance($pincodes[$pin1],$pincodes[$pin2]);
				if(is_nan($dis)){

				}elseif($dis <= 3 && $dis != 0){
					echo "\033[31m Found pincode $pin2 near $pin1 \033[0m";
					echo "\033[32m Distance $dis \033[0m\n";
					$pins  = DB::table('drm_nearest_pinmap')->where('pincode','=',$pin1)->get()->toArray();
					if(count($pins) == 0){
						$pins = [];
						$pins[] = $pin2;
						$pins[] = $pin1;
						DB::table('drm_nearest_pinmap')
						->insert(['pincode' => $pin1,'mapped_pins' => implode(',',array_unique($pins))]);
					}else{
						$pins = explode(',',$pins[0]->mapped_pins);
						$pins[] = $pin2;
						$pins[] = $pin1;
						DB::table('drm_nearest_pinmap')
						->where('pincode','=',$pin1)
						->update(['mapped_pins' => implode(',',array_unique($pins))]);
					}
				}
			}
		}
		echo "Completed.\n";
    }
}
