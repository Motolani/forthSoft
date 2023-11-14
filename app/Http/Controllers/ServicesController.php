<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\PricePoint;
use App\Models\Service;
use App\Models\ServiceChannel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ServicesController extends Controller
{
    //services
    public function getServices(Request $request){
        if(isset($request->network)){
            $net = Network::where('network', $request->network);
            if($net->exists()){
                $network = $net->first();

                $serv = Service::where('network_id', $network->id);
                if($serv->exists()){
                    $services = $serv->get();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $services
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
                    'message' => 'Network Does not exist',
                    'status' => '300',
                ]);
            }
        }elseif(isset($request->keyword)){
            $serv = Service::where('keyword', $request->keyword);
            if($serv->exists()){
                $services = $serv->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $services
                ]);
            }else{
                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => []
                ]);
            }
        }elseif(isset($request->price_point)){

            
                $serv = PricePoint::join('services','services.id','price_point_table.id')
                ->where('price_point_table.price', $request->price_point);
                
            if($serv->exists()){

                $services = $serv->select('services.*')->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $services
                ]);
            }else{
                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => []
                ]);
            }
        }else{

            $services = Service::all();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $services
                ]);
        }
    }

    public function getKeyword(Request $request){
        $validator = Validator::make($request->all(), [
            'service_id'  => "required",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'required_fields' => $validator->errors()->all(),
                'message' => 'Missing field(s)',
                'status' => '500'
            ]);
        }

        $service_id = $request->service_id;

        $key = DB::table('keyword')->where('service_id', $service_id);
        if($key->exists()){
            $keyWord = $key->first();

            return response()->json([
                'message' => 'Successful',
                'status' => '200',
                'data' => $keyWord
            ]);
        }else{
            return response()->json([
                'message' => 'Failed',
                'status' => '300',
                'data' => 'Invalid Keyword'
            ]);
        }
    }

    public function getPricePoint(Request $request){
        //validate inputs
        if(isset($request->keyword)){
            $key = DB::table('keyword')->where('name', $request->keyword);

            if($key->exists()){
                $keyword = $key->first();

                $priceCheck = PricePoint::where('keyword_id', $keyword->id);
                if($priceCheck->exists()){
                    $pricePoint = $priceCheck->first();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $pricePoint
                    ]);
                }else{
                    return response()->json([
                        'message' => 'Failed',
                        'status' => '300',
                        'data' => 'Invalid Keyword'
                    ]);
                }
            }else{
                return response()->json([
                    'message' => 'Network Does not exist',
                    'status' => '300',
                ]);
            }
        }elseif(isset($request->price)){
            $priceCheck = PricePoint::where('price', $request->price);
            if($priceCheck->exists()){
                $pricePoint = $priceCheck->first();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $pricePoint
                ]);
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Invalid Price'
                ]);
            }
        }elseif(isset($request->service)){
            $serv = Service::where('serviceName', $request->service);
            if($serv->exists()){
                $services = $serv->first();

                $pp = PricePoint::where('service_id', $services->id);

                if($pp->exists()){
                    $pricePoint = $pp->first();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $pricePoint
                    ]);
                }else{
                    return response()->json([
                        'message' => 'Failed',
                        'status' => '300',
                        'data' => 'Invalid service'
                    ]);
                }
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Invalid service'
                ]);
            }
        }else{
            $pricePoint = PricePoint::all();

            return response()->json([
                'message' => 'Successful',
                'status' => '200',
                'data' => $pricePoint
            ]);
        }
    }

    //validate methods
    public function countServices(Request $request){
        if(isset($request->network)){
            $net = Network::where('network', $request->network);
            if($net->exists()){
                $network = $net->first();

                $serv = Service::where('network_id', $network->id);
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
                        'data' => []
                    ]);
                }
            }else{
                return response()->json([
                    'message' => 'Network Does not exist',
                    'status' => '300',
                ]);
            }
        }else{

            $servicesCount = Service::get()->count();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $servicesCount
                ]);
        }
    }

    public function serviceChannels(Request $request){
        if(isset($request->channel)){
            $chan = DB::table('channel')->where('channel', $request->channel);
            if($chan->exists()){
                $channel = $chan->first();
                $channel_id = $channel->id;
            }else{
                return response()->json([
                    'message' => 'Failed',
                        'status' => '300',
                        'data' => 'Invalid Channel'
                ]);
            }

            $serv = Service::join('service_channel_table','service_channel_table.service_id','services.id')
                ->where('service_channel_table.channel', $channel_id);
                
            if($serv->exists()){
                $servicesChannels = $serv->select('services.*', )->get();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $servicesChannels
                    ]);

            }else{
                return response()->json([
                    'message' => 'Failed',
                        'status' => '300',
                        'data' => 'Invalid Channel'
                ]);
            }
        }else{
            
            $servicesChannels = ServiceChannel::join('services','services.id','service_channel_table.service_id')
                            ->join('channel_table','channel_table.id','service_channel_table.channel_id')
                            ->select('services.*', 'channel_table.channel')->get();

            return response()->json([
                'message' => 'Successful',
                'status' => '200',
                'data' => $servicesChannels
            ]);
        }
    }

    
}
