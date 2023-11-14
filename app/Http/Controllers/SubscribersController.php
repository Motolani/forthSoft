<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Subscriber;
use App\Models\Unsubscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SubscribersController extends Controller
{
    //
    public function getSubscribers(Request $request){
        if(isset($request->service)){
            $serv = Service::where('serviceName', $request->service);
            if($serv->exists()){
                $service = $serv->first();

                $subs = Subscriber::where('service_id', $service->id);
                if($subs->exists()){
                    $subscribers = $subs->get();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $subscribers
                    ]);

                }else{
                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => []
                    ]);
                }
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Service not Found'
                ]);
            }
        }elseif(isset($request->network)){
            Log::info('network');
            $net = DB::table('network_table')->where('network', $request->network);

            if($net->exists()){
                $network = $net->first();

                $sub = Subscriber::where('network_id', $request->network);
                if($sub->exists()){
                    $subscribers = $sub->get();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $subscribers
                    ]);
                }else{
                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => []
                    ]);
                }
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Network does not exist'
                ]);
            }
        }elseif(isset($request->subscriber_id)){
            $subs = Subscriber::where('subscriber_id', $request->subscriber_id);
            if($subs->exists()){
                $subscribers = $subs->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $subscribers
                ]);
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Subscriber does not exist'
                ]);
            }
        }elseif(isset($request->to) && isset($request->fro)){
            $from = new Carbon($request->fro);
            $to = new Carbon($request->to);
            $sub = Subscriber::whereBetween('created_at', [$from, $to]);
            if($sub->exists()){
                $subscribers = $sub->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $subscribers
                ]);
            }else{
                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => []
                ]);
            }
        }elseif(isset($request->service) && isset($request->network)){
            Log::info('Network & Service');
            $net = DB::where('network', $request->network);

            if($net->exists()){
                $network = $net->first();
                $network_id = $network->id;
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Invalid Network'
                ]);
            }

            $serv = Service::where('serviceName', $request->service);
            if($serv->exists()){
                $service = $serv->first();
                $service_id = $service->id;

            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Invalid Service'
                ]);
            }

            $sub = Subscriber::where('network_id', $network_id)->where('service_id', $service_id);
            if($sub->exists()){
                $subscribers = $sub->get();
                
                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $subscribers
                ]);
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'No Subscribers'
                ]);
            }
        }elseif(isset($request->channel)){
            $chan = DB::table('channel_table')->where('channel', $request->channel);
            if($chan->exists()){
                $channel = $chan->first();
                $channel_id = $channel->id;

                $serv = DB::table('service_channel_table')->where('channel_id', $channel_id);
                if($serv->exists()){
                    $service = $serv->first();
                    $service_id = $service->id;

                    $sub = Subscriber::where('service_id', $service_id);
                    if($sub->exists()){
                        $subscribers = $sub->get();

                        return response()->json([
                            'message' => 'Successful',
                            'status' => '200',
                            'data' => $subscribers
                        ]);
                    }else{
                        return response()->json([
                            'message' => 'Successful',
                            'status' => '200',
                            'data' => []
                        ]);
                    }
                }else{
                    return response()->json([
                        'message' => 'Failed',
                        'status' => '300',
                        'data' => 'Service Not found'
                    ]);
                }
            }
        }else{

            return response()->json([
                'message' => 'Choose a parameter',
                'status' => '500',
                'data' => ''
            ]);
        }
    }

    public function getUnsubscribers(Request $request){
        if(isset($request->service)){
            $serv = Service::where('serviceName', $request->service);
            if($serv->exists()){
                $service = $serv->first();
                $service_id = $service->id; 
                $service_cp_id = $service->user_id;


                $subs = Unsubscriber::where('user_id', $service_cp_id);

                if($subs->exists()){

                    $unsubscribers = $subs->get();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $unsubscribers
                    ]);

                }else{
                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => []
                    ]);
                }
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Service not Found'
                ]);
            }
        }elseif(isset($request->network)){
            Log::info('network');
            $net = DB::table('network_table')->where('network', $request->network);

            if($net->exists()){
                $network = $net->first();

                $sub = Unsubscriber::where('network_id', $request->network);
                if($sub->exists()){
                    $unsubscribers = $sub->get();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $unsubscribers
                    ]);
                }else{
                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => []
                    ]);
                }
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Network does not exist'
                ]);
            }
        }elseif(isset($request->subscriber_id)){
            $subs = Unsubscriber::where('callingParty', $request->subscriber_id);
            if($subs->exists()){
                $unsubscribers = $subs->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $unsubscribers
                ]);
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Subscriber does not exist'
                ]);
            }
        }elseif(isset($request->to) && isset($request->fro)){
            $from = new Carbon($request->fro);
            $to = new Carbon($request->to);
            $sub = Unsubscriber::whereBetween('created_at', [$from, $to]);
            if($sub->exists()){
                $subscribers = $sub->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $subscribers
                ]);
            }else{
                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => []
                ]);
            }
        }elseif(isset($request->service) && isset($request->network)){
            Log::info('Network & Service');

            $net = DB::table('network_table')->where('network', $request->network);

            if($net->exists()){
                $network = $net->first();
                $network_id = $network->id;

                // $sub = Unsubscriber::where('network_id', $network_id);

            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Invalid Network'
                ]);

            }


            $serv = Service::where('serviceName', $request->service);
            if($serv->exists()){
                $service = $serv->first();
                $service_id = $service->id; 
                $service_cp_id = $service->user_id;
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Invalid Service'
                ]);
            }

            $sub = Unsubscriber::where('network_id', $network_id)->where('user_id', $service_cp_id);
            if($sub->exists()){
                $unsubscribers = $sub->get();
                
                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $unsubscribers
                ]);
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'No Subscribers'
                ]);
            }
        }elseif(isset($request->channel)){
            $chan = DB::table('channel_table')->where('channel', $request->channel);
            if($chan->exists()){
                $channel = $chan->first();
                $channel_id = $channel->id;

                $serv = DB::table('service_channel_table')->where('channel_id', $channel_id);
                if($serv->exists()){
                    $service = $serv->first();
                    $service_id = $service->id;

                    $ser = Service::where('id', $service_id);
                    if($ser->exists()){
                        $serve = $ser->first();
                        $cp_id = $serve->user_id;

                        $tt = Unsubscriber::where('user_id', $cp_id);
                        if($tt->exists()){
                            $unsubscribers = $tt->get();
                        }else{
                            return response()->json([
                                'message' => 'Successful',
                                'status' => '200',
                                'data' => []
                            ]);
                        }
                        return response()->json([
                            'message' => 'Successful',
                            'status' => '200',
                            'data' => $unsubscribers
                        ]);
                    }else{
                        return response()->json([
                            'message' => 'Successful',
                            'status' => '200',
                            'data' => []
                        ]);
                    }
                }else{
                    return response()->json([
                        'message' => 'Failed',
                        'status' => '300',
                        'data' => 'Service Not found'
                    ]);
                }
            }
        }else{
            return response()->json([
                'message' => 'Choose a parameter',
                'status' => '500',
                'data' => ''
            ]);
        }
    }

    public function subscribersCount(Request $request){
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
        $sub = Subscriber::whereBetween('created_at', [$from, $to]);
            if($sub->exists()){
                $subscribersCount = $sub->get()->count();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $subscribersCount
                ]);
            }else{
                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => ''
                ]);
            }
    }

    public function unsubscribersCount(Request $request){
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
        $sub = Unsubscriber::whereBetween('created_at', [$from, $to]);
        if($sub->exists()){
            $subscribersCount = $sub->get()->count();

            return response()->json([
                'message' => 'Successful',
                'status' => '200',
                'data' => $subscribersCount
            ]);
        }else{
            return response()->json([
                'message' => 'Successful',
                'status' => '200',
                'data' => ''
            ]);
        }
    }
}
