<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DB;
use auth;
use App\Models\UsermasterModel;
use Illuminate\Support\Facades\Route;

class EnsureUserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // retrieving logged in user id
        $logged_in_user_id = (auth()->user()->id??-1);
        // helps to decide whether page gonna be accessible to logged in user or not. Default value is FALSE means not accessible
        $flag_page_accessible = false;
        // get currently viewing page route
        $current_route_uri = Route::getCurrentRoute()->uri();
        // gets the query parameters for the currently viewing page route
        $current_route_parameters = Route::getCurrentRoute()->parameters();

        // retrieving the logged in user role id
        $retrieved_data = UsermasterModel::get_specific_user_role_and_permissions($logged_in_user_id, $current_route_uri);
        // checking whether a logged in user had a role assigned which is having flag "have_all_permissions = 1"
        if(isset($retrieved_data['role_details']) && isset($retrieved_data['role_details']['have_all_permissions']) && (intval($retrieved_data['role_details']['have_all_permissions']) == 1)){
            $flag_page_accessible = true;
        }
        elseif(isset($retrieved_data['role_permissions']) && is_array($retrieved_data['role_permissions']) && count($retrieved_data['role_permissions']) > 0){
            // checking whether current route have have been present insiede the ACTIVE permissions list available against a logged in user role
            if(in_array($current_route_uri, $retrieved_data['role_permissions']) !== FALSE){
                $flag_page_accessible = true;
            }
        }
        unset($logged_in_user_id, $retrieved_data, $current_route_uri, $current_route_parameters);

        if($flag_page_accessible){
            // page is accessible to logged in user
            return $next($request);
        }
        else{
            // page is not accessible to logged in user, so redirecting them to Dashboard with message
            if($request->ajax()){
                // sending response in JSON format, if page is requested via an AJAX
                return response()->json(array('err_flag' => 1, 'err_msg' => array('Not authorized to perform requested action')), 401);
            }
            else{
                return redirect('/')->with('error', 'Unable to access the requested page');
            }
        }
    }
}
