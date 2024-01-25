<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Providers\RouteServiceProvider;

class getAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
		if($request->method() == 'GET'){
			return $next($request);
		}
		
		$tmp = DB::connection('partner-rankmf')
			->table('mfp_partner_login_master')
			->select(['id','email','password'])
			->where('email','=',\Auth::user()->email)//->toSql2();
			->first();

		$request->is_bdm = 0;

		$retrieved_data = [];

		if(isset($tmp->id)){

			$request->is_bdm = 1;

			$responce = get_curl_call(env('RANKMF_URL').'/webservices/Mf_api/getAccessToken',
				array(
					'app_key' => 'rankmf-web-app',
					'app_secret' => 'JJ6fyuf67uyguy327FRTT85GH',
					'user' => 'BDM_'. $tmp->id,
					'client_id' => 'BDM_'. $tmp->id
				));

			$retrieved_data = json_decode($responce,1);

			if(isset($retrieved_data['response']['access_token'])){
				$request->token = $retrieved_data['response']['access_token'];
				$request->bdm_id = $tmp->id;
			}
		}

        return $next($request);
    }
}

