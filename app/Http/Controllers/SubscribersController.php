<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Subscriber;
use App\Models\Unsubscriber;
use App\Models\User;
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
        $user = User::where('token', $request->header('token'))->first();
        $user_id = $user->id;
        if(isset($request->service)){
            $serv = Service::where('serviceName','LIKE', '%'.$request->service.'%');
            if($serv->exists()){
                $service = $serv->first();

                Log::info($service->id);
                $subs = Subscriber::Join('services', 'services.id', 'subscribers.service_id')
                ->where('service_id', $service->id)->where('user_id', $user_id);

                // $subs = Subscriber::where('service_id', $service->id)->where('user_id', $user_id);
                if($subs->exists()){
                    $subscribers = $subs->select('subscribers.*', 'services.serviceName')->get();

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
                    'message' => 'Service not Found',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }elseif(isset($request->network)){
            Log::info('network');
            $net = DB::table('network_table')->where('network', 'LIKE', '%'.$request->network.'%');

            if($net->exists()){
                $network = $net->first();
                $sub = Subscriber::Join('services', 'services.id', 'subscribers.service_id')
                ->where('service_id', $network->id)->where('user_id', $user_id);
                // $sub = Subscriber::where('network_id', $network->id)->where('user_id', $user_id);
                if($sub->exists()){
                    $subscribers = $sub->select('subscribers.*', 'services.serviceName')->get();

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
                    'message' => 'Network does not exist',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }elseif(isset($request->subscriber_address)){
            $subs = Subscriber::Join('services', 'services.id', 'subscribers.service_id')
                ->where('senderAddress', 'LIKE', '%'.$request->subscriber_address.'%')->where('user_id', $user_id);
            // $subs = Subscriber::where('senderAddress', 'LIKE', '%'.$request->subscriber_address.'%')->where('user_id', $user_id);
            if($subs->exists()){
                $subscribers = $subs->select('subscribers.*', 'services.serviceName')->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $subscribers
                ]);
            }else{
                return response()->json([
                    'message' => 'Subscriber does not exist',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }elseif(isset($request->to) && isset($request->fro)){
            $from = new Carbon($request->fro);
            $to = new Carbon($request->to);
            $sub = Subscriber::Join('services', 'services.id', 'subscribers.service_id')
                ->whereBetween('created_at', [$from, $to])->where('user_id', $user_id);
            // $sub = Subscriber::whereBetween('created_at', [$from, $to])->where('user_id', $user_id);
            if($sub->exists()){
                $subscribers = $sub->select('subscribers.*', 'services.serviceName')->get();

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
                    'message' => 'Invalid Network',
                    'status' => '300',
                    'data' => []
                ]);
            }

            $serv = Service::where('serviceName', 'LIKE', '%'.$request->service.'%');
            if($serv->exists()){
                $service = $serv->first();
                $service_id = $service->id;

            }else{
                return response()->json([
                    'message' => 'Invalid Service',
                    'status' => '300',
                    'data' => []
                ]);
            }

            $sub = Subscriber::Join('services', 'services.id', 'subscribers.service_id')
                ->where('network_id', $network_id)->where('service_id', $service_id)->where('user_id', $user_id);
            // $sub = Subscriber::where('network_id', $network_id)->where('service_id', $service_id)->where('user_id', $user_id);
            if($sub->exists()){
                $subscribers = $sub->get();
                
                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $subscribers
                ]);
            }else{
                return response()->json([
                    'message' => 'No Subscribers',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }elseif(isset($request->channel)){
            $chan = DB::table('channel_table')->where('channel', 'LIKE', '%'.$request->channel.'%');
            if($chan->exists()){
                $channel = $chan->first();
                $channel_id = $channel->id;

                $serv = DB::table('service_channel_table')->where('channel_id', $channel_id);
                if($serv->exists()){
                    $service = $serv->first();
                    $service_id = $service->id;

                    $sub = Subscriber::Join('services', 'services.id', 'subscribers.service_id')
                    ->where('service_id', $service_id)->where('user_id', $user_id);
                    // $sub = Subscriber::where('service_id', $service_id)->where('user_id', $user_id);
                    if($sub->exists()){
                        $subscribers = $sub->select('subscribers.*', 'services.serviceName')->get();

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
                        'message' => 'Service Not found',
                        'status' => '300',
                        'data' => []
                    ]);
                }
            }
        }else{

            return response()->json([
                'message' => 'Choose a parameter',
                'status' => '500',
                'data' => []
            ]);
        }
    }

    public function getUnsubscribers(Request $request){
        $user = User::where('token', $request->header('token'))->first();
        $user_id = $user->id;
        if(isset($request->service)){
            $serv = Service::where('serviceName', 'LIKE', '%'.$request->service.'%');
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
                    'message' => 'Service not Found',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }elseif(isset($request->network)){
            Log::info('network');
            $net = DB::table('network_table')->where('network', 'LIKE', '%'.$request->network.'%');

            if($net->exists()){
                $network = $net->first();

                $sub = Unsubscriber::where('network_id', $network->id)->where('user_id', $user_id);
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
                    'message' => 'Network does not exist',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }elseif(isset($request->subscriber_address)){
            $subs = Unsubscriber::where('callingParty', 'LIKE', '%'.$request->subscriber_address.'%')->where('user_id', $user_id);
            if($subs->exists()){
                $unsubscribers = $subs->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $unsubscribers
                ]);
            }else{
                return response()->json([
                    'message' => 'Subscriber does not exist',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }elseif(isset($request->to) && isset($request->fro)){
            $from = new Carbon($request->fro);
            $to = new Carbon($request->to);
            $sub = Unsubscriber::whereBetween('created_at', [$from, $to])->where('user_id', $user_id);
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

            $net = DB::table('network_table')->where('network',  'LIKE', '%'.$request->network.'%');

            if($net->exists()){
                $network = $net->first();
                $network_id = $network->id;

            }else{
                return response()->json([
                    'message' => 'Invalid Network',
                    'status' => '300',
                    'data' => []
                ]);

            }


            $serv = Service::where('serviceName',  'LIKE', '%'.$request->service.'%');
            if($serv->exists()){
                $service = $serv->first();
                $service_id = $service->id; 
                $service_cp_id = $service->user_id;
            }else{
                return response()->json([
                    'message' => 'Invalid Service',
                    'status' => '300',
                    'data' => []
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
                    'message' => 'No Subscribers',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }elseif(isset($request->channel)){
            $chan = DB::table('channel_table')->where('channel', 'LIKE', '%'.$request->channel.'%');
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
                        'message' => 'Service Not found',
                        'status' => '300',
                        'data' => []
                    ]);
                }
            }
        }else{
            return response()->json([
                'message' => 'Choose a parameter',
                'status' => '500',
                'data' => []
            ]);
        }
    }

    
}
