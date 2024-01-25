<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class assignUserToArn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignusertoarn:map {--arn_number= : ARN number for whom user needs to mapped} {--from_date= : ARN created from and after the specified date, it should be in YYYY-MM-DD format} {--till_date= : ARN created till the specified date, it should be in YYYY-MM-DD format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign user(s)/bdm(s) to arn based on logic given in https://www.mindmeister.com/2055663759?t=Nza85amUKc';

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
        $retrieved_data = \App\Models\DistributorsModel::assign_users_to_arn(array(
                                                                'arn_number' => $this->option('arn_number'),
                                                                'from_date' => $this->option('from_date'),
                                                                'till_date' => $this->option('till_date'),
                                                                'flag_log_display_messages' => true
                                                                )
                                                            );
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
        return 0;
    }
}
