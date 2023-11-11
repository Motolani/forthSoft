<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = "services";

    protected $fillable = [
        'user_id',
        'serviceName',
        'status',
        'amount',
        'serviceId',
        'productCode',
        'productId',
        'keyword',
        'product_name',
        'network_id',
        'shortcode',
    ];
}
