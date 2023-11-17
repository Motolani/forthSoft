<?php

namespace App\Http\Controllers;

use App\Models\Charging;
use App\Models\Network;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChargingController extends Controller
{
    //
    public function chargingSync(Request $request){
        if(isset($request->service)){
            $serv = Service::where('serviceName',  'LIKE', '%'.$request->service.'%');
            if($serv->exists()){
                $service = $serv->first();
                $service_id = $service->id;
                $telcoServiceId = $service->serviceId;

                $char = Charging::where('serviceId', $telcoServiceId)->where('status', 2);
                if($char->exists()){
                    $charging = $char->get();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $charging
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
            $net = Network::where('network',  'LIKE', '%'.$request->network.'%');
            if($net->exists()){
                $network = $net->first();
                $network_id = $network->id;

                $charg = Charging::where('network_id', $network_id)->where('status', 2);
                if($charg->exists()){
                    $charging = $charg->get();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $charging
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
                    'message' => 'Network not Found',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }elseif(isset($request->subscriber_id)){
            $charg = Charging::where('callingParty',  'LIKE', '%'.$request->subscriber_id.'%')->where('status', 2);
            if($charg->exists()){
                $charging = $charg->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $charging
                ]);
            }else{
                return response()->json([
                    'message' => 'No Record Found',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }elseif(isset($request->bearerId)){
            $charg = Charging::where('bearerId',  'LIKE', '%'.$request->bearerId.'%')->where('status', 2);
            if($charg->exists()){
                $charging = $charg->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $charging
                ]);
            }else{
                return response()->json([
                    'message' => 'No Record Found',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }else{
            return response()->json([
                'message' => 'Choose a parameter',
                'status' => '500',
                'data' => ''
            ]);
        }
    }

    public function chargingUnsync(Request $request){
        if(isset($request->service)){
            $serv = Service::where('serviceName',  'LIKE', '%'.$request->service.'%');
            if($serv->exists()){
                $service = $serv->first();
                $service_id = $service->id;
                $telcoServiceId = $service->serviceId;

                $char = Charging::where('serviceId', $telcoServiceId)->where('status', 0)->orWhere('status', 1);
                if($char->exists()){
                    $charging = $char->get();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $charging
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
            $net = Network::where('network',  'LIKE', '%'.$request->network.'%');
            if($net->exists()){
                $network = $net->first();
                $network_id = $network->id;

                $charg = Charging::where('network_id', $network_id)->where('status', 0)->orWhere('status', 1);
                if($charg->exists()){
                    $charging = $charg->get();

                    return response()->json([
                        'message' => 'Successful',
                        'status' => '200',
                        'data' => $charging
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
                    'message' => 'Network not Found',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }elseif(isset($request->subscriber_id)){
            $charg = Charging::where('callingParty',  'LIKE', '%'.$request->subscriber_id.'%')->where('status', 0)->orWhere('status', 1);
            if($charg->exists()){
                $charging = $charg->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $charging
                ]);
            }else{
                return response()->json([
                    'message' => 'No Record Found',
                    'status' => '300',
                    'data' => []
                ]);
            }
        }elseif(isset($request->bearerId)){
            $charg = Charging::where('bearerId',  'LIKE', '%'.$request->bearerId.'%')->where('status', 0)->orWhere('status', 1);
            if($charg->exists()){
                $charging = $charg->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $charging
                ]);
            }else{
                return response()->json([
                    'message' => 'No Record Found',
                    'status' => '300',
                    'data' => []
                ]);
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
