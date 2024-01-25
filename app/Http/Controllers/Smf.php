<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class Smf extends Controller
{
    public function index($short_code){
        // x($short_code);
        $result=DB::table('url_shorten')
        ->where([['short_code', $short_code]])
       ->get();
       if(!$result->isEmpty() && count($result) > 0 && strpos(url()->current(), $result[0]->short_url_domain) !== FALSE){
            $redirect_url = $result[0]->url;
            $hits=$result[0]->hits+1;
            // x($redirect_url);
            $final_uplaod['hits'] = $hits;
            $result_update=DB::table('url_shorten')
            ->where('short_code', $short_code)
            ->update($final_uplaod);
            return redirect($redirect_url);
        }
        else{
            return abort(404);
        }
    }
}
