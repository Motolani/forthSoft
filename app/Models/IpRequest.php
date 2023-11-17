<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpRequest extends Model
{
    use HasFactory;
    protected $table = "ip_requests";

    protected $fillable = [
        'user_id',
        'ip',
        'type',
        'old_ip'
    ];
}
