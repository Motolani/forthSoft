<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceChannel extends Model
{
    use HasFactory;
    protected $table = "service_channel_table";

    protected $fillable = [
        'service_id',
        'service_name',
        'channel_id',
        'channel',
    ];
}
