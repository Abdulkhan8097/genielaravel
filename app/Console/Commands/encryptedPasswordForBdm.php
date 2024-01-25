<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class encryptedPasswordForBdm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encryptedpasswordforbdm:update {--user_email= : Email id for whom encrypted password needs to retrieved}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve the encrypted password for BDM user(s) whose password is not yet updated in MySQL table: drm_partners_rankmf_bdm_list';

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
        $retrieved_data = \App\Models\UsermasterModel::update_encrypted_password_for_bdm_list(array(
                                                            'user_email' => $this->option('user_email'),
                                                            'flag_log_display_messages' => true,
                                                            'enable_query_log' => false
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
