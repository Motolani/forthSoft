<?php

namespace App\Http\Controllers;

use App\Models\CP;
use App\Models\Service;
use Illuminate\Http\Request;

class CpController extends Controller
{
    //
    public function noSubscribers(Request $request)
    {
        $subers = Service::join('cp_table','cp_table.id','services.user_id')
                ->join('charging_table','charging_table.serviceId','services.id')
                // ->where('services.id', 'charging_table.serviceId')
                ->where('serivces.user_id', 'cp_table.id')
                ->select('charging_table.callingParty as subscriber_msisdn', 'charging_table.serviceType as service','charging_table.validityDays','charging_table.chargeAmount','incidenceoprations.status as offenceStatus')
                ->get();
    }
}
