<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class getAMCBearerAuthHeader
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

		$fetch_for = [
			'saveInterSwitchSchemes',
			'check_for_autoswitch',
		];

		if(!in_array($request->api_url,$fetch_for)){
			return $next($request);
		}

		//fetching token header
		$post = [
			'token_requested_for' => 'samcomf',
			'device_name' => 'RANKMF'
		];

		$request->amc_api_url = getSettingsTableValue('AMC_API_URL');

		$header = [
			'Accept: application/json',
			'Content-Type: application/json'
		];

		$access_token = json_decode(get_curl_call($request->amc_api_url.'/get-access-token', $post,$header,true),1);

		$header = [];

		// Bearer header token
		if(isset($access_token['data']['access_token'])){
			$header = [
				'Accept: application/json',
				'Content-Type: application/json',
				'Authorization: Bearer '.$access_token['data']['access_token'],
			];
		}

		$request->amc_header = $header;

        return $next($request);
    }
}

