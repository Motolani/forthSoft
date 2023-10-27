<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthApiCheck
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
        // return $next($request);
        if($request->token){
            $token = $request->token;
        }else{
            $token = $request->header('token');
        }
        
        Log::info($token);
	    $ip = $request->ip();
	    $envip = env('SERVER_IP');
	    // $myhash = hash("sha512", $envip.$token);
		$recs = User::where('token', $token);

        if($recs->exists()){
            $record = $recs->first();
            $now = Carbon::now()->toDateTimeString();
            $exp_time = Carbon::now()->subMinutes(30)->toDateTimeString();
            $start_time = $record->token_time;
            if($exp_time <= $start_time){
                return $next($request);
            }else{
                return response()->json([
                    'message' => 'Expired Token... Please Log in',
                    'status' => '900'
                ]);
            }
        }else {
            return response()->json([
                'message' => 'Authentication Failed',
                'status' => '300'
            ]);
        } 
    }
}
