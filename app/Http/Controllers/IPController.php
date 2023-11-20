<?php

namespace App\Http\Controllers;

use App\Models\IP;
use App\Models\IpRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class IPController extends Controller
{
    //
    public function whiteListIP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'  => "required",
            'ip'  => "required|ip",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'required_fields' => $validator->errors()->all(),
                'message' => 'Missing field(s)',
                'status' => '500'
            ]);
        }

        $userExists = User::where('email', $request->email);
        if($userExists->exists()){
            $user = $userExists->first();
            // $ip = IP::where('user_id', $user->id);

                $ip = new IP();
                $ip->user_id = $user->id;
                $ip->ip = $request->ip;
                $ip->save();

            return response()->json([
                'status' => 200,
                'message' => 'IP Successfully Whitelisted',
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Invalid User',
            ]);
        }
    }

    public function updateWhiteListedIP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'  => "required",
            'old_ip'  => "required|ip",
            'new_ip'  => "required|ip",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'required_fields' => $validator->errors()->all(),
                'message' => 'Missing field(s)',
                'status' => '500'
            ]);
        }

        $userExists = User::where('email', $request->email);
        if($userExists->exists()){
            $user = $userExists->first();
            $ip = IP::where('user_id', $user->id)->where('ip', $request->old_ip);
            if($ip->exists()){

                $ip->update([
                    'ip' => $request->new_ip
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'IP Successfully Updated',
                ]);
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid IP',
                ]);
            }

        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Invalid User',
            ]);
        }
    }

    public function viewWhitelistedIP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'  => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'required_fields' => $validator->errors()->all(),
                'message' => 'Missing field(s)',
                'status' => '500'
            ]);
        }

        $userExists = User::where('email', $request->email);
        if($userExists->exists()){
            $user = $userExists->first();
            $userId = $user->id;

            $ipRow = IP::where('user_id', $userId);
            if($ipRow->exists()){
                $ips = $ipRow->get();

                $ipAddresses = []; 
                foreach($ips as $ip){
                    array_push($ipAddresses,$ip->ip);
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'Ip Lists',
                    'data' => $ipAddresses
                ]);
            }else{
                return response()->json([
                    'status' => 201,
                    'message' => 'No Ip Whitelisted',
                    'data' => ''
                ]);
            }

        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Invalid User',
            ]);
        }

    }

    public function deleteWhitelistedIP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'  => "required",
            'ip'  => "required|ip",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'required_fields' => $validator->errors()->all(),
                'message' => 'Missing field(s)',
                'status' => '500'
            ]);
        }

        $userExists = User::where('email', $request->email);
        if($userExists->exists()){
            $user = $userExists->first();
            $userId = $user->id;

            $ipRow = IP::where('user_id	', $userId);
            $ipRow->delete();

            return response()->json([
                'status' => 200,
                'message' => 'IP Successfully Deleted',
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Invalid User',
            ]);
        }

    }

    public function IpRequest(Request $request){
        $validator = Validator::make($request->all(), [
            'ip'  => "required|ip",
            'type'  => "required",
            'new_ip'  => "ip",
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

        if($request->type == 'add'){
            $ipReq = new IpRequest;
            $ipReq->user_id = $user_id;
            $ipReq->ip = $request->ip;
            $ipReq->type = $request->type;
            $ipReq->save();
        }elseif($request->type == 'update'){
            $ipReq = IpRequest::where('ip', $request->old_ip)->where('user_id', $user_id);
            Log::info($request);
            Log::info($user_id);
            if($ipReq->exists()){
                $ipReqFurther = IpRequest::where('ip', $request->old_ip)->where('user_id', $user_id)->where('status', 0);
                if($ipReqFurther->exists()){
                    $ipReqFurther->update([
                        'ip' => $request->ip 
                    ]);
                }else{
                    $ipReq = new IpRequest;
                    $ipReq->user_id = $user_id;
                    $ipReq->ip = $request->ip;
                    $ipReq->old_ip = $request->old_ip;
                    $ipReq->type = $request->type;
                    $ipReq->save();
                }
                
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => 'Ip does not exist',
                ]);
            }
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Invalid Operation',
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Ip Request Successfully Logged',
        ]);
    }

    public function viewRequest(Request $request){
        $user = User::where('token', $request->header('token'))->first();
        $user_id = $user->id;

        $ipReq = IpRequest::where('user_id', $user_id);
        if($ipReq->exists()){
            $req = $ipReq->get();
            return response()->json([
                'message' => 'Successful',
                'status' => '200',
                'data' => $req
            ]);
        }else{
            return response()->json([
                'message' => 'Successful',
                'status' => '200',
                'data' => []
            ]);
        }
    }

}
