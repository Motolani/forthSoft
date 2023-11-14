<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charging extends Model
{
    use HasFactory;
    protected $table = "charging_table";

    protected $fillable = [
        'serviceType',
        'chargingMode',
        'appliedPlan',
        'contentId',
        'resultCode',
        'renFlag',
        'processingTime',
        'result',
        'validityType',
        'sequenceNo',
        'callingParty',
        'bearerId',
        'operationId',
        'requestedPlan',
        'chargeAmount',
        'serviceNode',
        'serviceId',
        'keyword',
        'category',
        'validityDays',
        'status',
        'user_id',
        'network_id',
    ];
}
