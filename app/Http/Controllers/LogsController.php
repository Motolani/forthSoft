<?php

namespace App\Http\Controllers;

use App\Models\LogRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LogsController extends Controller
{
    //
    public function logRequestbyTime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'  => "required",
            'type'  => "required",
            'to' => "required",
            'fro' => "required",
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
            $logRequest = new LogRequest();
            $logRequest->search_type = $request->type;
            $logRequest->user_id = $user->id;
            $logRequest->fro = $request->fro;
            $logRequest->to = $request->to;

        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Invalid User',
            ]);
        }

    }
}
