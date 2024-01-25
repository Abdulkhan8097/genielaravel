<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MasterSipStpTransactionDetailsController;

class SendEmailsInflowOutflowOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inoutflow:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email Inflow Out Flow';

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
        $controller_obj = new MasterSipStpTransactionDetailsController;

        $save_emosi_value_details_to_kfin =  $controller_obj->inflow_outflow_order(); 
        $this->line('The command execution is successful!');
    }
}
