<?php

namespace App\Http\Controllers;

use App\Models\Charging;
use App\Models\Network;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Subscriber;
use App\Models\Unsubscriber;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CountController extends Controller
{
    //
    public function count(Request $request){
        
        $user = User::where('token', $request->header('token'))->first();
        $user_id = $user->id;
        $validator = Validator::make($request->all(), [
            'fro'  => "required",
            'to'  => "required",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'required_fields' => $validator->errors()->all(),
                'message' => 'Missing field(s)',
                'status' => '500'
            ]);
        }
        
        $from = new Carbon($request->fro);
        $to = new Carbon($request->to);

        $sub = Subscriber::whereBetween('created_at', [$from, $to])->where('user_id', $user_id);

        $charg = Charging::whereBetween('created_at', [$from, $to])->where('status', 2)->where('user_id', $user_id);

        $unsub = Unsubscriber::whereBetween('created_at', [$from, $to])->where('user_id', $user_id);

        $charg = Charging::whereBetween('created_at', [$from, $to])->where('status', 0)->orWhere('status', 1)->where('user_id', $user_id);

        if(!$charg->exists()){
            $chargingUnsyncCount = Null;
        }else{
            $chargingUnsyncCount = $charg->get()->count();
        }

        if(!$charg->exists()){
            $chargingSyncCount = Null;
        }else{
            $chargingSyncCount = $charg->get()->count();
        }

        if(!$sub->exists()){
            $subscribersCount = Null;
        }else{
            $subscribersCount = $sub->get()->count();
        }

        if(!$unsub->exists()){
            $unsubscribersCount = Null;
        }else{
            $unsubscribersCount = $unsub->get()->count();
        }

        return response()->json([
            'message' => 'Successful',
            'status' => '200',
            'data' => [
                'subscribers' => $subscribersCount,
                'unsubscribers' => $unsubscribersCount,
                'chargingSync' => $chargingSyncCount,
                'chargingUnsync' => $chargingUnsyncCount
            ]
        ]);
    }

    public function countServices(Request $request){
        $validator = Validator::make($request->all(), [
            'fro'  => "required",
            'to'  => "required",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'required_fields' => $validator->errors()->all(),
                'message' => 'Missing field(s)',
                'status' => '500'
            ]);
        }

        $user = User::where('token', $request->header('token'))->first();
        $user_id = $user->id;

        if(isset($request->network)){
            $net = Network::where('network',  'LIKE', '%'.$request->network.'%');
            if($net->exists()){
                $network = $net->first();

                $from = new Carbon($request->fro);
                $to = new Carbon($request->to);
                $serv = Service::where('network_id', $network->id)->whereBetween('created_at', [$from, $to])->where('user_id', $user_id);
                if($serv->exists()){
                    $servicesCount = $serv->count();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $servicesCount
                    ]);
                }else{
                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => Null
                    ]);
                }
            }else{
                return response()->json([
                    'message' => 'Network Does not exist',
                    'status' => '300',
                    'data' => Null
                ]);
            }
        }else{

            $servicesCount = Service::where('user_id', $user_id)->get()->count();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $servicesCount
                ]);
        }
    }
}
