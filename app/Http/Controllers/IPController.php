<?php

namespace App\Http\Controllers;

use App\Models\IP;
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
            $ip = IP::where('user_id', $user->id);

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


}
