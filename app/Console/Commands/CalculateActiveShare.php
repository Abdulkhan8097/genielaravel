<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalculateActiveShare extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activeshare:calculate {--rta_scheme_code= : RTA schemecode for whom active share needs to mapped} {--enable_query_log= : want to enable query log then send this parameter as 1} {--active_share_date= : date for which active share needs to be calculated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate active share for the list of schemes available in scheme master';

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
        $retrieved_data = \App\Models\ActiveShareModel::calculate_active_share(array(
                                                                'rta_scheme_code' => $this->option('rta_scheme_code'),
                                                                'enable_query_log' => $flag_enable_query_log,
                                                                'active_share_date' => $this->option('active_share_date')
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
