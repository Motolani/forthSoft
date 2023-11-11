<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogRequest extends Model
{
    use HasFactory;
    protected $table = "log_requests";

    protected $fillable = [
        'user_id',
        'search_type',
        'status',
        'service_name',
        'network_name',
        'fro',
        'to',
    ];
}
