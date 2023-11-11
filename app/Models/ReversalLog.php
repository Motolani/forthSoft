<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReversalLog extends Model
{
    use HasFactory;
    protected $table = "reversal_logs";

    protected $fillable = [
        'requestId',
        'reference',
        'senderPhone',
        'mobilePhone',
        'receiverPhone',
        'transType',
        'amount',
        'pin',
        'bankCode',
        'bankName',
        'accountNumber',
        'description',
        'channel',
        'response',
        'serialNumber',
        'balance_after',
    ];

}
