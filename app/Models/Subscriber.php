<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;
    protected $table = "subscribers";

    protected $fillable = [
        'subscriber_id',
        'senderAddress',
        'receiverAddress',
        'message',
        'created',
        'service_id',
        'user_id',
        'network_id',
    ];
}
