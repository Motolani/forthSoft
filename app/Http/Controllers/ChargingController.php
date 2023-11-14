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
            $serv = Service::where('serviceName', $request->service);
            if($serv->exists()){
                $service = $serv->first();
                $service_id = $service->id;
                $telcoServiceId = $service->serviceId;

                $char = Charging::where('serviceId', $telcoServiceId)->where('status', 2);
                if($char->exists()){
                    $charging = $char->first();

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
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Service not Found'
                ]);
            }
        }elseif(isset($request->network)){
            $net = Network::where('network', $request->network);
            if($net->exists()){
                $network = $net->first();
                $network_id = $network->id;

                $charg = Charging::where('network_id', $network_id)->where('status', 2);
                if($charg->exists()){
                    $charging = $charg->first();

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
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Network not Found'
                ]);
            }
        }elseif(isset($request->subscriber_id)){
            $charg = Charging::where('callingParty', $request->subscriber_id)->where('status', 2);
            if($charg->exists()){
                $charging = $charg->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $charging
                ]);
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'No Record Found'
                ]);
            }
        }elseif(isset($request->bearerId)){
            $charg = Charging::where('bearerId', $request->bearerId)->where('status', 2);
            if($charg->exists()){
                $charging = $charg->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $charging
                ]);
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'No Record Found'
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
            $serv = Service::where('serviceName', $request->service);
            if($serv->exists()){
                $service = $serv->first();
                $service_id = $service->id;
                $telcoServiceId = $service->serviceId;

                $char = Charging::where('serviceId', $telcoServiceId)->where('status', 0)->orWhere('status', 1);
                if($char->exists()){
                    $charging = $char->first();

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
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Service not Found'
                ]);
            }
        }elseif(isset($request->network)){
            $net = Network::where('network', $request->network);
            if($net->exists()){
                $network = $net->first();
                $network_id = $network->id;

                $charg = Charging::where('network_id', $network_id)->where('status', 0)->orWhere('status', 1);
                if($charg->exists()){
                    $charging = $charg->first();

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
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'Network not Found'
                ]);
            }
        }elseif(isset($request->subscriber_id)){
            $charg = Charging::where('callingParty', $request->subscriber_id)->where('status', 0)->orWhere('status', 1);
            if($charg->exists()){
                $charging = $charg->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $charging
                ]);
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'No Record Found'
                ]);
            }
        }elseif(isset($request->bearerId)){
            $charg = Charging::where('bearerId', $request->bearerId)->where('status', 0)->orWhere('status', 1);
            if($charg->exists()){
                $charging = $charg->get();

                return response()->json([
                    'message' => 'Successful',
                    'status' => '200',
                    'data' => $charging
                ]);
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'status' => '300',
                    'data' => 'No Record Found'
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

    public function chargingSyncCount(Request $request){
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
        $charg = Charging::whereBetween('created_at', [$from, $to])->where('status', 2);
        if($charg->exists()){
            $chargingSyncCount = $charg->get()->count();

            return response()->json([
                'message' => 'Successful',
                'status' => '200',
                'data' => $chargingSyncCount
            ]);
        }else{
            return response()->json([
                'message' => 'Successful',
                'status' => '200',
                'data' => ''
            ]);
        }
    }

    public function chargingUnsyncCount(Request $request){
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
        $charg = Charging::whereBetween('created_at', [$from, $to])->where('status', 0)->orWhere('status', 1);

        if($charg->exists()){
            $chargingUnsyncCount = $charg->get()->count();

            return response()->json([
                'message' => 'Successful',
                'status' => '200',
                'data' => $chargingUnsyncCount
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
