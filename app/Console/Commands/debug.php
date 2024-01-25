<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers;

class debug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
		$retrieved_data = json_decode(get_curl_call(env('RANKMF_URL').'/webservices/Mf_api/getAccessToken',
		array(
			'app_key' => 'rankmf-web-app',
			'app_secret' => 'JJ6fyuf67uyguy327FRTT85GH',
			'user' => 'BDM_17',
			'client_id' => 'BDM_17'
		)),1);

		echo http_build_query([
			'api' => 8.4,
			'user' => 'BDM_17',
			'access_token' => $retrieved_data['response']['access_token'],
			'search_term' => 'axis',
			'bdm_id' => 'BDM_17',
		]);

		// echo get_curl_call(env('PARTNER_RANKMF_STATIC_WEB_URL').'/admin/InterAMCSwitch/'.	'get_scheme_details' ,[
		// 		'api' => 8.4,
		// 		'user' => 'BDM_17',
		// 		'access_token' => $retrieved_data['response']['access_token'],
		// 		'search_term' => 'axis',
		// 		'bdm_id' => 'BDM_17',
		// ]);

        return 0;
    }
}
