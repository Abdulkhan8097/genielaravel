<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GeneralAlertMiddleware
{
	var $general_alerts_group = [];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

		if(!$request->isMethod('get') || is_null(Auth::user())){
			return $next($request);
		}

		// Adding Arn Alert
		$this->ArnAlert($request, $next);
		/**
		 * create alert type function for general section and add alert messages in $this->general_alerts_group array
		 * 
		 * $this->general_alerts_group[$group_title][] = [$alrt_title,$alert_desc];
		 * 
		 */

		view()->share('general_alerts_group', $this->general_alerts_group);

        return $next($request);
    }

	function ArnAlert(Request $request, Closure $next){

		$alert = &$this->general_alerts_group;

		$week_ago = date('Y-m-d H:i:s',strtotime ( "-1 week" ));
		$week_later = date('Y-m-d H:i:s',strtotime ( "+1 week" ));

		$results = DB::table('drm_distributor_master')
			->select(['ARN','arn_holders_name','arn_valid_from','arn_valid_till','created_at'])
			->where(function($where) use ($week_ago,$week_later){
				$where->where('direct_relationship_user_id','=',Auth::user()->id)
					->Where('arn_valid_till', '>', $week_ago)
					->Where('arn_valid_till', '<', $week_later);
			})
			->orwhere(function($where) use ($week_ago){
				$where->where('direct_relationship_user_id','=',Auth::user()->id)
					->Where('created_at', '>', $week_ago);
			})
			->orderby('updated_at','asc')
			->get()
			->toArray();

		foreach($results as $result){
			// about to expire
			if(0 < (strtotime($result->arn_valid_till) - time()) && (strtotime($result->arn_valid_till) - time()) < 86400*7){
				$relative_time = RelativeTime(strtotime($result->arn_valid_till));
				$alert['ARN about to expire'][] = [
					'title' => "{$result->arn_holders_name} ( {$result->ARN} )",
					'desc' => "ARN {$result->ARN} expires on {$relative_time}.",
					'href' => url('distributor/'.$result->ARN),
				];
			}
			// expired
			if(0 < (time() - strtotime($result->arn_valid_till)) && (time() - strtotime($result->arn_valid_till)) < 86400*7){
				$relative_time = RelativeTime(strtotime($result->arn_valid_till));
				$alert['ARN expired'][] = [
					'title' => "{$result->arn_holders_name} ( {$result->ARN} )",
					'desc' => "ARN {$result->ARN} expired {$relative_time}.",
					'href' => url('distributor/'.$result->ARN),
				];
			}
			// new arn
			if(0 < (time() - strtotime($result->created_at)) && (time() - strtotime($result->created_at)) < 86400*7){
				$relative_time = RelativeTime(strtotime($result->created_at));
				$alert['Assiged new ARN'][] = [
					'title' => "{$result->arn_holders_name} ( {$result->ARN} )",
					'desc' => "New ARN {$result->ARN} assigned to you {$relative_time}.",
					'href' => url('distributor/'.$result->ARN),
				];
			}
		}

		$results = DB::table(DB::raw('drm_arn_transfer_log lg'))
			->select([
				DB::raw('GROUP_CONCAT(DISTINCT lg.ARN) AS ARN'),
				DB::raw('MAX(lg.updated_at) AS updated_at')
			])
			->join(DB::raw('drm_distributor_master dm'),DB::raw("FIND_IN_SET(dm.ARN, lg.ARN)"),'=',DB::raw('1'))
			->where('lg.to','=',Auth::user()->id)
			->where('dm.direct_relationship_user_id','=',Auth::user()->id)
			->where('lg.department','=','RankMF DRM')
			->Where('lg.created_at', '>', $week_ago)
			->orderby('lg.updated_at','desc')
			->groupby('lg.ARN')
			->get()
			->toArray();

		$arn_list = DB::table('drm_distributor_master')
				->select('ARN')
				->where('direct_relationship_user_id','=',Auth::user()->id)
				->get()
				->toArray();

		$arn_list = array_column(json_decode(json_encode($arn_list),1),'ARN');

		$arns = [];

		foreach($results as $result){

			$tmp = explode(',',$result->ARN);

			if(!is_array($tmp)){
				$tmp = [$tmp];
			}

			$tmp = array_diff($tmp,$arns);

			if(count($tmp) == 0){
				continue;
			}

			$tmp = array_intersect($tmp,$arn_list);

			$result->ARN = implode(',',$tmp);

			$arns = array_merge($arns,explode(',',$result->ARN));

			$result->ARN = str_replace(',',', ',$result->ARN);

			$relative_time = RelativeTime(strtotime($result->updated_at));

			$title = "Arn {$result->ARN} assigned to you";
			$desc = "New ARN {$result->ARN} assigned to you {$relative_time}.";
			$href = url('distributor/'.$result->ARN);

			if(preg_match("/,/i", $result->ARN)){
				$title = "Arn's are assigned to you";
				$desc = "ARN's $result->ARN are assigned to you {$relative_time}.";
				$href = 'javascript:void(0);';
			}

			$alert['Assiged new ARN'][] = [
				'title' => $title,
				'desc' => $desc,
				'href' => $href,
			];
		}

	}

}
