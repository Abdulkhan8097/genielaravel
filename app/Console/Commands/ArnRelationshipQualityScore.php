<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ArnRelationshipQualityScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users_arn_relationship_quality_score:calculate {--user_email= : Email id for whom score needs to be calculated} {--score_of_date= : Date for which score needs to be calculated, it should be in YYYY-MM-DD format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate user(s) relationship quality score available against an ARN at regular interval of time.';

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
        $retrieved_data = \App\Models\UsermasterModel::InsertUpdateQualityRelationshipARNScore(array(
                                                            'user_email' => $this->option('user_email'),
                                                            'score_of_date' => $this->option('score_of_date'),
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
