<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class daily_distributor_empanelment_and_activation_update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily_distributor_empanelment_and_activation_update:retrieve {--send_email= : decides whether to send an email or just display the result}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email regarding distributor empanelment and activation update';

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
        $flag_send_email = 0;
        if($this->option('send_email') !== null && !empty($this->option('send_email')) && ($this->option('send_email') == 1)){
            $flag_send_email = 1;
        }

        $retrieved_data = \App\Models\ReportModel::daily_empanelment_and_activation_data(array('send_email' => $flag_send_email));
        if(is_array($retrieved_data) && count($retrieved_data) > 0){
            $this->line('Data retrieved: '. print_r($retrieved_data, true));
        }
        $this->line('The command execution is successful!');
        unset($retrieved_data, $flag_send_email);
        return 0;
    }
}
