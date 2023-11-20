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
        Log::info('IP');
        Log::info($request->ip());
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
                        'message'=>'Unauthorised',
                        'data' => []
                    ]); 
                }
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid User',
                    'data' => []
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
                'data' => []
            ]);
        }
    
    }

    public function getUserDetails(Request $request){
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

            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'apiKey' => $user->apiKey,
                'token' => $user->token,
                'created' => $user->created_at,
            ];

            return response()->json([
                'status' => 200,
                'message' => 'ApiKey Successfully Changed',
                'data' => $data
            ]);
        }   
    }

    public function activateTwoFA(Request $request){
        $validator = Validator::make($request->all(), [
            'twoFA'  => "required",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'required_fields' => $validator->errors()->all(),
                'message' => 'Missing field(s)',
                'status' => '500'
            ]);
        }

        if($request->twoFA == 1){
            $user = User::where('token', $request->header('token'))->first();
            $user_id = $user->id;

            $user = User::where('id', $user_id);
            $userInfo = $user->get();
            if($userInfo->twoFa == 1){
                return response()->json([
                    'status' => 300,
                    'message' => '2FA Already Activated',             
                ]);
            }

            $user->update([
                'twoFa' => $request->twoFA
            ]);

            return response()->json([
                'status' => 200,
                'message' => '2FA Successfully Activate',
            ]);
        }elseif($request->twoFA == 0){
            $user = User::where('token', $request->header('token'))->first();
            $user_id = $user->id;

            $user = User::where('id', $user_id);
            $userInfo = $user->get();
            if($userInfo->twoFa == 0){
                return response()->json([
                    'status' => 300,
                    'message' => '2FA Already Deactivated',             
                ]);
            }

            $user->update([
                'twoFa' => $request->twoFA
            ]);

            return response()->json([
                'status' => 200,
                'message' => '2FA Successfully deactivate',
            ]);

        }else{
            return response()->json([
                'status' => 300,
                'message' => 'Invalid Value passed',
            ]);
        }
    }

    public function twoFAToken(Request $request){
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

        $use = User::where('email', $request->email);
        if($use->exists()){
            $token = rand(100, 999);
            $use->update([
                'twoFa_token' => $token
            ]);

            return response()->json([
                'status' => 200,
                'message' => '2FA Token Successfully Generated',
                'data' => $token
            ]);
        }else{
            return response()->json([
                'status' => 300,
                'message' => 'Invalid Email',
                'data' => ''
            ]);
        }
    }

    public function twoFACheck(Request $request){
        $validator = Validator::make($request->all(), [
            'twoFatoken'  => "required",
            'email'  => "required",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'required_fields' => $validator->errors()->all(),
                'message' => 'Missing field(s)',
                'status' => '500'
            ]);
        }

        $use = User::where('email', $request->email);
        if($use->exists()){
            $user = $use->first();
            
            if($user->twoFa_token == $request->twoFatoken){
                return response()->json([
                    'status' => 200,
                    'message' => 'Token Match',
                ]);
            }else{
                return response()->json([
                    'status' => 300,
                    'message' => 'Token Mismatch',
                ]);
            }
        }else{
            return response()->json([
                'status' => 300,
                'message' => 'Invalid Email',
             ]);
        }
    }
}
