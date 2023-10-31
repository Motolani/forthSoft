<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    //
    public function login(Request $request)
    {
        Log::info(Hash::make('password'));
        $validator = Validator::make($request->all(), [
            'email'  => "required",
            'password'  => "required",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'required_fields' => $validator->errors()->all(),
                'message' => 'Missing field(s)',
                'status' => '500'
            ]);
        }

        $userMail = User::where('email', $request->email);
        if($userMail->exists()){
            //Hash password and run an hash check  before login
            $potentialUser = $userMail->first();
            $hashPwd = $potentialUser->password;
            Log::info('here');
            if(Hash::check($request->password, $hashPwd)){
                if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
                    $token = time() . rand(100, 999);
                    $currentTime = Carbon::now()->toDateTimeString();
                    $potentialUser->update([
                        'token_time' => $currentTime,
                        'token' => $token
                    ]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Login Succcessful',
                        'data' => [
                            'token' => $token,
                            'apiKey' => $potentialUser->apiKey,
                            'name' => $potentialUser->name,
                            'email' => $potentialUser->email,
                        ]
                    ]);
                }else{
                    return response()->json([
                        'status' => 401,
                        'message'=>'Unauthorised'
                    ]); 
                }
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid User',
                ]);
            }
            
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Invalid User',
            ]);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password'  => "required",
            'new_password'  => "required",
            'email'  => "required",

        ]);
        if ($validator->fails()) {
            return response()->json([
                'required_fields' => $validator->errors()->all(),
                'message' => 'Missing field(s)',
                'status' => '500'
            ]);
        }

        $userCheck = User::where('email', $request->email);
        if($userCheck->exists()){
            $user = $userCheck->first();
            if(Hash::check($request->old_password, $user->password)){
                $hashed = Hash::make($request->new_password);
                $userCheck->update([
                    'password' => $hashed,
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'Password Successfully Changed',
                ]);
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid Password',
                ]);
            }
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Invalid User',
            ]);
        }
    }

    function changeApiKey(Request $request)
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
        $userCheck = User::where('email', $request->email);
        if($userCheck->exists())
        {
            $user = $userCheck->first();
            $preApiKey = $user->email . time() . rand(100, 999);
            $apiKey = Hash::make($preApiKey);
            Log::info($apiKey);

            $userCheck->update([
                'apiKey' => $apiKey
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'ApiKey Successfully Changed',
                'data' => [
                    'ApiKey' => $apiKey
                ]
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Invalid User',
            ]);
        }
    
    }
}
